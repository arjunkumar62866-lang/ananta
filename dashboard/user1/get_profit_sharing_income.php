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

    $currency = !empty($_REQUEST['currency']) 
        ? strtoupper(trim($_REQUEST['currency'])) 
        : (function_exists('getUserCurrency') ? getUserCurrency($userid, $pdo) : ($_SESSION['currency'] ?? 'USD'));
    $currSymbol = function_exists('getCurrencySymbol') ? getCurrencySymbol($currency) : ($currency === 'INR' ? '₹' : '$');

    $stmt = $pdo->prepare("
        SELECT amount, created_date, time, type, subject, level_num, closing_month, source_investment_id
        FROM tbl_daily_levelinc 
        WHERE subject LIKE :subject 
        AND user_id = :user_id
        ORDER BY id DESC
    ");

    $subject = "%Profit Sharing Income%";
    $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userid);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$r) {
        $amtInr = (float)($r['amount'] ?? 0);
        $dispAmt = function_exists('convertCurrencyFromINR') 
            ? convertCurrencyFromINR($amtInr, $currency, $pdo) 
            : ($currency === 'USD' ? round($amtInr / 90.0, 2) : $amtInr);

        $r['currency_code']   = $currency;
        $r['currency_symbol'] = $currSymbol;
        $r['display_amount']  = $dispAmt;
        $r['formatted_amount'] = $currSymbol . ' ' . number_format($dispAmt, 2);
    }
    unset($r);

    echo json_encode($rows);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
