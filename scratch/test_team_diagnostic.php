<?php
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");
include_once("common/db_method.php");

// -------------------------------------------------------------------------
// RECURSIVE TEAM PAGES AUDIT & VERIFICATION REPORT
// Testing my_direct.php, left_team.php, right_team.php via db_method.php
// -------------------------------------------------------------------------

$stmtAllUsers = $pdo->query("SELECT userid FROM user ORDER BY userid");
$allUserIds = $stmtAllUsers->fetchAll(PDO::FETCH_COLUMN);

echo "=========================================================================================================\n";
echo "RECURSIVE TEAM PAGES AUDIT & VERIFICATION REPORT (MY DIRECT, LEFT TEAM, RIGHT TEAM)\n";
echo "=========================================================================================================\n";
echo sprintf("%-15s | %-12s | %-12s | %-12s | %-12s | %-12s | %-8s\n",
    "USER ID", "PAGE DIRECTS", "EXPECTED DIR", "LEFT TEAM", "RIGHT TEAM", "DUPLICATES", "STATUS");
echo str_repeat("-", 95) . "\n";

$testedCount = 0;
$failedCount = 0;

foreach ($allUserIds as $uid) {
    // 1. Directs check
    $stmtDirects = $pdo->prepare("SELECT userid FROM user WHERE sponserid = :uid ORDER BY userid");
    $stmtDirects->execute([':uid' => $uid]);
    $expectedDirectIds = $stmtDirects->fetchAll(PDO::FETCH_COLUMN);

    $myDirectMembers = getUserTeamMembersDetailed($uid, 'MY_DIRECT', $pdo);
    $myDirectIds = array_column($myDirectMembers, 'userid');

    $missingDirects = array_diff($expectedDirectIds, $myDirectIds);
    $extraDirects = array_diff($myDirectIds, $expectedDirectIds);
    $dupDirects = array_keys(array_filter(array_count_values($myDirectIds), function($c) { return $c > 1; }));

    // 2. Left team check
    $leftMembers = getUserTeamMembersDetailed($uid, 'LEFT', $pdo);
    $leftIds = array_column($leftMembers, 'userid');
    $dupLeft = array_keys(array_filter(array_count_values($leftIds), function($c) { return $c > 1; }));

    // 3. Right team check
    $rightMembers = getUserTeamMembersDetailed($uid, 'RIGHT', $pdo);
    $rightIds = array_column($rightMembers, 'userid');
    $dupRight = array_keys(array_filter(array_count_values($rightIds), function($c) { return $c > 1; }));

    // Overlap check (No user should be in both Left and Right team for same root)
    $overlapLeftRight = array_intersect($leftIds, $rightIds);

    $isFail = (count($missingDirects) > 0 || count($extraDirects) > 0 || count($dupDirects) > 0 || count($dupLeft) > 0 || count($dupRight) > 0 || count($overlapLeftRight) > 0);

    if (count($expectedDirectIds) > 0 || count($leftIds) > 0 || count($rightIds) > 0) {
        $testedCount++;
        if ($isFail) $failedCount++;

        $statusStr = $isFail ? "FAIL" : "PASS";

        echo sprintf("%-15s | %-12d | %-12d | %-12d | %-12d | %-12d | %-8s\n",
            $uid,
            count($myDirectIds),
            count($expectedDirectIds),
            count($leftIds),
            count($rightIds),
            count($dupDirects) + count($dupLeft) + count($dupRight),
            $statusStr
        );

        if (count($missingDirects) > 0) echo "  --> MISSING DIRECTS: " . implode(", ", $missingDirects) . "\n";
        if (count($extraDirects) > 0) echo "  --> EXTRA DIRECTS: " . implode(", ", $extraDirects) . "\n";
        if (count($dupLeft) > 0) echo "  --> DUP LEFT TEAM: " . implode(", ", $dupLeft) . "\n";
        if (count($dupRight) > 0) echo "  --> DUP RIGHT TEAM: " . implode(", ", $dupRight) . "\n";
        if (count($overlapLeftRight) > 0) echo "  --> LEFT/RIGHT OVERLAP: " . implode(", ", $overlapLeftRight) . "\n";
    }
}

echo "=========================================================================================================\n";
echo sprintf("TOTAL TESTED USERS: %d | PASSED: %d | FAILED: %d\n", $testedCount, $testedCount - $failedCount, $failedCount);
echo "=========================================================================================================\n";
