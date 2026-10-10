<?php
session_start();
require 'common/connection.php';
// require 'common/header.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT id, name, package, package_code, real_fund_usd, bonus_amount_usd, maturity_date, capital_withdrawal_status, date, time
        FROM tbl_roi_one 
        WHERE user_id = :user_id
    ");

    // Bind parameters
    $userid = $_SESSION['userid'];
    
    $stmt->bindParam(':user_id', $userid);

    // Execute query
    $stmt->execute();

    // Fetch results
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output JSON
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
