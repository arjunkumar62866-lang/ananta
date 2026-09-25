<?php
require 'common/connection.php'; // Your PDO connection
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

switch ($type) {
    case 'pending_fund_request':
        $stmt = $pdo->prepare("SELECT p.id, p.userid, u.name as username, p.tr_id, p.subject, p.amount, p.mode, p.image, p.remark, p.date, p.time, p.status FROM tbl_payment p LEFT JOIN user u ON p.userid = u.userid WHERE p.status = '0' ORDER BY p.id DESC");
        break;
    case 'fund_request_history':
        $stmt = $pdo->prepare("SELECT p.id, p.userid, u.name as username, p.tr_id, p.subject, p.amount, p.mode, p.image, p.remark, p.date, p.time, p.status FROM tbl_payment p LEFT JOIN user u ON p.userid = u.userid ORDER BY p.id DESC");
        break;
    
    default:
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user ORDER BY id DESC");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
exit;

