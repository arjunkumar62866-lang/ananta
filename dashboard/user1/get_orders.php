<?php
header('Content-Type: application/json');
include("common/connection.php");
session_start();

$userid = $_SESSION['userid'] ?? null;

if (!$userid) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT tr_id, name, amount, ac_status FROM tbl_order WHERE userid = :userid AND status = '1' ORDER BY id DESC");
    $stmt->execute([':userid' => $userid]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($orders);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
