<?php
/**
 * Automated Test Suite for Wallet, Balance & Withdrawal System Rules.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "=======================================================\n";
echo " STARTING WALLET & WITHDRAWAL SYSTEM TEST SUITE       \n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "[PASS] {$testName}\n";
        if (!empty($details)) echo "       Details: {$details}\n";
    } else {
        $failCount++;
        echo "[FAIL] {$testName}\n";
        if (!empty($details)) echo "       Details: {$details}\n";
    }
}

try {
    $testUser = 'TEST_USR_WDL_RULES';
    $txnPin = '1234';

    // Cleanup sandbox test user
    $pdo->exec("DELETE FROM tbl_capital_withdrawal_request WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUser}'");

    // Insert sandbox user with $500 available Net Balance (amount), $1000 pin_wallet (Main Wallet), and set Txn Key = '1234'
    $txnHash = password_hash($txnPin, PASSWORD_BCRYPT);
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet, pin_wallet
        ) VALUES (
            :uid, 'Withdrawal Test User', '1', 0, '', '9999999999', 'M', 'wdl@test.com', 'ABCDE1234F', :txn_pass,
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            500.00, 1500.00, 200.00, 100.00, 50.00, 0.00, 0.00, 450000.00
        )
    ");
    $stmtUser->execute([':uid' => $testUser, ':txn_pass' => $txnHash]);

    // 1. Main Wallet separation verification
    $u1 = $pdo->query("SELECT pin_wallet, amount FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    assertTest((float)$u1['pin_wallet'] == 450000.00 && (float)$u1['amount'] == 500.00, "1. Main Wallet is separate from Net Balance", "Main: ₹{$u1['pin_wallet']} | Net: \${$u1['amount']}");

    // 2. Net Balance vs User Growth separation
    $u2 = $pdo->query("SELECT amount, profit_income_wallet FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    assertTest((float)$u2['amount'] == 500.00 && (float)$u2['profit_income_wallet'] == 1500.00, "2. Net Balance is separate from User Growth history", "Net Bal: \${$u2['amount']} | User Growth: \${$u2['profit_income_wallet']}");

    // 3. Missing Transaction Key blocks withdrawal
    $res3 = processUserWithdrawalRequest($testUser, 'INR', 100.00, '', $pdo);
    assertTest($res3['status'] === 'error' && strpos($res3['message'], 'Transaction Key') !== false, "3. Missing Transaction Key blocks Net Balance withdrawal", $res3['message']);

    // 4. Invalid Transaction Key blocks withdrawal
    $res4 = processUserWithdrawalRequest($testUser, 'INR', 100.00, '9999', $pdo);
    assertTest($res4['status'] === 'error' && strpos($res4['message'], 'Invalid') !== false, "4. Invalid Transaction Key blocks Net Balance withdrawal", $res4['message']);

    // 5. Valid Transaction Key allows withdrawal
    $res5 = processUserWithdrawalRequest($testUser, 'INR', 100.00, '1234', $pdo);
    assertTest($res5['status'] === 'success', "5. Valid Transaction Key allows Net Balance withdrawal", $res5['message']);

    // 6. Balance deducted after withdrawal
    $u6 = $pdo->query("SELECT amount FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    assertTest((float)$u6['amount'] == 400.00, "6. Net Balance deducted after successful withdrawal", "New Bal: \${$u6['amount']}");

    // 7. Over-balance withdrawal blocked
    $res7 = processUserWithdrawalRequest($testUser, 'INR', 500.00, '1234', $pdo);
    assertTest($res7['status'] === 'error' && strpos($res7['message'], 'Insufficient') !== false, "7. Over-balance withdrawal rejected server-side", $res7['message']);

    // 8. Capital Withdrawal pre-maturity locked
    $inv8 = processAnantaPackageInvestment($testUser, 'BASIC', 500.00, $pdo);
    $res8 = processCapitalWithdrawal($testUser, $inv8['investment_id'], '1234', $pdo);
    assertTest($res8['status'] === 'error' && strpos($res8['message'], 'locked') !== false, "8. Capital Withdrawal pre-maturity locked server-side", $res8['message']);

    // 9. Capital Withdrawal post-maturity allowed with 15% deduction
    $pastDate = date('Y-m-d', strtotime('-1 day'));
    $pdo->exec("UPDATE tbl_roi_one SET maturity_date = '{$pastDate}' WHERE id = {$inv8['investment_id']}");
    $res9 = processCapitalWithdrawal($testUser, $inv8['investment_id'], '1234', $pdo);
    assertTest($res9['status'] === 'success', "9. Capital Withdrawal post-maturity allowed", $res9['message']);

    // 10. 15% Deduction verified ($500 - $75 = $425)
    assertTest($res9['deduction_amount_usd'] == 75.00 && $res9['net_withdrawal_usd'] == 425.00, "10. 15% Deduction correctly calculated on Capital Withdrawal", "Deduction: \${$res9['deduction_amount_usd']} | Net: \${$res9['net_withdrawal_usd']}");

    // 11. Duplicate Capital Withdrawal blocked
    $res11 = processCapitalWithdrawal($testUser, $inv8['investment_id'], '1234', $pdo);
    assertTest($res11['status'] === 'error' && strpos($res11['message'], 'ALREADY') !== false, "11. Duplicate Capital Withdrawal blocked", $res11['message']);

    // Cleanup sandbox test user
    $pdo->exec("DELETE FROM tbl_capital_withdrawal_request WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUser}'");

} catch (Exception $e) {
    echo "[EXCEPTIONAL FAIL] " . $e->getMessage() . "\n";
    $failCount++;
}

echo "\n=======================================================\n";
echo " TEST SUITE COMPLETE: PASS = {$passCount} | FAIL = {$failCount} \n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
} else {
    exit(0);
}
