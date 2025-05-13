<?php
session_start();
include("../includes/db.php");
include("../includes/header_recruiter.php");

$recruiter_id = $_SESSION['user_id'] ?? 0;
$filter = $_GET['filter'] ?? 'all';

// Fetch jobs posted by this recruiter
$jobQuery = $conn->prepare("SELECT * FROM jobs WHERE recruiter_id = :rid");
$jobQuery->execute([':rid' => $recruiter_id]);
$jobList = $jobQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/applications.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

<div class="applications-wrapper">
  <h2>Job Applications</h2>

  <!-- Tabs -->
  <div class="tab-buttons">
    <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All</a>
    <a href="?filter=shortlisted" class="<?= $filter === 'shortlisted' ? 'active' : '' ?>">Shortlisted</a>
    <a href="?filter=rejected" class="<?= $filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
  </div>

  <?php foreach ($jobList as $job): ?>
    <div class="job-card">
      <h3><?= htmlspecialchars($job['job_title']) ?></h3>
      <p><?= htmlspecialchars($job['job_level']) ?> Level</p>

      <?php
        $whereStatus = '';
        if ($filter === 'shortlisted') {
            $whereStatus = " AND a.status = 'Shortlisted'";
        } elseif ($filter === 'rejected') {
            $whereStatus = " AND a.status = 'Rejected'";
        }

        $stmt = $conn->prepare("
        SELECT a.status, a.applied_at, u.id as job_seeker_id, u.email, u.fullname, js.profile_image,
               (SELECT MAX(el.level_name)
                  FROM education e 
                  JOIN education_levels el ON e.level_id = el.id 
                  WHERE e.job_seeker_id = js.job_seeker_id) AS education,
               (SELECT SUM(GREATEST(0, TIMESTAMPDIFF(YEAR, start_date, end_date))) 
                  FROM experience ex 
                  WHERE ex.job_seeker_id = js.job_seeker_id) AS experience,
               (SELECT r1.file_name FROM resumes r1 
                  WHERE r1.job_seeker_id = js.job_seeker_id 
                  ORDER BY uploaded_at DESC LIMIT 1) AS file_name
        FROM applications a
        JOIN users u ON u.id = a.job_seeker_id
        JOIN job_seekers js ON js.job_seeker_id = u.id
        WHERE a.job_id = :jobid $whereStatus
        GROUP BY a.job_seeker_id
      ");
      
        $stmt->execute([':jobid' => $job['job_id']]);
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <?php if ($applicants): ?>
        <div class="applicants-grid">
          <?php foreach ($applicants as $app): ?>
            <div class="applicant-card">
              <!-- Dropdown -->
              <div class="dropdown">
                <i class="fas fa-ellipsis-v dropdown-toggle" onclick="toggleDropdown(this)"></i>
                <div class="dropdown-menu">
                  <form method="post" action="update_status.php" class="status-form">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($app['email']) ?>">
                    <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
                    <label>Status:</label>
                    <select name="status" onchange="this.form.submit()">
                      <option <?= $app['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                      <option <?= $app['status'] === 'Shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                      <option <?= $app['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                  </form>
                  <a href="view_applicant.php?job_seeker_id=<?= $app['job_seeker_id'] ?>">👁 View Details</a>
                </div>
              </div>

              <!-- Profile -->
              <?php
              $profileImg = (!empty($app['profile_image']) && file_exists('../uploads/' . $app['profile_image']))
                  ? '../uploads/' . $app['profile_image']
                  : '../assets/img/default-user.png';
              ?>
              <div class="profile-header">
                <img src="<?= $profileImg ?>" alt="Profile" class="applicant-avatar">
                <div class="profile-text">
                  <h4><?= htmlspecialchars($app['fullname']) ?></h4>
                  <p class="designation">UI/UX Designer</p>
                </div>
              </div>
              <hr>

              <!-- Info -->
              <ul class="info-list">
                <li>🕒 <?= $app['experience'] ?? '0' ?> Years Experience</li>
                <li>🎓 Education: <?= $app['education'] ?? 'N/A' ?></li>
                <li>📅 Applied: <?= date('M d, Y', strtotime($app['applied_at'])) ?></li>
              </ul>

              <!-- Download CV -->
              <?php if (!empty($app['file_name'])): ?>
                <a href="../uploads/resumes/<?= htmlspecialchars($app['file_name']) ?>" download class="download-btn">
                  <i class="fas fa-download"></i> Download CV
                </a>
              <?php else: ?>
                <span class="no-resume">No CV Uploaded</span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>No applications in this category.</p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<script>
function toggleDropdown(el) {
  const parent = el.closest('.dropdown');
  document.querySelectorAll('.dropdown').forEach(drop => {
    if (drop !== parent) drop.classList.remove('open');
  });
  parent.classList.toggle('open');
}
document.addEventListener('click', function(e) {
  if (!e.target.closest('.dropdown')) {
    document.querySelectorAll('.dropdown').forEach(drop => drop.classList.remove('open'));
  }
});
</script>

<?php include("../includes/footer_recruiter.php"); ?>
