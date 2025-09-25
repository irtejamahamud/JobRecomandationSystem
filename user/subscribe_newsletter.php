<?php
// user/subscribe_newsletter.php
if (session_status() === PHP_SESSION_NONE) {
		session_start();
}
include_once('../includes/header.php');
include_once('../includes/db.php');

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$email = trim($_POST['email'] ?? '');
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$error = 'Please enter a valid email address.';
		} else {
				try {
						// Ensure table exists
						$conn->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
								id INT AUTO_INCREMENT PRIMARY KEY,
								email VARCHAR(255) NOT NULL UNIQUE,
								created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

						$stmt = $conn->prepare('INSERT INTO newsletter_subscribers (email) VALUES (:email)');
						$stmt->execute([':email' => $email]);
						$message = 'Thank you! You have been subscribed to our newsletter.';
				} catch (PDOException $e) {
						// Duplicate entry error code: 23000 (depending on driver)
						if ($e->getCode() === '23000') {
								$error = 'This email is already subscribed.';
						} else {
								$error = 'An error occurred. Please try again later.';
						}
				}
		}
}
?>

<link rel="stylesheet" href="../assets/css/newsletter.css">

	<main class="container" style="padding:40px 0">
		<div class="newsletter-card">
			<div class="newsletter-left">
				<h1>Join NextWorkX Newsletter</h1>
				<p>Get weekly job alerts, career tips, and hiring trends delivered to your inbox. Stay ahead in your job search with curated content from NextWorkX.</p>
				<ul>
					<li>Top jobs matched to your profile</li>
					<li>Resume & interview tips</li>
					<li>Industry and salary insights</li>
				</ul>
			</div>
			<div class="newsletter-form">
				<?php if ($message): ?>
					<div class="notice success"><?= htmlspecialchars($message) ?></div>
				<?php endif; ?>
				<?php if ($error): ?>
					<div class="notice error"><?= htmlspecialchars($error) ?></div>
				<?php endif; ?>

			<form method="POST" action="">
					<label for="email">Email address</label>
					<input type="email" name="email" id="email" placeholder="you@example.com" required>
					<button type="submit" class="btn-solid">Subscribe</button>
					<p class="muted">We never share your email. Unsubscribe anytime.</p>
				</form>
			</div>
		</div>
	</main>

<?php include_once('../includes/footer.php'); ?>
