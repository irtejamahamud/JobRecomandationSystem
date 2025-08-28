<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Notifications</title>
	<style>
		body {
			font-family: 'Poppins', Arial, sans-serif;
			background: #f7f7f7;
			margin: 0;
			padding: 0;
		}
		.notification-panel {
			position: fixed;
			top: 0;
			right: -400px;
			width: 350px;
			height: 100vh;
			background: #fff;
			box-shadow: -2px 0 16px rgba(0,0,0,0.08);
			z-index: 9999;
			transition: right 0.4s cubic-bezier(.4,0,.2,1);
			padding: 0;
			display: flex;
			flex-direction: column;
		}
		.notification-panel.open {
			right: 0;
		}
		.notification-header {
			padding: 22px 24px 12px 24px;
			border-bottom: 1px solid #f2f2f2;
			background: #fff;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.notification-header h2 {
			font-size: 1.3rem;
			font-weight: 600;
			color: #222;
			margin: 0;
		}
		.close-btn {
			background: none;
			border: none;
			font-size: 1.5rem;
			color: #888;
			cursor: pointer;
			transition: color 0.2s;
		}
		.close-btn:hover {
			color: #e53935;
		}
		.notification-list {
			flex: 1;
			overflow-y: auto;
			padding: 18px 24px;
			background: #fafafa;
		}
		.notification-item {
			background: #fff;
			border-radius: 10px;
			box-shadow: 0 1px 6px rgba(0,0,0,0.04);
			padding: 16px 14px;
			margin-bottom: 16px;
			display: flex;
			align-items: flex-start;
			gap: 12px;
			border-left: 4px solid #ff6600;
		}
		.notification-item:last-child {
			margin-bottom: 0;
		}
		.notification-icon {
			font-size: 1.5rem;
			color: #ff6600;
			margin-top: 2px;
		}
		.notification-content {
			flex: 1;
		}
		.notification-title {
			font-weight: 600;
			color: #222;
			margin-bottom: 4px;
			font-size: 1rem;
		}
		.notification-time {
			color: #888;
			font-size: 0.85rem;
			margin-top: 2px;
		}
	</style>
</head>
<body>
	<div class="notification-panel">
		<div class="notification-header">
			<h2>Notifications</h2>
			<button class="close-btn" id="closeNotificationBtn">&times;</button>
		</div>
		<div class="notification-list">
			<div class="notification-item">
				<span class="notification-icon"><i class="fas fa-briefcase"></i></span>
				<div class="notification-content">
					<div class="notification-title">Your application for <b>Frontend Developer</b> was viewed</div>
					<div class="notification-time">2 hours ago</div>
				</div>
			</div>
			<div class="notification-item">
				<span class="notification-icon"><i class="fas fa-check-circle"></i></span>
				<div class="notification-content">
					<div class="notification-title">Profile completed! Unlock more job matches.</div>
					<div class="notification-time">Yesterday</div>
				</div>
			</div>
			<div class="notification-item">
				<span class="notification-icon"><i class="fas fa-bell"></i></span>
				<div class="notification-content">
					<div class="notification-title">New job alert: <b>UI/UX Designer</b> in your area</div>
					<div class="notification-time">3 days ago</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Font Awesome for icons -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
	<script>
		// Open notification panel automatically on page load
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelector('.notification-panel').classList.add('open');
			// Close button sends message to parent to close modal
			document.getElementById('closeNotificationBtn').onclick = function() {
				if(window.parent) {
					window.parent.postMessage('closeNotification', '*');
				}
			};
		});
	</script>
</body>
</html>
