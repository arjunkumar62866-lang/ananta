<?php
session_start();
require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';
require_once __DIR__ . '/../user1/common/db_method.php';

header('Content-Type: application/json');

if (!isset($_SESSION['auserid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Admin authentication required.']);
    exit;
}

$adminId         = $_SESSION['auserid'];
$depositId       = $_POST['deposit_id'] ?? $_GET['deposit_id'] ?? null;
$decision        = $_POST['decision'] ?? $_GET['decision'] ?? '';
$rejectionReason = $_POST['rejection_reason'] ?? $_GET['rejection_reason'] ?? '';

if (!$depositId || !$decision) {
    echo json_encode(['status' => 'error', 'message' => 'Deposit ID and decision (APPROVE/REJECT) are required.']);
    exit;
}

$res = processAdminDepositDecision($adminId, $depositId, $decision, $rejectionReason, $pdo);
echo json_encode($res);
exit;
