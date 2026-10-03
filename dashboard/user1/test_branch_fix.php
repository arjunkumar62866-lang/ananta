<?php
$isCli = (php_sapi_name() === "cli");
$key = $_GET["key"] ?? ($argv[1] ?? "");
if (!$isCli && $key !== "aX9zK7mQ") { http_response_code(403); die("Forbidden\n"); }
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

global $pdo;

echo "=== TESTING MULTI-ROOT BRANCH CALCULATION ===\n\n";

function testGetUserTeamMembersDetailedFixed($userid, $teamType, $db) {
    if (!$db || empty($userid)) return [];

    $userid = trim((string)$userid);
    $cleanUserid = (stripos($userid, 'AN') === 0) ? trim(substr($userid, 2)) : $userid;
    $teamType = strtoupper(trim($teamType));

    $stmtUsers = $db->query("
        SELECT u.userid, u.name, COALESCE(u.`rank`, 'Member') as `rank`, u.joining_date, u.active, u.status,
               COALESCE(u.total_package, 0) as user_total_package, COALESCE(u.package, 0) as user_package, COALESCE(u.amount, 0) as user_amount,
               u.join_side, u.sponserid, u.underuserid,
               t.left_id, t.right_id
        FROM user u
        LEFT JOIN tree t ON t.userid = u.userid
    ");
    $userMap = [];
    $underMap = [];
    $directMap = [];
    while ($r = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
        $uid = trim((string)$r['userid']);
        $userMap[$uid] = $r;
        $cleanUid = (stripos($uid, 'AN') === 0) ? trim(substr($uid, 2)) : $uid;
        if ($cleanUid !== $uid) {
            $userMap[$cleanUid] = $r;
        } else {
            $userMap['AN' . $uid] = $r;
        }

        $sp = !empty($r['sponserid']) ? trim((string)$r['sponserid']) : '';
        if ($sp !== '') {
            $directMap[$sp] = ($directMap[$sp] ?? 0) + 1;
            $cleanSp = (stripos($sp, 'AN') === 0) ? trim(substr($sp, 2)) : $sp;
            if ($cleanSp !== $sp) {
                $directMap[$cleanSp] = ($directMap[$cleanSp] ?? 0) + 1;
            }
        }
        $pId = !empty($r['underuserid']) ? trim((string)$r['underuserid']) : '';
        if ($pId !== '') {
            $cleanPid = (stripos($pId, 'AN') === 0) ? trim(substr($pId, 2)) : $pId;
            $anPid = (stripos($pId, 'AN') === 0) ? $pId : 'AN' . $pId;
            $underMap[$pId][] = $r;
            $underMap[$cleanPid][] = $r;
            $underMap[$anPid][] = $r;
        }
    }

    $effectiveUserId = (!isset($userMap[$userid]) && isset($userMap[$cleanUserid])) ? $cleanUserid : $userid;
    if (!isset($userMap[$effectiveUserId])) return [];

    $targetSide = ($teamType === 'LEFT') ? 'LEFT' : 'RIGHT';
    $targetSideLower = strtolower($targetSide);
    $rootChildIds = [];

    // 1. From tree table
    $uRow = $userMap[$effectiveUserId];
    $treeChild = ($targetSide === 'LEFT') ? ($uRow['left_id'] ?? '') : ($uRow['right_id'] ?? '');
    $treeChild = trim((string)$treeChild);
    if (!empty($treeChild) && $treeChild !== $effectiveUserId && (isset($userMap[$treeChild]) || isset($userMap[trim(substr($treeChild, 2))]))) {
        $rootChildIds[] = $treeChild;
    }

    // 2. From underMap (all users whose underuserid = effectiveUserId and join_side matches)
    if (isset($underMap[$effectiveUserId])) {
        foreach ($underMap[$effectiveUserId] as $uc) {
            $cId = trim((string)$uc['userid']);
            $side = strtolower($uc['join_side'] ?? '');
            if ($cId !== $effectiveUserId && !in_array($cId, $rootChildIds)) {
                if ($side === $targetSideLower || ($targetSideLower === 'left' && $side === 'l') || ($targetSideLower === 'right' && $side === 'r')) {
                    $rootChildIds[] = $cId;
                }
            }
        }
    }

    if (empty($rootChildIds)) return [];

    // Investments map
    $stmtInv = $db->query("
        SELECT r.user_id, COALESCE(SUM(r.package), 0) as total_inr, COALESCE(SUM(r.real_fund_usd), 0) as total_usd, MAX(r.date) as latest_date,
               (SELECT r2.package_code FROM tbl_roi_one r2 WHERE r2.user_id = r.user_id ORDER BY r2.id DESC LIMIT 1) as latest_pkg
        FROM tbl_roi_one r
        GROUP BY r.user_id
    ");
    $invMap = [];
    while ($r = $stmtInv->fetch(PDO::FETCH_ASSOC)) {
        $uIdStr = trim((string)$r['user_id']);
        $cleanU = (stripos($uIdStr, 'AN') === 0) ? trim(substr($uIdStr, 2)) : $uIdStr;
        $anU    = (stripos($uIdStr, 'AN') === 0) ? $uIdStr : 'AN' . $uIdStr;
        $invMap[$uIdStr] = $r;
        $invMap[$cleanU] = $r;
        $invMap[$anU]    = $r;
    }

    try {
        $stmtInv2 = $db->query("
            SELECT r.user_id, COALESCE(SUM(r.package), 0) as total_inr, MAX(r.date) as latest_date
            FROM tbl_roi_two r
            GROUP BY r.user_id
        ");
        while ($r2 = $stmtInv2->fetch(PDO::FETCH_ASSOC)) {
            $uid2 = (string)$r2['user_id'];
            if (!isset($invMap[$uid2])) {
                $invMap[$uid2] = [
                    'total_inr'   => (float)$r2['total_inr'],
                    'total_usd'   => 0,
                    'latest_date' => $r2['latest_date'],
                    'latest_pkg'  => 'ANANTA'
                ];
            } else {
                $invMap[$uid2]['total_inr'] += (float)$r2['total_inr'];
                if (empty($invMap[$uid2]['latest_date'])) {
                    $invMap[$uid2]['latest_date'] = $r2['latest_date'];
                }
            }
        }
    } catch (Exception $exInv2) {}

    $results = [];
    $queue = [];
    foreach ($rootChildIds as $cid) {
        $queue[] = [
            'userid'    => $cid,
            'parent_id' => $effectiveUserId,
            'position'  => strtoupper($targetSide),
            'level'     => 1
        ];
    }
    $visited = [];

    while (!empty($queue)) {
        $curr = array_shift($queue);
        $uid = $curr['userid'];
        if (isset($visited[$uid]) || !isset($userMap[$uid])) continue;
        $visited[$uid] = true;

        $uData = $userMap[$uid];
        $invData = $invMap[$uid] ?? [];

        $userPkgInr = max((float)($uData['user_total_package'] ?? 0), (float)($uData['user_package'] ?? 0), (float)($uData['user_amount'] ?? 0));
        $invInr = max((float)($invData['total_inr'] ?? 0), $userPkgInr);
        $invUsd = (float)($invData['total_usd'] ?? 0);
        if ($invUsd <= 0 && $invInr > 0) {
            $invUsd = parseInputToUSD($invInr, 'INR', $db);
        }

        $directCount = $directMap[$uid] ?? 0;
        $nodePos = !empty($curr['position']) ? $curr['position'] : (!empty($uData['join_side']) ? strtoupper($uData['join_side']) : $targetSide);

        $hasActiveInvestment = ($invUsd > 0 || $invInr > 0);
        $isActive = $hasActiveInvestment;
        $nodeStatus = $isActive ? 'Active' : 'Inactive';

        $results[] = [
            'userid'           => $uid,
            'name'             => $uData['name'],
            'rank'             => $uData['rank'],
            'level'            => $curr['level'],
            'parent_id'        => $curr['parent_id'],
            'position'         => strtoupper($nodePos),
            'joining_date'     => $uData['joining_date'],
            'investment_date'  => $invData['latest_date'] ?? 'N/A',
            'investment_inr'   => $invInr,
            'investment_usd'   => $invUsd,
            'status'           => $nodeStatus,
            'package'          => $invData['latest_pkg'] ?? ($invUsd > 0 ? 'ANANTA' : 'N/A'),
            'direct_count'     => $directCount,
            'downline_count'   => 0
        ];

        // Placement children
        $children = [];
        $assignedChildIds = [];

        // 1. LEFT SLOT
        $leftId = (!empty($uData['left_id']) && isset($userMap[(string)$uData['left_id']])) ? (string)$uData['left_id'] : '';
        if (empty($leftId) && isset($underMap[$uid])) {
            foreach ($underMap[$uid] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $uid && strtolower($uc['join_side'] ?? '') === 'left' && !isset($visited[$cId])) {
                    $leftId = $cId;
                    break;
                }
            }
        }
        if (!empty($leftId) && !isset($visited[$leftId])) {
            $children[] = ['id' => $leftId, 'side' => 'LEFT'];
            $assignedChildIds[$leftId] = true;
        }

        // 2. RIGHT SLOT
        $rightId = (!empty($uData['right_id']) && isset($userMap[(string)$uData['right_id']])) ? (string)$uData['right_id'] : '';
        if (empty($rightId) && isset($underMap[$uid])) {
            foreach ($underMap[$uid] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $uid && !isset($assignedChildIds[$cId]) && strtolower($uc['join_side'] ?? '') === 'right' && !isset($visited[$cId])) {
                    $rightId = $cId;
                    break;
                }
            }
            if (empty($rightId)) {
                foreach ($underMap[$uid] as $uc) {
                    $cId = (string)$uc['userid'];
                    if ($cId !== $uid && !isset($assignedChildIds[$cId]) && !isset($visited[$cId])) {
                        $rightId = $cId;
                        break;
                    }
                }
            }
        }
        if (!empty($rightId) && !isset($visited[$rightId]) && !isset($assignedChildIds[$rightId])) {
            $children[] = ['id' => $rightId, 'side' => 'RIGHT'];
            $assignedChildIds[$rightId] = true;
        }

        // 3. Additional placement children (where underuserid = uid)
        if (isset($underMap[$uid])) {
            foreach ($underMap[$uid] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $uid && !isset($assignedChildIds[$cId]) && !isset($visited[$cId])) {
                    $side = !empty($uc['join_side']) ? strtoupper($uc['join_side']) : 'DOWNLINE';
                    $children[] = ['id' => $cId, 'side' => $side];
                    $assignedChildIds[$cId] = true;
                }
            }
        }

        foreach ($children as $ch) {
            $queue[] = [
                'userid'    => $ch['id'],
                'parent_id' => $uid,
                'position'  => $ch['side'],
                'level'     => $curr['level'] + 1
            ];
        }
    }

    return $results;
}

// Test users
$testUsers = ['540599', '1290', '789260', '224891'];

foreach ($testUsers as $u) {
    echo "========================================\n";
    echo "TESTING USER: {$u}\n";
    echo "========================================\n";
    
    $left = testGetUserTeamMembersDetailedFixed($u, 'LEFT', $pdo);
    $right = testGetUserTeamMembersDetailedFixed($u, 'RIGHT', $pdo);
    
    $leftBus = array_sum(array_column($left, 'investment_usd'));
    $rightBus = array_sum(array_column($right, 'investment_usd'));
    $leftAct = count(array_filter($left, function($m) { return ($m['status'] ?? '') === 'Active'; }));
    $rightAct = count(array_filter($right, function($m) { return ($m['status'] ?? '') === 'Active'; }));
    
    echo "LEFT:  Count=" . count($left) . " | Active=" . $leftAct . " | Business=$" . number_format($leftBus, 2) . "\n";
    echo "RIGHT: Count=" . count($right) . " | Active=" . $rightAct . " | Business=$" . number_format($rightBus, 2) . "\n";
    echo "TOTAL: Count=" . (count($left) + count($right)) . " | Active=" . ($leftAct + $rightAct) . " | Business=$" . number_format($leftBus + $rightBus, 2) . "\n";
}

