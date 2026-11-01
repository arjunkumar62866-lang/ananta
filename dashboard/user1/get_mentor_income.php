<?php
session_start();
require_once 'common/connection.php';
require_once 'common/db_method.php';
header('Content-Type: application/json');

if (!isset($_SESSION['userid'])) {
    echo json_encode([]);
    exit;
}

$userid = $_SESSION['userid'];
$selectedCurrency = getUserCurrency($userid, $pdo);

$stmt = $pdo->prepare("
    SELECT 
        s.id,
        s.mentor_id,
        u.name as mentor_name,
        s.closing_month,
        s.mentor_income_rate,
        s.leg_monthly_business,
        s.total_eligible_legs_business,
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
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
foreach ($rows as $r) {
    $r['leg_monthly_business_fmt'] = formatCurrencyFromINR((float)$r['leg_monthly_business'], $selectedCurrency);
    $r['total_eligible_legs_business_fmt'] = formatCurrencyFromINR((float)$r['total_eligible_legs_business'], $selectedCurrency);
    $r['total_mentor_income_fmt'] = formatCurrencyFromINR((float)$r['total_mentor_income'], $selectedCurrency);
    $r['payout_amount_fmt'] = formatCurrencyFromINR((float)$r['payout_amount'], $selectedCurrency);
    $data[] = $r;
}

echo json_encode($data);
exit;
