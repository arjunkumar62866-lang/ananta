<?php
require 'common/connection.php';
require 'common/db_method.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

switch ((string)$type) {
    case '1':
        // Approved Withdrawals
        $stmt = $pdo->prepare("
            SELECT id, user_id, subject, act_amount, amount, type, a_status, status, withdrawal_method, created_date, api_txn_no
            FROM tbl_transaction
            WHERE (subject LIKE '%Withdrawal Request%' OR subject LIKE '%Withdrawal%')
              AND subject NOT LIKE '%Investment%'
              AND (a_status = '1' OR status = 1)
            ORDER BY id DESC
        ");
        break;
    case '3':
        // Investment Withdrawal Requests
        $stmt = $pdo->prepare("
            SELECT id, user_id, subject, act_amount, amount, type, a_status, status, withdrawal_method, created_date, api_txn_no
            FROM tbl_transaction
            WHERE (subject LIKE '%Investment Withdrawal%')
              AND (a_status = '0' OR a_status IS NULL OR status = 0)
            ORDER BY id DESC
        ");
        break;
    case '0':
        // Pending Net Balance Withdrawals
        $stmt = $pdo->prepare("
            SELECT id, user_id, subject, act_amount, amount, type, a_status, status, withdrawal_method, created_date, api_txn_no
            FROM tbl_transaction
            WHERE (subject LIKE '%Withdrawal Request%' OR subject LIKE '%Withdrawal%')
              AND subject NOT LIKE '%Investment%'
              AND (a_status = '0' OR a_status IS NULL OR status = 0)
            ORDER BY id DESC
        ");
        break;
    case '2':
        // Rejected / Cancelled Withdrawals
        $stmt = $pdo->prepare("
            SELECT id, user_id, subject, act_amount, amount, type, a_status, status, withdrawal_method, created_date, api_txn_no
            FROM tbl_transaction
            WHERE (subject LIKE '%Cancel Withdrawal%' OR a_status = '2' OR status = 2)
            ORDER BY id DESC
        ");
        break;
    default:
        // All Net Balance Withdrawals
        $stmt = $pdo->prepare("
            SELECT id, user_id, subject, act_amount, amount, type, a_status, status, withdrawal_method, created_date, api_txn_no
            FROM tbl_transaction
            WHERE (subject LIKE '%Withdrawal Request%' OR subject LIKE '%Withdrawal%')
              AND subject NOT LIKE '%Investment%'
              AND type = 'Debit'
            ORDER BY id DESC
        ");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as &$user) {
    // Fetch user row for name and bep20_address
    $uStmt = $pdo->prepare("SELECT name, bep20_address FROM user WHERE userid = :uid LIMIT 1");
    $uStmt->execute([':uid' => $user['user_id']]);
    $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);

    $user['name'] = $uRow['name'] ?? '';
    $userBep20   = $uRow['bep20_address'] ?? '';

    // Fetch KYC data
    $kycStmt = $pdo->prepare("SELECT holder_name, ac_number, ifsc, bit_coin FROM kyc WHERE userid = :userid LIMIT 1");
    $kycStmt->execute(['userid' => $user['user_id']]);
    $kycData = $kycStmt->fetch(PDO::FETCH_ASSOC);

    $user['holder_name'] = $kycData['holder_name'] ?? '';
    $user['ac_number']   = $kycData['ac_number'] ?? '';
    $user['ifsc']        = $kycData['ifsc'] ?? '';

    // Wallet address (use BEP20 address if available)
    $user['bit_coin']    = !empty($kycData['bit_coin']) ? $kycData['bit_coin'] : $userBep20;
    if (empty($user['withdrawal_method'])) {
        $user['withdrawal_method'] = (!empty($user['bit_coin']) && empty($user['ac_number'])) ? 'BEP20' : 'INR';
    }
}

echo json_encode($users);
exit;
