<?php
session_start();
include("../includes/db.php");

$success = $error = "";
$recruiter_id = $_SESSION['user_id'] ?? null;

// Handle submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $recruiter_id) {
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $youtube = $_POST['youtube'];

    $stmt = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);

    if ($stmt->rowCount() > 0) {
        $update = $conn->prepare("UPDATE company_profiles 
            SET facebook=?, twitter=?, instagram=?, youtube=?
            WHERE recruiter_id=?");
        $update->execute([$facebook, $twitter, $instagram, $youtube, $recruiter_id]);

        // Recalculate dynamic progress
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        $fields = [
            'logo', 'banner', 'company_name', 'about_us',
            'org_type', 'industry_type', 'team_size', 'est_year', 'website', 'company_vision',
            'facebook', 'twitter', 'instagram', 'youtube',
            'map_location', 'phone', 'email'
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($profile[$field])) $filled++;
        }

        $progress = round(($filled / count($fields)) * 100);

        $updateProgress = $conn->prepare("UPDATE company_profiles SET progress = ? WHERE recruiter_id = ?");
        $updateProgress->execute([$progress, $recruiter_id]);

        header("Location: contact_info.php");
        exit;
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
    <a href="social_info.php" class="tab active"><i class="fas fa-globe"></i> Social Media</a>
    <a href="contact_info.php" class="tab"><i class="fas fa-envelope"></i> Contact</a>
  </div>

  <!-- Dynamic Progress Bar -->
  <div class="progress-bar">
    <div class="progress" style="width: <?= $progress ?>%;"><?= $progress ?>% Completed</div>
  </div>

  <h2>Social Media Profile</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <form action="" method="POST" class="company-form">
    <div class="form-group full-width">
      <label>Facebook</label>
      <input type="url" name="facebook" placeholder="Facebook profile link...">
    </div>

    <div class="form-group full-width">
      <label>Twitter</label>
      <input type="url" name="twitter" placeholder="Twitter profile link...">
    </div>

    <div class="form-group full-width">
      <label>Instagram</label>
      <input type="url" name="instagram" placeholder="Instagram profile link...">
    </div>

    <div class="form-group full-width">
      <label>YouTube</label>
      <input type="url" name="youtube" placeholder="YouTube channel link...">
    </div>

    <div class="form-navigation">
      <a href="founding_info.php" class="btn-outline">← Previous</a>
      <button type="submit" class="btn-submit">Save & Next</button>
    </div>
  </form>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
