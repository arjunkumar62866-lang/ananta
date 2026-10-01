<?php
chdir(__DIR__ . '/../dashboard/user1');
require_once 'common/connection.php';
require_once 'common/db_method.php';

echo "========================================================================\n";
echo "              ACTIVE TEAM (LEFT / RIGHT) VERIFICATION TESTS             \n";
echo "========================================================================\n\n";

// Test 1: Registered but inactive user (active=0, status=1)
echo "[TEST 1] Registered but inactive user (active=0, status=1)\n";
$stmt = $pdo->query("SELECT userid, active, status FROM user WHERE active = 0 AND status = 1 LIMIT 5");
$inactiveUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Checking sample registered users (active=0, status=1):\n";
foreach ($inactiveUsers as $u) {
    // Check classification in getRootBranchTreeDetailed for root 1290
    $branch = getRootBranchTreeDetailed('1290', $pdo, 'LEFT');
    $classified = 'Not in branch';
    foreach ($branch as $m) {
        if ((string)$m['userid'] === (string)$u['userid']) {
            $classified = $m['status'];
            break;
        }
    }
    echo "  User {$u['userid']}: active={$u['active']}, status={$u['status']} => Classification: $classified\n";
}

// Test 2: Genuine active user (active=1)
echo "\n[TEST 2] Genuine active user (active=1)\n";
$pdo->beginTransaction();
try {
    // Mark user 215567 as active=1 temporarily
    $pdo->exec("UPDATE user SET active = 1 WHERE userid = '215567'");

    $branch1290 = getRootBranchTreeDetailed('215567', $pdo, 'LEFT');
    $user215567Node = null;
    foreach ($branch1290 as $m) {
        if ($m['userid'] === '215567') {
            $user215567Node = $m;
            break;
        }
    }
    echo "  User 215567 with active=1 => Classification: " . ($user215567Node ? $user215567Node['status'] : 'N/A') . "\n";
} finally {
    $pdo->rollBack();
}

// Test 3: User with active investment (package > 0)
echo "\n[TEST 3] User with active investment (package = 100)\n";
$pdo->beginTransaction();
try {
    $pdo->exec("UPDATE user SET package = 100 WHERE userid = '224891'");

    $leftMembers = getRootBranchTreeDetailed('215567', $pdo, 'LEFT');
    $user224891Node = null;
    foreach ($leftMembers as $m) {
        if ($m['userid'] === '224891') {
            $user224891Node = $m;
            break;
        }
    }
    echo "  User 224891 with investment package \$100 => Classification: " . ($user224891Node ? $user224891Node['status'] : 'N/A') . "\n";
} finally {
    $pdo->rollBack();
}

// Test 4: Left / Right separation
echo "\n[TEST 4] Left / Right Branch Separation Test\n";
$pdo->beginTransaction();
try {
    // Activate 970637 (in Left branch of 224891) and 172097 (in Right branch of 224891)
    $pdo->exec("UPDATE user SET active = 1 WHERE userid IN ('970637', '172097')");

    $stmt = $pdo->prepare("SELECT left_id, right_id FROM tree WHERE userid = '224891'");
    $stmt->execute();
    $tRow = $stmt->fetch(PDO::FETCH_ASSOC);

    $leftBranch = getRootBranchTreeDetailed($tRow['left_id'], $pdo, 'LEFT');
    $rightBranch = getRootBranchTreeDetailed($tRow['right_id'], $pdo, 'RIGHT');

    $activeLeftIDs = array_column(array_filter($leftBranch, function($m) { return $m['status'] === 'Active'; }), 'userid');
    $activeRightIDs = array_column(array_filter($rightBranch, function($m) { return $m['status'] === 'Active'; }), 'userid');

    echo "  User 224891 Left Active Count: " . count($activeLeftIDs) . " (IDs: " . implode(', ', $activeLeftIDs) . ")\n";
    echo "  User 224891 Right Active Count: " . count($activeRightIDs) . " (IDs: " . implode(', ', $activeRightIDs) . ")\n";
    echo "  Overlap between Left and Right: " . count(array_intersect($activeLeftIDs, $activeRightIDs)) . "\n";
} finally {
    $pdo->rollBack();
}

// Test 5: Verify index.php Active Team rendering output
echo "\n[TEST 5] index.php Active Team (Left / Right) output check\n";
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['userid'] = '1290';
ob_start();
include 'index.php';
$html = ob_get_clean();

if (preg_match('/Active Team \(Left \/ Right\)<\/span>\s*<span[^>]*>([^<]+)<\/span>/i', $html, $matches)) {
    echo "  Rendered index.php Active Team (Left / Right) string: " . trim($matches[1]) . "\n";
} else {
    echo "  Active Team string not found in HTML output\n";
}

echo "\n========================================================================\n";
echo "                    ALL VERIFICATION TESTS COMPLETED                    \n";
echo "========================================================================\n";
