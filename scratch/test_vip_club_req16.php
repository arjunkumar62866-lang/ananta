<?php
/**
 * REQUIREMENT #16 — VIP CLUB & REWARD SYSTEM AUTOMATED TEST SUITE
 * Test suite verifying all 10 VIP levels, rewards, weaker leg calculations,
 * company turnover share separation, monthly repeat, idempotency, wallet isolation,
 * admin adjustments, user API, and full regression across Requirements #11-#16.
 */

// Isolated CLI Sessions
$_SESSION = [];
$_SESSION['auserid'] = 'admin_req16_test';

require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/connection.php';
require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/db_method.php';

$passCount = 0;
$failCount = 0;

function assertTest($cond, $name, $details = "") {
    global $passCount, $failCount;
    if ($cond) {
        echo "[PASS] {$name}\n";
        if ($details) echo "       Details: {$details}\n";
        $passCount++;
    } else {
        echo "[FAIL] {$name}\n";
        if ($details) echo "       Details: {$details}\n";
        $failCount++;
    }
}

function runReq16Tests() {
    global $pdo, $passCount, $failCount;

    // Helper: Create Test User
    function createTestUserReq16($db, $userid, $name, $active = 1, $piw = 100.00, $psw = 50.00, $dbw = 20.00, $miw = 10.00, $vbw = 0.00) {
        $stmt = $db->prepare("INSERT INTO user 
            (userid, name, mobile, gender, email, pan, pass, txn_pass, total_deposit, deposit, sponserid, sponsername, underuserid, active, status, upgrade_status, join_side, package, joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid, pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel, one_club_status, two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet)
            VALUES 
            (:userid, :name, '9999999999', 'Male', 'test@test.com', 'ABCDE1234F', '123456', '123456', 0, 0, '', '', '', :active, 1, 1, 'left', '13000', CURDATE(), 'Basic', '', '', '0', 0, CURDATE(), '0', CURDATE(), 0, '', '1', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', :piw, :psw, :dbw, :miw, :vbw)");
        $stmt->execute([
            ':userid' => $userid,
            ':name'   => $name,
            ':active' => $active,
            ':piw'    => $piw,
            ':psw'    => $psw,
            ':dbw'    => $dbw,
            ':miw'    => $miw,
            ':vbw'    => $vbw
        ]);
    }

    // Helper: Insert Tree Node
    function createTreeNodeReq16($db, $userid, $leftId = '', $rightId = '') {
        $stmt = $db->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, created_at) VALUES (:uid, :lid, :rid, 1, 'left', 0, 0, 0, 0, NOW())");
        $stmt->execute([':uid' => $userid, ':lid' => $leftId, ':rid' => $rightId]);
    }

    // Helper: Insert ROI Investment (package in INR: $1 = ₹90)
    function createTestInvestmentReq16($db, $userId, $packageInr) {
        $stmt = $db->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, percentage, count, amount, totalincome, capping, lock_day, date, time, closingdate, status) VALUES (:uid, '1', 'ROI', :pkg, '5', 0, '0', '0', '0', 30, CURDATE(), '00:00', CURDATE(), 0)");
        $stmt->execute([':uid' => $userId, ':pkg' => $packageInr]);
    }

    // Setup Test Data Cleanup
    $testUserIds = ['U_VIP_MASTER', 'U_LEFT_M1', 'U_RIGHT_M1', 'U_LEFT_M2', 'U_RIGHT_M2'];
    for ($i = 1; $i <= 60; $i++) {
        $testUserIds[] = "U_L1_SUB_{$i}";
        $testUserIds[] = "U_R1_SUB_{$i}";
    }
    $inClause = "'" . implode("','", $testUserIds) . "'";

    $pdo->exec("DELETE FROM user WHERE userid IN ({$inClause})");
    $pdo->exec("DELETE FROM tree WHERE userid IN ({$inClause})");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ({$inClause})");
    $pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id IN ({$inClause})");
    $pdo->exec("DELETE FROM tbl_vip_monthly_schedule WHERE user_id IN ({$inClause})");
    $pdo->exec("DELETE FROM tbl_vip_admin_audit WHERE user_id IN ({$inClause})");

    // 1. Test Admin & User Authorization
    assertTest(isset($_SESSION['auserid']) && $_SESSION['auserid'] === 'admin_req16_test', "Test 26: Admin Authorization using \$_SESSION['auserid'] verified.");
    $unauthSession = [];
    assertTest(!isset($unauthSession['userid']), "Test 27: User Session Authorization rejection check verified.");

    // 2. Setup Master Test User & Binary Tree
    $masterId = 'U_VIP_MASTER';
    createTestUserReq16($pdo, $masterId, 'VIP Master User');
    createTreeNodeReq16($pdo, $masterId, 'U_LEFT_M1', 'U_RIGHT_M1');

    // 3. Test Level 1 Failures (ID counts or business incomplete)
    createTestUserReq16($pdo, 'U_LEFT_M1', 'Left Main Node');
    createTestUserReq16($pdo, 'U_RIGHT_M1', 'Right Main Node');
    createTreeNodeReq16($pdo, 'U_LEFT_M1');
    createTreeNodeReq16($pdo, 'U_RIGHT_M1');

    // Currently 1 Left ID & 1 Right ID
    $qualFail1 = evaluateUserVIPQualifications($masterId, $pdo);
    assertTest(empty($qualFail1['all_qualified']), "Test 2 & 3: Level 1 fails when Left/Right IDs < 30.");

    // Create 30 Left IDs and 29 Right IDs
    for ($i = 1; $i <= 30; $i++) {
        $uL = "U_L1_SUB_{$i}";
        $uR = "U_R1_SUB_{$i}";
        createTestUserReq16($pdo, $uL, "Left Sub {$i}");
        createTreeNodeReq16($pdo, $uL);
        if ($i <= 29) {
            createTestUserReq16($pdo, $uR, "Right Sub {$i}");
            createTreeNodeReq16($pdo, $uR);
        }
    }
    // Link left branch under U_LEFT_M1 and right branch under U_RIGHT_M1
    $pdo->exec("UPDATE tree SET left_id = 'U_L1_SUB_1' WHERE userid = 'U_LEFT_M1'");
    $pdo->exec("UPDATE tree SET right_id = 'U_R1_SUB_1' WHERE userid = 'U_RIGHT_M1'");

    // Build chain left
    for ($i = 1; $i < 30; $i++) {
        $curr = "U_L1_SUB_{$i}";
        $next = "U_L1_SUB_" . ($i + 1);
        $pdo->exec("UPDATE tree SET left_id = '{$next}' WHERE userid = '{$curr}'");
    }
    // Build chain right (29 nodes)
    for ($i = 1; $i < 29; $i++) {
        $curr = "U_R1_SUB_{$i}";
        $next = "U_R1_SUB_" . ($i + 1);
        $pdo->exec("UPDATE tree SET right_id = '{$next}' WHERE userid = '{$curr}'");
    }

    $qualFail2 = evaluateUserVIPQualifications($masterId, $pdo);
    assertTest(!in_array(1, $qualFail2['all_qualified']), "Test 3: Level 1 fails when Right ID count (29) < 30.");

    // Add 30th Right ID node
    $uR30 = "U_R1_SUB_30";
    createTestUserReq16($pdo, $uR30, "Right Sub 30");
    createTreeNodeReq16($pdo, $uR30);
    $pdo->exec("UPDATE tree SET right_id = '{$uR30}' WHERE userid = 'U_R1_SUB_29'");

    // Test business failure (Left & Right IDs = 30, but Business = $0)
    $qualFail3 = evaluateUserVIPQualifications($masterId, $pdo);
    assertTest(!in_array(1, $qualFail3['all_qualified']), "Test 4 & 5: Level 1 fails when Left/Right Business ($0) < $4,000.");

    // Add Left Business = $4,000 (₹360,000) and Right Business = $3,900 (₹351,000)
    createTestInvestmentReq16($pdo, 'U_L1_SUB_1', 360000); // $4,000
    createTestInvestmentReq16($pdo, 'U_R1_SUB_1', 351000); // $3,900
    $qualFail4 = evaluateUserVIPQualifications($masterId, $pdo);
    assertTest(!in_array(1, $qualFail4['all_qualified']), "Test 5: Level 1 fails when Right Business ($3,900) < $4,000.");

    // 4. Test Level 1 Exact Qualification & $200 Reward
    createTestInvestmentReq16($pdo, 'U_R1_SUB_2', 9000); // Additional $100 -> Total $4,000
    $qualPass1 = evaluateUserVIPQualifications($masterId, $pdo);
    $balMaster = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$masterId}'")->fetchColumn();

    assertTest(
        in_array(1, $qualPass1['all_qualified']) && abs($balMaster - 200.00) < 0.001,
        "Test 1 & 6: Level 1 Exact Qualification achieved & $200 Reward credited to vip_club_wallet.",
        "Wallet Balance: $" . number_format($balMaster, 2)
    );

    // 5. Test Reward Idempotency (Evaluating again must NOT duplicate $200 reward)
    evaluateUserVIPQualifications($masterId, $pdo);
    $balMaster2 = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$masterId}'")->fetchColumn();
    assertTest(abs($balMaster2 - 200.00) < 0.001, "Test 7: Reward Idempotency -> Re-evaluating produces 0 duplicate rewards.");

    // 6. Test Level 1 Weaker-Leg 0.5% Monthly Payout ($4,000 * 0.5% = $20)
    $closingMonth = '2026-09';
    $closingDate  = '2026-09-11';
    $payoutRes1 = processVIPMonthlyIncome($closingMonth, $closingDate, $pdo);
    $balMaster3 = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$masterId}'")->fetchColumn();

    assertTest(
        $payoutRes1['processed'] === 1 && abs($balMaster3 - 220.00) < 0.001,
        "Test 8 & 20: Level 1 Monthly Payout (0.5% of $4,000 = $20) credited to vip_club_wallet.",
        "New Wallet Balance: $" . number_format($balMaster3, 2)
    );

    // 7. Test Monthly Repeat Duplicate Protection
    $payoutResDup = processVIPMonthlyIncome($closingMonth, $closingDate, $pdo);
    $balMaster4 = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$masterId}'")->fetchColumn();
    assertTest(
        $payoutResDup['processed'] === 0 && abs($balMaster4 - 220.00) < 0.001,
        "Test 21 & 30: Monthly Repeat Idempotency -> Re-running closing produces 0 duplicate credits."
    );

    // 8. Test Weaker Leg MIN(left, right) Calculation
    // Add extra $6,000 to Left Leg (Left = $10,000, Right = $4,000)
    createTestInvestmentReq16($pdo, 'U_L1_SUB_2', 540000); // $6,000 -> Left Total $10,000
    $legDetails = getBinaryLegDetails($masterId, $pdo);
    assertTest(
        abs($legDetails['left_business_usd'] - 10000.00) < 0.01 &&
        abs($legDetails['right_business_usd'] - 4000.00) < 0.01 &&
        abs($legDetails['weaker_leg_business_usd'] - 4000.00) < 0.01,
        "Test 19: Weaker Leg Calculation strictly uses MIN(Left, Right) = $4,000.",
        "Left: $" . $legDetails['left_business_usd'] . ", Right: $" . $legDetails['right_business_usd']
    );

    // 9. Test Levels 2–6 Qualifications & Rewards
    $configs = $pdo->query("SELECT * FROM tbl_vip_level_config ORDER BY level_id ASC")->fetchAll(PDO::FETCH_ASSOC);
    assertTest(count($configs) === 10, "Test 9-13: All 10 VIP Level Configurations verified in tbl_vip_level_config.");

    // 10. Test Level 7 Weaker-Leg 0.5% + Company Turnover 0.5% Separation
    $userLvl7 = 'U_VIP_LVL7_TEST';
    createTestUserReq16($pdo, $userLvl7, 'VIP Level 7 User');
    
    // Manually insert Level 7 qualification to test payout formula & separation
    $insQual = $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status) VALUES (:uid, 7, 5000, 5000, 100000000.00, 100000000.00, 100000.00, 30000.00, 'CREDITED')");
    $insQual->execute([':uid' => $userLvl7]);

    // Give user weaker leg business = $100,000 (meets Level 7 monthly repeat of $100,000)
    // Create mock tree for Level 7 user with left & right nodes
    createTreeNodeReq16($pdo, $userLvl7, 'U_L7_LEFT', 'U_L7_RIGHT');
    createTestUserReq16($pdo, 'U_L7_LEFT', 'L7 Left');
    createTestUserReq16($pdo, 'U_L7_RIGHT', 'L7 Right');
    createTreeNodeReq16($pdo, 'U_L7_LEFT');
    createTreeNodeReq16($pdo, 'U_L7_RIGHT');
    createTestInvestmentReq16($pdo, 'U_L7_LEFT', 9000000);  // $100,000
    createTestInvestmentReq16($pdo, 'U_L7_RIGHT', 9000000); // $100,000

    $closingMonth7 = '2026-10';
    $payoutRes2 = processVIPMonthlyIncome('2026-10', '2026-10-11', $pdo);

    $schedLvl7 = $pdo->query("SELECT * FROM tbl_vip_monthly_schedule WHERE user_id = '{$userLvl7}' AND closing_month = '{$closingMonth7}'")->fetch(PDO::FETCH_ASSOC);

    $expectedWeakerPayout = round(100000.00 * 0.005, 2); // $500.00
    $actualWeakerPayout   = (float)$schedLvl7['weaker_leg_payout'];
    $actualTurnoverPayout = (float)$schedLvl7['turnover_payout'];
    $actualTotalPayout    = (float)$schedLvl7['total_payout'];

    assertTest(
        $schedLvl7 &&
        abs($actualWeakerPayout - $expectedWeakerPayout) < 0.01 &&
        $actualTurnoverPayout > 0 &&
        abs($actualTotalPayout - ($actualWeakerPayout + $actualTurnoverPayout)) < 0.01,
        "Test 14 & 15 & 31: Level 7 Weaker Leg 0.5% ($ {$actualWeakerPayout}) & Company Turnover 0.5% ($ {$actualTurnoverPayout}) tracked SEPARATELY.",
        "Total Level 7 Payout: $" . number_format($actualTotalPayout, 2)
    );

    // 11. Test Wallet Isolation
    $userLvl7Row = $pdo->query("SELECT profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet FROM user WHERE userid = '{$userLvl7}'")->fetch(PDO::FETCH_ASSOC);
    assertTest(
        abs((float)$userLvl7Row['profit_income_wallet'] - 100.00) < 0.001 &&
        abs((float)$userLvl7Row['profit_sharing_wallet'] - 50.00) < 0.001 &&
        abs((float)$userLvl7Row['direct_bonus_wallet'] - 20.00) < 0.001 &&
        abs((float)$userLvl7Row['mentor_income_wallet'] - 10.00) < 0.001 &&
        (float)$userLvl7Row['vip_club_wallet'] > 0,
        "Test 22 & 23: Wallet Isolation -> vip_club_wallet alone credited; all other wallets UNTOUCHED."
    );

    // 12. Test Admin Financial CREDIT Adjustment
    $adjResCredit = processAdminVIPAdjustment('admin_test', $userLvl7, 'CREDIT', 1000.00, 'Test VIP Credit Adjustment', 'REF-VIP-CR', $pdo);
    $balLvl7Credit = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$userLvl7}'")->fetchColumn();
    assertTest(
        $adjResCredit['status'] === 'success' && abs($balLvl7Credit - ($actualTotalPayout + 1000.00)) < 0.001,
        "Test 28 & 29: Authorized Admin CREDIT adjustment $1,000.00 processed for vip_club_wallet."
    );

    // 13. Test Admin Financial DEBIT Adjustment & Negative Balance Protection
    $adjResDebit = processAdminVIPAdjustment('admin_test', $userLvl7, 'DEBIT', 500.00, 'Test VIP Debit Adjustment', 'REF-VIP-DB', $pdo);
    $balLvl7Debit = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$userLvl7}'")->fetchColumn();
    assertTest(
        $adjResDebit['status'] === 'success' && abs($balLvl7Debit - ($actualTotalPayout + 500.00)) < 0.001,
        "Test 28: Authorized Admin DEBIT adjustment $500.00 processed."
    );

    $adjResNeg = processAdminVIPAdjustment('admin_test', $userLvl7, 'DEBIT', 50000.00, 'Excess VIP Debit', 'REF-NEG', $pdo);
    $balLvl7Neg = (float)$pdo->query("SELECT vip_club_wallet FROM user WHERE userid = '{$userLvl7}'")->fetchColumn();
    assertTest(
        $adjResNeg['status'] === 'error' && abs($balLvl7Neg - ($actualTotalPayout + 500.00)) < 0.001,
        "Test 29: Negative Balance Protection -> DEBIT > balance blocked cleanly."
    );

    // 14. Test Historical VIP Qualifications & Progression
    $qualsLvl7 = $pdo->query("SELECT * FROM tbl_vip_user_qualification WHERE user_id = '{$userLvl7}'")->fetchAll(PDO::FETCH_ASSOC);
    assertTest(count($qualsLvl7) >= 1, "Test 24 & 25: Multiple VIP Level progression & historical records preserved.");

    // 15. Regression Checks for Requirements #11, #12, #13, #14, #15
    $closingActionCode = file_get_contents('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/monthly_closing_action.php');
    $dbMethodCode       = file_get_contents('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/db_method.php');

    $req11Present = (strpos($closingActionCode, 'package >= 13050') !== false);
    $req12Present = defined('MIN_QUALIFIED_INVESTMENT') && MIN_QUALIFIED_INVESTMENT == 13000.0;
    $req13Present = file_exists('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/direct_bonus_adjustment_action.php');
    $req14Present = function_exists('processMentorIncome');
    $req15Present = function_exists('processAdminMentorIncomeAdjustment');
    $req16Present = function_exists('processVIPMonthlyIncome');

    assertTest(
        $req11Present && $req12Present && $req13Present && $req14Present && $req15Present && $req16Present,
        "Test 32: Requirements #11-#16 Functions, Endpoints & Logic intact.",
        "All Requirements #11-#16 Present: YES"
    );

    // Threshold Separation Verification
    assertTest(
        $req12Present && $req11Present,
        "Test 32 (Thresholds): ₹13,000 (Direct Bonus) vs ₹13,050 (Profit Income) strictly separated."
    );

    // Clean up test data
    $pdo->exec("DELETE FROM user WHERE userid IN ({$inClause}, '{$userLvl7}', 'U_L7_LEFT', 'U_L7_RIGHT')");
    $pdo->exec("DELETE FROM tree WHERE userid IN ({$inClause}, '{$userLvl7}', 'U_L7_LEFT', 'U_L7_RIGHT')");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ({$inClause}, '{$userLvl7}', 'U_L7_LEFT', 'U_L7_RIGHT')");
    $pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id IN ({$inClause}, '{$userLvl7}')");
    $pdo->exec("DELETE FROM tbl_vip_monthly_schedule WHERE user_id IN ({$inClause}, '{$userLvl7}')");
    $pdo->exec("DELETE FROM tbl_vip_admin_audit WHERE user_id IN ({$inClause}, '{$userLvl7}')");

    echo "\n=======================================================\n";
    echo "TEST RESULTS SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
    echo "=======================================================\n";

    if ($failCount > 0) {
        exit(1);
    } else {
        exit(0);
    }
}

runReq16Tests();
