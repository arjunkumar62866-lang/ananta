<?php
ob_start();
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$userid = $_SESSION['userid'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'get_latest';

if ($action === 'get_unread_count') {
    $count = getUnreadNotificationCount($userid, $pdo);
    echo json_encode([
        'status'       => 'success',
        'unread_count' => $count
    ]);
    exit();
}

if ($action === 'get_latest') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
    $count = getUnreadNotificationCount($userid, $pdo);
    $notifs = getUserNotifications($userid, $limit, 0, $pdo);

    // Format timestamps for human readability
    $formattedNotifs = array_map(function($n) {
        $n['formatted_time'] = date('d M, h:i A', strtotime($n['created_at']));
        return $n;
    }, $notifs);

    echo json_encode([
        'status'        => 'success',
        'unread_count'  => $count,
        'notifications' => $formattedNotifs
    ]);
    exit();
}

if ($action === 'mark_read') {
    $notifId = intval($_POST['id'] ?? $_GET['id'] ?? 0);
    if ($notifId > 0 && markNotificationAsRead($userid, $notifId, $pdo)) {
        $count = getUnreadNotificationCount($userid, $pdo);
        echo json_encode(['status' => 'success', 'unread_count' => $count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to mark notification as read']);
    }
    exit();
}

if ($action === 'mark_all_read') {
    markAllNotificationsAsRead($userid, $pdo);
    echo json_encode(['status' => 'success', 'unread_count' => 0]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit();
