<?php
session_start();
include("includes/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE email = :input OR fullname = :input";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':input', $email_or_username);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname']; // ✅ Add this here
        
            // Redirect based on role
            if ($user['role'] === 'recruiter') {
                header("Location: employer/dashboard.php");
            } elseif ($user['role'] === 'job_seeker') {
                header("Location: user/dashboard.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php"); // default fallback
            }
            exit;
        }
         else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>



<?php include("includes/header.php"); ?>

<div class="login-wrapper">
    <div class="login-box">
        <form method="POST">
            <h2>Login to your Account</h2>
            <p class="subtitle">Welcome back! Select the below login methods.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>

            <label>Email ID / Username</label>
            <input type="text" name="email" required placeholder="Enter email id / username">

            <label>Password</label>
            <div class="password-wrap">
                <input type="password" name="password" required placeholder="Enter password">
                <span class="toggle">Show</span>
            </div>

            <div class="login-options">
                <label><input type="checkbox"> Remember me</label>
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" class="login-btn">Login</button>

            <div class="social-line"><span>or login with</span></div>
            <div class="social-icons">
                <img src="assets/img/google.svg">
                <img src="assets/img/facebook.svg">
                <img src="assets/img/linkedin.svg">
            </div>

            <p class="register-prompt">Don’t have an account? <a href="register.php">Register</a></p>
        </form>

        <div class="login-illustration">
            <img src="assets/img/login_image.png" alt="Login">
        </div>
    </div>
</div>

