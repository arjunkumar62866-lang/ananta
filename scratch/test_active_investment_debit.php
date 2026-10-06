<?php
/**
 * Test Suite: Admin Wallet Adjustment — Active Investment Debit Verification
 */
chdir(__DIR__ . '/..');
require_once 'dashboard/user1/common/connection.php';
require_once 'dashboard/admin/common/db_method.php';

echo "=== TEST SUITE: ADMIN ACTIVE INVESTMENT DEBIT VERIFICATION ===\n\n";

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
$testUserA = "TEST_A_" . $suf; // $2,000 single row
$testUserB = "TEST_B_" . $suf; // $0
$testUserC = "TEST_C_" . $suf; // $500
$testUserD = "TEST_D_" . $suf; // $2,000 for exact match & over-debit
$testUserF = "TEST_F_" . $suf; // $1,500 + $500 multi row

$allUsers = [$testUserA, $testUserB, $testUserC, $testUserD, $testUserF];

// Clean up if existed
$pdo->exec("DELETE FROM user WHERE userid IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id IN ('" . implode("','", $allUsers) . "')");

function createTestUserWithInvestment($pdo, $uid, $name, $investments = [], $mainBal = 350.00, $netBal = 750.00) {
    $stmt = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, pin_wallet, amount, net_balance, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            active_investment, total_package, profit_income_wallet
        ) VALUES (
            :uid, :name, '1', '1', '123456', '9999999999', 'Male', 'test@test.com', 'ABCDE1234F', '123456',
            0, 0, '1290', 'Admin', '1290', '1', '0', 'L', 0,
            CURDATE(), 'Plan A', '0', 'Father', :main_bal, :main_bal, :net_bal, :net_bal, 0, CURDATE(), 0, CURDATE(), 1,
            0, 0, 0, CURDATE(), 0, 0, 0, 0, 0, 0,
            '0', '0', '0', '0', 0,
            0.00, :total_pkg_inr, 120.00
        )
    ");
    $totalPkgInr = 0;
    foreach ($investments as $inv) {
        $totalPkgInr += ($inv * 90);
    }
    $stmt->execute([
        ':uid'           => $uid,
        ':name'          => $name,
        ':main_bal'      => $mainBal,
        ':net_bal'       => $netBal,
        ':total_pkg_inr' => $totalPkgInr
    ]);

    foreach ($investments as $invUsd) {
        $stmtRoi = $pdo->prepare("
            INSERT INTO tbl_roi_one 
            (user_id, level, name, package_code, real_fund_usd, bonus_percent_snapshot, bonus_amount_usd, lock_period_months, maturity_date, deduction_percent_snapshot, capital_withdrawal_status, package, percentage, count, amount, totalincome, capping, lock_day, date, time, closingdate, status)
            VALUES
            (:uid, 1, 'Test Package', 'TEST_PKG', :fund_usd, 0.00, 0.00, 48, DATE_ADD(CURDATE(), INTERVAL 48 MONTH), 15.00, 'LOCKED', :pkg_inr, 3.00, 0, 0, 0, :capping, 1460, CURDATE(), CURTIME(), CURDATE(), '0')
        ");
        $stmtRoi->execute([
            ':uid'      => $uid,
            ':fund_usd' => $invUsd,
            ':pkg_inr'  => $invUsd * 90,
            ':capping'  => $invUsd * 90 * 2
        ]);
    }
}

// -------------------------------------------------------------
// TEST CASE 1: Before: Active Investment = $2,000, Debit = $1,112
// Expected: Debit = SUCCESS, Remaining = $888
// -------------------------------------------------------------
echo "--- TEST CASE 1: Active Investment = \$2,000, Debit Request = \$1,112 ---\n";
createTestUserWithInvestment($pdo, $testUserA, "User A", [2000.00], 350.00, 750.00);

$resA = processUniversalAdminWalletAdjustment('AN1290', $testUserA, 'active_investment', 'DEBIT', 1112.00, 'Test debit 1112 from 2000', '', $pdo, 'USD');

assertCondition($resA['status'] === 'success', "1.1 Debit operation succeeds", $resA['message'] ?? '');
assertCondition($resA['new_balance'] == 888.00, "1.2 Return new balance is \$888.00", "Returned: " . ($resA['new_balance'] ?? 'null'));

// Verify in DB
$invDetailsA = getUserActiveInvestmentTotal($testUserA, $pdo);
assertCondition($invDetailsA['total_usd'] == 888.00, "1.3 tbl_roi_one active sum is exactly \$888.00", "Active USD: {$invDetailsA['total_usd']}");

$stmtU = $pdo->prepare("SELECT active_investment, total_package, deposite_wallet, amount, profit_income_wallet FROM user WHERE userid = :uid");
$stmtU->execute([':uid' => $testUserA]);
$uA = $stmtU->fetch(PDO::FETCH_ASSOC);

assertCondition((float)$uA['active_investment'] == 888.00, "1.4 user.active_investment is \$888.00", "Value: {$uA['active_investment']}");
assertCondition((float)$uA['total_package'] == (888 * 90), "1.5 user.total_package is ₹79,920.00", "Value: {$uA['total_package']}");
assertCondition((float)$uA['deposite_wallet'] == 350.00, "1.6 Main Wallet (deposite_wallet) UNTOUCHED at \$350.00", "Value: {$uA['deposite_wallet']}");
assertCondition((float)$uA['amount'] == 750.00, "1.7 Net Balance (amount) UNTOUCHED at \$750.00", "Value: {$uA['amount']}");
assertCondition((float)$uA['profit_income_wallet'] == 120.00, "1.8 Profit Income Wallet UNTOUCHED at \$120.00", "Value: {$uA['profit_income_wallet']}");

// Verify transaction table
$stmtTxn = $pdo->prepare("SELECT * FROM tbl_transaction WHERE user_id = :uid ORDER BY id DESC LIMIT 1");
$stmtTxn->execute([':uid' => $testUserA]);
$txA = $stmtTxn->fetch(PDO::FETCH_ASSOC);
assertCondition($txA && (float)$txA['amount'] == 1112.00 && $txA['type'] === 'Debit', "1.9 Transaction ledger created with \$1,112.00 Debit", $txA['subject'] ?? '');

// -------------------------------------------------------------
// TEST CASE 2: Active Investment = $0, Debit Request = $1,112
// Expected: MUST REJECT
// -------------------------------------------------------------
echo "\n--- TEST CASE 2: Active Investment = \$0, Debit Request = \$1,112 ---\n";
createTestUserWithInvestment($pdo, $testUserB, "User B", [], 300.00, 500.00);

$resB = processUniversalAdminWalletAdjustment('AN1290', $testUserB, 'active_investment', 'DEBIT', 1112.00, 'Test debit from zero', '', $pdo, 'USD');

assertCondition($resB['status'] === 'error', "2.1 Zero balance debit is REJECTED", $resB['message'] ?? '');
assertCondition(strpos($resB['message'], 'Insufficient balance') !== false, "2.2 Correct error message returned", $resB['message'] ?? '');
assertCondition(strpos($resB['message'], 'Current: $0.00') !== false, "2.3 Current balance reported as \$0.00", $resB['message'] ?? '');

// -------------------------------------------------------------
// TEST CASE 3: Active Investment = $500, Debit Request = $1,112
// Expected: MUST REJECT
// -------------------------------------------------------------
echo "\n--- TEST CASE 3: Active Investment = \$500, Debit Request = \$1,112 ---\n";
createTestUserWithInvestment($pdo, $testUserC, "User C", [500.00], 300.00, 500.00);

$resC = processUniversalAdminWalletAdjustment('AN1290', $testUserC, 'active_investment', 'DEBIT', 1112.00, 'Test debit from 500', '', $pdo, 'USD');

assertCondition($resC['status'] === 'error', "3.1 Insufficient balance debit is REJECTED", $resC['message'] ?? '');
assertCondition(strpos($resC['message'], 'Current: $500.00') !== false, "3.2 Current balance correctly reported as \$500.00", $resC['message'] ?? '');

$invDetailsC = getUserActiveInvestmentTotal($testUserC, $pdo);
assertCondition($invDetailsC['total_usd'] == 500.00, "3.3 Active investment preserved at \$500.00 after reject", "Total USD: {$invDetailsC['total_usd']}");

// -------------------------------------------------------------
// TEST CASE 4: Active Investment = $2,000, Debit Request = $2,000
// Expected: MUST ALLOW, Remaining = $0.00
// -------------------------------------------------------------
echo "\n--- TEST CASE 4: Active Investment = \$2,000, Debit Request = \$2,000 ---\n";
createTestUserWithInvestment($pdo, $testUserD, "User D", [2000.00], 300.00, 500.00);

$resD = processUniversalAdminWalletAdjustment('AN1290', $testUserD, 'active_investment', 'DEBIT', 2000.00, 'Test exact full debit', '', $pdo, 'USD');

assertCondition($resD['status'] === 'success', "4.1 Exact full debit is ALLOWED", $resD['message'] ?? '');
assertCondition($resD['new_balance'] == 0.00, "4.2 Remaining balance is \$0.00", "New bal: " . ($resD['new_balance'] ?? 'null'));

$invDetailsD = getUserActiveInvestmentTotal($testUserD, $pdo);
assertCondition($invDetailsD['total_usd'] == 0.00, "4.3 Active investment in tbl_roi_one is \$0.00", "Total USD: {$invDetailsD['total_usd']}");

// -------------------------------------------------------------
// TEST CASE 5: Active Investment = $2,000, Debit Request = $2,001
// Expected: MUST REJECT
// -------------------------------------------------------------
echo "\n--- TEST CASE 5: Active Investment = \$2,000, Debit Request = \$2,001 ---\n";
$testUserE = "TEST_E_" . $suf;
$allUsers[] = $testUserE;
createTestUserWithInvestment($pdo, $testUserE, "User E", [2000.00], 300.00, 500.00);

$resE = processUniversalAdminWalletAdjustment('AN1290', $testUserE, 'active_investment', 'DEBIT', 2001.00, 'Test over-debit by 1 dollar', '', $pdo, 'USD');

assertCondition($resE['status'] === 'error', "5.1 Over-debit \$2,001 is REJECTED", $resE['message'] ?? '');
assertCondition(strpos($resE['message'], 'Current: $2,000.00') !== false, "5.2 Current balance correctly reported as \$2,000.00", $resE['message'] ?? '');

// -------------------------------------------------------------
// TEST CASE 6: Multi-Row Investment ($1,500 + $500), Debit = $1,112
// Expected: Row 1 reduced by $1,112 to $388, Row 2 remains $500, Total = $888
// -------------------------------------------------------------
echo "\n--- TEST CASE 6: Multi-Row Investment (\$1,500 + \$500), Debit = \$1,112 ---\n";
createTestUserWithInvestment($pdo, $testUserF, "User F", [1500.00, 500.00], 100.00, 200.00);

$resF = processUniversalAdminWalletAdjustment('AN1290', $testUserF, 'active_investment', 'DEBIT', 1112.00, 'Multi-row debit test', '', $pdo, 'USD');

assertCondition($resF['status'] === 'success', "6.1 Multi-row debit is SUCCESS", $resF['message'] ?? '');
$invDetailsF = getUserActiveInvestmentTotal($testUserF, $pdo);
assertCondition($invDetailsF['total_usd'] == 888.00, "6.2 Multi-row remaining active investment is \$888.00", "Total USD: {$invDetailsF['total_usd']}");

// Clean up
$pdo->exec("DELETE FROM user WHERE userid IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ('" . implode("','", $allUsers) . "')");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id IN ('" . implode("','", $allUsers) . "')");

echo "\n=======================================================\n";
echo "SUMMARY: Passed: {$passCount}, Failed: {$failCount}\n";
echo "=======================================================\n";
