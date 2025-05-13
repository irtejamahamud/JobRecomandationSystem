<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: ../login.php");
    exit();
}

$job_id = $_GET['job_id'] ?? 0;

if ($job_id > 0) {
    try {
        // Begin transaction
        $conn->beginTransaction();

        // Delete from dependent tables
        $delete_apps = $conn->prepare("DELETE FROM applications WHERE job_id = :job_id");
        $delete_apps->execute([':job_id' => $job_id]);

        $delete_applied = $conn->prepare("DELETE FROM applied_jobs WHERE job_id = :job_id");
        $delete_applied->execute([':job_id' => $job_id]);

        $delete_saved = $conn->prepare("DELETE FROM saved_jobs WHERE job_id = :job_id");
        $delete_saved->execute([':job_id' => $job_id]);

        $delete_views = $conn->prepare("DELETE FROM job_views WHERE job_id = :job_id");
        $delete_views->execute([':job_id' => $job_id]);

        $delete_details = $conn->prepare("DELETE FROM job_details WHERE job_id = :job_id");
        $delete_details->execute([':job_id' => $job_id]);

        // Finally, delete the job itself
        $delete_job = $conn->prepare("DELETE FROM jobs WHERE job_id = :job_id");
        $delete_job->execute([':job_id' => $job_id]);

        $conn->commit();
        header("Location: dashboard.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error deleting job: " . $e->getMessage();
    }
} else {
    echo "Invalid Job ID";
}
?>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
  <div class="alert success">Job deleted successfully.</div>
<?php endif; ?>
