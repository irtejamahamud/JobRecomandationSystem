<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['job_id'])) {
    header("Location: ../login.php");
    exit;
}

$job_id = $_GET['job_id'];
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Update the expire_date to yesterday
$stmt = $conn->prepare("UPDATE jobs SET expire_date = :expire_date WHERE job_id = :job_id AND recruiter_id = :recruiter_id");
$stmt->execute([
    ':expire_date' => $yesterday,
    ':job_id' => $job_id,
    ':recruiter_id' => $_SESSION['user_id']
]);

// Redirect back to My Jobs page
header("Location: my_jobs.php");
exit;
?>
