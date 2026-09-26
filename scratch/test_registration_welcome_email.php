<?php
require_once __DIR__ . '/../dashboard/user1/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "=== TESTING REGISTRATION & WELCOME EMAIL FLOW ===\n";

$testEmail = "testreg_" . time() . "@gmail.com";
$testName = "Test Reg User";
$testMobile = "+91" . rand(6000000000, 9999999999);
$testSponsor = "1290";

// Check sponsor exists
$stmtSponsor = $pdo->prepare("SELECT userid, name FROM user WHERE userid = ?");
$stmtSponsor->execute([$testSponsor]);
$sponsorRow = $stmtSponsor->fetch(PDO::FETCH_ASSOC);
assert(!empty($sponsorRow), "Sponsor not found");

$newUserId = rand(100000, 999999);
$otpreg = sprintf("%06d", rand(100000, 999999));
$transaction_password = rand(100000, 999999);
$password = "TestPass123";
$date = date('Y-m-d');
$time = date('h:i a');

$userData = [
    $newUserId, $testName, $testMobile, $testEmail, '', $password, $transaction_password,
    $testSponsor, $sponsorRow['name'], $testSponsor, '0', '1', 'left', '', $date, '', '', '0', 'active',
    '', $time, '', '', '0', 'NA', '', '', $newUserId, '', '0', '', '', '', '', '', $otpreg, ''
];

$inserted = insertUser($pdo, $userData);
echo "[TEST 1] User inserted into database: " . ($inserted ? "SUCCESS" : "FAILED") . "\n";
assert($inserted, "User insertion failed");

// Verify Welcome Email construction & headers
$fromEmailDomain = !empty($home['emailfrom']) ? $home['emailfrom'] : 'no-reply@anantamtptl.com';
if (strpos($fromEmailDomain, '@gmail.com') !== false || strpos($fromEmailDomain, '@yahoo.com') !== false) {
    $fromEmailDomain = 'no-reply@anantamtptl.com';
}

$headers = "From: ANANTA Security <" . strip_tags($fromEmailDomain) . ">\r\n";
$headers .= "Reply-To: " . strip_tags($fromEmailDomain) . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$to = $testEmail;
$subject = "Welcome to ANANTA — Your Account Details & OTP";

$sent = @mail($to, $subject, "Test Welcome Message for ID AN{$newUserId}", $headers);
echo "[TEST 2] Mail function test triggered with sender {$fromEmailDomain}\n";

// Cleanup test user
$pdo->prepare("DELETE FROM user WHERE userid = ?")->execute([$newUserId]);
echo "[TEST 3] Cleaned up test user AN{$newUserId}\n";

echo "=== ALL REGISTRATION & WELCOME EMAIL TESTS COMPLETED ===\n";
