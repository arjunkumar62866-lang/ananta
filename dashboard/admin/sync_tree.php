<?php
/**
 * Master Tree Resynchronization & Downline Indexing Script
 * Run this from browser or CLI whenever dashboard counts seem wrong after a move.
 * URL: /dashboard/admin/sync_tree.php
 */
chdir(__DIR__ . '/../user1');
require_once 'common/connection.php';

header('Content-Type: text/plain; charset=utf-8');
echo "=== MASTER TREE RESYNCHRONIZATION ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

$errors  = [];
$log     = [];

try {
    /* ------------------------------------------------------------------
     * STEP 0 — Ensure every user has a tree row
     * ------------------------------------------------------------------ */
    echo "STEP 0: Auto-creating missing tree rows...\n";

    $allUsers = $pdo->query("SELECT userid, sponserid, underuserid, join_side FROM user")->fetchAll(PDO::FETCH_ASSOC);
    $existingTreeUsers = $pdo->query("SELECT userid FROM tree")->fetchAll(PDO::FETCH_COLUMN);
    $existingTreeSet   = array_flip($existingTreeUsers);

    $insTree = $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, leftpv, rightpv, leftcount, rightcount, lefttotal, righttotal)
                              VALUES (:uid, '', '', 1, 'left', 0, 0, 0, 0, 0, 0, 0, 0)");
    $newTreeRows = 0;
    foreach ($allUsers as $u) {
        if (!isset($existingTreeSet[$u['userid']])) {
            $insTree->execute([':uid' => $u['userid']]);
            $existingTreeSet[$u['userid']] = true;
            $newTreeRows++;
        }
    }
    echo "  -> Created {$newTreeRows} missing tree rows.\n\n";

    /* ------------------------------------------------------------------
     * STEP 1 — Sync tree.left_id / tree.right_id from user.underuserid
     *          (Only fills EMPTY slots — does NOT overwrite existing links)
     * ------------------------------------------------------------------ */
    echo "STEP 1: Syncing tree left_id/right_id from user.underuserid...\n";

    // First: force-update links based on user table (the ground truth after a move)
    // We must CLEAR old parent links for moved users, then set new ones.

    // Build parent→children map from user table
    $parentToChildren = [];
    foreach ($allUsers as $u) {
        $pId  = (string)($u['underuserid'] ?? '');
        $cId  = (string)($u['userid'] ?? '');
        $side = strtolower((string)($u['join_side'] ?? ''));
        if (!empty($pId) && !empty($cId) && $pId !== $cId) {
            $parentToChildren[$pId][$side] = $cId;
        }
    }

    // Rebuild ALL left_id / right_id in tree from user table (full resync, not just empty)
    $resetTree = $pdo->prepare("UPDATE tree SET left_id = '', right_id = '' WHERE userid = :uid");
    $updLeft   = $pdo->prepare("UPDATE tree SET left_id  = :cid WHERE userid = :pid");
    $updRight  = $pdo->prepare("UPDATE tree SET right_id = :cid WHERE userid = :pid");

    // First reset all left/right so we have clean state
    foreach (array_keys($existingTreeSet) as $uid) {
        $resetTree->execute([':uid' => $uid]);
    }

    // Then re-apply from user.underuserid
    $linkCount = 0;
    foreach ($parentToChildren as $pId => $sides) {
        if (isset($sides['left']) || isset($sides['l'])) {
            $cId = $sides['left'] ?? $sides['l'];
            $updLeft->execute([':cid' => $cId, ':pid' => $pId]);
            $linkCount++;
        }
        if (isset($sides['right']) || isset($sides['r'])) {
            $cId = $sides['right'] ?? $sides['r'];
            $updRight->execute([':cid' => $cId, ':pid' => $pId]);
            $linkCount++;
        }
    }
    echo "  -> Applied {$linkCount} left/right tree links.\n\n";

    /* ------------------------------------------------------------------
     * STEP 2 — Load tree map for traversal
     * ------------------------------------------------------------------ */
    echo "STEP 2: Loading tree structure...\n";

    $treeUsers = $pdo->query("SELECT userid, left_id, right_id FROM tree")->fetchAll(PDO::FETCH_ASSOC);
    $treeMap   = [];
    foreach ($treeUsers as $u) {
        $treeMap[(string)$u['userid']] = [
            'left'  => (string)($u['left_id']  ?? ''),
            'right' => (string)($u['right_id'] ?? '')
        ];
    }
    echo "  -> Loaded " . count($treeMap) . " users from tree table.\n\n";

    /* ------------------------------------------------------------------
     * Subtree traversal helper — defined ONCE at top level to prevent
     * PHP "Cannot redeclare" fatal errors on repeated calls
     * ------------------------------------------------------------------ */
    if (!function_exists('getSubtreeNodesProd')) {
        function getSubtreeNodesProd($nodeId, &$treeMap) {
            if (empty($nodeId) || !isset($treeMap[$nodeId])) return [];
            $nodes = [];
            $stack = [$nodeId];
            $seen  = [];
            while (!empty($stack)) {
                $cur = array_pop($stack);
                if (isset($seen[$cur])) continue;
                $seen[$cur] = true;
                if ($cur !== $nodeId) $nodes[] = $cur;
                $l = $treeMap[$cur]['left']  ?? '';
                $r = $treeMap[$cur]['right'] ?? '';
                if (!empty($l) && !isset($seen[$l])) $stack[] = $l;
                if (!empty($r) && !isset($seen[$r])) $stack[] = $r;
            }
            return array_unique($nodes);
        }
    }

    /* ------------------------------------------------------------------
     * STEP 3 — Truncate index tables & rebuild
     * ------------------------------------------------------------------ */
    echo "STEP 3: Truncating index tables (tbl_userlevel_a, tbl_userlevel_b, tbl_downline)...\n";
    $pdo->exec("TRUNCATE TABLE tbl_userlevel_a");
    $pdo->exec("TRUNCATE TABLE tbl_userlevel_b");
    $pdo->exec("TRUNCATE TABLE tbl_downline");
    echo "  -> Truncated.\n\n";

    echo "STEP 4: Rebuilding per-user subtree indexes...\n";

    $insLvlA      = $pdo->prepare("INSERT INTO tbl_userlevel_a (sponser_id, downline_id, level, date) VALUES (:sp, :dl, :lvl, NOW())");
    $insLvlB      = $pdo->prepare("INSERT INTO tbl_userlevel_b (sponser_id, downline_id, level, date) VALUES (:sp, :dl, :lvl, NOW())");
    $insDown      = $pdo->prepare("INSERT INTO tbl_downline (upline_id, downline_id, date, time) VALUES (:up, :dl, CURDATE(), CURTIME())");
    $updTreeCount = $pdo->prepare("UPDATE tree SET leftcount = :lc, rightcount = :rc WHERE userid = :uid");

    $totalA = 0; $totalB = 0; $totalD = 0; $syncCount = 0;

    foreach ($treeMap as $uid => $children) {
        $leftId  = $children['left'];
        $rightId = $children['right'];

        $leftSubtree  = !empty($leftId)  ? getSubtreeNodesProd($leftId,  $treeMap) : [];
        if (!empty($leftId))  array_unshift($leftSubtree,  $leftId);

        $rightSubtree = !empty($rightId) ? getSubtreeNodesProd($rightId, $treeMap) : [];
        if (!empty($rightId)) array_unshift($rightSubtree, $rightId);

        $leftCount  = count($leftSubtree);
        $rightCount = count($rightSubtree);

        $updTreeCount->execute([':lc' => $leftCount, ':rc' => $rightCount, ':uid' => $uid]);

        foreach ($leftSubtree as $dlId) {
            $insLvlA->execute([':sp' => $uid, ':dl' => $dlId, ':lvl' => 1]);
            $insDown->execute([':up' => $uid, ':dl' => $dlId]);
            $totalA++; $totalD++;
        }
        foreach ($rightSubtree as $dlId) {
            $insLvlB->execute([':sp' => $uid, ':dl' => $dlId, ':lvl' => 1]);
            $insDown->execute([':up' => $uid, ':dl' => $dlId]);
            $totalB++; $totalD++;
        }

        if ($leftCount > 0 || $rightCount > 0) {
            $log[] = "  User {$uid}: Left={$leftCount}, Right={$rightCount}";
        }
        $syncCount++;
    }

    echo "  -> Processed {$syncCount} users.\n";
    echo "  -> tbl_userlevel_a: {$totalA} records inserted\n";
    echo "  -> tbl_userlevel_b: {$totalB} records inserted\n";
    echo "  -> tbl_downline:    {$totalD} records inserted\n\n";

    /* ------------------------------------------------------------------
     * STEP 5 — Sync tbl_sponsor
     * ------------------------------------------------------------------ */
    echo "STEP 5: Syncing tbl_sponsor...\n";

    $users = $pdo->query("SELECT userid, sponserid FROM user WHERE sponserid IS NOT NULL AND sponserid != ''")->fetchAll(PDO::FETCH_ASSOC);
    $chkSpon  = $pdo->prepare("SELECT COUNT(*) FROM tbl_sponsor WHERE sponsor_id = :sp AND referral_id = :ref");
    $insSpon  = $pdo->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (:sp, :ref, CURDATE())");

    $sponInserted = 0;
    foreach ($users as $u) {
        if (empty($u['sponserid']) || empty($u['userid'])) continue;
        $chkSpon->execute([':sp' => $u['sponserid'], ':ref' => $u['userid']]);
        if ($chkSpon->fetchColumn() == 0) {
            $insSpon->execute([':sp' => $u['sponserid'], ':ref' => $u['userid']]);
            $sponInserted++;
        }
    }
    echo "  -> {$sponInserted} missing sponsor records added.\n\n";

    /* ------------------------------------------------------------------
     * Summary
     * ------------------------------------------------------------------ */
    echo "=== USERS WITH TEAM (Left > 0 OR Right > 0) ===\n";
    foreach ($log as $line) echo $line . "\n";

    echo "\n=== SYNCHRONIZATION COMPLETE ===\n";
    echo "Finished at: " . date('Y-m-d H:i:s') . "\n";
    echo "All dashboard counts will now reflect correct team quantities.\n";

} catch (Exception $e) {
    echo "\n!!! ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
