<?php include('../includes/header_recruiter.php'); ?>
<link rel="stylesheet" href="../assets/css/employee_style.css">
<?php include('../includes/db.php'); // ensure connection for category dropdown ?>

<div class="job-post-wrapper">
  <h2>Post a Job</h2>
  <p class="subtitle">Find the best talent for your company</p>

  <form action="submit_job.php" method="POST" class="job-grid">
    <!-- Row 1 -->
    <div class="form-group">
      <label>Job Title</label>
      <input type="text" name="job_title" required placeholder="e.g. Software Engineer">
    </div>
    <div class="form-group">
      <label>Tags</label>
      <input type="text" name="tags" placeholder="e.g. PHP, Laravel, Team Lead">
    </div>

    <!-- Row 2 -->
    <div class="form-group">
      <label>Job Role</label>
      <select name="job_role" required>
        <option value="">Select...</option>
        <option>Developer</option>
        <option>Designer</option>
        <option>Manager</option>
        <option>Analyst</option>
        <option>Marketing Executive</option>
        <option>Sales Executive</option>
        <option>HR Officer</option>
        <option>Project Manager</option>
        <option>Customer Support</option>
      </select>
    </div>
    <div class="form-group">
      <label>Job Level</label>
      <select name="job_level">
        <option>Entry</option>
        <option>Mid</option>
        <option>Senior</option>
      </select>
    </div>

    <!-- Row 2.5 -->
    <div class="form-group">
      <label>Job Type</label>
      <select name="job_type" required>
        <option value="Full Time">Full Time</option>
        <option value="Part Time">Part Time</option>
        <option value="Remote">Remote</option>
        <option value="Freelance">Freelance</option>
        <option value="Seasonal">Seasonal</option>
        <option value="Fixed-Price">Fixed-Price</option>
      </select>
    </div>

    <!-- ✅ New Category Dropdown -->
    <div class="form-group">
      <label>Category</label>
      <select name="category" required>
        <option value="">Select Category...</option>
        <?php
        $stmt = $conn->query("SELECT name FROM job_categories ORDER BY name ASC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo "<option value='{$row['name']}'>{$row['name']}</option>";
        }
        ?>
      </select>
    </div>

    <!-- Row 3 -->
    <div class="form-group">
      <label>Min Salary</label>
      <input type="number" name="min_salary" placeholder="e.g. 20000">
    </div>
    <div class="form-group">
      <label>Max Salary</label>
      <input type="number" name="max_salary" placeholder="e.g. 50000">
    </div>

    <!-- Row 4 -->
    <div class="form-group">
      <label>Vacancies</label>
      <input type="number" name="vacancies" placeholder="e.g. 3">
    </div>
    <div class="form-group">
      <label>Currency</label>
      <input type="text" name="currency" value="BDT" maxlength="10">
    </div>

    <!-- Row 5 -->
    <div class="form-group">
      <label>Country</label>
      <input type="text" name="country" placeholder="e.g. Bangladesh">
    </div>
    <div class="form-group">
      <label>City</label>
      <input type="text" name="city" placeholder="e.g. Dhaka">
    </div>

    <!-- Row 6 -->
    <div class="form-group">
      <label>Start Date</label>
      <input type="date" name="start_date" required>
    </div>
    <div class="form-group">
      <label>Expiration Date</label>
      <input type="date" name="expire_date" required>
    </div>

    <!-- Row 7 -->
    <div class="form-group full-width">
      <label>Skills Required</label>
      <input type="text" name="skills" placeholder="e.g. HTML, CSS, JS, MySQL">
    </div>

    <!-- Row 8 -->
    <div class="form-group full-width">
      <label>Job Description</label>
      <textarea name="job_description" rows="5" placeholder="Describe the role, responsibilities, and expectations..."></textarea>
    </div>

    <!-- Job Details -->
    <div class="form-group">
      <label>Experience Required</label>
      <input type="text" name="experience_required" placeholder="e.g. 5 Years">
    </div>
    <div class="form-group">
      <label>Degree Required</label>
      <input type="text" name="degree_required" placeholder="e.g. BSc, MSc, MBA">
    </div>

    <div class="form-group full-width">
      <label>Key Responsibilities</label>
      <textarea name="responsibilities" rows="3" placeholder="Bullet point key responsibilities..."></textarea>
    </div>

    <div class="form-group full-width">
      <label>Professional Skills</label>
      <textarea name="professional_skills" rows="3" placeholder="Bullet point skills needed..."></textarea>
    </div>

    <div class="form-group full-width">
      <label>Google Map Embed URL</label>
      <textarea name="map_embed_url" rows="2" placeholder="Paste Google Map iframe src value only..."></textarea>
    </div>

    <!-- Submit -->
    <div class="form-group full-width">
      <button type="submit">Post Job</button>
    </div>
  </form>
</div>

<?php include('../includes/footer_recruiter.php'); ?>
