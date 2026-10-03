<?php
header('Content-Type: text/plain; charset=utf-8');
@ini_set('max_execution_time', 120);

if (function_exists('opcache_reset')) {
    @opcache_reset();
    echo "OPCache Reset: SUCCESS\n\n";
}

if (file_exists(__DIR__ . '/dashboard/user1/common/connection.php')) {
    require_once __DIR__ . '/dashboard/user1/common/connection.php';
}
if (file_exists(__DIR__ . '/dashboard/user1/common/db_method.php')) {
    require_once __DIR__ . '/dashboard/user1/common/db_method.php';
}
if (file_exists(__DIR__ . '/dashboard/admin/common/db_method.php')) {
    require_once __DIR__ . '/dashboard/admin/common/db_method.php';
}

global $pdo;

echo "=== 1. TREE CLEANUP ===\n";
try {
    if (function_exists('cleanupGlobalTreeDuplicates')) {
        $c = cleanupGlobalTreeDuplicates($pdo);
        echo "Cleanup done. Removed duplicates: {$c}\n\n";
    }
} catch (Throwable $e) {
    echo "Cleanup error: " . $e->getMessage() . "\n\n";
}

$targetIds = ['1290', '540599', '224891', '789260'];

foreach ($targetIds as $tid) {
    echo "----------------------------------------\n";
    echo "AUDITING USER ID: {$tid}\n";
    try {
        $uStmt = $pdo->prepare("SELECT userid, name, active, status, total_package, sponserid, underuserid, join_side FROM user WHERE userid = :uid OR userid = :anid");
        $uStmt->execute([':uid' => $tid, ':anid' => 'AN' . $tid]);
        $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
        echo "USER TABLE: " . ($uRow ? json_encode($uRow) : 'NOT FOUND') . "\n";

        $tStmt = $pdo->prepare("SELECT userid, left_id, right_id, join_side FROM tree WHERE userid = :uid OR userid = :anid");
        $tStmt->execute([':uid' => $tid, ':anid' => 'AN' . $tid]);
        $tRow = $tStmt->fetch(PDO::FETCH_ASSOC);
        echo "TREE NODE: " . ($tRow ? json_encode($tRow) : 'NOT FOUND') . "\n";

        $pStmt = $pdo->prepare("SELECT userid, left_id, right_id FROM tree WHERE left_id = :uid OR right_id = :uid OR left_id = :anid OR right_id = :anid");
        $pStmt->execute([':uid' => $tid, ':anid' => 'AN' . $tid]);
        $pRows = $pStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "REFERENCED AS CHILD BY: " . json_encode($pRows) . "\n";

        if (function_exists('getUserTeamMembersDetailed')) {
            $leftM  = getUserTeamMembersDetailed($tid, 'LEFT', $pdo);
            $rightM = getUserTeamMembersDetailed($tid, 'RIGHT', $pdo);
            $leftBus  = array_sum(array_column($leftM, 'investment_usd'));
            $rightBus = array_sum(array_column($rightM, 'investment_usd'));
            $leftAct  = count(array_filter($leftM,  function($m) { return (($m['status'] ?? '') === 'Active'); }));
            $rightAct = count(array_filter($rightM, function($m) { return (($m['status'] ?? '') === 'Active'); }));
            echo "LEFT BRANCH: Total=" . count($leftM) . ", Active=" . $leftAct . ", BusUSD=$" . number_format($leftBus, 2) . "\n";
            echo "RIGHT BRANCH: Total=" . count($rightM) . ", Active=" . $rightAct . ", BusUSD=$" . number_format($rightBus, 2) . "\n";
        }
    } catch (Throwable $ex) {
        echo "Error for {$tid}: " . $ex->getMessage() . "\n";
    }
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
