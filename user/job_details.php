<?php
session_start();
include('../includes/db.php');
include('../includes/header_jobseeker.php');

// ✅ Check if logged in and role is job_seeker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../login.php");
    exit();
}

// ✅ Check if job_id is passed
if (!isset($_GET['job_id'])) {
    echo "Job not found!";
    exit();
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

// Fetch related jobs based on same job_role or tags
$relatedStmt = $conn->prepare("
    SELECT * FROM jobs 
    WHERE job_id != :job_id 
    AND (job_role = :job_role OR FIND_IN_SET(:tag, tags)) 
    ORDER BY RAND() 
    LIMIT 3
");
$relatedStmt->execute([
    ':job_id' => $job_id,
    ':job_role' => $job['job_role'],
    ':tag' => explode(',', $job['tags'])[0] ?? ''
]);
$relatedJobs = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);


if (!$job) {
    echo "Job not found!";
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/job_details.css">

<div class="job-details-wrapper">
<?php if (isset($_GET['applied_success'])): ?>
    <div class="alert-success">Successfully applied for the job!</div>
<?php elseif (isset($_GET['already_applied'])): ?>
    <div class="alert-error">You have already applied for this job.</div>
<?php endif; ?>
    <div class="job-main">
        <h2 class="job-title">Job Details</h2>
        <p class="job-subtitle">Search for your desired job matching your skills</p>

        <div class="job-head">
            <div class="job-info">
                <span class="time-badge">Posted on <?= date('M d, Y', strtotime($job['posted_on'])) ?></span>
                <h3><?= htmlspecialchars($job['job_title']) ?></h3>
                <p><?= htmlspecialchars($job['job_role']) ?></p>
                <div class="job-tags">
                    <span><?= htmlspecialchars($job['job_role']) ?></span>
                    <span><?= htmlspecialchars($job['job_level']) ?></span>
                    <span><?= htmlspecialchars($job['currency']) ?> <?= number_format($job['min_salary']) ?> - <?= number_format($job['max_salary']) ?></span>
                    <span><?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></span>
                </div>
            </div>
            <a href="apply_job.php?job_id=<?= $job['job_id'] ?>" class="apply-btn">Apply Job</a>

        </div>

        <div class="job-body">
            <div class="job-description">
                <h4>Job Description</h4>
                <p><?= nl2br(htmlspecialchars($job['job_description'])) ?></p>

                <h4>Key Responsibilities</h4>
                <ul>
                    <?php
                    if (!empty($job_details['responsibilities'])) {
                        $responsibilities = explode("\n", $job_details['responsibilities']);
                        foreach ($responsibilities as $item) {
                            echo "<li>✅ " . htmlspecialchars(trim($item)) . "</li>";
                        }
                    } else {
                        echo "<li>No responsibilities mentioned.</li>";
                    }
                    ?>
                </ul>

                <h4>Professional Skills</h4>
                <ul>
                    <?php
                    if (!empty($job_details['professional_skills'])) {
                        $skills = explode("\n", $job_details['professional_skills']);
                        foreach ($skills as $item) {
                            echo "<li>✅ " . htmlspecialchars(trim($item)) . "</li>";
                        }
                    } else {
                        echo "<li>No professional skills mentioned.</li>";
                    }
                    ?>
                </ul>

                <div class="tags-share">
                    <p>Tags:</p>
                    <?php
                    if (!empty($job['tags'])) {
                        $tags = explode(",", $job['tags']);
                        foreach ($tags as $tag) {
                            echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                        }
                    }
                    ?>

                    <div class="share">
                        <span>Share Job:</span>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <aside class="job-sidebar">
                <div class="overview">
                    <h4>Job Overview</h4>

                    <div class="overview-item"><i class="fas fa-user-tie"></i> <span><strong>Job Title</strong><br><?= htmlspecialchars($job['job_title']) ?></span></div>
                    <div class="overview-item"><i class="fas fa-clock"></i> <span><strong>Job Type</strong><br><?= htmlspecialchars($job['job_level']) ?></span></div>
                    <div class="overview-item"><i class="fas fa-briefcase"></i> <span><strong>Category</strong><br><?= htmlspecialchars($job['job_role']) ?></span></div>
                    <div class="overview-item"><i class="fas fa-award"></i> <span><strong>Experience</strong><br><?= htmlspecialchars($job_details['experience_required'] ?? 'Not mentioned') ?></span></div>
                    <div class="overview-item"><i class="fas fa-graduation-cap"></i> <span><strong>Degree</strong><br><?= htmlspecialchars($job_details['degree_required'] ?? 'Not mentioned') ?></span></div>
                    <div class="overview-item"><i class="fas fa-wallet"></i> <span><strong>Offered Salary</strong><br><?= htmlspecialchars($job['currency']) ?> <?= number_format($job['min_salary']) ?> - <?= number_format($job['max_salary']) ?></span></div>
                    <div class="overview-item"><i class="fas fa-map-marker-alt"></i> <span><strong>Location</strong><br><?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></span></div>

                    <?php if (!empty($job_details['map_embed_url'])): ?>
                        <iframe src="<?= htmlspecialchars($job_details['map_embed_url']) ?>" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>

                <div class="message-box">
                    <h4>Send Us Message</h4>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Full name">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Email Address">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-phone"></i>
                        <input type="text" placeholder="Phone Number">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-comment-alt"></i>
                        <textarea placeholder="Your Message"></textarea>
                    </div>
                    <button>Send Message</button>
                </div>
            </aside>
        </div>
        

        <?php if ($relatedJobs): ?>
    <?php foreach ($relatedJobs as $related): ?>
        <div class="related-job-card">
            <div class="job-left">
                <span class="posted-time"><?= date('d M Y', strtotime($related['posted_on'])) ?></span>
                <div class="job-header">
                    <img src="<?= !empty($related['logo']) ? '../uploads/company/' . htmlspecialchars($related['logo']) : '../uploads/company/default_logo.png' ?>" alt="Company Logo" class="company-logo">
                    <div>
                        <h4 class="job-title"><?= htmlspecialchars($related['job_title']) ?></h4>
                        <p class="company-name"><?= htmlspecialchars($related['job_role']) ?></p>
                    </div>
                </div>
                <div class="job-info">
                    <span><i class="fas fa-briefcase"></i> <?= htmlspecialchars($related['job_role']) ?></span>
                    <span><i class="fas fa-clock"></i> <?= htmlspecialchars($related['job_level']) ?></span>
                    <span><i class="fas fa-wallet"></i> <?= htmlspecialchars($related['currency']) ?> <?= number_format($related['min_salary']) ?> - <?= number_format($related['max_salary']) ?></span>
                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($related['city']) ?>, <?= htmlspecialchars($related['country']) ?></span>
                </div>
            </div>
            <div class="job-right">
                <a href="job_details.php?job_id=<?= $related['job_id'] ?>" class="job-details-btn">Job Details</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No related jobs found.</p>
<?php endif; ?>


    </div>
</div>



<?php include("../includes/footer_jobseeker.php"); ?>
