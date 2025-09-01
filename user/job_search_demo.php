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

// --- Filter Logic ---
$where = [];
$params = [];

// Search by job title or company
if (!empty($_GET['search'])) {
    $where[] = "(jobs.job_title LIKE ? OR cp.company_name LIKE ?)";
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}

// City filter
if (!empty($_GET['city']) && $_GET['city'] != 'Choose city') {
    $where[] = "jobs.city = ?";
    $params[] = $_GET['city'];
}

// Category filter (array)
if (!empty($_GET['category']) && is_array($_GET['category'])) {
    $catPlaceholders = implode(',', array_fill(0, count($_GET['category']), '?'));
    $where[] = "jobs.category IN ($catPlaceholders)";
    foreach ($_GET['category'] as $cat) $params[] = $cat;
}

// Salary filter
if (!empty($_GET['salary_max'])) {
    $where[] = "jobs.max_salary <= ?";
    $params[] = intval($_GET['salary_max']);
}

// Job type filter (array)
if (!empty($_GET['job_type']) && is_array($_GET['job_type'])) {
    $jtPlaceholders = implode(',', array_fill(0, count($_GET['job_type']), '?'));
    $where[] = "jobs.job_type IN ($jtPlaceholders)";
    foreach ($_GET['job_type'] as $jt) $params[] = $jt;
}

// Experience level filter (array)
if (!empty($_GET['experience_level']) && is_array($_GET['experience_level'])) {
    $elPlaceholders = implode(',', array_fill(0, count($_GET['experience_level']), '?'));
    $where[] = "jobs.job_level IN ($elPlaceholders)";
    foreach ($_GET['experience_level'] as $el) $params[] = $el;
}

// Date posted filter (array)
if (!empty($_GET['date_posted']) && is_array($_GET['date_posted'])) {
    $dateWhere = [];
    $now = date('Y-m-d H:i:s');
    foreach ($_GET['date_posted'] as $dp) {
        if ($dp == 'Last Hour') $dateWhere[] = "jobs.posted_on >= DATE_SUB('$now', INTERVAL 1 HOUR)";
        elseif ($dp == 'Last 24 Hours') $dateWhere[] = "jobs.posted_on >= DATE_SUB('$now', INTERVAL 1 DAY)";
        elseif ($dp == 'Last 7 Days') $dateWhere[] = "jobs.posted_on >= DATE_SUB('$now', INTERVAL 7 DAY)";
        elseif ($dp == 'Last 30 Days') $dateWhere[] = "jobs.posted_on >= DATE_SUB('$now', INTERVAL 30 DAY)";
        elseif ($dp == 'All') $dateWhere[] = "1=1";
    }
    if ($dateWhere) $where[] = '(' . implode(' OR ', $dateWhere) . ')';
}

// --- Pagination ---
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// --- Query ---
$sql = "
    SELECT jobs.*, cp.logo, cp.company_name, cp.map_location
    FROM jobs
    LEFT JOIN company_profiles cp ON jobs.recruiter_id = cp.recruiter_id
";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY jobs.posted_on DESC LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total jobs for pagination
$countSql = "SELECT COUNT(*) FROM jobs LEFT JOIN company_profiles cp ON jobs.recruiter_id = cp.recruiter_id";
if ($where) $countSql .= " WHERE " . implode(' AND ', $where);
$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$totalJobs = $countStmt->fetchColumn();
$totalPages = ceil($totalJobs / $limit);
?>

<link rel="stylesheet" href="../assets/css/jobsearch.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="jobsearch-container">
  <h1 class="jobsearch-title">Job Search</h1>
  <p class="jobsearch-sub">Search for your desired job matching your skills</p>

  <form method="GET">
  <div class="jobsearch-grid">
    <aside class="jobsearch-filters">
      <!-- Sidebar Filters -->
      <div class="filter-group">
        <label for="search"><i class="fas fa-search"></i> Search by Job Title or Company</label>
        <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Job title or company">
      </div>

      <div class="filter-group">
        <label><i class="fas fa-map-marker-alt"></i> Location</label>
        <select name="city">
          <option <?= empty($_GET['city']) ? 'selected' : '' ?>>Choose city</option>
          <option <?= (@$_GET['city'] == 'Dhaka') ? 'selected' : '' ?>>Dhaka</option>
          <option <?= (@$_GET['city'] == 'Madaripur') ? 'selected' : '' ?>>Madaripur</option>
          <option <?= (@$_GET['city'] == 'Barishal') ? 'selected' : '' ?>>Barishal</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Category</label>
        <div class="checkbox-list">
          <?php
          $categories = ['Commerce', 'Telecommunications', 'Hotels & Tourism', 'Education', 'Financial Services'];
          foreach ($categories as $cat) {
            $checked = (!empty($_GET['category']) && in_array($cat, (array)$_GET['category'])) ? 'checked' : '';
            echo "<label><input type='checkbox' name='category[]' value='$cat' $checked> $cat</label>";
          }
          ?>
        </div>
      </div>

      <div class="filter-group salary-filter">
        <label>Salary</label>
        <input type="range" name="salary_max" min="0" max="100000" value="<?= htmlspecialchars($_GET['salary_max'] ?? 50000) ?>" oninput="document.getElementById('salaryOut').textContent = this.value">
        <div class="salary-range">
          <span>Salary: $0 - $<span id="salaryOut"><?= htmlspecialchars($_GET['salary_max'] ?? 50000) ?></span></span>
        </div>
      </div>

      <div class="filter-group">
        <label>Job Type</label>
        <div class="checkbox-list">
          <?php
          $types = ['Full Time', 'Part Time', 'Freelance', 'Seasonal', 'Fixed-Price'];
          foreach ($types as $t) {
            $checked = (!empty($_GET['job_type']) && in_array($t, (array)$_GET['job_type'])) ? 'checked' : '';
            echo "<label><input type='checkbox' name='job_type[]' value='$t' $checked> $t</label>";
          }
          ?>
        </div>
      </div>

      <div class="filter-group">
        <label>Experience Level</label>
        <div class="checkbox-list">
          <?php
          $levels = ['No-experience', 'Fresher', 'Intermediate', 'Expert'];
          foreach ($levels as $lvl) {
            $checked = (!empty($_GET['experience_level']) && in_array($lvl, (array)$_GET['experience_level'])) ? 'checked' : '';
            echo "<label><input type='checkbox' name='experience_level[]' value='$lvl' $checked> $lvl</label>";
          }
          ?>
        </div>
      </div>

      <div class="filter-group">
        <label>Date Posted</label>
        <div class="checkbox-list">
          <?php
          $opts = ['All', 'Last Hour', 'Last 24 Hours', 'Last 7 Days', 'Last 30 Days'];
          foreach ($opts as $opt) {
            $checked = (!empty($_GET['date_posted']) && in_array($opt, (array)$_GET['date_posted'])) ? 'checked' : '';
            echo "<label><input type='checkbox' name='date_posted[]' value='$opt' $checked> $opt</label>";
          }
          ?>
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

      <div class="filter-group">
        <button type="submit" class="apply-btn"
          style="background: linear-gradient(90deg,#ff6600 70%,#ffb366 100%);
                 color: #fff;
                 border: none;
                 padding: 7px 16px;
                 font-size: 14px;
                 font-weight: 600;
                 border-radius: 7px;
                 box-shadow: 0 2px 8px rgba(255,102,0,0.08);
                 cursor: pointer;
                 transition: background 0.3s, box-shadow 0.3s, transform 0.2s;">
          <i class="fas fa-filter" style="margin-right:7px;"></i>Apply Filters
        </button>
        <style>
          .apply-btn:hover {
            background: linear-gradient(90deg,#e65c00 70%,#ffd699 100%);
            box-shadow: 0 4px 16px rgba(255,102,0,0.15);
            color: #fff;
            transform: scale(0.97) translateY(-2px);
            opacity: 0.92;
          }
        </style>
      </div>

      <div class="filter-group">
        <a href="job_search_demo.php" class="apply-btn"
           style="background: linear-gradient(90deg,#cccccc 70%,#f5f5f5 100%);
                  color: #333;
                  text-align: center;
                  text-decoration: none;
                  border: none;
                  padding: 7px 16px;
                  font-size: 14px;
                  font-weight: 600;
                  border-radius: 7px;
                  box-shadow: 0 2px 8px rgba(120,120,120,0.08);
                  cursor: pointer;
                  margin-top: 6px;
                  display: block;
                  transition: background 0.3s, box-shadow 0.3s, transform 0.2s;">
          <i class="fas fa-times" style="margin-right:7px;"></i>Clear All Filters
        </a>
        <style>
          .apply-btn[href="job_search_demo.php"]:hover {
            background: linear-gradient(90deg,#bdbdbd 70%,#e0e0e0 100%);
            box-shadow: 0 4px 16px rgba(120,120,120,0.15);
            color: #222;
            transform: scale(0.97) translateY(-2px);
            opacity: 0.92;
          }
        </style>
      </div>

      <div class="hiring-banner">
        <div class="overlay">
          <h2>WE ARE HIRING</h2>
          <p>Apply Today!</p>
        </div>
      </div>
    </aside>

    <main class="jobsearch-results">
      <div class="result-meta">
        <p>Showing <?= $totalJobs ?> result<?= $totalJobs !== 1 ? 's' : '' ?></p>
        <select class="sort-select" name="sort" onchange="this.form.submit()">
          <option value="latest" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'latest') ? 'selected' : '' ?>>Sort by latest</option>
          <option value="oldest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : '' ?>>Sort by oldest</option>
        </select>
      </div>

      <?php $i = 0; foreach ($jobs as $job): ?>
        <?php $isSaved = in_array($job['job_id'], $savedJobs); ?>
        <div class="job-card">
          <div class="bookmark-wrapper" data-jobid="<?= $job['job_id'] ?>">
            <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
          </div>

          <div class="card-left">
            <?php if ($i < 2): ?>
              <div class="recommended-badge">⭐ Recommended</div>
            <?php endif; ?>
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
              <span><i class="fas fa-wallet"></i> TK<?= number_format($job['min_salary']) ?>–<?= number_format($job['max_salary']) ?></span>
              <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></span>
            </div>
          </div>

          <div class="card-right">
            <a href="job_details.php?job_id=<?= $job['job_id'] ?>" class="details-btn">Job Details</a>
          </div>
        </div>
      <?php $i++; endforeach; ?>

      <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>" class="page <?= ($p == $page) ? 'active' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="page">Next →</a>
        <?php endif; ?>
      </div>
    </main>
  </div>
  </form>
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
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Recommendation badge logic
    $.ajax({
        url: '../ai/recommend_jobs.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
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
