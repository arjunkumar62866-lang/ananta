<?php
session_start();
require_once 'common/connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['userid'])) {
    echo json_encode([]);
    exit;
}

$userid = $_SESSION['userid'];

$stmt = $pdo->prepare("
    SELECT 
        s.id,
        s.mentor_id,
        u.name as mentor_name,
        s.closing_month,
        s.mentor_monthly_income,
        s.mentor_income_rate,
        s.total_mentor_income,
        s.contribution_percentage,
        s.payout_amount,
        s.status,
        s.credited_at
    FROM tbl_mentor_income_schedule s
    LEFT JOIN user u ON u.userid = s.mentor_id
    WHERE s.direct_user_id = :userid
    ORDER BY s.id DESC
");
$stmt->execute([':userid' => $userid]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
exit;
