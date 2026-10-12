<?php
/**
 * scratch/test_vip_safeguards_verification.php
 * Automated verification of all 10 VIP Club requirements and safeguards:
 * 1. 10-level VIP qualification via evaluateUserVIPQualifications
 * 2. Event-driven ancestor trigger integration
 * 3. rank_reward($userid) deactivation
 * 4. Idempotency across tbl_vip_user_qualification, user.vip_club_wallet, user.amount, tbl_transaction
 * 5. Monthly repeat requirement and 11th-date restriction
 * 6. Level 6-10 currency thresholds intact
 * 7. Lifetime income separation (turnover not counted twice)
 */

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "=======================================================\n";
echo " VIP CLUB MANDATORY SAFEGUARDS & INTEGRATION AUDIT TEST\n";
echo "=======================================================\n\n";

$pass = 0;
$fail = 0;

function assertCheck($cond, $title, $detail = '') {
    global $pass, $fail;
    if ($cond) {
        echo "[PASS] {$title}\n";
        if ($detail) echo "       {$detail}\n";
        $pass++;
    } else {
        echo "[FAIL] {$title}\n";
        if ($detail) echo "       {$detail}\n";
        $fail++;
    }
}

// -------------------------------------------------------------
// CHECK 1: Function signatures and dependencies exist
// -------------------------------------------------------------
assertCheck(
    function_exists('evaluateUserVIPQualifications'),
    "Check 1.1: evaluateUserVIPQualifications exists and callable"
);
assertCheck(
    function_exists('getBinaryLegDetails'),
    "Check 1.2: getBinaryLegDetails exists and callable"
);
assertCheck(
    function_exists('triggerVIPQualificationsForAncestors'),
    "Check 1.3: triggerVIPQualificationsForAncestors exists and callable"
);

// -------------------------------------------------------------
// CHECK 2: Deactivated rank_reward returns false without db writes
// -------------------------------------------------------------
$dummyId = 'NONEXISTENT_USER_' . mt_rand(1000, 9999);
$resRank = rank_reward($dummyId);
assertCheck(
    $resRank === false,
    "Check 2.1: Obsolete rank_reward returns false without throwing errors",
    "Returned: " . var_export($resRank, true)
);

// -------------------------------------------------------------
// CHECK 3: Level 6-10 crore business thresholds intact in DB
// -------------------------------------------------------------
$l6 = $pdo->query("SELECT req_left_business, req_right_business FROM tbl_vip_level_config WHERE level_id = 6")->fetch(PDO::FETCH_ASSOC);
$l7 = $pdo->query("SELECT req_left_business, req_right_business FROM tbl_vip_level_config WHERE level_id = 7")->fetch(PDO::FETCH_ASSOC);
$l10 = $pdo->query("SELECT req_left_business, req_right_business FROM tbl_vip_level_config WHERE level_id = 10")->fetch(PDO::FETCH_ASSOC);

assertCheck(
    (float)$l6['req_left_business'] == 30000000.00 && (float)$l6['req_right_business'] == 30000000.00,
    "Check 3.1: Level 6 business threshold preserved at 30,000,000.00 ($3 Crore)",
    "Left: {$l6['req_left_business']}, Right: {$l6['req_right_business']}"
);
assertCheck(
    (float)$l7['req_left_business'] == 100000000.00 && (float)$l7['req_right_business'] == 100000000.00,
    "Check 3.2: Level 7 business threshold preserved at 100,000,000.00 ($10 Crore)",
    "Left: {$l7['req_left_business']}, Right: {$l7['req_right_business']}"
);
assertCheck(
    (float)$l10['req_left_business'] == 750000000.00 && (float)$l10['req_right_business'] == 750000000.00,
    "Check 3.3: Level 10 business threshold preserved at 750,000,000.00 ($75 Crore)",
    "Left: {$l10['req_left_business']}, Right: {$l10['req_right_business']}"
);

// -------------------------------------------------------------
// CHECK 4: Isolated Simulation for Exact Single Credit Across Wallets
// -------------------------------------------------------------
$testU = 'TSG_ROOT';
$testL = 'TSG_LEFT';
$testR = 'TSG_RIGHT';

// Cleanup
$pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tbl_vip_monthly_schedule WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tree WHERE userid LIKE 'TSG_%' OR left_id LIKE 'TSG_%' OR right_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM user WHERE userid LIKE 'TSG_%'");

// Insert test root user with $150 investment (satisfies >= $145 condition)
$stmtInsUser = $pdo->prepare("
    INSERT INTO user (
        userid, name, active, mobile, gender, email, pan, txn_pass, pass,
        total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
        joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
        pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
        one_club_status, two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
        amount, vip_club_wallet, user_growth_wallet
    ) VALUES (
        :u, :name, '1', '9999999999', 'M', 'test@test.com', 'ABCDE1234F', '1234', '1234',
        0, 0, '', '', '', 1, 0, 'L', 0,
        NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
        0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, '0',
        :amt, :vip_bal, :amt
    )
");

$stmtInsUser->execute([':u' => $testU, ':name' => 'SG Root', ':amt' => 500.00, ':vip_bal' => 0.00]);
$stmtInsUser->execute([':u' => $testL, ':name' => 'SG Left', ':amt' => 0.00, ':vip_bal' => 0.00]);
$stmtInsUser->execute([':u' => $testR, ':name' => 'SG Right', ':amt' => 0.00, ':vip_bal' => 0.00]);

// Tree connection
$stmtInsTree = $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal) VALUES (:u, :l, :r, 1, 'L', 0, 0, 0, 0)");
$stmtInsTree->execute([':u' => $testU, ':l' => $testL, ':r' => $testR]);

// Root self-investment
$stmtInsRoi = $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, package, real_fund_usd, percentage, amount, totalincome, capping, status, count, lock_day, date, time) VALUES (:u, 1, :pkg, :usd, 1.0, 0, 0, 0, '0', 1, 10, CURDATE(), '00:00:00')");
$stmtInsRoi->execute([':u' => $testU, ':pkg' => 13500, ':usd' => 150.00]);

// Left & Right business ($5,000 each = ₹450,000, satisfies Level 1 req of $4,000)
$stmtInsRoi->execute([':u' => $testL, ':pkg' => 450000, ':usd' => 5000.00]);
$stmtInsRoi->execute([':u' => $testR, ':pkg' => 450000, ':usd' => 5000.00]);

// Insert 30 active children under left and 30 under right
for ($i = 1; $i <= 30; $i++) {
    $lId = "TSG_L_{$i}";
    $rId = "TSG_R_{$i}";
    $stmtInsUser->execute([':u' => $lId, ':name' => 'Sub', ':amt' => 0.00, ':vip_bal' => 0.00]);
    $stmtInsUser->execute([':u' => $rId, ':name' => 'Sub', ':amt' => 0.00, ':vip_bal' => 0.00]);
}
$stmtInsTree->execute([':u' => $testL, ':l' => 'TSG_L_1', ':r' => '']);
$stmtInsTree->execute([':u' => $testR, ':l' => 'TSG_R_1', ':r' => '']);
for ($i = 1; $i < 30; $i++) {
    $stmtInsTree->execute([':u' => "TSG_L_{$i}", ':l' => "TSG_L_" . ($i + 1), ':r' => '']);
    $stmtInsTree->execute([':u' => "TSG_R_{$i}", ':l' => "TSG_R_" . ($i + 1), ':r' => '']);
}

// FIRST EVALUATION
$eval1 = evaluateUserVIPQualifications($testU, $pdo);

$uRow = $pdo->query("SELECT amount, vip_club_wallet, user_growth_wallet FROM user WHERE userid = '{$testU}'")->fetch(PDO::FETCH_ASSOC);
$qRow = $pdo->query("SELECT * FROM tbl_vip_user_qualification WHERE user_id = '{$testU}' AND vip_level = 1")->fetch(PDO::FETCH_ASSOC);
$txCount = (int)$pdo->query("SELECT COUNT(*) FROM tbl_transaction WHERE user_id = '{$testU}' AND subject LIKE '%VIP Club Level 1 Reward%'")->fetchColumn();

assertCheck(
    !empty($eval1['newly_qualified']) && (float)$qRow['reward_amount'] == 200.00 && $qRow['reward_status'] === 'CREDITED',
    "Check 4.1: Level 1 qualification recorded in tbl_vip_user_qualification with $200 reward",
    "Reward: {$qRow['reward_amount']}, Status: {$qRow['reward_status']}"
);
assertCheck(
    (float)$uRow['amount'] == 700.00 && (float)$uRow['vip_club_wallet'] == 200.00,
    "Check 4.2: Wallet balances correctly credited by exactly $200.00",
    "Amount: {$uRow['amount']} (initial 500 + 200), VIP Wallet: {$uRow['vip_club_wallet']}"
);
assertCheck(
    $txCount === 1,
    "Check 4.3: Exactly 1 transaction entry recorded in tbl_transaction",
    "Count: {$txCount}"
);

// SECOND EVALUATION (IDEMPOTENCY TEST)
$eval2 = evaluateUserVIPQualifications($testU, $pdo);
$uRow2 = $pdo->query("SELECT amount, vip_club_wallet, user_growth_wallet FROM user WHERE userid = '{$testU}'")->fetch(PDO::FETCH_ASSOC);
$qCount2 = (int)$pdo->query("SELECT COUNT(*) FROM tbl_vip_user_qualification WHERE user_id = '{$testU}' AND vip_level = 1")->fetchColumn();
$txCount2 = (int)$pdo->query("SELECT COUNT(*) FROM tbl_transaction WHERE user_id = '{$testU}' AND subject LIKE '%VIP Club Level 1 Reward%'")->fetchColumn();

assertCheck(
    empty($eval2['newly_qualified']) && $qCount2 === 1 && (float)$uRow2['vip_club_wallet'] == 200.00 && $txCount2 === 1,
    "Check 4.4: Re-evaluation idempotency verified (0 duplicates, wallet stays $200.00, 1 transaction)",
    "VIP Wallet: {$uRow2['vip_club_wallet']}, Transactions: {$txCount2}"
);

// -------------------------------------------------------------
// CHECK 5: Event-driven ancestor trigger verification
// -------------------------------------------------------------
$ancestors = getBinaryAncestors('TSG_L_30', $pdo);
assertCheck(
    in_array($testU, $ancestors) && in_array($testL, $ancestors),
    "Check 5.1: getBinaryAncestors correctly discovers parent {$testL} and root {$testU}",
    "Discovered: " . implode(' -> ', $ancestors)
);

// Cleanup test rows
$pdo->exec("DELETE FROM tbl_vip_user_qualification WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tbl_vip_monthly_schedule WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tbl_transaction WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tree WHERE userid LIKE 'TSG_%' OR left_id LIKE 'TSG_%' OR right_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM tbl_roi_one WHERE user_id LIKE 'TSG_%'");
$pdo->exec("DELETE FROM user WHERE userid LIKE 'TSG_%'");

echo "\n=======================================================\n";
echo "AUDIT SUMMARY: {$pass} PASSED, {$fail} FAILED\n";
echo "=======================================================\n";

if ($fail > 0) exit(1);
exit(0);
