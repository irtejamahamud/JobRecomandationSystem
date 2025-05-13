<?php
session_start();
include("../includes/db.php");

$job_seeker_id = $_SESSION['user_id'] ?? null;
$success = "";

// Handle POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $job_seeker_id) {
    // Delete old experiences
    $conn->prepare("DELETE FROM experience WHERE job_seeker_id = ?")->execute([$job_seeker_id]);

    // Insert new experiences
    if (!empty($_POST['company_name'])) {
        $count = count($_POST['company_name']);
        for ($i = 0; $i < $count; $i++) {
            $company = trim($_POST['company_name'][$i]);
            $title = trim($_POST['job_title'][$i]);
            $start = $_POST['start_date'][$i] ?? null;
            $end = $_POST['end_date'][$i] ?? null;
            $res = trim($_POST['responsibilities'][$i]);

            if ($company && $title) {
                $stmt = $conn->prepare("INSERT INTO experience (job_seeker_id, company_name, job_title, start_date, end_date, responsibilities)
                                        VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$job_seeker_id, $company, $title, $start, $end, $res]);
            }
        }
    }

    // Delete old projects
    $conn->prepare("DELETE FROM projects WHERE job_seeker_id = ?")->execute([$job_seeker_id]);

    // Insert new projects
    if (!empty($_POST['project_title'])) {
        $project_count = count($_POST['project_title']);
        for ($i = 0; $i < $project_count; $i++) {
            $title = trim($_POST['project_title'][$i]);
            $desc = trim($_POST['project_description'][$i]);
            $link = trim($_POST['project_link'][$i]);

            if ($title) { // Only insert if title is provided
                $stmt = $conn->prepare("INSERT INTO projects (job_seeker_id, title, description, project_link)
                                        VALUES (?, ?, ?, ?)");
                $stmt->execute([$job_seeker_id, $title, $desc, $link]);
            }
        }
    }

    // Redirect after saving
    header("Location: step4_skills_languages.php");
    exit;
}

// Fetch existing experiences
$experiences = [];
if ($job_seeker_id) {
    $stmt = $conn->prepare("SELECT * FROM experience WHERE job_seeker_id = ?");
    $stmt->execute([$job_seeker_id]);
    $experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($experiences)) $experiences[] = []; // At least one blank experience
}

// Fetch existing projects
$projects = [];
if ($job_seeker_id) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE job_seeker_id = ?");
    $stmt->execute([$job_seeker_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($projects)) $projects[] = []; // At least one blank project
}
?>

<?php include("../includes/header_jobseeker.php"); ?>
<link rel="stylesheet" href="../assets/css/jobseeker_profile.css">

<div class="profile-wrapper">

<div class="profile-tab-nav">
    <a href="step1_personal.php" class="profile-tab"><i class="fas fa-user-circle"></i> Personal Info</a>
    <a href="step2_education.php" class="profile-tab"><i class="fas fa-graduation-cap"></i> Education</a>
    <a href="step3_experience.php" class="profile-tab active"><i class="fas fa-briefcase"></i> Experience</a>
    <a href="step4_skills_languages.php" class="profile-tab"><i class="fas fa-cogs"></i> Skills & Languages</a>
    <a href="step5_resume_upload.php" class="profile-tab"><i class="fas fa-file-alt"></i> Resume</a>
    <a href="step6_social_links.php" class="profile-tab"><i class="fas fa-share-alt"></i> Social Links</a>
</div>

<h2>Work Experience</h2>

<form method="POST" class="profile-form" id="experience-form">

  <div id="experience-container">
    <?php foreach ($experiences as $exp): ?>
    <div class="experience-group">
      <div class="row-2col">
        <div class="form-group">
          <label>Company Name</label>
          <input type="text" name="company_name[]" value="<?= htmlspecialchars($exp['company_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Job Title</label>
          <input type="text" name="job_title[]" value="<?= htmlspecialchars($exp['job_title'] ?? '') ?>" required>
        </div>
      </div>

      <div class="row-2col">
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date[]" value="<?= htmlspecialchars($exp['start_date'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date[]" value="<?= htmlspecialchars($exp['end_date'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label>Responsibilities</label>
        <textarea name="responsibilities[]"><?= htmlspecialchars($exp['responsibilities'] ?? '') ?></textarea>
      </div>

      <hr>
    </div>
    <?php endforeach; ?>
  </div>

  <button type="button" class="btn-submit" onclick="addExperience()">+ Add More Experience</button>

  <h2>Projects <small style="font-weight:normal;">(Optional)</small></h2>

  <div id="project-container">
    <?php foreach ($projects as $proj): ?>
    <div class="project-group">
      <div class="form-group">
        <label>Project Title</label>
        <input type="text" name="project_title[]" value="<?= htmlspecialchars($proj['title'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Project Description</label>
        <textarea name="project_description[]"><?= htmlspecialchars($proj['description'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Project Link (optional)</label>
        <input type="url" name="project_link[]" value="<?= htmlspecialchars($proj['project_link'] ?? '') ?>">
      </div>
      <hr>
    </div>
    <?php endforeach; ?>
  </div>

  <button type="button" class="btn-submit" onclick="addProject()">+ Add More Project</button>

  <button type="submit" class="btn-submit">Save & Next</button>

</form>

</div>

<script>
// Add More Experience
function addExperience() {
    const container = document.getElementById("experience-container");
    const newGroup = document.createElement("div");
    newGroup.className = "experience-group";
    newGroup.innerHTML = `
      <div class="row-2col">
        <div class="form-group">
          <label>Company Name</label>
          <input type="text" name="company_name[]" required>
        </div>
        <div class="form-group">
          <label>Job Title</label>
          <input type="text" name="job_title[]" required>
        </div>
      </div>

      <div class="row-2col">
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date[]">
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date[]">
        </div>
      </div>

      <div class="form-group">
        <label>Responsibilities</label>
        <textarea name="responsibilities[]"></textarea>
      </div>
      <hr>
    `;
    container.appendChild(newGroup);
}

// Add More Project
function addProject() {
    const container = document.getElementById("project-container");
    const newGroup = document.createElement("div");
    newGroup.className = "project-group";
    newGroup.innerHTML = `
      <div class="form-group">
        <label>Project Title</label>
        <input type="text" name="project_title[]">
      </div>
      <div class="form-group">
        <label>Project Description</label>
        <textarea name="project_description[]"></textarea>
      </div>
      <div class="form-group">
        <label>Project Link (optional)</label>
        <input type="url" name="project_link[]">
      </div>
      <hr>
    `;
    container.appendChild(newGroup);
}
</script>

<?php include("../includes/footer_jobseeker.php"); ?>
