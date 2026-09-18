<?php
include 'common/connection.php';
session_start();

$user_id = $_SESSION['userid'];

$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.qty, p.title, p.price, p.dp_price, p.pv, p.img 
    FROM tbl_cart c 
    JOIN tbl_product p ON c.product_id = p.id 
    WHERE c.user_id = :uid
");
$stmt->execute(['uid' => $user_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
