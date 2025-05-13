<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../includes/db.php");

// Default logo
$logo_path = "https://cdn-icons-png.flaticon.com/512/1144/1144760.png";

// Recruiter logo load
if (isset($_SESSION['user_id'])) {
    $recruiter_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT logo FROM company_profiles WHERE recruiter_id = ?");
    $stmt->execute([$recruiter_id]);
    $logo = $stmt->fetchColumn();
    if (!empty($logo)) {
        $logo_path = "../uploads/company/" . $logo;
    }
}

// ➡️ Detect active page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NextWorkX - Recruiter</title>
  <link rel="stylesheet" href="../assets/css/employee_style.css">
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
        <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active-link' : '' ?>">Home</a>
        <a href="post_job.php" class="<?= ($current_page == 'post_job.php') ? 'active-link' : '' ?>">Post Job</a>
        <a href="my_jobs.php" class="<?= ($current_page == 'my_jobs.php') ? 'active-link' : '' ?>">My Jobs</a>
        <a href="applications.php" class="<?= ($current_page == 'applications.php') ? 'active-link' : '' ?>">Applications</a>
        <a href="view_company.php" class="<?= ($current_page == 'view_company.php') ? 'active-link' : '' ?>">Company's Info</a>
      </nav>

      <div class="user-icons">
        <div class="icon-wrapper">
          <i class="fas fa-bell"></i>
        </div>

        <div class="user-dropdown">
          <img src="<?= $logo_path ?>" alt="User"
               onerror="this.onerror=null;this.src='https://cdn-icons-png.flaticon.com/512/1144/1144760.png';" />
          <ul class="recruiter-dropdown-menu">
            <li><a href="company_info.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>
