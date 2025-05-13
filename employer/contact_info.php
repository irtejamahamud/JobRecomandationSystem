<?php
session_start();
include("../includes/db.php");

$success = $error = "";
$recruiter_id = $_SESSION['user_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && $recruiter_id) {
    $map_location = $_POST['map_location'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);

    if ($stmt->rowCount() > 0) {
        $update = $conn->prepare("UPDATE company_profiles 
            SET map_location = ?, phone = ?, email = ?
            WHERE recruiter_id = ?");
        $update->execute([$map_location, $phone, $email, $recruiter_id]);

        // Recalculate dynamic progress
        $profile = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = ?");
        $profile->execute([$recruiter_id]);
        $data = $profile->fetch(PDO::FETCH_ASSOC);

        $fields = [
            'logo', 'banner', 'company_name', 'about_us',
            'org_type', 'industry_type', 'team_size', 'est_year', 'website', 'company_vision',
            'facebook', 'twitter', 'instagram', 'youtube',
            'map_location', 'phone', 'email'
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($data[$field])) $filled++;
        }

        $progress = round(($filled / count($fields)) * 100);

        $progressUpdate = $conn->prepare("UPDATE company_profiles SET progress = ? WHERE recruiter_id = ?");
        $progressUpdate->execute([$progress, $recruiter_id]);

        $success = "🎉 Company profile setup completed!";
    } else {
        $error = "Please complete previous steps first.";
    }
}

// Fetch current progress to display
$progress = 0;
if ($recruiter_id) {
    $stmt = $conn->prepare("SELECT progress FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);
    $progress = $stmt->fetchColumn() ?? 0;
}
?>

<?php include("../includes/header_recruiter.php"); ?>
<link rel="stylesheet" href="../assets/css/employee_style.css">

<div class="company-profile-wrapper">
  <!-- Navigation Tabs -->
  <div class="tab-nav">
    <a href="company_info.php" class="tab"><i class="fas fa-user-circle"></i> Company Info</a>
    <a href="founding_info.php" class="tab"><i class="fas fa-building"></i> Founding Info</a>
    <a href="social_info.php" class="tab"><i class="fas fa-globe"></i> Social Media</a>
    <a href="contact_info.php" class="tab active"><i class="fas fa-envelope"></i> Contact</a>
  </div>

  <!-- Progress Bar -->
  <div class="progress-bar">
    <div class="progress" style="width: <?= $progress ?>%;"><?= $progress ?>% Completed</div>
  </div>

  <h2>Contact Information</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
    <div style="text-align:center; margin-top:20px;">
        <a href="dashboard.php" class="btn-submit">View Dashboard</a>
        <a href="post_job.php" class="btn-outline">Post a Job</a>
    </div>
  <?php else: ?>
  <form method="POST" class="company-form">
    <div class="form-group full-width">
      <label>Map Location</label>
      <input type="text" name="map_location" placeholder="Paste your map embed or location">
    </div>

    <div class="form-group full-width">
      <label>Phone</label>
      <input type="text" name="phone" placeholder="+8801XXXXXXXXX">
    </div>

    <div class="form-group full-width">
      <label>Email</label>
      <input type="email" name="email" placeholder="example@company.com">
    </div>

    <div class="form-navigation">
      <a href="social_info.php" class="btn-outline">← Previous</a>
      <button type="submit" class="btn-submit">Finish Editing</button>
    </div>
  </form>
  <?php endif; ?>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
