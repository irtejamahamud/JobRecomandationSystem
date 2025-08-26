<?php
session_start();
include("../includes/db.php");

$job_seeker_id = $_SESSION['user_id'] ?? null;
if (!$job_seeker_id) {
		header("Location: ../login.php");
		exit();
}

// Fetch profile fields for completion calculation
$stmt = $conn->prepare("SELECT biography, cover_letter, linkedin_link, marital_status, facebook_link, twitter_link, reddit_link, instagram_link, youtube_link FROM job_seeker_profiles WHERE job_seeker_id = ?");
$stmt->execute([$job_seeker_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$filled_fields = 0;
$total_fields = count($profile);
foreach ($profile as $value) {
		if (!empty($value)) $filled_fields++;
}
$completion = ($total_fields > 0) ? round(($filled_fields / $total_fields) * 100) : 0;

?>

<?php include("../includes/header_jobseeker.php"); ?>
<style>
	body {
		font-family: 'Poppins', Arial, sans-serif;
		background: #fefefe;
		color: #222;
	}
	.container {
		max-width: 700px;
		margin: 60px auto;
		padding: 0 30px;
		background: #fff;
		border-radius: 16px;
		box-shadow: 0 2px 16px rgba(0,0,0,0.07);
	}
	.dashboard-title {
		font-size: 2rem;
		font-weight: 700;
		color: #222;
		margin-top: 100px;
        padding-top: 30px;
	}
	.subtitle {
		margin-top: 5px;
		color: #666;
		font-size: 1rem;
		margin-bottom: 20px;
	}
	.progress-section {
		margin: 30px 0 20px 0;
		padding: 18px 0 0 0;
	}
	.progress-bar {
		width: 100%;
		background: #eee;
		border-radius: 8px;
		height: 32px;
		margin-bottom: 18px;
		overflow: hidden;
		box-shadow: 0 1px 4px rgba(76,175,80,0.08);
	}
	.progress {
		height: 100%;
		background: linear-gradient(90deg, #4caf50 60%, #81c784 100%);
		border-radius: 8px;
		text-align: center;
		color: #fff;
		line-height: 32px;
		font-weight: bold;
		font-size: 1.1rem;
		transition: width 0.6s cubic-bezier(.4,0,.2,1);
	}
	.alert {
		padding: 12px 18px;
		border-radius: 8px;
		margin: 10px 0 0 0;
		font-size: 1rem;
		font-weight: 500;
	}
	.alert-warning {
		background: #fffbe6;
		color: #b26a00;
		border: 1px solid #ffe58f;
	}
	.alert-success {
		background: #f0fff4;
		color: #2f7785;
		border: 1px solid #c6f6d5;
	}
	.profile-fields-status {
		margin-top: 28px;
		background: #f9f9f9;
		border-radius: 14px;
		box-shadow: 0 2px 10px rgba(0,0,0,0.04);
		padding: 22px 18px;
	}
	.profile-fields-status h3 {
		font-size: 1.2rem;
		margin-bottom: 16px;
		color: #222;
		font-weight: 600;
	}
	.profile-fields-status ul {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.profile-fields-status li {
		padding: 10px 0;
		border-bottom: 1px solid #ececec;
		font-size: 1rem;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	.profile-fields-status li:last-child {
		border-bottom: none;
	}
	.profile-fields-status strong {
		color: #333;
	}
	.profile-fields-status span {
		font-weight: 600;
		padding: 2px 10px;
		border-radius: 6px;
	}
	.profile-fields-status span[style*="color: #4caf50"] {
		background: #e8f5e9;
		color: #388e3c;
	}
	.profile-fields-status span[style*="color: #f44336"] {
		background: #ffebee;
		color: #c62828;
	}
</style>

<div class="container">
	<h1 class="dashboard-title">Profile Progress</h1>
	<p class="subtitle">See how complete your profile is and what you can improve.</p>

	<div class="progress-section">
		<label for="profile-progress"><strong>Profile Completion:</strong></label>
		<div class="progress-bar">
			<div class="progress" style="width: <?= $completion ?>%;">
				<?= $completion ?>%
			</div>
		</div>
		<?php if ($completion < 100): ?>
			<div class="alert alert-warning">Your profile is incomplete. Complete all sections for better job matches!</div>
		<?php else: ?>
			<div class="alert alert-success">Great job! Your profile is fully complete.</div>
		<?php endif; ?>
	</div>

	<div class="profile-fields-status">
		<h3>Profile Sections</h3>
		<ul>
			<?php foreach ($profile as $field => $value): ?>
				<li>
					<strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong>
					<?php if (!empty($value)): ?>
						<span style="color: #4caf50;">Completed</span>
					<?php else: ?>
						<span style="color: #f44336;">Incomplete</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php include("../includes/footer_jobseeker.php"); ?>
