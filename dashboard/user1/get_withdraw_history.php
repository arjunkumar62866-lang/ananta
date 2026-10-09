<?php
require 'common/connection.php';
header('Content-Type: application/json');
session_start();
$userid = $_SESSION['userid'];

try {
    $stmt = $pdo->prepare("SELECT id, amount, created_date, time, type, a_status, act_amount, admin_remarks FROM tbl_transaction WHERE (subject LIKE '%Withdrawal%' OR withdrawal_method IS NOT NULL) AND user_id = :userid ORDER BY id DESC");
    $stmt->execute([':userid' => $userid]);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
