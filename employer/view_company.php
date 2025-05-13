<?php
session_start();
include("../includes/db.php");
include("../includes/header_recruiter.php");

$recruiter_id = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = :recruiter_id");
$stmt->execute([':recruiter_id' => $recruiter_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/company_view_recruiter.css">

<div class="company-profile-wrapper">
  <h2>Your Company Profile</h2>

  <!-- Logo and Banner -->
  <div class="upload-section">
    <div class="upload-box wide">
      <img src="<?= !empty($company['banner']) ? '../uploads/company/' . $company['banner'] : '../assets/img/logo-default.jpg' ?>" 
           alt="Banner">
    </div>
    <div class="upload-box">
      <img src="<?= !empty($company['logo']) ? '../uploads/company/' . $company['logo'] : '../assets/img/logo-default.jpg' ?>" 
           alt="Logo">
    </div>
  </div>

  <!-- Company Info -->
  <div class="form-group">
    <label><i class="fas fa-building"></i> <strong>Company Name:</strong></label>
    <p><?= htmlspecialchars($company['company_name'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-align-left"></i> <strong>About Us:</strong></label>
    <p><?= nl2br(htmlspecialchars($company['about_us'] ?? 'N/A')) ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-industry"></i> <strong>Industry:</strong></label>
    <p><?= htmlspecialchars($company['industry_type'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-users"></i> <strong>Team Size:</strong></label>
    <p><?= htmlspecialchars($company['team_size'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-briefcase"></i> <strong>Organization Type:</strong></label>
    <p><?= htmlspecialchars($company['org_type'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-calendar-alt"></i> <strong>Year Established:</strong></label>
    <p><?= htmlspecialchars($company['est_year'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-bullseye"></i> <strong>Vision:</strong></label>
    <p><?= nl2br(htmlspecialchars($company['company_vision'] ?? 'N/A')) ?></p>
  </div>

  <!-- Contact Info -->
  <div class="form-group">
    <label><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong></label>
    <p><?= htmlspecialchars($company['map_location'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-envelope"></i> <strong>Email:</strong></label>
    <p><?= htmlspecialchars($company['email'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-phone-alt"></i> <strong>Phone:</strong></label>
    <p><?= htmlspecialchars($company['phone'] ?? 'N/A') ?></p>
  </div>

  <div class="form-group">
    <label><i class="fas fa-globe"></i> <strong>Website:</strong></label>
    <p><a href="<?= htmlspecialchars($company['website'] ?? '#') ?>" target="_blank"><?= htmlspecialchars($company['website'] ?? 'N/A') ?></a></p>
  </div>

  <!-- Social Links -->
  <div class="form-group">
    <label><i class="fas fa-share-alt"></i> <strong>Social Media:</strong></label>
    <p>
      <?= !empty($company['facebook']) ? "<a href='{$company['facebook']}' target='_blank'><i class='fab fa-facebook'></i> Facebook</a> | " : '' ?>
      <?= !empty($company['twitter']) ? "<a href='{$company['twitter']}' target='_blank'><i class='fab fa-twitter'></i> Twitter</a> | " : '' ?>
      <?= !empty($company['linkedin']) ? "<a href='{$company['linkedin']}' target='_blank'><i class='fab fa-linkedin'></i> LinkedIn</a> | " : '' ?>
      <?= !empty($company['instagram']) ? "<a href='{$company['instagram']}' target='_blank'><i class='fab fa-instagram'></i> Instagram</a>" : '' ?>
    </p>
  </div>

  <!-- Completion -->
  <div class="form-group">
    <label><i class="fas fa-chart-line"></i> <strong>Profile Completion:</strong></label>
    <div class="progress-bar">
      <div class="progress" style="width: <?= $company['completion_percentage'] ?? 0 ?>%;">
        <?= $company['completion_percentage'] ?? 0 ?>%
      </div>
    </div>
  </div>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
