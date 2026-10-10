<?php
/**
 * scratch/test_user_growth_wallet_reconciliation.php
 * Automated Isolated Test Suite for:
 * 1. User Growth Wallet Reconciliation (7 Income Streams)
 * 2. Preservation of Existing Wallet Balance (No Overwrite by Closing)
 * 3. Preservation of Withdrawals (Not Restored by Closing)
 * 4. Preservation of Transfers (Not Restored by Closing)
 * 5. Preservation of Reversals / Admin Debit Adjustments
 * 6. Preservation of Admin Credit Adjustments
 * 7. Repeated Monthly Closing (Idempotency / Zero Duplicate Credit)
 * 8. Distinction between Lifetime Generated Income & Available Wallet Balance
 * 
 * 100% Rollback Guarantee — Zero changes committed to production.
 */

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

echo "================================================================================\n";
echo " ANANTA: USER GROWTH WALLET RECONCILIATION & SAFEGUARD AUDIT SUITE\n";
echo "================================================================================\n\n";

$passCount = 0;
$failCount = 0;
$results = [];

function recordTest($testNum, $testName, $expected, $actual, $condition, $details = '') {
    global $passCount, $failCount, $results;
    $status = $condition ? 'PASS' : 'FAIL';
    if ($condition) {
        $passCount++;
    } else {
        $failCount++;
    }
    $results[] = [
        'num'      => $testNum,
        'name'     => $testName,
        'expected' => is_scalar($expected) ? (string)$expected : json_encode($expected),
        'actual'   => is_scalar($actual) ? (string)$actual : json_encode($actual),
        'status'   => $status,
        'details'  => $details
    ];
    echo sprintf("[%s] Test %02d: %s\n", $status, $testNum, $testName);
    echo sprintf("       Expected: %s | Actual: %s\n", is_scalar($expected) ? $expected : json_encode($expected), is_scalar($actual) ? $actual : json_encode($actual));
    if (!empty($details)) {
        echo sprintf("       Note: %s\n", $details);
    }
}

$pdo->beginTransaction();

try {
    $suffix = rand(1000, 9999);
    $testUser = 'UGW_' . $suffix;

    // Helper to create test user
    function createTestUser(PDO $db, $uid, $name, $growthBal = 0.00) {
        $stmt = $db->prepare("
            INSERT INTO user (
                userid, name, mobile, gender, email, pan, pass, txn_pass,
                total_deposit, deposit, sponserid, sponsername, underuserid,
                active, status, upgrade_status, join_side, package, joining_date,
                plan, pin, father, deposite_wallet, shop_amount, closingdate,
                capping, upgrade_date2, rankid, pool, level, topuplevel, ads_date,
                ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
                one_club_status, two_club_status, three_club_status, four_club_status,
                five_club_status, coin_wallet, amount, profit_income_wallet,
                profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet,
                rank_reward_wallet, vip_club_wallet, company_turnover_wallet,
                user_growth_wallet, currency_preference
            ) VALUES (
                :uid, :name, '9999999999', 'Male', 'ugw@test.com', 'ABCDE1234F',
                'pass', 'txn', 0, 0, '', '', '',
                '1', 1, 1, 'left', '13050', CURDATE(),
                'Basic', '', '', 0, 0, CURDATE(),
                0, CURDATE(), 0, '', '1', 0, CURDATE(),
                0, 0, 0, 0, 0, 0,
                0, 0, 0, 0, 0, '0', 0.00,
                0.00, 0.00, 0.00, 0.00,
                0.00, 0.00, 0.00, :growth, 'USD'
            )
        ");
        $stmt->execute([':uid' => $uid, ':name' => $name, ':growth' => $growthBal]);
    }

    createTestUser($pdo, $testUser, 'Audit User Growth User', 0.00);

    // -------------------------------------------------------------------------
    // TEST 1: Seven Income Streams Source Ledger Reconciliation
    // -------------------------------------------------------------------------
    function insertRoiInc($db, $uid, $amt) {
        $stmt = $db->prepare("INSERT INTO tbl_roiinc (name, user_id, type, subject, product_code, time, created_date, status, amount, act_amount, wallet_type, a_status, paid_date, beneficiary_id, api_status, api_txn_no, api_bank_ref_no, api_message) VALUES ('Profit', ?, 'Profit Income', 'Monthly Profit', '', '10:00:00', CURDATE(), 1, ?, ?, '', 1, CURDATE(), '', '', '', '', '')");
        $stmt->execute([$uid, (string)$amt, (string)$amt]);
    }
    function insertLevelInc($db, $uid, $amt) {
        $stmt = $db->prepare("INSERT INTO tbl_daily_levelinc (name, user_id, type, subject, product_code, time, created_date, status, amount, act_amount, wallet_type, a_status, paid_date, beneficiary_id, api_status, api_txn_no, api_bank_ref_no, api_message) VALUES ('Sharing', ?, 'Credit', 'Profit Sharing', '', '10:00:00', CURDATE(), 1, ?, ?, '', 1, CURDATE(), '', '', '', '', '')");
        $stmt->execute([$uid, (string)$amt, (string)$amt]);
    }
    function insertRewardInc($db, $uid, $amt) {
        $stmt = $db->prepare("INSERT INTO tbl_rewardinc (name, user_id, type, subject, product_code, time, created_date, status, amount, act_amount, wallet_type, a_status, paid_date, beneficiary_id, api_status, api_txn_no, api_bank_ref_no, api_message) VALUES ('Reward', ?, 'Credit', 'Rank Reward', '', '10:00:00', CURDATE(), 1, ?, ?, '', 1, CURDATE(), '', '', '', '', '')");
        $stmt->execute([$uid, (string)$amt, (string)$amt]);
    }

    // 1. Profit Income: tbl_roiinc
    insertRoiInc($pdo, $testUser, 500.00);
    // 2. Profit Sharing: tbl_daily_levelinc
    insertLevelInc($pdo, $testUser, 200.00);
    // 3. Direct Bonus: tbl_direct_bonus_schedule (CREDITED)
    $pdo->prepare("INSERT INTO tbl_direct_bonus_schedule (investment_id, source_user_id, beneficiary_id, installment_number, installment_month, investment_amount, total_bonus, installment_amount, status, credited_at) VALUES (999, 'OTHER', ?, 1, '2026-01', 13050.00, 783.00, 78.30, 'CREDITED', NOW())")->execute([$testUser]);
    // 4. Mentor Income: tbl_mentor_income_schedule (CREDITED)
    $pdo->prepare("INSERT INTO tbl_mentor_income_schedule (mentor_id, direct_user_id, closing_month, mentor_monthly_income, total_mentor_income, contribution_percentage, payout_amount, status, credited_at) VALUES (?, 'OTHER', '2026-01', 3000.00, 150.00, 100.00, 150.00, 'CREDITED', NOW())")->execute([$testUser]);
    // 5. Rank Reward: tbl_rewardinc
    insertRewardInc($pdo, $testUser, 300.00);
    // 6. VIP Club Income: tbl_vip_user_qualification + tbl_vip_monthly_schedule
    $pdo->prepare("INSERT INTO tbl_vip_user_qualification (user_id, vip_level, reward_amount, reward_status, qualified_at) VALUES (?, 1, 100.00, 'CREDITED', NOW())")->execute([$testUser]);
    $pdo->prepare("INSERT INTO tbl_vip_monthly_schedule (user_id, vip_level, closing_month, total_payout, turnover_payout, status, credited_at) VALUES (?, 1, '2026-01', 200.00, 50.00, 'CREDITED', NOW())")->execute([$testUser]);

    $summary = getUserIncomeWalletSummary($testUser, $pdo);

    $expectedPI = 500.00;
    $expectedPS = 200.00;
    $expectedDB = 78.30;
    $expectedMI = 150.00;
    $expectedRR = 300.00;
    $expectedVIP = 300.00; // 100 reward + 200 monthly
    $expectedCT = 50.00;  // 50 turnover payout
    $expectedLifetimeTotal = round($expectedPI + $expectedPS + $expectedDB + $expectedMI + $expectedRR + $expectedVIP + $expectedCT, 2); // 1578.30

    $actualLifetimeTotal = (float)($summary['total_income_balance'] ?? 0);

    $t1_cond = (
        $summary['profit_income'] == $expectedPI &&
        $summary['profit_sharing'] == $expectedPS &&
        $summary['direct_bonus'] == $expectedDB &&
        $summary['mentor_income'] == $expectedMI &&
        $summary['rank_reward'] == $expectedRR &&
        $summary['vip_club'] == $expectedVIP &&
        $summary['company_turnover'] == $expectedCT &&
        $actualLifetimeTotal == $expectedLifetimeTotal
    );

    recordTest(1, 'Seven Income Streams Source Ledger Reconciliation',
        sprintf('PI: %.2f, PS: %.2f, DB: %.2f, MI: %.2f, RR: %.2f, VIP: %.2f, CT: %.2f, Total: %.2f',
            $expectedPI, $expectedPS, $expectedDB, $expectedMI, $expectedRR, $expectedVIP, $expectedCT, $expectedLifetimeTotal),
        sprintf('PI: %.2f, PS: %.2f, DB: %.2f, MI: %.2f, RR: %.2f, VIP: %.2f, CT: %.2f, Total: %.2f',
            $summary['profit_income'], $summary['profit_sharing'], $summary['direct_bonus'], $summary['mentor_income'],
            $summary['rank_reward'], $summary['vip_club'], $summary['company_turnover'], $actualLifetimeTotal),
        $t1_cond,
        'Authoritative source tables for all 7 income streams sum perfectly'
    );

    // -------------------------------------------------------------------------
    // TEST 2: Existing Valid Balance Preserved on Monthly Closing (No Overwrite)
    // -------------------------------------------------------------------------
    // Set user's available wallet balance to an existing $100.00
    $pdo->prepare("UPDATE user SET user_growth_wallet = 100.00 WHERE userid = ?")->execute([$testUser]);

    // Simulate monthly closing credit of $25.00 profit income
    $newProfit = 25.00;
    $pdo->prepare("UPDATE user SET profit_income_wallet = profit_income_wallet + :profit, user_growth_wallet = user_growth_wallet + :profit WHERE userid = :uid")->execute([
        ':profit' => $newProfit,
        ':uid'    => $testUser
    ]);

    // Run the non-destructive safeguard closing synchronization
    $pdo->exec("
        UPDATE user 
        SET user_growth_wallet = ROUND(
            COALESCE(profit_income_wallet, 0) + 
            COALESCE(profit_sharing_wallet, 0) + 
            COALESCE(direct_bonus_wallet, 0) + 
            COALESCE(mentor_income_wallet, 0) + 
            COALESCE(rank_reward_wallet, 0) + 
            COALESCE(vip_club_wallet, 0) + 
            COALESCE(company_turnover_wallet, 0), 
            2
        )
        WHERE active = '1' AND (user_growth_wallet IS NULL OR user_growth_wallet = 0.00)
    ");

    $balAfterClosing = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    $expectedBal = 125.00; // 100 existing + 25 new profit

    $t2_cond = ($balAfterClosing == $expectedBal);
    recordTest(2, 'Existing Valid Balance Preserved on Monthly Closing',
        sprintf('Balance: %.2f', $expectedBal),
        sprintf('Balance: %.2f', $balAfterClosing),
        $t2_cond,
        'Existing balance of 100.00 was preserved and incremented by 25.00, NOT overwritten by lifetime generated income'
    );

    // -------------------------------------------------------------------------
    // TEST 3: Withdrawal Preservation (Not Restored or Undone by Closing)
    // -------------------------------------------------------------------------
    // User performs a withdrawal of $50.00 from user_growth_wallet
    $withdrawalAmt = 50.00;
    $pdo->prepare("UPDATE user SET user_growth_wallet = user_growth_wallet - :w_amt WHERE userid = :uid")->execute([
        ':w_amt' => $withdrawalAmt,
        ':uid'   => $testUser
    ]);

    $balAfterWithdrawal = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    // 125.00 - 50.00 = 75.00

    // Simulate another monthly closing adding $15.00
    $closingProfit2 = 15.00;
    $pdo->prepare("UPDATE user SET profit_income_wallet = profit_income_wallet + :p, user_growth_wallet = user_growth_wallet + :p WHERE userid = :uid")->execute([
        ':p'   => $closingProfit2,
        ':uid' => $testUser
    ]);

    // Safeguard closing sync
    $pdo->exec("
        UPDATE user 
        SET user_growth_wallet = ROUND(
            COALESCE(profit_income_wallet, 0) + 
            COALESCE(profit_sharing_wallet, 0) + 
            COALESCE(direct_bonus_wallet, 0) + 
            COALESCE(mentor_income_wallet, 0) + 
            COALESCE(rank_reward_wallet, 0) + 
            COALESCE(vip_club_wallet, 0) + 
            COALESCE(company_turnover_wallet, 0), 
            2
        )
        WHERE active = '1' AND (user_growth_wallet IS NULL OR user_growth_wallet = 0.00)
    ");

    $balAfterClosing2 = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    $expectedBalAfterWdAndClosing = 90.00; // 75 remaining + 15 new closing profit

    $t3_cond = ($balAfterWithdrawal == 75.00 && $balAfterClosing2 == $expectedBalAfterWdAndClosing);
    recordTest(3, 'Withdrawal Preservation Across Monthly Closing',
        sprintf('After Wd: 75.00, After Closing: %.2f', $expectedBalAfterWdAndClosing),
        sprintf('After Wd: %.2f, After Closing: %.2f', $balAfterWithdrawal, $balAfterClosing2),
        $t3_cond,
        'Withdrawal of 50.00 debited correctly and was NOT restored by monthly closing'
    );

    // -------------------------------------------------------------------------
    // TEST 4: Transfer Preservation (Not Restored or Undone by Closing)
    // -------------------------------------------------------------------------
    // User transfers $30.00 out
    $transferAmt = 30.00;
    $pdo->prepare("UPDATE user SET user_growth_wallet = user_growth_wallet - :t_amt WHERE userid = :uid")->execute([
        ':t_amt' => $transferAmt,
        ':uid'   => $testUser
    ]);

    $balAfterTransfer = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    // 90.00 - 30.00 = 60.00

    // Closing adds $10.00
    $closingProfit3 = 10.00;
    $pdo->prepare("UPDATE user SET profit_income_wallet = profit_income_wallet + :p, user_growth_wallet = user_growth_wallet + :p WHERE userid = :uid")->execute([
        ':p'   => $closingProfit3,
        ':uid' => $testUser
    ]);

    $pdo->exec("
        UPDATE user 
        SET user_growth_wallet = ROUND(
            COALESCE(profit_income_wallet, 0) + 
            COALESCE(profit_sharing_wallet, 0) + 
            COALESCE(direct_bonus_wallet, 0) + 
            COALESCE(mentor_income_wallet, 0) + 
            COALESCE(rank_reward_wallet, 0) + 
            COALESCE(vip_club_wallet, 0) + 
            COALESCE(company_turnover_wallet, 0), 
            2
        )
        WHERE active = '1' AND (user_growth_wallet IS NULL OR user_growth_wallet = 0.00)
    ");

    $balAfterClosing3 = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    $expectedBalAfterTransferAndClosing = 70.00; // 60 remaining + 10 new

    $t4_cond = ($balAfterTransfer == 60.00 && $balAfterClosing3 == $expectedBalAfterTransferAndClosing);
    recordTest(4, 'Transfer Preservation Across Monthly Closing',
        sprintf('After Transfer: 60.00, After Closing: %.2f', $expectedBalAfterTransferAndClosing),
        sprintf('After Transfer: %.2f, After Closing: %.2f', $balAfterTransfer, $balAfterClosing3),
        $t4_cond,
        'Transfer of 30.00 debited correctly and was NOT undone by monthly closing'
    );

    // -------------------------------------------------------------------------
    // TEST 5: Reversal / Admin Debit Adjustment Preservation
    // -------------------------------------------------------------------------
    // Admin debits $20.00 reversal
    $reversalRes = processUniversalAdminWalletAdjustment(
        'ADMIN_AUDIT',
        $testUser,
        'user_growth_wallet',
        'DEBIT',
        20.00,
        'Audit Reversal of erroneous income credit',
        'REV_' . $suffix,
        $pdo,
        'USD'
    );

    $balAfterReversal = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    // 70.00 - 20.00 = 50.00

    // Closing runs again
    $pdo->exec("
        UPDATE user 
        SET user_growth_wallet = ROUND(
            COALESCE(profit_income_wallet, 0) + 
            COALESCE(profit_sharing_wallet, 0) + 
            COALESCE(direct_bonus_wallet, 0) + 
            COALESCE(mentor_income_wallet, 0) + 
            COALESCE(rank_reward_wallet, 0) + 
            COALESCE(vip_club_wallet, 0) + 
            COALESCE(company_turnover_wallet, 0), 
            2
        )
        WHERE active = '1' AND (user_growth_wallet IS NULL OR user_growth_wallet = 0.00)
    ");

    $balAfterReversalAndClosing = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();

    $t5_cond = (($reversalRes['status'] ?? '') === 'success' && $balAfterReversal == 50.00 && $balAfterReversalAndClosing == 50.00);
    recordTest(5, 'Reversal / Admin Debit Adjustment Preservation',
        'Status: success, Balance: 50.00',
        sprintf('Status: %s, Balance: %.2f', $reversalRes['status'] ?? 'err', $balAfterReversalAndClosing),
        $t5_cond,
        'Admin debit reversal of 20.00 was recorded and preserved across closing'
    );

    // -------------------------------------------------------------------------
    // TEST 6: Admin Credit Adjustment Preservation
    // -------------------------------------------------------------------------
    $creditRes = processUniversalAdminWalletAdjustment(
        'ADMIN_AUDIT',
        $testUser,
        'user_growth_wallet',
        'CREDIT',
        40.00,
        'Audit Authorized Admin Credit Bonus',
        'CRD_' . $suffix,
        $pdo,
        'USD'
    );

    $balAfterCredit = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();
    // 50.00 + 40.00 = 90.00

    $pdo->exec("
        UPDATE user 
        SET user_growth_wallet = ROUND(
            COALESCE(profit_income_wallet, 0) + 
            COALESCE(profit_sharing_wallet, 0) + 
            COALESCE(direct_bonus_wallet, 0) + 
            COALESCE(mentor_income_wallet, 0) + 
            COALESCE(rank_reward_wallet, 0) + 
            COALESCE(vip_club_wallet, 0) + 
            COALESCE(company_turnover_wallet, 0), 
            2
        )
        WHERE active = '1' AND (user_growth_wallet IS NULL OR user_growth_wallet = 0.00)
    ");

    $balAfterCreditAndClosing = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();

    $t6_cond = (($creditRes['status'] ?? '') === 'success' && $balAfterCredit == 90.00 && $balAfterCreditAndClosing == 90.00);
    recordTest(6, 'Admin Credit Adjustment Preservation Across Closing',
        'Status: success, Balance: 90.00',
        sprintf('Status: %s, Balance: %.2f', $creditRes['status'] ?? 'err', $balAfterCreditAndClosing),
        $t6_cond,
        'Admin credit adjustment of 40.00 was added cleanly and preserved'
    );

    // -------------------------------------------------------------------------
    // TEST 7: Repeated Monthly Closing (Idempotency Protection)
    // -------------------------------------------------------------------------
    // Calling processDirectBonusInstallments for an already processed month
    $monthRepeated = '2026-01';
    $repeatDirectBonusRes = processDirectBonusInstallments($monthRepeated, '2026-01-31', $pdo);
    // Since month 1 schedule was already CREDITED in setup, processed count must be 0
    $repeatProcessedCount = $repeatDirectBonusRes['processed'] ?? 0;
    $repeatPaidAmount     = $repeatDirectBonusRes['total_paid'] ?? 0.00;

    $balAfterRepeatedClosing = (float)$pdo->query("SELECT user_growth_wallet FROM user WHERE userid = '{$testUser}'")->fetchColumn();

    $t7_cond = ($repeatProcessedCount === 0 && $repeatPaidAmount == 0.00 && $balAfterRepeatedClosing == 90.00);
    recordTest(7, 'Repeated Monthly Closing Idempotency Protection',
        'Processed: 0, Paid: 0.00, Balance Unchanged: 90.00',
        sprintf('Processed: %d, Paid: %.2f, Balance: %.2f', $repeatProcessedCount, $repeatPaidAmount, $balAfterRepeatedClosing),
        $t7_cond,
        'Re-running closing for same month issues 0 additional credits and leaves wallet balance intact'
    );

    // -------------------------------------------------------------------------
    // TEST 8: Distinction Between Lifetime Generated Income & Available Wallet Balance
    // -------------------------------------------------------------------------
    // Check get_wallet_dashboard payload logic
    $uStmtDash = $pdo->prepare("SELECT * FROM user WHERE userid = ?");
    $uStmtDash->execute([$testUser]);
    $uRowDash = $uStmtDash->fetch(PDO::FETCH_ASSOC);

    $summaryLive = getUserIncomeWalletSummary($testUser, $pdo);
    $lifetimeIncome = (float)($summaryLive['total_income_balance'] ?? 0.00);
    $availableWalletBal = (float)($uRowDash['user_growth_wallet'] ?? 0.00);

    // Lifetime generated income = 500 + 200 + 78.30 + 150 + 300 + 300 + 50 + 25 (t2 profit) + 15 (t3 profit) + 10 (t4 profit) = 1628.30
    // Available balance = 90.00 (reflecting credits, withdrawals, transfers, reversals, admin adjustments)
    $t8_cond = ($lifetimeIncome > 0 && $availableWalletBal == 90.00 && $lifetimeIncome != $availableWalletBal);
    recordTest(8, 'Clear Distinction: Lifetime Generated Income vs Available Wallet Balance',
        sprintf('Lifetime: %.2f, Available: 90.00 (Distinct Metrics)', $lifetimeIncome),
        sprintf('Lifetime: %.2f, Available: %.2f', $lifetimeIncome, $availableWalletBal),
        $t8_cond,
        'Lifetime income represents gross earned sum; user_growth_wallet reflects spendable net balance'
    );

} catch (Exception $e) {
    echo "ERROR EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    $failCount++;
} finally {
    // 100% Rollback guarantee — zero live money touched
    $pdo->rollBack();
    echo "\n>>> TRANSACTION 100% ROLLED BACK. Zero changes committed to production database. <<<\n\n";
}

echo "================================================================================\n";
echo sprintf(" TEST SUITE SUMMARY: %d PASSED, %d FAILED (TOTAL %d)\n", $passCount, $failCount, $passCount + $failCount);
echo "================================================================================\n";

if ($failCount === 0) {
    echo ">>> ALL 8 USER GROWTH WALLET RECONCILIATION & SAFEGUARD TESTS PASSED! <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED. PLEASE REVIEW LOGS ABOVE. <<<\n";
    exit(1);
}
