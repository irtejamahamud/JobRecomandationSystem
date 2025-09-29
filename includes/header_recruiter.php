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
  <style>
    .icon-wrapper { position: relative; }
    #unreadBadgeEmp {
      display:none; position:absolute; top:-4px; right:-4px; background:#ff3b30; color:#fff; border-radius:999px; font-size:11px; line-height:16px; min-width:16px; height:16px; text-align:center; padding:0 4px;
    }
    #notifDropdownEmp {
      display:none; position:absolute; right:0; top:36px; width:320px; max-height:380px; overflow:auto; background:#fff; border:1px solid rgba(0,0,0,0.08); box-shadow:0 8px 24px rgba(0,0,0,0.12); border-radius:10px; z-index:10001;
    }
    #notifDropdownEmp .nd-header { padding:10px 12px; font-weight:600; border-bottom:1px solid #eee; }
    #notifDropdownEmp .nd-list { list-style:none; margin:0; padding:0; }
    #notifDropdownEmp .nd-item { display:flex; gap:10px; padding:10px 12px; border-left:3px solid #ff6600; cursor:pointer; }
    #notifDropdownEmp .nd-item.read { border-left-color:#ddd; opacity:0.8; }
    #notifDropdownEmp .nd-item:hover { background:#fafafa; }
    #notifDropdownEmp .nd-title { font-size:14px; font-weight:500; color:#222; }
    #notifDropdownEmp .nd-time { font-size:12px; color:#666; margin-top:2px; }
    #notifDropdownEmp .nd-footer { padding:8px 12px; border-top:1px solid #eee; text-align:center; }
    #notifDropdownEmp .nd-footer a { color:#007bff; text-decoration:none; font-size:13px; }
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
        <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active-link' : '' ?>">Home</a>
        <a href="post_job.php" class="<?= ($current_page == 'post_job.php') ? 'active-link' : '' ?>">Post Job</a>
        <a href="my_jobs.php" class="<?= ($current_page == 'my_jobs.php') ? 'active-link' : '' ?>">My Jobs</a>
        <a href="applications.php" class="<?= ($current_page == 'applications.php') ? 'active-link' : '' ?>">Applications</a>
        <a href="view_company.php" class="<?= ($current_page == 'view_company.php') ? 'active-link' : '' ?>">Company's Info</a>
      </nav>

      <div class="user-icons">
        <div class="icon-wrapper" id="notificationBell" style="position:relative;">
          <i class="fas fa-bell" style="cursor:pointer;"></i>
          <span id="unreadBadgeEmp"></span>
          <div id="notifDropdownEmp">
            <div class="nd-header">Notifications</div>
            <ul class="nd-list" id="ndListEmp"></ul>
            <div class="nd-footer"><a href="#" id="viewAllNotifEmp">View all</a></div>
          </div>
        </div>
        <!-- Notification Modal Panel -->
        <div id="notificationModal" style="display:none;position:fixed;top:0;right:0;width:350px;height:100vh;z-index:10000;background:transparent;">
          <iframe src="../employer/notification_emp.php" style="width:100%;height:100%;border:none;background:transparent;"></iframe>
        </div>
  <script>
    // Show notification panel when bell is clicked
    document.addEventListener('DOMContentLoaded', function() {
      var bell = document.getElementById('notificationBell');
      var modal = document.getElementById('notificationModal');
      var badge = document.getElementById('unreadBadgeEmp');
      var dropdown = document.getElementById('notifDropdownEmp');
      var list = document.getElementById('ndListEmp');
      var viewAll = document.getElementById('viewAllNotifEmp');
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

      if(bell && modal) {
        bell.addEventListener('click', function(e) {
          if (e && e.ctrlKey) { modal.style.display = 'block'; return; }
          toggleDropdown();
        });
      }
      // Listen for closeNotification message from iframe
      window.addEventListener('message', function(event) {
        if(event.data === 'closeNotification') {
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

      fetchUnread();
      setInterval(fetchUnread, 30000);
    });
  </script>

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
