<?php
require 'common/connection.php';
header('Content-Type: application/json');
session_start();


try {
    $stmt = $pdo->prepare("SELECT userid, sub, status, or_date FROM tbl_query WHERE userid = :userid");
    $userid = $_SESSION['userid']; 
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
