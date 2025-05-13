<?php
session_start();
include("../includes/db.php");
include("../includes/header_recruiter.php");

// Get recruiter ID
$recruiter_id = $_SESSION['user_id'] ?? 0;

// Get recruiter name
$recruiter_name = $_SESSION['fullname'] ?? 'Recruiter';

// Get jobs
$stmt = $conn->prepare("SELECT * FROM jobs WHERE recruiter_id = :recruiter_id ORDER BY job_id DESC");
$stmt->execute([':recruiter_id' => $recruiter_id]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get number of applications
$applications_stmt = $conn->prepare("
    SELECT job_id, COUNT(*) as total_apps 
    FROM applications 
    WHERE job_id IN (SELECT job_id FROM jobs WHERE recruiter_id = :recruiter_id)
    GROUP BY job_id
");
$applications_stmt->execute([':recruiter_id' => $recruiter_id]);
$applications = $applications_stmt->fetchAll(PDO::FETCH_ASSOC);

// Build easy lookup array
$app_counts = [];
foreach ($applications as $app) {
    $app_counts[$app['job_id']] = $app['total_apps'];
}
?>

<div class="dashboard-container">
    <h1>Welcome, <?= htmlspecialchars($recruiter_name) ?></h1>
    <p class="subtitle">Manage your job posts, track applicants, and update your profile.</p>

    <!-- Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h3>Posted Jobs</h3>
            <p><strong><?= count($jobs) ?></strong> Active</p>
            <a href="my_jobs.php" class="dashboard-btn">View All</a>
        </div>

        <div class="card">
            <h3>Applications</h3>
            <p><strong><?= array_sum($app_counts) ?></strong> New</p>
            <a href="applications.php" class="dashboard-btn">View Applicants</a>
        </div>

        <div class="card">
            <h3>Post a New Job</h3>
            <p>Find top talent fast</p>
            <a href="post_job.php" class="dashboard-btn highlight">Post Job</a>
        </div>

        <div class="card">
            <h3>Profile</h3>
            <p>Edit your details</p>
            <a href="company_info.php" class="dashboard-btn">Update Profile</a>
        </div>
    </div>

    <!-- Metrics -->
    <div class="dashboard-stats">
        <div class="stat-box">
            <div class="icon"><i class="fas fa-briefcase"></i></div>
            <div class="details">
                <h2><?= count($jobs) ?></h2>
                <p>Open Jobs</p>
            </div>
        </div>
        <div class="stat-box">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div class="details">
                <h2><?= array_sum($app_counts) ?></h2>
                <p>Saved Candidates</p>
            </div>
        </div>
    </div>

    <!-- Recently Posted Jobs Table -->
    <table class="jobs-table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Status</th>
                <th>Applications</th>
                <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($jobs): ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                        $expire_date = new DateTime($job['expire_date']);
                        $today = new DateTime();
                        $status = $expire_date >= $today ? "Active" : "Expired";
                        $days_left = $today->diff($expire_date)->days;
                        $app_count = $app_counts[$job['job_id']] ?? 0;
                    ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($job['job_title']) ?><br>
                            <small><?= htmlspecialchars($job['job_level']) ?> · 
                                <?= $status == 'Active' ? "$days_left days left" : "Expired" ?>
                            </small>
                        </td>
                        <td><span class="job-status <?= strtolower($status) ?>"><?= $status ?></span></td>
                        <td><?= $app_count ?> Applications</td>
                        <td class="actions relative">
                            <a href="applications.php?job_id=<?= $job['job_id'] ?>" class="view-btn">View Applications</a>
                            <button class="more-options" onclick="toggleOptions(this)">⋮</button>
                            <div class="options-dropdown">
  <a href="#">Promote Job</a>
  <a href="view_detail.php?job_id=<?= $job['job_id'] ?>">View Detail</a>
  <?php if ($status == 'Active'): ?>
    <a href="mark_expired.php?job_id=<?= $job['job_id'] ?>" onclick="return confirm('Mark job as expired?')">Mark as Expired</a>
  <?php endif; ?>
  <a href="delete_job.php?job_id=<?= $job['job_id'] ?>" onclick="return confirm('Are you sure you want to delete this job and all related data?')">Delete Job</a>
</div>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No jobs posted yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleOptions(button) {
    const dropdown = button.nextElementSibling;
    document.querySelectorAll('.options-dropdown').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
    });
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

window.onclick = function(e) {
    if (!e.target.matches('.more-options')) {
        document.querySelectorAll('.options-dropdown').forEach(d => d.style.display = 'none');
    }
}
</script>

<?php include("../includes/footer_recruiter.php"); ?>
