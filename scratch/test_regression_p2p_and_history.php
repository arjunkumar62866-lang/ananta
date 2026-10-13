<?php
/**
 * Regression Test Suite for P2P Received History & Income Wallet History Segregation
 */
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "\n=================================================================\n";
echo "REGRESSION TEST: P2P RECEIVED HISTORY & INCOME WALLET HISTORY\n";
echo "=================================================================\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $description, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$description}" . ($details ? " - {$details}" : "") . "\n";
    } else {
        $failCount++;
        echo "  [FAIL] {$description}" . ($details ? " - {$details}" : "") . "\n";
    }
}

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

$pdo->beginTransaction();

try {
    // 1. Setup mock user and P2P transfers
    $testSender = 'TEST_SENDER_' . substr(time(), -5);
    $testReceiver = 'TEST_REC_' . substr(time(), -5);

    createTestUser($pdo, $testSender, 'Test Sender');
    createTestUser($pdo, $testReceiver, 'Test Receiver');

    // Use MySQL native timestamps to align with CURDATE()
    $todayDate = $pdo->query("SELECT NOW()")->fetchColumn();
    $pastDate  = $pdo->query("SELECT NOW() - INTERVAL 5 DAY")->fetchColumn();

    // Insert historical P2P transfers (one 5 days ago, one today)
    $stmtP2P = $pdo->prepare("INSERT INTO tbl_p2p_transfer (transfer_ref, sender_id, receiver_id, amount, from_wallet, to_wallet, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtP2P->execute(['P2P_REF_PAST', $testSender, $testReceiver, 150.00, 'Main Wallet', 'Main Wallet', 'COMPLETED', $pastDate]);
    $stmtP2P->execute(['P2P_REF_TODAY', $testSender, $testReceiver, 50.00, 'Main Wallet', 'Main Wallet', 'COMPLETED', $todayDate]);

    echo "\n[1] Testing getUserP2PReceivedReport()...\n";

    // Test A: Calling with default 'all'
    $reportAll = getUserP2PReceivedReport($testReceiver, 'all', null, null, $pdo);
    assertTest(count($reportAll) === 2, "Default 'all' returns both past and today's transfers", "Found " . count($reportAll) . " records");

    // Test B: Calling with legacy 2-argument signature ($userid, $pdo)
    $reportLegacy = getUserP2PReceivedReport($testReceiver, $pdo);
    assertTest(count($reportLegacy) === 2, "Legacy 2-arg signature getUserP2PReceivedReport(\$uid, \$pdo) defaults to 'all'", "Found " . count($reportLegacy) . " records");

    // Test C: Calling with 'today' filter
    $reportToday = getUserP2PReceivedReport($testReceiver, 'today', null, null, $pdo);
    assertTest(count($reportToday) === 1, "'today' filter only returns today's transfer", "Found " . count($reportToday) . " records");
    assertTest(!empty($reportToday) && $reportToday[0]['transfer_ref'] === 'P2P_REF_TODAY', "'today' transfer reference matches P2P_REF_TODAY");

    // Test D: Calling with 'custom' date range
    $fromDate = $pdo->query("SELECT CURDATE() - INTERVAL 6 DAY")->fetchColumn();
    $toDate   = $pdo->query("SELECT CURDATE() - INTERVAL 4 DAY")->fetchColumn();
    $reportCustom = getUserP2PReceivedReport($testReceiver, 'custom', $fromDate, $toDate, $pdo);
    assertTest(count($reportCustom) === 1, "'custom' date range returns only transfer within range", "Found " . count($reportCustom) . " records");
    assertTest(!empty($reportCustom) && $reportCustom[0]['transfer_ref'] === 'P2P_REF_PAST', "'custom' transfer reference matches P2P_REF_PAST");

    echo "\n[2] Testing Lifetime P2P Aggregates...\n";
    $stmtRecTotal = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM tbl_p2p_transfer WHERE receiver_id = ? AND status = 'COMPLETED'");
    $stmtRecTotal->execute([$testReceiver]);
    $totalRec = (float)$stmtRecTotal->fetchColumn();
    assertTest($totalRec == 200.00, "Lifetime received total is $200.00 (150 past + 50 today)", "Total: $" . number_format($totalRec, 2));

    $stmtSentTotal = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM tbl_p2p_transfer WHERE sender_id = ? AND status = 'COMPLETED'");
    $stmtSentTotal->execute([$testSender]);
    $totalSent = (float)$stmtSentTotal->fetchColumn();
    assertTest($totalSent == 200.00, "Lifetime sent total is $200.00", "Total: $" . number_format($totalSent, 2));

    echo "\n[3] Testing getUserIncomeWalletHistory() Segregation...\n";

    // Insert mock qualification record (Rank Reward)
    $stmtQual = $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status, qualified_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PAID', ?)");
    $stmtQual->execute([$testReceiver, 1, 30, 30, 4000.00, 4000.00, 4000.00, 200.00, $pastDate]);

    // Insert mock monthly schedule record (VIP Club and Company Turnover)
    $stmtSched = $pdo->prepare("INSERT INTO tbl_vip_monthly_schedule (user_id, vip_level, closing_month, closing_date, weaker_leg_business, weaker_leg_rate, weaker_leg_payout, company_turnover, turnover_rate, turnover_payout, total_payout, status, credited_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'CREDITED', ?)");
    $stmtSched->execute([$testReceiver, 7, '2026-09', '2026-09-11', 1000000.00, 0.50, 5000.00, 500000.00, 0.50, 2500.00, 7500.00, $todayDate]);

    // Test RANK_REWARD filter
    $histRank = getUserIncomeWalletHistory($testReceiver, 'RANK_REWARD', null, null, $pdo);
    assertTest(count($histRank) >= 1, "RANK_REWARD query returns qualification records", "Found " . count($histRank));
    $foundRank = false;
    foreach ($histRank as $h) {
        if ($h['amount'] == 200.00 && $h['income_type'] === 'Rank Reward') {
            $foundRank = true;
            break;
        }
    }
    assertTest($foundRank, "RANK_REWARD history entry contains exact $200.00 reward amount with income_type='Rank Reward'");

    // Test VIP_CLUB filter
    $histVIP = getUserIncomeWalletHistory($testReceiver, 'VIP_CLUB', null, null, $pdo);
    assertTest(count($histVIP) >= 1, "VIP_CLUB query returns monthly schedule weaker leg payouts", "Found " . count($histVIP));
    $foundVIP = false;
    foreach ($histVIP as $h) {
        if ($h['amount'] == 5000.00 && $h['income_type'] === 'VIP Club') {
            $foundVIP = true;
            break;
        }
    }
    assertTest($foundVIP, "VIP_CLUB history entry contains exact $5,000.00 weaker leg payout with income_type='VIP Club'");

    // Test COMPANY_TURNOVER filter
    $histTO = getUserIncomeWalletHistory($testReceiver, 'COMPANY_TURNOVER', null, null, $pdo);
    assertTest(count($histTO) >= 1, "COMPANY_TURNOVER query returns monthly turnover payouts", "Found " . count($histTO));
    $foundTO = false;
    foreach ($histTO as $h) {
        if ($h['amount'] == 2500.00 && $h['income_type'] === 'Company Turnover') {
            $foundTO = true;
            break;
        }
    }
    assertTest($foundTO, "COMPANY_TURNOVER history entry contains exact $2,500.00 turnover payout with income_type='Company Turnover'");

    // Test ALL filter
    $histAll = getUserIncomeWalletHistory($testReceiver, 'ALL', null, null, $pdo);
    assertTest(count($histAll) >= 3, "ALL filter aggregates all streams without collision", "Total records: " . count($histAll));

} finally {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\n[INFO] Transaction rolled back safely. Zero test data persisted.\n";
}

echo "\n=================================================================\n";
echo "REGRESSION RESULTS: {$passCount} PASSED, {$failCount} FAILED.\n";
echo "=================================================================\n\n";

exit($failCount > 0 ? 1 : 0);
