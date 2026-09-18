<?php
session_start();
include 'common/connection.php'; // your PDO connection

$userId = $_SESSION['userid'] ?? 0;

if ($userId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$proId  = $_POST['proId'] ?? 0;
$proQty = $_POST['proQty'] ?? 1;

try {
    // Check if product already in cart
    $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userId AND product_id = :proId");
    $stmt->execute([':userId' => $userId, ':proId' => $proId]);

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['status' => 'error', 'message' => 'Product already in cart']);
        exit;
    }

    // Insert into cart
    $stmt = $pdo->prepare("INSERT INTO tbl_cart (userid, product_id, qty) VALUES (:userId, :proId, :qty)");
    $stmt->execute([':userId' => $userId, ':proId' => $proId, ':qty' => $proQty]);

    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
