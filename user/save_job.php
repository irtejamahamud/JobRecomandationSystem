<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
  echo json_encode(["error" => "Not logged in"]);
  exit();
}

$job_seeker_id = $_SESSION['user_id'];

if (isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);

    // Check if already saved
    $check = $conn->prepare("SELECT id FROM saved_jobs WHERE job_seeker_id = ? AND job_id = ?");
    $check->execute([$job_seeker_id, $job_id]);
    $exists = $check->fetch();

    if ($exists) {
        // Remove from saved jobs
        $del = $conn->prepare("DELETE FROM saved_jobs WHERE job_seeker_id = ? AND job_id = ?");
        $del->execute([$job_seeker_id, $job_id]);
        echo json_encode(["status" => "removed"]);
    } else {
        // Save into saved jobs
        $ins = $conn->prepare("INSERT INTO saved_jobs (job_seeker_id, job_id, saved_at) VALUES (?, ?, NOW())");
        $ins->execute([$job_seeker_id, $job_id]);
        echo json_encode(["status" => "saved"]);
    }
} else {
    echo json_encode(["error" => "No job_id sent"]);
}
?>
