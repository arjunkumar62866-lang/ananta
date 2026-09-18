<?php
header('Content-Type: application/json');

include("common/connection.php"); 

try {
    $sql = "SELECT id,sub_status, status, name, image, status 
            FROM tbl_category 
            ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
