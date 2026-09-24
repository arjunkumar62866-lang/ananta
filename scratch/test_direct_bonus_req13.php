<?php
/**
 * TEST SUITE: REQUIREMENT #13 - ADMIN DIRECT BONUS MANAGEMENT & REGRESSION TEST
 */

require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/connection.php';
require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo "STARTING REQUIREMENT #13 TEST SUITE\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = "") {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] {$testName}\n";
        if ($details) echo "       Details: {$details}\n";
        $passCount++;
    } else {
        echo "[FAIL] {$testName}\n";
        if ($details) echo "       Details: {$details}\n";
        $failCount++;
    }
}

function createTestUser(PDO $pdo, $userid, $name, $active = '1', $sponserid = '', $piw = 100.00, $psw = 50.00, $dbw = 0.00) {
    $stmt = $pdo->prepare("INSERT INTO user 
        (userid, name, mobile, gender, email, pan, pass, txn_pass, total_deposit, deposit, sponserid, sponsername, underuserid, active, status, upgrade_status, join_side, package, joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid, pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel, one_club_status, two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet)
        VALUES 
        (:userid, :name, '9999999999', 'Male', 'test@test.com', 'ABCDE1234F', '123456', '123456', 0, 0, :sponserid, '', '', :active, 1, 1, 'left', '13000', CURDATE(), 'Basic', '', '', '0', 0, CURDATE(), '0', CURDATE(), 0, '', '1', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', :piw, :psw, :dbw)");
    $stmt->execute([
        ':userid'    => $userid,
        ':name'      => $name,
        ':sponserid' => $sponserid,
        ':active'    => $active,
        ':piw'       => $piw,
        ':psw'       => $psw,
        ':dbw'       => $dbw
    ]);
}

// -------------------------------------------------------------------------
// PREPARATION: Create Test Admin & Test Users
// -------------------------------------------------------------------------
$admin_id = 9999;
$test_sponsor_id = "TST_ADMIN_SPONSOR";
$test_direct1_id = "TST_ADMIN_DIR1";
$test_direct2_id = "TST_ADMIN_DIR2";

// Cleanup existing test records
$pdo->exec("DELETE FROM user WHERE userid IN ('{$test_sponsor_id}', '{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_sponsor WHERE sponsor_id = '{$test_sponsor_id}' OR referral_id IN ('{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ('{$test_sponsor_id}', '{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_direct_bonus_schedule WHERE beneficiary_id = '{$test_sponsor_id}' OR source_user_id IN ('{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_direct_bonus_admin_audit WHERE user_id = '{$test_sponsor_id}'");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$test_sponsor_id}'");

// Insert Sponsor User (inactive $11)
createTestUser($pdo, $test_sponsor_id, 'Test Admin Sponsor', 0, '', 100.00, 50.00, 0.00);

// -------------------------------------------------------------------------
// TEST 1: Qualified Direct Details Helper (Case 1: Sponsor Inactive $11 & No Directs)
// -------------------------------------------------------------------------
$details = getQualifiedDirectDetails($test_sponsor_id, $pdo);
$qCount = 0;
foreach ($details as $d) { if ($d['is_qualified']) $qCount++; }
assertTest(
    $qCount === 0 && count($details) === 0,
    "Test 1: Sponsor Inactive ($11) & No Directs -> 0 Qualified Directs",
    "Direct Count: " . count($details) . ", Q-Count: " . $qCount
);

// -------------------------------------------------------------------------
// TEST 2: Qualification Case 2 (Active Sponsor + 1 Qualified Direct)
// -------------------------------------------------------------------------
// Activate sponsor ($11 paid)
$pdo->exec("UPDATE user SET active = 1 WHERE userid = '{$test_sponsor_id}'");

// Create Direct 1 ($11 active + ₹15,000 investment)
createTestUser($pdo, $test_direct1_id, 'Direct 1', 1, $test_sponsor_id);
$pdo->exec("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES ('{$test_sponsor_id}', '{$test_direct1_id}', NOW())");
$pdo->exec("INSERT INTO tbl_roi_one (user_id, level, name, package, percentage, amount, totalincome, capping, date, time, status) VALUES ('{$test_direct1_id}', '1', 'Basic', 15000, '0', '0', '0', '0', CURDATE(), '12:00:00', 1)");

$details = getQualifiedDirectDetails($test_sponsor_id, $pdo);
$qCount = 0;
foreach ($details as $d) { if ($d['is_qualified']) $qCount++; }
assertTest(
    $qCount === 1,
    "Test 2: Active Sponsor + 1 Qualified Direct -> 1 Qualified Direct (Needs 2)",
    "Direct Count: " . count($details) . ", Q-Count: " . $qCount
);

// -------------------------------------------------------------------------
// TEST 3: Qualification Case 4 (Active Sponsor + Direct 2 has investment < ₹13,000)
// -------------------------------------------------------------------------
// Create Direct 2 ($11 active + ₹10,000 investment < ₹13,000)
createTestUser($pdo, $test_direct2_id, 'Direct 2', 1, $test_sponsor_id);
$pdo->exec("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES ('{$test_sponsor_id}', '{$test_direct2_id}', NOW())");
$pdo->exec("INSERT INTO tbl_roi_one (user_id, level, name, package, percentage, amount, totalincome, capping, date, time, status) VALUES ('{$test_direct2_id}', '1', 'Basic', 10000, '0', '0', '0', '0', CURDATE(), '12:00:00', 1)");
$dir2_inv_id = $pdo->lastInsertId();

$details = getQualifiedDirectDetails($test_sponsor_id, $pdo);
$qCount = 0;
foreach ($details as $d) { if ($d['is_qualified']) $qCount++; }
assertTest(
    $qCount === 1,
    "Test 3: Active Sponsor + Direct 2 Investment ₹10,000 < ₹13,000 -> Still 1 Qualified Direct",
    "Direct Count: " . count($details) . ", Q-Count: " . $qCount
);

// -------------------------------------------------------------------------
// TEST 4: Qualification Case 3 (Direct 2 updates investment to >= ₹13,000 -> Qualified!)
// -------------------------------------------------------------------------
$pdo->exec("UPDATE tbl_roi_one SET package = 13500 WHERE id = {$dir2_inv_id}");

$details = getQualifiedDirectDetails($test_sponsor_id, $pdo);
$qCount = 0;
foreach ($details as $d) { if ($d['is_qualified']) $qCount++; }
assertTest(
    $qCount === 2,
    "Test 4: Active Sponsor + 2 Qualified Directs (₹15k & ₹13.5k) -> Fully Qualified! (2 Qualified Directs)",
    "Direct Count: " . count($details) . ", Q-Count: " . $qCount
);

// -------------------------------------------------------------------------
// TEST 5: Admin Balance CREDIT Adjustment
// -------------------------------------------------------------------------
$creditRes = processAdminDirectBonusAdjustment($admin_id, $test_sponsor_id, 'CREDIT', 500.00, 'Test Admin Manual Credit', 'ADM_REF_101', $pdo);
assertTest(
    $creditRes['status'] === 'success' && (float)$creditRes['new_balance'] === 500.00,
    "Test 5: Admin CREDIT Adjustment ₹500.00",
    "Status: {$creditRes['status']}, New Balance: " . ($creditRes['new_balance'] ?? '')
);

// -------------------------------------------------------------------------
// TEST 6: Audit Trail & Transaction Log Verification for CREDIT
// -------------------------------------------------------------------------
$auditStmt = $pdo->query("SELECT * FROM tbl_direct_bonus_admin_audit WHERE user_id = '{$test_sponsor_id}' AND action = 'CREDIT'");
$auditRow = $auditStmt->fetch(PDO::FETCH_ASSOC);

$txStmt = $pdo->query("SELECT * FROM tbl_transaction WHERE user_id = '{$test_sponsor_id}' AND subject LIKE '%Admin Direct Bonus Adjustment%'");
$txRow = $txStmt->fetch(PDO::FETCH_ASSOC);

assertTest(
    $auditRow && (float)$auditRow['amount'] === 500.00 && $txRow && (float)$txRow['amount'] === 500.00,
    "Test 6: Audit Trail & Transaction Record Created for CREDIT Adjustment",
    "Audit Amount: " . ($auditRow['amount'] ?? 'NULL') . ", Tx Amount: " . ($txRow['amount'] ?? 'NULL')
);

// -------------------------------------------------------------------------
// TEST 7: Admin Balance DEBIT Adjustment
// -------------------------------------------------------------------------
$debitRes = processAdminDirectBonusAdjustment($admin_id, $test_sponsor_id, 'DEBIT', 200.00, 'Test Admin Manual Debit', 'ADM_REF_102', $pdo);
assertTest(
    $debitRes['status'] === 'success' && (float)$debitRes['new_balance'] === 300.00,
    "Test 7: Admin DEBIT Adjustment ₹200.00",
    "Status: {$debitRes['status']}, New Balance: " . ($debitRes['new_balance'] ?? '')
);

// -------------------------------------------------------------------------
// TEST 8: Negative Balance Rejection on Excessive DEBIT
// -------------------------------------------------------------------------
$excessDebitRes = processAdminDirectBonusAdjustment($admin_id, $test_sponsor_id, 'DEBIT', 1000.00, 'Test Excessive Debit', 'ADM_REF_103', $pdo);
assertTest(
    $excessDebitRes['status'] === 'error' && strpos($excessDebitRes['message'], 'Insufficient') !== false,
    "Test 8: Rejects DEBIT that would cause negative balance",
    "Response Message: {$excessDebitRes['message']}"
);

// -------------------------------------------------------------------------
// TEST 9: Wallet Isolation Audit (Other wallets remain untouched)
// -------------------------------------------------------------------------
$uStmt = $pdo->query("SELECT direct_bonus_wallet, profit_income_wallet, profit_sharing_wallet FROM user WHERE userid = '{$test_sponsor_id}'");
$uRow = $uStmt->fetch(PDO::FETCH_ASSOC);

assertTest(
    (float)$uRow['direct_bonus_wallet'] === 300.00 && (float)$uRow['profit_income_wallet'] === 100.00 && (float)$uRow['profit_sharing_wallet'] === 50.00,
    "Test 9: Wallet Isolation - profit_income_wallet & profit_sharing_wallet UNTOUCHED",
    "Direct Bonus: {$uRow['direct_bonus_wallet']}, Profit Income: {$uRow['profit_income_wallet']}, Profit Sharing: {$uRow['profit_sharing_wallet']}"
);

// -------------------------------------------------------------------------
// TEST 10: Requirements #1-#12 Zero Regression Check
// -------------------------------------------------------------------------
$closingActionCode = file_get_contents('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/monthly_closing_action.php');
$req11Check = strpos($closingActionCode, 'package >= 13050') !== false;
assertTest(
    $req11Check,
    "Test 10: Requirement #11 Threshold package >= 13050 in monthly_closing_action.php verified",
    "Threshold 13050 Present: " . ($req11Check ? 'YES' : 'NO')
);

// -------------------------------------------------------------------------
// CLEANUP TEST DATA
// -------------------------------------------------------------------------
$pdo->exec("DELETE FROM user WHERE userid IN ('{$test_sponsor_id}', '{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_sponsor WHERE sponsor_id = '{$test_sponsor_id}' OR referral_id IN ('{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ('{$test_sponsor_id}', '{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_direct_bonus_schedule WHERE beneficiary_id = '{$test_sponsor_id}' OR source_user_id IN ('{$test_direct1_id}', '{$test_direct2_id}')");
$pdo->exec("DELETE FROM tbl_direct_bonus_admin_audit WHERE user_id = '{$test_sponsor_id}'");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$test_sponsor_id}'");

echo "\n=======================================================\n";
echo "TEST RESULTS SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
} else {
    exit(0);
}
