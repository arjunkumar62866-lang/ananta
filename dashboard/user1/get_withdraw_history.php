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
        SELECT id, amount, created_date, time, type, a_status, act_amount, admin_remarks, withdrawal_method, subject
        FROM tbl_transaction 
        WHERE (subject LIKE '%Withdrawal%' OR withdrawal_method IS NOT NULL) 
          AND user_id = :userid 
        ORDER BY id DESC
    ");
    $stmt->execute([':userid' => $userid]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedRows = [];
    foreach ($rows as $r) {
        $rawAct = isset($r['act_amount']) && $r['act_amount'] !== null && $r['act_amount'] !== '' ? (float)$r['act_amount'] : null;
        $rawAmt = isset($r['amount']) && $r['amount'] !== null && $r['amount'] !== '' ? (float)$r['amount'] : 0.0;

        // Base detection: In tbl_transaction, withdrawal amounts are base USD.
        // Exception: Rare legacy test records where amount > 500 were entered directly in INR.
        $isLegacyInr = ($rawAmt > 500.0) || ($rawAct !== null && $rawAct > 500.0);

        if ($isLegacyInr) {
            $grossInr = ($rawAct !== null && $rawAct > 0) ? $rawAct : $rawAmt;
            $netInr   = $rawAmt;
            $grossUsd = round($grossInr / $rate, 2);
            $netUsd   = round($netInr / $rate, 2);
        } else {
            $grossUsd = ($rawAct !== null && $rawAct > 0) ? $rawAct : $rawAmt;
            $netUsd   = $rawAmt;
            $grossInr = round($grossUsd * $rate, 2);
            $netInr   = round($netUsd * $rate, 2);
        }

        $chargeUsd = max(0, round($grossUsd - $netUsd, 2));
        $chargeInr = max(0, round($grossInr - $netInr, 2));

        if ($currency === 'INR') {
            $displayGross = $grossInr;
            $displayCharge = $chargeInr;
            $displayNet = $netInr;
            $locale = 'en-IN';
        } else {
            $displayGross = $grossUsd;
            $displayCharge = $chargeUsd;
            $displayNet = $netUsd;
            $locale = 'en-US';
        }

        $r['currency_code']       = $currency;
        $r['currency_symbol']     = $currSymbol;
        $r['gross_usd']           = $grossUsd;
        $r['net_usd']             = $netUsd;
        $r['charge_usd']          = $chargeUsd;
        $r['gross_inr']           = $grossInr;
        $r['net_inr']             = $netInr;
        $r['charge_inr']          = $chargeInr;
        $r['display_gross']       = $displayGross;
        $r['display_charge']      = $displayCharge;
        $r['display_net']         = $displayNet;
        $r['formatted_gross']     = $currSymbol . number_format($displayGross, 2);
        $r['formatted_charge']    = $currSymbol . number_format($displayCharge, 2);
        $r['formatted_net']       = $currSymbol . number_format($displayNet, 2);

        $formattedRows[] = $r;
    }

    echo json_encode($formattedRows);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
