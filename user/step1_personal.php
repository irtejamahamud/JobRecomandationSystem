<?php
session_start();
include("../includes/db.php");

$job_seeker_id = $_SESSION['user_id'] ?? null;
$success = $error = "";
$progress = 20; // We'll calculate progress later dynamically

// Fetch existing data
$data = [];
$profile = [];
if ($job_seeker_id) {
    $stmt = $conn->prepare("SELECT * FROM job_seekers WHERE job_seeker_id = ?");
    $stmt->execute([$job_seeker_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtProfile = $conn->prepare("SELECT * FROM job_seeker_profiles WHERE job_seeker_id = ?");
    $stmtProfile->execute([$job_seeker_id]);
    $profile = $stmtProfile->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $job_seeker_id) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];
    $secondary_phone = $_POST['secondary_phone'];
    $website = $_POST['website'];
    $marital_status = $_POST['marital_status'];
    $biography = $_POST['biography'];
    $cover_letter = $_POST['cover_letter'];

    $upload_dir = "../uploads/profile/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $profile_image = $data['profile_image'] ?? '';

    if (!empty($_FILES['profile_image']['name'])) {
        $new_image_name = time() . "_" . basename($_FILES['profile_image']['name']);
        $image_path = $upload_dir . $new_image_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
            $profile_image = $new_image_name;
        }
    }

    // Update job_seekers table
    $updateUser = $conn->prepare("UPDATE job_seekers SET fullname=?, email=?, date_of_birth=?, gender=?, address=?, profile_image=?, mobile=? WHERE job_seeker_id=?");
    $updateUser->execute([$fullname, $email, $dob, $gender, $address, $profile_image, $mobile, $job_seeker_id]);

    // Update job_seeker_profiles table
    $checkProfile = $conn->prepare("SELECT id FROM job_seeker_profiles WHERE job_seeker_id=?");
    $checkProfile->execute([$job_seeker_id]);

    if ($checkProfile->rowCount() > 0) {
        $updateProfile = $conn->prepare("UPDATE job_seeker_profiles SET biography=?, cover_letter=?, marital_status=?, secondary_phone=?, website=? WHERE job_seeker_id=?");
        $updateProfile->execute([$biography, $cover_letter, $marital_status, $secondary_phone, $website, $job_seeker_id]);
    } else {
        $insertProfile = $conn->prepare("INSERT INTO job_seeker_profiles (job_seeker_id, biography, cover_letter, marital_status, secondary_phone, website) VALUES (?,?,?,?,?,?)");
        $insertProfile->execute([$job_seeker_id, $biography, $cover_letter, $marital_status, $secondary_phone, $website]);
    }

    header("Location: step2_education.php");
    exit;
}
?>

<?php include("../includes/header_jobseeker.php"); ?>
<link rel="stylesheet" href="../assets/css/jobseeker_profile.css">

<div class="profile-wrapper">
  <div class="profile-tab-nav">
    <a href="step1_personal.php" class="profile-tab active"><i class="fas fa-user-circle"></i> Personal Info</a>
    <a href="step2_education.php" class="profile-tab"><i class="fas fa-graduation-cap"></i> Education</a>
    <a href="step3_experience.php" class="profile-tab"><i class="fas fa-briefcase"></i> Experience</a>
    <a href="step4_skills_languages.php" class="profile-tab"><i class="fas fa-cogs"></i> Skills & Languages</a>
    <a href="step5_resume_upload.php" class="profile-tab"><i class="fas fa-file-alt"></i> Resume</a>
    <a href="step6_social_links.php" class="profile-tab"><i class="fas fa-share-alt"></i> Social Links</a>
  </div>

  <h2>Personal Info</h2>

  <form action="" method="POST" enctype="multipart/form-data" class="profile-form">
    <div class="upload-section">
      <div class="upload-box">
        <label>Profile Image</label>
        <div class="file-drop">
          <?php if (!empty($data['profile_image'])): ?>
            <img src="../uploads/profile/<?= htmlspecialchars($data['profile_image']) ?>" alt="Profile" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 10px;">
          <?php endif; ?>
          <input type="file" name="profile_image">
          <p><strong>Browse photo</strong> or drop here<br><small>Min 400px · Max 5MB</small></p>
        </div>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($data['fullname'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Mobile Number</label>
        <input type="text" name="mobile" value="<?= htmlspecialchars($data['mobile'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Secondary Phone</label>
        <input type="text" name="secondary_phone" value="<?= htmlspecialchars($profile['secondary_phone'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= htmlspecialchars($data['date_of_birth'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="">Select</option>
          <option value="Male" <?= ($data['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= ($data['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
          <option value="Other" <?= ($data['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Website</label>
        <input type="text" name="website" value="<?= htmlspecialchars($profile['website'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Marital Status</label>
        <select name="marital_status">
          <option value="">Select</option>
          <option value="Single" <?= ($profile['marital_status'] ?? '') == 'Single' ? 'selected' : '' ?>>Single</option>
          <option value="Married" <?= ($profile['marital_status'] ?? '') == 'Married' ? 'selected' : '' ?>>Married</option>
        </select>
      </div>
    </div>

    <div class="form-group full-width">
      <label>Address</label>
      <textarea name="address"><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
    </div>

    <div class="form-group full-width">
      <label>Biography</label>
      <textarea name="biography" rows="4" placeholder="Write a short biography"><?= htmlspecialchars($profile['biography'] ?? '') ?></textarea>
    </div>

    <div class="form-group full-width">
      <label>Cover Letter</label>
      <textarea name="cover_letter" rows="4" placeholder="Write your cover letter"><?= htmlspecialchars($profile['cover_letter'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn-submit">Save & Next</button>
  </form>
</div>

<?php include("../includes/footer_jobseeker.php"); ?>
