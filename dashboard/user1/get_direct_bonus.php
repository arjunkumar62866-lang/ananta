<?php
session_start();
require 'common/connection.php';
// require 'common/header.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT amount, created_date, time, type, subject
        FROM tbl_transaction 
        WHERE subject LIKE :subject 
        AND user_id = :user_id
    ");

    // Bind parameters
    $userid = $_SESSION['userid'];
    $subject = "Direct Bonus%";
    $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
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
