<?php
require_once __DIR__ . '/../dashboard/admin/common/connection.php';
$stmt = $pdo->query("SELECT userid, name, amount, net_balance, active_investment, total_withdrawal, total_package, package FROM user ORDER BY id ASC LIMIT 15");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
