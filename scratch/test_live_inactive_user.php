<?php
chdir(__DIR__ . '/..');
require_once 'dashboard/admin/common/connection.php';
require_once 'dashboard/user1/common/db_method.php';

echo "=== VERIFYING REAL PRODUCTION INACTIVE USER ===\n";

$uInact = '789260'; // VIP Mr Patel
$st = getUserAccountActivationStatus($uInact, $pdo);
echo "1. User {$uInact} Status: {$st['status']} (is_active=" . ($st['is_active'] ? 'true' : 'false') . ")\n";

$res = processAnantaPackageInvestment($uInact, 'BASIC', 150.00, $pdo);
echo "2. processAnantaPackageInvestment Response: " . json_encode($res) . "\n";

if ($res['status'] === 'error' && strpos($res['message'], 'Please complete your $11 activation before purchasing an investment/package') !== false) {
    echo "✅ SUCCESS: Real inactive user 789260 is blocked with exact required message!\n";
} else {
    echo "❌ FAILURE: Real inactive user was not blocked properly!\n";
}
