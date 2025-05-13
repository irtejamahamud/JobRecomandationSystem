<?php
session_start();
include("../includes/db.php");

$success = $error = "";
$recruiter_id = $_SESSION['user_id'] ?? null;

// Update the data if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && $recruiter_id) {
    $organization_type = $_POST['organization_type'];
    $industry_type = $_POST['industry_type'];
    $team_size = $_POST['team_size'];
    $establishment_year = $_POST['establishment_year'];
    $website = $_POST['website'];
    $company_vision = $_POST['company_vision'];

    // Update company_profiles
    $stmt = $conn->prepare("UPDATE company_profiles SET 
        org_type = ?, 
        industry_type = ?, 
        team_size = ?, 
        est_year = ?, 
        website = ?, 
        company_vision = ?
        WHERE recruiter_id = ?");
    $stmt->execute([
        $organization_type, 
        $industry_type, 
        $team_size, 
        $establishment_year, 
        $website, 
        $company_vision, 
        $recruiter_id
    ]);

    // Progress Recalculation Logic
    $stmt2 = $conn->prepare("SELECT * FROM company_profiles WHERE recruiter_id = ?");
    $stmt2->execute([$recruiter_id]);
    $profile = $stmt2->fetch(PDO::FETCH_ASSOC);

    $fields = [
        'logo', 'banner', 'company_name', 'about_us',         // Company Info (25%)
        'org_type', 'industry_type', 'team_size', 'est_year', 'website', 'company_vision', // Founding Info (25%)
        'facebook', 'twitter', 'instagram', 'youtube',        // Social Info (25%)
        'map_location', 'phone', 'email'                      // Contact Info (25%)
    ];

    $filled = 0;
    foreach ($fields as $field) {
        if (!empty($profile[$field])) $filled++;
    }

    $progress = round(($filled / count($fields)) * 100);

    $updateProgress = $conn->prepare("UPDATE company_profiles SET progress = ? WHERE recruiter_id = ?");
    $updateProgress->execute([$progress, $recruiter_id]);

    header("Location: social_info.php");
    exit;
}

// Get progress to display in bar
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
    <a href="founding_info.php" class="tab active"><i class="fas fa-building"></i> Founding Info</a>
    <a href="social_info.php" class="tab"><i class="fas fa-globe"></i> Social Media</a>
    <a href="contact_info.php" class="tab"><i class="fas fa-envelope"></i> Contact</a>
  </div>

  <!-- Progress Bar -->
  <div class="progress-bar">
    <div class="progress" style="width: <?= $progress ?>%;"><?= $progress ?>% Completed</div>
  </div>

  <h2>Founding Info</h2>
  <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>

  <form method="POST" class="founding-form">
    <div class="form-row">
      <div class="form-group">
        <label>Organization Type</label>
        <select name="organization_type" required>
          <option value="">Select...</option>
          <option>Private</option>
          <option>Public</option>
          <option>NGO</option>
          <option>Startup</option>
        </select>
      </div>

      <div class="form-group">
        <label>Industry Type</label>
        <select name="industry_type" required>
          <option value="">Select...</option>
          <option>Technology</option>
          <option>Finance</option>
          <option>Education</option>
          <option>Healthcare</option>
        </select>
      </div>

      <div class="form-group">
        <label>Team Size</label>
        <select name="team_size">
          <option value="">Select...</option>
          <option>1–10</option>
          <option>11–50</option>
          <option>51–200</option>
          <option>200+</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Year of Establishment</label>
        <input type="date" name="establishment_year">
      </div>

      <div class="form-group full-width">
        <label>Company Website</label>
        <input type="url" name="website" placeholder="https://example.com">
      </div>
    </div>

    <div class="form-group full-width">
      <label>Company Vision</label>
      <textarea name="company_vision" rows="4" placeholder="Share your company's vision..."></textarea>
    </div>

    <div class="form-navigation">
      <a href="company_info.php" class="btn-outline">← Previous</a>
      <button type="submit" class="btn-submit">Save & Next</button>
    </div>
  </form>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
