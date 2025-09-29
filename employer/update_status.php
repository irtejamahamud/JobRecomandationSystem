<?php
session_start();
include("../includes/db.php");
include_once("../includes/notifications.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['status'], $_POST['job_id'])) {
    $email = trim($_POST['email']);
    $status = trim($_POST['status']);
    $job_id = intval($_POST['job_id']);

    $validStatuses = ['Pending', 'Shortlisted', 'Rejected'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
    } elseif (!in_array($status, $validStatuses)) {
        $_SESSION['error'] = "Invalid status value.";
    } elseif ($job_id <= 0) {
        $_SESSION['error'] = "Invalid job ID.";
    } else {
        // Get user ID
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $job_seeker_id = $user['id'];

            // Update status for the specific job application
            $update = $conn->prepare("
                UPDATE applications 
                SET status = :status 
                WHERE job_seeker_id = :uid AND job_id = :jobid
            ");
            $update->execute([
                ':status' => $status,
                ':uid' => $job_seeker_id,
                ':jobid' => $job_id
            ]);

            // 🔔 Notify the job seeker about status update
            $actor = $_SESSION['user_id'] ?? null; // recruiter acting
            $title = 'Application status updated';
            $body  = 'Your application status was updated to ' . $status . ' for Job ID #' . $job_id;
            add_notification($conn, $actor, 'status_update', $title, $body, 'job', $job_id, [$job_seeker_id], ['status'=>$status,'job_id'=>$job_id]);

            $_SESSION['success'] = "Status updated for this job application.";
        } else {
            $_SESSION['error'] = "User not found.";
        }
    }
}

header("Location: applications.php?filter=" . ($_GET['filter'] ?? 'all'));
exit();
