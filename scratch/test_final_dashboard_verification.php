<?php
chdir(__DIR__ . '/../dashboard/user1');
require_once 'common/connection.php';
require_once 'common/db_method.php';

echo "========================================================================\n";
echo "       READ-ONLY VERIFICATION — BUSINESS OVERVIEW VS TEAM PAGES         \n";
echo "========================================================================\n\n";

$allPassed = true;
$users = ['1290', '215567', '224891', '509701', '773330', '843217', '970637'];

foreach ($users as $uid) {
    // 1. left_team.php logic
    $leftMembers = getUserTeamMembersDetailed($uid, 'LEFT', $pdo);
    $left_page_total = count($leftMembers);
    $left_page_active = count(array_filter($leftMembers, function($m) { return ($m['status'] === 'Active'); }));

    // 2. right_team.php logic
    $rightMembers = getUserTeamMembersDetailed($uid, 'RIGHT', $pdo);
    $right_page_total = count($rightMembers);
    $right_page_active = count(array_filter($rightMembers, function($m) { return ($m['status'] === 'Active'); }));

    // 3. index.php logic (simulated with active session)
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['userid'] = $uid;

    ob_start();
    include 'index.php';
    $html = ob_get_clean();

    $index_active_left = null;
    $index_active_right = null;

    if (preg_match('/Active Team \(Left \/ Right\)<\/span>\s*<span[^>]*>(\d+)\s*\/\s*(\d+)<\/span>/i', $html, $m)) {
        $index_active_left = (int)$m[1];
        $index_active_right = (int)$m[2];
    }

    $leftMatch = ($left_page_active === $index_active_left);
    $rightMatch = ($right_page_active === $index_active_right);

    if (!$leftMatch || !$rightMatch) {
        $allPassed = false;
    }

    echo "USER ID: $uid\n";
    echo "  left_team.php  => Total: $left_page_total | Active: $left_page_active\n";
    echo "  right_team.php => Total: $right_page_total | Active: $right_page_active\n";
    echo "  index.php      => Rendered Active Team (Left / Right): {$index_active_left} / {$index_active_right}\n";
    echo "  MATCH VERDICT  => Left: " . ($leftMatch ? "MATCH (PASS)" : "MISMATCH (FAIL)") . " | Right: " . ($rightMatch ? "MATCH (PASS)" : "MISMATCH (FAIL)") . "\n\n";
}

// Test with simulated active users in transaction rollback
echo "------------------------------------------------------------------------\n";
echo "SIMULATING ACTIVE USERS TEST (WITH DATABASE ROLLBACK):\n";
echo "------------------------------------------------------------------------\n";
$pdo->beginTransaction();
try {
    // Set 970637 (in Left of 224891) and 172097 (in Right of 224891) to active = 1
    $pdo->exec("UPDATE user SET active = 1 WHERE userid IN ('970637', '172097')");

    $uid = '224891';
    $_SESSION['userid'] = $uid;

    $leftMembers = getUserTeamMembersDetailed($uid, 'LEFT', $pdo);
    $left_page_active = count(array_filter($leftMembers, function($m) { return ($m['status'] === 'Active'); }));

    $rightMembers = getUserTeamMembersDetailed($uid, 'RIGHT', $pdo);
    $right_page_active = count(array_filter($rightMembers, function($m) { return ($m['status'] === 'Active'); }));

    ob_start();
    include 'index.php';
    $html = ob_get_clean();

    $index_active_left = null;
    $index_active_right = null;
    if (preg_match('/Active Team \(Left \/ Right\)<\/span>\s*<span[^>]*>(\d+)\s*\/\s*(\d+)<\/span>/i', $html, $m)) {
        $index_active_left = (int)$m[1];
        $index_active_right = (int)$m[2];
    }

    $leftMatch = ($left_page_active === $index_active_left);
    $rightMatch = ($right_page_active === $index_active_right);

    if (!$leftMatch || !$rightMatch) {
        $allPassed = false;
    }

    echo "SIMULATED USER ID: 224891 (with 1 active Left + 1 active Right)\n";
    echo "  left_team.php  => Active Left: $left_page_active\n";
    echo "  right_team.php => Active Right: $right_page_active\n";
    echo "  index.php      => Rendered Active Team (Left / Right): {$index_active_left} / {$index_active_right}\n";
    echo "  MATCH VERDICT  => Left: " . ($leftMatch ? "MATCH (PASS)" : "MISMATCH (FAIL)") . " | Right: " . ($rightMatch ? "MATCH (PASS)" : "MISMATCH (FAIL)") . "\n";

} finally {
    $pdo->rollBack();
}

echo "\n========================================================================\n";
echo "FINAL AUDIT & VERIFICATION RESULT: " . ($allPassed ? "100% MATCH & PASS" : "FAIL") . "\n";
echo "========================================================================\n";
