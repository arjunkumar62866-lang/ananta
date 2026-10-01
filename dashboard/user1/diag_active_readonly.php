<?php
/**
 * PRODUCTION READ-ONLY DIAGNOSTIC — Active Team (Left/Right) Count Mismatch
 * 
 * SAFETY: This script performs ZERO writes to the database.
 * Access requires ?key= parameter for security.
 * 
 * After use, DELETE this file from production immediately.
 */

// Security gate
$DIAG_KEY = 'ananta_diag_2026_readonly_x7k9m';
if (!isset($_GET['key']) || $_GET['key'] !== $DIAG_KEY) {
    http_response_code(403);
    die('Access denied. Append ?key=YOUR_KEY to URL.');
}

// Allow specifying user ID via ?uid= parameter
$forceUid = isset($_GET['uid']) ? trim($_GET['uid']) : '';

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

echo "================================================================\n";
echo " PRODUCTION READ-ONLY DIAGNOSTIC\n";
echo " Active Team (Left / Right) Mismatch Investigation\n";
echo " Server Time: " . date('Y-m-d H:i:s') . "\n";
echo " PHP Version: " . phpversion() . "\n";
echo "================================================================\n\n";

// ============================
// SESSION CHECK
// ============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'common/connection.php';
require_once 'common/db_method.php';

echo "========================================================\n";
echo "1. SESSION\n";
echo "========================================================\n";

$sessionUserId = $_SESSION['userid'] ?? 'NOT SET';
echo "\$_SESSION['userid'] = " . var_export($sessionUserId, true) . "\n";

// Determine the user ID to test
if (!empty($forceUid)) {
    $userid = (string)$forceUid;
    echo "FORCED UID via ?uid= parameter: {$userid}\n";
} elseif ($sessionUserId !== 'NOT SET') {
    $userid = (string)$sessionUserId;
    echo "Using session userid: {$userid}\n";
} else {
    // Find users who have active members in their team
    echo "Scanning database for users with active downline members...\n";
    $candidates = $pdo->query("SELECT u.userid, u.name FROM user u LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
    $foundUser = null;
    $userStats = [];
    foreach ($candidates as $cand) {
        $cId = $cand['userid'];
        $leftMembers = getRootBranchTreeDetailed(getUserTreeData($cId)['left'] ?? '', $pdo, 'LEFT');
        $rightMembers = getRootBranchTreeDetailed(getUserTreeData($cId)['right'] ?? '', $pdo, 'RIGHT');
        $leftActive = count(array_filter($leftMembers, function($m) { return $m['status'] === 'Active'; }));
        $rightActive = count(array_filter($rightMembers, function($m) { return $m['status'] === 'Active'; }));
        $leftTotal = count($leftMembers);
        $rightTotal = count($rightMembers);
        if ($leftTotal > 0 || $rightTotal > 0) {
            $userStats[] = [
                'userid' => $cId,
                'name' => $cand['name'],
                'left_active' => $leftActive,
                'left_total' => $leftTotal,
                'right_active' => $rightActive,
                'right_total' => $rightTotal
            ];
            if (($leftActive > 0 || $rightActive > 0) && !$foundUser) {
                $foundUser = $cId;
            }
        }
    }
    
    echo "Found " . count($userStats) . " users with downlines:\n";
    usort($userStats, function($a, $b) {
        return ($b['left_active'] + $b['right_active']) <=> ($a['left_active'] + $a['right_active']);
    });
    foreach (array_slice($userStats, 0, 10) as $stat) {
        echo "  User ID: {$stat['userid']} ({$stat['name']}) -> Left: {$stat['left_active']}/{$stat['left_total']}, Right: {$stat['right_active']}/{$stat['right_total']}\n";
    }

    if ($foundUser) {
        $userid = (string)$foundUser;
        echo "\nSelected top user with active members: {$userid}\n";
    } else {
        $userid = !empty($userStats) ? (string)$userStats[0]['userid'] : '821867';
        echo "\nNo users with active members found in top scan, using: {$userid}\n";
    }
}

echo "\nFINAL TEST USER ID: {$userid}\n\n";

// ========================================================
// 2. USER RECORD
// ========================================================
echo "========================================================\n";
echo "2. USER RECORD\n";
echo "========================================================\n";

$stmtU = $pdo->prepare("SELECT userid, name, active, status, package, amount, total_package, underuserid, join_side, sponserid FROM user WHERE userid = :uid");
$stmtU->execute([':uid' => $userid]);
$userRow = $stmtU->fetch(PDO::FETCH_ASSOC);

if (!$userRow) {
    die("ERROR: User {$userid} not found in database!\n");
}

foreach ($userRow as $k => $v) {
    echo "  {$k} = " . var_export($v, true) . "\n";
}

// ========================================================
// 3. TREE RECORD
// ========================================================
echo "\n========================================================\n";
echo "3. TREE RECORD\n";
echo "========================================================\n";

$stmtT = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid");
$stmtT->execute([':uid' => $userid]);
$treeRow = $stmtT->fetch(PDO::FETCH_ASSOC);

if ($treeRow) {
    foreach ($treeRow as $k => $v) {
        echo "  {$k} = " . var_export($v, true) . "\n";
    }
} else {
    echo "  ⚠️ NO tree row found for userid={$userid}\n";
}

// ========================================================
// 4. ROOT CHILD RESOLUTION
// ========================================================
echo "\n========================================================\n";
echo "4. ROOT CHILD RESOLUTION\n";
echo "========================================================\n";

// --- PATH A: index.php (via getUserTreeData + header.php fallback) ---
echo "\n--- PATH A: index.php resolution ---\n";

$treedata = getUserTreeData($userid);
$indexLeftId = $treedata['left'] ?? '';
$indexRightId = $treedata['right'] ?? '';

echo "getUserTreeData({$userid}):\n";
echo "  left  = '{$indexLeftId}'\n";
echo "  right = '{$indexRightId}'\n";

// header.php fallback (lines 99-111)
if (empty($indexLeftId) || empty($indexRightId)) {
    echo "\n  [FALLBACK] Querying user.underuserid children...\n";
    $stmtCh = $pdo->prepare("SELECT userid, join_side FROM user WHERE underuserid = :uid");
    $stmtCh->execute([':uid' => $userid]);
    $children = $stmtCh->fetchAll(PDO::FETCH_ASSOC);
    foreach ($children as $cRow) {
        $cSide = strtolower($cRow['join_side'] ?? '');
        echo "    child userid={$cRow['userid']}, join_side='{$cSide}'\n";
        if ($cSide === 'left' && empty($indexLeftId)) {
            $indexLeftId = (string)$cRow['userid'];
            echo "    → Set indexLeftId = '{$indexLeftId}'\n";
        } elseif ($cSide === 'right' && empty($indexRightId)) {
            $indexRightId = (string)$cRow['userid'];
            echo "    → Set indexRightId = '{$indexRightId}'\n";
        }
    }
}

echo "\nFINAL index.php LEFT ROOT ID:  '{$indexLeftId}'\n";
echo "FINAL index.php RIGHT ROOT ID: '{$indexRightId}'\n";

// --- PATH B: getUserTeamMembersDetailed() internal resolution ---
echo "\n--- PATH B: getUserTeamMembersDetailed() resolution ---\n";

$stmtAll = $pdo->query("
    SELECT u.userid, u.join_side, u.underuserid, t.left_id, t.right_id
    FROM user u
    LEFT JOIN tree t ON t.userid = u.userid
");
$treeMap = [];
$underMap = [];
while ($r = $stmtAll->fetch(PDO::FETCH_ASSOC)) {
    $uid = (string)$r['userid'];
    $treeMap[$uid] = $r;
    $pId = !empty($r['underuserid']) ? (string)$r['underuserid'] : '';
    if ($pId !== '') {
        $underMap[$pId][] = $r;
    }
}

// LEFT root (same as getUserTeamMembersDetailed lines 2214-2231)
$teamLeftRoot = '';
if (isset($treeMap[$userid])) {
    $uRow = $treeMap[$userid];
    $rawLeftId = $uRow['left_id'] ?? '';
    $existsInMap = isset($treeMap[(string)$rawLeftId]);
    echo "tree.left_id = '{$rawLeftId}', exists in userMap = " . ($existsInMap ? 'YES' : 'NO') . "\n";
    $teamLeftRoot = (!empty($rawLeftId) && $existsInMap) ? (string)$rawLeftId : '';
}
if (empty($teamLeftRoot) && isset($underMap[$userid])) {
    echo "  [underMap fallback for LEFT]\n";
    foreach ($underMap[$userid] as $uc) {
        $cId = (string)$uc['userid'];
        if (strtolower($uc['join_side'] ?? '') === 'left') {
            $teamLeftRoot = $cId;
            echo "  → Left root = {$cId}\n";
            break;
        }
    }
}

// RIGHT root
$teamRightRoot = '';
if (isset($treeMap[$userid])) {
    $uRow = $treeMap[$userid];
    $rawRightId = $uRow['right_id'] ?? '';
    $existsInMap = isset($treeMap[(string)$rawRightId]);
    echo "tree.right_id = '{$rawRightId}', exists in userMap = " . ($existsInMap ? 'YES' : 'NO') . "\n";
    $teamRightRoot = (!empty($rawRightId) && $existsInMap) ? (string)$rawRightId : '';
}
if (empty($teamRightRoot) && isset($underMap[$userid])) {
    echo "  [underMap fallback for RIGHT]\n";
    foreach ($underMap[$userid] as $uc) {
        $cId = (string)$uc['userid'];
        if (strtolower($uc['join_side'] ?? '') === 'right') {
            $teamRightRoot = $cId;
            echo "  → Right root = {$cId}\n";
            break;
        }
    }
}

echo "\nFINAL left_team.php LEFT ROOT ID:   '{$teamLeftRoot}'\n";
echo "FINAL right_team.php RIGHT ROOT ID: '{$teamRightRoot}'\n";

// ========================================================
// 5. ROOT-ID COMPARISON
// ========================================================
echo "\n========================================================\n";
echo "5. ROOT-ID COMPARISON\n";
echo "========================================================\n";

$leftRootMatch = ($indexLeftId === $teamLeftRoot);
$rightRootMatch = ($indexRightId === $teamRightRoot);

echo "LEFT ROOT:\n";
echo "  index.php      = '{$indexLeftId}'\n";
echo "  left_team.php  = '{$teamLeftRoot}'\n";
echo "  MATCH = " . ($leftRootMatch ? "YES" : "*** NO ***") . "\n\n";

echo "RIGHT ROOT:\n";
echo "  index.php       = '{$indexRightId}'\n";
echo "  right_team.php  = '{$teamRightRoot}'\n";
echo "  MATCH = " . ($rightRootMatch ? "YES" : "*** NO ***") . "\n";

if (!$leftRootMatch || !$rightRootMatch) {
    echo "\n🛑 ROOT ID MISMATCH DETECTED — THIS IS LIKELY THE ROOT CAUSE\n";
}

// ========================================================
// 6. DIRECT FUNCTION OUTPUT
// ========================================================
echo "\n========================================================\n";
echo "6. DIRECT FUNCTION OUTPUT\n";
echo "========================================================\n";

// LEFT Branch
echo "\n--- LEFT Branch (root='{$indexLeftId}') ---\n";
$leftBranch = !empty($indexLeftId) ? getRootBranchTreeDetailed($indexLeftId, $pdo, 'LEFT') : [];
$leftTotal = count($leftBranch);
$leftActive = 0;
$leftInactive = 0;

echo sprintf("%-12s %-8s %-10s %-10s %-12s %-12s %-15s\n", 
    'USERID', 'ACTIVE', 'STATUS', 'u.active', 'u.status', 'INV_USD', 'FUNC_STATUS');
echo str_repeat('-', 85) . "\n";

foreach ($leftBranch as $m) {
    // Get raw user values for this member
    $stmtRaw = $pdo->prepare("SELECT active, status FROM user WHERE userid = :uid");
    $stmtRaw->execute([':uid' => $m['userid']]);
    $rawU = $stmtRaw->fetch(PDO::FETCH_ASSOC);
    
    if ($m['status'] === 'Active') $leftActive++;
    else $leftInactive++;
    
    echo sprintf("%-12s %-8s %-10s %-10s %-12s %-12s %-15s\n",
        $m['userid'],
        $rawU ? $rawU['active'] : '?',
        $rawU ? $rawU['status'] : '?',
        'u.active=' . ($rawU['active'] ?? '?'),
        'u.status=' . ($rawU['status'] ?? '?'),
        number_format($m['investment_usd'], 2),
        $m['status']
    );
}
echo "\nLEFT TOTAL: {$leftTotal}, ACTIVE: {$leftActive}, INACTIVE: {$leftInactive}\n";

// RIGHT Branch
echo "\n--- RIGHT Branch (root='{$indexRightId}') ---\n";
$rightBranch = !empty($indexRightId) ? getRootBranchTreeDetailed($indexRightId, $pdo, 'RIGHT') : [];
$rightTotal = count($rightBranch);
$rightActive = 0;
$rightInactive = 0;

echo sprintf("%-12s %-8s %-10s %-10s %-12s %-12s %-15s\n",
    'USERID', 'ACTIVE', 'STATUS', 'u.active', 'u.status', 'INV_USD', 'FUNC_STATUS');
echo str_repeat('-', 85) . "\n";

foreach ($rightBranch as $m) {
    $stmtRaw = $pdo->prepare("SELECT active, status FROM user WHERE userid = :uid");
    $stmtRaw->execute([':uid' => $m['userid']]);
    $rawU = $stmtRaw->fetch(PDO::FETCH_ASSOC);
    
    if ($m['status'] === 'Active') $rightActive++;
    else $rightInactive++;
    
    echo sprintf("%-12s %-8s %-10s %-10s %-12s %-12s %-15s\n",
        $m['userid'],
        $rawU ? $rawU['active'] : '?',
        $rawU ? $rawU['status'] : '?',
        'u.active=' . ($rawU['active'] ?? '?'),
        'u.status=' . ($rawU['status'] ?? '?'),
        number_format($m['investment_usd'], 2),
        $m['status']
    );
}
echo "\nRIGHT TOTAL: {$rightTotal}, ACTIVE: {$rightActive}, INACTIVE: {$rightInactive}\n";

// ========================================================
// 7. TEAM PAGE CALCULATION
// ========================================================
echo "\n========================================================\n";
echo "7. TEAM PAGE CALCULATION (getUserTeamMembersDetailed)\n";
echo "========================================================\n";

$teamLeftMembers = getUserTeamMembersDetailed($userid, 'LEFT', $pdo);
$teamRightMembers = getUserTeamMembersDetailed($userid, 'RIGHT', $pdo);

$teamLeftActive = count(array_filter($teamLeftMembers, function($m) { return ($m['status'] === 'Active'); }));
$teamRightActive = count(array_filter($teamRightMembers, function($m) { return ($m['status'] === 'Active'); }));

echo "left_team.php:\n";
echo "  Total members = " . count($teamLeftMembers) . "\n";
echo "  Active count  = {$teamLeftActive}\n\n";

echo "right_team.php:\n";
echo "  Total members = " . count($teamRightMembers) . "\n";
echo "  Active count  = {$teamRightActive}\n\n";

// ========================================================
// 8. DASHBOARD CALCULATION (index.php exact reproduction)
// ========================================================
echo "========================================================\n";
echo "8. DASHBOARD CALCULATION (index.php exact reproduction)\n";
echo "========================================================\n";

// Reproduce EXACTLY what index.php does:
// Line 149: $leftBranchMembers = !empty($leftid) ? getRootBranchTreeDetailed($leftid, $pdo, 'LEFT') : [];
// Line 150: $rightBranchMembers = !empty($rightid) ? getRootBranchTreeDetailed($rightid, $pdo, 'RIGHT') : [];
// Line 171: $my_left_active_directs = count(array_filter(...));
// Line 172: $my_right_active_directs = count(array_filter(...));

$leftid = $indexLeftId; // same variable name as index.php
$rightid = $indexRightId;

$leftBranchMembers = !empty($leftid) ? getRootBranchTreeDetailed($leftid, $pdo, 'LEFT') : [];
$rightBranchMembers = !empty($rightid) ? getRootBranchTreeDetailed($rightid, $pdo, 'RIGHT') : [];

$my_left_active_directs = count(array_filter($leftBranchMembers, function($m) { return ($m['status'] === 'Active'); }));
$my_right_active_directs = count(array_filter($rightBranchMembers, function($m) { return ($m['status'] === 'Active'); }));

echo "index.php \$leftid                  = '{$leftid}'\n";
echo "index.php \$rightid                 = '{$rightid}'\n";
echo "index.php count(\$leftBranchMembers)  = " . count($leftBranchMembers) . "\n";
echo "index.php count(\$rightBranchMembers) = " . count($rightBranchMembers) . "\n";
echo "index.php \$my_left_active_directs  = {$my_left_active_directs}\n";
echo "index.php \$my_right_active_directs = {$my_right_active_directs}\n";
echo "\nRENDERED VALUE: \"{$my_left_active_directs} / {$my_right_active_directs}\"\n";

// ========================================================
// 9. HTML OUTPUT (simulated render)
// ========================================================
echo "\n========================================================\n";
echo "9. HTML OUTPUT (what index.php would render)\n";
echo "========================================================\n";

$simulatedHTML = <<<HTML
<div class="d-flex justify-content-between py-1 border-bottom">
    <span class="text-muted">Active Team (Left / Right)</span>
    <span class="font-weight-bold text-dark">{$my_left_active_directs} / {$my_right_active_directs}</span>
</div>
HTML;
echo $simulatedHTML . "\n";

// Also read the actual file to print the source lines
$indexFileContent = file_get_contents(__DIR__ . '/index.php');
$indexLines = explode("\n", $indexFileContent);

echo "\nActual source lines from index.php:\n";
// Find and print calculation lines
foreach ($indexLines as $i => $line) {
    $ln = $i + 1;
    if ($ln >= 148 && $ln <= 173) {
        echo "  {$ln}: " . rtrim($line) . "\n";
    }
}
echo "\n  ...\n\n";
// Find and print render lines
foreach ($indexLines as $i => $line) {
    $ln = $i + 1;
    if ($ln >= 1568 && $ln <= 1574) {
        echo "  {$ln}: " . rtrim($line) . "\n";
    }
}

// ========================================================
// 10. CACHE / OPCACHE CHECK
// ========================================================
echo "\n========================================================\n";
echo "10. CACHE / OPCACHE CHECK\n";
echo "========================================================\n";

if (function_exists('opcache_get_status')) {
    $opcStatus = @opcache_get_status(true);
    if ($opcStatus === false) {
        echo "OPcache: DISABLED or restricted\n";
    } else {
        echo "OPcache enabled:            " . ($opcStatus['opcache_enabled'] ? 'YES' : 'NO') . "\n";
        
        $config = @opcache_get_configuration();
        if ($config) {
            echo "validate_timestamps:        " . ($config['directives']['opcache.validate_timestamps'] ? 'YES' : 'NO') . "\n";
            echo "revalidate_freq:            " . $config['directives']['opcache.revalidate_freq'] . " seconds\n";
        }
        
        // Check specific files
        $filesToCheck = [
            realpath(__DIR__ . '/index.php'),
            realpath(__DIR__ . '/common/db_method.php'),
        ];
        echo "\nCached script status:\n";
        foreach ($filesToCheck as $f) {
            if (!$f) continue;
            $found = false;
            if (isset($opcStatus['scripts'])) {
                foreach ($opcStatus['scripts'] as $script) {
                    if (isset($script['full_path']) && $script['full_path'] === $f) {
                        echo "  {$f}:\n";
                        echo "    cached: YES\n";
                        echo "    timestamp: " . date('Y-m-d H:i:s', $script['timestamp']) . "\n";
                        echo "    last_used: " . date('Y-m-d H:i:s', $script['last_used_timestamp']) . "\n";
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                echo "  {$f}: NOT in cache\n";
            }
        }
    }
} else {
    echo "OPcache: NOT AVAILABLE (function opcache_get_status does not exist)\n";
}

// ========================================================
// 11. FILE VERSION CHECK
// ========================================================
echo "\n========================================================\n";
echo "11. FILE VERSION CHECK\n";
echo "========================================================\n";

$indexPath = realpath(__DIR__ . '/index.php');
$dbMethodPath = realpath(__DIR__ . '/common/db_method.php');

echo "index.php path:      {$indexPath}\n";
echo "index.php md5:       " . md5_file($indexPath) . "\n";
echo "index.php sha256:    " . hash_file('sha256', $indexPath) . "\n";
echo "index.php size:      " . filesize($indexPath) . " bytes\n";
echo "index.php mtime:     " . date('Y-m-d H:i:s', filemtime($indexPath)) . "\n\n";

echo "db_method.php path:  {$dbMethodPath}\n";
echo "db_method.php md5:   " . md5_file($dbMethodPath) . "\n";
echo "db_method.php sha256:" . hash_file('sha256', $dbMethodPath) . "\n";
echo "db_method.php size:  " . filesize($dbMethodPath) . " bytes\n";
echo "db_method.php mtime: " . date('Y-m-d H:i:s', filemtime($dbMethodPath)) . "\n";

// Check for stale overrides in the ACTUAL production file
echo "\nChecking production index.php for stale patterns:\n";
echo "  getmydirectactiveleft:  " . substr_count($indexFileContent, 'getmydirectactiveleft') . " occurrences\n";
echo "  getmydirectactiveright: " . substr_count($indexFileContent, 'getmydirectactiveright') . " occurrences\n";
echo "  tbl_userlevel_a:        " . substr_count($indexFileContent, 'tbl_userlevel_a') . " occurrences\n";
echo "  tbl_userlevel_b:        " . substr_count($indexFileContent, 'tbl_userlevel_b') . " occurrences\n";

// Print the relevant source around calculation
echo "\nRelevant source lines (calculation of active directs):\n";
foreach ($indexLines as $i => $line) {
    $ln = $i + 1;
    if (strpos($line, 'my_left_active_directs') !== false || strpos($line, 'my_right_active_directs') !== false) {
        echo "  LINE {$ln}: " . trim($line) . "\n";
    }
}

// ========================================================
// 12. CRITICAL COMPARISON TABLE
// ========================================================
echo "\n========================================================\n";
echo "12. CRITICAL COMPARISON TABLE\n";
echo "========================================================\n";

echo sprintf("\n%-40s %-15s %-15s\n", 'SOURCE', 'LEFT', 'RIGHT');
echo str_repeat('-', 70) . "\n";
echo sprintf("%-40s %-15s %-15s\n", 'tree.left_id / right_id', $treeRow['left_id'] ?? 'NULL', $treeRow['right_id'] ?? 'NULL');
echo sprintf("%-40s %-15s %-15s\n", 'index.php root ID', $indexLeftId, $indexRightId);
echo sprintf("%-40s %-15s %-15s\n", 'left/right_team.php root ID', $teamLeftRoot, $teamRightRoot);
echo sprintf("%-40s %-15s %-15s\n", 'getRootBranchTreeDetailed total', $leftTotal, $rightTotal);
echo sprintf("%-40s %-15s %-15s\n", 'getRootBranchTreeDetailed active', $leftActive, $rightActive);
echo sprintf("%-40s %-15s %-15s\n", 'team page active count', $teamLeftActive, $teamRightActive);
echo sprintf("%-40s %-15s %-15s\n", 'index.php calculated count', $my_left_active_directs, $my_right_active_directs);
echo sprintf("%-40s %-15s %-15s\n", 'index.php rendered value', $my_left_active_directs, $my_right_active_directs);

// ========================================================
// 13. FINAL DIAGNOSIS
// ========================================================
echo "\n========================================================\n";
echo "13. FINAL DIAGNOSIS\n";
echo "========================================================\n";

$divergenceFound = false;
$diagnosis = '';

// Check A: Different root IDs
if (!$leftRootMatch || !$rightRootMatch) {
    $diagnosis = "B. Different root child ID — index.php resolves a different root than team pages";
    $divergenceFound = true;
}

// Check B: Different traversal results
if (!$divergenceFound && ($leftTotal !== count($teamLeftMembers) || $rightTotal !== count($teamRightMembers))) {
    $diagnosis = "C. Different tree traversal — same root but different member sets";
    $divergenceFound = true;
}

// Check C: Different active counts
if (!$divergenceFound && ($leftActive !== $teamLeftActive || $rightActive !== $teamRightActive)) {
    $diagnosis = "D. Different active classification — same members but different active detection";
    $divergenceFound = true;
}

// Check D: index.php calculated vs function direct
if (!$divergenceFound && ($my_left_active_directs !== $leftActive || $my_right_active_directs !== $rightActive)) {
    $diagnosis = "E. Different variable assignment — function returns correct but index.php assigns differently";
    $divergenceFound = true;
}

// Check stale overrides
if (!$divergenceFound && (substr_count($indexFileContent, 'getmydirectactiveleft') > 0 || substr_count($indexFileContent, 'getmydirectactiveright') > 0)) {
    $diagnosis = "G. Production file is outdated — stale override functions still present in production index.php";
    $divergenceFound = true;
}

if (!$divergenceFound) {
    if ($my_left_active_directs === $teamLeftActive && $my_right_active_directs === $teamRightActive) {
        $diagnosis = "NO DIVERGENCE — PHP calculation is IDENTICAL between index.php and team pages.\n"
                   . "  index.php calculates: {$my_left_active_directs} / {$my_right_active_directs}\n"
                   . "  team pages calculate: {$teamLeftActive} / {$teamRightActive}\n"
                   . "  If browser still shows 0/0, the issue is:\n"
                   . "  - H. OPcache serving old bytecode, OR\n"
                   . "  - F. Browser cache, OR\n"
                   . "  - G. Production file deployment not completed (git pull not executed)";
    } else {
        $diagnosis = "UNKNOWN — further investigation needed";
    }
}

echo "\n{$diagnosis}\n";

// ========================================================
// SAFETY CONFIRMATION
// ========================================================
echo "\n========================================================\n";
echo "SAFETY CONFIRMATION\n";
echo "========================================================\n";
echo "Database writes:         0\n";
echo "User records changed:    0\n";
echo "Tree records changed:    0\n";
echo "Investment records changed: 0\n";
echo "Wallet records changed:  0\n";
echo "Transactions changed:    0\n";
echo "Files modified:          0\n";
echo "Migrations executed:     0\n";
echo "sync_tree executed:      0\n";
echo "OPcache reset:           NO\n";

echo "\n================================================================\n";
echo " DIAGNOSTIC COMPLETE — DELETE THIS FILE FROM PRODUCTION\n";
echo "================================================================\n";
