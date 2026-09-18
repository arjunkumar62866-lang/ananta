<?php
require 'common/connection.php';
require 'common/db_method.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

switch ($type) {
    case '1':
        $stmt = $pdo->prepare("SELECT id,user_id, subject, act_amount, amount, type,a_status, created_date,api_txn_no FROM tbl_transaction WHERE subject='Withdrawal Request' AND a_status='1' ORDER BY id DESC");
        break;
    case '3':
        $stmt = $pdo->prepare("SELECT id,user_id, subject, act_amount, amount, type,a_status, created_date,api_txn_no FROM tbl_transaction WHERE subject='Investment Withdrawal Request' AND a_status='0' ORDER BY id DESC");
        break;
    case '0':
        $stmt = $pdo->prepare("SELECT id,user_id, subject, act_amount, amount, a_status,type, created_date,api_txn_no FROM tbl_transaction WHERE subject='Withdrawal Request' AND a_status='0' ORDER BY id DESC");
        break;
    case '2':
        $stmt = $pdo->prepare("SELECT id,user_id, subject, act_amount, amount,a_status, type, created_date,api_txn_no FROM tbl_transaction WHERE subject='Cancel Withdrawal' AND a_status='2' ORDER BY id DESC");
        break;
    default:
        $stmt = $pdo->prepare("SELECT id,user_id, subject, act_amount, amount,a_status, type, created_date,api_txn_no FROM tbl_transaction WHERE subject='Withdrawal Request' AND type='Debit' ORDER BY id DESC");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as &$user) {
    // Get user data
    $userdata = getuserdatabyid($user['user_id']);
    $user['name'] = $userdata['name'] ?? '';

    // Get KYC data
    $kycStmt = $pdo->prepare("SELECT holder_name, ac_number, ifsc, bit_coin 
                               FROM kyc 
                               WHERE userid = :userid 
                               LIMIT 1");
    $kycStmt->execute(['userid' => $user['user_id']]);
    $kycData = $kycStmt->fetch(PDO::FETCH_ASSOC);

    // Merge KYC data into result
    $user['holder_name'] = $kycData['holder_name'] ?? '';
    $user['ac_number']   = $kycData['ac_number'] ?? '';
    $user['ifsc']        = $kycData['ifsc'] ?? '';
    $user['bit_coin']    = $kycData['bit_coin'] ?? '';
}


echo json_encode($users);
exit;
?>
