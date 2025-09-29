<?php
// ajax/notifications.php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once('../includes/db.php');
require_once('../includes/notifications.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'mark_read') {
            $rid = (int)($_POST['recipient_id'] ?? 0);
            if ($rid > 0) {
                mark_notification_read($conn, $rid, $user_id);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['error' => 'Bad Request']);
        exit;
    }

    // GET notifications
    $limit = (int)($_GET['limit'] ?? 10);
    $unreadOnly = isset($_GET['unread']) ? (bool)$_GET['unread'] : false;
    $list = get_notifications_for_user($conn, $user_id, $limit, $unreadOnly);
    $count = unread_count($conn, $user_id);
    echo json_encode(['unread' => $count, 'notifications' => $list]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>
