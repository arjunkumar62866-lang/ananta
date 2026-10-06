<?php
require_once 'dashboard/user1/common/connection.php';
require_once 'dashboard/admin/common/db_method.php';

echo "--- USERS WITH ACTIVE INVESTMENTS IN tbl_roi_one ---\n";
$stmt = $pdo->query("
    SELECT user_id, 
           SUM(real_fund_usd) as total_usd, 
           SUM(package) as total_inr, 
           COUNT(*) as cnt 
    FROM tbl_roi_one 
    WHERE status = '0' 
      AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
    GROUP BY user_id
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    // Check user table columns
    $stmtU = $pdo->prepare("SELECT userid, name, active_investment, total_package, amount, net_balance, deposite_wallet FROM user WHERE userid = :uid");
    $stmtU->execute([':uid' => $u['user_id']]);
    $uRow = $stmtU->fetch(PDO::FETCH_ASSOC);
    echo "User ID: {$u['user_id']} ({$uRow['name']})\n";
    echo "  tbl_roi_one: Active Rows={$u['cnt']}, Total USD=\${$u['total_usd']}, Total INR=₹{$u['total_inr']}\n";
    echo "  user table: active_investment=\${$uRow['active_investment']}, total_package=₹{$uRow['total_package']}, deposite_wallet=\${$uRow['deposite_wallet']}, amount=\${$uRow['amount']}\n";
    echo "---------------------------------------------------------\n";
}

// Also check specifically for $2000 active investment
echo "\n--- CHECK FOR SPECIFIC $2000 INVESTMENT ---\n";
$stmt2k = $pdo->query("
    SELECT r.*, u.name 
    FROM tbl_roi_one r 
    LEFT JOIN user u ON u.userid = r.user_id 
    WHERE (r.real_fund_usd = 2000 OR r.package = 180000)
");
while ($r = $stmt2k->fetch(PDO::FETCH_ASSOC)) {
    echo "ROI ID: {$r['id']}, User: {$r['user_id']} ({$r['name']}), Fund USD: \${$r['real_fund_usd']}, Package INR: ₹{$r['package']}, Status: {$r['status']}, CapStatus: {$r['capital_withdrawal_status']}\n";
}
