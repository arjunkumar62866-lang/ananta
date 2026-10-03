<?php
// TEMPORARY PROTECTED DEEP DIAGNOSTIC - DELETE AFTER USE
if (($_GET['key'] ?? '') !== 'aX9zK7mQ') { http_response_code(403); die('Forbidden'); }
header('Content-Type: text/plain; charset=utf-8');
@ini_set('max_execution_time', 120);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

global $pdo;
echo "=== PHASE 2: DATABASE STRUCTURE AUDIT ===\n\n";

// Check user table schema
echo "--- user TABLE SCHEMA ---\n";
$cols = $pdo->query("DESCRIBE user")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    if (in_array($c['Field'], ['userid','sponserid','underuserid','join_side','active','status','total_package','package','amount','name'])) {
        echo $c['Field'] . " | Type: " . $c['Type'] . " | Null: " . $c['Null'] . " | Key: " . $c['Key'] . "\n";
    }
}

echo "\n--- tree TABLE SCHEMA ---\n";
$cols2 = $pdo->query("DESCRIBE tree")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols2 as $c) {
    echo $c['Field'] . " | Type: " . $c['Type'] . " | Key: " . $c['Key'] . "\n";
}

echo "\n--- tbl_roi_one TABLE SCHEMA ---\n";
$cols3 = $pdo->query("DESCRIBE tbl_roi_one")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols3 as $c) {
    if (in_array($c['Field'], ['id','user_id','package','amount','real_fund_usd','status','date','package_code','level'])) {
        echo $c['Field'] . " | Type: " . $c['Type'] . " | Null: " . $c['Null'] . "\n";
    }
}

echo "\n--- tbl_roi_two TABLE SCHEMA ---\n";
try {
    $cols4 = $pdo->query("DESCRIBE tbl_roi_two")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols4 as $c) {
        if (in_array($c['Field'], ['id','user_id','package','amount','real_fund_usd','status','date'])) {
            echo $c['Field'] . " | Type: " . $c['Type'] . " | Null: " . $c['Null'] . "\n";
        }
    }
} catch (Throwable $e) { echo "tbl_roi_two: " . $e->getMessage() . "\n"; }

echo "\n=== PHASE 2B: ID FORMAT AUDIT ===\n";
// Check actual ID formats in each table
$idFormats = $pdo->query("SELECT userid FROM user ORDER BY userid LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
echo "user.userid samples: " . implode(', ', $idFormats) . "\n";

$roiIds = $pdo->query("SELECT DISTINCT user_id FROM tbl_roi_one LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
echo "tbl_roi_one.user_id samples: " . implode(', ', $roiIds) . "\n";

$treeIds = $pdo->query("SELECT userid FROM tree LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
echo "tree.userid samples: " . implode(', ', $treeIds) . "\n";

$leftIds = $pdo->query("SELECT left_id FROM tree WHERE left_id IS NOT NULL AND left_id != '' LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
echo "tree.left_id samples: " . implode(', ', $leftIds) . "\n";

$underIds = $pdo->query("SELECT DISTINCT underuserid FROM user WHERE underuserid IS NOT NULL AND underuserid != '' LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
echo "user.underuserid samples: " . implode(', ', $underIds) . "\n";

// Check if AN prefix exists anywhere
$anCount = $pdo->query("SELECT COUNT(*) FROM user WHERE userid LIKE 'AN%'")->fetchColumn();
echo "Users with AN prefix in userid: {$anCount}\n";
$anRoi = $pdo->query("SELECT COUNT(*) FROM tbl_roi_one WHERE user_id LIKE 'AN%'")->fetchColumn();
echo "ROI records with AN prefix in user_id: {$anRoi}\n";

echo "\n=== PHASE 3: DEEP USER AUDIT ===\n";
$auditIds = ['540599', '1290', '789260', '224891'];

foreach ($auditIds as $tid) {
    echo "\n========================================\n";
    echo "USER: {$tid}\n";
    echo "========================================\n";
    
    // User record
    $u = $pdo->prepare("SELECT userid, name, active, status, total_package, package, amount, sponserid, underuserid, join_side FROM user WHERE userid = :a OR userid = :b");
    $u->execute([':a' => $tid, ':b' => 'AN'.$tid]);
    $ur = $u->fetch(PDO::FETCH_ASSOC);
    echo "USER RECORD: " . ($ur ? json_encode($ur) : 'NOT FOUND') . "\n";
    
    // Tree record
    $t = $pdo->prepare("SELECT * FROM tree WHERE userid = :a OR userid = :b");
    $t->execute([':a' => $tid, ':b' => 'AN'.$tid]);
    $tr = $t->fetch(PDO::FETCH_ASSOC);
    echo "TREE NODE: " . ($tr ? json_encode($tr) : 'NOT FOUND') . "\n";
    
    // Parents referencing this user
    $p = $pdo->prepare("SELECT userid, left_id, right_id FROM tree WHERE left_id IN (:a,:b) OR right_id IN (:a,:b)");
    $p->execute([':a' => $tid, ':b' => 'AN'.$tid]);
    $parents = $p->fetchAll(PDO::FETCH_ASSOC);
    echo "PARENTS REFERENCING: " . json_encode($parents) . "\n";
    
    // Investment records
    $inv = $pdo->prepare("SELECT id, user_id, package, amount, real_fund_usd, status, date FROM tbl_roi_one WHERE user_id = :a OR user_id = :b");
    $inv->execute([':a' => $tid, ':b' => 'AN'.$tid]);
    $invRows = $inv->fetchAll(PDO::FETCH_ASSOC);
    echo "tbl_roi_one RECORDS (" . count($invRows) . "): " . json_encode($invRows) . "\n";
    
    try {
        $inv2 = $pdo->prepare("SELECT id, user_id, package, amount, status, date FROM tbl_roi_two WHERE user_id = :a OR user_id = :b");
        $inv2->execute([':a' => $tid, ':b' => 'AN'.$tid]);
        $inv2Rows = $inv2->fetchAll(PDO::FETCH_ASSOC);
        echo "tbl_roi_two RECORDS (" . count($inv2Rows) . "): " . json_encode($inv2Rows) . "\n";
    } catch (Throwable $e) { echo "tbl_roi_two error: " . $e->getMessage() . "\n"; }
    
    // Direct children in tree
    if ($tr) {
        echo "LEFT CHILD: " . ($tr['left_id'] ?: 'EMPTY') . "\n";
        echo "RIGHT CHILD: " . ($tr['right_id'] ?: 'EMPTY') . "\n";
    }
    
    // getUserTeamMembersDetailed test
    if (function_exists('getUserTeamMembersDetailed') && $ur) {
        $actualId = $ur['userid'];
        $leftM = getUserTeamMembersDetailed($actualId, 'LEFT', $pdo);
        $rightM = getUserTeamMembersDetailed($actualId, 'RIGHT', $pdo);
        
        $leftBus = array_sum(array_column($leftM, 'investment_usd'));
        $rightBus = array_sum(array_column($rightM, 'investment_usd'));
        $leftInr = array_sum(array_column($leftM, 'investment_inr'));
        $rightInr = array_sum(array_column($rightM, 'investment_inr'));
        $leftAct = count(array_filter($leftM, function($m) { return ($m['status'] ?? '') === 'Active'; }));
        $rightAct = count(array_filter($rightM, function($m) { return ($m['status'] ?? '') === 'Active'; }));
        
        echo "FUNC LEFT: Count=" . count($leftM) . " Active={$leftAct} BusINR={$leftInr} BusUSD={$leftBus}\n";
        echo "FUNC RIGHT: Count=" . count($rightM) . " Active={$rightAct} BusINR={$rightInr} BusUSD={$rightBus}\n";
        
        // Show first 5 left members with their investment details
        echo "LEFT MEMBER SAMPLES:\n";
        $shown = 0;
        foreach ($leftM as $m) {
            if ($shown >= 5) break;
            echo "  ID={$m['userid']} InvINR={$m['investment_inr']} InvUSD={$m['investment_usd']} Status={$m['status']}\n";
            $shown++;
        }
        
        // Find members with investment in DB but zero in function
        echo "INVESTMENT MISMATCH CHECK (LEFT, first 5 with DB inv):\n";
        $mismatchCount = 0;
        foreach ($leftM as $m) {
            $mid = $m['userid'];
            $dbInv = $pdo->prepare("SELECT COALESCE(SUM(package),0) as pkg, COALESCE(SUM(real_fund_usd),0) as usd FROM tbl_roi_one WHERE user_id = :uid");
            $dbInv->execute([':uid' => $mid]);
            $dbRow = $dbInv->fetch(PDO::FETCH_ASSOC);
            $dbPkg = (float)$dbRow['pkg'];
            $dbUsd = (float)$dbRow['usd'];
            $funcUsd = (float)$m['investment_usd'];
            
            if ($dbPkg > 0 && $funcUsd <= 0) {
                echo "  MISMATCH: ID={$mid} DB_pkg={$dbPkg} DB_usd={$dbUsd} Func_usd={$funcUsd}\n";
                $mismatchCount++;
                if ($mismatchCount >= 5) break;
            }
        }
        if ($mismatchCount == 0) echo "  No mismatches found in left branch\n";
    }
}

echo "\n=== PHASE 4: GLOBAL AUDIT ===\n";

// 1. User exists but missing from tree
$q1 = $pdo->query("SELECT COUNT(*) FROM user u LEFT JOIN tree t ON t.userid = u.userid WHERE t.userid IS NULL")->fetchColumn();
echo "1. User exists, no tree node: {$q1}\n";

// 2. Tree child exists but user missing
$q2a = $pdo->query("SELECT COUNT(*) FROM tree t LEFT JOIN user u ON u.userid = t.left_id WHERE t.left_id IS NOT NULL AND t.left_id != '' AND u.userid IS NULL")->fetchColumn();
$q2b = $pdo->query("SELECT COUNT(*) FROM tree t LEFT JOIN user u ON u.userid = t.right_id WHERE t.right_id IS NOT NULL AND t.right_id != '' AND u.userid IS NULL")->fetchColumn();
echo "2. Tree left_id points to missing user: {$q2a}\n";
echo "2. Tree right_id points to missing user: {$q2b}\n";

// 5. Child appears under multiple parents
$q5 = $pdo->query("
    SELECT child_id, COUNT(*) as cnt FROM (
        SELECT left_id as child_id FROM tree WHERE left_id IS NOT NULL AND left_id != ''
        UNION ALL
        SELECT right_id as child_id FROM tree WHERE right_id IS NOT NULL AND right_id != ''
    ) sub GROUP BY child_id HAVING cnt > 1
")->fetchAll(PDO::FETCH_ASSOC);
echo "5. Duplicate child placements: " . count($q5) . "\n";
foreach ($q5 as $dup) echo "   Child {$dup['child_id']} appears {$dup['cnt']} times\n";

// 6. Same user in both left and right of parent
$q6 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE left_id = right_id AND left_id IS NOT NULL AND left_id != ''")->fetchAll(PDO::FETCH_ASSOC);
echo "6. Same user in both left+right: " . count($q6) . "\n";

// 7. Investment exists but user missing
$q7 = $pdo->query("SELECT COUNT(*) FROM tbl_roi_one r LEFT JOIN user u ON u.userid = r.user_id WHERE u.userid IS NULL")->fetchColumn();
echo "7. ROI record with no user: {$q7}\n";

// 11. ID format analysis
$numericUsers = $pdo->query("SELECT COUNT(*) FROM user WHERE userid REGEXP '^[0-9]+$'")->fetchColumn();
$anUsers = $pdo->query("SELECT COUNT(*) FROM user WHERE userid LIKE 'AN%'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
echo "11. Total users: {$totalUsers}, Numeric IDs: {$numericUsers}, AN-prefixed: {$anUsers}\n";

// 12. Investment table ID format
$numericRoi = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM tbl_roi_one WHERE user_id REGEXP '^[0-9]+$'")->fetchColumn();
$anRoi2 = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM tbl_roi_one WHERE user_id LIKE 'AN%'")->fetchColumn();
$totalRoiUsers = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM tbl_roi_one")->fetchColumn();
echo "12. ROI users: {$totalRoiUsers}, Numeric: {$numericRoi}, AN-prefixed: {$anRoi2}\n";

// Investment stats
$totalInvAmt = $pdo->query("SELECT SUM(package) FROM tbl_roi_one")->fetchColumn();
$totalInvUsd = $pdo->query("SELECT SUM(real_fund_usd) FROM tbl_roi_one")->fetchColumn();
echo "TOTAL ROI: INR={$totalInvAmt}, USD={$totalInvUsd}\n";

// Users with investment
$usersWithInv = $pdo->query("SELECT DISTINCT user_id FROM tbl_roi_one")->fetchAll(PDO::FETCH_COLUMN);
echo "Users with tbl_roi_one investment (" . count($usersWithInv) . "): " . implode(', ', $usersWithInv) . "\n";

// parseInputToUSD test
if (function_exists('parseInputToUSD')) {
    $testUsd = parseInputToUSD(13000, 'INR', $pdo);
    echo "parseInputToUSD(13000 INR) = {$testUsd} USD\n";
}

echo "\n=== PHASE 13: GLOBAL TREE INTEGRITY ===\n";

// A. Every tree child has max 1 parent (already checked in #5)
// D. user.underuserid matches tree parent
$q_d = $pdo->query("
    SELECT u.userid, u.underuserid, 
           (SELECT t2.userid FROM tree t2 WHERE t2.left_id = u.userid OR t2.right_id = u.userid LIMIT 1) as tree_parent
    FROM user u 
    WHERE u.underuserid IS NOT NULL AND u.underuserid != ''
    HAVING tree_parent IS NOT NULL AND tree_parent != u.underuserid
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);
echo "D. user.underuserid != tree parent: " . count($q_d) . "\n";
foreach ($q_d as $mm) echo "   User {$mm['userid']}: underuserid={$mm['underuserid']} tree_parent={$mm['tree_parent']}\n";

// Multiple tree rows for same userid
$q19 = $pdo->query("SELECT userid, COUNT(*) as cnt FROM tree GROUP BY userid HAVING cnt > 1")->fetchAll(PDO::FETCH_ASSOC);
echo "19. Multiple tree rows for same user: " . count($q19) . "\n";

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
