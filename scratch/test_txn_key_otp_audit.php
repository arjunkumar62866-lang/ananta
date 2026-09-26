<?php
// Scratch test script to verify OTP and Transaction Key Reset flow
require_once __DIR__ . '/../dashboard/user1/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "=== START TRANSACTION KEY OTP TEST ===\n";

// 1. Ensure table auto-provisioning
ensureOTPTableExists($pdo);
echo "[TEST 1] ensureOTPTableExists executed successfully.\n";

// 2. Fetch an existing user from DB
$stmtUser = $pdo->query("SELECT userid, email FROM user WHERE email IS NOT NULL AND email != '' LIMIT 1");
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$userRow) {
    die("No test user found in user table.\n");
}

$testUid = $userRow['userid'];
echo "[TEST 2] Using user ID: {$testUid} ({$userRow['email']})\n";

// Clear previous test OTPs for clean state
$pdo->prepare("DELETE FROM tbl_otp WHERE userid = :uid")->execute([':uid' => $testUid]);

// 3. Test sendTransactionKeyOTP
$resSend = sendTransactionKeyOTP($testUid, $pdo);
echo "[TEST 3] sendTransactionKeyOTP response: " . json_encode($resSend) . "\n";
assert($resSend['status'] === 'success', "Send OTP failed");

// Fetch the generated OTP from DB
$stmtOtp = $pdo->prepare("SELECT otp, is_used FROM tbl_otp WHERE userid = :uid AND type = 'TXN_KEY_RESET' AND is_used = 0 ORDER BY id DESC LIMIT 1");
$stmtOtp->execute([':uid' => $testUid]);
$otpRow = $stmtOtp->fetch(PDO::FETCH_ASSOC);
echo "[TEST 4] OTP fetched from DB: " . json_encode($otpRow) . "\n";
assert(!empty($otpRow['otp']), "OTP not found in DB");

// 4. Test wrong OTP
$resWrong = verifyTransactionKeyOTP($testUid, '000000', $pdo);
echo "[TEST 5] Wrong OTP verification response: " . json_encode($resWrong) . "\n";
assert($resWrong['status'] === 'error', "Wrong OTP should fail");

// 5. Test valid OTP verification
$resVerify = verifyTransactionKeyOTP($testUid, $otpRow['otp'], $pdo);
echo "[TEST 6] Valid OTP verification response: " . json_encode($resVerify) . "\n";
assert($resVerify['status'] === 'success', "Valid OTP verification failed");

// 6. Test reused OTP (should fail now because is_used = 1)
$resReused = verifyTransactionKeyOTP($testUid, $otpRow['otp'], $pdo);
echo "[TEST 7] Reused OTP verification response: " . json_encode($resReused) . "\n";
assert($resReused['status'] === 'error', "Reused OTP should fail");

// 7. Test setting new Transaction Key
$resSet = setTransactionKey($testUid, '9999', $pdo);
echo "[TEST 8] setTransactionKey response: " . json_encode($resSet) . "\n";
assert($resSet['status'] === 'success', "Set transaction key failed");

// Verify password_hash verification
$verKey = verifyTransactionKey($testUid, '9999', $pdo);
echo "[TEST 9] verifyTransactionKey with new key '9999': " . json_encode($verKey) . "\n";
assert($verKey['status'] === 'success', "New transaction key verification failed");

// Cleanup test OTPs
$pdo->prepare("DELETE FROM tbl_otp WHERE userid = :uid")->execute([':uid' => $testUid]);

echo "=== ALL OTP & TXN KEY TESTS PASSED SUCCESSFULLY ===\n";
