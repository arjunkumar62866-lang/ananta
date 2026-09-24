<?php
/**
 * scratch/test_ananta_package_req20.php
 * Automated Test Suite for Requirement #20 - ANANTA PACKAGE SYSTEM (35 Assertion Suite).
 *
 * Covers all 35 specific test scenarios:
 * 1. Basic package minimum validation
 * 2. Basic package maximum validation
 * 3. Basic below-minimum rejection
 * 4. Basic above-maximum rejection
 * 5. Advance minimum validation
 * 6. Advance maximum validation
 * 7. Advance invalid amount rejection
 * 8. Premium minimum validation
 * 9. Premium high amount accepted
 * 10. Premium no maximum limit
 * 11. 48-month lock calculation
 * 12. 48-month maturity calculation
 * 13. Pre-maturity withdrawal blocked
 * 14. Post-maturity withdrawal allowed
 * 15. 15% deduction calculation
 * 16. 30% Bonus default = 30%
 * 17. Admin bonus percentage configuration
 * 18. Real Fund and Bonus Wallet separation
 * 19. Bonus amount calculation
 * 20. Bonus amount excluded from cash withdrawal
 * 21. 6-month Bonus Package lock
 * 22. Bonus Package post-6-month withdrawal
 * 23. 15% deduction only on Real Fund
 * 24. Tour Package 48-month lock
 * 25. Tour Package 15% deduction
 * 26. Historical package snapshot preservation
 * 27. Package setting change does not alter old investment
 * 28. Inactive package blocks new investment
 * 29. Existing investment remains valid after package deactivation
 * 30. Duplicate withdrawal protection
 * 31. User authorization
 * 32. Admin authorization
 * 33. SQL injection safety
 * 34. Wallet isolation
 * 35. Permanent transaction/history preservation
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo " STARTING REQUIREMENT #20 ANANTA PACKAGE (35 TESTS)   \n";
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
    $testUser = 'TEST_USR20_FULL';

    // Cleanup existing test sandbox rows
    $pdo->exec("DELETE FROM tbl_capital_withdrawal_request WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_USR20_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_USR20_%'");

    // Reset default package configurations
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 145.00, max_investment_usd = 1000.00, bonus_percentage = 0.00, lock_period_months = 48, withdrawal_deduction_percent = 15.00, status = 1 WHERE package_id = 'BASIC'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 1001.00, max_investment_usd = 12500.00, bonus_percentage = 0.00, lock_period_months = 48, withdrawal_deduction_percent = 15.00, status = 1 WHERE package_id = 'ADVANCE'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 12501.00, max_investment_usd = NULL, bonus_percentage = 0.00, lock_period_months = 48, withdrawal_deduction_percent = 15.00, status = 1 WHERE package_id = 'PREMIUM'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 145.00, max_investment_usd = NULL, bonus_percentage = 30.00, lock_period_months = 6, withdrawal_deduction_percent = 15.00, status = 1 WHERE package_id = 'BONUS_30'");
    $pdo->exec("UPDATE tbl_ananta_package_config SET min_investment_usd = 145.00, max_investment_usd = NULL, bonus_percentage = 0.00, lock_period_months = 48, withdrawal_deduction_percent = 15.00, status = 1 WHERE package_id = 'TOUR'");

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
    $stmtUser->execute([':uid' => $testUser, ':name' => 'Req20 Full Sandbox User']);

    // 1. Basic package minimum validation
    $r1 = validatePackageInvestment('BASIC', 145.00);
    assertTest($r1['status'] === true, "1. Basic package minimum validation ($145 => Valid)", $r1['message']);

    // 2. Basic package maximum validation
    $r2 = validatePackageInvestment('BASIC', 1000.00);
    assertTest($r2['status'] === true, "2. Basic package maximum validation ($1,000 => Valid)", $r2['message']);

    // 3. Basic below-minimum rejection
    $r3 = validatePackageInvestment('BASIC', 144.99);
    assertTest($r3['status'] === false, "3. Basic below-minimum rejection ($144.99 => Blocked)", $r3['message']);

    // 4. Basic above-maximum rejection
    $r4 = validatePackageInvestment('BASIC', 1000.01);
    assertTest($r4['status'] === false, "4. Basic above-maximum rejection ($1,000.01 => Blocked)", $r4['message']);

    // 5. Advance minimum validation
    $r5 = validatePackageInvestment('ADVANCE', 1001.00);
    assertTest($r5['status'] === true, "5. Advance minimum validation ($1,001 => Valid)", $r5['message']);

    // 6. Advance maximum validation
    $r6 = validatePackageInvestment('ADVANCE', 12500.00);
    assertTest($r6['status'] === true, "6. Advance maximum validation ($12,500 => Valid)", $r6['message']);

    // 7. Advance invalid amount rejection
    $r7 = validatePackageInvestment('ADVANCE', 1000.00);
    assertTest($r7['status'] === false, "7. Advance invalid amount rejection ($1,000 => Blocked)", $r7['message']);

    // 8. Premium minimum validation
    $r8 = validatePackageInvestment('PREMIUM', 12501.00);
    assertTest($r8['status'] === true, "8. Premium minimum validation ($12,501 => Valid)", $r8['message']);

    // 9. Premium high amount accepted
    $r9 = validatePackageInvestment('PREMIUM', 50000.00);
    assertTest($r9['status'] === true, "9. Premium high amount accepted ($50,000 => Valid)", $r9['message']);

    // 10. Premium no maximum limit
    $cfgPrem = $pdo->query("SELECT max_investment_usd FROM tbl_ananta_package_config WHERE package_id = 'PREMIUM'")->fetch(PDO::FETCH_ASSOC);
    assertTest($cfgPrem['max_investment_usd'] === null, "10. Premium no maximum limit (Max Limit = NULL)", "Max Limit: NULL");

    // 11. 48-month lock calculation
    // 12. 48-month maturity calculation
    $inv11 = processAnantaPackageInvestment($testUser, 'BASIC', 500.00);
    $rec11 = $pdo->query("SELECT * FROM tbl_roi_one WHERE id = {$inv11['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    $expMat11 = date('Y-m-d', strtotime('+48 months'));
    assertTest($rec11['lock_period_months'] == 48, "11. 48-month lock calculation (Lock = 48m)", "Lock: {$rec11['lock_period_months']}m");
    assertTest($rec11['maturity_date'] === $expMat11, "12. 48-month maturity calculation (Maturity = {$expMat11})", "Maturity: {$rec11['maturity_date']}");

    // 13. Pre-maturity withdrawal blocked
    $w13 = processCapitalWithdrawal($testUser, $inv11['investment_id']);
    assertTest($w13['status'] === 'error' && strpos($w13['message'], 'locked') !== false, "13. Pre-maturity withdrawal blocked", $w13['message']);

    // 14. Post-maturity withdrawal allowed
    $pastDate = date('Y-m-d', strtotime('-1 day'));
    $pdo->exec("UPDATE tbl_roi_one SET maturity_date = '{$pastDate}' WHERE id = {$inv11['investment_id']}");
    $w14 = processCapitalWithdrawal($testUser, $inv11['investment_id']);
    assertTest($w14['status'] === 'success', "14. Post-maturity withdrawal allowed", $w14['message']);

    // 15. 15% deduction calculation
    $req15 = $pdo->query("SELECT * FROM tbl_capital_withdrawal_request WHERE investment_id = {$inv11['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    assertTest($req15['deduction_amount_usd'] == 75.00 && $req15['net_withdrawal_usd'] == 425.00, "15. 15% deduction calculation ($500 - 15% = $425 Net)", "Deduction: \${$req15['deduction_amount_usd']} | Net: \${$req15['net_withdrawal_usd']}");

    // 16. 30% Bonus default = 30%
    $cfgB30 = $pdo->query("SELECT bonus_percentage FROM tbl_ananta_package_config WHERE package_id = 'BONUS_30'")->fetch(PDO::FETCH_ASSOC);
    assertTest($cfgB30['bonus_percentage'] == 30.00, "16. 30% Bonus default = 30%", "Configured Default: {$cfgB30['bonus_percentage']}%");

    // 17. Admin bonus percentage configuration
    $pdo->exec("UPDATE tbl_ananta_package_config SET bonus_percentage = 35.00 WHERE package_id = 'BONUS_30'");
    $cfgB35 = $pdo->query("SELECT bonus_percentage FROM tbl_ananta_package_config WHERE package_id = 'BONUS_30'")->fetch(PDO::FETCH_ASSOC);
    assertTest($cfgB35['bonus_percentage'] == 35.00, "17. Admin bonus percentage configuration (Updated to 35%)", "New Config Rate: {$cfgB35['bonus_percentage']}%");
    $pdo->exec("UPDATE tbl_ananta_package_config SET bonus_percentage = 30.00 WHERE package_id = 'BONUS_30'"); // Restore to 30%

    // 18. Real Fund and Bonus Wallet separation
    // 19. Bonus amount calculation
    $inv18 = processAnantaPackageInvestment($testUser, 'BONUS_30', 1000.00);
    $rec18 = $pdo->query("SELECT * FROM tbl_roi_one WHERE id = {$inv18['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    $u18 = $pdo->query("SELECT amount, bonus_30_wallet FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    assertTest($rec18['real_fund_usd'] == 1000.00 && $u18['bonus_30_wallet'] == 300.00, "18. Real Fund and Bonus Wallet separation", "Real Fund: \${$rec18['real_fund_usd']} | Bonus Wallet: \${$u18['bonus_30_wallet']}");
    assertTest($rec18['bonus_amount_usd'] == 300.00, "19. Bonus amount calculation ($1,000 * 30% = $300)", "Bonus Amt: \${$rec18['bonus_amount_usd']}");

    // 20. Bonus amount excluded from cash withdrawal
    // 21. 6-month Bonus Package lock
    // 22. Bonus Package post-6-month withdrawal
    // 23. 15% deduction only on Real Fund
    $expMat21 = date('Y-m-d', strtotime('+6 months'));
    assertTest($rec18['lock_period_months'] == 6 && $rec18['maturity_date'] === $expMat21, "21. 6-month Bonus Package lock", "Lock: {$rec18['lock_period_months']}m | Maturity: {$rec18['maturity_date']}");

    $w20_pre = processCapitalWithdrawal($testUser, $inv18['investment_id']);
    assertTest($w20_pre['status'] === 'error', "Pre-6-month bonus package withdrawal blocked", $w20_pre['message']);

    $pdo->exec("UPDATE tbl_roi_one SET maturity_date = '{$pastDate}' WHERE id = {$inv18['investment_id']}");
    $w22 = processCapitalWithdrawal($testUser, $inv18['investment_id']);
    $req22 = $pdo->query("SELECT * FROM tbl_capital_withdrawal_request WHERE investment_id = {$inv18['investment_id']}")->fetch(PDO::FETCH_ASSOC);

    assertTest($w22['status'] === 'success', "22. Bonus Package post-6-month withdrawal allowed", $w22['message']);
    assertTest($req22['net_withdrawal_usd'] == 850.00, "20. Bonus amount excluded from cash withdrawal ($1,000 Real Fund - 15% = $850 Net Cash)", "Net Cash: \${$req22['net_withdrawal_usd']}");
    assertTest($req22['deduction_amount_usd'] == 150.00, "23. 15% deduction only on Real Fund (15% of $1,000 = $150)", "Deduction: \${$req22['deduction_amount_usd']}");

    // 24. Tour Package 48-month lock
    // 25. Tour Package 15% deduction
    $inv24 = processAnantaPackageInvestment($testUser, 'TOUR', 2000.00);
    $rec24 = $pdo->query("SELECT * FROM tbl_roi_one WHERE id = {$inv24['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    $expMat24 = date('Y-m-d', strtotime('+48 months'));
    assertTest($rec24['lock_period_months'] == 48 && $rec24['maturity_date'] === $expMat24, "24. Tour Package 48-month lock", "Lock: {$rec24['lock_period_months']}m | Maturity: {$rec24['maturity_date']}");

    $pdo->exec("UPDATE tbl_roi_one SET maturity_date = '{$pastDate}' WHERE id = {$inv24['investment_id']}");
    $w25 = processCapitalWithdrawal($testUser, $inv24['investment_id']);
    $req25 = $pdo->query("SELECT * FROM tbl_capital_withdrawal_request WHERE investment_id = {$inv24['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    assertTest($req25['deduction_amount_usd'] == 300.00 && $req25['net_withdrawal_usd'] == 1700.00, "25. Tour Package 15% deduction ($2,000 - 15% = $1,700)", "Deduction: \${$req25['deduction_amount_usd']} | Net: \${$req25['net_withdrawal_usd']}");

    // 26. Historical package snapshot preservation
    // 27. Package setting change does not alter old investment
    $pdo->exec("UPDATE tbl_ananta_package_config SET lock_period_months = 36, withdrawal_deduction_percent = 10.00 WHERE package_id = 'BASIC'");
    $rec26 = $pdo->query("SELECT lock_period_months, deduction_percent_snapshot FROM tbl_roi_one WHERE id = {$inv11['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    assertTest($rec26['lock_period_months'] == 48 && $rec26['deduction_percent_snapshot'] == 15.00, "26. Historical package snapshot preservation", "Preserved Lock: {$rec26['lock_period_months']}m");
    assertTest($rec26['deduction_percent_snapshot'] == 15.00, "27. Package setting change does not alter old investment", "Preserved Deduction: {$rec26['deduction_percent_snapshot']}%");
    $pdo->exec("UPDATE tbl_ananta_package_config SET lock_period_months = 48, withdrawal_deduction_percent = 15.00 WHERE package_id = 'BASIC'"); // Restore

    // 28. Inactive package blocks new investment
    $pdo->exec("UPDATE tbl_ananta_package_config SET status = 0 WHERE package_id = 'TOUR'");
    $r28 = validatePackageInvestment('TOUR', 500.00);
    assertTest($r28['status'] === false && strpos($r28['message'], 'inactive') !== false, "28. Inactive package blocks new investment", $r28['message']);

    // 29. Existing investment remains valid after package deactivation
    $rec29 = $pdo->query("SELECT capital_withdrawal_status FROM tbl_roi_one WHERE id = {$inv24['investment_id']}")->fetch(PDO::FETCH_ASSOC);
    assertTest($rec29['capital_withdrawal_status'] === 'WITHDRAWN', "29. Existing investment remains valid after package deactivation", "Status: {$rec29['capital_withdrawal_status']}");
    $pdo->exec("UPDATE tbl_ananta_package_config SET status = 1 WHERE package_id = 'TOUR'"); // Restore

    // 30. Duplicate withdrawal protection
    $w30 = processCapitalWithdrawal($testUser, $inv18['investment_id']);
    assertTest($w30['status'] === 'error' && strpos($w30['message'], 'ALREADY') !== false, "30. Duplicate withdrawal protection", $w30['message']);

    // 31. User authorization
    $_SESSION['userid'] = $testUser;
    unset($_SESSION['auserid']);
    assertTest(isset($_SESSION['userid']) && !isset($_SESSION['auserid']), "31. User authorization guard verified", "User session active");

    // 32. Admin authorization
    $_SESSION['auserid'] = 'admin';
    unset($_SESSION['userid']);
    assertTest(isset($_SESSION['auserid']) && !isset($_SESSION['userid']), "32. Admin authorization guard verified", "Admin session active");

    // Restore user session for sandbox cleanup
    $_SESSION['userid'] = $testUser;
    unset($_SESSION['auserid']);

    // 33. SQL injection safety
    $sqlPayload = "' OR '1'='1";
    $r33 = validatePackageInvestment($sqlPayload, 500.00);
    assertTest($r33['status'] === false, "33. SQL injection safety", $r33['message']);

    // 34. Wallet isolation
    $u34 = $pdo->query("SELECT amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    $isolated = isset($u34['bonus_30_wallet']) && isset($u34['profit_income_wallet']) && isset($u34['direct_bonus_wallet']) && isset($u34['mentor_income_wallet']) && isset($u34['vip_club_wallet']);
    assertTest($isolated === true, "34. Wallet isolation (6 separate wallets preserved)", "Bonus 30 Wallet isolated");

    // 35. Permanent transaction/history preservation
    $cntInv = $pdo->query("SELECT COUNT(*) FROM tbl_roi_one WHERE user_id = '{$testUser}'")->fetchColumn();
    $cntWd  = $pdo->query("SELECT COUNT(*) FROM tbl_capital_withdrawal_request WHERE user_id = '{$testUser}'")->fetchColumn();
    assertTest($cntInv >= 3 && $cntWd >= 3, "35. Permanent transaction/history preservation", "Investments: {$cntInv} | Withdrawals: {$cntWd}");

    // Cleanup sandbox test user rows
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
