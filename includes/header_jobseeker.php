<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Default profile image
$profile_img = "https://cdn-icons-png.flaticon.com/512/1144/1144760.png";

// Load user profile image if available
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  include_once("../includes/db.php");
  // Try to get profile image from job_seekers table
  try {
    $stmt = $conn->prepare("SELECT profile_image FROM job_seekers WHERE job_seeker_id = ?");
    $stmt->execute([$user_id]);
    $img = $stmt->fetchColumn();
    if (!empty($img)) {
      $profile_img = "../uploads/profile/" . $img;
    }
  } catch (PDOException $e) {
    // Ignore and use default image
  }
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
  <style>
    /* Notifications badge and dropdown (scoped) */
    .icon-wrapper { position: relative; }
    #unreadBadge {
      display:none; position:absolute; top:-4px; right:-4px; background:#ff3b30; color:#fff; border-radius:999px; font-size:11px; line-height:16px; min-width:16px; height:16px; text-align:center; padding:0 4px;
    }
    #notifDropdown {
      display:none; position:absolute; right:0; top:36px; width:320px; max-height:380px; overflow:auto; background:#fff; border:1px solid rgba(0,0,0,0.08); box-shadow:0 8px 24px rgba(0,0,0,0.12); border-radius:10px; z-index:10001;
    }
    #notifDropdown .nd-header { padding:10px 12px; font-weight:600; border-bottom:1px solid #eee; }
    #notifDropdown .nd-list { list-style:none; margin:0; padding:0; }
    #notifDropdown .nd-item { display:flex; gap:10px; padding:10px 12px; border-left:3px solid #ff6600; cursor:pointer; }
    #notifDropdown .nd-item.read { border-left-color:#ddd; opacity:0.8; }
    #notifDropdown .nd-item:hover { background:#fafafa; }
    #notifDropdown .nd-title { font-size:14px; font-weight:500; color:#222; }
    #notifDropdown .nd-time { font-size:12px; color:#666; margin-top:2px; }
    #notifDropdown .nd-footer { padding:8px 12px; border-top:1px solid #eee; text-align:center; }
    #notifDropdown .nd-footer a { color:#007bff; text-decoration:none; font-size:13px; }
  </style>
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
        <!-- <a href="recommended_jobs.php" class="<?= ($current_page == 'recommended_jobs.php') ? 'active' : '' ?>">Recommended</a> -->
        <a href="applied_jobs.php" class="<?= ($current_page == 'applied_jobs.php') ? 'active' : '' ?>">Applied Jobs</a>
        <a href="job_seeker_profile.php" class="<?= ($current_page == 'job_seeker_profile.php') ? 'active' : '' ?>">Profile</a>
        <a href="generate_cv.php" class="<?= ($current_page == 'generate_cv.php') ? 'active' : '' ?>">Generate CV</a>
        <a href="ai_chat.php" class="<?= ($current_page == 'ai_chat.php') ? 'active' : '' ?>">AI Assistant</a>
      </nav>

      <div class="user-icons">
        <div class="icon-wrapper">
          <i class="fas fa-bell" id="notificationBell" style="cursor:pointer;"></i>
          <span id="unreadBadge"></span>
          <div id="notifDropdown">
            <div class="nd-header">Notifications</div>
            <ul class="nd-list" id="ndList"></ul>
            <div class="nd-footer"><a href="#" id="viewAllNotif">View all</a></div>
          </div>
        </div>
        <!-- Notification Modal -->
        <div id="notificationModal" style="display:none;position:fixed;top:0;right:0;width:350px;height:100vh;z-index:99999;background:transparent;">
          <iframe src="../user/notification_user.php" style="width:100%;height:100%;border:none;box-shadow:-2px 0 16px rgba(0,0,0,0.08);"></iframe>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            var bell = document.getElementById('notificationBell');
            var modal = document.getElementById('notificationModal');
            var badge = document.getElementById('unreadBadge');
            var dropdown = document.getElementById('notifDropdown');
            var list = document.getElementById('ndList');
            var viewAll = document.getElementById('viewAllNotif');
            var open = false;

            function setBadge(count){
              if (!badge) return;
              if (count > 0) { badge.textContent = count > 99 ? '99+' : String(count); badge.style.display = 'inline-block'; }
              else { badge.style.display = 'none'; }
            }

            function fetchUnread(){
              fetch('../ajax/notifications.php?limit=1', { credentials:'same-origin' })
                .then(r=>r.json())
                .then(d=>{ if(d && typeof d.unread !== 'undefined'){ setBadge(d.unread); } })
                .catch(()=>{});
            }

            function loadList(){
              list.innerHTML = '<li style="padding:12px;">Loading...</li>';
              fetch('../ajax/notifications.php?limit=8', { credentials:'same-origin' })
                .then(r=>r.json())
                .then(d=>{
                  setBadge(d.unread||0);
                  var items = d.notifications || [];
                  if(items.length === 0){ list.innerHTML = '<li style="padding:12px;color:#666;">No notifications</li>'; return; }
                  list.innerHTML = '';
                  items.forEach(function(n){
                    var li = document.createElement('li');
                    li.className = 'nd-item' + (n.is_read ? ' read' : '');
                    li.setAttribute('data-recipient-id', n.recipient_id);
                    li.innerHTML = '<div><div class="nd-title">'+ (n.title ? n.title.replace(/[<>]/g,'') : '') +'</div>' +
                                   (n.created_at ? '<div class="nd-time">'+ String(n.created_at) +'</div>' : '') + '</div>';
                    list.appendChild(li);
                  });
                }).catch(()=>{ list.innerHTML = '<li style="padding:12px;color:#c00;">Failed to load</li>'; });
            }

            function toggleDropdown(){
              open = !open;
              dropdown.style.display = open ? 'block' : 'none';
              if (open) { loadList(); }
            }

            bell.addEventListener('click', function(e) {
              // Toggle dropdown; ctrl+click opens full panel
              if (e && e.ctrlKey) { modal.style.display = 'block'; return; }
              toggleDropdown();
            });
            window.addEventListener('message', function(e) {
              if(e.data === 'closeNotification') {
                modal.style.display = 'none';
              }
            });

            // Click outside to close
            document.addEventListener('click', function(e){
              if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
                dropdown.style.display = 'none'; open = false;
              }
            });

            // Mark as read on click
            list.addEventListener('click', function(e){
              var item = e.target.closest('.nd-item');
              if (!item) return;
              var rid = item.getAttribute('data-recipient-id');
              if (!rid) return;
              fetch('../ajax/notifications.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=mark_read&recipient_id=' + encodeURIComponent(rid) })
                .then(()=>{ item.classList.add('read'); var c = parseInt(badge.textContent||'0',10); if(!isNaN(c)&&c>0){ setBadge(c-1); } });
            });

            // View all opens panel
            viewAll.addEventListener('click', function(e){ e.preventDefault(); dropdown.style.display='none'; open=false; modal.style.display='block'; });

            // Initial and periodic unread fetch
            fetchUnread();
            setInterval(fetchUnread, 30000);
          });
        </script>
        <div class="user-dropdown">
    <img src="<?= $profile_img ?>" alt="User"
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
