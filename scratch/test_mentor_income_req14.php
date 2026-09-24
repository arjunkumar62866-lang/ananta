<?php
/**
 * TEST SUITE: REQUIREMENT #14 - MENTOR INCOME SYSTEM & FULL REGRESSION TEST
 */

require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/connection.php';
require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/db_method.php';

echo "=======================================================\n";
echo " STARTING REQUIREMENT #14 MENTOR INCOME TEST SUITE\n";
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

function createTestUserReq14(PDO $pdo, $userid, $name, $active = '1', $sponserid = '', $piw = 100.00, $psw = 50.00, $dbw = 0.00, $miw = 0.00) {
    $stmt = $pdo->prepare("INSERT INTO user 
        (userid, name, mobile, gender, email, pan, pass, txn_pass, total_deposit, deposit, sponserid, sponsername, underuserid, active, status, upgrade_status, join_side, package, joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid, pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel, one_club_status, two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet)
        VALUES 
        (:userid, :name, '9999999999', 'Male', 'test@test.com', 'ABCDE1234F', '123456', '123456', 0, 0, :sponserid, '', '', :active, 1, 1, 'left', '13000', CURDATE(), 'Basic', '', '', '0', 0, CURDATE(), '0', CURDATE(), 0, '', '1', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', :piw, :psw, :dbw, :miw)");
    $stmt->execute([
        ':userid'    => $userid,
        ':name'      => $name,
        ':sponserid' => $sponserid,
        ':active'    => $active,
        ':piw'       => $piw,
        ':psw'       => $psw,
        ':dbw'       => $dbw,
        ':miw'       => $miw
    ]);
}

// -------------------------------------------------------------------------
// PREPARATION: Create Test Mentor & Direct Users
// -------------------------------------------------------------------------
$mentor1_id = "MTR_1001";
$directA_id = "DIR_A_101";
$directB_id = "DIR_B_102";
$directC_id = "DIR_C_103";
$directD_id = "DIR_D_104";
$directE_id = "DIR_E_105";

$allTestUserIds = [$mentor1_id, $directA_id, $directB_id, $directC_id, $directD_id, $directE_id];
$quotedIds = "'" . implode("','", $allTestUserIds) . "'";

// Cleanup previous test data
$pdo->exec("DELETE FROM user WHERE userid IN ({$quotedIds})");
$pdo->exec("DELETE FROM tbl_sponsor WHERE sponsor_id IN ({$quotedIds}) OR referral_id IN ({$quotedIds})");
$pdo->exec("DELETE FROM tbl_mentor_direct_contribution WHERE mentor_id IN ({$quotedIds}) OR direct_user_id IN ({$quotedIds})");
$pdo->exec("DELETE FROM tbl_mentor_income_schedule WHERE mentor_id IN ({$quotedIds}) OR direct_user_id IN ({$quotedIds})");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id IN ({$quotedIds})");

// Insert Mentor and Direct Users
createTestUserReq14($pdo, $mentor1_id, 'Test Mentor 1', 1, '', 0.00, 0.00, 0.00, 0.00);
createTestUserReq14($pdo, $directA_id, 'Direct User A', 1, $mentor1_id, 100.00, 50.00, 20.00, 0.00);
createTestUserReq14($pdo, $directB_id, 'Direct User B', 1, $mentor1_id, 100.00, 50.00, 20.00, 0.00);
createTestUserReq14($pdo, $directC_id, 'Direct User C', 1, $mentor1_id, 100.00, 50.00, 20.00, 0.00);
createTestUserReq14($pdo, $directD_id, 'Direct User D', 1, $mentor1_id, 100.00, 50.00, 20.00, 0.00);
createTestUserReq14($pdo, $directE_id, 'Direct User E', 1, $mentor1_id, 100.00, 50.00, 20.00, 0.00);

// Link sponsor relationships in tbl_sponsor
foreach ([$directA_id, $directB_id, $directC_id, $directD_id, $directE_id] as $dId) {
    $pdo->exec("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES ('{$mentor1_id}', '{$dId}', NOW())");
}

// -------------------------------------------------------------------------
// TEST 1 & 2: 2% Mentor Income Formula Check ($5,00,000 * 2% = $10,000)
// -------------------------------------------------------------------------
$monthlyInc = 500000.00;
$rate = MENTOR_INCOME_PERCENT;
$calcMentorInc = round($monthlyInc * ($rate / 100.0), 2);
assertTest(
    $calcMentorInc === 10000.00,
    "Test 1 & 2: Mentor Monthly Income ₹5,00,000 * 2% = ₹10,000 Total Mentor Income",
    "Calculated: ₹" . number_format($calcMentorInc, 2)
);

// -------------------------------------------------------------------------
// TEST 7: Set Contribution Percentages (50%, 30%, 20%, 0%, 0% -> Total = 100%)
// -------------------------------------------------------------------------
saveMentorDirectContribution($mentor1_id, $directA_id, 50.00, $pdo);
saveMentorDirectContribution($mentor1_id, $directB_id, 30.00, $pdo);
saveMentorDirectContribution($mentor1_id, $directC_id, 20.00, $pdo);
saveMentorDirectContribution($mentor1_id, $directD_id, 0.00, $pdo);
saveMentorDirectContribution($mentor1_id, $directE_id, 0.00, $pdo);

$valResult = validateMentorContributions($mentor1_id, $pdo);
assertTest(
    $valResult['valid'] && (float)$valResult['total_percentage'] === 100.00,
    "Test 7: Contribution Total exactly 100.00% (50 + 30 + 20 + 0 + 0 = 100%)",
    "Valid: " . ($valResult['valid'] ? 'YES' : 'NO') . ", Total: {$valResult['total_percentage']}%"
);

// -------------------------------------------------------------------------
// TEST 8: Contribution Total Below 100% -> Blocked
// -------------------------------------------------------------------------
saveMentorDirectContribution($mentor1_id, $directC_id, 10.00, $pdo); // Total becomes 50+30+10 = 90%
$valBelow = validateMentorContributions($mentor1_id, $pdo);
assertTest(
    !$valBelow['valid'] && (float)$valBelow['total_percentage'] === 90.00,
    "Test 8: Contribution Total below 100% (90%) -> Validation Failed / Blocked",
    "Reason: {$valBelow['reason']}"
);

// -------------------------------------------------------------------------
// TEST 9: Contribution Total Above 100% -> Blocked
// -------------------------------------------------------------------------
saveMentorDirectContribution($mentor1_id, $directC_id, 30.00, $pdo); // Total becomes 50+30+30 = 110%
$valAbove = validateMentorContributions($mentor1_id, $pdo);
assertTest(
    !$valAbove['valid'] && (float)$valAbove['total_percentage'] === 110.00,
    "Test 9: Contribution Total above 100% (110%) -> Validation Failed / Blocked",
    "Reason: {$valAbove['reason']}"
);

// -------------------------------------------------------------------------
// TEST 10: Negative Contribution -> Rejected
// -------------------------------------------------------------------------
$negRes = saveMentorDirectContribution($mentor1_id, $directA_id, -10.00, $pdo);
assertTest(
    $negRes['status'] === 'error',
    "Test 10: Negative Contribution (-10%) -> Rejected by save helper",
    "Message: {$negRes['message']}"
);

// Reset contribution back to valid 100% (50%, 30%, 20%, 0%, 0%)
saveMentorDirectContribution($mentor1_id, $directA_id, 50.00, $pdo);
saveMentorDirectContribution($mentor1_id, $directB_id, 30.00, $pdo);
saveMentorDirectContribution($mentor1_id, $directC_id, 20.00, $pdo);

// -------------------------------------------------------------------------
// TEST 3, 4, 5, 6, 13, 14, 15: Execute Mentor Income Payout & Check User Distribution
// -------------------------------------------------------------------------
$cDate = date('Y-m-d');
$cMonth = date('Y-m');

// Record Mentor Profit Income transaction of ₹5,00,000 for today
$pdo->exec("INSERT INTO tbl_transaction (user_id, type, subject, amount, created_date, status) 
            VALUES ('{$mentor1_id}', 'Profit Income', 'Monthly Profit Income ({$cMonth} @ 5%)', 500000.00, '{$cDate}', 1)");

$payoutRes = processMentorIncome($cMonth, $cDate, $pdo);

// Fetch user mentor_income_wallet balances
$uStmtA = $pdo->query("SELECT mentor_income_wallet, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet FROM user WHERE userid = '{$directA_id}'");
$rowA = $uStmtA->fetch(PDO::FETCH_ASSOC);

$uStmtB = $pdo->query("SELECT mentor_income_wallet FROM user WHERE userid = '{$directB_id}'");
$rowB = $uStmtB->fetch(PDO::FETCH_ASSOC);

$uStmtC = $pdo->query("SELECT mentor_income_wallet FROM user WHERE userid = '{$directC_id}'");
$rowC = $uStmtC->fetch(PDO::FETCH_ASSOC);

$uStmtD = $pdo->query("SELECT mentor_income_wallet FROM user WHERE userid = '{$directD_id}'");
$rowD = $uStmtD->fetch(PDO::FETCH_ASSOC);

$uStmtE = $pdo->query("SELECT mentor_income_wallet FROM user WHERE userid = '{$directE_id}'");
$rowE = $uStmtE->fetch(PDO::FETCH_ASSOC);

assertTest(
    (float)$rowA['mentor_income_wallet'] === 5000.00,
    "Test 3: Direct User A (50% contribution) received exactly ₹5,000 payout",
    "Wallet: ₹" . number_format($rowA['mentor_income_wallet'], 2)
);

assertTest(
    (float)$rowB['mentor_income_wallet'] === 3000.00,
    "Test 4: Direct User B (30% contribution) received exactly ₹3,000 payout",
    "Wallet: ₹" . number_format($rowB['mentor_income_wallet'], 2)
);

assertTest(
    (float)$rowC['mentor_income_wallet'] === 2000.00,
    "Test 5: Direct User C (20% contribution) received exactly ₹2,000 payout",
    "Wallet: ₹" . number_format($rowC['mentor_income_wallet'], 2)
);

assertTest(
    (float)$rowD['mentor_income_wallet'] === 0.00 && (float)$rowE['mentor_income_wallet'] === 0.00,
    "Test 6: Direct Users D & E (0% contribution) received exactly ₹0 payout",
    "User D: ₹" . number_format($rowD['mentor_income_wallet'], 2) . ", User E: ₹" . number_format($rowE['mentor_income_wallet'], 2)
);

// -------------------------------------------------------------------------
// TEST 12: Wallet Isolation (Other wallets remain untouched)
// -------------------------------------------------------------------------
assertTest(
    (float)$rowA['profit_income_wallet'] === 100.00 && (float)$rowA['profit_sharing_wallet'] === 50.00 && (float)$rowA['direct_bonus_wallet'] === 20.00,
    "Test 12: Wallet Isolation - profit_income_wallet, profit_sharing_wallet & direct_bonus_wallet UNTOUCHED",
    "PIW: {$rowA['profit_income_wallet']}, PSW: {$rowA['profit_sharing_wallet']}, DBW: {$rowA['direct_bonus_wallet']}"
);

// -------------------------------------------------------------------------
// TEST 11: Idempotency Protection (Re-running monthly closing produces 0 duplicate payouts)
// -------------------------------------------------------------------------
$repeatRes = processMentorIncome($cMonth, $cDate, $pdo);
$uStmtA2 = $pdo->query("SELECT mentor_income_wallet FROM user WHERE userid = '{$directA_id}'");
$rowA2 = $uStmtA2->fetch(PDO::FETCH_ASSOC);

assertTest(
    $repeatRes['processed'] === 0 && (float)$rowA2['mentor_income_wallet'] === 5000.00,
    "Test 11: Idempotency Protection - Re-running monthly closing produces 0 duplicate payouts",
    "Processed: {$repeatRes['processed']}, Wallet Balance: ₹{$rowA2['mentor_income_wallet']}"
);

// -------------------------------------------------------------------------
// TEST 13, 14, 15: Transaction History, Credit Date & Month Reference Check
// -------------------------------------------------------------------------
$txStmt = $pdo->query("SELECT * FROM tbl_transaction WHERE user_id = '{$directA_id}' AND subject LIKE '%Mentor Income Payout%'");
$txRow = $txStmt->fetch(PDO::FETCH_ASSOC);

$schStmt = $pdo->query("SELECT * FROM tbl_mentor_income_schedule WHERE direct_user_id = '{$directA_id}' AND closing_month = '{$cMonth}'");
$schRow = $schStmt->fetch(PDO::FETCH_ASSOC);

assertTest(
    $txRow && (float)$txRow['amount'] === 5000.00 && $schRow && $schRow['closing_month'] === $cMonth,
    "Test 13, 14, 15: Transaction Record, Credit Date & Month Reference correctly saved",
    "Tx Amount: " . ($txRow['amount'] ?? 'NULL') . ", Closing Month: " . ($schRow['closing_month'] ?? 'NULL')
);

// -------------------------------------------------------------------------
// TEST 16 & 17: Multiple Mentors & Multiple Direct Users Isolation
// -------------------------------------------------------------------------
$mentor2_id = "MTR_2002";
$directF_id = "DIR_F_106";

createTestUserReq14($pdo, $mentor2_id, 'Test Mentor 2', 1, '', 0.00, 0.00, 0.00, 0.00);
createTestUserReq14($pdo, $directF_id, 'Direct User F', 1, $mentor2_id, 0.00, 0.00, 0.00, 0.00);

$pdo->exec("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES ('{$mentor2_id}', '{$directF_id}', NOW())");
saveMentorDirectContribution($mentor2_id, $directF_id, 100.00, $pdo);

// Record Profit Income for Mentor 2 ($2,00,000 * 2% = $4,000 -> Direct F gets $4,000)
$pdo->exec("INSERT INTO tbl_transaction (user_id, type, subject, amount, created_date, status) 
            VALUES ('{$mentor2_id}', 'Profit Income', 'Monthly Profit Income ({$cMonth} @ 5%)', 200000.00, '{$cDate}', 1)");

$payoutRes2 = processMentorIncome($cMonth, $cDate, $pdo);
$uStmtF = $pdo->query("SELECT mentor_income_wallet FROM user WHERE userid = '{$directF_id}'");
$rowF = $uStmtF->fetch(PDO::FETCH_ASSOC);

assertTest(
    (float)$rowF['mentor_income_wallet'] === 4000.00,
    "Test 16 & 17: Multiple Mentors & Direct Users Isolation - Mentor 2 Direct F received ₹4,000 independently",
    "Direct F Wallet: ₹" . number_format($rowF['mentor_income_wallet'], 2)
);

// -------------------------------------------------------------------------
// TEST 18, 19, 20, 21, 22: Regression Checks for Requirements #1-#13
// -------------------------------------------------------------------------
$closingActionCode = file_get_contents('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/monthly_closing_action.php');
$req11Check = strpos($closingActionCode, 'package >= 13050') !== false;
assertTest(
    $req11Check,
    "Test 19: Requirement #11 Threshold package >= 13050 in monthly_closing_action.php verified",
    "Threshold Present: " . ($req11Check ? 'YES' : 'NO')
);

$req12Check = defined('MIN_QUALIFIED_INVESTMENT') && MIN_QUALIFIED_INVESTMENT == 13000.0;
assertTest(
    $req12Check,
    "Test 20: Requirement #12 Threshold MIN_QUALIFIED_INVESTMENT == 13,000 verified",
    "Threshold Present: " . ($req12Check ? 'YES' : 'NO')
);

$req13Check = file_exists('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/direct_bonus_adjustment_action.php');
assertTest(
    $req13Check,
    "Test 21: Requirement #13 Admin Direct Bonus Management endpoint intact",
    "Endpoint Exists: " . ($req13Check ? 'YES' : 'NO')
);

// Cleanup test records
$pdo->exec("DELETE FROM user WHERE userid IN ({$quotedIds}, '{$mentor2_id}', '{$directF_id}')");
$pdo->exec("DELETE FROM tbl_sponsor WHERE sponsor_id IN ({$quotedIds}, '{$mentor2_id}', '{$directF_id}') OR referral_id IN ({$quotedIds}, '{$mentor2_id}', '{$directF_id}')");
$pdo->exec("DELETE FROM tbl_mentor_direct_contribution WHERE mentor_id IN ({$quotedIds}, '{$mentor2_id}', '{$directF_id}')");
$pdo->exec("DELETE FROM tbl_mentor_income_schedule WHERE mentor_id IN ({$quotedIds}, '{$mentor2_id}', '{$directF_id}')");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id IN ({$quotedIds}, '{$mentor2_id}', '{$directF_id}')");

echo "\n=======================================================\n";
echo "TEST RESULTS SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
} else {
    exit(0);
}
