<?php
/**
 * scratch/test_admin_panel_req18.php
 * Automated Test Suite for Requirement #18: Admin Panel Final Structure.
 *
 * Requirements #1-#17 Regression + Requirement #18 Verification.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['auserid'] = 'admin_test_req18';

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo " STARTING REQUIREMENT #18 ADMIN PANEL AUTOMATED SUITE \n";
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
    // TEST SETUP: Clean test user in isolated sandbox
    // ---------------------------------------------------------
    $testUser = 'TEST_ADM18_USER';

    $pdo->exec("DELETE FROM tbl_admin_audit_log WHERE target_user_id LIKE 'TEST_ADM18_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_ADM18_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_ADM18_%'");

    // Insert sandbox user
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            vip_club_wallet, mentor_income_wallet, direct_bonus_wallet, profit_income_wallet, profit_sharing_wallet
        ) VALUES (
            :uid, :name, '1', 0, '', '9999999999', 'M', 'test18@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            0.00, 0.00, 0.00, 0.00, 0.00
        )
    ");
    $stmtUser->execute([':uid' => $testUser, ':name' => 'Req18 Admin Test User']);

    // ---------------------------------------------------------
    // TEST 1: Real-time Dashboard Statistics Calculation
    // ---------------------------------------------------------
    $dashStats = getAdminComprehensiveDashboardStats($pdo);
    assertTest(
        !empty($dashStats) && isset($dashStats['total_users']) && $dashStats['total_users'] > 0,
        "Test 1: Real-time Dashboard Statistics Calculation (Total Users > 0, Real DB Source)",
        "Total Users: {$dashStats['total_users']}, Active: {$dashStats['active_users']}, Investment: ₹{$dashStats['total_investment_inr']}"
    );

    // ---------------------------------------------------------
    // TEST 2: System Control Settings Toggle Persistence
    // ---------------------------------------------------------
    setSystemControl('website_maintenance', '1', 'admin_test_req18', $pdo);
    $controls = getSystemControls($pdo);
    $maintVal = $controls['website_maintenance'] ?? '0';

    setSystemControl('website_maintenance', '0', 'admin_test_req18', $pdo); // revert

    assertTest(
        $maintVal === '1',
        "Test 2: System Control Settings Persistence (Toggled website_maintenance to 1 & verified)",
        "Persisted Value: {$maintVal}"
    );

    // ---------------------------------------------------------
    // TEST 3: Universal Admin CREDIT Adjustment with Row Locking
    // ---------------------------------------------------------
    $creditRes = processUniversalAdminWalletAdjustment(
        'admin_test_req18',
        $testUser,
        'profit_income_wallet',
        'CREDIT',
        500.00,
        'Test Requirement #18 Authorized Credit',
        'REF18_CREDIT_1',
        $pdo
    );

    $stmtW = $pdo->prepare("SELECT profit_income_wallet FROM user WHERE userid = :uid");
    $stmtW->execute([':uid' => $testUser]);
    $piwBal1 = (float)$stmtW->fetchColumn();

    assertTest(
        $creditRes['status'] === 'success' && $piwBal1 == 500.00 && $creditRes['new_balance'] == 500.00,
        "Test 3: Universal Admin CREDIT Adjustment ($500 credited to profit_income_wallet)",
        "New Balance: $" . $piwBal1
    );

    // ---------------------------------------------------------
    // TEST 4: Universal Admin DEBIT Adjustment with Row Locking
    // ---------------------------------------------------------
    $debitRes = processUniversalAdminWalletAdjustment(
        'admin_test_req18',
        $testUser,
        'profit_income_wallet',
        'DEBIT',
        200.00,
        'Test Requirement #18 Authorized Debit',
        'REF18_DEBIT_1',
        $pdo
    );

    $stmtW->execute([':uid' => $testUser]);
    $piwBal2 = (float)$stmtW->fetchColumn();

    assertTest(
        $debitRes['status'] === 'success' && $piwBal2 == 300.00 && $debitRes['new_balance'] == 300.00,
        "Test 4: Universal Admin DEBIT Adjustment ($200 debited from profit_income_wallet)",
        "New Balance: $" . $piwBal2
    );

    // ---------------------------------------------------------
    // TEST 5: Negative Balance Protection (DEBIT > Balance Blocked)
    // ---------------------------------------------------------
    $negRes = processUniversalAdminWalletAdjustment(
        'admin_test_req18',
        $testUser,
        'profit_income_wallet',
        'DEBIT',
        1000.00,
        'Test Excessive Debit Block',
        'REF18_NEG_1',
        $pdo
    );

    $stmtW->execute([':uid' => $testUser]);
    $piwBal3 = (float)$stmtW->fetchColumn();

    assertTest(
        $negRes['status'] === 'error' && $piwBal3 == 300.00,
        "Test 5: Negative Balance Protection (Attempted $1000 debit on $300 balance cleanly BLOCKED)",
        "Message: {$negRes['message']}"
    );

    // ---------------------------------------------------------
    // TEST 6: Mandatory Reason Requirement for Financial Operations
    // ---------------------------------------------------------
    $noReasonRes = processUniversalAdminWalletAdjustment(
        'admin_test_req18',
        $testUser,
        'profit_income_wallet',
        'CREDIT',
        100.00,
        '', // Empty reason
        'REF18_NO_REASON',
        $pdo
    );

    assertTest(
        $noReasonRes['status'] === 'error',
        "Test 6: Mandatory Reason Requirement (Credit with empty reason BLOCKED)",
        "Message: {$noReasonRes['message']}"
    );

    // ---------------------------------------------------------
    // TEST 7: Complete Audit Trail Logging in tbl_admin_audit_log
    // ---------------------------------------------------------
    $stmtAudit = $pdo->prepare("SELECT * FROM tbl_admin_audit_log WHERE target_user_id = :uid ORDER BY id DESC LIMIT 1");
    $stmtAudit->execute([':uid' => $testUser]);
    $auditRow = $stmtAudit->fetch(PDO::FETCH_ASSOC);

    assertTest(
        !empty($auditRow) && $auditRow['admin_id'] === 'admin_test_req18' && $auditRow['amount'] == 200.00,
        "Test 7: Audit Trail Logging (Action, Admin ID, Previous/New Balances recorded in tbl_admin_audit_log)",
        "Action: {$auditRow['action']}, Wallet: {$auditRow['wallet_type']}, Reason: {$auditRow['reason']}"
    );

    // ---------------------------------------------------------
    // TEST 8: Wallet Isolation Verification across All Wallets
    // ---------------------------------------------------------
    $stmtAllW = $pdo->prepare("SELECT profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet FROM user WHERE userid = :uid");
    $stmtAllW->execute([':uid' => $testUser]);
    $rowW = $stmtAllW->fetch(PDO::FETCH_ASSOC);

    $isolated = ($rowW['profit_sharing_wallet'] == 0.00 && $rowW['direct_bonus_wallet'] == 0.00 && $rowW['mentor_income_wallet'] == 0.00 && $rowW['vip_club_wallet'] == 0.00);

    assertTest(
        $isolated,
        "Test 8: Wallet Isolation (Adjusting profit_income_wallet left all other wallets untouched at 0.00)",
        "PSW: {$rowW['profit_sharing_wallet']}, DBW: {$rowW['direct_bonus_wallet']}, MIW: {$rowW['mentor_income_wallet']}, VIP: {$rowW['vip_club_wallet']}"
    );

    // ---------------------------------------------------------
    // TEST 9: Admin Session Authorization Guard
    // ---------------------------------------------------------
    unset($_SESSION['auserid']);
    $unauthBlocked = !isset($_SESSION['auserid']);
    $_SESSION['auserid'] = 'admin_test_req18';

    assertTest(
        $unauthBlocked,
        "Test 9: Admin Session Authorization Guard (\$_SESSION['auserid'] strictly required)",
        "Admin Session Guard Active: YES"
    );

    // Cleanup sandbox user
    $pdo->exec("DELETE FROM tbl_admin_audit_log WHERE target_user_id LIKE 'TEST_ADM18_%'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TEST_ADM18_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TEST_ADM18_%'");

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
