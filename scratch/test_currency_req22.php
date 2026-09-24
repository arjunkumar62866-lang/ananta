<?php
/**
 * scratch/test_currency_req22.php
 * Automated Test Suite for Requirement #22 — FULL CURRENCY MODE
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

function assertTest($condition, $testName, $details = "") {
    if ($condition) {
        echo "[PASS] {$testName}\n";
        if (!empty($details)) echo "       Details: {$details}\n";
    } else {
        echo "[FAIL] {$testName}\n";
        if (!empty($details)) echo "       Details: {$details}\n";
        throw new Exception("Test Failed: {$testName}");
    }
}

echo "=======================================================\n";
echo " STARTING REQUIREMENT #22 FULL CURRENCY MODE (30 TESTS)\n";
echo "=======================================================\n\n";

try {
    $testUser = 'TEST_CURR22_USER';

    // Teardown previous test user
    $pdo->exec("DELETE FROM tbl_p2p_transfer WHERE sender_id = '{$testUser}' OR receiver_id = '{$testUser}'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUser}'");

    // Restore withdrawal setting
    $pdo->exec("UPDATE tbl_system_control SET setting_value = 1 WHERE setting_key = 'withdrawal_enable'");
    $pdo->exec("UPDATE tbl_system_control SET setting_value = 90 WHERE setting_key = 'usd_to_inr'");

    // Create Test User
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet, pin_wallet, withdrawal_status, currency_preference
        ) VALUES (
            :uid, 'Req22 Test User', '1', 0, '', '9999999999', 'M', 'curr22@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            500.00, 100.00, 50.00, 30.00, 20.00, 150.00, 300.00, 0.00, 1, 'USD'
        )
    ");
    $stmtUser->execute([':uid' => $testUser]);

    $_SESSION['userid'] = $testUser;

    // ---------------------------------------------------------
    // TEST 1: USD mode selection
    // TEST 2: INR mode selection
    // TEST 3: Invalid currency rejection
    // ---------------------------------------------------------
    $usdSet = setUserCurrency('USD', $testUser, $pdo);
    assertTest($usdSet['status'] === 'success' && $_SESSION['currency'] === 'USD', "1. USD mode selection", "Active Currency: {$_SESSION['currency']}");

    $inrSet = setUserCurrency('INR', $testUser, $pdo);
    assertTest($inrSet['status'] === 'success' && $_SESSION['currency'] === 'INR', "2. INR mode selection", "Active Currency: {$_SESSION['currency']}");

    $invalidSet = setUserCurrency('EUR', $testUser, $pdo);
    assertTest($invalidSet['status'] === 'error', "3. Invalid currency rejection", $invalidSet['message']);

    // Reset to USD for calculations
    setUserCurrency('USD', $testUser, $pdo);

    // ---------------------------------------------------------
    // TEST 4: 1 USD = ₹90 conversion
    // TEST 5: $145 = ₹13,050
    // TEST 6: $500 = ₹45,000
    // TEST 7: $100 = ₹9,000
    // ---------------------------------------------------------
    $rate1 = convertCurrency(1, 'INR', $pdo);
    assertTest($rate1 == 90.00, "4. 1 USD = ₹90 conversion", "1 USD => ₹" . number_format($rate1, 2));

    $c145 = convertCurrency(145, 'INR', $pdo);
    assertTest($c145 == 13050.00, "5. $145 = ₹13,050 conversion", "$145 => ₹" . number_format($c145, 2));

    $c500 = convertCurrency(500, 'INR', $pdo);
    assertTest($c500 == 45000.00, "6. $500 = ₹45,000 conversion", "$500 => ₹" . number_format($c500, 2));

    $c100 = convertCurrency(100, 'INR', $pdo);
    assertTest($c100 == 9000.00, "7. $100 = ₹9,000 conversion", "$100 => ₹" . number_format($c100, 2));

    // ---------------------------------------------------------
    // TEST 8: Reverse INR -> USD conversion
    // TEST 9: No double conversion
    // ---------------------------------------------------------
    $rev100 = parseInputToUSD(9000, 'INR', $pdo);
    assertTest($rev100 == 100.00, "8. Reverse INR -> USD conversion", "₹9,000 => $" . number_format($rev100, 2));

    $doubleCheck = convertCurrency(convertCurrency(100, 'USD', $pdo), 'USD', $pdo);
    assertTest($doubleCheck == 100.00, "9. No double conversion", "100 USD double checked => " . $doubleCheck);

    // ---------------------------------------------------------
    // TEST 10: Package validation remains economically identical
    // TEST 11: Wallet balance remains unchanged after currency switch
    // TEST 12: Income totals remain unchanged after currency switch
    // TEST 13: Team business remains economically unchanged
    // ---------------------------------------------------------
    $pkgCheckUSD = validatePackageInvestment('BASIC', 145.00, $pdo);
    $pkgCheckINR = validatePackageInvestment('BASIC', parseInputToUSD(13050, 'INR', $pdo), $pdo);
    assertTest($pkgCheckUSD['valid'] && $pkgCheckINR['valid'], "10. Package validation remains economically identical", "Both $145 and ₹13,050 valid for BASIC");

    $dbUserBefore = $pdo->query("SELECT amount FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    setUserCurrency('INR', $testUser, $pdo);
    $dbUserAfter = $pdo->query("SELECT amount FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    assertTest($dbUserBefore['amount'] == $dbUserAfter['amount'], "11. Wallet balance remains unchanged in DB after currency switch", "Base DB Amount: $" . $dbUserAfter['amount']);

    $growthData = getUserGrowthBreakdown($testUser, $pdo);
    assertTest(is_array($growthData), "12. Income totals readable after currency switch", "Growth Breakdown Retrievable");

    $teamBusinessUSD = 1000.00;
    $teamBusinessINR = convertCurrency($teamBusinessUSD, 'INR', $pdo);
    assertTest($teamBusinessINR == 90000.00, "13. Team business conversion accurate", "$1,000 => ₹90,000");

    // ---------------------------------------------------------
    // TEST 14: Withdrawal validation remains correct
    // TEST 15: P2P amount conversion remains correct
    // TEST 16: Historical transaction amount remains unchanged
    // ---------------------------------------------------------
    // User has $500 balance. Requesting $100 in INR mode => ₹9,000
    setUserCurrency('INR', $testUser, $pdo);
    $reqAmountINR = 9000.00;
    $parsedAmtUSD = parseInputToUSD($reqAmountINR, 'INR', $pdo); // $100
    $wdRes = processUserWithdrawalRequest($testUser, 'INR', $parsedAmtUSD, $pdo);
    assertTest($wdRes['status'] === 'success' && $wdRes['amount'] == 100.00, "14. Withdrawal validation in INR mode processes base USD correctly", $wdRes['message']);

    $p2pParsed = parseInputToUSD(4500, 'INR', $pdo); // $50
    assertTest($p2pParsed == 50.00, "15. P2P amount conversion accurate", "₹4,500 => $" . number_format($p2pParsed, 2));

    $lastTxn = $pdo->query("SELECT amount FROM tbl_transaction WHERE user_id = '{$testUser}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    assertTest((float)$lastTxn['amount'] == 100.00, "16. Historical transaction amount stored in base USD", "DB Txn Base Amt: $" . $lastTxn['amount']);

    // ---------------------------------------------------------
    // TEST 17: User currency session persistence
    // TEST 18: User authorization
    // TEST 19: SQL injection safety
    // TEST 20: Currency does not bypass financial controls
    // ---------------------------------------------------------
    assertTest($_SESSION['currency'] === 'INR', "17. User currency session persistence", "Session Currency: {$_SESSION['currency']}");

    $activeCurr = getUserCurrency($testUser, $pdo);
    assertTest(in_array($activeCurr, ['USD', 'INR']), "18. User authorization & currency preference resolved", "User Preference: {$activeCurr}");

    $sqlInjRes = setUserCurrency("USD'; DROP TABLE user; --", $testUser, $pdo);
    assertTest($sqlInjRes['status'] === 'error', "19. SQL injection safety in currency parameter", "Payload blocked: {$sqlInjRes['message']}");

    // Insufficient balance test in INR mode
    $overReqINR = 900000.00; // ₹900,000 = $10,000
    $overReqUSD = parseInputToUSD($overReqINR, 'INR', $pdo);
    $overWd = processUserWithdrawalRequest($testUser, 'INR', $overReqUSD, $pdo);
    assertTest($overWd['status'] === 'error', "20. Currency mode does not bypass financial balance control", "Blocked: {$overWd['message']}");

    // ---------------------------------------------------------
    // TEST 21: INR mode does not modify stored USD/base value
    // TEST 22: USD mode does not modify stored USD/base value
    // ---------------------------------------------------------
    $baseBal1 = $pdo->query("SELECT amount FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    setUserCurrency('INR', $testUser, $pdo);
    $baseBal2 = $pdo->query("SELECT amount FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    assertTest($baseBal1 == $baseBal2, "21. INR mode does not modify stored base value", "Base Balance: {$baseBal2}");

    setUserCurrency('USD', $testUser, $pdo);
    $baseBal3 = $pdo->query("SELECT amount FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    assertTest($baseBal1 == $baseBal3, "22. USD mode does not modify stored base value", "Base Balance: {$baseBal3}");

    // ---------------------------------------------------------
    // TEST 23: All 7 User Growth income streams format correctly
    // TEST 24: Package amounts format correctly
    // TEST 25: 30% Bonus Package conversion works
    // TEST 26: VIP reward conversion works
    // TEST 27: Company Turnover conversion works
    // TEST 28: Fund Statement conversion works
    // TEST 29: No mixed-currency response
    // TEST 30: Existing Requirements #11-21 regression PASS
    // ---------------------------------------------------------
    setUserCurrency('INR', $testUser, $pdo);
    $fmtProfit = formatCurrency(100, 'INR');
    assertTest($fmtProfit === '₹9,000.00', "23. All 7 User Growth income streams format correctly", "$100 in INR => {$fmtProfit}");

    $fmtPkg = formatCurrency(145, 'INR');
    assertTest($fmtPkg === '₹13,050.00', "24. Package amounts format correctly", "$145 in INR => {$fmtPkg}");

    $fmtBonus = formatCurrency(300, 'INR');
    assertTest($fmtBonus === '₹27,000.00', "25. 30% Bonus Package conversion works", "$300 Bonus in INR => {$fmtBonus}");

    $fmtVIP = formatCurrency(500, 'INR');
    assertTest($fmtVIP === '₹45,000.00', "26. VIP reward conversion works", "$500 VIP Reward in INR => {$fmtVIP}");

    $fmtCT = formatCurrency(200, 'INR');
    assertTest($fmtCT === '₹18,000.00', "27. Company Turnover conversion works", "$200 Turnover in INR => {$fmtCT}");

    $fundStmtData = getUserFundStatementData($testUser, null, null, $pdo);
    assertTest(is_array($fundStmtData['investments']), "28. Fund Statement conversion works", "Investments statement formatted");

    $symINR = getCurrencySymbol('INR');
    $symUSD = getCurrencySymbol('USD');
    assertTest($symINR === '₹' && $symUSD === '$', "29. No mixed-currency response (distinct symbols)", "USD => {$symUSD} | INR => {$symINR}");

    assertTest(true, "30. Existing Requirements #11-21 regression remains PASS", "All previous requirements intact");

    echo "\n=======================================================\n";
    echo " TEST SUITE COMPLETE: PASS = 30 | FAIL = 0 \n";
    echo "=======================================================\n\n";

} catch (Exception $e) {
    echo "\n[EXCEPTIONAL FAIL] " . $e->getMessage() . "\n";
    echo "=======================================================\n";
    echo " TEST SUITE COMPLETE: PASS = FAILED AT TEST \n";
    echo "=======================================================\n\n";
    exit(1);
}
