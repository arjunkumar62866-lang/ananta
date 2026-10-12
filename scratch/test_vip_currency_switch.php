<?php
/**
 * scratch/test_vip_currency_switch.php
 * Automated verification test for VIP Club USD/INR currency switch
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

function assertTest($cond, $title, $details = '') {
    if ($cond) {
        echo "[PASS] {$title}\n";
        if ($details) echo "       Details: {$details}\n";
    } else {
        echo "[FAIL] {$title}\n";
        if ($details) echo "       Details: {$details}\n";
        throw new Exception("Test failed: {$title}");
    }
}

echo "=======================================================\n";
echo " TESTING VIP CLUB CURRENCY SWITCH (USD <-> INR)\n";
echo "=======================================================\n\n";

$testUser = 'TSG_VC_' . substr(time(), -6);

try {
    // 1. Setup isolated test user in DB with USD preference
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, vip_club_wallet, currency_preference
        ) VALUES (
            :uid, 'VIP Currency Tester', '1', 0, '', '9999999999', 'M', 'vipcurr@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            500.00, 200.00, 'USD'
        )
    ");
    $stmtUser->execute([':uid' => $testUser]);

    // Insert investment to ensure income eligibility
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, level, package, real_fund_usd, percentage, amount, totalincome, capping, status, count, lock_day, date, time) VALUES (:uid, 1, 13050, 145.00, 1.0, 0, 0, 0, '0', 1, 10, CURDATE(), '00:00:00')")
        ->execute([':uid' => $testUser]);

    // Insert tree placement
    $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, lefttotal, righttotal) VALUES (:uid, '', '', 1, 'L', 0, 0, 0, 0)")
        ->execute([':uid' => $testUser]);

    // Insert mock VIP Qualification (Level 1, $200 reward, $15,000 weaker leg)
    $pdo->prepare("
        INSERT INTO tbl_vip_user_qualification 
        (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status)
        VALUES 
        (:uid, 1, 10, 10, 15000.00, 20000.00, 15000.00, 200.00, 'CREDITED')
    ")->execute([':uid' => $testUser]);

    // Insert mock Monthly Schedule (Month 2026-09, weaker leg $15,000, weaker payout $75, turnover $50, total $125)
    $pdo->prepare("
        INSERT INTO tbl_vip_monthly_schedule
        (user_id, vip_level, closing_month, weaker_leg_business, weaker_leg_payout, turnover_payout, total_payout, status, credited_at)
        VALUES
        (:uid, 1, '2026-09', 15000.00, 75.00, 50.00, 125.00, 'CREDITED', NOW())
    ")->execute([':uid' => $testUser]);

    $_SESSION['userid'] = $testUser;

    // -------------------------------------------------------------
    // TEST 1: USD Mode API Response
    // -------------------------------------------------------------
    setUserCurrency('USD', $testUser, $pdo);
    $_SESSION['currency'] = 'USD';
    $_REQUEST['currency'] = 'USD';

    ob_start();
    include __DIR__ . '/../dashboard/user1/get_vip_club.php';
    $rawJsonUSD = ob_get_clean();

    $resUSD = json_decode($rawJsonUSD, true);
    assertTest(!empty($resUSD['status']) && $resUSD['status'] === 'success', "1.1 get_vip_club API returns success in USD mode");
    assertTest($resUSD['currency']['code'] === 'USD', "1.2 Currency code is USD", "Reported: {$resUSD['currency']['code']}");
    assertTest($resUSD['currency']['symbol'] === '$', "1.3 Currency symbol is $", "Reported: {$resUSD['currency']['symbol']}");
    assertTest($resUSD['data']['user']['display_vip_club_wallet'] == 200.00, "1.4 VIP wallet displayed as 200.00 USD");
    assertTest(strpos($resUSD['data']['user']['formatted_vip_club_wallet'], '$') !== false, "1.5 VIP wallet formatted with $ symbol", $resUSD['data']['user']['formatted_vip_club_wallet']);
    assertTest($resUSD['data']['qualifications'][0]['display_reward_amount'] == 200.00, "1.6 Level 1 reward displayed as 200.00 USD");
    assertTest(strpos($resUSD['data']['qualifications'][0]['formatted_reward_amount'], '$') !== false, "1.7 Level 1 reward formatted with $ symbol", $resUSD['data']['qualifications'][0]['formatted_reward_amount']);
    assertTest($resUSD['data']['monthly_payouts'][0]['display_total_payout'] == 125.00, "1.8 Monthly payout displayed as 125.00 USD");

    // -------------------------------------------------------------
    // TEST 2: Switch to INR Mode
    // -------------------------------------------------------------
    setUserCurrency('INR', $testUser, $pdo);
    $_SESSION['currency'] = 'INR';
    $_REQUEST['currency'] = 'INR';

    $inrRate = getUSDToINRRate($pdo);
    assertTest($inrRate == 90.0, "2.1 Canonical exchange rate is 90.0 INR/USD", "Rate: {$inrRate}");

    ob_start();
    include __DIR__ . '/../dashboard/user1/get_vip_club.php';
    $rawJsonINR = ob_get_clean();

    $resINR = json_decode($rawJsonINR, true);
    assertTest(!empty($resINR['status']) && $resINR['status'] === 'success', "2.2 get_vip_club API returns success in INR mode");
    assertTest($resINR['currency']['code'] === 'INR', "2.3 Currency code is INR", "Reported: {$resINR['currency']['code']}");
    assertTest($resINR['currency']['symbol'] === '₹', "2.4 Currency symbol is ₹", "Reported: {$resINR['currency']['symbol']}");
    
    // Check conversion calculations: $200 * 90 = ₹18,000.00
    $expectedWalletINR = 200.00 * $inrRate;
    assertTest($resINR['data']['user']['display_vip_club_wallet'] == $expectedWalletINR, "2.5 VIP wallet converted to ₹18,000.00", "Wallet: {$resINR['data']['user']['display_vip_club_wallet']}");
    assertTest(strpos($resINR['data']['user']['formatted_vip_club_wallet'], '₹') !== false, "2.6 VIP wallet formatted with ₹ symbol", $resINR['data']['user']['formatted_vip_club_wallet']);
    
    // Qualification reward: $200 * 90 = ₹18,000.00
    assertTest($resINR['data']['qualifications'][0]['display_reward_amount'] == $expectedWalletINR, "2.7 Qualification reward converted to ₹18,000.00", "Reward: {$resINR['data']['qualifications'][0]['display_reward_amount']}");
    assertTest(strpos($resINR['data']['qualifications'][0]['formatted_reward_amount'], '₹') !== false, "2.8 Qualification reward formatted with ₹ symbol", $resINR['data']['qualifications'][0]['formatted_reward_amount']);

    // Monthly payout: $125 * 90 = ₹11,250.00
    $expectedTotalPayoutINR = 125.00 * $inrRate;
    assertTest($resINR['data']['monthly_payouts'][0]['display_total_payout'] == $expectedTotalPayoutINR, "2.9 Monthly payout converted to ₹11,250.00", "Payout: {$resINR['data']['monthly_payouts'][0]['display_total_payout']}");
    assertTest(strpos($resINR['data']['monthly_payouts'][0]['formatted_total_payout'], '₹') !== false, "2.10 Monthly payout formatted with ₹ symbol", $resINR['data']['monthly_payouts'][0]['formatted_total_payout']);

    // -------------------------------------------------------------
    // TEST 3: Stored Database Integrity Verification
    // -------------------------------------------------------------
    $stmtCheck = $pdo->prepare("SELECT vip_club_wallet, amount FROM user WHERE userid = :uid");
    $stmtCheck->execute([':uid' => $testUser]);
    $dbUser = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    assertTest((float)$dbUser['vip_club_wallet'] == 200.00, "3.1 DB vip_club_wallet remains intact at $200.00 in canonical USD", "DB Wallet: {$dbUser['vip_club_wallet']}");
    assertTest((float)$dbUser['amount'] == 500.00, "3.2 DB user.amount remains intact at $500.00 in canonical USD", "DB Amount: {$dbUser['amount']}");

    $stmtCheckQual = $pdo->prepare("SELECT reward_amount, weaker_leg_business FROM tbl_vip_user_qualification WHERE user_id = :uid");
    $stmtCheckQual->execute([':uid' => $testUser]);
    $dbQual = $stmtCheckQual->fetch(PDO::FETCH_ASSOC);

    assertTest((float)$dbQual['reward_amount'] == 200.00, "3.3 DB tbl_vip_user_qualification reward remains $200.00", "DB Reward: {$dbQual['reward_amount']}");
    assertTest((float)$dbQual['weaker_leg_business'] == 15000.00, "3.4 DB tbl_vip_user_qualification weaker leg remains 15000.00", "DB Weaker Leg: {$dbQual['weaker_leg_business']}");

    // -------------------------------------------------------------
    // TEST 4: Persistence across reload
    // -------------------------------------------------------------
    // Unset $_REQUEST and $_SESSION, reload getUserCurrency from DB
    unset($_SESSION['currency']);
    unset($_REQUEST['currency']);
    $reloadedCurr = getUserCurrency($testUser, $pdo);
    assertTest($reloadedCurr === 'INR', "4.1 Reloading currency preference directly from user DB returns INR", "Reloaded: {$reloadedCurr}");

    echo "\n=======================================================\n";
    echo " ALL 16 VIP CURRENCY SWITCH TESTS PASSED SUCCESSFULLY!\n";
    echo "=======================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
} finally {
    // Teardown test user
    $pdo->prepare("DELETE FROM tbl_vip_user_qualification WHERE user_id = :uid")->execute([':uid' => $testUser]);
    $pdo->prepare("DELETE FROM tbl_vip_monthly_schedule WHERE user_id = :uid")->execute([':uid' => $testUser]);
    $pdo->prepare("DELETE FROM tbl_roi_one WHERE user_id = :uid")->execute([':uid' => $testUser]);
    $pdo->prepare("DELETE FROM tree WHERE userid = :uid")->execute([':uid' => $testUser]);
    $pdo->prepare("DELETE FROM user WHERE userid = :uid")->execute([':uid' => $testUser]);
}
