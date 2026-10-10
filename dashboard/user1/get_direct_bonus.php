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

    $currency = function_exists('getUserCurrency') ? getUserCurrency($userid, $pdo) : ($_SESSION['currency'] ?? 'USD');
    $currSymbol = function_exists('getCurrencySymbol') ? getCurrencySymbol($currency) : ($currency === 'INR' ? '₹' : '$');

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

    foreach ($schedules as &$sch) {
        $invInr  = (float)($sch['investment_amount'] ?? 0);
        $totInr  = (float)($sch['total_bonus'] ?? 0);
        $instInr = (float)($sch['installment_amount'] ?? 0);

        $sch['currency_code']   = $currency;
        $sch['currency_symbol'] = $currSymbol;

        $dispInv  = function_exists('convertCurrencyFromINR') ? convertCurrencyFromINR($invInr, $currency, $pdo) : ($currency === 'USD' ? round($invInr / 90.0, 2) : $invInr);
        $dispTot  = function_exists('convertCurrencyFromINR') ? convertCurrencyFromINR($totInr, $currency, $pdo) : ($currency === 'USD' ? round($totInr / 90.0, 2) : $totInr);
        $dispInst = function_exists('convertCurrencyFromINR') ? convertCurrencyFromINR($instInr, $currency, $pdo) : ($currency === 'USD' ? round($instInr / 90.0, 2) : $instInr);

        $sch['display_investment_amount']  = $dispInv;
        $sch['display_total_bonus']        = $dispTot;
        $sch['display_installment_amount'] = $dispInst;

        $sch['formatted_investment_amount']  = $currSymbol . ' ' . number_format($dispInv, 2);
        $sch['formatted_total_bonus']        = $currSymbol . ' ' . number_format($dispTot, 2);
        $sch['formatted_installment_amount'] = $currSymbol . ' ' . number_format($dispInst, 2);
    }
    unset($sch);

    echo json_encode($schedules);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
