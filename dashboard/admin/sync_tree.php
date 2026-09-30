<?php
/**
 * Master Tree Resynchronization & Downline Indexing Script for Production / Hostinger
 */
chdir(__DIR__ . '/../user1');
require_once 'common/connection.php';
require_once 'common/db_method.php';

header('Content-Type: text/plain');
echo "=== STARTING PRODUCTION TREE RESYNCHRONIZATION ===\n\n";

try {
    // 1. Fetch all users from tree table
    $treeUsers = $pdo->query("SELECT userid, left_id, right_id FROM tree")->fetchAll(PDO::FETCH_ASSOC);
    $treeMap = [];
    foreach ($treeUsers as $u) {
        $treeMap[$u['userid']] = [
            'left'  => $u['left_id'],
            'right' => $u['right_id']
        ];
    }

    echo "Total users in tree table: " . count($treeMap) . "\n";

    function getSubtreeNodesProd($nodeId, &$treeMap) {
        if (empty($nodeId) || !isset($treeMap[$nodeId])) {
            return [];
        }
        $nodes = [];
        $left = $treeMap[$nodeId]['left'];
        $right = $treeMap[$nodeId]['right'];

        if (!empty($left)) {
            $nodes[] = $left;
            $nodes = array_merge($nodes, getSubtreeNodesProd($left, $treeMap));
        }
        if (!empty($right)) {
            $nodes[] = $right;
            $nodes = array_merge($nodes, getSubtreeNodesProd($right, $treeMap));
        }
        return array_unique($nodes);
    }

    // 2. Clean mapping tables
    $pdo->exec("TRUNCATE TABLE tbl_userlevel_a");
    $pdo->exec("TRUNCATE TABLE tbl_userlevel_b");
    $pdo->exec("TRUNCATE TABLE tbl_downline");

    $insLvlA = $pdo->prepare("INSERT INTO tbl_userlevel_a (sponser_id, downline_id, level, date) VALUES (:sp, :dl, :lvl, NOW())");
    $insLvlB = $pdo->prepare("INSERT INTO tbl_userlevel_b (sponser_id, downline_id, level, date) VALUES (:sp, :dl, :lvl, NOW())");
    $insDown = $pdo->prepare("INSERT INTO tbl_downline (upline_id, downline_id, date, time) VALUES (:up, :dl, CURDATE(), CURTIME())");
    $updTreeCount = $pdo->prepare("UPDATE tree SET leftcount = :lc, rightcount = :rc WHERE userid = :uid");

    $syncCount = 0;
    foreach ($treeMap as $uid => $children) {
        $leftId  = $children['left'];
        $rightId = $children['right'];

        $leftSubtree  = getSubtreeNodesProd($leftId, $treeMap);
        if (!empty($leftId)) array_unshift($leftSubtree, $leftId);

        $rightSubtree = getSubtreeNodesProd($rightId, $treeMap);
        if (!empty($rightId)) array_unshift($rightSubtree, $rightId);

        $leftCount  = count($leftSubtree);
        $rightCount = count($rightSubtree);

        $updTreeCount->execute([':lc' => $leftCount, ':rc' => $rightCount, ':uid' => $uid]);

        foreach ($leftSubtree as $downlineId) {
            $insLvlA->execute([':sp' => $uid, ':dl' => $downlineId, ':lvl' => 1]);
            $insDown->execute([':up' => $uid, ':dl' => $downlineId]);
        }

        foreach ($rightSubtree as $downlineId) {
            $insLvlB->execute([':sp' => $uid, ':dl' => $downlineId, ':lvl' => 1]);
            $insDown->execute([':up' => $uid, ':dl' => $downlineId]);
        }
        $syncCount++;
    }

    echo "Successfully synchronized tree counts & level mapping for {$syncCount} users.\n";

    // 3. Ensure tbl_sponsor sync
    $users = $pdo->query("SELECT userid, sponserid FROM user WHERE sponserid IS NOT NULL AND sponserid != ''")->fetchAll(PDO::FETCH_ASSOC);

    $chkSpon = $pdo->prepare("SELECT COUNT(*) FROM tbl_sponsor WHERE sponsor_id = :sp AND referral_id = :ref");
    $insSpon = $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (:sp, :ref, CURDATE())");

    $sponInserted = 0;
    foreach ($users as $u) {
        $sp  = $u['sponserid'];
        $ref = $u['userid'];
        if (empty($sp) || empty($ref)) continue;

        $chkSpon->execute([':sp' => $sp, ':ref' => $ref]);
        if ($chkSpon->fetchColumn() == 0) {
            $insSpon->execute([':sp' => $sp, ':ref' => $ref]);
            $sponInserted++;
        }
    }

    echo "Ensured tbl_sponsor sync ({$sponInserted} missing records added).\n";
    echo "\n=== PRODUCTION TREE SYNCHRONIZATION COMPLETE SUCCESSFULLY ===";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
