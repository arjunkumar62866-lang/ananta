<?php
session_start();
include("common/connection.php"); // PDO connection

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$userid = $_SESSION['userid'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

try {
    // Check if product is already in cart
    $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid AND product_id = :product_id");
    $stmt->execute([':userid' => $userid, ':product_id' => $product_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        // Update quantity if already in cart
        $newQty = $cartItem['qty'] + $qty;
        $update = $pdo->prepare("UPDATE tbl_cart SET qty = :qty WHERE id = :id");
        $update->execute([':qty' => $newQty, ':id' => $cartItem['id']]);
    } else {
        // Insert new row
        $insert = $pdo->prepare("INSERT INTO tbl_cart (userid, product_id, qty) VALUES (:userid, :product_id, :qty)");
        $insert->execute([':userid' => $userid, ':product_id' => $product_id, ':qty' => $qty]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
