<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// ➡️ Detect current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/loginstyle.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <title>NextWorkX</title>
</head>

<body>
  <header class="site-header">
    <div class="container">
      <div class="logo">
        <img src="assets/img/logo.png" alt="NextWorkX Logo">
        <span>NextWorkX</span>
      </div>

      <nav class="nav-menu">
        <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a>
        <a href="job_finds.php" class="<?= ($current_page == 'job_finds.php') ? 'active' : '' ?>">Find Jobs</a>
        <a href="blog.php" class="<?= ($current_page == 'blog.php') ? 'active' : '' ?>">Blog</a>
        <a href="aboutus.php" class="<?= ($current_page == 'aboutus.php') ? 'active' : '' ?>">About Us</a>
      </nav>

      <div class="auth-buttons">
        <a href="register.php" class="btn-outline">Sign Up</a>
        <a href="login.php" class="btn-solid">Login</a>
      </div>
    </div>
  </header>
