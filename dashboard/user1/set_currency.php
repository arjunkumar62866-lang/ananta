<?php
session_start();
require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

$curr = $_REQUEST['currency'] ?? $_REQUEST['curr'] ?? 'USD';
$curr = strtoupper(trim($curr));

$res = setUserCurrency($curr, $_SESSION['userid'] ?? null, $pdo);
$_SESSION['currency'] = $curr;
$_SESSION['selected_currency'] = $curr;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($res);
    exit;
}

$redirect = $_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: " . $redirect);
exit;
