<?php
session_start();
require 'common/connection.php';
require_once 'common/db_method.php';
header('Content-Type: application/json');

try {
    $userid = $_SESSION['userid'] ?? '';
    if (!$userid) {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.investment_id,
            s.source_user_id,
            u.name as source_user_name,
            s.investment_amount,
            s.total_bonus,
            s.installment_amount,
            s.installment_number,
            s.installment_month,
            s.status,
            s.credited_at,
            s.withdrawal_status,
            s.withdrawal_date
        FROM tbl_direct_bonus_schedule s
        LEFT JOIN user u ON u.userid = s.source_user_id
        WHERE s.beneficiary_id = :beneficiary_id
        ORDER BY s.installment_month ASC, s.installment_number ASC
    ");
    $stmt->execute([':beneficiary_id' => $userid]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($schedules);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
