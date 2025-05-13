<?php
session_start();
include("../includes/db.php");

$success = $error = "";
$recruiter_id = $_SESSION['user_id'] ?? null;

// Utility function to calculate progress
function calculateProfileProgress($data) {
    $fields = ['logo', 'banner', 'company_name', 'about_us', 'org_type', 'industry_type', 'team_size', 'est_year', 'website', 'company_vision', 'facebook', 'twitter', 'instagram', 'youtube', 'map_location', 'phone', 'email'];
    $filled = 0;

    foreach ($fields as $field) {
        if (!empty($data[$field])) $filled++;
    }

    return round(($filled / count($fields)) * 100);
}

// Save form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $recruiter_id) {
    $company_name = $_POST['company_name'];
    $about_us = $_POST['about_us'];
    $logo_name = $_FILES['logo']['name'];
    $banner_name = $_FILES['banner']['name'];

    $upload_dir = "../uploads/company/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Upload files
    $logo_path = $upload_dir . basename($logo_name);
    $banner_path = $upload_dir . basename($banner_name);
    move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path);
    move_uploaded_file($_FILES['banner']['tmp_name'], $banner_path);

    // Check record
    $stmt = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);

    if ($stmt->rowCount() > 0) {
        $update = $conn->prepare("UPDATE company_profiles SET company_name=?, about_us=?, logo=?, banner=? WHERE recruiter_id=?");
        $update->execute([$company_name, $about_us, $logo_name, $banner_name, $recruiter_id]);
    } else {
        $insert = $conn->prepare("INSERT INTO company_profiles (recruiter_id, company_name, about_us, logo, banner) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$recruiter_id, $company_name, $about_us, $logo_name, $banner_name]);
    }

    // Recalculate progress
    $stmt = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);
    $profileData = $stmt->fetch(PDO::FETCH_ASSOC);

    $progress = calculateProfileProgress($profileData);
    $conn->prepare("UPDATE company_profiles SET progress = ?, completion_percentage = ? WHERE recruiter_id = ?")
         ->execute([$progress, $progress, $recruiter_id]);

    header("Location: founding_info.php");
    exit;
}

// Fetch progress for display
$progress = 0;
if ($recruiter_id) {
    $stmt = $conn->prepare("SELECT progress FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);
    $progress = $stmt->fetchColumn() ?: 0;
}
?>

<?php include("../includes/header_recruiter.php"); ?>
<link rel="stylesheet" href="../assets/css/employee_style.css">

<div class="company-profile-wrapper">
  <div class="tab-nav">
    <a href="company_info.php" class="tab active"><i class="fas fa-user-circle"></i> Company Info</a>
    <a href="founding_info.php" class="tab"><i class="fas fa-building"></i> Founding Info</a>
    <a href="social_info.php" class="tab"><i class="fas fa-globe"></i> Social Media</a>
    <a href="contact_info.php" class="tab"><i class="fas fa-envelope"></i> Contact</a>
  </div>

  <!-- Progress Bar -->
  <div class="progress-bar">
    <div class="progress" style="width: <?= $progress ?>%;"><?= $progress ?>% Completed</div>
  </div>

  <h2>Company Info</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" class="company-form">
    <div class="upload-section">
      <div class="upload-box">
        <label>Upload Logo</label>
        <div class="file-drop">
          <input type="file" name="logo" required>
          <p><strong>Browse photo</strong> or drop here<br><small>Min 400px · Max 5MB</small></p>
        </div>
      </div>

      <div class="upload-box wide">
        <label>Banner Image</label>
        <div class="file-drop">
          <input type="file" name="banner" required>
          <p><strong>Browse photo</strong> or drop here<br><small>Ideal size 1520×440 · Max 5MB</small></p>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>Company Name</label>
      <input type="text" name="company_name" required placeholder="Enter your company name">
    </div>

    <div class="form-group">
      <label>About Us</label>
      <textarea name="about_us" rows="4" placeholder="Tell us about your company..."></textarea>
    </div>

    <button type="submit" class="btn-submit">Save & Next</button>
  </form>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
