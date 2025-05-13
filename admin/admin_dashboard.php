<?php
include 'admin_header.php';
include '../includes/db.php'; // make sure your db.php connection file path is correct

// Fetch recent jobs
$stmt = $conn->query("SELECT * FROM jobs ORDER BY posted_on DESC LIMIT 5");
$recentJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch statistics (example: count total job posts and applications)
$jobPostsCount = $conn->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$applicationsCount = $conn->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$meetingsCount = 125; // Example static number (you can later fetch dynamically)
$hiringsCount = $conn->query("SELECT COUNT(*) FROM applied_jobs WHERE status = 'Hired'")->fetchColumn();

?>

<div class="admin-dashboard">
<!-- Sidebar -->
<aside class="admin-sidebar">
  <div class="sidebar-header">
    <div class="menu-toggle">
      <i class="fas fa-bars"></i>
    </div>
    <div class="logo">
      <img src="../assets/img/logo.png" alt="NextWorkX Logo">
      <span>NextWorkX</span>
    </div>
  </div>

  <nav class="admin-nav">
    <a href="#"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="#"><i class="fas fa-briefcase"></i> Post Job</a>
    <a href="#"><i class="fas fa-user-graduate"></i> Post Internship</a>
    <a href="#"><i class="fas fa-laptop"></i> Application</a>
  </nav>
</aside>

<!-- Main Content -->
<div class="admin-main">

  <!-- Top Bar -->
  <div class="admin-topbar">
    <div class="search-bar">
      <input type="text" placeholder="Search for Jobs and etc.">
    </div>
    <div class="top-icons">
      <i class="fas fa-bell"></i>
      <span class="notification-count">2</span>
    </div>
  </div>

  <!-- Welcome Section -->
  <div class="welcome-banner">
    <div class="welcome-text">
      <h2>Welcome To</h2>
      <h1>Irteja Mahmud</h1>
    </div>
    <div class="welcome-img">
      <img src="../assets/img/welcome.png" alt="Welcome Illustration">
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="stats-cards">
    <div class="stat-card">
      <h3>Job Posts</h3>
      <p><?php echo htmlspecialchars($jobPostsCount); ?></p>
      <span class="stat-positive">+2.5%</span>
    </div>
    <div class="stat-card">
      <h3>Total Applications</h3>
      <p><?php echo htmlspecialchars($applicationsCount); ?></p>
      <span class="stat-negative">-4.4%</span>
    </div>
    <div class="stat-card">
      <h3>No of Meetings</h3>
      <p><?php echo htmlspecialchars($meetingsCount); ?></p>
      <span class="stat-positive">+1.5%</span>
    </div>
    <div class="stat-card">
      <h3>No of Hirings</h3>
      <p><?php echo htmlspecialchars($hiringsCount); ?></p>
      <span class="stat-positive">+4.5%</span>
    </div>
  </div>

  <!-- Dashboard Bottom -->
  <div class="dashboard-bottom">

    <!-- Application Response Section -->
    <div class="application-response">
      <div class="application-header">
        <h3>Application Response</h3>
        <a href="#" class="download-report">Download Report</a>
      </div>

      <div class="application-chart">
        <img src="../assets/img/Group 2386.png" alt="Application Chart">
      </div>

      <div class="application-stats">
        <div class="stat-item">
          <div class="dot shortlisted"></div>
          <p>Shortlisted</p>
          <h4>942</h4>
        </div>
        <div class="stat-item">
          <div class="dot hired"></div>
          <p>Hired</p>
          <h4>25</h4>
        </div>
        <div class="stat-item">
          <div class="dot rejected"></div>
          <p>Rejected</p>
          <h4>2,452</h4>
        </div>
      </div>
    </div>

    <!-- Recent Jobs Section -->
    <div class="recent-jobs">
      <div class="recent-jobs-header">
        <h3>Recent Job Posts</h3>
        <div class="tabs">
          <button>Monthly</button>
          <button>Weekly</button>
          <button class="active">Today</button>
        </div>
      </div>

      <table class="recent-jobs-table">
        <thead>
          <tr>
            <th>Job Title</th>
            <th>Category</th>
            <th>Openings</th>
            <th>Applications</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentJobs as $job): ?>
            <tr>
              <td><?php echo htmlspecialchars($job['job_title']); ?></td>
              <td><?php echo htmlspecialchars($job['job_role']); ?></td>
              <td><?php echo htmlspecialchars($job['vacancies']); ?></td>
              <td>
                <?php
                  // Count how many applied for this job
                  $jobId = $job['job_id'];
                  $appCountStmt = $conn->prepare("SELECT COUNT(*) FROM applied_jobs WHERE job_id = ?");
                  $appCountStmt->execute([$jobId]);
                  $applications = $appCountStmt->fetchColumn();
                  echo htmlspecialchars($applications);
                ?>
              </td>
              <td>
                <?php
                  $today = date('Y-m-d');
                  $expireDate = $job['expire_date'];
                  if ($expireDate >= $today) {
                    echo '<span class="status-active">Active</span>';
                  } else {
                    echo '<span class="status-inactive">Inactive</span>';
                  }
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

  <!-- Footer -->
  <div class="admin-footer">
    <p>© 2025 All Rights Reserved to NextWorkX | Version 0.1</p>
  </div>

</div>

</div> <!-- end admin-dashboard -->
