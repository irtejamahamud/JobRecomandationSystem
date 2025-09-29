<?php
// includes/notifications.php
// Helper functions to manage notifications

if (!function_exists('add_notification')) {
    function add_notification(PDO $conn, $actor_user_id, $type, $title, $body = null, $target_type = null, $target_id = null, $recipients = [], $metadata = null) {
        $stmt = $conn->prepare("INSERT INTO notifications (actor_user_id, type, title, body, target_type, target_id, metadata) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $metaJson = $metadata ? json_encode($metadata, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : null;
        $stmt->execute([$actor_user_id, $type, $title, $body, $target_type, $target_id, $metaJson]);
        $notification_id = (int)$conn->lastInsertId();

        $recipients = is_array($recipients) ? $recipients : [$recipients];
        if (!empty($recipients)) {
            $ins = $conn->prepare("INSERT INTO notification_recipients (notification_id, user_id) VALUES (?, ?)");
            foreach ($recipients as $uid) {
                if ($uid) { $ins->execute([$notification_id, (int)$uid]); }
            }
        }
        return $notification_id;
    }
}

if (!function_exists('get_notifications_for_user')) {
    function get_notifications_for_user(PDO $conn, $user_id, $limit = 20, $unreadOnly = false) {
    $limit = (int)$limit;
    if ($limit <= 0) { $limit = 20; }
    if ($limit > 200) { $limit = 200; }
    $sql = "SELECT nr.id as recipient_id, n.id as notification_id, n.actor_user_id, n.type, n.title, n.body, n.target_type, n.target_id, n.metadata, n.created_at, nr.is_read, nr.read_at
        FROM notification_recipients nr
        JOIN notifications n ON n.id = nr.notification_id
        WHERE nr.user_id = ?" . ($unreadOnly ? " AND nr.is_read = 0" : "") . "
        ORDER BY n.created_at DESC
        LIMIT $limit"; // inline validated integer to satisfy MySQL LIMIT semantics
    $stmt = $conn->prepare($sql);
    $stmt->execute([(int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('unread_count')) {
    function unread_count(PDO $conn, $user_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM notification_recipients WHERE user_id = ? AND is_read = 0");
        $stmt->execute([(int)$user_id]);
        return (int)$stmt->fetchColumn();
    }
}

if (!function_exists('mark_notification_read')) {
    function mark_notification_read(PDO $conn, $recipient_id, $user_id = null) {
        if ($user_id) {
            $stmt = $conn->prepare("UPDATE notification_recipients SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?");
            return $stmt->execute([(int)$recipient_id, (int)$user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE notification_recipients SET is_read = 1, read_at = NOW() WHERE id = ?");
            return $stmt->execute([(int)$recipient_id]);
        }
    }
}

?>
