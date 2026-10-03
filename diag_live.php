<?php
// Temporary Live Diagnostic & Auto-Repair Script
header('Content-Type: text/plain; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Clear OPCache if enabled
if (function_exists('opcache_reset')) {
    $opRes = opcache_reset() ? 'SUCCESS' : 'FAILED';
    echo "OPCache Reset: {$opRes}\n\n";
} else {
    echo "OPCache Reset: Not Available\n\n";
}

require_once __DIR__ . '/dashboard/user1/common/connection.php';
require_once __DIR__ . '/dashboard/user1/common/db_method.php';
require_once __DIR__ . '/dashboard/admin/common/db_method.php';

global $pdo;

echo "=== 1. RUNNING LIVE TREE DUPLICATE CLEANUP ===\n";
if (function_exists('cleanupGlobalTreeDuplicates')) {
    $cleanCount = cleanupGlobalTreeDuplicates($pdo);
    echo "Cleanup completed. Removed/fixed duplicates count: {$cleanCount}\n\n";
} else {
    echo "cleanupGlobalTreeDuplicates function not found!\n\n";
}

echo "=== 2. AUDITING TARGET USERS IN LIVE DB ===\n";
$targetIds = ['1290', '540599', '224891', '789260'];

foreach ($targetIds as $tid) {
    echo "----------------------------------------\n";
    echo "AUDITING USER ID: {$tid}\n";
    
    // Check User Table
    $uStmt = $pdo->prepare("SELECT userid, name, active, status, total_package, amount, sponserid, underuserid, join_side FROM user WHERE userid = :uid OR userid = :anid");
    $uStmt->execute([':uid' => $tid, ':anid' => 'AN' . $tid]);
    $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
    echo "USER TABLE: " . json_encode($uRow) . "\n";
    
    // Check Tree Table where user is node
    $tStmt = $pdo->prepare("SELECT userid, left_id, right_id, join_side FROM tree WHERE userid = :uid OR userid = :anid");
    $tStmt->execute([':uid' => $tid, ':anid' => 'AN' . $tid]);
    $tRow = $tStmt->fetch(PDO::FETCH_ASSOC);
    echo "TREE NODE: " . json_encode($tRow) . "\n";
    
    // Check Tree Table where user is referenced as child
    $pStmt = $pdo->prepare("SELECT userid, left_id, right_id FROM tree WHERE left_id = :uid OR right_id = :uid OR left_id = :anid OR right_id = :anid");
    $pStmt->execute([':uid' => $tid, ':anid' => 'AN' . $tid]);
    $pRows = $pStmt->fetchAll(PDO::FETCH_ASSOC);
    echo "REFERENCED AS CHILD BY PARENTS: " . json_encode($pRows) . "\n";

    // Test getUserTeamMembersDetailed
    if (function_exists('getUserTeamMembersDetailed')) {
        $leftM = getUserTeamMembersDetailed($tid, 'LEFT', $pdo);
        $rightM = getUserTeamMembersDetailed($tid, 'RIGHT', $pdo);
        $leftBus = array_sum(array_column($leftM, 'investment_usd'));
        $rightBus = array_sum(array_column($rightM, 'investment_usd'));
        $leftAct = count(array_filter($leftM, function($m) { return (($m['status'] ?? '') === 'Active'); }));
        $rightAct = count(array_filter($rightM, function($m) { return (($m['status'] ?? '') === 'Active'); }));
        echo "CALCULATED LEFT BRANCH: Count=" . count($leftM) . ", Active=" . $leftAct . ", BusUSD=" . $leftBus . "\n";
        echo "CALCULATED RIGHT BRANCH: Count=" . count($rightM) . ", Active=" . $rightAct . ", BusUSD=" . $rightBus . "\n";
    }
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
