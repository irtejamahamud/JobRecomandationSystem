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

// Fetch all jobs
$stmt = $conn->prepare("
    SELECT jobs.*, cp.logo, cp.company_name, cp.map_location
    FROM jobs
    LEFT JOIN company_profiles cp ON jobs.recruiter_id = cp.recruiter_id
    ORDER BY jobs.posted_on DESC
    LIMIT 10
");
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalJobs = count($jobs);
?>

<link rel="stylesheet" href="../assets/css/jobsearch.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->

<div class="jobsearch-container">
  <h1 class="jobsearch-title">Job Search</h1>
  <p class="jobsearch-sub">Search for your desired job matching your skills</p>

  <div class="jobsearch-grid">
    <!-- Sidebar -->
    <aside class="jobsearch-filters">
      <!-- Sidebar Filters -->
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

      <?php foreach ($jobs as $job): ?>
        <?php $isSaved = in_array($job['job_id'], $savedJobs); ?>
        <div class="job-card">
          <div class="bookmark-wrapper" data-jobid="<?= $job['job_id'] ?>">
            <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
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

      <div class="pagination">
        <a href="#" class="page active">1</a>
        <a href="#" class="page">2</a>
        <a href="#" class="page">Next →</a>
      </div>
    </main>
  </div>
</div>

<?php include('../includes/footer_jobseeker.php'); ?>

<!-- Bookmark Save/Unsave and Recommendation AJAX -->
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
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Recommendation AJAX
    // Recommendation AJAX
    $.ajax({
    url: '../ai/recommend_jobs.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
        console.log("🔍 Full Recommendation Response:", response); // ✅ Log to console

        if (response.recommended_jobs && response.recommended_jobs.length > 0) {
            const recommendedMap = {};
            response.recommended_jobs.forEach(job => {
                recommendedMap[job.job_id] = job.similarity;
            });

            document.querySelectorAll('.job-card').forEach(function(card) {
                const jobLink = card.querySelector('.details-btn');
                if (jobLink) {
                    const url = new URL(jobLink.href);
                    const jobID = parseInt(url.searchParams.get('job_id'));

                    const matchPercent = Math.round((recommendedMap[jobID] || 0) * 100);
                    const showBadge = matchPercent >= 70 || 
                                      (matchPercent >= 50 && Math.random() < 0.5);

                    if (showBadge) {
                        const badge = document.createElement('div');
                        badge.className = 'recommended-badge';
                        badge.innerText = `⭐ Recommended (${matchPercent}%)`;

                        const badgeContainer = card.querySelector('.card-left');
                        if (badgeContainer) {
                            badgeContainer.insertBefore(badge, badgeContainer.firstChild);
                        }
                    }
                }
            });
        }
    },
    error: function(xhr, status, error) {
        console.error("❌ Recommendation fetch failed:", error);
        console.log("🧾 Response:", xhr.responseText);
    }
});


});
</script>
