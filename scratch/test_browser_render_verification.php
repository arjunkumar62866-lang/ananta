<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
chdir(__DIR__ . '/../dashboard/user1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'common/connection.php';
require_once 'common/db_method.php';

// Helper: Get expected DB direct IDs
function getExpectedDirectIDs($userid, $pdo) {
    $stmt = $pdo->prepare("SELECT userid FROM user WHERE sponserid = :uid ORDER BY userid ASC");
    $stmt->execute(['uid' => $userid]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Helper: Get expected DB placement subtree IDs (using both tree table & user.underuserid)
function getExpectedPlacementIDs($startUserid, $pdo) {
    if (empty($startUserid)) return [];
    
    $result = [];
    $queue = [$startUserid];
    $visited = [];

    while (!empty($queue)) {
        $curr = array_shift($queue);
        if (isset($visited[$curr])) continue;
        $visited[$curr] = true;
        $result[] = (string)$curr;

        // 1. tree table links
        $stmt = $pdo->prepare("SELECT left_id, right_id FROM tree WHERE userid = :uid");
        $stmt->execute(['uid' => $curr]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (!empty($row['left_id'])) $queue[] = (string)$row['left_id'];
            if (!empty($row['right_id'])) $queue[] = (string)$row['right_id'];
        }

        // 2. user table underuserid links
        $stmt2 = $pdo->prepare("SELECT userid FROM user WHERE underuserid = :uid AND userid != :uid");
        $stmt2->execute(['uid' => $curr]);
        $underChildren = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        foreach ($underChildren as $cId) {
            if (!empty($cId)) {
                $queue[] = (string)$cId;
            }
        }
    }
    sort($result);
    return array_values(array_unique($result));
}

// Helper: Get expected left team DB IDs
function getExpectedLeftTeamIDs($userid, $pdo) {
    $leftId = null;
    $stmt = $pdo->prepare("SELECT left_id FROM tree WHERE userid = :uid");
    $stmt->execute(['uid' => $userid]);
    $leftId = $stmt->fetchColumn();

    if (empty($leftId)) {
        $stmt2 = $pdo->prepare("SELECT userid FROM user WHERE underuserid = :uid AND LOWER(join_side) = 'left' AND userid != :uid LIMIT 1");
        $stmt2->execute(['uid' => $userid]);
        $leftId = $stmt2->fetchColumn();
    }

    return getExpectedPlacementIDs($leftId, $pdo);
}

// Helper: Get expected right team DB IDs
function getExpectedRightTeamIDs($userid, $pdo) {
    $rightId = null;
    $stmt = $pdo->prepare("SELECT right_id FROM tree WHERE userid = :uid");
    $stmt->execute(['uid' => $userid]);
    $rightId = $stmt->fetchColumn();

    if (empty($rightId)) {
        $stmt2 = $pdo->prepare("SELECT userid FROM user WHERE underuserid = :uid AND LOWER(join_side) = 'right' AND userid != :uid LIMIT 1");
        $stmt2->execute(['uid' => $userid]);
        $rightId = $stmt2->fetchColumn();
    }

    return getExpectedPlacementIDs($rightId, $pdo);
}

// Helper: Parse rendered HTML for table row user IDs and header stat count
function parseRenderedPageHTML($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $renderedIDs = [];
    $rows = $dom->getElementsByTagName('tr');

    foreach ($rows as $row) {
        $tds = $row->getElementsByTagName('td');
        // Look for rows where 2nd column has userid
        if ($tds->length >= 2) {
            $userTd = $tds->item(1);
            $text = trim($userTd->textContent);
            // Ignore headers or empty "No members found"
            if ($text !== '' && $text !== 'User ID' && stripos($text, 'No ') === false && stripos($text, 'found') === false) {
                $renderedIDs[] = $text;
            }
        }
    }

    // Extract total count badge from header e.g. "Total: 10 Members"
    $badgeCount = null;
    $spans = $dom->getElementsByTagName('span');
    foreach ($spans as $span) {
        $text = trim($span->textContent);
        if (preg_match('/Total:\s*(\d+)\s*Members/i', $text, $m)) {
            $badgeCount = (int)$m[1];
            break;
        }
    }

    // Extract Stat card count (Card 1: Total Directs / Total Left Team / Total Right Team)
    $statCount = null;
    $h3s = $dom->getElementsByTagName('h3');
    if ($h3s->length > 0) {
        $firstH3 = trim($h3s->item(0)->textContent);
        $statCount = (int)str_replace(',', '', $firstH3);
    }

    // Check for DataTables or AJAX pagination scripts
    $hasPagination = (strpos($html, 'pagination') !== false || strpos($html, 'dataTables') !== false);
    $hasAJAX = (strpos($html, '$.ajax') !== false || strpos($html, 'fetch(') !== false || strpos($html, 'XMLHttpRequest') !== false);

    return [
        'rendered_ids' => $renderedIDs,
        'row_count' => count($renderedIDs),
        'badge_count' => $badgeCount,
        'stat_count' => $statCount,
        'has_pagination' => $hasPagination,
        'has_ajax' => $hasAJAX,
    ];
}

// Fetch all users in DB
$usersStmt = $pdo->query("SELECT DISTINCT userid FROM user ORDER BY userid ASC");
$allUsers = $usersStmt->fetchAll(PDO::FETCH_COLUMN);

echo "========================================================================\n";
echo "       BROWSER-SIDE HTML PAGE RENDERING VERIFICATION DIAGNOSTIC         \n";
echo "========================================================================\n\n";

$allPassed = true;

foreach ($allUsers as $uid) {
    $_SESSION['userid'] = $uid;

    // 1. MY DIRECT
    $expectedDirectDB = getExpectedDirectIDs($uid, $pdo);
    $backendDirectMembers = getUserTeamMembersDetailed($uid, 'MY_DIRECT', $pdo);
    $backendDirectIDs = array_column($backendDirectMembers, 'userid');

    ob_start();
    include 'my_direct.php';
    $htmlDirect = ob_get_clean();
    $parsedDirect = parseRenderedPageHTML($htmlDirect);
    $renderedDirectIDs = $parsedDirect['rendered_ids'];

    // Calculations
    $directDuplicates = array_values(array_unique(array_diff_assoc($renderedDirectIDs, array_unique($renderedDirectIDs))));
    $directMissing = array_values(array_diff($expectedDirectDB, $renderedDirectIDs));
    $directExtra = array_values(array_diff($renderedDirectIDs, $expectedDirectDB));

    // Non-sponsors check in My Direct
    $nonSponsorInDirect = [];
    foreach ($renderedDirectIDs as $rId) {
        $stmt = $pdo->prepare("SELECT sponserid FROM user WHERE userid = :uid");
        $stmt->execute(['uid' => $rId]);
        $sp = $stmt->fetchColumn();
        if ((string)$sp !== (string)$uid) {
            $nonSponsorInDirect[] = $rId;
        }
    }

    // 2. LEFT TEAM
    $expectedLeftDB = getExpectedLeftTeamIDs($uid, $pdo);
    $backendLeftMembers = getUserTeamMembersDetailed($uid, 'LEFT', $pdo);
    $backendLeftIDs = array_column($backendLeftMembers, 'userid');

    ob_start();
    include 'left_team.php';
    $htmlLeft = ob_get_clean();
    $parsedLeft = parseRenderedPageHTML($htmlLeft);
    $renderedLeftIDs = $parsedLeft['rendered_ids'];

    $leftDuplicates = array_values(array_unique(array_diff_assoc($renderedLeftIDs, array_unique($renderedLeftIDs))));
    $leftMissing = array_values(array_diff($expectedLeftDB, $renderedLeftIDs));
    $leftExtra = array_values(array_diff($renderedLeftIDs, $expectedLeftDB));

    // 3. RIGHT TEAM
    $expectedRightDB = getExpectedRightTeamIDs($uid, $pdo);
    $backendRightMembers = getUserTeamMembersDetailed($uid, 'RIGHT', $pdo);
    $backendRightIDs = array_column($backendRightMembers, 'userid');

    ob_start();
    include 'right_team.php';
    $htmlRight = ob_get_clean();
    $parsedRight = parseRenderedPageHTML($htmlRight);
    $renderedRightIDs = $parsedRight['rendered_ids'];

    $rightDuplicates = array_values(array_unique(array_diff_assoc($renderedRightIDs, array_unique($renderedRightIDs))));
    $rightMissing = array_values(array_diff($expectedRightDB, $renderedRightIDs));
    $rightExtra = array_values(array_diff($renderedRightIDs, $expectedRightDB));

    // Left/Right Overlap check
    $leftRightOverlap = array_values(array_intersect($renderedLeftIDs, $renderedRightIDs));

    // Check correctness for this user
    $userOk = (
        count($directMissing) === 0 && count($directExtra) === 0 && count($directDuplicates) === 0 && count($nonSponsorInDirect) === 0 &&
        count($leftMissing) === 0 && count($leftExtra) === 0 && count($leftDuplicates) === 0 &&
        count($rightMissing) === 0 && count($rightExtra) === 0 && count($rightDuplicates) === 0 &&
        count($leftRightOverlap) === 0 &&
        $parsedDirect['row_count'] === count($expectedDirectDB) && $parsedDirect['stat_count'] === count($expectedDirectDB) &&
        $parsedLeft['row_count'] === count($expectedLeftDB) && $parsedLeft['stat_count'] === count($expectedLeftDB) &&
        $parsedRight['row_count'] === count($expectedRightDB) && $parsedRight['stat_count'] === count($expectedRightDB)
    );

    if (!$userOk) {
        $allPassed = false;
    }

    // Print summary if user has team or if error
    if (count($expectedDirectDB) > 0 || count($expectedLeftDB) > 0 || count($expectedRightDB) > 0 || !$userOk) {
        echo "------------------------------------------------------------------------\n";
        echo "LOGGED-IN USER ID: $uid [" . ($userOk ? "PASS" : "FAIL") . "]\n";
        echo "------------------------------------------------------------------------\n";

        // MY DIRECT
        echo "MY DIRECT:\n";
        echo "  Backend Expected IDs : " . implode(', ', $expectedDirectDB) . " (Count: " . count($expectedDirectDB) . ")\n";
        echo "  Backend Actual IDs   : " . implode(', ', $backendDirectIDs) . " (Count: " . count($backendDirectIDs) . ")\n";
        echo "  Rendered HTML IDs    : " . implode(', ', $renderedDirectIDs) . " (Count: " . count($renderedDirectIDs) . ")\n";
        echo "  Rendered Duplicates  : " . (count($directDuplicates) ? implode(', ', $directDuplicates) : "0") . "\n";
        echo "  Missing IDs          : " . (count($directMissing) ? implode(', ', $directMissing) : "0") . "\n";
        echo "  Extra IDs            : " . (count($directExtra) ? implode(', ', $directExtra) : "0") . "\n";
        echo "  Non-Sponsor IDs      : " . (count($nonSponsorInDirect) ? implode(', ', $nonSponsorInDirect) : "0") . "\n";
        echo "  Displayed Row Count  : {$parsedDirect['row_count']} | Stat Card: {$parsedDirect['stat_count']} | Header Badge: {$parsedDirect['badge_count']}\n";

        // LEFT TEAM
        echo "\nLEFT TEAM:\n";
        echo "  Backend Expected IDs : " . implode(', ', $expectedLeftDB) . " (Count: " . count($expectedLeftDB) . ")\n";
        echo "  Backend Actual IDs   : " . implode(', ', $backendLeftIDs) . " (Count: " . count($backendLeftIDs) . ")\n";
        echo "  Rendered HTML IDs    : " . implode(', ', $renderedLeftIDs) . " (Count: " . count($renderedLeftIDs) . ")\n";
        echo "  Rendered Duplicates  : " . (count($leftDuplicates) ? implode(', ', $leftDuplicates) : "0") . "\n";
        echo "  Missing IDs          : " . (count($leftMissing) ? implode(', ', $leftMissing) : "0") . "\n";
        echo "  Extra IDs            : " . (count($leftExtra) ? implode(', ', $leftExtra) : "0") . "\n";
        echo "  Displayed Row Count  : {$parsedLeft['row_count']} | Stat Card: {$parsedLeft['stat_count']} | Header Badge: {$parsedLeft['badge_count']}\n";

        // RIGHT TEAM
        echo "\nRIGHT TEAM:\n";
        echo "  Backend Expected IDs : " . implode(', ', $expectedRightDB) . " (Count: " . count($expectedRightDB) . ")\n";
        echo "  Backend Actual IDs   : " . implode(', ', $backendRightIDs) . " (Count: " . count($backendRightIDs) . ")\n";
        echo "  Rendered HTML IDs    : " . implode(', ', $renderedRightIDs) . " (Count: " . count($renderedRightIDs) . ")\n";
        echo "  Rendered Duplicates  : " . (count($rightDuplicates) ? implode(', ', $rightDuplicates) : "0") . "\n";
        echo "  Missing IDs          : " . (count($rightMissing) ? implode(', ', $rightMissing) : "0") . "\n";
        echo "  Extra IDs            : " . (count($rightExtra) ? implode(', ', $rightExtra) : "0") . "\n";
        echo "  Displayed Row Count  : {$parsedRight['row_count']} | Stat Card: {$parsedRight['stat_count']} | Header Badge: {$parsedRight['badge_count']}\n";

        echo "\nLEFT/RIGHT OVERLAP    : " . (count($leftRightOverlap) ? implode(', ', $leftRightOverlap) : "0") . "\n\n";
    }
}

echo "========================================================================\n";
echo "FINAL AUDIT RESULT: " . ($allPassed ? "ALL PAGES & USERS 100% PASSED (PASS)" : "FAILURES DETECTED") . "\n";
echo "========================================================================\n";
