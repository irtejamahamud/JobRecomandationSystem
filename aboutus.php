<?php
include('includes/header.php'); // Reuse your main header
?>

<link rel="stylesheet" href="assets/css/about.css">

<div class="about-container">
<!-- Hero Section -->
<section class="split-hero">
  <div class="text-section">
    <h2>Empowering Careers with AI-Powered Job Matching</h2>
    <p>NextWorkX helps job seekers and employers connect through smart, skill-based recommendations. Whether you're a fresher or a professional, our intelligent system ensures you're matched with the right opportunity at the right time.</p>
  </div>
  <div class="image-section">
    <img src="assets/img/Img.png" alt="About Section Image">
  </div>
</section>



  <!-- How it works -->
<section class="how-it-works">
  <h2>How It Works</h2>
  <p>Find the right job faster with AI-powered support.</p>
  <div class="steps">
    <div class="step-box">
      <i class="fas fa-user-plus"></i>
      <h4>Sign Up</h4>
      <p>Create your job seeker account.</p>
    </div>
    <div class="step-box">
      <i class="fas fa-file-upload"></i>
      <h4>Build Profile</h4>
      <p>Add details and upload your resume.</p>
    </div>
    <div class="step-box">
      <i class="fas fa-robot"></i>
      <h4>Get Suggestions</h4>
      <p>Receive smart job matches instantly.</p>
    </div>
    <div class="step-box">
      <i class="fas fa-paper-plane"></i>
      <h4>Apply Easily</h4>
      <p>Apply with one click and track status.</p>
    </div>
  </div>
</section>


<!-- Video Section -->
<section class="video-section">
  <div class="video-overlay">
    <div class="video-wrapper">
      <iframe 
        src="https://www.youtube.com/embed/tgbNymZ7vqY" 
        title="Career Boost AI Video" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
        allowfullscreen>
      </iframe>
    </div>
    <h2>Good Life Begins With A Good Company</h2>
  </div>

  <div class="video-steps">
    <div>
      <span>1</span>
      <p>Explore AI-driven career options tailored to your goals.</p>
      <a href="#">Learn more</a>
    </div>
    <div>
      <span>2</span>
      <p>Upload your resume and build a strong profile easily.</p>
      <a href="#">Learn more</a>
    </div>
    <div>
      <span>3</span>
      <p>Apply to top roles directly through our smart platform.</p>
      <a href="#">Learn more</a>
    </div>
  </div>
</section>




<!-- FAQ Section -->
<section class="faq-section">
  <h2>Frequently Asked Questions</h2>
  <p>Answers to common questions about using NextWorkX</p>

  <div class="faq-item active">
  <h3><span>01</span> How do I create a job seeker account? <i class="fas fa-times"></i></h3>
  <p>Click on "Sign Up", select "Job Seeker", fill in your details, and start exploring job opportunities tailored to your skills.</p>
</div>


  <div class="faq-item">
    <h3><span>02</span> Can I upload my resume to apply for jobs? <i class="fas fa-plus"></i></h3>
    <p>Yes, after creating your account, go to your profile and upload a PDF version of your resume under the Resume Upload section.</p>
  </div>

  <div class="faq-item">
    <h3><span>03</span> How does the recommendation system work? <i class="fas fa-plus"></i></h3>
    <p>Our AI analyzes your profile, skills, and search history to suggest jobs that best match your background and interests.</p>
  </div>

  <div class="faq-item">
    <h3><span>04</span> Can employers post jobs on the platform? <i class="fas fa-plus"></i></h3>
    <p>Yes, employers can register, set up their company profile, and post jobs using their dashboard under the employer portal.</p>
  </div>

  <div class="faq-item">
    <h3><span>05</span> Will I get notifications for new matching jobs? <i class="fas fa-plus"></i></h3>
    <p>Yes, once logged in, you can enable notifications and receive alerts when jobs relevant to your profile are posted.</p>
  </div>
</section>


  <!-- Best Company Section -->
  <section class="best-company">
    <div class="best-images">
      <img src="assets/img/Img_1.png" alt="">
      <img src="assets/img/Img_1.png" alt="">
      <img src="assets/img/Img_1.png" alt="">
    </div>
    <div class="best-text">
      <h2>We're Only Working With The Best</h2>
      <p>Ultrices purus dolor viverra mi laoreet at cursus justo...</p>
      <ul>
        <li><i class="fas fa-check-circle"></i> Quality Job</li>
        <li><i class="fas fa-file-alt"></i> Resume builder</li>
        <li><i class="fas fa-building"></i> Top Companies</li>
        <li><i class="fas fa-user-tie"></i> Top Talents</li>
      </ul>
    </div>
  </section>
</div>

<?php include('includes/footer.php'); ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Hide all p tags initially
    document.querySelectorAll('.faq-item').forEach((item, index) => {
      const content = item.querySelector('p');
      const icon = item.querySelector('i');

      if (index === 0) {
        item.classList.add('active');
        content.style.display = 'block';
        icon.classList.remove('fa-plus');
        icon.classList.add('fa-times');
      } else {
        content.style.display = 'none';
        item.classList.remove('active');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-plus');
      }
    });

    // Add click behavior
    document.querySelectorAll('.faq-item h3').forEach(header => {
      header.addEventListener('click', () => {
        const parent = header.parentElement;
        const isActive = parent.classList.contains('active');

        document.querySelectorAll('.faq-item').forEach(item => {
          item.classList.remove('active');
          item.querySelector('p').style.display = 'none';
          const icon = item.querySelector('i');
          icon.classList.remove('fa-times');
          icon.classList.add('fa-plus');
        });

        if (!isActive) {
          parent.classList.add('active');
          parent.querySelector('p').style.display = 'block';
          const icon = parent.querySelector('i');
          icon.classList.remove('fa-plus');
          icon.classList.add('fa-times');
        }
      });
    });
  });
</script>




