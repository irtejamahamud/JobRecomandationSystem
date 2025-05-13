<?php
session_start();
include("../includes/db.php");
include("../includes/header_recruiter.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$recruiter_id = $_SESSION['user_id'];

// Fetch jobs
$stmt = $conn->prepare("SELECT * FROM jobs WHERE recruiter_id = :recruiter_id ORDER BY job_id DESC");
$stmt->execute([':recruiter_id' => $recruiter_id]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get application counts
$appCounts = [];
$appStmt = $conn->prepare("SELECT job_id, COUNT(*) AS total_apps FROM applications GROUP BY job_id");
$appStmt->execute();
while ($row = $appStmt->fetch(PDO::FETCH_ASSOC)) {
    $appCounts[$row['job_id']] = $row['total_apps'];
}

// Expiry check
function isExpired($expire_date) {
    return strtotime($expire_date) < time();
}
?>

<link rel="stylesheet" href="../assets/css/my_jobs.css">
<div class="my-jobs-wrapper">
    <h2>My Posted Jobs</h2>
    <table class="my-jobs-table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Status</th>
                <th>Applications</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($job['job_title']) ?></strong><br>
                        <small><?= htmlspecialchars($job['job_role']) ?> · 
                        <?= isExpired($job['expire_date']) ? 'Expired' : date_diff(date_create(), date_create($job['expire_date']))->format('%a days left') ?>
                        </small>
                    </td>
                    <td>
                        <span class="badge <?= isExpired($job['expire_date']) ? 'expired' : 'active' ?>">
                            <?= isExpired($job['expire_date']) ? 'Expired' : 'Active' ?>
                        </span>
                    </td>
                    <td>
                        <?= $appCounts[$job['job_id']] ?? 0 ?> Applications
                    </td>
                    <td class="job-actions-wrapper">
                        <a href="applications.php?job_id=<?= $job['job_id'] ?>" class="btn-view">View Applications</a>
                        <div class="job-actions-dropdown">
                            <button class="dropdown-toggle" onclick="toggleDropdown(this)">⋮</button>
                            <ul class="dropdown-menu">
                                <li><a href="#">Promote Job</a></li>
                                <li><a href="view_detail.php?job_id=<?= $job['job_id'] ?>">View Detail</a></li>
                                <li><a href="mark_expired.php?job_id=<?= $job['job_id'] ?>" onclick="return confirm('Are you sure?')">Mark as Expired</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function toggleDropdown(btn) {
  const menu = btn.nextElementSibling;

  // Close all open menus first
  document.querySelectorAll('.job-actions-dropdown .dropdown-menu').forEach(m => {
    if (m !== menu) m.style.display = 'none';
  });

  // Toggle selected menu
  menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

window.onclick = function(e) {
  if (!e.target.closest('.job-actions-dropdown')) {
    document.querySelectorAll('.job-actions-dropdown .dropdown-menu').forEach(m => m.style.display = 'none');
  }
}
</script>

<?php include("../includes/footer_recruiter.php"); ?>
