<?php
include('includes/db.php');
include('includes/header.php');
?>

<link rel="stylesheet" href="assets/css/jobsearch.css">
<script src="assets/js/job_filter.js" defer></script>

<style>
  .login-warning {
    background-color: #fff3cd;
    color: #856404;
    padding: 15px;
    border: 1px solid #ffeeba;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
    width: 800px;
    margin-bottom:15px;
  }
  .apply-btn {
    background-color: #ff6600;
    color: #fff;
    border: none;
    padding: 8px 20px;
    margin-top: 10px;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    width: 100%;
  }
</style>

<div class="jobsearch-container">
  <h1 class="jobsearch-title">Job Search</h1>
  <p class="jobsearch-sub">Search for your desired job matching your skills</p>

  <form method="GET">
    <div class="jobsearch-grid">

      <!-- Sidebar Filters -->
      <aside class="jobsearch-filters">
        <!-- Search -->
        <div class="filter-group">
          <label for="search"><i class="fas fa-search"></i> Search by Job Title or Company</label>
          <input type="text" name="search" placeholder="Job title or company">
        </div>

        <!-- City -->
        <div class="filter-group">
          <label><i class="fas fa-map-marker-alt"></i> Location</label>
          <select name="city">
            <option value="">Choose city</option>
            <?php
            $cities = $conn->query("SELECT DISTINCT city FROM jobs WHERE city IS NOT NULL AND city != '' ORDER BY city");
            while ($c = $cities->fetch(PDO::FETCH_ASSOC)) {
              echo "<option value='{$c['city']}'>{$c['city']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Category -->
        <div class="filter-group">
          <label>Category</label>
          <div class="checkbox-list">
            <?php
            $categories = ['Commerce', 'Telecommunications', 'Hotels & Tourism', 'Education', 'Financial Services'];
            foreach ($categories as $cat) {
              echo "<label><input type='checkbox' name='category[]' value='$cat'> $cat</label>";
            }
            ?>
          </div>
        </div>

        <!-- Salary -->
        <div class="filter-group salary-filter">
          <label>Salary</label>
          <input type="range" name="salary_max" min="0" max="100000" value="50000" oninput="document.getElementById('salaryOut').textContent = this.value">
          <div class="salary-range">
            <span>Salary: $0 - $<span id="salaryOut">50000</span></span>
          </div>
        </div>

        <!-- Job Type -->
        <div class="filter-group">
          <label>Job Type</label>
          <div class="checkbox-list">
            <?php
            $types = ['Full Time', 'Part Time', 'Freelance', 'Seasonal', 'Fixed-Price'];
            foreach ($types as $t) {
              echo "<label><input type='checkbox' name='job_type[]' value='$t'> $t</label>";
            }
            ?>
          </div>
        </div>

        <!-- Experience -->
        <div class="filter-group">
          <label>Experience Level</label>
          <div class="checkbox-list">
            <?php
            $levels = ['No-experience', 'Fresher', 'Intermediate', 'Expert'];
            foreach ($levels as $lvl) {
              echo "<label><input type='checkbox' name='experience_level[]' value='$lvl'> $lvl</label>";
            }
            ?>
          </div>
        </div>

        <!-- Date Posted -->
        <div class="filter-group">
          <label>Date Posted</label>
          <div class="checkbox-list">
            <?php
            $opts = ['Last Hour', 'Last 24 Hours', 'Last 7 Days', 'Last 30 Days'];
            foreach ($opts as $opt) {
              echo "<label><input type='checkbox' name='date_posted[]' value='$opt'> $opt</label>";
            }
            ?>
          </div>
        </div>

        <!-- Apply Filters -->
        <div class="filter-group">
          <button type="submit" class="apply-btn">Apply Filters</button>
        </div>

        <!-- Clear Filters -->
        <div class="filter-group">
          <a href="job_finds.php" class="apply-btn" style="background: #ccc; color: #333; text-align:center; text-decoration:none;">Clear All Filters</a>
        </div>

        <!-- Hiring Banner -->
        <div class="hiring-banner" style="margin-top: 20px;">
          <div class="overlay" style="background-color: #ff6600; color: white; text-align: center; padding: 20px; border-radius: 10px;">
            <h2 style="margin:0; font-size: 20px;">WE ARE HIRING</h2>
            <p style="margin: 5px 0 0;">Apply Today!</p>
          </div>
        </div>
      </aside>

      <!-- Results Load Here -->
      <main class="jobsearch-results" id="job-results">
      <div id="job-loading" style="text-align:center; display:none; margin:20px;">
  <img src="assets/img/spinner.gif" alt="Loading..." width="50">
</div>

<div id="job-list"></div>

<div id="no-results" style="text-align:center; display:none; margin:20px;">
  <p style="font-size:18px; color:#777;">😞 No jobs found based on your filters.</p>
</div>

        <!-- Jobs will be loaded via AJAX -->
      </main>
    </div>
  </form>
</div>

<?php include('includes/footer.php'); ?>
