<?php
// generate_cv.php (dynamic CV templates)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');
// Auth guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'job_seeker') {
  header('Location: ../login.php');
  exit();
}
$job_seeker_id = $_SESSION['user_id'];

// Fetch core profile + extended profile
$stmt = $conn->prepare("SELECT js.*, jsp.* FROM job_seekers js LEFT JOIN job_seeker_profiles jsp ON js.job_seeker_id = jsp.job_seeker_id WHERE js.job_seeker_id = ?");
$stmt->execute([$job_seeker_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Sanitized display values
$fullName   = htmlspecialchars($user['fullname'] ?? '');
$email      = htmlspecialchars($user['email'] ?? '');
$mobile     = htmlspecialchars($user['mobile'] ?? '');
$address    = htmlspecialchars($user['address'] ?? '');
$website    = htmlspecialchars($user['website'] ?? '');
$dobRaw     = $user['date_of_birth'] ?? '';
$dobDisplay = $dobRaw ? date('d M, Y', strtotime($dobRaw)) : '';
$gender     = htmlspecialchars($user['gender'] ?? '');
$marital    = htmlspecialchars($user['marital_status'] ?? '');
$bio        = htmlspecialchars($user['biography'] ?? '');
$cover      = htmlspecialchars($user['cover_letter'] ?? '');
$profileImg = !empty($user['profile_image']) ? '../uploads/profile/' . htmlspecialchars($user['profile_image']) : 'https://cdn-icons-png.flaticon.com/512/1144/1144760.png';

// Education
$eduStmt = $conn->prepare("SELECT e.*, el.level_name FROM education e LEFT JOIN education_levels el ON e.level_id = el.id WHERE e.job_seeker_id = ? ORDER BY e.end_year DESC, e.start_year DESC");
$eduStmt->execute([$job_seeker_id]);
$educations = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

// Experience
$expStmt = $conn->prepare("SELECT company_name, job_title, start_date, end_date, responsibilities FROM experience WHERE job_seeker_id = ? ORDER BY COALESCE(end_date, CURDATE()) DESC, start_date DESC");
$expStmt->execute([$job_seeker_id]);
$experiences = $expStmt->fetchAll(PDO::FETCH_ASSOC);

// Projects
$projStmt = $conn->prepare("SELECT title, description, project_link FROM projects WHERE job_seeker_id = ? ORDER BY id DESC");
$projStmt->execute([$job_seeker_id]);
$projects = $projStmt->fetchAll(PDO::FETCH_ASSOC);

// Skills
$skillStmt = $conn->prepare("SELECT sm.name, jss.proficiency FROM job_seeker_skills jss JOIN skill_master sm ON jss.skill_id = sm.id WHERE jss.job_seeker_id = ? ORDER BY sm.name ASC");
$skillStmt->execute([$job_seeker_id]);
$skills = $skillStmt->fetchAll(PDO::FETCH_ASSOC);

// Languages
$langStmt = $conn->prepare("SELECT language_name, proficiency FROM languages WHERE job_seeker_id = ? ORDER BY language_name ASC");
$langStmt->execute([$job_seeker_id]);
$languages = $langStmt->fetchAll(PDO::FETCH_ASSOC);

// Certifications
$certStmt = $conn->prepare("SELECT certification_name, issuing_organization, issue_date, certificate_url FROM certifications WHERE job_seeker_id = ? ORDER BY issue_date DESC");
$certStmt->execute([$job_seeker_id]);
$certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);

// Latest resume
$resumeStmt = $conn->prepare("SELECT file_name FROM resumes WHERE job_seeker_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$resumeStmt->execute([$job_seeker_id]);
$latestResume = $resumeStmt->fetchColumn();

include('../includes/header_jobseeker.php'); ?>

<style>
  body {
    background: #f6f7fb;
    font-family: 'Poppins', Arial, sans-serif;
  }

  .cv-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 24px;
    max-width: 1300px;
    margin: 30px auto;
    padding: 0 20px;
  }

  /* Sidebar Styling */
  .cv-sidebar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 18px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 90px;
    height: fit-content;
    transition: all 0.2s ease;
  }

  .cv-sidebar:hover {
    box-shadow: 0 6px 22px rgba(0, 0, 0, 0.07);
  }

  .cv-sidebar h2 {
    margin: 0 0 18px;
    font-size: 18px;
    font-weight: 700;
    color: #1f2869;
    text-align: center;
    border-bottom: 2px solid #e5e9f7;
    padding-bottom: 8px;
  }

  /* Template grid */
  .tpl-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* Template cards */
  .tpl-card {
    background: #f9fbff;
    border: 1px solid #e3e8f9;
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 10px 8px 12px;
    position: relative;
  }

  .tpl-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 14px rgba(56, 67, 208, 0.12);
  }

  .tpl-card.active {
    border: 2px solid #3843d0;
    background: #eef3ff;
    box-shadow: 0 0 0 3px rgba(56, 67, 208, 0.1);
  }

  .tpl-preview {
    width: 100%;
    height: 100px;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #e5e9f7;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .tpl-name {
    margin-top: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #1f2869;
    text-align: center;
    letter-spacing: 0.3px;
  }

  .tpl-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #0b7d3e;
    color: #fff;
    font-size: 10px;
    padding: 3px 7px;
    border-radius: 999px;
    letter-spacing: 0.4px;
  }

  /* Main Section */
  .cv-main {
    min-height: 400px;
  }

  .cv-toolbar {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-bottom: 16px;
  }

  .cv-toolbar button,
  .cv-toolbar a {
    background: #3843d0;
    color: #fff;
    border: none;
    padding: 10px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: 0.2s;
  }

  .cv-toolbar button:hover,
  .cv-toolbar a:hover {
    background: #2e36b4;
  }

  .cv-toolbar button.secondary {
    background: #0b7d3e;
  }

  .cv-toolbar button.secondary:hover {
    background: #096433;
  }

  .cv-toolbar button.outline {
    background: #fff;
    color: #3843d0;
    border: 1px solid #3843d0;
  }

  @media print {
    .cv-sidebar,
    .cv-toolbar,
    .site-header,
    .site-footer {
      display: none !important;
    }
    body {
      background: #fff;
    }
  }
</style>


<div class="cv-layout">
  <aside class="cv-sidebar no-print">
    <h2>Templates</h2>
    <div class="tpl-grid" id="templateList"></div>
  </aside>
  <section class="cv-main">
    <div class="cv-toolbar no-print">
      <button id="downloadBtn"><i class="fas fa-file-pdf"></i> Download PDF</button>
      <button id="printBtn" class="outline"><i class="fas fa-print"></i> Print</button>
      <a href="job_seeker_profile.php" class="secondary"><i class="fas fa-user"></i> View Profile</a>
    </div>
    <div id="cvRender" class="cv-render-target"></div>
  </section>
</div>

<script>
  // Data hydration
  window.CV_DATA = <?php
    $payload = [
      'fullName' => $fullName,
      'jobTitle' => htmlspecialchars($user['job_title'] ?? 'Job Seeker'),
      'email' => $email,
      'mobile' => $mobile,
      'website' => $website,
      'address' => $address,
      'dob' => $dobRaw,
      'dobDisplay' => $dobDisplay,
      'gender' => $gender,
      'marital' => $marital,
      'bio' => $bio,
      'coverLetter' => $cover,
      'profileImg' => $profileImg,
      'experiences' => $experiences,
      'educations' => $educations,
      'projects' => $projects,
      'skills' => $skills,
      'languages' => $languages,
      'certifications' => $certifications,
      'latestResume' => $latestResume ? '../uploads/resumes/' . $latestResume : null,
    ];
    echo json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
  ?>;

  window.CVUtils = {
    esc: function(s){ if(s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); },
    nl2br: s => (s?String(s):'').replace(/\n/g,'<br>'),
    fmtMonthYear: d => { if(!d) return ''; try{ const dt=new Date(d); return dt.toLocaleDateString(undefined,{month:'short', year:'numeric'});}catch(e){return d;} },
    fmtDate: d => { if(!d) return ''; try{ const dt=new Date(d); return dt.toLocaleDateString(undefined,{day:'2-digit', month:'short', year:'numeric'});}catch(e){return d;} }
  };
</script>

<!-- Load template scripts -->
<script src="templates/template-classic.js"></script>
<script src="templates/template-compact.js"></script>
<script src="templates/template-elegant.js"></script>
<script src="templates/template-modern.js"></script>
<!-- Centralized CV export helper (loads html2pdf with multi-CDN + queue) -->
<script src="../assets/js/cv_export.js"></script>

<script>
  (function(){
    const listEl = document.getElementById('templateList');
    const renderEl = document.getElementById('cvRender');
    let active = 'modern';

    function renderTemplateButtons(){
      listEl.innerHTML = '';
      (window.TEMPLATES||[]).forEach(t => {
        const card = document.createElement('div');
        card.className = 'tpl-card' + (t.id === active ? ' active':'' );
        card.dataset.id = t.id;
        const previewHTML = (t.renderPreview ? t.renderPreview() : '');
        card.innerHTML = `<div class="tpl-preview">${previewHTML}</div><div class="tpl-name">${t.name}</div>` + (t.badge?`<div class="tpl-badge">${t.badge}</div>`:'');
        card.addEventListener('click', () => { active = t.id; renderTemplateButtons(); applyTemplate(); });
        listEl.appendChild(card);
      });
    }

    function applyTemplate(){
      const tpl = (window.TEMPLATES||[]).find(x => x.id === active) || (window.TEMPLATES||[])[0];
      if(!tpl){ renderEl.innerHTML = '<p style="padding:20px;">No templates loaded.</p>'; return; }
      try {
        renderEl.innerHTML = tpl.renderFull(window.CV_DATA);
      } catch(e){
        console.error(e);
        renderEl.innerHTML = '<p style="padding:20px;color:#c00;">Template render failed.</p>';
      }
    }

    // Wire up export & print using CVExport helper
    document.getElementById('downloadBtn').addEventListener('click', () => {
      if (!window.CVExport) {
        alert('Exporter still initializing. Please try again in a second.');
        return;
      }
      CVExport.exportPDF(renderEl, (window.CV_DATA.fullName || 'cv') + '-' + active + '.pdf');
    });
    document.getElementById('printBtn').addEventListener('click', () => window.print());

    renderTemplateButtons();
    applyTemplate();
  })();
</script>

<?php include('../includes/footer_jobseeker.php'); ?>
