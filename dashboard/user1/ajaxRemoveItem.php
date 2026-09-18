<?php
include("common/connection.php");

$id = $_POST['id'] ?? null;
if (!$id) {
    echo "Invalid request";
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM tbl_cart WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo "Item Removed";
} catch (PDOException $e) {
    echo "Error removing item";
}
?>
