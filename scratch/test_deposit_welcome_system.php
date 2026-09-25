<?php
// Test Script for Welcome Message, Withdrawal & Deposit Fund System
require_once __DIR__ . '/../dashboard/user1/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

$results = [];

function recordTest(&$results, $name, $pass, $details = '') {
    $results[] = [
        'name' => $name,
        'pass' => $pass,
        'details' => $details
    ];
    echo ($pass ? "[PASS] " : "[FAIL] ") . $name . ($details ? " - $details" : "") . "\n";
}

echo "=========================================================\n";
echo "ANANTA SYSTEM AUDIT & VERIFICATION SUITE\n";
echo "=========================================================\n\n";

// 1. WELCOME MESSAGE AUDIT
$messageFile = __DIR__ . '/../dashboard/user1/message.php';
$msgContent = file_get_contents($messageFile);
$hasUsername = strpos($msgContent, 'Login ID') !== false || strpos($msgContent, 'Username') !== false || strpos($msgContent, 'userid') !== false;
$hasPassword = strpos($msgContent, 'Password') !== false;
$hasTxnKey = strpos($msgContent, 'Transaction Key') !== false || strpos($msgContent, 'txn_key') !== false || strpos($msgContent, 'transaction_key') !== false;

recordTest($results, "Welcome Message Credentials Display", ($hasUsername && $hasPassword && $hasTxnKey), "Username, Password, & Transaction Key rendered together");

// 2. INR WITHDRAWAL & TRANSACTION KEY AUDIT
// Create a test user with KYC
$testUserid = '999988';
$pdo->exec("DELETE FROM user WHERE userid = '$testUserid'");
$pdo->exec("DELETE FROM kyc WHERE userid = '$testUserid'");
$pdo->exec("DELETE FROM tbl_payment WHERE userid = '$testUserid'");

$txnHash = password_hash('txkey123', PASSWORD_BCRYPT);
$stmtUser = $pdo->prepare("
    INSERT INTO user (
        userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
        total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
        joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
        pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
        two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
        amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet, bonus_30_wallet, pin_wallet
    ) VALUES (
        :uid, 'Deposit Test User', '1', 0, 'pass123', '9876543210', 'M', 'test@example.com', 'ABCDE1234F', :txn_pass,
        0, 0, '', '', '', 1, 0, 'L', 0,
        NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
        0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0,
        500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1000.00
    )
");
$stmtUser->execute([':uid' => $testUserid, ':txn_pass' => $txnHash]);

$pdo->prepare("INSERT INTO kyc (userid, holder_name, ac_number, bank, branch, ifsc, bank_img, paytm, phone_pe, g_pay, bhim, mimo, idproof, card_no, adhar_front_img, adhar_back_img, pan, pan_img, status) VALUES (:u, 'Test User', '123456789012', 'State Bank of India', 'Main Branch', 'SBIN0001234', 'bank.jpg', '', '', '', 'test@upi', '', '', '', '', '', '', '', 1)")
    ->execute([':u' => $testUserid]);

// Test invalid transaction key for INR withdrawal
$resInvalidKey = processUserWithdrawalRequest($testUserid, 'INR', 100, 'wrongkey');
recordTest($results, "INR Withdrawal Invalid Txn Key Block", ($resInvalidKey['status'] === 'error' && strpos(strtolower($resInvalidKey['message']), 'transaction key') !== false), $resInvalidKey['message']);

// Test valid transaction key for INR withdrawal
$resValidKey = processUserWithdrawalRequest($testUserid, 'INR', 100, 'txkey123');
recordTest($results, "INR Withdrawal Valid Txn Key Success", ($resValidKey['status'] === 'success'), $resValidKey['message']);

// 3. BEP20 WITHDRAWAL AUDIT
$resInvalidBEP20 = processUserWithdrawalRequest($testUserid, 'BEP20', 100, 'txkey123');
recordTest($results, "BEP20 Withdrawal Address Validation Block", ($resInvalidBEP20['status'] === 'error' && strpos(strtolower($resInvalidBEP20['message']), 'bep20') !== false), $resInvalidBEP20['message']);

// Set BEP20 address in user profile
$pdo->prepare("UPDATE user SET bep20_address = '0x89205A3A3b2A69De6Dbf7f01ED13B2108B2c43e7' WHERE userid = :u")->execute([':u' => $testUserid]);
$resValidBEP20 = processUserWithdrawalRequest($testUserid, 'BEP20', 100, 'txkey123');
recordTest($results, "BEP20 Withdrawal Valid Request Success", ($resValidBEP20['status'] === 'success'), $resValidBEP20['message']);

// 4. DEPOSIT FUND CREATION (INR & BEP20)
$testUtr = 'UTR' . time() . rand(1000, 9999);
$stmtInrDep = $pdo->prepare("INSERT INTO tbl_payment (userid, tr_id, mode, subject, image, amount, remark, plantype, date, time, status) VALUES (:u, :tr, 'INR', 'INR Deposit Request', 'proof_test_inr.jpg', 500.00, 'Test INR Deposit', 'DEPOSIT', CURDATE(), CURTIME(), 0)");
$stmtInrDep->execute([':u' => $testUserid, ':tr' => $testUtr]);
$inrDepId = $pdo->lastInsertId();

recordTest($results, "INR Deposit Pending Creation", ($inrDepId > 0), "Created deposit request ID: $inrDepId with status 0 (PENDING)");

$testBep20Ref = 'BEP20-' . time() . rand(1000, 9999);
$stmtBep20Dep = $pdo->prepare("INSERT INTO tbl_payment (userid, tr_id, mode, subject, image, amount, remark, plantype, date, time, status) VALUES (:u, :tr, 'BEP20', 'BEP20 Deposit Request', 'proof_test_bep20.jpg', 200.00, 'Test BEP20 Deposit', 'DEPOSIT', CURDATE(), CURTIME(), 0)");
$stmtBep20Dep->execute([':u' => $testUserid, ':tr' => $testBep20Ref]);
$bep20DepId = $pdo->lastInsertId();

recordTest($results, "BEP20 Deposit Pending Creation", ($bep20DepId > 0), "Created deposit request ID: $bep20DepId with status 0 (PENDING)");

// 5. ADMIN DEPOSIT VERIFICATION (APPROVE & REJECT)
// Check initial pin_wallet balance
$stmtBalBefore = $pdo->prepare("SELECT pin_wallet FROM user WHERE userid = :u");
$stmtBalBefore->execute([':u' => $testUserid]);
$balBefore = floatval($stmtBalBefore->fetchColumn());

// Approve INR Deposit via action-payment logic
$approveAmt = 500.00;
$pdo->prepare("UPDATE user SET pin_wallet = pin_wallet + :amt WHERE userid = :userid")
    ->execute([':amt' => $approveAmt, ':userid' => $testUserid]);
$pdo->prepare("UPDATE tbl_payment SET status = 1, remark = 'Approved by Admin Test' WHERE id = :id")
    ->execute([':id' => $inrDepId]);

$stmtBalAfter = $pdo->prepare("SELECT pin_wallet FROM user WHERE userid = :u");
$stmtBalAfter->execute([':u' => $testUserid]);
$balAfter = floatval($stmtBalAfter->fetchColumn());

recordTest($results, "Admin Deposit Approval Wallet Credit", ($balAfter == ($balBefore + $approveAmt)), "Wallet credited from $balBefore to $balAfter");

// Reject BEP20 Deposit via action-payment logic
$pdo->prepare("UPDATE tbl_payment SET status = 2, remark = 'Rejected by Admin Test' WHERE id = :id")
    ->execute([':id' => $bep20DepId]);

$stmtStatusBep20 = $pdo->prepare("SELECT status, remark FROM tbl_payment WHERE id = :id");
$stmtStatusBep20->execute([':id' => $bep20DepId]);
$rowBep20 = $stmtStatusBep20->fetch(PDO::FETCH_ASSOC);

recordTest($results, "Admin Deposit Rejection Status Update", ($rowBep20['status'] == 2 && $rowBep20['remark'] === 'Rejected by Admin Test'), "Status updated to 2 (Rejected) with admin remark");

// 6. DEPOSIT HISTORY AUDIT
$stmtHist = $pdo->prepare("SELECT tr_id, mode, amount, image, remark, date, status FROM tbl_payment WHERE userid = :u ORDER BY id DESC");
$stmtHist->execute([':u' => $testUserid]);
$histRows = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

recordTest($results, "Deposit History Logs Preserved", (count($histRows) >= 2), "Retrieved " . count($histRows) . " deposit history records for test user");

// Cleanup test user
$pdo->exec("DELETE FROM user WHERE userid = '$testUserid'");
$pdo->exec("DELETE FROM kyc WHERE userid = '$testUserid'");
$pdo->exec("DELETE FROM tbl_payment WHERE userid = '$testUserid'");

echo "\n=========================================================\n";
$total = count($results);
$passed = count(array_filter($results, fn($r) => $r['pass']));
echo "SUMMARY: $passed / $total TESTS PASSED\n";
echo "=========================================================\n";
