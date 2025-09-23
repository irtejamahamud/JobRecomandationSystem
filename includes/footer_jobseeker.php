<footer class="site-footer">
  <div class="container" style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start;padding:18px 0;">
    <div style="flex:1 1 220px;">
      <img src="../assets/img/logo.png" alt="NextWorkX" style="height:36px;object-fit:contain;margin-bottom:6px;">
      <p style="color:#666;max-width:320px;margin-top:6px;font-size:14px;">NextWorkX helps you find the right job faster — tailored recommendations and interview prep to accelerate your career.</p>
      <p style="color:#888;font-size:13px;margin-top:8px;">&copy; <?= date('Y') ?> NextWorkX. All rights reserved.</p>
    </div>

    <div style="flex:1 1 160px;">
      <h4 style="color:#FF6600;margin-bottom:6px;font-size:15px;">Quick links</h4>
      <ul style="list-style:none;padding:0;margin:0;color:#555;line-height:1.8;font-size:14px;">
        <li><a href="/user/dashboard.php" style="color:inherit;text-decoration:none;">Dashboard</a></li>
        <li><a href="/user/job_search.php" style="color:inherit;text-decoration:none;">Find Jobs</a></li>
        <li><a href="/user/recommended_jobs.php" style="color:inherit;text-decoration:none;">Recommended</a></li>
        <li><a href="/user/settings.php" style="color:inherit;text-decoration:none;">Profile Settings</a></li>
      </ul>
    </div>

    <div style="flex:1 1 220px;">
      <h4 style="color:#FF6600;margin-bottom:6px;font-size:15px;">Resources</h4>
      <ul style="list-style:none;padding:0;margin:0;color:#555;line-height:1.8;font-size:14px;">
        <li><a href="#" style="color:inherit;text-decoration:none;">About Us</a></li>
        <li><a href="#" style="color:inherit;text-decoration:none;">Blog & Tips</a></li>
        <li><a href="#" style="color:inherit;text-decoration:none;">Resume Builder</a></li>
        <li><a href="#" style="color:inherit;text-decoration:none;">Resume Templates</a></li>
      </ul>
    </div>

    <div style="flex:1 1 200px;">
      <h4 style="color:#FF6600;margin-bottom:6px;font-size:15px;">Stay in touch</h4>
      <p style="color:#666;font-size:13px;margin:0 0 6px;">Get job alerts and interview tips — delivered weekly.</p>
      <form action="/user/subscribe_newsletter.php" method="post" style="display:flex;gap:6px;">
        <input name="email" type="email" placeholder="Your email" required style="flex:1;padding:8px;border-radius:6px;border:1px solid #eee;">
        <button type="submit" style="background:#FF6600;color:#fff;border:none;padding:8px 10px;border-radius:6px;font-weight:600;">Subscribe</button>
      </form>
      <div style="margin-top:10px;display:flex;gap:10px;align-items:center;font-size:18px;color:#FF6600;">
        <a href="#" style="color:inherit;text-decoration:none;"><i class="fab fa-facebook"></i></a>
        <a href="#" style="color:inherit;text-decoration:none;"><i class="fab fa-linkedin"></i></a>
        <a href="#" style="color:inherit;text-decoration:none;"><i class="fab fa-twitter"></i></a>
      </div>
    </div>
  </div>

  <!-- <div style="border-top:1px solid #eee;margin-top:0px;padding:6px 0;">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
      <small style="color:#666;font-size:12px;">Privacy · Terms · Contact</small>
      <small style="color:#666;font-size:12px;">Need help? <a href="/contact_info.php" style="color:#FF6600;text-decoration:underline;">Contact support</a></small>
    </div>
  </div> -->
</footer>
</body>
</html>
