<?php
session_start();
include("../includes/db.php");

// Validate session
$job_seeker_id = $_SESSION['user_id'] ?? null;
$success = $error = "";
$progress = 40;

if (!$job_seeker_id) {
    header("Location: ../login.php");
    exit();
}

// Fetch education levels
$levels = $conn->query("SELECT * FROM education_levels")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $job_seeker_id) {
    $level_ids = $_POST['level_id'];
    $institutions = $_POST['institution_name'];
    $degrees = $_POST['degree_title'];
    $starts = $_POST['start_year'];
    $ends = $_POST['end_year'];
    $grades = $_POST['grade'];

    // Delete old records first
    $conn->prepare("DELETE FROM education WHERE job_seeker_id = ?")->execute([$job_seeker_id]);

    // Insert multiple entries
    foreach ($level_ids as $index => $level_id) {
        if (!empty($level_id) && !empty($institutions[$index]) && !empty($degrees[$index])) {
            $stmt = $conn->prepare("INSERT INTO education (job_seeker_id, level_id, institution_name, degree_title, start_year, end_year, grade) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $job_seeker_id,
                $level_id,
                trim($institutions[$index]),
                trim($degrees[$index]),
                (int)$starts[$index],
                (int)$ends[$index],
                trim($grades[$index])
            ]);
        }
    }

    // Redirect after saving
    header("Location: step3_experience.php");
    exit;
}

// Fetch current education data (multiple)
$data = $conn->prepare("SELECT * FROM education WHERE job_seeker_id = ?");
$data->execute([$job_seeker_id]);
$educations = $data->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../includes/header_jobseeker.php"); ?>
<link rel="stylesheet" href="../assets/css/jobseeker_profile.css">

<div class="profile-wrapper">
<div class="profile-tab-nav">
    <a href="step1_personal.php" class="profile-tab"><i class="fas fa-user-circle"></i> Personal Info</a>
    <a href="step2_education.php" class="profile-tab active"><i class="fas fa-graduation-cap"></i> Education</a>
    <a href="step3_experience.php" class="profile-tab"><i class="fas fa-briefcase"></i> Experience</a>
    <a href="step4_skills_languages.php" class="profile-tab"><i class="fas fa-cogs"></i> Skills & Languages</a>
    <a href="step5_resume_upload.php" class="profile-tab"><i class="fas fa-file-alt"></i> Resume</a>
    <a href="step6_social_links.php" class="profile-tab"><i class="fas fa-share-alt"></i> Social Links</a>
  </div>

  <h2>Education Info</h2>

  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" class="profile-form" id="educationForm">
    <div id="educationFields">

      <?php if (!empty($educations)): ?>
        <?php foreach ($educations as $education): ?>
        <div class="education-block">
          <div class="row-2col">
            <div class="form-group">
              <label>Education Level</label>
              <select name="level_id[]" required>
                <option value="">Select level</option>
                <?php foreach ($levels as $level): ?>
                  <option value="<?= $level['id'] ?>" <?= ($education['level_id'] == $level['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($level['level_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Institution Name</label>
              <input type="text" name="institution_name[]" value="<?= htmlspecialchars($education['institution_name']) ?>" required>
            </div>
          </div>

          <div class="row-2col">
            <div class="form-group">
              <label>Degree Title</label>
              <input type="text" name="degree_title[]" value="<?= htmlspecialchars($education['degree_title']) ?>" required>
            </div>

            <div class="form-group">
              <label>Grade/CGPA</label>
              <input type="text" name="grade[]" value="<?= htmlspecialchars($education['grade']) ?>">
            </div>
          </div>

          <div class="row-2col">
            <div class="form-group">
              <label>Start Year</label>
              <input type="number" name="start_year[]" value="<?= htmlspecialchars($education['start_year']) ?>" required min="1950" max="<?= date('Y') ?>">
            </div>

            <div class="form-group">
              <label>End Year</label>
              <input type="number" name="end_year[]" value="<?= htmlspecialchars($education['end_year']) ?>" required min="1950" max="<?= date('Y') + 10 ?>">
            </div>
          </div>

          <hr class="divider">
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Empty Default Education Block if no data -->
        <div class="education-block">
          <div class="row-2col">
            <div class="form-group">
              <label>Education Level</label>
              <select name="level_id[]" required>
                <option value="">Select level</option>
                <?php foreach ($levels as $level): ?>
                  <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Institution Name</label>
              <input type="text" name="institution_name[]" required>
            </div>
          </div>

          <div class="row-2col">
            <div class="form-group">
              <label>Degree Title</label>
              <input type="text" name="degree_title[]" required>
            </div>

            <div class="form-group">
              <label>Grade/CGPA</label>
              <input type="text" name="grade[]">
            </div>
          </div>

          <div class="row-2col">
            <div class="form-group">
              <label>Start Year</label>
              <input type="number" name="start_year[]" required min="1950" max="<?= date('Y') ?>">
            </div>

            <div class="form-group">
              <label>End Year</label>
              <input type="number" name="end_year[]" required min="1950" max="<?= date('Y') + 10 ?>">
            </div>
          </div>

          <hr class="divider">
        </div>
      <?php endif; ?>

    </div>

    <button type="button" id="addMoreBtn" class="btn-add">+ Add Another Education</button>

    <button type="submit" class="btn-submit">Save & Next</button>
  </form>
</div>

<script>
document.getElementById('addMoreBtn').addEventListener('click', function() {
    const educationFields = document.getElementById('educationFields');
    const firstBlock = educationFields.querySelector('.education-block');
    const clone = firstBlock.cloneNode(true);

    // Clear the values
    clone.querySelectorAll('input, select').forEach(function(el) {
        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else {
            el.value = '';
        }
    });

    educationFields.appendChild(clone);
});
</script>

<?php include("../includes/footer_jobseeker.php"); ?>
