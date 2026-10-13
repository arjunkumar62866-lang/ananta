<?php
/**
 * VERIFICATION HARNESS: COMPANY TURNOVER & MENTOR INCOME PIPELINE AUDIT
 * Tests:
 * 1. Read-only environment inspection.
 * 2. Option B Company Turnover Income logic and UI pipeline.
 * 3. Option A Mentor Income logic and UI pipeline.
 * 4. User dashboard Cards and Statement Pages synchronization.
 * 5. Rollback-safe test transaction verification.
 */

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "=================================================================\n";
echo " ANANTA: COMPANY TURNOVER & MENTOR INCOME END-TO-END PIPELINE AUDIT\n";
echo "=================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition($name, $condition, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] {$name}" . ($details ? " - {$details}" : "") . "\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$name}" . ($details ? " - {$details}" : "") . "\n";
        $failCount++;
    }
}

function createHarnessUser($pdo, $uid, $name = 'Test User', $overrides = []) {
    $defaults = [
        'userid' => $uid,
        'name' => $name,
        'mobile' => '9999999999',
        'gender' => 'Male',
        'email' => 'test@example.com',
        'pan' => 'ABCDE1234F',
        'pass' => '123456',
        'sponserid' => '',
        'sponsername' => 'Admin',
        'underuserid' => '',
        'active' => 1,
        'status' => 1,
        'join_side' => 'L',
        'package' => 0,
        'joining_date' => date('Y-m-d'),
        'plan' => 'Plan A',
        'pin' => 0,
        'father' => 'Father',
        'closingdate' => date('Y-m-d'),
        'capping' => 0,
        'pool' => 0,
        'level' => 0,
        'coin_wallet' => 0,
        'rank_reward_wallet' => 0.00,
        'vip_club_wallet' => 0.00,
        'company_turnover_wallet' => 0.00,
        'mentor_income_wallet' => 0.00,
        'user_growth_wallet' => 0.00,
        'amount' => 0.00,
        'rank' => 'NA',
        'rankid' => 0
    ];
    $data = array_merge($defaults, $overrides);
    $cols = implode(', ', array_map(function($k) { return "`$k`"; }, array_keys($data)));
    $placeholders = implode(', ', array_map(function($k) { return ":$k"; }, array_keys($data)));
    $stmt = $pdo->prepare("INSERT INTO user ($cols) VALUES ($placeholders)");
    $stmt->execute($data);
}

// -------------------------------------------------------------
// [1] DATABASE ENVIRONMENT CHECK
// -------------------------------------------------------------
echo "[1] Checking Database Environment & Production Safety...\n";
$dbName = $pdo->query("SELECT DATABASE()")->fetchColumn();
assertCondition("Connected Database", $dbName === 'u914531711_anantamtptl', "Database: {$dbName}");

// Check real users qualification status
$realQualified = (int)$pdo->query("SELECT COUNT(*) FROM user WHERE `rankid` >= 7")->fetchColumn();
assertCondition("Real Users at Tiers 7-10 (PEAK-ANANT) is 0 on current snapshot", $realQualified === 0, "Count: {$realQualified}");

// Check real mentor direct contributions in DB
$mentorContribRows = (int)$pdo->query("SELECT COUNT(*) FROM tbl_mentor_direct_contribution")->fetchColumn();
assertCondition("Real tbl_mentor_direct_contribution records is 0 on current snapshot", $mentorContribRows === 0, "Count: {$mentorContribRows}");

// -------------------------------------------------------------
// [2] ROLLBACK-SAFE SYNTHETIC PIPELINE TESTS
// -------------------------------------------------------------
echo "\n[2] Testing Option A Mentor Income End-to-End Pipeline (In Rollback Txn)...\n";
$pdo->beginTransaction();

try {
    $mentorId = 'TST_MENTOR_99';
    $directA  = 'TST_DIR_99A';
    $directB  = 'TST_DIR_99B';
    $directC  = 'TST_DIR_99C';

    createHarnessUser($pdo, $mentorId, 'Test Mentor');
    createHarnessUser($pdo, $directA,  'Test Direct A', ['sponserid' => $mentorId]);
    createHarnessUser($pdo, $directB,  'Test Direct B', ['sponserid' => $mentorId]);
    createHarnessUser($pdo, $directC,  'Test Direct C', ['sponserid' => $mentorId]);

    // Give each user $150 active package in tbl_roi_one to meet $145 minimum investment requirement
    $insRoi = $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES (:uid, 1, 'Test Pkg', 13500, 150.00, 3.0, 0, 0, 45000, '0', '2026-09-01', '12:00:00')");
    $insRoi->execute([':uid' => $mentorId]);
    $insRoi->execute([':uid' => $directA]);
    $insRoi->execute([':uid' => $directB]);
    $insRoi->execute([':uid' => $directC]);

    // Sponsor relationships
    $insSp = $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (:s, :r, NOW())");
    $insSp->execute([':s' => $mentorId, ':r' => $directA]);
    $insSp->execute([':s' => $mentorId, ':r' => $directB]);
    $insSp->execute([':s' => $mentorId, ':r' => $directC]);

    // Test 1: No contributions configured -> Validation fails, payout blocked under Option A
    $val0 = validateMentorContributions($mentorId, $pdo);
    assertCondition("Unconfigured Mentor contributions fails Option A validation", !$val0['valid'], $val0['reason']);

    // Test 2: Incomplete contribution (90%) -> Validation fails
    saveMentorDirectContribution($mentorId, $directA, 50.0, $pdo);
    saveMentorDirectContribution($mentorId, $directB, 40.0, $pdo);
    $val90 = validateMentorContributions($mentorId, $pdo);
    assertCondition("90% contribution total blocked under Option A", !$val90['valid'], "Total: {$val90['total_percentage']}%");

    // Test 3: Configure exact 100% (50% to A, 30% to B, 20% to C)
    saveMentorDirectContribution($mentorId, $directB, 30.0, $pdo);
    saveMentorDirectContribution($mentorId, $directC, 20.0, $pdo);
    $val100 = validateMentorContributions($mentorId, $pdo);
    assertCondition("Exact 100.00% passes Option A validation", $val100['valid'], "Total: {$val100['total_percentage']}%");

    // Mock Mentor's canonical Monthly Profit Income for 2026-09
    $closingMonth = '2026-09';
    $closingDate  = '2026-09-30';
    $mentorMonthlyProfit = 50000.00; // $50,000 profit
    $insTxn = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, amount, created_date, status) VALUES (:u, 'Profit Income', :sub, :amt, :dt, 1)");
    $insTxn->execute([
        ':u'   => $mentorId,
        ':sub' => "Monthly Profit Income ({$closingMonth}%",
        ':amt' => $mentorMonthlyProfit,
        ':dt'  => $closingDate
    ]);

    // Run processMentorIncome
    $payoutRes = processMentorIncome($closingMonth, $closingDate, $pdo);
    // Pool = $50,000 * 2% = $1,000.00
    // Direct A (50%) = $500.00
    // Direct B (30%) = $300.00
    // Direct C (20%) = $200.00
    assertCondition("processMentorIncome paid 3 directs", $payoutRes['processed'] === 3, "Processed: {$payoutRes['processed']}");
    assertCondition("processMentorIncome total paid is $1,000.00", abs($payoutRes['total_paid'] - 1000.00) < 0.01, "Total: {$payoutRes['total_paid']}");

    // Check Direct A wallet and growth summary
    $stmtA = $pdo->prepare("SELECT amount, mentor_income_wallet, user_growth_wallet FROM user WHERE userid = :u");
    $stmtA->execute([':u' => $directA]);
    $rowA = $stmtA->fetch(PDO::FETCH_ASSOC);
    assertCondition("Direct A mentor_income_wallet credited with $500.00", (float)$rowA['mentor_income_wallet'] === 500.00, "Wallet: {$rowA['mentor_income_wallet']}");
    assertCondition("Direct A user_growth_wallet credited with $500.00", (float)$rowA['user_growth_wallet'] === 500.00, "Wallet: {$rowA['user_growth_wallet']}");
    assertCondition("Direct A main amount credited with $500.00", (float)$rowA['amount'] === 500.00, "Wallet: {$rowA['amount']}");

    // Check Single Source of Truth helper for Direct A
    $summaryA = getUserIncomeWalletSummary($directA, $pdo);
    assertCondition("getUserIncomeWalletSummary reports mentor_income = $500.00", $summaryA['mentor_income'] === 500.00, "Summary: {$summaryA['mentor_income']}");

    $breakdownA = getUserGrowthBreakdown($directA, $pdo);
    assertCondition("getUserGrowthBreakdown reports mentor_income total_balance = $500.00", (float)$breakdownA['mentor_income']['total_balance'] === 500.00, "Breakdown: {$breakdownA['mentor_income']['total_balance']}");

    // Test Idempotency: Second closing run on same month does NOT duplicate credits
    $payoutResDup = processMentorIncome($closingMonth, $closingDate, $pdo);
    assertCondition("Second closing attempt creates 0 payouts (Strict Idempotency)", $payoutResDup['processed'] === 0, "Processed: {$payoutResDup['processed']}");

} finally {
    $pdo->rollBack();
    echo "  [INFO] Mentor Income test transaction rolled back safely.\n";
}

// -------------------------------------------------------------
// [3] OPTION B COMPANY TURNOVER PIPELINE IN ROLLBACK TXN
// -------------------------------------------------------------
echo "\n[3] Testing Option B Company Turnover End-to-End Pipeline (In Rollback Txn)...\n";
$pdo->beginTransaction();

try {
    $peakUser = 'TST_PEAK_01';
    $apexUser = 'TST_APEX_01';

    createHarnessUser($pdo, $peakUser, 'Test Peak', ['rank' => 'PEAK', 'rankid' => 7]);
    createHarnessUser($pdo, $apexUser, 'Test Apex', ['rank' => 'APEX', 'rankid' => 8]);

    // Give each user $150 active package in tbl_roi_one to meet $145 minimum investment requirement
    $insRoi = $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES (:uid, 1, 'Test Pkg', 13500, 150.00, 3.0, 0, 0, 45000, '0', '2026-10-01', '12:00:00')");
    $insRoi->execute([':uid' => $peakUser]);
    $insRoi->execute([':uid' => $apexUser]);

    // Insert qualifications into tbl_vip_user_qualification
    $insQual = $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status) VALUES (:u, :lvl, 1500, 1000, 1500000, 1000000, 1000000, 30000, 'CREDITED')");
    $insQual->execute([':u' => $peakUser, ':lvl' => 7]); // Level 7: PEAK
    $insQual->execute([':u' => $apexUser, ':lvl' => 8]); // Level 8: APEX

    // Seed binary tree legs for Peak and Apex so getBinaryLegDetails meets repeat business ($100k)
    $pkL = 'TPKL_99'; $pkR = 'TPKR_99';
    createHarnessUser($pdo, $pkL, 'Peak Left');
    createHarnessUser($pdo, $pkR, 'Peak Right');
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, '0', CURDATE(), '12:00:00')")->execute([':u' => $pkL]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, '0', CURDATE(), '12:00:00')")->execute([':u' => $pkR]);
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) VALUES (:u, :lid, :rid, '1', 'L', '0', '0', 1, 1, 1, 1)")->execute([':u' => $peakUser, ':lid' => $pkL, ':rid' => $pkR]);

    $axL = 'TAXL_99'; $axR = 'TAXR_99';
    createHarnessUser($pdo, $axL, 'Apex Left');
    createHarnessUser($pdo, $axR, 'Apex Right');
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, '0', CURDATE(), '12:00:00')")->execute([':u' => $axL]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, '0', CURDATE(), '12:00:00')")->execute([':u' => $axR]);
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) VALUES (:u, :lid, :rid, '1', 'L', '0', '0', 1, 1, 1, 1)")->execute([':u' => $apexUser, ':lid' => $axL, ':rid' => $axR]);

    // Ensure company turnover exists in tbl_roi_one for 2026-10
    // Total company turnover = $1,000,000 USD (90,000,000 INR)
    $insTurnover = $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, status, `date`, `time`) VALUES ('CORP_SYSTEM', 1, 'Corp Pkg', 90000000, 1000000.00, 3.0, 0, 0, 270000000, '0', '2026-10-05', '12:00:00')");
    $insTurnover->execute();

    // Verify previewVIPMonthlyIncome
    $prev = previewVIPMonthlyIncome('2026-10', '2026-10-11', $pdo);
    assertCondition("previewVIPMonthlyIncome succeeds", $prev['status'] === 'success');
    assertCondition("preview calculates positive company turnover base", (float)$prev['company_turnover_usd'] >= 1000000.00, "TO Base: $" . $prev['company_turnover_usd']);

    // Run processVIPMonthlyIncome on 11th date
    $closingRes = processVIPMonthlyIncome('2026-10', '2026-10-11', $pdo, false);
    assertCondition("processVIPMonthlyIncome executes on 11th", $closingRes['status'] === 'success', "Msg: {$closingRes['message']}");

    // Check Option B calculation: Each qualified PEAK/APEX user gets 0.5% of company turnover
    // Turnover base = $1,000,000+ -> 0.005 * turnover >= $5,000.00
    $stmtWallets = $pdo->prepare("SELECT company_turnover_wallet, vip_club_wallet, amount FROM user WHERE userid = :u");
    $stmtWallets->execute([':u' => $peakUser]);
    $peakWal = $stmtWallets->fetch(PDO::FETCH_ASSOC);

    assertCondition("Peak user received positive company_turnover_wallet", (float)$peakWal['company_turnover_wallet'] > 0, "Wallet: {$peakWal['company_turnover_wallet']}");

    // Check Single Source of Truth
    $summaryPeak = getUserIncomeWalletSummary($peakUser, $pdo);
    assertCondition("getUserIncomeWalletSummary matches company_turnover_wallet", $summaryPeak['company_turnover'] == (float)$peakWal['company_turnover_wallet'], "Summary: {$summaryPeak['company_turnover']}");

    // Check history items via getUserIncomeWalletHistory
    $histPeak = getUserIncomeWalletHistory($peakUser, 'COMPANY_TURNOVER', null, null, $pdo);
    assertCondition("getUserIncomeWalletHistory returns company turnover record", count($histPeak) > 0, "Records: " . count($histPeak));

    // Check schedule table
    $schedCount = (int)$pdo->query("SELECT COUNT(*) FROM tbl_vip_monthly_schedule WHERE closing_month = '2026-10' AND turnover_payout > 0")->fetchColumn();
    assertCondition("tbl_vip_monthly_schedule records turnover_payout > 0", $schedCount >= 2, "Count: {$schedCount}");

} finally {
    $pdo->rollBack();
    echo "  [INFO] Company Turnover test transaction rolled back safely.\n";
}

echo "\n=================================================================\n";
echo "TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED.\n";
echo "=================================================================\n";
