<?php
/**
 * Test Suite: Verify $11 Activation Requirement for Investment/Package Buy
 */
chdir(__DIR__ . '/..');
require_once 'dashboard/user1/common/connection.php';
require_once 'dashboard/user1/common/db_method.php';

echo "=== AUDIT & VERIFICATION: INVESTMENT / PACKAGE BUY ACTIVATION RESTRICTION ===\n\n";

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

// 1. Setup Test Users in DB with rollback/cleanup (max 10-15 chars)
$suf = substr((string)time(), -4);
$inactUser = "INA_" . $suf;
$actUser   = "ACT_" . $suf;
$expUser   = "EXP_" . $suf;

// Clean up if existed
$pdo->exec("DELETE FROM user WHERE userid IN ('{$inactUser}', '{$actUser}', '{$expUser}')");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ('{$inactUser}', '{$actUser}', '{$expUser}')");

function insertTestUser($pdo, $uid, $name, $active, $startDt, $expDt, $bal = 500.00) {
    $stmt = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet,
            bonus_30_wallet, pin_wallet, activation_start_date, activation_expiry_date
        ) VALUES (
            :uid, :name, :active, 0, '', '9999999999', 'M', 'test@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', :bal, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            0.00, 0.00, 0.00, 0.00, 0.00, 0.00,
            0.00, :bal, :start_dt, :exp_dt
        )
    ");
    $stmt->execute([
        ':uid'      => $uid,
        ':name'     => $name,
        ':active'   => $active,
        ':bal'      => $bal,
        ':start_dt' => $startDt,
        ':exp_dt'   => $expDt
    ]);
}

// Inactive User (active = 0, no activation date, $500 balance)
insertTestUser($pdo, $inactUser, 'Inactive Test User', '0', null, null, 500.00);

// Active User (active = 1, valid expiry date 4 years in future, $500 balance)
$actExp = date('Y-m-d H:i:s', strtotime('+4 years'));
insertTestUser($pdo, $actUser, 'Active Test User', '1', date('Y-m-d H:i:s'), $actExp, 500.00);

// Expired User (active = 0, expired 1 day ago, $500 balance)
$expPast = date('Y-m-d H:i:s', strtotime('-1 day'));
$expStart = date('Y-m-d H:i:s', strtotime('-4 years'));
insertTestUser($pdo, $expUser, 'Expired Test User', '0', $expStart, $expPast, 500.00);

// -------------------------------------------------------------------
// TEST CASE 1: Inactive User attempts Package Buy
// -------------------------------------------------------------------
echo "--- TEST CASE 1: Inactive User (Activation NOT Completed) ---\n";
$stInact = getUserAccountActivationStatus($inactUser, $pdo);
assertCondition(!$stInact['is_active'], "1.1 Activation status is identified as INACTIVE", "Status: {$stInact['status']}");

$balBeforeInact = (float)$pdo->query("SELECT deposite_wallet FROM user WHERE userid = '{$inactUser}'")->fetchColumn();
$resInact = processAnantaPackageInvestment($inactUser, 'BASIC', 200.00, $pdo);

assertCondition($resInact['status'] === 'error', "1.2 Investment purchase is completely REJECTED for inactive user", "Error returned");
assertCondition(
    strpos($resInact['message'], 'Please complete your $11 activation before purchasing an investment/package') !== false,
    "1.3 Rejection returns exact required error message",
    $resInact['message']
);

$balAfterInact = (float)$pdo->query("SELECT deposite_wallet FROM user WHERE userid = '{$inactUser}'")->fetchColumn();
assertCondition($balBeforeInact === $balAfterInact, "1.4 Wallet balance is NOT deducted", "Bal: {$balAfterInact}");

$invCountInact = (int)$pdo->query("SELECT COUNT(*) FROM tbl_roi_one WHERE user_id = '{$inactUser}'")->fetchColumn();
assertCondition($invCountInact === 0, "1.5 Zero records created in tbl_roi_one for inactive user", "Count: {$invCountInact}");

// -------------------------------------------------------------------
// TEST CASE 2: Expired User attempts Package Buy
// -------------------------------------------------------------------
echo "\n--- TEST CASE 2: Expired User (Activation Expired) ---\n";
$stExp = getUserAccountActivationStatus($expUser, $pdo);
assertCondition(!$stExp['is_active'], "2.1 Activation status is identified as EXPIRED/INACTIVE", "Status: {$stExp['status']}");

$resExp = processAnantaPackageInvestment($expUser, 'BASIC', 200.00, $pdo);
assertCondition($resExp['status'] === 'error', "2.2 Investment purchase is completely REJECTED for expired user");
assertCondition(
    strpos($resExp['message'], 'Please complete your $11 activation before purchasing an investment/package') !== false,
    "2.3 Rejection returns exact required error message for expired user",
    $resExp['message']
);

// -------------------------------------------------------------------
// TEST CASE 3: Active User attempts Package Buy
// -------------------------------------------------------------------
echo "\n--- TEST CASE 3: Active User (Activation COMPLETED) ---\n";
$stAct = getUserAccountActivationStatus($actUser, $pdo);
assertCondition($stAct['is_active'], "3.1 Activation status is identified as ACTIVE", "Status: {$stAct['status']}, Remaining: {$stAct['remaining_days']} days");

$balBeforeAct = (float)$pdo->query("SELECT deposite_wallet FROM user WHERE userid = '{$actUser}'")->fetchColumn();
$resAct = processAnantaPackageInvestment($actUser, 'BASIC', 200.00, $pdo);

assertCondition($resAct['status'] === 'success', "3.2 Investment purchase is ALLOWED for active user", "Message: {$resAct['message']}");

$balAfterAct = (float)$pdo->query("SELECT deposite_wallet FROM user WHERE userid = '{$actUser}'")->fetchColumn();
assertCondition(abs($balAfterAct - ($balBeforeAct - 200.00)) < 0.01, "3.3 Wallet balance correctly deducted ($500 -> $300)", "New Bal: $" . $balAfterAct);

$invRowAct = $pdo->query("SELECT * FROM tbl_roi_one WHERE user_id = '{$actUser}'")->fetch(PDO::FETCH_ASSOC);
assertCondition(!empty($invRowAct), "3.4 Investment record correctly created in tbl_roi_one", "ID: {$invRowAct['id']}, Package: {$invRowAct['package_code']}");
assertCondition((float)$invRowAct['real_fund_usd'] === 200.00, "3.5 Real Fund USD recorded as $200.00");
assertCondition((float)$invRowAct['package'] === 18000.00, "3.6 Base INR package recorded as ₹18,000.00 ($200 * ₹90)");

// Cleanup test records
$pdo->exec("DELETE FROM user WHERE userid IN ('{$inactUser}', '{$actUser}', '{$expUser}')");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id IN ('{$inactUser}', '{$actUser}', '{$expUser}')");

echo "\n=======================================================\n";
echo "SUMMARY: Passed: {$passCount}, Failed: {$failCount}\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
}
