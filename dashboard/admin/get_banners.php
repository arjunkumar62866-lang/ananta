<?php
include 'common/connection.php';

$stmt = $pdo->prepare("SELECT id, img, status FROM tbl_banner ORDER BY id DESC");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
