<?php
require 'common/connection.php';
header('Content-Type: application/json');
session_start();
$userid = $_SESSION['userid'];

try {
    $stmt = $pdo->prepare("SELECT amount, created_date, time, type,a_status FROM tbl_transaction WHERE subject LIKE :subject AND user_id=:userid");
    $subject = "%Withdrawal Request%";
    $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
