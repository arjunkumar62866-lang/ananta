<?php
include 'common/connection.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM tbl_banner WHERE id = :id");
$stmt->execute(['id' => $id]);

echo '<script>alert("Banner deleted successfully"); window.location="manage_banner.php";</script>';
?>
