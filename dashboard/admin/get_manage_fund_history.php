<?php
require 'common/connection.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

if ($type == 'pin_wallet_amount_history') {
    
    $stmt = $pdo->prepare("
        SELECT *
        FROM tbl_transaction
        WHERE type='Credit' 
          AND status='1' 
          AND subject LIKE '%Amount Add To Wallet%'
        ORDER BY id DESC
    ");
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($transactions as $t) {
        $userStmt = $pdo->prepare("SELECT name FROM user WHERE userid = :userid");
        $userStmt->execute(['userid' => $t['user_id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        $t['name'] = $user['name'] ?? '';
        $result[] = $t;
    }

    echo json_encode($result);
    exit;
}

else if($type == 'fund_transfer_history'){
    $stmt = $pdo->prepare("
        SELECT user_id, subject, amount, type, created_date, time
        FROM tbl_transaction
        WHERE subject LIKE '%Money%'
        ORDER BY id DESC
    ");
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($transactions);
    exit;
}
?>