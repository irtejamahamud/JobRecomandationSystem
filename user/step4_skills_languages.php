<?php
session_start();
include("../includes/db.php");

$job_seeker_id = $_SESSION['user_id'] ?? null;
$progress = 80;

// Fetch available skills
$skills_query = $conn->prepare("SELECT * FROM skill_master");
$skills_query->execute();
$all_skills = $skills_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing skills/languages if needed
$existing_skills = $conn->prepare("SELECT * FROM job_seeker_skills WHERE job_seeker_id = ?");
$existing_skills->execute([$job_seeker_id]);
$skills_data = $existing_skills->fetchAll(PDO::FETCH_ASSOC);

$existing_languages = $conn->prepare("SELECT * FROM languages WHERE job_seeker_id = ?");
$existing_languages->execute([$job_seeker_id]);
$languages_data = $existing_languages->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $job_seeker_id) {
    // Clear previous entries
    $conn->prepare("DELETE FROM job_seeker_skills WHERE job_seeker_id = ?")->execute([$job_seeker_id]);
    $conn->prepare("DELETE FROM languages WHERE job_seeker_id = ?")->execute([$job_seeker_id]);

    // Insert new skills
    if (!empty($_POST['skills'])) {
        foreach ($_POST['skills'] as $index => $skill_id) {
            $proficiency = $_POST['skill_proficiency'][$index];
            $stmt = $conn->prepare("INSERT INTO job_seeker_skills (job_seeker_id, skill_id, proficiency) VALUES (?, ?, ?)");
            $stmt->execute([$job_seeker_id, $skill_id, $proficiency]);
        }
    }

    // Insert new languages
    if (!empty($_POST['language_name'])) {
        foreach ($_POST['language_name'] as $index => $language_name) {
            $lang_prof = $_POST['language_proficiency'][$index];
            $stmt = $conn->prepare("INSERT INTO languages (job_seeker_id, language_name, proficiency) VALUES (?, ?, ?)");
            $stmt->execute([$job_seeker_id, $language_name, $lang_prof]);
        }
    }

    header("Location: step5_resume_upload.php");
    exit;
}
?>

<?php include("../includes/header_jobseeker.php"); ?>
<link rel="stylesheet" href="../assets/css/jobseeker_profile.css">

<div class="profile-wrapper">
<div class="profile-tab-nav">
    <a href="step1_personal.php" class="profile-tab"><i class="fas fa-user-circle"></i> Personal Info</a>
    <a href="step2_education.php" class="profile-tab"><i class="fas fa-graduation-cap"></i> Education</a>
    <a href="step3_experience.php" class="profile-tab"><i class="fas fa-briefcase"></i> Experience</a>
    <a href="step4_skills_languages.php" class="profile-tab active"><i class="fas fa-cogs"></i> Skills & Languages</a>
    <a href="step5_resume_upload.php" class="profile-tab"><i class="fas fa-file-alt"></i> Resume</a>
    <a href="step6_social_links.php" class="profile-tab"><i class="fas fa-share-alt"></i> Social Links</a>
  </div>



  <h2>Skills & Languages</h2>

  <form action="" method="POST" class="profile-form">

    <!-- Skills Section -->
    <div class="form-group">
      <label>Skills</label>
      <div id="skills-container">
        <?php if (!empty($skills_data)): ?>
          <?php foreach ($skills_data as $s): ?>
            <div class="row">
              <select name="skills[]" required>
                <option value="">Select Skill</option>
                <?php foreach ($all_skills as $skill): ?>
                  <option value="<?= $skill['id'] ?>" <?= $s['skill_id'] == $skill['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($skill['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <select name="skill_proficiency[]" required>
                <option value="Beginner" <?= $s['proficiency'] == 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                <option value="Intermediate" <?= $s['proficiency'] == 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                <option value="Advanced" <?= $s['proficiency'] == 'Advanced' ? 'selected' : '' ?>>Advanced</option>
              </select>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="row">
            <select name="skills[]" required>
              <option value="">Select Skill</option>
              <?php foreach ($all_skills as $skill): ?>
                <option value="<?= $skill['id'] ?>"><?= htmlspecialchars($skill['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <select name="skill_proficiency[]" required>
              <option value="Beginner">Beginner</option>
              <option value="Intermediate" selected>Intermediate</option>
              <option value="Advanced">Advanced</option>
            </select>
          </div>
        <?php endif; ?>
      </div>
      <button type="button" onclick="addSkill()">+ Add Skill</button>
    </div>

    <!-- Languages Section -->
    <div class="form-group">
      <label>Languages</label>
      <div id="languages-container">
        <?php if (!empty($languages_data)): ?>
          <?php foreach ($languages_data as $lang): ?>
            <div class="row">
              <input type="text" name="language_name[]" value="<?= htmlspecialchars($lang['language_name']) ?>" placeholder="Language" required>
              <select name="language_proficiency[]" required>
                <option value="Basic" <?= $lang['proficiency'] == 'Basic' ? 'selected' : '' ?>>Basic</option>
                <option value="Conversational" <?= $lang['proficiency'] == 'Conversational' ? 'selected' : '' ?>>Conversational</option>
                <option value="Fluent" <?= $lang['proficiency'] == 'Fluent' ? 'selected' : '' ?>>Fluent</option>
                <option value="Native" <?= $lang['proficiency'] == 'Native' ? 'selected' : '' ?>>Native</option>
              </select>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="row">
            <input type="text" name="language_name[]" placeholder="Language" required>
            <select name="language_proficiency[]" required>
              <option value="Basic">Basic</option>
              <option value="Conversational" selected>Conversational</option>
              <option value="Fluent">Fluent</option>
              <option value="Native">Native</option>
            </select>
          </div>
        <?php endif; ?>
      </div>
      <button type="button" onclick="addLanguage()">+ Add Language</button>
    </div>

    <button type="submit" class="btn-submit">Save & Next</button>
  </form>
</div>

<script>
function addSkill() {
    const container = document.getElementById("skills-container");
    const row = document.createElement("div");
    row.classList.add("row");
    row.innerHTML = `
      <select name="skills[]" required>
        <option value="">Select Skill</option>
        <?php foreach ($all_skills as $skill): ?>
          <option value="<?= $skill['id'] ?>"><?= htmlspecialchars($skill['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="skill_proficiency[]" required>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
      </select>
    `;
    container.appendChild(row);
}

function addLanguage() {
    const container = document.getElementById("languages-container");
    const row = document.createElement("div");
    row.classList.add("row");
    row.innerHTML = `
      <input type="text" name="language_name[]" placeholder="Language" required>
      <select name="language_proficiency[]" required>
        <option value="Basic">Basic</option>
        <option value="Conversational">Conversational</option>
        <option value="Fluent">Fluent</option>
        <option value="Native">Native</option>
      </select>
    `;
    container.appendChild(row);
}
</script>

<?php include("../includes/footer_jobseeker.php"); ?>
