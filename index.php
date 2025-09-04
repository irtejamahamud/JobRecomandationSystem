<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="hero-content">
      <h1>Discover Your Perfect Job Match with <span>AI-Powered Precision</span></h1>
      <p>Thousands of jobs in all the leading sectors are waiting for you.</p>

      <div class="search-bar">
        <div class="input-group">
          <span class="icon"><i class="fas fa-search"></i></span>
          <input type="text" placeholder="Job title, Keyword...">
        </div>
        <div class="divider"></div>
        <div class="input-group">
          <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
          <input type="text" placeholder="Location">
        </div>
        <button type="submit">Find Job</button>
      </div>

      <p class="suggestions">Suggestion: 
        <span>UI/UX Designer</span>, 
        <span>Programming</span>, 
        <span>Digital Marketing</span>, 
        <span>Video</span>, 
        <span>Animation</span>
      </p>
    </div>
    <div class="hero-image">
      <img src="assets/img/hero_img.png" alt="AI Job Match Illustration">
    </div>
  </div>
</section>

<!-- Top Categories -->
<section class="top-categories">
  <div class="container">
    <div class="categories-grid">
      <div class="category-card">
        <i class="fas fa-laptop-code"></i>
        <h3>IT & Software</h3>
        <p>1200+ Jobs</p>
      </div>
      <div class="category-card">
        <i class="fas fa-chart-line"></i>
        <h3>Marketing</h3>
        <p>800+ Jobs</p>
      </div>
      <div class="category-card">
        <i class="fas fa-briefcase"></i>
        <h3>Finance</h3>
        <p>500+ Jobs</p>
      </div>
      <div class="category-card">
        <i class="fas fa-paint-brush"></i>
        <h3>Design</h3>
        <p>600+ Jobs</p>
      </div>
    </div>
  </div>
</section>

<!-- Featured Jobs -->
<section class="featured-jobs-section">
  <div class="container featured-jobs-container">
    <div class="featured-jobs-heading">
      <div>
        <h2>Featured Jobs</h2>
        <p>Choose jobs from the top employers and apply for the same.</p>
      </div>
    </div>

    <div class="featured-jobs-grid">
  <?php
  include 'includes/db.php'; // PDO connection assumed

  $stmt = $conn->prepare("
    SELECT 
      j.job_id, j.job_title, j.city, j.country, j.min_salary, j.max_salary, j.job_type,
      cp.company_name, cp.logo
    FROM jobs j
    JOIN recruiters r ON j.recruiter_id = r.user_id
    JOIN company_profiles cp ON cp.recruiter_id = r.user_id
    ORDER BY j.posted_on DESC
    LIMIT 3
  ");
  $stmt->execute();
  $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($jobs as $job):
    $logo = !empty($job['logo']) ? 'uploads/company/' . $job['logo'] : 'assets/img/logo-default.jpg';
    $typeClass = strtolower(str_replace(' ', '-', $job['job_type']));
    $salary = 'Salary: $' . number_format($job['min_salary']) . ' - $' . number_format($job['max_salary']);
  ?>
    <div class="featured-job-card">
      <div class="featured-job-header">
        <span class="featured-job-type <?= $typeClass ?>">
          <?= strtoupper(htmlspecialchars($job['job_type'])) ?>
        </span>
        <span class="featured-salary"><?= $salary ?></span>
      </div>
      <div class="featured-company-info">
        <img src="<?= $logo ?>" alt="<?= htmlspecialchars($job['company_name']) ?> Logo">
        <div class="featured-company-details">
          <h3><?= htmlspecialchars($job['job_title']) ?></h3>
          <p><?= htmlspecialchars($job['company_name']) ?><br>
            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?>
          </p>
        </div>
      </div>
      <div class="featured-applicants">
        <img src="uploads/profile/1706601911714.jpg" alt="applicant">
        <img src="uploads/profile/1706601911714.jpg" alt="applicant">
        <img src="uploads/profile/1706601911714.jpg" alt="applicant">
        <span>10+ applicants</span>
      </div>
      <div class="featured-job-buttons">
        <a href="job_details.php?id=<?= $job['job_id'] ?>" class="featured-btn-outline">View details</a>
        <a href="job_details.php?id=<?= $job['job_id'] ?>" class="featured-btn-solid">Apply now</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

</section>


<!-- Top Companies Hiring Section -->
<section class="top-companies-section">
  <div class="container top-companies-container">
    <div class="top-companies-heading">
      <h2>Top companies hiring now</h2>
    </div>

    <div class="top-companies-grid">
  <?php
  include 'includes/db.php';

  $stmt = $conn->prepare("
    SELECT 
      cp.company_name,
      cp.logo,
      MAX(j.city) AS city,
      MAX(j.country) AS country,
      COUNT(j.job_id) AS total_jobs
    FROM company_profiles cp
    JOIN recruiters r ON cp.recruiter_id = r.user_id
    JOIN jobs j ON j.recruiter_id = r.user_id
    GROUP BY cp.company_name, cp.logo
    ORDER BY total_jobs DESC
    LIMIT 4
  ");
  $stmt->execute();
  $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($companies as $company):
    $logo = !empty($company['logo']) ? 'uploads/company/' . $company['logo'] : 'assets/img/logo-default.jpg';
    $name = htmlspecialchars($company['company_name']);
    $location = htmlspecialchars($company['city']) . ', ' . htmlspecialchars($company['country']);
    $jobs = (int) $company['total_jobs'];
  ?>
    <div class="top-company-card">
      <img src="<?= $logo ?>" alt="<?= $name ?>">
      <h3><?= $name ?></h3>
      <div class="rating">★★★★★</div>
      <p><i class="fas fa-map-marker-alt"></i> <?= $location ?></p>
      <span class="open-jobs"><?= $jobs ?> Job<?= $jobs > 1 ? 's' : '' ?> Posted</span>
    </div>
  <?php endforeach; ?>
</div>


  </div>
</section>


<!-- News and Blog Section -->
<section class="news-blog-section">
  <div class="container news-blog-container">
    <div class="news-blog-heading">
      <div>
        <h2>News and Blog</h2>
        <p>Effective training and teamwork lead to success. Overcoming challenges together.</p>
      </div>
      <a href="#" class="view-all-link">View all</a>
    </div>

    <div class="news-blog-grid">
      <!-- Blog Card 1 -->
      <div class="news-blog-card">
        <div class="news-blog-image">
          <span class="badge-news">News</span>
          <img src="assets/img/Img.png" alt="Blog Image 1">
        </div>
        <div class="news-blog-content">
          <small>30 March 2024</small>
          <h3>Revitalizing Workplace Morale: Innovative Tactics For Boosting Employee Engagement In 2024</h3>
          <a href="#" class="read-more">Read more →</a>
        </div>
      </div>

      <!-- Blog Card 2 -->
      <div class="news-blog-card">
        <div class="news-blog-image">
          <span class="badge-blog">Blog</span>
          <img src="assets/img/Img.png" alt="Blog Image 2">
        </div>
        <div class="news-blog-content">
          <small>30 March 2024</small>
          <h3>How To Avoid The Top Six Most Common Job Interview Mistakes</h3>
          <a href="#" class="read-more">Read more →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="why-choose-us">
  <div class="container">
    <h2>Why Choose NextWorkX?</h2>
    <div class="reasons-grid">
      <div class="reason-card">
        <i class="fas fa-robot"></i>
        <h3>AI-Powered Matching</h3>
        <p>We use smart algorithms to find your perfect job match.</p>
      </div>
      <div class="reason-card">
        <i class="fas fa-shield-alt"></i>
        <h3>Secure & Private</h3>
        <p>Your data and privacy are fully protected with us.</p>
      </div>
      <div class="reason-card">
        <i class="fas fa-bolt"></i>
        <h3>Quick Application</h3>
        <p>Apply to jobs quickly and track your applications easily.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
