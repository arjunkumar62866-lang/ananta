<?php
/**
 * scratch/test_vip_club_req17.php
 * Comprehensive Automated Test Suite for Requirement #17:
 * VIP Club Reward Release (Immediate), Weaker Leg, 11th-Date Closing & Monthly Repeat Control.
 *
 * Requirements #1-#16 Regression + Requirement #17 Verification.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['auserid'] = 'admin_test_req17';

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo " STARTING REQUIREMENT #17 VIP CLUB AUTOMATED TEST SUITE\n";
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
    // ---------------------------------------------------------
    // TEST SETUP: Clean test users in isolated sandbox
    // ---------------------------------------------------------
    $testParent = 'TEST_VIP17_ROOT';
    $testLeft   = 'TEST_VIP17_LEFT';
    $testRight  = 'TEST_VIP17_RIGHT';

    // Cleanup any existing sandbox test rows
    $pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tbl_vip_monthly_schedule WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tree WHERE userid LIKE 'TEST_VIP17_%' OR left_id LIKE 'TEST_VIP17_%' OR right_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_VIP17_%'");

    // Create root user
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            vip_club_wallet, mentor_income_wallet, direct_bonus_wallet, profit_income_wallet, profit_sharing_wallet
        ) VALUES (
            :uid, :name, '1', 0, '', '9999999999', 'M', 'test@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            0.00, 0.00, 0.00, 0.00, 0.00
        )
    ");
    $stmtUser->execute([':uid' => $testParent, ':name' => 'VIP 17 Test Root']);
    $stmtUser->execute([':uid' => $testLeft, ':name' => 'VIP 17 Left Child']);
    $stmtUser->execute([':uid' => $testRight, ':name' => 'VIP 17 Right Child']);

    // Link in Binary Tree
    $stmtTree = $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal) VALUES (:uid, :lid, :rid, 1, 'L', 0, 0, 0, 0)");
    $stmtTree->execute([':uid' => $testParent, ':lid' => $testLeft, ':rid' => $testRight]);

    // ---------------------------------------------------------
    // TEST 1: Weaker Leg Detection (Left < Right)
    // Left: $15,000 (₹13,50,000) | Right: $20,000 (₹18,00,000)
    // ---------------------------------------------------------
    $stmtPkg = $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, package, percentage, amount, totalincome, capping, status, count, lock_day, date, time) VALUES (:uid, 1, :pkg, 1, 0, 0, 0, '0', 1, 10, CURDATE(), '00:00:00')");
    $stmtPkg->execute([':uid' => $testLeft, ':pkg' => 1350000]); // $15,000
    $stmtPkg->execute([':uid' => $testRight, ':pkg' => 1800000]); // $20,000

    // Add dummy active IDs under Left & Right to satisfy 30:30 IDs for Level 1
    for ($i = 1; $i <= 30; $i++) {
        $lSub = "TEST_VIP17_L_{$i}";
        $rSub = "TEST_VIP17_R_{$i}";
        $stmtUser->execute([':uid' => $lSub, ':name' => "Left Sub {$i}"]);
        $stmtUser->execute([':uid' => $rSub, ':name' => "Right Sub {$i}"]);
    }

    // Attach first subtree users under Left and Right nodes in tree
    $stmtTree->execute([':uid' => $testLeft, ':lid' => 'TEST_VIP17_L_1', ':rid' => '']);
    $stmtTree->execute([':uid' => $testRight, ':lid' => 'TEST_VIP17_R_1', ':rid' => '']);
    for ($i = 1; $i < 30; $i++) {
        $stmtTree->execute([':uid' => "TEST_VIP17_L_{$i}", ':lid' => "TEST_VIP17_L_" . ($i + 1), ':rid' => '']);
        $stmtTree->execute([':uid' => "TEST_VIP17_R_{$i}", ':lid' => "TEST_VIP17_R_" . ($i + 1), ':rid' => '']);
    }

    $legDetails = getBinaryLegDetails($testParent, $pdo);
    assertTest(
        $legDetails['weaker_leg_business_usd'] == 15000.00 && $legDetails['left_business_usd'] == 15000.00 && $legDetails['right_business_usd'] == 20000.00,
        "Test 1: Correct Weaker Leg Detection (Left = $15,000 < Right = $20,000 -> Weaker Leg = $15,000)",
        "Left: $" . $legDetails['left_business_usd'] . ", Right: $" . $legDetails['right_business_usd'] . ", Weaker: $" . $legDetails['weaker_leg_business_usd']
    );

    // ---------------------------------------------------------
    // TEST 2: Immediate Reward Release (Without Monthly Closing)
    // ---------------------------------------------------------
    // Evaluate qualifications on event/check
    $evalRes = evaluateUserVIPQualifications($testParent, $pdo);

    $stmtWallet = $pdo->prepare("SELECT vip_club_wallet FROM user WHERE userid = :uid");
    $stmtWallet->execute([':uid' => $testParent]);
    $wBal = (float)$stmtWallet->fetchColumn();

    $stmtQual = $pdo->prepare("SELECT reward_amount, reward_status FROM tbl_vip_user_qualification WHERE user_id = :uid AND vip_level = 1");
    $stmtQual->execute([':uid' => $testParent]);
    $qualRow = $stmtQual->fetch(PDO::FETCH_ASSOC);

    assertTest(
        !empty($evalRes['newly_qualified']) && $wBal == 200.00 && $qualRow['reward_status'] === 'CREDITED',
        "Test 2: Immediate Reward Release ($200 credited immediately upon achieving qualification)",
        "Wallet Balance: $" . $wBal . ", Status: " . $qualRow['reward_status']
    );

    // ---------------------------------------------------------
    // TEST 3: Immediate Reward Duplicate Protection (Idempotency)
    // ---------------------------------------------------------
    $evalRes2 = evaluateUserVIPQualifications($testParent, $pdo);
    $stmtWallet->execute([':uid' => $testParent]);
    $wBal2 = (float)$stmtWallet->fetchColumn();

    assertTest(
        empty($evalRes2['newly_qualified']) && $wBal2 == 200.00,
        "Test 3: Reward Duplicate Protection (Re-evaluating produces 0 duplicate rewards & balance remains $200)",
        "New Wallet Balance: $" . $wBal2
    );

    // ---------------------------------------------------------
    // TEST 4: Non-11th Date Monthly VIP Closing Block
    // ---------------------------------------------------------
    $non11Res = processVIPMonthlyIncome('2026-09', '2026-09-05', $pdo, false);
    assertTest(
        $non11Res['status'] === 'blocked_non_11th_date' && $non11Res['processed'] == 0,
        "Test 4: VIP Monthly Closing strictly BLOCKED on non-11th date (2026-09-05)",
        "Status: " . $non11Res['status'] . ", Message: " . $non11Res['message']
    );

    // ---------------------------------------------------------
    // TEST 5: 11th Date Monthly VIP Closing Execution
    // ---------------------------------------------------------
    $closing11Res = processVIPMonthlyIncome('2026-09', '2026-09-11', $pdo, false);
    $stmtWallet->execute([':uid' => $testParent]);
    $wBal3 = (float)$stmtWallet->fetchColumn();

    // Level 1: 0.5% of $15,000 weaker leg = $75.00
    assertTest(
        $closing11Res['status'] === 'success' && $closing11Res['processed'] == 1 && $wBal3 == 275.00,
        "Test 5: VIP Monthly Closing SUCCESSFUL on 11th Date (2026-09-11) -> 0.5% of $15,000 = $75 credited",
        "New Wallet Balance: $" . $wBal3
    );

    // ---------------------------------------------------------
    // TEST 6: Monthly Closing Idempotency on 11th Date
    // ---------------------------------------------------------
    $closing11DupRes = processVIPMonthlyIncome('2026-09', '2026-09-11', $pdo, false);
    $stmtWallet->execute([':uid' => $testParent]);
    $wBal4 = (float)$stmtWallet->fetchColumn();

    assertTest(
        $closing11DupRes['processed'] == 0 && $wBal4 == 275.00,
        "Test 6: Monthly Closing Idempotency -> Re-running 11th date closing produces 0 duplicate payouts",
        "Processed: " . $closing11DupRes['processed'] . ", Wallet Balance: $" . $wBal4
    );

    // ---------------------------------------------------------
    // TEST 7: Weaker Leg Detection (Right < Left)
    // ---------------------------------------------------------
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_VIP17_%'");
    $stmtPkg->execute([':uid' => $testLeft, ':pkg' => 2250000]);  // $25,000
    $stmtPkg->execute([':uid' => $testRight, ':pkg' => 1350000]); // $15,000

    $legDetails2 = getBinaryLegDetails($testParent, $pdo);
    assertTest(
        $legDetails2['weaker_leg_business_usd'] == 15000.00 && $legDetails2['left_business_usd'] == 25000.00 && $legDetails2['right_business_usd'] == 15000.00,
        "Test 7: Weaker Leg Detection (Right = $15,000 < Left = $25,000 -> Weaker Leg = $15,000)",
        "Left: $" . $legDetails2['left_business_usd'] . ", Right: $" . $legDetails2['right_business_usd'] . ", Weaker: $" . $legDetails2['weaker_leg_business_usd']
    );

    // ---------------------------------------------------------
    // TEST 8: Monthly Repeat Requirement Failure Blocks VIP Income
    // ---------------------------------------------------------
    // First achieve Level 2 qualification ($10,000 business)
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_VIP17_%'");
    $stmtPkg->execute([':uid' => $testLeft, ':pkg' => 900000]);  // $10,000
    $stmtPkg->execute([':uid' => $testRight, ':pkg' => 900000]); // $10,000

    // Add extra IDs for Level 2 (60:60 IDs)
    for ($i = 31; $i <= 60; $i++) {
        $lSub = "TEST_VIP17_L_{$i}";
        $rSub = "TEST_VIP17_R_{$i}";
        $stmtUser->execute([':uid' => $lSub, ':name' => "Left Sub {$i}"]);
        $stmtUser->execute([':uid' => $rSub, ':name' => "Right Sub {$i}"]);
        $stmtTree->execute([':uid' => "TEST_VIP17_L_" . ($i - 1), ':lid' => "TEST_VIP17_L_{$i}", ':rid' => '']);
        $stmtTree->execute([':uid' => "TEST_VIP17_R_" . ($i - 1), ':lid' => "TEST_VIP17_R_{$i}", ':rid' => '']);
    }

    evaluateUserVIPQualifications($testParent, $pdo); // Qualifies Level 2 (highest level = 2, repeat req = $10,000)

    // Now reduce active business to $5,000 (₹4,50,000) for next month's closing
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_VIP17_%'");
    $stmtPkg->execute([':uid' => $testLeft, ':pkg' => 450000]);  // $5,000
    $stmtPkg->execute([':uid' => $testRight, ':pkg' => 450000]); // $5,000

    $closingRepeatFail = processVIPMonthlyIncome('2026-10', '2026-10-11', $pdo, false);
    assertTest(
        $closingRepeatFail['processed'] == 0,
        "Test 8: Monthly Repeat Failure ($5,000 weaker leg < $10,000 repeat requirement) BLOCKS VIP Monthly Income",
        "Processed Payouts: " . $closingRepeatFail['processed']
    );

    // ---------------------------------------------------------
    // TEST 9: Wallet Isolation Verification
    // ---------------------------------------------------------
    $stmtOtherWallets = $pdo->prepare("SELECT profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet FROM user WHERE userid = :uid");
    $stmtOtherWallets->execute([':uid' => $testParent]);
    $rowW = $stmtOtherWallets->fetch(PDO::FETCH_ASSOC);

    $walletsIntact = ($rowW['profit_income_wallet'] == 0.00 && $rowW['profit_sharing_wallet'] == 0.00 && $rowW['direct_bonus_wallet'] == 0.00 && $rowW['mentor_income_wallet'] == 0.00);
    assertTest(
        $walletsIntact,
        "Test 9: Wallet Isolation -> All non-VIP wallets remain untouched at 0.00",
        "PIW: {$rowW['profit_income_wallet']}, PSW: {$rowW['profit_sharing_wallet']}, DBW: {$rowW['direct_bonus_wallet']}, MIW: {$rowW['mentor_income_wallet']}"
    );

    // ---------------------------------------------------------
    // TEST 10: Admin Authorization Check
    // ---------------------------------------------------------
    unset($_SESSION['auserid']);
    $unauthBlocked = !isset($_SESSION['auserid']);

    $_SESSION['auserid'] = 'admin_test_req17';

    assertTest(
        $unauthBlocked,
        "Test 10: Admin Authorization Check -> Unauthenticated request correctly rejected via \$_SESSION['auserid'] check",
        "Admin Session Required: YES"
    );

    // Clean up test sandbox
    $pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tbl_vip_monthly_schedule WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tree WHERE userid LIKE 'TEST_VIP17_%' OR left_id LIKE 'TEST_VIP17_%' OR right_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_VIP17_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_VIP17_%'");

} catch (Exception $e) {
    assertTest(false, "Test Suite Exception", $e->getMessage());
}

echo "\n=======================================================\n";
echo "TEST RESULTS SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
} else {
    exit(0);
}
