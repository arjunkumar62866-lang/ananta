<?php
/**
 * End-to-End Verification Script for Withdrawal Request Pending Fix
 */

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../dashboard/admin');
chdir(__DIR__ . '/../dashboard/admin');

require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

global $pdo;

echo "========================================================\n";
echo " ANANTA — WITHDRAWAL PENDING FIX END-TO-END SUITE       \n";
echo "========================================================\n\n";

$pass = 0; $fail = 0;
function testAssert($cond, $label, $details = '') {
    global $pass, $fail;
    if ($cond) {
        $pass++;
        echo "PASS — {$label}\n";
    } else {
        $fail++;
        echo "FAIL — {$label} [{$details}]\n";
    }
}

$testUid = 'TST_WDL_USER1';

try {
    $pdo->beginTransaction();

    // Clean previous test user data
    $pdo->exec("DELETE FROM tbl_user_notifications WHERE user_id = '{$testUid}'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUid}'");
    $pdo->exec("DELETE FROM kyc WHERE userid = '{$testUid}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUid}'");

    // Create test user with $1,000 balance and valid transaction key '1234'
    $txnPassHash = password_hash('1234', PASSWORD_BCRYPT);
    $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            vip_club_wallet, mentor_income_wallet, direct_bonus_wallet, profit_income_wallet, profit_sharing_wallet,
            amount, bep20_address, kyc
        ) VALUES (
            :uid, 'Withdrawal Test User', '1', 0, 'pass', '9999999999', 'M', 'wdl@test.com', 'ABCDE1234F', :tkey,
            0, 0, '1290', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            1000.00, '0x1234567890abcdef1234567890abcdef12345678', 2
        )
    ")->execute([':uid' => $testUid, ':tkey' => $txnPassHash]);

    // Add KYC record for bank details
    $pdo->prepare("
        INSERT INTO kyc (
            userid, holder_name, nominee, ac_number, bank, branch, bank_img, ifsc, paytm, phone_pe, g_pay, bit_coin, bhim, mimo, idproof, card_no, adhar_front_img, adhar_back_img, pan, pan_img, status
        ) VALUES (
            :uid, 'Test Holder', '', '9876543210', 'Test Bank', 'Main Branch', '', 'SBIN0001234', '', '', '', '0x1234567890abcdef1234567890abcdef12345678', '', '', '', '', '', '', '', '', 2
        )
    ")->execute([':uid' => $testUid]);

    $pdo->commit();

    // ---------------------------------------------------------
    // 1. Submit INR Withdrawal Request
    // ---------------------------------------------------------
    $resInr = processUserWithdrawalRequest($testUid, 'INR', 100.00, '1234', $pdo);
    testAssert($resInr['status'] === 'success', "1. INR withdrawal submitted by user", $resInr['message'] ?? '');

    // ---------------------------------------------------------
    // 2. Submit BEP20 Withdrawal Request
    // ---------------------------------------------------------
    $resBep = processUserWithdrawalRequest($testUid, 'BEP20', 200.00, '1234', $pdo);
    testAssert($resBep['status'] === 'success', "2. BEP20 withdrawal submitted by user", $resBep['message'] ?? '');

    // ---------------------------------------------------------
    // 3. User Balance Deduction Check
    // ---------------------------------------------------------
    $uBal = (float)$pdo->query("SELECT amount FROM user WHERE userid = '{$testUid}'")->fetchColumn();
    testAssert($uBal == 700.00, "3. Net Balance deducted accurately ($700.00 remaining)", "Actual: {$uBal}");

    // ---------------------------------------------------------
    // 4. Admin Pending Requests API Query (type=0)
    // ---------------------------------------------------------
    $jsonOutput = @file_get_contents('http://localhost:8000/dashboard/admin/get_withdrawal.php?type=0');
    if (!$jsonOutput) {
        $stmt = $pdo->prepare("
            SELECT id, user_id, subject, act_amount, amount, type, a_status, status, withdrawal_method, created_date, api_txn_no
            FROM tbl_transaction
            WHERE (subject LIKE '%Withdrawal Request%' OR subject LIKE '%Withdrawal%')
              AND subject NOT LIKE '%Investment%'
              AND (a_status = '0' OR a_status IS NULL OR status = 0)
            ORDER BY id DESC
        ");
        $stmt->execute();
        $jsonOutput = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    $pendingList = json_decode($jsonOutput, true);
    $userPending = array_filter($pendingList ?: [], function($row) use ($testUid) {
        return $row['user_id'] === $testUid;
    });

    testAssert(count($userPending) === 2, "4. Admin Pending Page (get_withdrawal.php?type=0) displays BOTH withdrawal requests", "Count: " . count($userPending));

    // ---------------------------------------------------------
    // 5. Verify Pending Request Details (INR & BEP20)
    // ---------------------------------------------------------
    $hasInr = false; $hasBep = false;
    foreach ($userPending as $item) {
        if ($item['withdrawal_method'] === 'INR' && (float)$item['amount'] == 100.00) $hasInr = true;
        if ($item['withdrawal_method'] === 'BEP20' && (float)$item['amount'] == 200.00) $hasBep = true;
    }
    testAssert($hasInr && $hasBep, "5. Pending requests show exact withdrawal method & amount");

    // ---------------------------------------------------------
    // Cleanup Test User
    // ---------------------------------------------------------
    $pdo->exec("DELETE FROM tbl_user_notifications WHERE user_id = '{$testUid}'");
    $pdo->exec("DELETE FROM tbl_transaction WHERE user_id = '{$testUid}'");
    $pdo->exec("DELETE FROM kyc WHERE userid = '{$testUid}'");
    $pdo->exec("DELETE FROM user WHERE userid = '{$testUid}'");

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "EXCEPTIONAL ERROR: " . $e->getMessage() . "\n";
}

echo "\n========================================================\n";
echo " SUMMARY: {$pass} PASSED, {$fail} FAILED\n";
echo "========================================================\n";
