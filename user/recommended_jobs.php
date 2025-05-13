<?php
session_start();
include('../includes/db.php');
include('../includes/header_jobseeker.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../login.php");
    exit();
}

$job_seeker_id = $_SESSION['user_id'];

// Fetch saved jobs
$savedStmt = $conn->prepare("SELECT job_id FROM saved_jobs WHERE job_seeker_id = ?");
$savedStmt->execute([$job_seeker_id]);
$savedJobs = $savedStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch seeker skills
$skillsStmt = $conn->prepare("
    SELECT sm.name 
    FROM job_seeker_skills js 
    JOIN skill_master sm ON js.skill_id = sm.id 
    WHERE js.job_seeker_id = ?
");
$skillsStmt->execute([$job_seeker_id]);
$skills = $skillsStmt->fetchAll(PDO::FETCH_COLUMN);

$recommended_jobs = [];
$totalJobs = 0;

if (!empty($skills)) {
    $keywords = array_map('strtolower', $skills);
    $likeConditions = [];
    $params = [];

    foreach ($keywords as $kw) {
        $likeConditions[] = "(LOWER(jobs.job_title) LIKE ? OR LOWER(jobs.tags) LIKE ? OR LOWER(jobs.skills) LIKE ?)";
        for ($i = 0; $i < 3; $i++) $params[] = "%$kw%";
    }

    $whereSQL = implode(" OR ", $likeConditions);
    $sql = "
        SELECT jobs.*, cp.logo, cp.company_name
        FROM jobs
        LEFT JOIN company_profiles cp ON jobs.recruiter_id = cp.recruiter_id
        WHERE jobs.expire_date >= NOW() AND ($whereSQL)
        ORDER BY jobs.posted_on DESC
        LIMIT 50
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add fake match percentage (80–95%)
    foreach ($results as &$job) {
        $job['match_percentage'] = rand(80, 95);
    }

    $recommended_jobs = $results;
    $totalJobs = count($recommended_jobs);
}
?>

<link rel="stylesheet" href="../assets/css/jobsearch.css">
<style>
.match-badge {
  background-color: #e0ffe6;
  color: #0b7d3e;
  font-weight: bold;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 13px;
  display: inline-block;
  margin-left: 10px;
}
</style>

<div class="jobsearch-container">
  <h1 class="jobsearch-title">⭐ Recommended Jobs</h1>
  <p class="jobsearch-sub">Based on your profile and skills</p>

  <div class="jobsearch-grid">
    <!-- Sidebar -->
    <aside class="jobsearch-filters">
      <!-- Sidebar Filters (static UI only) -->
      <div class="filter-group">
        <label for="search"><i class="fas fa-search"></i> Search by Job Title or Company</label>
        <input type="text" id="search" placeholder="Job title or company">
      </div>

      <div class="filter-group">
        <label><i class="fas fa-map-marker-alt"></i> Location</label>
        <select>
          <option selected>Choose city</option>
          <option>New York</option>
          <option>Texas</option>
          <option>California</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Category</label>
        <div class="checkbox-list">
          <label><input type="checkbox"> Commerce</label>
          <label><input type="checkbox"> Telecommunications</label>
          <label><input type="checkbox"> Hotels & Tourism</label>
          <label><input type="checkbox"> Education</label>
          <label><input type="checkbox"> Financial Services</label>
        </div>
      </div>

      <div class="filter-group salary-filter">
        <label>Salary</label>
        <input type="range" min="0" max="100000" value="50000">
        <div class="salary-range">
          <span>Salary: $0 - $99999</span>
          <button class="apply-btn">Apply</button>
        </div>
      </div>

      <div class="filter-group">
        <label>Job Type</label>
        <div class="checkbox-list">
          <label><input type="checkbox"> Full Time</label>
          <label><input type="checkbox"> Part Time</label>
          <label><input type="checkbox"> Freelance</label>
          <label><input type="checkbox"> Seasonal</label>
          <label><input type="checkbox"> Fixed-Price</label>
        </div>
      </div>

      <div class="filter-group">
        <label>Experience Level</label>
        <div class="checkbox-list">
          <label><input type="checkbox"> No-experience</label>
          <label><input type="checkbox"> Fresher</label>
          <label><input type="checkbox"> Intermediate</label>
          <label><input type="checkbox"> Expert</label>
        </div>
      </div>

      <div class="filter-group">
        <label>Date Posted</label>
        <div class="checkbox-list">
          <label><input type="checkbox"> All</label>
          <label><input type="checkbox"> Last Hour</label>
          <label><input type="checkbox"> Last 24 Hours</label>
          <label><input type="checkbox"> Last 7 Days</label>
          <label><input type="checkbox"> Last 30 Days</label>
        </div>
      </div>

      <div class="filter-group">
        <label>Tags</label>
        <div class="tag-list">
          <span>engineering</span>
          <span>design</span>
          <span>ui/ux</span>
          <span>marketing</span>
          <span>management</span>
          <span>soft</span>
          <span>construction</span>
        </div>
      </div>

      <div class="hiring-banner">
        <div class="overlay">
          <h2>WE ARE HIRING</h2>
          <p>Apply Today!</p>
        </div>
      </div>
    </aside>

    <!-- Job Results -->
    <main class="jobsearch-results">
      <div class="result-meta">
        <p>Showing <?= $totalJobs ?> result<?= $totalJobs !== 1 ? 's' : '' ?></p>
        <select class="sort-select">
          <option>Sort by latest</option>
          <option>Sort by oldest</option>
        </select>
      </div>

      <?php if ($totalJobs > 0): ?>
        <?php foreach ($recommended_jobs as $job): ?>
          <?php $isSaved = in_array($job['job_id'], $savedJobs); ?>
          <div class="job-card">
            <div class="bookmark-wrapper" data-jobid="<?= $job['job_id'] ?>">
              <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
            </div>

            <div class="match-badge">
              <?= $job['match_percentage'] ?>% Match
            </div>

            <div class="card-left">
              <span class="time-badge"><?= date('M d', strtotime($job['posted_on'])) ?></span>
              <div class="job-logo">
                <img src="<?= !empty($job['logo']) ? '../uploads/company/' . htmlspecialchars($job['logo']) : '../uploads/company/default_logo.png' ?>" alt="Company Logo" />
              </div>
            </div>

            <div class="card-middle">
              <h3><?= htmlspecialchars($job['job_title']) ?></h3>
              <p class="company"><?= htmlspecialchars($job['company_name']) ?: 'Company Name' ?></p>
              <div class="job-meta">
                <span><i class="fas fa-briefcase"></i> <?= htmlspecialchars($job['tags']) ?></span>
                <span><i class="fas fa-clock"></i> <?= htmlspecialchars($job['job_level']) ?></span>
                <span><i class="fas fa-wallet"></i> $<?= number_format($job['min_salary']) ?>–$<?= number_format($job['max_salary']) ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></span>
              </div>
            </div>

            <div class="card-right">
              <a href="job_details.php?job_id=<?= $job['job_id'] ?>" class="details-btn">Job Details</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center;">No recommendations found. Try updating your profile or adding more skills.</p>
      <?php endif; ?>
    </main>
  </div>
</div>

<?php include('../includes/footer_jobseeker.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const bookmarks = document.querySelectorAll('.bookmark-wrapper');
    bookmarks.forEach(bm => {
        bm.addEventListener('click', function() {
            const jobId = this.getAttribute('data-jobid');
            const icon = this.querySelector('i');

            fetch('save_job.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'job_id=' + jobId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'saved') {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else if (data.status === 'removed') {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
            });
        });
    });
});
</script>
