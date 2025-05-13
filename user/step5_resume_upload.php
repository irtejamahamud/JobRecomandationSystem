<?php
session_start();
include("../includes/db.php");

$job_seeker_id = $_SESSION['user_id'] ?? null;
$success = $error = "";

if (!$job_seeker_id) {
    header("Location: ../login.php");
    exit();
}

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
    $resume_title = trim($_POST['resume_title']);
    $resume_name = $_FILES['resume']['name'];
    $target_dir = "../uploads/resumes/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $new_filename = time() . "_" . basename($resume_name);
    $target_file = $target_dir . $new_filename;

    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($fileType !== 'pdf') {
        $error = "Only PDF files are allowed.";
    } elseif ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
        $error = "File size should be less than 5MB.";
    } elseif (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO resumes (job_seeker_id, resume_title, file_name) VALUES (?, ?, ?)");
        $stmt->execute([$job_seeker_id, $resume_title, $new_filename]);
        $success = "Resume uploaded successfully!";
    } else {
        $error = "Failed to upload file. Please try again.";
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("SELECT file_name FROM resumes WHERE id = ? AND job_seeker_id = ?");
    $stmt->execute([$delete_id, $job_seeker_id]);
    $resume = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resume) {
        unlink("../uploads/resumes/" . $resume['file_name']);
        $del = $conn->prepare("DELETE FROM resumes WHERE id = ? AND job_seeker_id = ?");
        $del->execute([$delete_id, $job_seeker_id]);
        $success = "Resume deleted successfully.";
    }
}

// Fetch existing resumes
$resumes = $conn->prepare("SELECT * FROM resumes WHERE job_seeker_id = ?");
$resumes->execute([$job_seeker_id]);
$resumeList = $resumes->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../includes/header_jobseeker.php"); ?>
<link rel="stylesheet" href="../assets/css/jobseeker_profile.css">

<div class="profile-wrapper">

<div class="profile-tab-nav">
    <a href="step1_personal.php" class="profile-tab"><i class="fas fa-user-circle"></i> Personal Info</a>
    <a href="step2_education.php" class="profile-tab"><i class="fas fa-graduation-cap"></i> Education</a>
    <a href="step3_experience.php" class="profile-tab"><i class="fas fa-briefcase"></i> Experience</a>
    <a href="step4_skills_languages.php" class="profile-tab"><i class="fas fa-cogs"></i> Skills & Languages</a>
    <a href="step5_resume_upload.php" class="profile-tab active"><i class="fas fa-file-alt"></i> Resume</a>
    <a href="step6_social_links.php" class="profile-tab"><i class="fas fa-share-alt"></i> Social Links</a>
</div>

<h2>Your CV/Resume</h2>

<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Upload New Resume Section -->
<div class="upload-resume-card">
  <form method="POST" enctype="multipart/form-data">
    <label class="upload-resume-box">
      <i class="fas fa-plus-circle"></i>
      <p>Add CV/Resume</p>
      <small>Browse file or drop here. Only PDF</small>
      <input type="file" name="resume" accept="application/pdf" required onchange="showResumeInput()" hidden>
    </label>

    <input type="text" name="resume_title" id="resumeTitleInput" placeholder="Enter Resume Title" class="resume-title-input" style="display:none;" required>
    <button type="submit" id="submitResumeBtn" class="btn-submit" style="display:none; margin-top: 10px;">Upload Resume</button>
  </form>
</div>

<!-- Uploaded Resumes List -->
<div class="resume-grid">

  <?php foreach ($resumeList as $resume): ?>
    <div class="resume-card">
      <div class="resume-icon"><i class="fas fa-file-pdf"></i></div>
      <div class="resume-details">
        <h4><?= htmlspecialchars($resume['resume_title']) ?></h4>
        <p><?= round(filesize("../uploads/resumes/" . $resume['file_name']) / 1048576, 1) ?> MB</p>
      </div>
      <div class="resume-options">
        <div class="dropdown">
          <button class="dropbtn">⋮</button>
          <div class="dropdown-content">
            <a href="../uploads/resumes/<?= htmlspecialchars($resume['file_name']) ?>" target="_blank">View Resume</a>
            <a href="?delete_id=<?= $resume['id'] ?>" onclick="return confirm('Are you sure you want to delete this resume?')">Delete</a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

</div>

<!-- Next Button -->
<div style="text-align:right; margin-top: 30px;">
<a href="step6_social_links.php" class="btn-submit">Next ➔</a>

</div>

</div>

<script>
function showResumeInput() {
    document.getElementById('resumeTitleInput').style.display = 'block';
    document.getElementById('submitResumeBtn').style.display = 'inline-block';
}
</script>

<?php include("../includes/footer_jobseeker.php"); ?>
