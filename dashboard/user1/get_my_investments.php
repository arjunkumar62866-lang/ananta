<?php
session_start();
require_once 'common/connection.php';
require_once 'common/db_method.php';
header('Content-Type: application/json');

$userid = $_SESSION['userid'] ?? '';
if (!$userid) {
    echo json_encode([]);
    exit;
}

$currency = !empty($_REQUEST['currency']) 
    ? strtoupper(trim($_REQUEST['currency'])) 
    : (function_exists('getUserCurrency') ? getUserCurrency($userid, $pdo) : ($_SESSION['currency'] ?? 'USD'));

$currSymbol = function_exists('getCurrencySymbol') ? getCurrencySymbol($currency) : ($currency === 'INR' ? '₹' : '$');
$rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
if ($rate <= 0) $rate = 90.0;

try {
    $stmt = $pdo->prepare("
        SELECT id, name, package, package_code, real_fund_usd, bonus_amount_usd, maturity_date, capital_withdrawal_status, date, time
        FROM tbl_roi_one 
        WHERE user_id = :user_id
        ORDER BY id DESC
    ");
    $stmt->execute([':user_id' => $userid]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedRows = [];
    foreach ($rows as $r) {
        $realUsd = (float)($r['real_fund_usd'] ?? 0);
        $pkgInr  = (float)($r['package'] ?? 0);

        // If real_fund_usd is present and > 0, base USD is real_fund_usd
        if ($realUsd > 0) {
            $baseUsd = $realUsd;
            $baseInr = round($baseUsd * $rate, 2);
        } else {
            // pkgInr is in INR
            $baseInr = $pkgInr;
            $baseUsd = round($baseInr / $rate, 2);
        }

        $displayAmt = ($currency === 'INR') ? $baseInr : $baseUsd;
        $r['currency_code']      = $currency;
        $r['currency_symbol']    = $currSymbol;
        $r['display_amount']     = $displayAmt;
        $r['formatted_package']  = $currSymbol . number_format($displayAmt, 2);
        $formattedRows[] = $r;
    }

    echo json_encode($formattedRows);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
