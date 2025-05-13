<?php include 'includes/header.php'; ?>
<?php include 'fetch_news.php'; ?>
<link rel="stylesheet" href="assets/css/blog.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<?php $newsArticles = fetchJobNews(); ?>

<section class="blog-container">
  <div class="blog-wrapper">

    <!-- Sidebar -->
    <aside class="blog-sidebar">
      <div class="sidebar-box">
        <h3>Search</h3>
        <div class="search-input-group">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search...">
        </div>
      </div>

      <div class="sidebar-box">
        <h3>Category</h3>
        <ul class="checkbox-list">
          <li><label><input type="checkbox"> Graphics & Design</label></li>
          <li><label><input type="checkbox"> Code & Programming</label></li>
          <li><label><input type="checkbox"> Digital Marketing</label></li>
          <li><label><input type="checkbox"> Video & Animation</label></li>
          <li><label><input type="checkbox"> Music & Audio</label></li>
          <li><label><input type="checkbox"> Finance & Accounting</label></li>
          <li><label><input type="checkbox"> Health & Care</label></li>
          <li><label><input type="checkbox"> Data Science</label></li>
        </ul>
      </div>

      <div class="sidebar-box">
        <h3>Recent Post</h3>
        <div class="recent-post">
          <img src="assets/img/thumb1.jpg" alt="">
          <div>
            <small>Nov 12, 2021 • 25 Comments</small>
            <p>Integer volutpat fringilla ipsum, nec tempor risus facilisis eget.</p>
          </div>
        </div>
        <div class="recent-post">
          <img src="assets/img/thumb2.jpg" alt="">
          <div>
            <small>Nov 12, 2021 • 25 Comments</small>
            <p>Integer volutpat fringilla ipsum, nec tempor risus facilisis eget.</p>
          </div>
        </div>
        <div class="recent-post">
          <img src="assets/img/thumb1.jpg" alt="">
          <div>
            <small>Nov 12, 2021 • 25 Comments</small>
            <p>Integer volutpat fringilla ipsum, nec tempor risus facilisis eget.</p>
          </div>
        </div>
      </div>

      <div class="sidebar-box">
        <h3>Gallery</h3>
        <div class="gallery-grid">
          <img src="assets/img/g1.jpg" alt="">
          <img src="assets/img/g1.jpg" alt="">
          <img src="assets/img/g1.jpg" alt="">
          <img src="assets/img/g1.jpg" alt="">
          <img src="assets/img/g1.jpg" alt="">
          <img src="assets/img/g1.jpg" alt="">
        </div>
      </div>

      <div class="sidebar-box">
        <h3>Popular Tag</h3>
        <div class="tag-list">
          <span>Design</span>
          <span class="active">Programming</span>
          <span>Health & Care</span>
          <span>Motion Design</span>
          <span>Photography</span>
          <span>Politics</span>
        </div>
      </div>
    </aside>

    <!-- Blog Content -->
    <div class="blog-content">
      <h2 style="margin-bottom: 20px;">Latest Job News</h2>

      <?php if (empty($newsArticles)): ?>
        <p>⚠️ Sorry, no job-related news found right now.</p>
      <?php else: ?>
        <?php foreach ($newsArticles as $article): ?>
        <div class="blog-post">
          <img src="<?= $article['urlToImage'] ?? 'assets/img/default.jpg' ?>" class="post-thumb" alt="news">
          <div class="post-info">
            <small>
              <i class="fas fa-calendar-alt"></i>
              <?= date("M d, Y", strtotime($article['publishedAt'])) ?>
              • <i class="fas fa-globe"></i> <?= parse_url($article['url'], PHP_URL_HOST) ?>
            </small>
            <h3><?= htmlspecialchars($article['title']) ?></h3>
            <p><?= htmlspecialchars($article['description'] ?? 'No description available.') ?></p>
            <a href="<?= $article['url'] ?>" target="_blank">Read more <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>
