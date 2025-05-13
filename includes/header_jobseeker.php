<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NextWorkX - Job Seeker</title>
  <link rel="stylesheet" href="../assets/css/jobseeker_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="container">
      <div class="logo">
        <img src="../assets/img/logo.png" alt="Logo" />
        <span>NextWorkX</span>
      </div>
      <nav class="nav-menu">
        <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">Home</a>
        <a href="job_search_demo.php" class="<?= ($current_page == 'job_search_demo.php') ? 'active' : '' ?>">Find Jobs</a>
        <!--<a href="job_search.php" class="<?= ($current_page == 'job_search.php') ? 'active' : '' ?>">Find Jobs</a> -->
        <!--<a href="recommended_jobs.php" class="<?= ($current_page == 'recommended_jobs.php') ? 'active' : '' ?>">Recommended</a> -->
        <a href="applied_jobs.php" class="<?= ($current_page == 'applied_jobs.php') ? 'active' : '' ?>">Applied Jobs</a>
        <a href="job_seeker_profile.php" class="<?= ($current_page == 'job_seeker_profile.php') ? 'active' : '' ?>">Profile</a>
      </nav>

      <div class="user-icons">
        <div class="icon-wrapper">
          <i class="fas fa-bell"></i>
        </div>
        <div class="user-dropdown">
          <img src="../assets/img/profile.png" alt="User"
               onerror="this.onerror=null;this.src='https://cdn-icons-png.flaticon.com/512/1144/1144760.png';" />
               <ul class="dropdown-menu">
  <li><a href="step1_personal.php"><i class="fas fa-user-cog"></i> Settings</a></li>
  <li><a href="bookmarks_job.php"><i class="fas fa-bookmark"></i> Bookmarks</a></li>
  <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
</ul>

        </div>
      </div>
    </div>
  </header>
