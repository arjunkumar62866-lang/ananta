<?php
require 'common/connection.php'; // Your PDO connection
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

switch ($type) {
    case 'pending_fund_request':
        $stmt = $pdo->prepare("SELECT userid,tr_id,subject,amount,mode,image,date FROM tbl_payment WHERE subject Like '%FUND REQUEST%' AND status='0' ORDER BY id DESC");
        break;
    case 'fund_request_history':
        $stmt = $pdo->prepare("SELECT userid,tr_id,subject,amount,date,status FROM tbl_payment WHERE subject Like '%FUND REQUEST%' ORDER BY id DESC");
        break;
    
    default:
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user ORDER BY id DESC");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
exit;
