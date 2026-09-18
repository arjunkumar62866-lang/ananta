<?php
include 'common/connection.php'; // PDO connection

try {
    $stmt = $pdo->prepare("SELECT id, tr_id, userid, name, mobile, date, time, amount, ac_status 
                           FROM tbl_order 
                           WHERE ac_status = '0' AND status = '1' 
                           ORDER BY id DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($orders);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
