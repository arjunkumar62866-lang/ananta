<?php
/**
 * Test Suite: Admin Wallet Adjustment — All Withdrawal & Income Wallets Debit Verification
 */
chdir(__DIR__ . '/..');
require_once 'dashboard/user1/common/connection.php';
require_once 'dashboard/admin/common/db_method.php';

echo "=== TEST SUITE: ADMIN ALL WITHDRAWAL DEBIT VERIFICATION ===\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition($cond, $title, $details = '') {
    global $passCount, $failCount;
    if ($cond) {
        $passCount++;
        echo "✅ PASS: {$title}" . ($details ? " ({$details})" : "") . "\n";
    } else {
        $failCount++;
        echo "❌ FAIL: {$title}" . ($details ? " ({$details})" : "") . "\n";
    }
}

$suf = substr((string)time(), -4);
$testUserW1 = "TEST_W1_" . $suf;
$testUserW2 = "TEST_W2_" . $suf;
$testUserW3 = "TEST_W3_" . $suf;
$testUserW4 = "TEST_W4_" . $suf;
$testUserPI = "TEST_PI_" . $suf;

$allUsers = [$testUserW1, $testUserW2, $testUserW3, $testUserW4, $testUserPI];

$pdo->exec("DELETE FROM user WHERE userid IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_roiinc WHERE user_id IN ('" . implode("','", $allUsers) . "')");

function createTestUser($pdo, $uid, $name, $mainBal = 500.00, $netBal = 1000.00) {
    $stmt = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, pin_wallet, amount, net_balance, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            active_investment, total_package, total_withdrawal, profit_income_wallet
        ) VALUES (
            :uid, :name, '1', '1', '123456', '9999999999', 'Male', 'test@test.com', 'ABCDE1234F', '123456',
            0, 0, '1290', 'Admin', '1290', '1', '0', 'L', 0,
            CURDATE(), 'Plan A', '0', 'Father', :main_bal, :main_bal, :net_bal, :net_bal, 0, CURDATE(), 0, CURDATE(), 1,
            0, 0, 0, CURDATE(), 0, 0, 0, 0, 0, 0,
            '0', '0', '0', '0', 0,
            0.00, 0, 0.00, 0.00
        )
    ");
    $stmt->execute([
        ':uid'      => $uid,
        ':name'     => $name,
        ':main_bal' => $mainBal,
        ':net_bal'  => $netBal
    ]);
}

// -------------------------------------------------------------
// TEST CASE 1: All Withdrawal = $124,700 in tbl_transaction, Debit = $124,700
// Expected: SUCCESS, Remaining = $0.00
// -------------------------------------------------------------
echo "--- TEST CASE 1: All Withdrawal = \$124,700.00, Debit Request = \$124,700.00 ---\n";
createTestUser($pdo, $testUserW1, "User W1", 500.00, 1000.00);

// Populate tbl_transaction mimicking production:
// 1. $250 withdrawal
// 2. $250 withdrawal
// 3. $124,200 investment withdrawal
$insTxn = $pdo->prepare("INSERT INTO tbl_transaction (user_id, amount, type, subject, status, a_status, created_date, time) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())");
$insTxn->execute([$testUserW1, 250.00, 'Debit', 'Withdrawal Request (INR) - $250.00', 1, 1]);
$insTxn->execute([$testUserW1, 250.00, 'Debit', 'Withdrawal Request (BEP20) - $250.00', 1, 1]);
$insTxn->execute([$testUserW1, 124200.00, 'Debit', 'Investment Withdrawal Request', 1, 0]);

$resW1 = processUniversalAdminWalletAdjustment('AN1290', $testUserW1, 'total_withdrawal', 'DEBIT', 124700.00, 'Clear withdrawal testing', '', $pdo, 'USD');

assertCondition($resW1['status'] === 'success', "1.1 Debit operation succeeds", $resW1['message'] ?? '');
assertCondition($resW1['new_balance'] == 0.00, "1.2 Return new balance is \$0.00", "Returned: " . ($resW1['new_balance'] ?? 'null'));

// Check user.total_withdrawal
$stmtU = $pdo->prepare("SELECT total_withdrawal, deposite_wallet, amount FROM user WHERE userid = :uid");
$stmtU->execute([':uid' => $testUserW1]);
$uW1 = $stmtU->fetch(PDO::FETCH_ASSOC);
assertCondition((float)$uW1['total_withdrawal'] == 0.00, "1.3 user.total_withdrawal is \$0.00", "Value: {$uW1['total_withdrawal']}");
assertCondition((float)$uW1['deposite_wallet'] == 500.00, "1.4 Main Wallet UNTOUCHED at \$500.00", "Value: {$uW1['deposite_wallet']}");
assertCondition((float)$uW1['amount'] == 1000.00, "1.5 Net Balance UNTOUCHED at \$1,000.00", "Value: {$uW1['amount']}");

// Check recalculation query in user_profile.php
$stmtWdCheck = $pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE user_id = :uid AND (subject LIKE '%Withdraw%' OR type = 'Withdrawal Request') AND status != 2 AND subject NOT LIKE 'Admin Adjustment%'");
$stmtWdCheck->execute([':uid' => $testUserW1]);
$remWdTxns = (float)$stmtWdCheck->fetchColumn();
assertCondition($remWdTxns == 0.00, "1.6 user_profile.php query returns \$0.00 after debit", "Remaining: \${$remWdTxns}");

// -------------------------------------------------------------
// TEST CASE 2: All Withdrawal = $0.00, Debit Request = $1,000.00
// Expected: MUST REJECT
// -------------------------------------------------------------
echo "\n--- TEST CASE 2: All Withdrawal = \$0.00, Debit Request = \$1,000.00 ---\n";
createTestUser($pdo, $testUserW2, "User W2", 500.00, 1000.00);

$resW2 = processUniversalAdminWalletAdjustment('AN1290', $testUserW2, 'total_withdrawal', 'DEBIT', 1000.00, 'Test debit zero withdrawal', '', $pdo, 'USD');

assertCondition($resW2['status'] === 'error', "2.1 Zero balance debit is REJECTED", $resW2['message'] ?? '');
assertCondition(strpos($resW2['message'], 'Current: $0.00') !== false, "2.2 Error reports Current: \$0.00", $resW2['message'] ?? '');

// -------------------------------------------------------------
// TEST CASE 3: All Withdrawal = $500.00, Debit Request = $1,000.00
// Expected: MUST REJECT
// -------------------------------------------------------------
echo "\n--- TEST CASE 3: All Withdrawal = \$500.00, Debit Request = \$1,000.00 ---\n";
createTestUser($pdo, $testUserW3, "User W3", 500.00, 1000.00);
$insTxn->execute([$testUserW3, 500.00, 'Debit', 'Withdrawal Request - $500.00', 1, 1]);

$resW3 = processUniversalAdminWalletAdjustment('AN1290', $testUserW3, 'total_withdrawal', 'DEBIT', 1000.00, 'Test over debit', '', $pdo, 'USD');

assertCondition($resW3['status'] === 'error', "3.1 Insufficient balance debit is REJECTED", $resW3['message'] ?? '');
assertCondition(strpos($resW3['message'], 'Current: $500.00') !== false, "3.2 Error reports Current: \$500.00", $resW3['message'] ?? '');

// -------------------------------------------------------------
// TEST CASE 4: Partial Debit: Balance = $10,000.00, Debit = $4,000.00
// Expected: SUCCESS, Remaining = $6,000.00
// -------------------------------------------------------------
echo "\n--- TEST CASE 4: Balance = \$10,000.00, Debit Request = \$4,000.00 ---\n";
createTestUser($pdo, $testUserW4, "User W4", 500.00, 1000.00);
$insTxn->execute([$testUserW4, 10000.00, 'Debit', 'Withdrawal Request - $10,000.00', 1, 1]);

$resW4 = processUniversalAdminWalletAdjustment('AN1290', $testUserW4, 'total_withdrawal', 'DEBIT', 4000.00, 'Partial withdrawal debit', '', $pdo, 'USD');

assertCondition($resW4['status'] === 'success', "4.1 Partial debit succeeds", $resW4['message'] ?? '');
assertCondition($resW4['new_balance'] == 6000.00, "4.2 Remaining balance is \$6,000.00", "Returned: " . ($resW4['new_balance'] ?? 'null'));

$stmtWdCheck4 = $pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE user_id = :uid AND (subject LIKE '%Withdraw%' OR type = 'Withdrawal Request') AND status != 2 AND subject NOT LIKE 'Admin Adjustment%'");
$stmtWdCheck4->execute([':uid' => $testUserW4]);
$remWdTxns4 = (float)$stmtWdCheck4->fetchColumn();
assertCondition($remWdTxns4 == 6000.00, "4.3 user_profile.php query returns \$6,000.00", "Remaining: \${$remWdTxns4}");

// -------------------------------------------------------------
// TEST CASE 5: Income Wallet Fallback: Profit Income = $500 in tbl_roiinc
// user.profit_income_wallet = 0.00, Debit = $200.00 -> Remaining $300.00
// -------------------------------------------------------------
echo "\n--- TEST CASE 5: Income Wallet Fallback (Profit Income = \$500 in tbl_roiinc) ---\n";
createTestUser($pdo, $testUserPI, "User PI", 500.00, 1000.00);
$pdo->prepare("INSERT INTO tbl_roiinc (name, user_id, type, subject, product_code, time, created_date, status, amount, act_amount, wallet_type, a_status, beneficiary_id, api_status, api_txn_no, api_bank_ref_no, api_message) VALUES ('Test User', ?, 'Credit', 'ROI Daily', 'BASIC', CURTIME(), CURDATE(), 1, 500.00, 500.00, 'ROI', 1, '', '', '', '', '')")->execute([$testUserPI]);

$resPI = processUniversalAdminWalletAdjustment('AN1290', $testUserPI, 'profit_income_wallet', 'DEBIT', 200.00, 'Profit income debit test', '', $pdo, 'USD');

assertCondition($resPI['status'] === 'success', "5.1 Profit Income debit succeeds reading \$500 from tbl_roiinc", $resPI['message'] ?? '');
assertCondition($resPI['new_balance'] == 300.00, "5.2 Remaining Profit Income is \$300.00", "Returned: " . ($resPI['new_balance'] ?? 'null'));

// Over debit test on Profit Income: Balance $300, Debit $400 -> Reject
$resPIOver = processUniversalAdminWalletAdjustment('AN1290', $testUserPI, 'profit_income_wallet', 'DEBIT', 400.00, 'Profit income over-debit test', '', $pdo, 'USD');
assertCondition($resPIOver['status'] === 'error', "5.3 Over-debit on Profit Income is REJECTED", $resPIOver['message'] ?? '');

// Clean up
$pdo->exec("DELETE FROM user WHERE userid IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_roiinc WHERE user_id IN ('" . implode("','", $allUsers) . "')");

echo "\n=======================================================\n";
echo "SUMMARY: Passed: {$passCount}, Failed: {$failCount}\n";
echo "=======================================================\n";
