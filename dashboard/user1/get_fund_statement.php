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

$fromDate = $_GET['from_date'] ?? null;
$toDate = $_GET['to_date'] ?? null;

$data = getUserFundStatementData($userid, $fromDate, $toDate, $pdo);
echo json_encode(['status' => 'success', 'data' => $data]);
exit;
