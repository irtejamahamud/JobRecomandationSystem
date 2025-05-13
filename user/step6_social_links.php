<?php
session_start();
include("../includes/db.php");

$job_seeker_id = $_SESSION['user_id'] ?? null;
$progress = 100;

if (!$job_seeker_id) {
    header("Location: ../login.php");
    exit();
}

// Fetch existing profile data
$stmt = $conn->prepare("SELECT facebook_link, twitter_link, linkedin_link, reddit_link, instagram_link, youtube_link FROM job_seeker_profiles WHERE job_seeker_id = ?");
$stmt->execute([$job_seeker_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $facebook_link = trim($_POST['facebook_link']);
    $twitter_link = trim($_POST['twitter_link']);
    $linkedin_link = trim($_POST['linkedin_link']);
    $reddit_link = trim($_POST['reddit_link']);
    $instagram_link = trim($_POST['instagram_link']);
    $youtube_link = trim($_POST['youtube_link']);

    $stmtCheck = $conn->prepare("SELECT id FROM job_seeker_profiles WHERE job_seeker_id = ?");
    $stmtCheck->execute([$job_seeker_id]);

    if ($stmtCheck->rowCount() > 0) {
        // Update existing
        $update = $conn->prepare("
            UPDATE job_seeker_profiles
            SET facebook_link = ?, twitter_link = ?, linkedin_link = ?, reddit_link = ?, instagram_link = ?, youtube_link = ?
            WHERE job_seeker_id = ?
        ");
        $update->execute([$facebook_link, $twitter_link, $linkedin_link, $reddit_link, $instagram_link, $youtube_link, $job_seeker_id]);
    } else {
        // Insert new
        $insert = $conn->prepare("
            INSERT INTO job_seeker_profiles (job_seeker_id, facebook_link, twitter_link, linkedin_link, reddit_link, instagram_link, youtube_link)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([$job_seeker_id, $facebook_link, $twitter_link, $linkedin_link, $reddit_link, $instagram_link, $youtube_link]);
    }

    header("Location: step6_social_links.php");

    exit();
}
?>

<?php include("../includes/header_jobseeker.php"); ?>
<link rel="stylesheet" href="../assets/css/jobseeker_profile.css">

<div class="profile-wrapper">
<div class="profile-tab-nav">
    <a href="step1_personal.php" class="profile-tab"><i class="fas fa-user-circle"></i> Personal Info</a>
    <a href="step2_education.php" class="profile-tab"><i class="fas fa-graduation-cap"></i> Education</a>
    <a href="step3_experience.php" class="profile-tab"><i class="fas fa-briefcase"></i> Experience</a>
    <a href="step4_skills_languages.php" class="profile-tab"><i class="fas fa-cogs"></i> Skills & Languages</a>
    <a href="step5_resume_upload.php" class="profile-tab"><i class="fas fa-file-alt"></i> Resume</a>
    <a href="step6_social_links.php" class="profile-tab active"><i class="fas fa-share-alt"></i> Social Links</a>
  </div>

<!--<div class="progress-bar">
    <div class="progress" style="width: <?= $progress ?>%;">100% Completed</div>
  </div>--> 

  <h2>Social Links</h2>

  <form action="" method="POST" class="profile-form">
    <div class="form-grid">

      <?php
      $socialPlatforms = [
        ['facebook', 'Facebook', 'fab fa-facebook'],
        ['twitter', 'Twitter', 'fab fa-twitter'],
        ['linkedin', 'LinkedIn', 'fab fa-linkedin'],
        ['reddit', 'Reddit', 'fab fa-reddit'],
        ['instagram', 'Instagram', 'fab fa-instagram'],
        ['youtube', 'YouTube', 'fab fa-youtube']
      ];

      foreach ($socialPlatforms as $social) {
          $field = $social[0] . '_link';
          $label = $social[1];
          $icon = $social[2];
          ?>
          <div class="form-group input-icon full-width">
            <div class="input-label">
              <i class="<?= $icon ?>"></i>
              <span><?= $label ?></span>
            </div>
            <input type="url" name="<?= $field ?>" placeholder="Enter your <?= $label ?> Profile Link" value="<?= htmlspecialchars($profile[$field] ?? '') ?>">
          </div>
      <?php } ?>

    </div>

    <button type="submit" class="btn-submit">Finish Profile</button>
  </form>
</div>

<?php include("../includes/footer_jobseeker.php"); ?>
