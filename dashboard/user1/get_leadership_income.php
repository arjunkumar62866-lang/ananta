<?php
session_start();
require 'common/connection.php';
// require 'common/header.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT amount, created_date, time, type, subject
        FROM tbl_transaction 
        WHERE (
            subject LIKE :subject1 
            OR subject LIKE :subject2 
            OR subject LIKE :subject3
            OR type IN ('Turnover', 'Company Turnover')
        )
        AND user_id = :user_id
        ORDER BY id DESC
    ");

    // Bind parameters
    $userid = $_SESSION['userid'];
    $sub1 = "%Leadership Income%";
    $sub2 = "%Company Turnover%";
    $sub3 = "%Turnover Share%";
    $stmt->bindParam(':subject1', $sub1, PDO::PARAM_STR);
    $stmt->bindParam(':subject2', $sub2, PDO::PARAM_STR);
    $stmt->bindParam(':subject3', $sub3, PDO::PARAM_STR);
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
