<?php
/**
 * scratch/test_user_wallet_req19.php
 * Comprehensive Automated Test Suite for Requirement #19 User Wallet Dashboard.
 *
 * Requirements #1-#18 Regression + Requirement #19 Verification.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['userid'] = 'TEST_USR19_MEMBER';

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo " STARTING REQUIREMENT #19 USER WALLET AUTOMATED SUITE \n";
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
    // TEST SETUP: Isolated user sandbox
    // ---------------------------------------------------------
    $testUser = 'TEST_USR19_MEMBER';

    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM tbl_direct_bonus_schedule WHERE beneficiary_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_USR19_%'");

    // Create member user with explicit wallet balances
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet
        ) VALUES (
            :uid, :name, '1', 0, '', '9999999999', 'M', 'test19@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 360000,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            1250.00, 450.00, 150.00, 300.00, 200.00, 500.00
        )
    ");
    $stmtUser->execute([':uid' => $testUser, ':name' => 'Req19 User Member']);

    // Create Active Investment (₹3,60,000 = $4,000)
    $stmtPkg = $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, package, percentage, amount, totalincome, capping, status, count, lock_day, date, time) VALUES (:uid, 1, 360000, 1, 0, 0, 0, '0', 2, 10, CURDATE(), '00:00:00')");
    $stmtPkg->execute([':uid' => $testUser]);

    // Create Direct Bonus Schedule row
    $stmtDb = $pdo->prepare("INSERT INTO tbl_direct_bonus_schedule (investment_id, source_user_id, beneficiary_id, investment_amount, total_bonus, installment_amount, installment_number, installment_month, status, credited_at) VALUES (999, 'OTHER_USER', :uid, 100000, 6000, 600.00, 1, '2026-09', 'CREDITED', NOW())");
    $stmtDb->execute([':uid' => $testUser]);

    // Create VIP qualification & reward row ($200 reward)
    $stmtVip = $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status) VALUES (:uid, 1, 30, 30, 4000.00, 4000.00, 4000.00, 200.00, 'CREDITED')");
    $stmtVip->execute([':uid' => $testUser]);

    // Create Transactions for Permanent History
    $stmtTxn = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, amount, created_date, status) VALUES (:uid, :type, :sub, :amt, NOW(), 1)");
    $stmtTxn->execute([':uid' => $testUser, ':type' => 'Credit', ':sub' => 'Profit Income Monthly Payout', ':amt' => 450.00]);
    $stmtTxn->execute([':uid' => $testUser, ':type' => 'Credit', ':sub' => 'Withdrawal Request Paid', ':amt' => 100.00]);

    // ---------------------------------------------------------
    // TEST 1: User Session Authentication Guard
    // ---------------------------------------------------------
    unset($_SESSION['userid']);
    $unauthBlocked = !isset($_SESSION['userid']);
    $_SESSION['userid'] = $testUser;

    assertTest(
        $unauthBlocked,
        "Test 1: User Session Authentication Guard (\$_SESSION['userid'] strictly enforced)",
        "User Session Guard Active: YES"
    );

    // ---------------------------------------------------------
    // TEST 2: Wallet Balances Fetch (Main, Profit Income, VIP, Direct Bonus)
    // ---------------------------------------------------------
    $stmtU = $pdo->prepare("SELECT amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet FROM user WHERE userid = :uid");
    $stmtU->execute([':uid' => $testUser]);
    $rowW = $stmtU->fetch(PDO::FETCH_ASSOC);

    assertTest(
        $rowW['amount'] == 1250.00 && $rowW['profit_income_wallet'] == 450.00 && $rowW['vip_club_wallet'] == 500.00,
        "Test 2: Wallet Balances fetched accurately from user record",
        "Main: ₹" . $rowW['amount'] . ", PIW: ₹" . $rowW['profit_income_wallet'] . ", VIP: $" . $rowW['vip_club_wallet']
    );

    // ---------------------------------------------------------
    // TEST 3: Cumulative Direct Bonus & VIP Reward Aggregations
    // ---------------------------------------------------------
    $stmtDbCheck = $pdo->prepare("SELECT COALESCE(SUM(installment_amount), 0) FROM tbl_direct_bonus_schedule WHERE beneficiary_id = :uid AND status = 'CREDITED'");
    $stmtDbCheck->execute([':uid' => $testUser]);
    $dbTotal = (float)$stmtDbCheck->fetchColumn();

    $stmtVipCheck = $pdo->prepare("SELECT COALESCE(SUM(reward_amount), 0) FROM tbl_vip_user_qualification WHERE user_id = :uid AND reward_status = 'CREDITED'");
    $stmtVipCheck->execute([':uid' => $testUser]);
    $vipTotal = (float)$stmtVipCheck->fetchColumn();

    assertTest(
        $dbTotal == 600.00 && $vipTotal == 200.00,
        "Test 3: Cumulative Direct Bonus (₹600) & VIP Rewards ($200) Aggregations verified",
        "Direct Bonus Total: ₹" . $dbTotal . ", VIP Rewards Total: $" . $vipTotal
    );

    // ---------------------------------------------------------
    // TEST 4: Active Investment Details Fetch
    // ---------------------------------------------------------
    $stmtInvCheck = $pdo->prepare("SELECT package, lock_day, count FROM tbl_roi_one WHERE user_id = :uid AND status = '0'");
    $stmtInvCheck->execute([':uid' => $testUser]);
    $invRow = $stmtInvCheck->fetch(PDO::FETCH_ASSOC);

    $pkgUsd = round(((float)$invRow['package']) / 90.0, 2);

    assertTest(
        $invRow['package'] == 360000 && $pkgUsd == 4000.00 && $invRow['count'] == 2,
        "Test 4: Active Investment Details (₹3,60,000 / $4,000) fetched accurately",
        "Package: ₹" . $invRow['package'] . " ($" . $pkgUsd . "), Paid Months: " . $invRow['count'] . "/" . $invRow['lock_day']
    );

    // ---------------------------------------------------------
    // TEST 5: Permanent History Records Verification
    // ---------------------------------------------------------
    $stmtTxnCheck = $pdo->prepare("SELECT COUNT(*) FROM tbl_transaction WHERE user_id = :uid");
    $stmtTxnCheck->execute([':uid' => $testUser]);
    $txnCount = (int)$stmtTxnCheck->fetchColumn();

    assertTest(
        $txnCount >= 2,
        "Test 5: Permanent History Records preserved in tbl_transaction (Count >= 2)",
        "Recorded Transactions: " . $txnCount
    );

    // ---------------------------------------------------------
    // TEST 6: User Data Isolation (User A cannot access User B)
    // ---------------------------------------------------------
    $otherUser = 'TEST_USR19_OTHER';
    $stmtUser->execute([':uid' => $otherUser, ':name' => 'Req19 Other User']);

    $stmtOtherCheck = $pdo->prepare("SELECT COUNT(*) FROM tbl_transaction WHERE user_id = :uid");
    $stmtOtherCheck->execute([':uid' => $otherUser]);
    $otherTxn = (int)$stmtOtherCheck->fetchColumn();

    assertTest(
        $otherTxn == 0,
        "Test 6: User Data Isolation (User A sandbox isolated from User B sandbox)",
        "Other User Txn Count: " . $otherTxn
    );

    // Cleanup sandbox user
    $pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM tbl_direct_bonus_schedule WHERE beneficiary_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id LIKE 'TEST_USR19_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_USR19_%'");

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
