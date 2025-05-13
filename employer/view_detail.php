<?php
session_start();
include("../includes/db.php");
include("../includes/header_recruiter.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['job_id'])) {
    echo "Job not found.";
    exit;
}

$job_id = intval($_GET['job_id']);

// Fetch job basic info
$stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
$stmt->execute([':job_id' => $job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch job extra details
$stmt2 = $conn->prepare("SELECT * FROM job_details WHERE job_id = :job_id");
$stmt2->execute([':job_id' => $job_id]);
$job_details = $stmt2->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo "Job not found.";
    exit;
}
?>

<link rel="stylesheet" href="../assets/css/view_detail.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="job-detail-wrapper">
  <h2><i class="fas fa-briefcase"></i> <?= htmlspecialchars($job['job_title']) ?></h2>
  <p class="subtitle"><i class="fas fa-user-tag"></i> <?= htmlspecialchars($job['job_role']) ?> &nbsp; <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></p>

  <div class="detail-section">
    <h3><i class="fas fa-info-circle"></i> Job Description</h3>
    <p><?= nl2br(htmlspecialchars($job['job_description'])) ?></p>
  </div>

  <div class="detail-section">
    <h3><i class="fas fa-tasks"></i> Key Responsibilities</h3>
    <ul>
      <?php foreach (explode("\n", $job_details['responsibilities'] ?? '') as $resp): ?>
        <?php if (!empty(trim($resp))): ?>
          <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars(trim($resp)) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="detail-section">
    <h3><i class="fas fa-tools"></i> Professional Skills</h3>
    <ul>
      <?php foreach (explode("\n", $job_details['professional_skills'] ?? '') as $skill): ?>
        <?php if (!empty(trim($skill))): ?>
          <li><i class="fas fa-star"></i> <?= htmlspecialchars(trim($skill)) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="detail-overview">
    <h3><i class="fas fa-clipboard-list"></i> Job Overview</h3>
    <ul>
      <li><i class="fas fa-briefcase"></i> <strong>Job Title:</strong> <?= htmlspecialchars($job['job_title']) ?></li>
      <li><i class="fas fa-clock"></i> <strong>Job Type:</strong> Full Time</li>
      <li><i class="fas fa-th-large"></i> <strong>Category:</strong> <?= htmlspecialchars($job['job_role']) ?></li>
      <li><i class="fas fa-user-clock"></i> <strong>Experience:</strong> <?= htmlspecialchars($job_details['experience_required'] ?? "Not mentioned") ?></li>
      <li><i class="fas fa-graduation-cap"></i> <strong>Degree:</strong> <?= htmlspecialchars($job_details['degree_required'] ?? "Not mentioned") ?></li>
      <li><i class="fas fa-dollar-sign"></i> <strong>Offered Salary:</strong> <?= htmlspecialchars($job['currency']) ?> <?= htmlspecialchars($job['min_salary']) ?> - <?= htmlspecialchars($job['max_salary']) ?></li>
      <li><i class="fas fa-map"></i> <strong>Location:</strong> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></li>
      <li><i class="fas fa-calendar-plus"></i> <strong>Start Date:</strong> <?= htmlspecialchars($job['start_date']) ?></li>
      <li><i class="fas fa-calendar-times"></i> <strong>Expire Date:</strong> <?= htmlspecialchars($job['expire_date']) ?></li>
    </ul>

    <?php if (!empty($job_details['map_embed_url'])): ?>
      <div class="map-container">
        <?= $job_details['map_embed_url'] ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include("../includes/footer_recruiter.php"); ?>
