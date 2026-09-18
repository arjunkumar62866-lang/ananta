<?php
require 'common/connection.php';
// include("common/header.php"); 
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

if ($type === 'get_available_pins') {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM pin_list 
        WHERE status = :status AND userid = :userid 
        ORDER BY id DESC
    ");
    $stmt->execute([
        ':status' => '0',
        ':userid' => $userid
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);
    exit;
}

echo json_encode([]);
exit;
