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
$stmt = $conn->prepare("
    SELECT jobs.*, cp.logo, cp.company_name
    FROM saved_jobs 
    JOIN jobs ON saved_jobs.job_id = jobs.job_id 
    LEFT JOIN company_profiles cp ON jobs.recruiter_id = cp.recruiter_id
    WHERE saved_jobs.job_seeker_id = ?
    ORDER BY saved_jobs.saved_at DESC
");
$stmt->execute([$job_seeker_id]);
$savedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/saved_jobs.css">

<div class="saved-jobs-container">
    <h1 class="page-title">Saved Jobs</h1>

    <div class="jobs-grid">
        <?php if (count($savedJobs) > 0): ?>
            <?php foreach ($savedJobs as $job): ?>
                <div class="job-card">
                    <div class="job-type-badge"><?= htmlspecialchars($job['job_level']) ?></div>

                    <div class="company-logo">
                        <img src="<?= !empty($job['logo']) ? '../uploads/company/' . htmlspecialchars($job['logo']) : '../uploads/company/default_logo.png' ?>" alt="Company Logo">
                    </div>

                    <h2 class="job-title"><?= htmlspecialchars($job['job_title']) ?></h2>

                    <div class="company-name"><?= htmlspecialchars($job['company_name']) ?: 'Company Name' ?></div>

                    <div class="location-salary">
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></span>
                        <span><i class="fas fa-wallet"></i> $<?= number_format($job['min_salary']) ?> - $<?= number_format($job['max_salary']) ?></span>
                    </div>

                    <div class="bookmark-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No saved jobs yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer_jobseeker.php'); ?>
