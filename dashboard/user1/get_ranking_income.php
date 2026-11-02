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
        SELECT id, amount, created_date, time, type, subject
        FROM tbl_transaction 
        WHERE subject LIKE :subject 
          AND user_id = :user_id
        ORDER BY id DESC
    ");

    $subject = "Ranking Income%";
    $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userid);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedRows = [];
    foreach ($rows as $r) {
        $rawUsd = (float)($r['amount'] ?? 0);
        $amtInr = round($rawUsd * $rate, 2);
        $displayAmt = ($currency === 'INR') ? $amtInr : $rawUsd;

        $r['currency_code']      = $currency;
        $r['currency_symbol']    = $currSymbol;
        $r['amount_usd']         = $rawUsd;
        $r['amount_inr']         = $amtInr;
        $r['display_amount']     = $displayAmt;
        $r['formatted_amount']   = $currSymbol . number_format($displayAmt, 2);

        $formattedRows[] = $r;
    }

    echo json_encode($formattedRows);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
