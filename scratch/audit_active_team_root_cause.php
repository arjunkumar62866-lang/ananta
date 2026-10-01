<?php
chdir(__DIR__ . '/../dashboard/user1');
require_once 'common/connection.php';
require_once 'common/db_method.php';

$usersStmt = $pdo->query("SELECT DISTINCT userid FROM user ORDER BY userid ASC");
$allUsers = $usersStmt->fetchAll(PDO::FETCH_COLUMN);

echo "========================================================================\n";
echo "       COMPLETE READ-ONLY AUDIT: TEAM PAGES VS INDEX.PHP                \n";
echo "========================================================================\n\n";

foreach ($allUsers as $uid) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['userid'] = $uid;

    // 1. left_team.php method: getUserTeamMembersDetailed(uid, 'LEFT')
    $leftMembers_TeamPage = getUserTeamMembersDetailed($uid, 'LEFT', $pdo);
    $activeLeft_TeamPage = array_filter($leftMembers_TeamPage, function($m) { return ($m['status'] === 'Active'); });

    // 2. right_team.php method: getUserTeamMembersDetailed(uid, 'RIGHT')
    $rightMembers_TeamPage = getUserTeamMembersDetailed($uid, 'RIGHT', $pdo);
    $activeRight_TeamPage = array_filter($rightMembers_TeamPage, function($m) { return ($m['status'] === 'Active'); });

    // 3. index.php method: $leftid and $rightid from header.php, then getRootBranchTreeDetailed
    $treedata = function_exists('getUserTreeData') ? getUserTreeData($uid) : null;
    $leftid = $treedata['left'] ?? '';
    $rightid = $treedata['right'] ?? '';
    if (empty($leftid) || empty($rightid)) {
        $stmtChildCheck = $pdo->prepare("SELECT userid, join_side FROM user WHERE underuserid = :uid");
        $stmtChildCheck->execute([':uid' => $uid]);
        while ($cRow = $stmtChildCheck->fetch(PDO::FETCH_ASSOC)) {
            $cSide = strtolower($cRow['join_side'] ?? '');
            if ($cSide === 'left' && empty($leftid)) {
                $leftid = (string)$cRow['userid'];
            } elseif ($cSide === 'right' && empty($rightid)) {
                $rightid = (string)$cRow['userid'];
            }
        }
    }

    $leftMembers_Index = !empty($leftid) ? getRootBranchTreeDetailed($leftid, $pdo, 'LEFT') : [];
    $rightMembers_Index = !empty($rightid) ? getRootBranchTreeDetailed($rightid, $pdo, 'RIGHT') : [];

    $activeLeft_Index = array_filter($leftMembers_Index, function($m) { return ($m['status'] === 'Active'); });
    $activeRight_Index = array_filter($rightMembers_Index, function($m) { return ($m['status'] === 'Active'); });

    // Compare
    $leftDiff = array_diff(array_column($activeLeft_TeamPage, 'userid'), array_column($activeLeft_Index, 'userid'));
    $rightDiff = array_diff(array_column($activeRight_TeamPage, 'userid'), array_column($activeRight_Index, 'userid'));

    if (count($leftMembers_TeamPage) > 0 || count($rightMembers_TeamPage) > 0 || count($leftMembers_Index) > 0 || count($rightMembers_Index) > 0) {
        echo "------------------------------------------------------------------------\n";
        echo "LOGGED-IN USER ID: $uid\n";
        echo "------------------------------------------------------------------------\n";
        echo "  left_team.php  => Total Left: " . count($leftMembers_TeamPage) . " | Active Left: " . count($activeLeft_TeamPage) . " (IDs: " . implode(', ', array_column($activeLeft_TeamPage, 'userid')) . ")\n";
        echo "  index.php      => Total Left: " . count($leftMembers_Index) . " | Active Left: " . count($activeLeft_Index) . " (IDs: " . implode(', ', array_column($activeLeft_Index, 'userid')) . ")\n";
        echo "  Left ID Diff   => " . (count($leftDiff) ? implode(', ', $leftDiff) : 'NONE (EXACT MATCH)') . "\n\n";

        echo "  right_team.php => Total Right: " . count($rightMembers_TeamPage) . " | Active Right: " . count($activeRight_TeamPage) . " (IDs: " . implode(', ', array_column($activeRight_TeamPage, 'userid')) . ")\n";
        echo "  index.php      => Total Right: " . count($rightMembers_Index) . " | Active Right: " . count($activeRight_Index) . " (IDs: " . implode(', ', array_column($activeRight_Index, 'userid')) . ")\n";
        echo "  Right ID Diff  => " . (count($rightDiff) ? implode(', ', $rightDiff) : 'NONE (EXACT MATCH)') . "\n\n";
    }
}

