<?php
session_start();
include('../includes/db.php');
include('../includes/header_recruiter.php');

if (!isset($_GET['job_seeker_id'])) {
    echo "Invalid access.";
    exit();
}

$job_seeker_id = intval($_GET['job_seeker_id']);

// Fetch basic job seeker info
$stmt = $conn->prepare("
    SELECT js.*, jsp.*, u.email
    FROM job_seekers js
    LEFT JOIN job_seeker_profiles jsp ON js.job_seeker_id = jsp.job_seeker_id
    LEFT JOIN users u ON js.job_seeker_id = u.id
    WHERE js.job_seeker_id = ?
");
$stmt->execute([$job_seeker_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch education
$eduStmt = $conn->prepare("SELECT * FROM education WHERE job_seeker_id = ? ORDER BY start_year DESC");
$eduStmt->execute([$job_seeker_id]);
$educations = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch certifications
$certStmt = $conn->prepare("SELECT * FROM certifications WHERE job_seeker_id = ?");
$certStmt->execute([$job_seeker_id]);
$certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects
$projStmt = $conn->prepare("SELECT * FROM projects WHERE job_seeker_id = ?");
$projStmt->execute([$job_seeker_id]);
$projects = $projStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch skills
$skillStmt = $conn->prepare("
    SELECT sm.name, jss.proficiency
    FROM job_seeker_skills jss
    JOIN skill_master sm ON jss.skill_id = sm.id
    WHERE jss.job_seeker_id = ?
");
$skillStmt->execute([$job_seeker_id]);
$skills = $skillStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch languages
$langStmt = $conn->prepare("SELECT * FROM languages WHERE job_seeker_id = ?");
$langStmt->execute([$job_seeker_id]);
$languages = $langStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch experience
$expStmt = $conn->prepare("SELECT start_date, end_date FROM experience WHERE job_seeker_id = ?");
$expStmt->execute([$job_seeker_id]);
$experienceRecords = $expStmt->fetchAll(PDO::FETCH_ASSOC);

$totalMonths = 0;
foreach ($experienceRecords as $exp) {
    $start = new DateTime($exp['start_date']);
    $end = (!empty($exp['end_date'])) ? new DateTime($exp['end_date']) : new DateTime();
    $interval = $start->diff($end);
    $totalMonths += ($interval->y * 12) + $interval->m;
}
$totalYears = floor($totalMonths / 12);
$remainingMonths = $totalMonths % 12;

// Highest Education Degree
$degreeStmt = $conn->prepare("
    SELECT e.degree_title, el.level_name 
    FROM education e
    LEFT JOIN education_levels el ON e.level_id = el.id
    WHERE e.job_seeker_id = ?
    ORDER BY el.id DESC LIMIT 1
");
$degreeStmt->execute([$job_seeker_id]);
$highestDegree = $degreeStmt->fetch(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/job_seeker_info.css">

<div class="profile-page">
  <!-- LEFT COLUMN -->
  <div class="profile-left">

    <div class="profile-header">
      <div class="profile-user">
        <img src="../uploads/profile/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Picture">
        <div class="profile-user-info">
          <h2><?= htmlspecialchars($user['fullname']) ?></h2>
          <p><?= htmlspecialchars($user['job_title'] ?? 'Job Seeker') ?></p>
        </div>
      </div>
    </div>

    <div class="profile-section">
      <h3>BIOGRAPHY</h3>
      <p><?= nl2br(htmlspecialchars($user['biography'] ?? 'Not provided.')) ?></p>
    </div>

    <div class="profile-section">
      <h3>COVER LETTER</h3>
      <p><?= nl2br(htmlspecialchars($user['cover_letter'] ?? 'Not provided.')) ?></p>
    </div>

    <div class="social-media-section">
      <h4>Follow me Social Media</h4>
      <div class="social-icons">
        <?php if (!empty($user['facebook_link'])): ?><a href="<?= htmlspecialchars($user['facebook_link']) ?>"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
        <?php if (!empty($user['twitter_link'])): ?><a href="<?= htmlspecialchars($user['twitter_link']) ?>"><i class="fab fa-twitter"></i></a><?php endif; ?>
        <?php if (!empty($user['linkedin_link'])): ?><a href="<?= htmlspecialchars($user['linkedin_link']) ?>"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
        <?php if (!empty($user['reddit_link'])): ?><a href="<?= htmlspecialchars($user['reddit_link']) ?>"><i class="fab fa-reddit-alien"></i></a><?php endif; ?>
        <?php if (!empty($user['instagram_link'])): ?><a href="<?= htmlspecialchars($user['instagram_link']) ?>"><i class="fab fa-instagram"></i></a><?php endif; ?>
        <?php if (!empty($user['youtube_link'])): ?><a href="<?= htmlspecialchars($user['youtube_link']) ?>"><i class="fab fa-youtube"></i></a><?php endif; ?>
      </div>
    </div>

    <div class="education-section">
      <h3>EDUCATION</h3>
      <?php foreach ($educations as $edu): ?>
        <div class="education-item">
          <h4><?= htmlspecialchars($edu['degree_title']) ?></h4>
          <p><?= htmlspecialchars($edu['institution_name']) ?> | <?= $edu['start_year'] ?> - <?= $edu['end_year'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="certifications-section">
      <h3>CERTIFICATIONS</h3>
      <ul>
        <?php foreach ($certifications as $cert): ?>
          <li><i class="fas fa-certificate"></i> <?= htmlspecialchars($cert['certification_name']) ?> - <?= date('Y', strtotime($cert['issue_date'])) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="projects-section">
      <h3>MY PROJECTS</h3>
      <div class="projects-grid">
        <?php foreach ($projects as $proj): ?>
          <div class="project-card">
            <h4><?= htmlspecialchars($proj['title']) ?></h4>
            <p><?= htmlspecialchars($proj['description']) ?></p>
            <?php if (!empty($proj['project_link'])): ?>
              <a href="<?= htmlspecialchars($proj['project_link']) ?>" target="_blank">View Project</a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- RIGHT COLUMN -->
  <div class="profile-right">
    <div class="profile-info-card">
      <div class="profile-info-grid">
        <div class="info-item"><i class="fas fa-birthday-cake"></i><div><span class="label">Date of Birth</span><p><?= htmlspecialchars(date('d M, Y', strtotime($user['date_of_birth']))) ?></p></div></div>
        <div class="info-item"><i class="fas fa-passport"></i><div><span class="label">Nationality</span><p>Bangladesh</p></div></div>
        <div class="info-item"><i class="fas fa-heart"></i><div><span class="label">Marital Status</span><p><?= htmlspecialchars($user['marital_status'] ?? 'Single') ?></p></div></div>
        <div class="info-item"><i class="fas fa-mars"></i><div><span class="label">Gender</span><p><?= htmlspecialchars($user['gender']) ?></p></div></div>
        <div class="info-item"><i class="fas fa-layer-group"></i><div><span class="label">Experience</span><p><?= $totalYears ?> Years</p></div></div>
        <div class="info-item"><i class="fas fa-graduation-cap"></i><div><span class="label">Educations</span><p><?= htmlspecialchars($highestDegree['level_name'] ?? 'Unknown') ?> - <?= htmlspecialchars($highestDegree['degree_title'] ?? 'N/A') ?></p></div></div>
      </div>
    </div>

    <div class="resume-download-card">
      <h3>Download Resume</h3>
      <div class="resume-content">
        <i class="fas fa-file-alt"></i>
        <div class="resume-details">
          <p><?= htmlspecialchars($user['fullname']) ?></p><span>PDF</span>
        </div>
        <?php if (!empty($user['resume_file'])): ?>
          <a href="../uploads/resume/<?= htmlspecialchars($user['resume_file']) ?>" class="download-btn" download><i class="fas fa-download"></i></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="contact-card">
      <h3>Contact Information</h3>
      <div class="contact-item"><i class="fas fa-globe"></i><div class="contact-text"><span class="label">WEBSITE</span><p><?= htmlspecialchars($user['website'] ?? '-') ?></p></div></div>
      <div class="contact-item"><i class="fas fa-map-marker-alt"></i><div class="contact-text"><span class="label">LOCATION</span><p><?= htmlspecialchars($user['address'] ?? 'N/A') ?></p></div></div>
      <div class="contact-item"><i class="fas fa-phone"></i><div class="contact-text"><span class="label">PHONE</span><p><?= htmlspecialchars($user['mobile']) ?></p><?php if (!empty($user['secondary_phone'])): ?><span class="label">SECONDARY PHONE</span><p><?= htmlspecialchars($user['secondary_phone']) ?></p><?php endif; ?></div></div>
      <div class="contact-item"><i class="fas fa-envelope"></i><div class="contact-text"><span class="label">EMAIL ADDRESS</span><p><?= htmlspecialchars($user['email']) ?></p></div></div>
    </div>

    <div class="skills-languages-card">
      <h3>Skills & Languages</h3>
      <div class="skills-section">
        <h4>Skills</h4>
        <?php foreach ($skills as $skill): ?>
          <div class="skill-item">
            <div class="skill-header">
              <span><?= htmlspecialchars($skill['name']) ?></span>
              <span><?= $skill['proficiency'] ?></span>
            </div>
            <div class="skill-bar"><div class="skill-fill" style="width:
              <?= match ($skill['proficiency']) {
                  'Beginner' => '60%',
                  'Intermediate' => '75%',
                  'Advanced' => '90%',
                  default => '70%',
              } ?>;"></div></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="languages-section">
        <h4>Languages</h4>
        <ul>
          <?php foreach ($languages as $lang): ?>
            <li><i class="fas fa-language"></i> <?= htmlspecialchars($lang['language_name']) ?> - <?= htmlspecialchars($lang['proficiency']) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
