<?php
session_start();
include('../includes/db.php');
include('../includes/header_jobseeker.php');

// Check if job seeker is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$jobseeker_name = $_SESSION['fullname'] ?? 'Job Seeker';

// Fetch applied jobs from applied_jobs table
$stmt = $conn->prepare("
    SELECT jobs.job_id, jobs.job_title, jobs.city, jobs.min_salary, jobs.max_salary, jobs.job_level, jobs.recruiter_id, applied_jobs.applied_at, applied_jobs.status, company_profiles.logo, company_profiles.company_name
    FROM applied_jobs
    JOIN jobs ON applied_jobs.job_id = jobs.job_id
    LEFT JOIN company_profiles ON jobs.recruiter_id = company_profiles.recruiter_id
    WHERE applied_jobs.job_seeker_id = :user_id
    ORDER BY applied_jobs.applied_at DESC
");
$stmt->execute([':user_id' => $user_id]);
$applied_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.applied-jobs-table {
  margin-top: 10px;
}
.job-logo {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #eee;
  object-fit: cover;
}
</style>

<div class="container">
  <h1 class="dashboard-title">Applied Jobs <span style="color: #999; font-weight: normal;">(<?= count($applied_jobs) ?>)</span></h1>

  <div class="applied-jobs-table">
    <div class="table-header">
      <span>Jobs</span>
      <span>Date Applied</span>
      <span>Status</span>
      <span>Action</span>
    </div>

    <?php if (count($applied_jobs) > 0): ?>
      <?php foreach ($applied_jobs as $job): ?>
        <div class="table-row">
          <div class="job-info">
            <?php
              if (!empty($job['logo'])) {
                  $logo_path = '../uploads/company/' . htmlspecialchars($job['logo']);
              } else {
                  $logo_path = '../assets/img/logo-default.jpg';
              }
            ?>
            <img src="<?= $logo_path ?>" alt="Company Logo" class="job-logo">
            <div>
              <strong><?= htmlspecialchars($job['job_title']) ?></strong><br>
              <small style="color: #777;">
                <?= htmlspecialchars($job['company_name'] ?? 'Unknown Company') ?>
              </small>
              <div class="job-meta" style="margin-top: 5px;">
                <span><?= htmlspecialchars($job['city']) ?></span> · 
                <span><?= htmlspecialchars($job['min_salary']) ?>-<?= htmlspecialchars($job['max_salary']) ?></span> · 
                <span class="tag"><?= htmlspecialchars($job['job_level']) ?></span>
              </div>
            </div>
          </div>
          <span><?= date('M d, Y', strtotime($job['applied_at'])) ?></span>
          <span class="status <?= strtolower($job['status']) ?>"><?= $job['status'] ?></span>
          <a href="../user/job_details.php?job_id=<?= $job['job_id'] ?>" class="view-btn">View Details</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="margin-top: 20px; color: #777;">You haven't applied for any jobs yet.</p>
    <?php endif; ?>
  </div>

  <!-- Pagination (Optional for future if needed) -->
  <div class="pagination">
    <a href="#" class="page-arrow disabled">←</a>
    <a href="#" class="page active">01</a>
    <a href="#" class="page">02</a>
    <a href="#" class="page">03</a>
    <a href="#" class="page">04</a>
    <a href="#" class="page">05</a>
    <a href="#" class="page-arrow">→</a>
  </div>
</div>

<?php include('../includes/footer_jobseeker.php'); ?>
