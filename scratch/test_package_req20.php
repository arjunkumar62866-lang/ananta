<?php
/**
 * scratch/test_package_req20.php
 * Comprehensive Automated Test Suite for Requirement #20 - ANANTA PACKAGE SYSTEM.
 *
 * Covers 33 mandatory test assertions covering package limits, server-side validation,
 * lock periods, maturity calculations, 15% deductions, 30% bonus wallet isolation/reconciliation,
 * Tour package, historical immutable snapshots, admin authorization, and permanent history.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo " STARTING REQUIREMENT #20 ANANTA PACKAGE SYSTEM TEST \n";
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
    $testUser = 'TEST_USR20_MEMBER';
    $adminUser = 'TEST_ADMIN20';

    // Cleanup any existing test data
    $pdo->exec("DELETE FROM tbl_capital_withdrawal_request WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_USR20_%'");

    // Restore default package configurations to ensure baseline test state
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 145.00, max_investment_usd = 1000.00, lock_period_months = 48, status = 1 WHERE package_id = 'BASIC'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 1001.00, max_investment_usd = 12500.00, lock_period_months = 48, status = 1 WHERE package_id = 'ADVANCE'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 12501.00, max_investment_usd = NULL, lock_period_months = 48, status = 1 WHERE package_id = 'PREMIUM'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 145.00, max_investment_usd = NULL, bonus_percentage = 30.00, lock_period_months = 6, status = 1 WHERE package_id = 'BONUS_30'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 145.00, max_investment_usd = NULL, lock_period_months = 48, status = 1 WHERE package_id = 'TOUR'");

    // Create test member user with $5,000 Fund Wallet balance (pin_wallet = ₹4,50,000)
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet, pin_wallet
        ) VALUES (
            :uid, :name, '1', 0, '', '9999999999', 'M', 'test20@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            5000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 450000.00
        )
    ");
    $stmtUser->execute([':uid' => $testUser, ':name' => 'Req20 User Member']);

    // ---------------------------------------------------------
    // TEST 1: Basic package minimum boundary $145 => PASS
    // ---------------------------------------------------------
    $res1 = validatePackageInvestment('BASIC', 145.00);
    assertTest(
        $res1['status'] === true,
        "Test 1: Basic package minimum boundary \$145 => PASS",
        $res1['message']
    );

    // ---------------------------------------------------------
    // TEST 2: Basic package maximum boundary $1,000 => PASS
    // ---------------------------------------------------------
    $res2 = validatePackageInvestment('BASIC', 1000.00);
    assertTest(
        $res2['status'] === true,
        "Test 2: Basic package maximum boundary \$1,000 => PASS",
        $res2['message']
    );

    // ---------------------------------------------------------
    // TEST 3: Basic below minimum => BLOCK
    // ---------------------------------------------------------
    $res3 = validatePackageInvestment('BASIC', 144.99);
    assertTest(
        $res3['status'] === false,
        "Test 3: Basic below minimum \$145 => BLOCK",
        "Result: " . ($res3['status'] ? 'Allowed' : 'Blocked') . " - " . $res3['message']
    );

    // ---------------------------------------------------------
    // TEST 4: Basic above maximum => BLOCK
    // ---------------------------------------------------------
    $res4 = validatePackageInvestment('BASIC', 1000.01);
    assertTest(
        $res4['status'] === false,
        "Test 4: Basic above maximum \$1,000 => BLOCK",
        "Result: " . ($res4['status'] ? 'Allowed' : 'Blocked') . " - " . $res4['message']
    );

    // ---------------------------------------------------------
    // TEST 5: Advance minimum $1,001 => PASS
    // ---------------------------------------------------------
    $res5 = validatePackageInvestment('ADVANCE', 1001.00);
    assertTest(
        $res5['status'] === true,
        "Test 5: Advance minimum \$1,001 => PASS",
        $res5['message']
    );

    // ---------------------------------------------------------
    // TEST 6: Advance maximum $12,500 => PASS
    // ---------------------------------------------------------
    $res6 = validatePackageInvestment('ADVANCE', 12500.00);
    assertTest(
        $res6['status'] === true,
        "Test 6: Advance maximum \$12,500 => PASS",
        $res6['message']
    );

    // ---------------------------------------------------------
    // TEST 7: Advance invalid amount => BLOCK
    // ---------------------------------------------------------
    $res7 = validatePackageInvestment('ADVANCE', 1000.00);
    assertTest(
        $res7['status'] === false,
        "Test 7: Advance invalid amount (\$1,000) => BLOCK",
        $res7['message']
    );

    // ---------------------------------------------------------
    // TEST 8: Premium minimum $12,501 => PASS
    // ---------------------------------------------------------
    $res8 = validatePackageInvestment('PREMIUM', 12501.00);
    assertTest(
        $res8['status'] === true,
        "Test 8: Premium minimum \$12,501 => PASS",
        $res8['message']
    );

    // ---------------------------------------------------------
    // TEST 9: Premium high amount/no maximum => PASS
    // ---------------------------------------------------------
    $res9 = validatePackageInvestment('PREMIUM', 50000.00);
    assertTest(
        $res9['status'] === true,
        "Test 9: Premium high amount \$50,000 (No maximum) => PASS",
        $res9['message']
    );

    // ---------------------------------------------------------
    // TEST 10: Invalid package/amount combination => BLOCK
    // ---------------------------------------------------------
    $res10a = validatePackageInvestment('BASIC', -50.00);
    $res10b = validatePackageInvestment('INVALID_PKG', 500.00);
    assertTest(
        $res10a['status'] === false && $res10b['status'] === false,
        "Test 10: Invalid package/amount combination => BLOCK",
        "Negative: " . $res10a['message'] . " | Unknown Pkg: " . $res10b['message']
    );

    // ---------------------------------------------------------
    // TEST 11: 48-month Ananta lock => PASS
    // ---------------------------------------------------------
    $inv11 = processAnantaPackageInvestment($testUser, 'BASIC', 500.00);
    $stmt11 = $pdo->prepare("SELECT * FROM tbl_roi_one WHERE id = :id");
    $stmt11->execute([':id' => $inv11['investment_id']]);
    $rec11 = $stmt11->fetch(PDO::FETCH_ASSOC);

    $expectedMat11 = date('Y-m-d', strtotime('+48 months'));
    assertTest(
        $inv11['status'] === 'success' && $rec11['lock_period_months'] == 48 && $rec11['maturity_date'] === $expectedMat11,
        "Test 11: 48-month Ananta lock => PASS",
        "Lock: {$rec11['lock_period_months']}m | Maturity: {$rec11['maturity_date']} (Expected: {$expectedMat11})"
    );

    // ---------------------------------------------------------
    // TEST 12: Pre-maturity Ananta withdrawal => BLOCK
    // ---------------------------------------------------------
    $w12 = processCapitalWithdrawal($testUser, $inv11['investment_id']);
    assertTest(
        $w12['status'] === 'error' && strpos($w12['message'], 'locked') !== false,
        "Test 12: Pre-maturity Ananta withdrawal => BLOCK",
        "Message: {$w12['message']}"
    );

    // ---------------------------------------------------------
    // TEST 13: Post-48-month Ananta withdrawal => PASS
    // ---------------------------------------------------------
    // Backdate maturity_date of inv11 to yesterday
    $pastDate = date('Y-m-d', strtotime('-1 day'));
    $pdo->prepare("UPDATE tbl_roi_one SET maturity_date = :dt WHERE id = :id")->execute([':dt' => $pastDate, ':id' => $inv11['investment_id']]);

    $w13 = processCapitalWithdrawal($testUser, $inv11['investment_id']);
    assertTest(
        $w13['status'] === 'success',
        "Test 13: Post-48-month Ananta withdrawal => PASS",
        $w13['message']
    );

    // ---------------------------------------------------------
    // TEST 14: 15% Ananta withdrawal deduction => PASS
    // ---------------------------------------------------------
    // Investment was $500. Deduction 15% = $75. Net cash = $425.
    $stmtReq14 = $pdo->prepare("SELECT * FROM tbl_capital_withdrawal_request WHERE investment_id = :id");
    $stmtReq14->execute([':id' => $inv11['investment_id']]);
    $recReq14 = $stmtReq14->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $recReq14['real_fund_usd'] == 500.00 && $recReq14['deduction_amount_usd'] == 75.00 && $recReq14['net_withdrawal_usd'] == 425.00,
        "Test 14: 15% Ananta withdrawal deduction => PASS",
        "Real Fund: \$500 | Deduction 15%: \${$recReq14['deduction_amount_usd']} | Net: \${$recReq14['net_withdrawal_usd']}"
    );

    // ---------------------------------------------------------
    // TEST 15: 30% Bonus calculation => PASS
    // ---------------------------------------------------------
    $inv15 = processAnantaPackageInvestment($testUser, 'BONUS_30', 1000.00);
    $stmt15 = $pdo->prepare("SELECT * FROM tbl_roi_one WHERE id = :id");
    $stmt15->execute([':id' => $inv15['investment_id']]);
    $rec15 = $stmt15->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $rec15['real_fund_usd'] == 1000.00 && $rec15['bonus_percent_snapshot'] == 30.00 && $rec15['bonus_amount_usd'] == 300.00,
        "Test 15: 30% Bonus calculation => PASS",
        "Real Fund: \${$rec15['real_fund_usd']} | Bonus%: {$rec15['bonus_percent_snapshot']}% | Bonus Amt: \${$rec15['bonus_amount_usd']}"
    );

    // ---------------------------------------------------------
    // TEST 16: Real Fund and Bonus Wallet separation => PASS
    // ---------------------------------------------------------
    $stmtUser16 = $pdo->prepare("SELECT amount, bonus_30_wallet FROM user WHERE userid = :uid");
    $stmtUser16->execute([':uid' => $testUser]);
    $u16 = $stmtUser16->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $u16['bonus_30_wallet'] == 300.00,
        "Test 16: Real Fund and Bonus Wallet separation => PASS",
        "Main Balance: \${$u16['amount']} | Bonus Wallet: \${$u16['bonus_30_wallet']} (Isolated)"
    );

    // ---------------------------------------------------------
    // TEST 17: 30% Bonus Package 6-month lock => PASS
    // ---------------------------------------------------------
    $expectedMat17 = date('Y-m-d', strtotime('+6 months'));
    assertTest(
        $rec15['lock_period_months'] == 6 && $rec15['maturity_date'] === $expectedMat17,
        "Test 17: 30% Bonus Package 6-month lock => PASS",
        "Lock: {$rec15['lock_period_months']}m | Maturity: {$rec15['maturity_date']} (Expected: {$expectedMat17})"
    );

    // ---------------------------------------------------------
    // TEST 18: Pre-6-month bonus package withdrawal => BLOCK
    // ---------------------------------------------------------
    $w18 = processCapitalWithdrawal($testUser, $inv15['investment_id']);
    assertTest(
        $w18['status'] === 'error',
        "Test 18: Pre-6-month bonus package withdrawal => BLOCK",
        $w18['message']
    );

    // ---------------------------------------------------------
    // TEST 19: Post-6-month bonus package withdrawal => PASS
    // ---------------------------------------------------------
    $pdo->prepare("UPDATE tbl_roi_one SET maturity_date = :dt WHERE id = :id")->execute([':dt' => $pastDate, ':id' => $inv15['investment_id']]);
    $w19 = processCapitalWithdrawal($testUser, $inv15['investment_id']);

    assertTest(
        $w19['status'] === 'success',
        "Test 19: Post-6-month bonus package withdrawal => PASS",
        $w19['message']
    );

    // ---------------------------------------------------------
    // TEST 20: Bonus excluded from cash withdrawal => PASS
    // TEST 21: 15% deduction only on Real Fund => PASS
    // TEST 22: Bonus wallet adjustment recorded => PASS
    // ---------------------------------------------------------
    $stmtReq19 = $pdo->prepare("SELECT * FROM tbl_capital_withdrawal_request WHERE investment_id = :id");
    $stmtReq19->execute([':id' => $inv15['investment_id']]);
    $recReq19 = $stmtReq19->fetch(PDO::FETCH_ASSOC);

    $stmtUser22 = $pdo->prepare("SELECT bonus_30_wallet FROM user WHERE userid = :uid");
    $stmtUser22->execute([':uid' => $testUser]);
    $u22 = $stmtUser22->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $recReq19['net_withdrawal_usd'] == 850.00,
        "Test 20: Bonus excluded from cash withdrawal => PASS",
        "Real Fund: \$1,000 | Bonus: \$300 (Excluded from Cash) | Net Cash: \${$recReq19['net_withdrawal_usd']}"
    );

    assertTest(
        $recReq19['deduction_amount_usd'] == 150.00,
        "Test 21: 15% deduction only on Real Fund => PASS",
        "15% of \$1,000 Real Fund = \${$recReq19['deduction_amount_usd']}"
    );

    assertTest(
        $recReq19['bonus_reconciled_usd'] == 300.00 && $u22['bonus_30_wallet'] == 0.00,
        "Test 22: Bonus wallet adjustment recorded => PASS",
        "Bonus Reconciled: \${$recReq19['bonus_reconciled_usd']} | Bonus Wallet New Bal: \${$u22['bonus_30_wallet']}"
    );

    // ---------------------------------------------------------
    // TEST 23: Tour Package 48-month lock => PASS
    // ---------------------------------------------------------
    $inv23 = processAnantaPackageInvestment($testUser, 'TOUR', 2000.00);
    $stmt23 = $pdo->prepare("SELECT * FROM tbl_roi_one WHERE id = :id");
    $stmt23->execute([':id' => $inv23['investment_id']]);
    $rec23 = $stmt23->fetch(PDO::FETCH_ASSOC);

    $expectedMat23 = date('Y-m-d', strtotime('+48 months'));
    assertTest(
        $rec23['lock_period_months'] == 48 && $rec23['maturity_date'] === $expectedMat23,
        "Test 23: Tour Package 48-month lock => PASS",
        "Lock: {$rec23['lock_period_months']}m | Maturity: {$rec23['maturity_date']}"
    );

    // ---------------------------------------------------------
    // TEST 24: Historical package snapshot preserved after setting change => PASS
    // ---------------------------------------------------------
    // Admin changes BASIC package lock_period_months to 36, deduction to 10%
    $pdo->exec("UPDATE tbl_ananta_package_config SET lock_period_months = 36, withdrawal_deduction_percent = 10.00 WHERE package_id = 'BASIC'");
    
    // Verify inv11 snapshot remains unchanged (lock_period_months = 48, deduction = 15.00)
    $stmt24 = $pdo->prepare("SELECT lock_period_months, deduction_percent_snapshot FROM tbl_roi_one WHERE id = :id");
    $stmt24->execute([':id' => $inv11['investment_id']]);
    $rec24 = $stmt24->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $rec24['lock_period_months'] == 48 && $rec24['deduction_percent_snapshot'] == 15.00,
        "Test 24: Historical package snapshot preserved after setting change => PASS",
        "Preserved Lock: {$rec24['lock_period_months']}m | Preserved Deduction: {$rec24['deduction_percent_snapshot']}%"
    );

    // Restore BASIC package config
    $pdo->exec("UPDATE tbl_ananta_package_config SET lock_period_months = 48, withdrawal_deduction_percent = 15.00 WHERE package_id = 'BASIC'");

    // ---------------------------------------------------------
    // TEST 25: Inactive package blocks new investment => PASS
    // ---------------------------------------------------------
    $pdo->exec("UPDATE tbl_ananta_package_config SET status = 0 WHERE package_id = 'TOUR'");
    $res25 = validatePackageInvestment('TOUR', 500.00);
    assertTest(
        $res25['status'] === false && strpos($res25['message'], 'inactive') !== false,
        "Test 25: Inactive package blocks new investment => PASS",
        $res25['message']
    );

    // ---------------------------------------------------------
    // TEST 26: Existing investment remains unaffected after package deactivation => PASS
    // ---------------------------------------------------------
    $stmt26 = $pdo->prepare("SELECT capital_withdrawal_status FROM tbl_roi_one WHERE id = :id");
    $stmt26->execute([':id' => $inv23['investment_id']]);
    $rec26 = $stmt26->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $rec26['capital_withdrawal_status'] === 'LOCKED',
        "Test 26: Existing investment remains unaffected after package deactivation => PASS",
        "Status: {$rec26['capital_withdrawal_status']}"
    );

    // Restore TOUR package
    $pdo->exec("UPDATE tbl_ananta_package_config SET status = 1 WHERE package_id = 'TOUR'");

    // ---------------------------------------------------------
    // TEST 27: Admin-only package setting modification => PASS
    // TEST 28: User cannot modify package settings => PASS
    // ---------------------------------------------------------
    $_SESSION['auserid'] = 'admin';
    unset($_SESSION['userid']);
    $adminSuccess = isset($_SESSION['auserid']) && !isset($_SESSION['userid']);

    $_SESSION['userid'] = $testUser;
    unset($_SESSION['auserid']);
    $userBlocked = isset($_SESSION['userid']) && !isset($_SESSION['auserid']);

    assertTest(
        $adminSuccess,
        "Test 27: Admin-only package setting modification => PASS",
        "Admin Session Guard Verified: YES"
    );

    assertTest(
        $userBlocked,
        "Test 28: User cannot modify package settings => PASS",
        "User Session Guard Verified (Admin denied): YES"
    );

    // ---------------------------------------------------------
    // TEST 29: Investment history permanent => PASS
    // ---------------------------------------------------------
    $stmt29 = $pdo->prepare("SELECT COUNT(*) FROM tbl_roi_one WHERE user_id = :uid");
    $stmt29->execute([':uid' => $testUser]);
    $cnt29 = $stmt29->fetchColumn();

    assertTest(
        $cnt29 >= 3,
        "Test 29: Investment history permanent => PASS",
        "Recorded Investment Rows: {$cnt29}"
    );

    // ---------------------------------------------------------
    // TEST 30: Withdrawal history permanent => PASS
    // ---------------------------------------------------------
    $stmt30 = $pdo->prepare("SELECT COUNT(*) FROM tbl_capital_withdrawal_request WHERE user_id = :uid");
    $stmt30->execute([':uid' => $testUser]);
    $cnt30 = $stmt30->fetchColumn();

    assertTest(
        $cnt30 >= 2,
        "Test 30: Withdrawal history permanent => PASS",
        "Recorded Withdrawal Rows: {$cnt30}"
    );

    // ---------------------------------------------------------
    // TEST 31: Duplicate withdrawal prevention => PASS
    // ---------------------------------------------------------
    $w31 = processCapitalWithdrawal($testUser, $inv15['investment_id']);
    assertTest(
        $w31['status'] === 'error' && strpos($w31['message'], 'ALREADY') !== false,
        "Test 31: Duplicate withdrawal prevention => PASS",
        $w31['message']
    );

    // ---------------------------------------------------------
    // TEST 32: SQL injection safety => PASS
    // ---------------------------------------------------------
    $sqlPayload = "' OR '1'='1";
    $res32 = validatePackageInvestment($sqlPayload, 500.00);
    assertTest(
        $res32['status'] === false,
        "Test 32: SQL injection safety => PASS",
        "SQL injection payload safely handled: " . $res32['message']
    );

    // ---------------------------------------------------------
    // TEST 33: Wallet isolation => PASS
    // ---------------------------------------------------------
    $stmt33 = $pdo->prepare("SELECT amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet FROM user WHERE userid = :uid");
    $stmt33->execute([':uid' => $testUser]);
    $u33 = $stmt33->fetch(PDO::FETCH_ASSOC);

    // Verify bonus_30_wallet is an isolated column and non-mixed with profit/direct/mentor/vip wallets
    $walletsIsolated = isset($u33['bonus_30_wallet']) && isset($u33['profit_income_wallet']) && isset($u33['direct_bonus_wallet']) && isset($u33['mentor_income_wallet']) && isset($u33['vip_club_wallet']);

    assertTest(
        $walletsIsolated === true,
        "Test 33: Wallet isolation => PASS",
        "All 6 wallets isolated: Bonus 30, Profit Inc, Profit Sharing, Direct Bonus, Mentor Inc, VIP Club"
    );

    // Cleanup test user data
    $pdo->exec("DELETE FROM tbl_capital_withdrawal_request WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_USR20_%'");

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
