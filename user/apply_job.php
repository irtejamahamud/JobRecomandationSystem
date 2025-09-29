<?php
session_start();
include("../includes/db.php");
include_once("../includes/notifications.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['job_id'])) {
    echo "Invalid job.";
    exit;
}

$job_id = intval($_GET['job_id']);
$job_seeker_id = $_SESSION['user_id'];

try {
    // ✅ Check if already applied to prevent duplicate application
    $checkStmt = $conn->prepare("SELECT * FROM applied_jobs WHERE job_id = :job_id AND job_seeker_id = :job_seeker_id");
    $checkStmt->execute([
        ':job_id' => $job_id,
        ':job_seeker_id' => $job_seeker_id
    ]);

    if ($checkStmt->rowCount() > 0) {
        // Already applied
        header("Location: job_details.php?job_id=$job_id&already_applied=1");
        exit;
    }

    // ✅ Insert into applied_jobs
    $applyStmt = $conn->prepare("
        INSERT INTO applied_jobs (job_id, job_seeker_id, applied_at, status)
        VALUES (:job_id, :job_seeker_id, NOW(), 'Pending')
    ");
    $applyStmt->execute([
        ':job_id' => $job_id,
        ':job_seeker_id' => $job_seeker_id
    ]);

    // ✅ Insert into applications (for recruiter view)
    $applicationStmt = $conn->prepare("
        INSERT INTO applications (job_id, job_seeker_id, status, applied_at)
        VALUES (:job_id, :job_seeker_id, 'Pending', NOW())
    ");
    $applicationStmt->execute([
        ':job_id' => $job_id,
        ':job_seeker_id' => $job_seeker_id
    ]);

    // 🔔 Notify recruiter about new application
    // Find recruiter user_id for this job
    $recStmt = $conn->prepare("SELECT recruiter_id, job_title FROM jobs WHERE job_id = ?");
    $recStmt->execute([$job_id]);
    $jobRow = $recStmt->fetch(PDO::FETCH_ASSOC);
    if ($jobRow && !empty($jobRow['recruiter_id'])) {
        $recruiterUserId = (int)$jobRow['recruiter_id'];
        $title = 'New application received';
        $body  = 'A candidate applied for your job: ' . ($jobRow['job_title'] ?? '');
        add_notification($conn, $job_seeker_id, 'application', $title, $body, 'job', $job_id, [$recruiterUserId], ['job_id'=>$job_id]);
    }

    // ✅ Redirect back to job details with success
    header("Location: job_details.php?job_id=$job_id&applied_success=1");
    exit;

} catch (PDOException $e) {
    echo "Error applying for the job: " . $e->getMessage();
    exit;
}
?>
