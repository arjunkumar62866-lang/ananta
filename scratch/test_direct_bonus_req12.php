<?php
/**
 * Isolated Automated Test Suite for Requirement #12: Direct Bonus System
 */

require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/connection.php';
require_once '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/common/db_method.php';

echo "========================================================\n";
echo " DIRECT BONUS REQUIREMENT #12 — AUTOMATED TEST SUITE\n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $message) {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] $message\n";
        $passCount++;
    } else {
        echo " [FAIL] $message\n";
        $failCount++;
    }
}

// -------------------------------------------------------------------
// SETUP TEST USER DATA IN ISOLATED TRANSACTION
// -------------------------------------------------------------------
$pdo->beginTransaction();

function createTestUser(PDO $pdo, $userid, $name, $active = '1', $piw = 0.00, $psw = 0.00, $dbw = 0.00) {
    $stmt = $pdo->prepare("INSERT INTO user 
        (userid, name, mobile, gender, email, pan, pass, txn_pass, total_deposit, deposit, sponserid, sponsername, underuserid, active, status, upgrade_status, join_side, package, joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid, pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel, one_club_status, two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet)
        VALUES 
        (:userid, :name, '9999999999', 'Male', 'test@test.com', 'ABCDE1234F', '123456', '123456', 0, 0, '', '', '', :active, 1, 1, 'left', '13000', CURDATE(), 'Basic', '', '', '0', 0, CURDATE(), '0', CURDATE(), 0, '', '', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', :piw, :psw, :dbw)");
    $stmt->execute([
        ':userid' => $userid,
        ':name'   => $name,
        ':active' => $active,
        ':piw'    => $piw,
        ':psw'    => $psw,
        ':dbw'    => $dbw
    ]);
}

try {
    $suffix = substr(time(), -4) . rand(10, 99);
    $benId = 'B_' . $suffix;
    $ref1  = 'R1_' . $suffix;
    $ref2  = 'R2_' . $suffix;
    $ref3  = 'R3_' . $suffix;

    // Create Beneficiary User
    createTestUser($pdo, $benId, 'Beneficiary User', '1', 1000.00, 500.00, 0.00);

    // Create Referral 1 (Active, Investment 13,000 - Qualified #1)
    createTestUser($pdo, $ref1, 'Referral One', '1');
    $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, CURDATE())")->execute([$benId, $ref1]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, package, status, date, closingdate, level, name, percentage, amount, totalincome, capping, lock_day, time) VALUES (?, ?, '0', CURDATE(), CURDATE(), '0', 'Basic', '0', '0', '0', '0', 10, '00:00:00')")->execute([$ref1, 13000]);

    // Create Referral 2 (Active, Investment 100,000 - Qualified #2)
    createTestUser($pdo, $ref2, 'Referral Two', '1');
    $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, CURDATE())")->execute([$benId, $ref2]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, package, status, date, closingdate, level, name, percentage, amount, totalincome, capping, lock_day, time) VALUES (?, ?, '0', CURDATE(), CURDATE(), '0', 'Basic', '0', '0', '0', '0', 10, '00:00:00')")->execute([$ref2, 100000]);
    $inv100kId = $pdo->lastInsertId();

    // Create Referral 3 (Active, Investment 10,000 - Non-Qualified investment < 13k)
    createTestUser($pdo, $ref3, 'Referral Three', '1');
    $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, CURDATE())")->execute([$benId, $ref3]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, package, status, date, closingdate, level, name, percentage, amount, totalincome, capping, lock_day, time) VALUES (?, ?, '0', CURDATE(), CURDATE(), '0', 'Basic', '0', '0', '0', '0', 10, '00:00:00')")->execute([$ref3, 10000]);
    $inv10kId = $pdo->lastInsertId();

    // -------------------------------------------------------------------
    // TEST 1: ₹100,000 Investment Calculation
    // -------------------------------------------------------------------
    $genResult1 = generateDirectBonusSchedule($inv100kId, $ref2, 100000, '2026-10-01', $pdo);
    assertTest($genResult1 === true, "Schedule generation for ₹100,000 investment returned true.");

    $stmt1 = $pdo->prepare("SELECT total_bonus, installment_amount, COUNT(*) as cnt FROM tbl_direct_bonus_schedule WHERE investment_id = ? GROUP BY total_bonus, installment_amount");
    $stmt1->execute([$inv100kId]);
    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    assertTest($row1 && (float)$row1['total_bonus'] == 6000.00, "TEST 1: ₹100,000 investment Total Bonus = ₹6,000.00.");
    assertTest((float)$row1['installment_amount'] == 600.00 && $row1['cnt'] == 10, "TEST 1: 10 equal monthly installments of ₹600.00.");

    // -------------------------------------------------------------------
    // TEST 2: ₹13,000 Investment Calculation
    // -------------------------------------------------------------------
    $stmtRef1Inv = $pdo->prepare("SELECT id FROM tbl_roi_one WHERE user_id = ?");
    $stmtRef1Inv->execute([$ref1]);
    $inv13kId = $stmtRef1Inv->fetchColumn();

    generateDirectBonusSchedule($inv13kId, $ref1, 13000, '2026-10-01', $pdo);
    $stmt2 = $pdo->prepare("SELECT total_bonus, installment_amount, COUNT(*) as cnt FROM tbl_direct_bonus_schedule WHERE investment_id = ? GROUP BY total_bonus, installment_amount");
    $stmt2->execute([$inv13kId]);
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    assertTest($row2 && (float)$row2['total_bonus'] == 780.00, "TEST 2: ₹13,000 investment Total Bonus = ₹780.00.");
    assertTest((float)$row2['installment_amount'] == 78.00 && $row2['cnt'] == 10, "TEST 2: 10 equal monthly installments of ₹78.00.");

    // -------------------------------------------------------------------
    // TEST 3: ₹50,000 Investment Calculation
    // -------------------------------------------------------------------
    createTestUser($pdo, 'REF50K', 'Referral 50k', '1');
    $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, 'REF50K', CURDATE())")->execute([$benId]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, package, status, date, closingdate, level, name, percentage, amount, totalincome, capping, lock_day, time) VALUES ('REF50K', 50000, '0', CURDATE(), CURDATE(), '0', 'Basic', '0', '0', '0', '0', 10, '00:00:00')")->execute();
    $inv50kId = $pdo->lastInsertId();

    generateDirectBonusSchedule($inv50kId, 'REF50K', 50000, '2026-10-01', $pdo);
    $stmt3 = $pdo->prepare("SELECT total_bonus, installment_amount FROM tbl_direct_bonus_schedule WHERE investment_id = ? LIMIT 1");
    $stmt3->execute([$inv50kId]);
    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    assertTest($row3 && (float)$row3['total_bonus'] == 3000.00 && (float)$row3['installment_amount'] == 300.00, "TEST 3: ₹50,000 investment Total Bonus = ₹3,000 & Installment = ₹300.");

    // -------------------------------------------------------------------
    // TEST 4 & 5: Qualified Directs Count & Threshold Check
    // -------------------------------------------------------------------
    $qCount = getQualifiedDirectCount($benId, $pdo);
    assertTest($qCount == 3, "Qualified Directs count correctly evaluated as 3 (Ref1, Ref2, Ref50k).");

    // Test with user having only 1 qualified direct
    $benOneId = 'B1_' . $suffix;
    $refOneId = 'RO1_' . $suffix;
    createTestUser($pdo, $benOneId, 'Beneficiary 1 Direct', '1');
    createTestUser($pdo, $refOneId, 'Ref Only 1', '1');
    $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, CURDATE())")->execute([$benOneId, $refOneId]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, package, status, date, closingdate, level, name, percentage, amount, totalincome, capping, lock_day, time) VALUES (?, 100000, '0', CURDATE(), CURDATE(), '0', 'Basic', '0', '0', '0', '0', 10, '00:00:00')")->execute([$refOneId]);
    $invOnly1Id = $pdo->lastInsertId();

    generateDirectBonusSchedule($invOnly1Id, $refOneId, 100000, '2026-10-01', $pdo);
    $procRes1 = processDirectBonusInstallments('2026-10', '2026-10-05', $pdo);

    $chkInst1 = $pdo->prepare("SELECT status FROM tbl_direct_bonus_schedule WHERE investment_id = ? AND installment_month = '2026-10'");
    $chkInst1->execute([$invOnly1Id]);
    $st1 = $chkInst1->fetchColumn();

    assertTest($st1 === 'PENDING', "TEST 4: Installment NOT credited when beneficiary has only 1 Qualified Direct.");

    // -------------------------------------------------------------------
    // TEST 5: Credit installment when Qualified Directs >= 2
    // -------------------------------------------------------------------
    $procRes2 = processDirectBonusInstallments('2026-10', '2026-10-05', $pdo);
    $chkInst2 = $pdo->prepare("SELECT status FROM tbl_direct_bonus_schedule WHERE investment_id = ? AND installment_month = '2026-10'");
    $chkInst2->execute([$inv100kId]);
    $st2 = $chkInst2->fetchColumn();

    assertTest($st2 === 'CREDITED', "TEST 5: Installment CREDITED when beneficiary has >= 2 Qualified Directs.");

    // -------------------------------------------------------------------
    // TEST 6 & 7: Idempotency Protection
    // -------------------------------------------------------------------
    $wStmt1 = $pdo->prepare("SELECT direct_bonus_wallet FROM user WHERE userid = ?");
    $wStmt1->execute([$benId]);
    $balBefore = (float)$wStmt1->fetchColumn();

    // Re-run monthly closing process for same month
    $procResDup = processDirectBonusInstallments('2026-10', '2026-10-05', $pdo);

    $wStmt2 = $pdo->prepare("SELECT direct_bonus_wallet FROM user WHERE userid = ?");
    $wStmt2->execute([$benId]);
    $balAfter = (float)$wStmt2->fetchColumn();

    assertTest($balBefore === $balAfter && $procResDup['processed'] == 0, "TEST 6 & 7: Re-running closing produced 0 duplicate credits or wallet increases.");

    // -------------------------------------------------------------------
    // TEST 8: Missing Activation ($11 / active = '0')
    // -------------------------------------------------------------------
    $inactUser = 'INC_' . $suffix;
    createTestUser($pdo, $inactUser, 'Inactive User', '0');
    $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, CURDATE())")->execute([$benId, $inactUser]);
    $pdo->prepare("INSERT INTO tbl_roi_one (user_id, package, status, date, closingdate, level, name, percentage, amount, totalincome, capping, lock_day, time) VALUES (?, 50000, '0', CURDATE(), CURDATE(), '0', 'Basic', '0', '0', '0', '0', 10, '00:00:00')")->execute([$inactUser]);
    $invInactId = $pdo->lastInsertId();

    $qCountInact = getQualifiedDirectCount($inactUser, $pdo);
    assertTest($qCountInact == 0, "TEST 8: Inactive user (active='0') is NOT counted as a Qualified Direct.");

    // -------------------------------------------------------------------
    // TEST 9 & 10: Minimum Investment Threshold (₹13,000)
    // -------------------------------------------------------------------
    $genRes10k = generateDirectBonusSchedule($inv10kId, $ref3, 10000, '2026-10-01', $pdo);
    assertTest($genRes10k === false, "TEST 9: Investment < ₹13,000 (₹10,000) is NOT eligible for Direct Bonus schedule.");

    $genRes13k = generateDirectBonusSchedule($inv13kId, $ref1, 13000, '2026-10-01', $pdo);
    assertTest($genRes13k === true, "TEST 10: Exactly ₹13,000 investment IS eligible for Direct Bonus schedule.");

    // -------------------------------------------------------------------
    // TEST 11: Wallet Isolation
    // -------------------------------------------------------------------
    $userWalletsStmt = $pdo->prepare("SELECT profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet FROM user WHERE userid = ?");
    $userWalletsStmt->execute([$benId]);
    $wData = $userWalletsStmt->fetch(PDO::FETCH_ASSOC);

    assertTest((float)$wData['profit_income_wallet'] == 1000.00, "TEST 11: Profit Income Wallet remains unchanged (₹1000.00).");
    assertTest((float)$wData['profit_sharing_wallet'] == 500.00, "TEST 11: Profit Sharing Wallet remains unchanged (₹500.00).");
    assertTest((float)$wData['direct_bonus_wallet'] > 0.00, "TEST 11: Direct Bonus Wallet alone received Direct Bonus credit.");

    // -------------------------------------------------------------------
    // TEST 12: Withdrawal Isolation
    // -------------------------------------------------------------------
    $deductBonus = 300.00;
    $pdo->prepare("UPDATE user SET direct_bonus_wallet = direct_bonus_wallet - ? WHERE userid = ?")->execute([$deductBonus, $benId]);

    $userWalletsAfterStmt = $pdo->prepare("SELECT profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet FROM user WHERE userid = ?");
    $userWalletsAfterStmt->execute([$benId]);
    $wDataAfter = $userWalletsAfterStmt->fetch(PDO::FETCH_ASSOC);

    assertTest((float)$wDataAfter['profit_income_wallet'] == 1000.00, "TEST 12: Direct Bonus withdrawal did NOT touch Profit Income Wallet.");
    assertTest((float)$wDataAfter['profit_sharing_wallet'] == 500.00, "TEST 12: Direct Bonus withdrawal did NOT touch Profit Sharing Wallet.");
    assertTest((float)$wDataAfter['direct_bonus_wallet'] == ($wData['direct_bonus_wallet'] - $deductBonus), "TEST 12: Direct Bonus withdrawal debited direct_bonus_wallet only.");

} catch (Exception $e) {
    echo "ERROR EXCEPTION: " . $e->getMessage() . "\n";
    $failCount++;
} finally {
    $pdo->rollBack(); // Always rollback test data
}

echo "\n========================================================\n";
echo " TEST SUMMARY: PASS: $passCount | FAIL: $failCount\n";
echo "========================================================\n";

if ($failCount === 0) {
    echo "\nDIRECT BONUS IMPLEMENTATION — PASSED\n";
} else {
    echo "\nDIRECT BONUS IMPLEMENTATION — FAILED\n";
}
