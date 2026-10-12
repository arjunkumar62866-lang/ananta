<?php
/**
 * Automated Verification Suite for Canonical 10-Rank Reward, VIP Club & Company Turnover System
 * Runs against verified local test database. Uses safe transactional assertions.
 */

require_once __DIR__ . '/../dashboard/admin/common/db_method.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "=================================================================\n";
echo "CANONICAL 10-RANK REWARD, VIP CLUB & COMPANY TURNOVER TEST SUITE\n";
echo "=================================================================\n\n";

// 1. Verify Database Connection
$dbName = $pdo->query("SELECT DATABASE()")->fetchColumn();
echo "[1] Database Identity Check: Connected to database: " . htmlspecialchars($dbName) . "\n";
if (empty($dbName)) {
    die("ERROR: No database connection.\n");
}

$testsPassed = 0;
$testsFailed = 0;

function assertCondition($name, $condition, $details = '') {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "  [PASS] {$name}" . ($details ? " - {$details}" : "") . "\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] {$name}" . ($details ? " - {$details}" : "") . "\n";
        $testsFailed++;
    }
}

// 2. Test Canonical 10-Level Configuration Matrix
echo "\n[2] Checking Canonical 10-Level Rank Matrix in tbl_vip_level_config...\n";
$stmt = $pdo->query("SELECT * FROM tbl_vip_level_config ORDER BY level_id ASC");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

$expectedMatrix = [
    1  => ['name' => 'PRIME', 'left_ids' => 30,    'right_ids' => 30,    'left_biz' => 4000.0,    'right_biz' => 4000.0,    'reward' => 200.0,    'rate' => 0.5, 'turnover' => 0],
    2  => ['name' => 'PRO',   'left_ids' => 60,    'right_ids' => 60,    'left_biz' => 10000.0,   'right_biz' => 10000.0,   'reward' => 500.0,    'rate' => 1.0, 'turnover' => 0],
    3  => ['name' => 'ELITE', 'left_ids' => 100,   'right_ids' => 100,   'left_biz' => 16000.0,   'right_biz' => 16000.0,   'reward' => 800.0,    'rate' => 1.0, 'turnover' => 0],
    4  => ['name' => 'CROWN', 'left_ids' => 200,   'right_ids' => 200,   'left_biz' => 30000.0,   'right_biz' => 30000.0,   'reward' => 2000.0,   'rate' => 1.0, 'turnover' => 0],
    5  => ['name' => 'ROYAL', 'left_ids' => 500,   'right_ids' => 500,   'left_biz' => 100000.0,  'right_biz' => 100000.0,  'reward' => 4000.0,   'rate' => 1.0, 'turnover' => 0],
    6  => ['name' => 'TITAN', 'left_ids' => 1200,  'right_ids' => 1200,  'left_biz' => 300000.0,  'right_biz' => 300000.0,  'reward' => 10000.0,  'rate' => 1.0, 'turnover' => 0],
    7  => ['name' => 'PEAK',  'left_ids' => 5000,  'right_ids' => 5000,  'left_biz' => 1000000.0, 'right_biz' => 1000000.0, 'reward' => 30000.0,  'rate' => 0.5, 'turnover' => 1],
    8  => ['name' => 'APEX',  'left_ids' => 12000, 'right_ids' => 12000, 'left_biz' => 2000000.0, 'right_biz' => 2000000.0, 'reward' => 70000.0,  'rate' => 0.5, 'turnover' => 1],
    9  => ['name' => 'ICON',  'left_ids' => 20000, 'right_ids' => 20000, 'left_biz' => 4000000.0, 'right_biz' => 4000000.0, 'reward' => 170000.0, 'rate' => 0.5, 'turnover' => 1],
    10 => ['name' => 'ANANT', 'left_ids' => 50000, 'right_ids' => 50000, 'left_biz' => 10000000.0,'right_biz' => 10000000.0,'reward' => 350000.0, 'rate' => 0.5, 'turnover' => 1],
];

assertCondition("Matrix row count is exactly 10", count($levels) === 10, "Found " . count($levels) . " rows");

foreach ($levels as $l) {
    $lvl = (int)$l['level_id'];
    $exp = $expectedMatrix[$lvl] ?? null;
    if ($exp) {
        $nameMatch = ($l['name'] === $exp['name']);
        $idMatch = ((int)$l['req_left_ids'] === $exp['left_ids'] && (int)$l['req_right_ids'] === $exp['right_ids']);
        $bizMatch = ((float)$l['req_left_business'] == $exp['left_biz'] && (float)$l['req_right_business'] == $exp['right_biz']);
        $rewMatch = ((float)$l['reward_amount'] == $exp['reward']);
        $rateMatch = ((float)$l['vip_income_rate'] == $exp['rate']);
        $toMatch = ((int)$l['has_turnover_share'] === $exp['turnover']);

        assertCondition("Level {$lvl} ({$exp['name']}) Configuration", 
            $nameMatch && $idMatch && $bizMatch && $rewMatch && $rateMatch && $toMatch,
            "Reward: \${$l['reward_amount']}, LeftBiz: \${$l['req_left_business']}, TO: {$l['has_turnover_share']}"
        );
    }
}

// Helper to create test user with valid schema defaults
function createTestUser($pdo, $uid, $name = 'Test User', $overrides = []) {
    $defaults = [
        'userid' => $uid,
        'name' => $name,
        'mobile' => '9999999999',
        'gender' => 'Male',
        'email' => 'test@example.com',
        'pan' => 'ABCDE1234F',
        'pass' => '123456',
        'sponserid' => '1290',
        'sponsername' => 'Admin',
        'underuserid' => '1290',
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
        'user_growth_wallet' => 0.00,
        'rank' => 'NA',
        'rankid' => 0
    ];
    $data = array_merge($defaults, $overrides);
    $cols = implode(', ', array_map(function($k) { return "`$k`"; }, array_keys($data)));
    $placeholders = implode(', ', array_map(function($k) { return ":$k"; }, array_keys($data)));
    $stmt = $pdo->prepare("INSERT INTO user ($cols) VALUES ($placeholders)");
    $stmt->execute($data);
}

// 3. Test Evaluation & Rank Reward Qualification (Isolated Transaction)
echo "\n[3] Testing Automatic Qualification & One-Time Rank Reward Crediting...\n";
$pdo->beginTransaction();
try {
    // Create dummy test user
    $testUser = 'TU_' . substr(time(), -6) . '_1';
    createTestUser($pdo, $testUser, 'Test Unit User', []);

    // Give user an active investment in tbl_roi_one (e.g. ₹15,000 / $166.67 > $145 min)
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:uid, 1, 'Unit Package', 15000, 166.67, 3.0, 0, 0, 45000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':uid' => $testUser]);

    // Test Case A: Seed 30 left members and 30 right members with $4,000 business each (₹360,000 each)
    $leftUsers = [];
    $rightUsers = [];
    for ($i = 1; $i <= 30; $i++) {
        $lUid = 'TL_' . substr(time(), -4) . '_' . $i;
        $rUid = 'TR_' . substr(time(), -4) . '_' . $i;
        createTestUser($pdo, $lUid, "Left {$i}");
        createTestUser($pdo, $rUid, "Right {$i}");
        // Add investment: $4,000 / 30 = $133.33 each leg (₹12,000)
        $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                       VALUES (:uid, 1, 'Pkg', 12000, 133.33, 3.0, 0, 0, 36000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':uid' => $lUid]);
        $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                       VALUES (:uid, 1, 'Pkg', 12000, 133.33, 3.0, 0, 0, 36000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':uid' => $rUid]);
        $leftUsers[] = $lUid;
        $rightUsers[] = $rUid;
    }

    // Set tree node for test user
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) 
                   VALUES (:uid, :lid, :rid, '1', 'L', '0', '0', 30, 30, 30, 30)")->execute([
        ':uid' => $testUser,
        ':lid' => $leftUsers[0],
        ':rid' => $rightUsers[0]
    ]);

    // Link remaining left users into a single left branch chain
    for ($i = 0; $i < 29; $i++) {
        $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) 
                       VALUES (:uid, :lid, '', '1', 'L', '0', '0', 1, 0, 1, 0)")->execute([
            ':uid' => $leftUsers[$i],
            ':lid' => $leftUsers[$i + 1]
        ]);
    }
    // Link remaining right users into a single right branch chain
    for ($i = 0; $i < 29; $i++) {
        $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) 
                       VALUES (:uid, '', :rid, '1', 'R', '0', '0', 0, 1, 0, 1)")->execute([
            ':uid' => $rightUsers[$i],
            ':rid' => $rightUsers[$i + 1]
        ]);
    }

    // Execute evaluateUserVIPQualifications
    $res = evaluateUserVIPQualifications($testUser, $pdo);
    
    assertCondition("Level 1 PRIME exact threshold qualifies", 
        !empty($res['newly_qualified']) && $res['newly_qualified'][0]['level'] === 1, 
        "Newly qualified: " . json_encode($res['newly_qualified'])
    );

    // Verify wallet updated in user table
    $stmt = $pdo->prepare("SELECT rank_reward_wallet, vip_club_wallet, company_turnover_wallet, `rank`, rankid FROM user WHERE userid = :uid");
    $stmt->execute([':uid' => $testUser]);
    $uData = $stmt->fetch(PDO::FETCH_ASSOC);

    assertCondition("Rank reward wallet credited with \$200.00", (float)$uData['rank_reward_wallet'] == 200.0, "Wallet: {$uData['rank_reward_wallet']}");
    assertCondition("User rank updated to PRIME (rankid 1)", $uData['rank'] === 'PRIME' && (int)$uData['rankid'] === 1, "Rank: {$uData['rank']}, RankId: {$uData['rankid']}");
    assertCondition("VIP and Turnover wallets remain 0.00", (float)$uData['vip_club_wallet'] == 0.0 && (float)$uData['company_turnover_wallet'] == 0.0);

    // Verify qualification row inserted in tbl_vip_user_qualification
    $stmt = $pdo->prepare("SELECT * FROM tbl_vip_user_qualification WHERE user_id = :uid AND vip_level = 1");
    $stmt->execute([':uid' => $testUser]);
    $qualRow = $stmt->fetch(PDO::FETCH_ASSOC);
    assertCondition("tbl_vip_user_qualification record exists with reward \$200", $qualRow && (float)$qualRow['reward_amount'] == 200.0);

    // Test Case B: Idempotency (Repeat evaluation should NOT duplicate reward or credit)
    $resDup = evaluateUserVIPQualifications($testUser, $pdo);
    assertCondition("Repeated evaluation returns 0 newly qualified ranks", empty($resDup['newly_qualified']));

    $uDataAfterDup = $pdo->query("SELECT rank_reward_wallet FROM user WHERE userid = '{$testUser}'")->fetch(PDO::FETCH_ASSOC);
    assertCondition("Rank reward wallet balance strictly unchanged at \$200.00", (float)$uDataAfterDup['rank_reward_wallet'] == 200.0);

} finally {
    $pdo->rollBack();
    echo "  [INFO] Transaction rolled back safely. Zero test data persisted to user or qualification tables.\n";
}

// 4. Test Monthly Closing: Date Restriction (11th Rule) & Preview
echo "\n[4] Testing Monthly Closing Date Rules & Preview Functionality...\n";
// Non-11th date test
$non11thDate = '2026-10-15';
$closingResFail = processVIPMonthlyIncome('2026-10', $non11thDate, $pdo, false);
assertCondition("Monthly closing is strictly rejected on non-11th date (2026-10-15)", 
    $closingResFail['status'] === 'blocked_non_11th_date' && strpos($closingResFail['message'], '11th') !== false,
    "Message: {$closingResFail['message']}"
);

// Preview on any date should not modify any wallet
$previewRes = previewVIPMonthlyIncome('2026-10', '2026-10-11', $pdo);
assertCondition("previewVIPMonthlyIncome() returns structured preview", 
    isset($previewRes['status']) && $previewRes['status'] === 'success' && isset($previewRes['eligible_users']) && isset($previewRes['ineligible_users']),
    "Eligible users: " . count($previewRes['eligible_users']) . ", Ineligible: " . count($previewRes['ineligible_users'])
);
assertCondition("Preview details aggregate payout calculations", 
    isset($previewRes['total_vip_payout']) && isset($previewRes['total_turnover_payout']) && isset($previewRes['company_turnover_usd'])
);

// 5. Test Monthly Closing & Option B Turnover Payout Calculation (Isolated Transaction)
echo "\n[5] Testing Option B (0.5% turnover share per qualified user) & Segregated Payouts...\n";
$pdo->beginTransaction();
try {
    $validClosingDate = '2026-10-11';
    $closingMonth = '2026-10';

    // Seed test users:
    // User 1: Level 1 (PRIME) with repeat business met
    $uPrime = 'TPC_' . substr(time(), -5) . '_1';
    createTestUser($pdo, $uPrime, 'Prime C', []);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 15000, 166.67, 3.0, 0, 0, 45000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $uPrime]);
    $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status) 
                   VALUES (:u, 1, 30, 30, 4000, 4000, 4000, 200, 'CREDITED')")->execute([':u' => $uPrime]);

    // Subtree members for Prime (weaker leg >= $4,000)
    $pL = 'TPL_' . substr(time(), -4);
    $pR = 'TPR_' . substr(time(), -4);
    createTestUser($pdo, $pL, 'Prime Left');
    createTestUser($pdo, $pR, 'Prime Right');
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 360000, 4000.0, 3.0, 0, 0, 1000000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $pL]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 360000, 4000.0, 3.0, 0, 0, 1000000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $pR]);
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) 
                   VALUES (:u, :lid, :rid, '1', 'L', '0', '0', 1, 1, 1, 1)")->execute([':u' => $uPrime, ':lid' => $pL, ':rid' => $pR]);

    // User 2: Level 7 (PEAK) with repeat business met ($100,000 req). Qualifies for both VIP weaker leg (0.5%) and Option B Turnover Share (0.5%)
    $uPeak = 'TPK_' . substr(time(), -5) . '_2';
    createTestUser($pdo, $uPeak, 'Peak C', []);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 15000, 166.67, 3.0, 0, 0, 45000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $uPeak]);
    $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status) 
                   VALUES (:u, 7, 5000, 5000, 1000000, 1000000, 1000000, 30000, 'CREDITED')")->execute([':u' => $uPeak]);

    $pkL = 'TPKL_' . substr(time(), -4);
    $pkR = 'TPKR_' . substr(time(), -4);
    createTestUser($pdo, $pkL, 'Peak Left');
    createTestUser($pdo, $pkR, 'Peak Right');
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $pkL]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $pkR]);
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) 
                   VALUES (:u, :lid, :rid, '1', 'L', '0', '0', 1, 1, 1, 1)")->execute([':u' => $uPeak, ':lid' => $pkL, ':rid' => $pkR]);

    // User 3: Level 8 (APEX) also qualifies for Option B Turnover Share
    $uApex = 'TAC_' . substr(time(), -5) . '_3';
    createTestUser($pdo, $uApex, 'Apex C', []);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 15000, 166.67, 3.0, 0, 0, 45000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $uApex]);
    $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status) 
                   VALUES (:u, 8, 12000, 12000, 2000000, 2000000, 2000000, 70000, 'CREDITED')")->execute([':u' => $uApex]);

    $axL = 'TAXL_' . substr(time(), -4);
    $axR = 'TAXR_' . substr(time(), -4);
    createTestUser($pdo, $axL, 'Apex Left');
    createTestUser($pdo, $axR, 'Apex Right');
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $axL]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES (:u, 1, 'Pkg', 9000000, 100000.0, 3.0, 0, 0, 20000000, CURDATE(), CURTIME(), '0', 'LOCKED')")->execute([':u' => $axR]);
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal, leftcount, rightcount) 
                   VALUES (:u, :lid, :rid, '1', 'L', '0', '0', 1, 1, 1, 1)")->execute([':u' => $uApex, ':lid' => $axL, ':rid' => $axR]);

    // Seed mock company turnover in tbl_roi_one for this month ($100,000 USD total turnover = ₹9,000,000)
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES ('DUMMY1', 1, 'Pkg', 4500000, 50000.0, 3.0, 0, 0, 10000000, '2026-10-03', '10:00:00', '0', 'LOCKED')")->execute();
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, name, package, real_fund_usd, percentage, amount, totalincome, capping, date, time, status, capital_withdrawal_status) 
                   VALUES ('DUMMY2', 1, 'Pkg', 4500000, 50000.0, 3.0, 0, 0, 10000000, '2026-10-04', '10:00:00', '0', 'LOCKED')")->execute();

    // Run preview
    $preview = previewVIPMonthlyIncome($closingMonth, $validClosingDate, $pdo);
    $companyTurnover = (float)$preview['company_turnover_usd'];
    assertCondition("Preview calculates positive company turnover base", $companyTurnover >= 100000.0, "Turnover: \${$companyTurnover}");

    // Run actual closing on 11th
    $closingRes = processVIPMonthlyIncome($closingMonth, $validClosingDate, $pdo, false);
    assertCondition("Monthly closing succeeds on the 11th", $closingRes['status'] === 'success', $closingRes['message']);

    // Check Option B Turnover calculation:
    // Option B rule: each qualified user in PEAK, APEX, ICON, ANANT receives turnover * 0.005 independently
    $expectedIndividualTurnover = round($companyTurnover * 0.005, 2);

    $peakUserAfter = $pdo->query("SELECT rank_reward_wallet, vip_club_wallet, company_turnover_wallet FROM user WHERE userid = '{$uPeak}'")->fetch(PDO::FETCH_ASSOC);
    $apexUserAfter = $pdo->query("SELECT rank_reward_wallet, vip_club_wallet, company_turnover_wallet FROM user WHERE userid = '{$uApex}'")->fetch(PDO::FETCH_ASSOC);
    $primeUserAfter = $pdo->query("SELECT rank_reward_wallet, vip_club_wallet, company_turnover_wallet FROM user WHERE userid = '{$uPrime}'")->fetch(PDO::FETCH_ASSOC);

    assertCondition("Prime user receives $0 company turnover (Level 1 excluded from turnover share)", (float)$primeUserAfter['company_turnover_wallet'] == 0.0);
    assertCondition("Prime user VIP weaker leg credited at 0.5% of \$4,000 = \$20.00", (float)$primeUserAfter['vip_club_wallet'] == 20.0, "Wallet: {$primeUserAfter['vip_club_wallet']}");

    assertCondition("Peak user received exact Option B turnover amount (\${$expectedIndividualTurnover})", 
        (float)$peakUserAfter['company_turnover_wallet'] == $expectedIndividualTurnover, 
        "Wallet: {$peakUserAfter['company_turnover_wallet']}, Expected: {$expectedIndividualTurnover}"
    );

    assertCondition("Apex user independently received exact Option B turnover amount (\${$expectedIndividualTurnover}) without pool splitting", 
        (float)$apexUserAfter['company_turnover_wallet'] == $expectedIndividualTurnover, 
        "Wallet: {$apexUserAfter['company_turnover_wallet']}, Expected: {$expectedIndividualTurnover}"
    );

    // Verify Idempotency: Attempt to close again for the same month
    $closingResAgain = processVIPMonthlyIncome($closingMonth, $validClosingDate, $pdo, false);
    assertCondition("Second closing attempt on the same month credits 0 users (Strictly Idempotent)", 
        $closingResAgain['processed'] === 0, 
        "Processed count: {$closingResAgain['processed']}"
    );

} finally {
    $pdo->rollBack();
    echo "  [INFO] Transaction rolled back safely. Zero monthly closing rows persisted.\n";
}

// 6. Test Wallet Segregation & 7-Stream Reconciliation in getUserIncomeWalletSummary()
echo "\n[6] Testing 7-Stream Wallet & Growth Reconciliation...\n";
$pdo->beginTransaction();
try {
    $reconUser = 'TR_' . substr(time(), -5) . '_1';
    createTestUser($pdo, $reconUser, 'Recon User', [
        'profit_income_wallet' => 10.0,
        'profit_sharing_wallet' => 20.0,
        'direct_bonus_wallet' => 30.0,
        'mentor_income_wallet' => 40.0,
        'rank_reward_wallet' => 50.0,
        'vip_club_wallet' => 60.0,
        'company_turnover_wallet' => 70.0,
        'user_growth_wallet' => 0.0
    ]);

    $summary = getUserIncomeWalletSummary($reconUser, $pdo);
    assertCondition("Summary profit income = \$10.00", (float)$summary['profit_income'] == 10.0);
    assertCondition("Summary profit sharing = \$20.00", (float)$summary['profit_sharing'] == 20.0);
    assertCondition("Summary direct bonus = \$30.00", (float)$summary['direct_bonus'] == 30.0);
    assertCondition("Summary mentor income = \$40.00", (float)$summary['mentor_income'] == 40.0);
    assertCondition("Summary rank reward = \$50.00", (float)$summary['rank_reward'] == 50.0);
    assertCondition("Summary vip club = \$60.00", (float)$summary['vip_club'] == 60.0);
    assertCondition("Summary company turnover = \$70.00", (float)$summary['company_turnover'] == 70.0);

    // Sum of 7 streams: 10 + 20 + 30 + 40 + 50 + 60 + 70 = 280.00
    assertCondition("Total user growth accurately sums all 7 distinct streams to \$280.00 without double counting", 
        (float)$summary['total_income_balance'] == 280.0, 
        "Total: {$summary['total_income_balance']}"
    );

} finally {
    $pdo->rollBack();
    echo "  [INFO] Transaction rolled back safely.\n";
}

echo "\n=================================================================\n";
echo "TEST RESULTS: {$testsPassed} PASSED, {$testsFailed} FAILED.\n";
echo "=================================================================\n";
