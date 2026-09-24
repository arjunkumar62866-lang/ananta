<?php
session_start();
require_once 'common/connection.php';
require_once 'common/db_method.php';

header('Content-Type: application/json');

$userid = $_SESSION['userid'] ?? '';
if (!$userid) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$sent = getUserP2PTransferHistory($userid, $pdo);
$received = getUserP2PReceivedReport($userid, $pdo);

echo json_encode([
    'status'   => 'success',
    'sent'     => $sent,
    'received' => $received
]);
exit;
