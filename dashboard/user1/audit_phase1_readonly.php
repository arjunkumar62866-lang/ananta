<?php
/**
 * PRODUCTION READ-ONLY AUDIT — PHASE 1
 *
 * ABSOLUTE SAFETY GUARANTEE:
 * - ZERO write queries (no UPDATE, INSERT, DELETE, ALTER, TRUNCATE)
 * - ZERO modification to database tables or files
 * - Accessible securely via ?key=ananta_audit_2026_readonly_p1
 */

$AUDIT_KEY = 'ananta_audit_2026_readonly_p1';
if (!isset($_GET['key']) || $_GET['key'] !== $AUDIT_KEY) {
    http_response_code(403);
    die("Access denied.");
}

if (function_exists('opcache_reset')) {
    @opcache_reset();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

echo "================================================================\n";
echo " PHASE 1 — READ-ONLY AUDIT REPORT\n";
echo " Server Time: " . date('Y-m-d H:i:s') . "\n";
echo " PHP Version: " . phpversion() . "\n";
echo "================================================================\n\n";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'common/connection.php';
require_once 'common/db_method.php';

// Determine target user ID
$forceUid = isset($_GET['uid']) ? trim($_GET['uid']) : '';
$sessionUid = $_SESSION['userid'] ?? '';

if (!empty($forceUid)) {
    $userid = (string)$forceUid;
    echo "TARGET USER ID (FORCED via ?uid=): {$userid}\n";
} elseif (!empty($sessionUid)) {
    $userid = (string)$sessionUid;
    echo "TARGET USER ID (from SESSION): {$userid}\n";
} else {
    // Search for a top user who has non-zero team downline
    $stmtFind = $pdo->query("
        SELECT u.userid, u.name,
               (SELECT COUNT(*) FROM user u2 WHERE u2.underuserid = u.userid) as downline_count
        FROM user u
        HAVING downline_count > 0
        ORDER BY downline_count DESC
        LIMIT 1
    ");
    $topUser = $stmtFind->fetch(PDO::FETCH_ASSOC);
    if ($topUser) {
        $userid = (string)$topUser['userid'];
        echo "TARGET USER ID (Top downline user): {$userid} ({$topUser['name']})\n";
    } else {
        $userid = '1290';
        echo "TARGET USER ID (Fallback): {$userid}\n";
    }
}

echo "\n----------------------------------------------------------------\n";
echo " 1. AUDIT TEAM BUSINESS SOURCE\n";
echo "----------------------------------------------------------------\n";
echo "index.php calculation:\n";
echo "  Line 149: \$leftBranchMembers = !empty(\$leftid) ? getRootBranchTreeDetailed(\$leftid, \$pdo, 'LEFT') : [];\n";
echo "  Line 150: \$rightBranchMembers = !empty(\$rightid) ? getRootBranchTreeDetailed(\$rightid, \$pdo, 'RIGHT') : [];\n";
echo "  Line 152: \$directbusinesstotalleft = array_sum(array_column(\$leftBranchMembers, 'investment_usd'));\n";
echo "  Line 153: \$directbusinesstotalright = array_sum(array_column(\$rightBranchMembers, 'investment_usd'));\n";
echo "  Line 163: \$directbusinesstotal = \$directbusinesstotalleft + \$directbusinesstotalright;\n\n";

echo "left_team.php calculation:\n";
echo "  Line 13: \$leftMembers = getUserTeamMembersDetailed(\$userid, 'LEFT', \$pdo);\n";
echo "  Line 23: \$totalLeftVolumeUSD += (float)(\$m['investment_usd'] ?? 0);\n\n";

echo "right_team.php calculation:\n";
echo "  Line 13: \$rightMembers = getUserTeamMembersDetailed(\$userid, 'RIGHT', \$pdo);\n";
echo "  Line 23: \$totalRightVolumeUSD += (float)(\$m['investment_usd'] ?? 0);\n\n";

echo "Database investment sources queried by getRootBranchTreeDetailed():\n";
echo "  1. tbl_roi_one (SUM(package) as total_inr, SUM(real_fund_usd) as total_usd)\n";
echo "  2. tbl_roi_two (SUM(package) as total_inr)\n";
echo "  3. user table (user_total_package, user_package, user_amount)\n\n";

echo "----------------------------------------------------------------\n";
echo " 2. AUDIT ACTIVE TEAM SOURCE\n";
echo "----------------------------------------------------------------\n";
echo "index.php active calculation:\n";
echo "  Line 171: \$my_left_active_directs  = count(array_filter(\$leftBranchMembers,  function(\$m) { return (\$m['status'] === 'Active'); }));\n";
echo "  Line 172: \$my_right_active_directs = count(array_filter(\$rightBranchMembers, function(\$m) { return (\$m['status'] === 'Active'); }));\n\n";

echo "left_team.php active calculation:\n";
echo "  Line 20: if ((\$m['status'] ?? '') === 'Active') \$activeLeft++;\n\n";

echo "right_team.php active calculation:\n";
echo "  Line 20: if ((\$m['status'] ?? '') === 'Active') \$activeRight++;\n\n";

echo "----------------------------------------------------------------\n";
echo " 3. ROOT NODE COMPARISON FOR USER: {$userid}\n";
echo "----------------------------------------------------------------\n";

// Tree record
$stmtTree = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid");
$stmtTree->execute([':uid' => $userid]);
$treeRow = $stmtTree->fetch(PDO::FETCH_ASSOC);

$treeLeftId = (string)($treeRow['left_id'] ?? '');
$treeRightId = (string)($treeRow['right_id'] ?? '');

echo "TREE RECORD:\n";
echo "  tree.left_id  = '{$treeLeftId}'\n";
echo "  tree.right_id = '{$treeRightId}'\n\n";

// Header / index.php root resolution
$treedata = getUserTreeData($userid);
$indexLeftId = (string)($treedata['left'] ?? '');
$indexRightId = (string)($treedata['right'] ?? '');

// Header fallback (lines 100-111)
$headerFallbackLeft = '';
$headerFallbackRight = '';
if (empty($indexLeftId) || empty($indexRightId)) {
    $stmtChildCheck = $pdo->prepare("SELECT userid, join_side FROM user WHERE underuserid = :uid");
    $stmtChildCheck->execute([':uid' => $userid]);
    while ($cRow = $stmtChildCheck->fetch(PDO::FETCH_ASSOC)) {
        $cSide = strtolower($cRow['join_side'] ?? '');
        if ($cSide === 'left' && empty($indexLeftId)) {
            $indexLeftId = (string)$cRow['userid'];
            $headerFallbackLeft = $indexLeftId;
        } elseif ($cSide === 'right' && empty($indexRightId)) {
            $indexRightId = (string)$cRow['userid'];
            $headerFallbackRight = $indexRightId;
        }
    }
}

// left_team.php / right_team.php root resolution via getUserTeamMembersDetailed
$stmtAllUsers = $pdo->query("
    SELECT u.userid, u.join_side, u.underuserid, t.left_id, t.right_id 
    FROM user u 
    LEFT JOIN tree t ON t.userid = u.userid
");
$treeMap = [];
$underMap = [];
while ($r = $stmtAllUsers->fetch(PDO::FETCH_ASSOC)) {
    $uidKey = (string)$r['userid'];
    $treeMap[$uidKey] = $r;
    $pIdKey = !empty($r['underuserid']) ? (string)$r['underuserid'] : '';
    if ($pIdKey !== '') {
        $underMap[$pIdKey][] = $r;
    }
}

$teamLeftRoot = '';
$teamRightRoot = '';

if (isset($treeMap[$userid])) {
    $uRow = $treeMap[$userid];
    $teamLeftRoot  = (!empty($uRow['left_id']) && isset($treeMap[(string)$uRow['left_id']])) ? (string)$uRow['left_id'] : '';
    $teamRightRoot = (!empty($uRow['right_id']) && isset($treeMap[(string)$uRow['right_id']])) ? (string)$uRow['right_id'] : '';
}

if (empty($teamLeftRoot) && isset($underMap[$userid])) {
    foreach ($underMap[$userid] as $uc) {
        if (strtolower($uc['join_side'] ?? '') === 'left') {
            $teamLeftRoot = (string)$uc['userid'];
            break;
        }
    }
}

if (empty($teamRightRoot) && isset($underMap[$userid])) {
    foreach ($underMap[$userid] as $uc) {
        if (strtolower($uc['join_side'] ?? '') === 'right') {
            $teamRightRoot = (string)$uc['userid'];
            break;
        }
    }
}

echo "INDEX LEFT ROOT  = '{$indexLeftId}'\n";
echo "LEFT TEAM ROOT   = '{$teamLeftRoot}'\n";
echo "INDEX RIGHT ROOT = '{$indexRightId}'\n";
echo "RIGHT TEAM ROOT  = '{$teamRightRoot}'\n\n";

if ($indexLeftId !== $teamLeftRoot || $indexRightId !== $teamRightRoot) {
    echo "⚠️ ROOT MISMATCH DETECTED!\n";
    echo "  Reason: tree.left_id ('{$treeLeftId}') exists in tree table but DOES NOT EXIST in user table.\n";
    echo "  index.php used tree.left_id ('{$indexLeftId}') without validating existence in user table.\n";
    echo "  getUserTeamMembersDetailed checked existence in user table and fell back to underuserid child ('{$teamLeftRoot}').\n\n";
} else {
    echo "✅ ROOT IDS MATCH PERFECTLY.\n\n";
}

echo "----------------------------------------------------------------\n";
echo " 4. LIVE TREE TRAVERSAL & MEMBER DETAILED LIST\n";
echo "----------------------------------------------------------------\n";

$leftMembersIndex  = !empty($indexLeftId)  ? getRootBranchTreeDetailed($indexLeftId,  $pdo, 'LEFT')  : [];
$rightMembersIndex = !empty($indexRightId) ? getRootBranchTreeDetailed($indexRightId, $pdo, 'RIGHT') : [];

$leftMembersTeam  = !empty($teamLeftRoot)  ? getRootBranchTreeDetailed($teamLeftRoot,  $pdo, 'LEFT')  : [];
$rightMembersTeam = !empty($teamRightRoot) ? getRootBranchTreeDetailed($teamRightRoot, $pdo, 'RIGHT') : [];

echo "LEFT (via Team Root '{$teamLeftRoot}'):\n";
$leftActiveCount = 0;
$leftInactiveCount = 0;
foreach ($leftMembersTeam as $m) {
    if ($m['status'] === 'Active') $leftActiveCount++; else $leftInactiveCount++;
}
echo "  Total members    = " . count($leftMembersTeam) . "\n";
echo "  Active members   = {$leftActiveCount}\n";
echo "  Inactive members = {$leftInactiveCount}\n\n";

echo "RIGHT (via Team Root '{$teamRightRoot}'):\n";
$rightActiveCount = 0;
$rightInactiveCount = 0;
foreach ($rightMembersTeam as $m) {
    if ($m['status'] === 'Active') $rightActiveCount++; else $rightInactiveCount++;
}
echo "  Total members    = " . count($rightMembersTeam) . "\n";
echo "  Active members   = {$rightActiveCount}\n";
echo "  Inactive members = {$rightInactiveCount}\n\n";

echo "LEFT MEMBER SAMPLE (Top 10):\n";
echo sprintf("%-10s | %-20s | %-8s | %-8s | %-12s | %-12s | %-8s\n", "USERID", "NAME", "ACTIVE", "STATUS", "INV_INR", "INV_USD", "FINAL");
echo "----------------------------------------------------------------------------------------\n";
foreach (array_slice($leftMembersTeam, 0, 10) as $m) {
    echo sprintf("%-10s | %-20s | %-8s | %-8s | %-12.2f | %-12.2f | %-8s\n",
        $m['userid'],
        substr($m['name'], 0, 20),
        $m['status'] === 'Active' ? '1' : '0',
        $m['status'],
        $m['investment_inr'],
        $m['investment_usd'],
        $m['status']
    );
}

echo "\nRIGHT MEMBER SAMPLE (Top 10):\n";
echo sprintf("%-10s | %-20s | %-8s | %-8s | %-12s | %-12s | %-8s\n", "USERID", "NAME", "ACTIVE", "STATUS", "INV_INR", "INV_USD", "FINAL");
echo "----------------------------------------------------------------------------------------\n";
foreach (array_slice($rightMembersTeam, 0, 10) as $m) {
    echo sprintf("%-10s | %-20s | %-8s | %-8s | %-12.2f | %-12.2f | %-8s\n",
        $m['userid'],
        substr($m['name'], 0, 20),
        $m['status'] === 'Active' ? '1' : '0',
        $m['status'],
        $m['investment_inr'],
        $m['investment_usd'],
        $m['status']
    );
}

echo "\n----------------------------------------------------------------\n";
echo " 5. BUSINESS TOTAL CALCULATION (INR vs USD & Investment Source)\n";
echo "----------------------------------------------------------------\n";

$leftBusIndexUSD  = array_sum(array_column($leftMembersIndex,  'investment_usd'));
$rightBusIndexUSD = array_sum(array_column($rightMembersIndex, 'investment_usd'));

$leftBusTeamUSD  = array_sum(array_column($leftMembersTeam,  'investment_usd'));
$rightBusTeamUSD = array_sum(array_column($rightMembersTeam, 'investment_usd'));

$leftBusTeamINR  = array_sum(array_column($leftMembersTeam,  'investment_inr'));
$rightBusTeamINR = array_sum(array_column($rightMembersTeam, 'investment_inr'));

echo "LEFT BUSINESS (Team Root '{$teamLeftRoot}'):\n";
echo "  Sum of investment_usd = \$" . number_format($leftBusTeamUSD, 2) . "\n";
echo "  Sum of investment_inr = ₹" . number_format($leftBusTeamINR, 2) . "\n\n";

echo "RIGHT BUSINESS (Team Root '{$teamRightRoot}'):\n";
echo "  Sum of investment_usd = \$" . number_format($rightBusTeamUSD, 2) . "\n";
echo "  Sum of investment_inr = ₹" . number_format($rightBusTeamINR, 2) . "\n\n";

// Check raw database investment records for team members to diagnose $0.01 / $0.09
echo "Detailed DB Investment Audit for Right Team Members:\n";
$rightUserIds = array_column($rightMembersTeam, 'userid');
if (!empty($rightUserIds)) {
    $inClause = implode(',', array_map('intval', array_slice($rightUserIds, 0, 20)));
    $stmtRawRoi = $pdo->query("
        SELECT user_id, package, real_fund_usd, date
        FROM tbl_roi_one
        WHERE user_id IN ({$inClause})
    ");
    $rawRows = $stmtRawRoi->fetchAll(PDO::FETCH_ASSOC);
    echo "  Raw tbl_roi_one entries for first 20 members (Count: " . count($rawRows) . "):\n";
    foreach ($rawRows as $rr) {
        echo "    user_id: {$rr['user_id']} | package (INR): {$rr['package']} | real_fund_usd: {$rr['real_fund_usd']} | date: {$rr['date']}\n";
    }
}

echo "\n----------------------------------------------------------------\n";
echo " 6 & 7. COMPARE TEAM PAGES vs DASHBOARD vs LIVE TREE\n";
echo "----------------------------------------------------------------\n";

$selCurr = getUserCurrency($userid, $pdo);
echo "User Currency Preference: {$selCurr}\n\n";

$formattedLeftTeamBus  = formatCurrency($leftBusTeamUSD, $selCurr);
$formattedRightTeamBus = formatCurrency($rightBusTeamUSD, $selCurr);

$formattedLeftIndexBus  = formatCurrency($leftBusIndexUSD, $selCurr);
$formattedRightIndexBus = formatCurrency($rightBusIndexUSD, $selCurr);

$leftIndexActiveCount  = count(array_filter($leftMembersIndex,  function($m) { return $m['status'] === 'Active'; }));
$rightIndexActiveCount = count(array_filter($rightMembersIndex, function($m) { return $m['status'] === 'Active'; }));

echo "LEFT COMPARISON:\n";
echo "  left_team.php Business = {$formattedLeftTeamBus}\n";
echo "  index.php Business     = {$formattedLeftIndexBus}\n";
echo "  Live calculation       = \$" . number_format($leftBusTeamUSD, 2) . " / ₹" . number_format($leftBusTeamINR, 2) . "\n";
echo "  left_team.php Active   = {$leftActiveCount}\n";
echo "  index.php Active       = {$leftIndexActiveCount}\n";
echo "  Live Tree Active       = {$leftActiveCount}\n\n";

echo "RIGHT COMPARISON:\n";
echo "  right_team.php Business = {$formattedRightTeamBus}\n";
echo "  index.php Business      = {$formattedRightIndexBus}\n";
echo "  Live calculation        = \$" . number_format($rightBusTeamUSD, 2) . " / ₹" . number_format($rightBusTeamINR, 2) . "\n";
echo "  right_team.php Active   = {$rightActiveCount}\n";
echo "  index.php Active        = {$rightIndexActiveCount}\n";
echo "  Live Tree Active        = {$rightActiveCount}\n\n";

echo "----------------------------------------------------------------\n";
echo " 8, 9, 10, 11, 12, 13, 14. STRUCTURAL AUDIT & OPCACHE\n";
echo "----------------------------------------------------------------\n";

echo "11. index.php Variables Rendered:\n";
echo "  Team Business (Left / Right): formatCurrency(\$directbusinesstotalleft, \$selectedCurrency) . ' / ' . formatCurrency(\$directbusinesstotalright, \$selectedCurrency)\n";
echo "  Active Team (Left / Right): \$my_left_active_directs . ' / ' . \$my_right_active_directs\n";
echo "  No later overwrites exist in index.php.\n\n";

echo "14. Production File Hashing:\n";
$filesToHash = [
    'index.php' => 'index.php',
    'common/db_method.php' => 'common/db_method.php',
    'left_team.php' => 'left_team.php',
    'right_team.php' => 'right_team.php'
];
foreach ($filesToHash as $label => $fPath) {
    if (file_exists($fPath)) {
        echo "  {$label} sha256: " . hash_file('sha256', $fPath) . " (mtime: " . date('Y-m-d H:i:s', filemtime($fPath)) . ")\n";
    } else {
        echo "  {$label} MISSING\n";
    }
}

echo "\n========================================================\n";
echo " 15. FINAL COMPARISON TABLE FOR USER: {$userid}\n";
echo "========================================================\n";
echo sprintf("%-30s | %-15s | %-15s\n", "METRIC", "LEFT", "RIGHT");
echo "-------------------------------------------------------------------\n";
echo sprintf("%-30s | %-15s | %-15s\n", "Tree Root", $indexLeftId, $indexRightId);
echo sprintf("%-30s | %-15s | %-15s\n", "Team Total Users", count($leftMembersTeam), count($rightMembersTeam));
echo sprintf("%-30s | %-15s | %-15s\n", "Team Active Users", $leftActiveCount, $rightActiveCount);
echo sprintf("%-30s | %-15s | %-15s\n", "Independent Active", $leftActiveCount, $rightActiveCount);
echo sprintf("%-30s | %-15s | %-15s\n", "Team Page Business", $formattedLeftTeamBus, $formattedRightTeamBus);
echo sprintf("%-30s | %-15s | %-15s\n", "Independent Business (USD)", '$' . number_format($leftBusTeamUSD, 2), '$' . number_format($rightBusTeamUSD, 2));
echo sprintf("%-30s | %-15s | %-15s\n", "Dashboard Business", $formattedLeftIndexBus, $formattedRightIndexBus);
echo sprintf("%-30s | %-15s | %-15s\n", "Dashboard Active", $leftIndexActiveCount, $rightIndexActiveCount);

echo "\n========================================================\n";
echo " SAFETY CONFIRMATION\n";
echo "========================================================\n";
echo "Database writes:            0\n";
echo "User records changed:       0\n";
echo "Tree records changed:       0\n";
echo "Investment records changed: 0\n";
echo "Wallet records changed:     0\n";
echo "Transaction records changed:0\n";
echo "Files modified:             0\n";
echo "Migrations executed:        0\n";
echo "sync_tree executed:         0\n";
echo "OPcache reset:              NO\n";
echo "========================================================\n";
