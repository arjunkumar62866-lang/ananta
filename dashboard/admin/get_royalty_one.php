<?php
require 'common/connection.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT userid, amount, created_date, time
        FROM tbl_royalty_user
        WHERE full_status = 0
        ORDER BY id DESC
    ");

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
?>
