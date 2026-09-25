<?php
/**
 * scratch/test_wallet_navigation.php
 * Automated Test Suite for User Panel Wallet Menu & Wallet Pages Integration
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dashboard/user1/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

function assertTest($condition, $testName, $details = "") {
    if ($condition) {
        echo "[PASS] {$testName}\n";
        if (!empty($details)) echo "       Details: {$details}\n";
    } else {
        echo "[FAIL] {$testName}\n";
        if (!empty($details)) echo "       Details: {$details}\n";
        throw new Exception("Test Failed: {$testName}");
    }
}

echo "=======================================================\n";
echo " STARTING WALLET MENU & PAGES INTEGRATION TEST SUITE\n";
echo "=======================================================\n\n";

try {
    $testUser = 'TEST_WLT_USER';

    // Cleanup existing sandbox test user
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUser}'");

    // Create sandbox test user with 50.00 USD Main Wallet and various income wallets
    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet, pin_wallet, withdrawal_status, currency_preference
        ) VALUES (
            :uid, 'Wallet Test User', '1', 0, '', '9999911111', 'M', 'wlt@test.com', 'ABCDE1234F', '',
            0, 0, '', '', '', 1, 0, 'L', 12000,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            50.00, 100.00, 50.00, 60.00, 40.00, 25.00, 0.00, 0.00, 1, 'USD'
        )
    ");
    $stmtUser->execute([':uid' => $testUser]);

    // Insert dummy transactions
    $pdo->exec("INSERT INTO tbl_transaction (user_id, amount, act_amount, type, subject, status, created_date, time) VALUES ('{$testUser}', 4500.00, 4500.00, 'Credit', 'P2P Fund Received', '1', CURDATE(), '10:00:00')");
    $pdo->exec("INSERT INTO tbl_transaction (user_id, amount, act_amount, type, subject, status, created_date, time) VALUES ('{$testUser}', 990.00, 990.00, 'Debit', 'Unlock Access Fee ($11)', '1', CURDATE(), '11:00:00')");
    $pdo->exec("INSERT INTO tbl_transaction (user_id, amount, act_amount, type, subject, status, created_date, time) VALUES ('{$testUser}', 900.00, 900.00, 'Profit Income', 'Daily Yield Bonus', '1', CURDATE(), '12:00:00')");
    $pdo->exec("INSERT INTO tbl_transaction (user_id, amount, act_amount, type, subject, status, created_date, time) VALUES ('{$testUser}', 450.00, 450.00, 'Credit', 'Profit Sharing Income', '1', CURDATE(), '13:00:00')");

    // 1. Single source of truth Main Wallet balance test
    $bal = getUserWalletBalance($testUser, $pdo);
    assertTest(abs($bal - 50.00) < 0.01, "1. Main Wallet balance matches single source of truth", "Balance: ${$bal}");

    // 2. Main Wallet transactions test
    $txns = getUserMainWalletTransactions($testUser, null, null, null, $pdo);
    assertTest(count($txns) === 4, "2. Main Wallet transactions fetched correctly", "Count: " . count($txns));

    // 3. Main Wallet credit filter test
    $credTxns = getUserMainWalletTransactions($testUser, null, null, 'Credit', $pdo);
    assertTest(count($credTxns) === 2, "3. Main Wallet credit filter works", "Credit Count: " . count($credTxns));

    // 4. Income Wallet summary calculation test
    $incSummary = getUserIncomeWalletSummary($testUser, $pdo);
    assertTest($incSummary['profit_income'] == 100.00, "4. Profit Income balance matches", "Profit Inc: ${$incSummary['profit_income']}");
    assertTest($incSummary['profit_sharing'] == 50.00, "5. Profit Sharing balance matches", "Profit Share: ${$incSummary['profit_sharing']}");
    assertTest($incSummary['direct_bonus'] == 60.00, "6. Direct Bonus balance matches", "Direct Bonus: ${$incSummary['direct_bonus']}");
    assertTest($incSummary['mentor_income'] == 40.00, "7. Mentor Income balance matches", "Mentor Inc: ${$incSummary['mentor_income']}");
    assertTest($incSummary['vip_club'] == 25.00, "8. VIP Club balance matches", "VIP Club: ${$incSummary['vip_club']}");
    assertTest($incSummary['total_income_balance'] == 275.00, "9. Total Income Wallet sum matches all 7 categories", "Total: ${$incSummary['total_income_balance']}");

    // 5. Income Wallet history test
    $incHist = getUserIncomeWalletHistory($testUser, 'ALL', null, null, $pdo);
    assertTest(count($incHist) === 2, "10. Income Wallet history fetched correctly", "History Count: " . count($incHist));

    // Cleanup sandbox test user
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUser}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUser}'");

    echo "\n=======================================================\n";
    echo " TEST SUITE COMPLETE: PASS = 10 | FAIL = 0 \n";
    echo "=======================================================\n\n";

} catch (Exception $e) {
    echo "\n[ERROR] Test Execution Interrupted: " . $e->getMessage() . "\n";
    exit(1);
}
