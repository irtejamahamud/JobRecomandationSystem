<?php
session_start();
include('../includes/db.php');

// Auth guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'job_seeker') {
  header('Location: ../login.php');
  exit();
}

$job_seeker_id = $_SESSION['user_id'];

// Fetch core profile
$stmt = $conn->prepare("
  SELECT js.*, jsp.*
  FROM job_seekers js
  LEFT JOIN job_seeker_profiles jsp ON js.job_seeker_id = jsp.job_seeker_id
  WHERE js.job_seeker_id = ?
");
$stmt->execute([$job_seeker_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallbacks
$fullName   = htmlspecialchars($user['fullname'] ?? 'N/A');
$email      = htmlspecialchars($user['email'] ?? '');
$mobile     = htmlspecialchars($user['mobile'] ?? '');
$address    = htmlspecialchars($user['address'] ?? '');
$website    = htmlspecialchars($user['website'] ?? '');
$dob        = !empty($user['date_of_birth']) ? date('d M, Y', strtotime($user['date_of_birth'])) : '';
$gender     = htmlspecialchars($user['gender'] ?? '');
$marital    = htmlspecialchars($user['marital_status'] ?? '');
$bio        = htmlspecialchars($user['biography'] ?? '');
$cover      = htmlspecialchars($user['cover_letter'] ?? '');
$profileImg = !empty($user['profile_image'])
  ? '../uploads/profile/' . htmlspecialchars($user['profile_image'])
  : 'https://cdn-icons-png.flaticon.com/512/1144/1144760.png';

// Fetch education with level names (latest first)
$eduStmt = $conn->prepare("
  SELECT e.*, el.level_name
  FROM education e
  LEFT JOIN education_levels el ON e.level_id = el.id
  WHERE e.job_seeker_id = ?
  ORDER BY e.end_year DESC, e.start_year DESC
");
$eduStmt->execute([$job_seeker_id]);
$educations = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch experiences (latest first)
$expStmt = $conn->prepare("
  SELECT company_name, job_title, start_date, end_date, responsibilities
  FROM experience
  WHERE job_seeker_id = ?
  ORDER BY COALESCE(end_date, CURDATE()) DESC, start_date DESC
");
$expStmt->execute([$job_seeker_id]);
$experiences = $expStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects
$projStmt = $conn->prepare("
  SELECT title, description, project_link
  FROM projects
  WHERE job_seeker_id = ?
  ORDER BY id DESC
");
$projStmt->execute([$job_seeker_id]);
$projects = $projStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch skills
$skillStmt = $conn->prepare("
  SELECT sm.name, jss.proficiency
  FROM job_seeker_skills jss
  JOIN skill_master sm ON jss.skill_id = sm.id
  WHERE jss.job_seeker_id = ?
  ORDER BY sm.name ASC
");
$skillStmt->execute([$job_seeker_id]);
$skills = $skillStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch languages
$langStmt = $conn->prepare("
  SELECT language_name, proficiency
  FROM languages
  WHERE job_seeker_id = ?
  ORDER BY language_name ASC
");
$langStmt->execute([$job_seeker_id]);
$languages = $langStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch certifications
$certStmt = $conn->prepare("
  SELECT certification_name, issuing_organization, issue_date, certificate_url
  FROM certifications
  WHERE job_seeker_id = ?
  ORDER BY issue_date DESC
");
$certStmt->execute([$job_seeker_id]);
$certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);

// Latest resume (optional link)
$resumeStmt = $conn->prepare("
  SELECT file_name FROM resumes
  WHERE job_seeker_id = ?
  ORDER BY uploaded_at DESC
  LIMIT 1
");
$resumeStmt->execute([$job_seeker_id]);
$latestResume = $resumeStmt->fetchColumn();
?>
<?php include('../includes/header_jobseeker.php'); ?>

<!-- Inline styles and print-ready layout -->
<style>
  /* Hide site chrome on print */
  @media print {
    .site-header, .no-print { display: none !important; }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .cv-container { box-shadow: none; margin: 0; }
  }

  body { background: #f6f7fb; font-family: 'Poppins', Arial, sans-serif; }
  .cv-toolbar.no-print {
    position: sticky; top: 0; z-index: 1000s; background: #fff; border-bottom: 1px solid #eee;
    padding: 10px 16px; display: flex; gap: 10px; justify-content: flex-end;
  }
  .cv-toolbar .btn {
    background: #0b7d3e; color: #fff; border: 0; padding: 10px 14px; border-radius: 8px; cursor: pointer;
  }
  .cv-toolbar .btn.secondary { background: #3843d0; }
  .cv-container {
    max-width: 900px; margin: 24px auto; background: #fff; border-radius: 14px;
    box-shadow: 0 6px 22px rgba(31,40,105,0.08); overflow: hidden;
  }
  .cv-header {
    display: grid; grid-template-columns: 120px 1fr; gap: 18px; padding: 24px; background: #f3f6ff;
    border-bottom: 1px solid #eef1ff;
  }
  .cv-avatar {
    width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  }
  .cv-title { margin: 0; font-size: 28px; color: #1f2869; }
  .cv-subtitle { margin: 6px 0 10px; color: #667; font-weight: 500; }
  .cv-contacts { display: flex; flex-wrap: wrap; gap: 10px 18px; color: #444; font-size: 13px; }
  .cv-contacts span { display: inline-flex; align-items: center; gap: 8px; }
  .cv-contacts i { color: #3843d0; }

  .cv-body { padding: 20px 24px 28px; }

  .cv-section { margin-top: 18px; }
  .cv-section h3 {
    margin: 0 0 12px; font-size: 16px; color: #3843d0; letter-spacing: 0.4px; text-transform: uppercase;
    display: inline-block; position: relative; padding-bottom: 6px;
  }
  .cv-section h3:after {
    content: ""; position: absolute; left: 0; bottom: 0; width: 46px; height: 3px; background: #0b7d3e; border-radius: 2px;
  }
  .cv-text { color: #333; line-height: 1.7; }

  /* Lists */
  .timeline { display: grid; gap: 14px; }
  .timeline-item { padding: 12px 14px; border: 1px solid #eef1ff; border-radius: 12px; background: #fff; }
  .timeline-item h4 { margin: 0 0 6px; color: #1f2869; font-size: 16px; }
  .timeline-item .muted { color: #667; font-size: 13px; }
  .timeline-item .range { color: #0b7d3e; font-weight: 600; font-size: 13px; }

  .grid-2 { display: grid; gap: 16px; grid-template-columns: 1fr 1fr; }
  .skill-badges, .lang-badges { display: flex; flex-wrap: wrap; gap: 10px; }
  .badge {
    font-size: 12px; padding: 6px 10px; border-radius: 999px; background: #f3f6ff; color: #1f2869; border: 1px solid #e1e7ff;
  }
  .proj-link { color: #3843d0; text-decoration: none; }
  .proj-link:hover { text-decoration: underline; }
  .resume-link { display: inline-flex; align-items: center; gap: 8px; color: #0b7d3e; text-decoration: none; font-weight: 600; }
</style>

<div class="cv-toolbar no-print">
  <button class="btn" onclick="window.print()"><i class="fas fa-file-pdf"></i> Download PDF</button>
  <a href="job_seeker_profile.php" class="btn secondary" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
    <i class="fas fa-user"></i> View Profile
  </a>
</div>

<div class="cv-container">
  <div class="cv-header">
    <img class="cv-avatar" src="<?= $profileImg ?>" alt="Profile">
    <div>
      <h1 class="cv-title"><?= $fullName ?></h1>
      <div class="cv-subtitle"><?= htmlspecialchars($user['job_title'] ?? 'Job Seeker') ?></div>
      <div class="cv-contacts">
        <?php if ($email): ?><span><i class="fas fa-envelope"></i><?= $email ?></span><?php endif; ?>
        <?php if ($mobile): ?><span><i class="fas fa-phone"></i><?= $mobile ?></span><?php endif; ?>
        <?php if ($website): ?><span><i class="fas fa-globe"></i><?= $website ?></span><?php endif; ?>
        <?php if ($address): ?><span><i class="fas fa-map-marker-alt"></i><?= $address ?></span><?php endif; ?>
        <?php if ($dob): ?><span><i class="fas fa-birthday-cake"></i><?= $dob ?></span><?php endif; ?>
        <?php if ($gender): ?><span><i class="fas fa-venus-mars"></i><?= $gender ?></span><?php endif; ?>
        <?php if ($marital): ?><span><i class="fas fa-heart"></i><?= $marital ?></span><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="cv-body">
    <?php if ($bio): ?>
      <div class="cv-section">
        <h3>Profile Summary</h3>
        <div class="cv-text"><?= nl2br($bio) ?></div>
      </div>
    <?php endif; ?>

    <?php if (!empty($experiences)): ?>
      <div class="cv-section">
        <h3>Experience</h3>
        <div class="timeline">
          <?php foreach ($experiences as $exp): ?>
            <?php
              $company = htmlspecialchars($exp['company_name'] ?? '');
              $title   = htmlspecialchars($exp['job_title'] ?? '');
              $start   = !empty($exp['start_date']) ? date('M Y', strtotime($exp['start_date'])) : '';
              $end     = !empty($exp['end_date']) ? date('M Y', strtotime($exp['end_date'])) : 'Present';
              $resp    = htmlspecialchars($exp['responsibilities'] ?? '');
            ?>
            <div class="timeline-item">
              <h4><?= $title ?><?= $company ? " • $company" : "" ?></h4>
              <div class="range"><?= $start ?> - <?= $end ?></div>
              <?php if ($resp): ?><div class="muted" style="margin-top:8px; white-space:pre-line;"><?= $resp ?></div><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="grid-2">
      <?php if (!empty($educations)): ?>
        <div class="cv-section">
          <h3>Education</h3>
          <div class="timeline">
            <?php foreach ($educations as $edu): ?>
              <?php
                $lvl = htmlspecialchars($edu['level_name'] ?? '');
                $deg = htmlspecialchars($edu['degree_title'] ?? '');
                $ins = htmlspecialchars($edu['institution_name'] ?? '');
                $sy  = htmlspecialchars($edu['start_year'] ?? '');
                $ey  = htmlspecialchars($edu['end_year'] ?? '');
              ?>
              <div class="timeline-item">
                <h4><?= $deg ?><?= $lvl ? " • $lvl" : '' ?></h4>
                <div class="muted"><?= $ins ?></div>
                <div class="range"><?= $sy ?> - <?= $ey ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($skills)): ?>
        <div class="cv-section">
          <h3>Skills</h3>
          <div class="skill-badges">
            <?php foreach ($skills as $sk): ?>
              <span class="badge"><?= htmlspecialchars($sk['name']) ?> — <?= htmlspecialchars($sk['proficiency']) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($projects)): ?>
      <div class="cv-section">
        <h3>Projects</h3>
        <div class="timeline">
          <?php foreach ($projects as $p): ?>
            <div class="timeline-item">
              <h4><?= htmlspecialchars($p['title'] ?? '') ?></h4>
              <?php if (!empty($p['project_link'])): ?>
                <div class="muted" style="margin:6px 0;">
                  <a class="proj-link" href="<?= htmlspecialchars($p['project_link']) ?>" target="_blank">
                    <i class="fas fa-link"></i> <?= htmlspecialchars($p['project_link']) ?>
                  </a>
                </div>
              <?php endif; ?>
              <?php if (!empty($p['description'])): ?>
                <div class="muted" style="white-space:pre-line;"><?= htmlspecialchars($p['description']) ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($certifications)): ?>
      <div class="cv-section">
        <h3>Certifications</h3>
        <div class="timeline">
          <?php foreach ($certifications as $c): ?>
            <?php
              $cname = htmlspecialchars($c['certification_name'] ?? '');
              $org   = htmlspecialchars($c['issuing_organization'] ?? '');
              $yr    = !empty($c['issue_date']) ? date('Y', strtotime($c['issue_date'])) : '';
              $curl  = htmlspecialchars($c['certificate_url'] ?? '');
            ?>
            <div class="timeline-item">
              <h4><?= $cname ?><?= $org ? " • $org" : '' ?></h4>
              <?php if ($yr): ?><div class="range"><?= $yr ?></div><?php endif; ?>
              <?php if ($curl): ?>
                <div class="muted" style="margin-top:6px;">
                  <a class="proj-link" href="<?= $curl ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Certificate</a>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($languages)): ?>
      <div class="cv-section">
        <h3>Languages</h3>
        <div class="lang-badges">
          <?php foreach ($languages as $lang): ?>
            <span class="badge"><?= htmlspecialchars($lang['language_name']) ?> — <?= htmlspecialchars($lang['proficiency']) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($latestResume)): ?>
      <div class="cv-section">
        <a class="resume-link" href="../uploads/resumes/<?= htmlspecialchars($latestResume) ?>" target="_blank">
          <i class="fas fa-file-pdf"></i> View Latest Uploaded Resume
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
