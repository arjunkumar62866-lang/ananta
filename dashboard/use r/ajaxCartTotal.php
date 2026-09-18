<?php
include("common/connection.php");

$userId = $_POST['userId'] ?? 0;

$stmt = $pdo->prepare("
    SELECT SUM(p.price * c.quantity) AS total 
    FROM tbl_cart c 
    JOIN tbl_product p ON c.product_id = p.id
    WHERE c.user_id = :uid
");
$stmt->execute([':uid' => $userId]);
$total = $stmt->fetchColumn();

echo $total ?: 0;
?>
