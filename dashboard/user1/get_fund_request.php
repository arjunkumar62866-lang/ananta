<?php
require 'common/connection.php'; // Your PDO connection
header('Content-Type: application/json');

$userid = $_GET['userid'] ?? null;

if (!$userid) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT userid, tr_id, mode, subject, image, amount, remark, date, time, status FROM tbl_payment WHERE userid = :userid ORDER BY id DESC");
$stmt->execute(['userid' => $userid]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
exit;