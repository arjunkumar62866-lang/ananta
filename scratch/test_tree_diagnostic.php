<?php
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");
include_once("common/db_method.php");

// -------------------------------------------------------------------------
// RECURSIVE ALL-USERS COMPREHENSIVE DIRECT REFERRAL & DOWNLINE AUDIT
// -------------------------------------------------------------------------

$stmtAllUsers = $pdo->query("
    SELECT 
        u.userid, u.name, u.active, u.status, u.package, u.sponserid, u.underuserid, u.join_side, u.joining_date, u.mobile, COALESCE(u.amount, 0) as user_amount,
        t.left_id, t.right_id, t.leftcount, t.rightcount, t.lefttotal, t.righttotal
    FROM user u
    LEFT JOIN tree t ON t.userid = u.userid
");
$globalUserMap = [];
$underUserChildrenMap = [];
$sponsorChildrenMap = [];
while ($row = $stmtAllUsers->fetch(PDO::FETCH_ASSOC)) {
    $uid = (string)$row['userid'];
    $globalUserMap[$uid] = $row;
    $pId = !empty($row['underuserid']) ? (string)$row['underuserid'] : '';
    if (!empty($pId)) $underUserChildrenMap[$pId][] = $row;
    $spId = !empty($row['sponserid']) ? (string)$row['sponserid'] : '';
    if (!empty($spId)) $sponsorChildrenMap[$spId][] = $row;
}

try {
    $stmtTblSpon = $pdo->query("SELECT sponsor_id, referral_id FROM tbl_sponsor");
    while ($spRow = $stmtTblSpon->fetch(PDO::FETCH_ASSOC)) {
        $spId = (string)$spRow['sponsor_id'];
        $refId = (string)$spRow['referral_id'];
        if (!empty($spId) && !empty($refId) && isset($globalUserMap[$refId])) {
            $alreadyAdded = false;
            if (isset($sponsorChildrenMap[$spId])) {
                foreach ($sponsorChildrenMap[$spId] as $existingSc) {
                    if ((string)$existingSc['userid'] === $refId) {
                        $alreadyAdded = true;
                        break;
                    }
                }
            }
            if (!$alreadyAdded) {
                $sponsorChildrenMap[$spId][] = $globalUserMap[$refId];
            }
        }
    }
} catch (Exception $e) {}

$stmtAllInv = $pdo->query("SELECT user_id, COALESCE(SUM(real_fund_usd), COALESCE(SUM(package), 0)) as total_usd FROM tbl_roi_one GROUP BY user_id");
$globalInvMap = [];
while ($r = $stmtAllInv->fetch(PDO::FETCH_ASSOC)) {
    $globalInvMap[(string)$r['user_id']] = (float)$r['total_usd'];
}

$currSelection = "USD";

function test_fetch_tree($nodeId, $currentDepth = 1, $maxDepth = 50, $visitedPath = [], &$globalRenderedUsers = [], $parentNodeId = '') {
    global $currSelection, $globalUserMap, $globalInvMap, $underUserChildrenMap, $sponsorChildrenMap;

    $nodeId = (string)$nodeId;
    if (empty($nodeId) || !isset($globalUserMap[$nodeId])) return null;

    if (isset($visitedPath[$nodeId])) return null;
    $visitedPath[$nodeId] = true;

    $globalRenderedUsers[$nodeId] = true;
    $user = $globalUserMap[$nodeId];

    $canClaimPlacementChild = function($cId) use ($nodeId, $globalUserMap) {
        if (!isset($globalUserMap[$cId])) return false;
        $sp = (string)($globalUserMap[$cId]['sponserid'] ?? '');
        if ($sp === $nodeId) return true;
        if (empty($sp) || !isset($globalUserMap[$sp])) return true;
        return false;
    };

    $childrenList = [];
    $assignedChildIds = [];

    // 1. LEFT SLOT
    $leftId = (!empty($user['left_id']) && isset($globalUserMap[(string)$user['left_id']])) ? (string)$user['left_id'] : '';
    if (empty($leftId) && isset($underUserChildrenMap[$nodeId])) {
        foreach ($underUserChildrenMap[$nodeId] as $uc) {
            $cId = (string)$uc['userid'];
            if ($cId !== $nodeId && strtolower($uc['join_side'] ?? '') === 'left' && !isset($globalRenderedUsers[$cId])) {
                if ($canClaimPlacementChild($cId)) {
                    $leftId = $cId;
                    break;
                }
            }
        }
    }
    if (!empty($leftId) && !isset($globalRenderedUsers[$leftId])) {
        if ($canClaimPlacementChild($leftId)) {
            $childrenList[] = ['id' => $leftId, 'side' => 'LEFT'];
            $assignedChildIds[$leftId] = true;
        }
    }

    // 2. RIGHT SLOT
    $rightId = (!empty($user['right_id']) && isset($globalUserMap[(string)$user['right_id']])) ? (string)$user['right_id'] : '';
    if (empty($rightId) && isset($underUserChildrenMap[$nodeId])) {
        foreach ($underUserChildrenMap[$nodeId] as $uc) {
            $cId = (string)$uc['userid'];
            if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && strtolower($uc['join_side'] ?? '') === 'right' && !isset($globalRenderedUsers[$cId])) {
                if ($canClaimPlacementChild($cId)) {
                    $rightId = $cId;
                    break;
                }
            }
        }
        if (empty($rightId)) {
            foreach ($underUserChildrenMap[$nodeId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                    if ($canClaimPlacementChild($cId)) {
                        $rightId = $cId;
                        break;
                    }
                }
            }
        }
    }
    if (!empty($rightId) && !isset($globalRenderedUsers[$rightId]) && !isset($assignedChildIds[$rightId])) {
        if ($canClaimPlacementChild($rightId)) {
            $childrenList[] = ['id' => $rightId, 'side' => 'RIGHT'];
            $assignedChildIds[$rightId] = true;
        }
    }

    // 3. Additional placement children
    if (isset($underUserChildrenMap[$nodeId])) {
        foreach ($underUserChildrenMap[$nodeId] as $uc) {
            $cId = (string)$uc['userid'];
            if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                if ($canClaimPlacementChild($cId)) {
                    $childrenList[] = ['id' => $cId, 'side' => !empty($uc['join_side']) ? strtoupper($uc['join_side']) : 'DIRECT'];
                    $assignedChildIds[$cId] = true;
                }
            }
        }
    }

    // 4. ALL Direct Sponsor Referrals
    if (isset($sponsorChildrenMap[$nodeId])) {
        foreach ($sponsorChildrenMap[$nodeId] as $sc) {
            $cId = (string)$sc['userid'];
            if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                $childrenList[] = ['id' => $cId, 'side' => 'DIRECT'];
                $assignedChildIds[$cId] = true;
            }
        }
    }

    $isDirectToParent = (!empty($user['sponserid']) && !empty($parentNodeId) && (string)$user['sponserid'] === (string)$parentNodeId);

    $node = [
        'id'                 => $user['userid'],
        'name'               => !empty($user['name']) ? $user['name'] : $user['userid'],
        'sponserid'          => $user['sponserid'] ?? '',
        'underuserid'        => $user['underuserid'] ?? '',
        'is_direct_referral' => $isDirectToParent,
        'children'           => []
    ];

    if ($currentDepth < $maxDepth) {
        foreach ($childrenList as $cItem) {
            $globalRenderedUsers[(string)$cItem['id']] = true;
        }

        foreach ($childrenList as $cItem) {
            $childNode = test_fetch_tree($cItem['id'], $currentDepth + 1, $maxDepth, $visitedPath, $globalRenderedUsers, $nodeId);
            if ($childNode) {
                $childNode['position'] = $cItem['side'];
                $node['children'][] = $childNode;
            }
        }
    }

    return $node;
}

echo "=========================================================================\n";
echo "DATABASE-WIDE AUDIT FOR ALL USERS WITH DIRECT REFERRALS\n";
echo "=========================================================================\n";

$failedCount = 0;
$testedCount = 0;

foreach ($globalUserMap as $uid => $uData) {
    $dbDirectIds = [];
    if (isset($sponsorChildrenMap[$uid])) {
        foreach ($sponsorChildrenMap[$uid] as $sc) {
            if ((string)$sc['userid'] !== (string)$uid) {
                $dbDirectIds[] = (string)$sc['userid'];
            }
        }
    }
    
    if (count($dbDirectIds) === 0) continue; // Skip users with 0 directs

    $testedCount++;
    $renderedUsers = [];
    $userTree = test_fetch_tree($uid, 1, 50, [], $renderedUsers, '');

    $immediateChildIds = array_map(function($c) { return (string)$c['id']; }, $userTree['children']);
    
    // Check missing direct referrals
    $missingDirects = array_diff($dbDirectIds, $immediateChildIds);

    // Check duplicates in tree
    $allNodeIds = [];
    $dupMap = [];
    $collect = function($n) use (&$collect, &$allNodeIds, &$dupMap) {
        if (!$n) return;
        $id = (string)$n['id'];
        $allNodeIds[] = $id;
        $dupMap[$id] = ($dupMap[$id] ?? 0) + 1;
        foreach ($n['children'] as $ch) $collect($ch);
    };
    $collect($userTree);

    $dups = [];
    foreach ($dupMap as $id => $cnt) {
        if ($cnt > 1) $dups[] = $id;
    }

    $status = (count($missingDirects) === 0 && count($dups) === 0) ? "PASS" : "FAIL";
    if ($status === "FAIL") $failedCount++;

    echo sprintf("USER %-12s | DB DIRECTS: %-3d | TREE DIRECTS: %-3d | MISSING: %-3d | DUPLICATES: %-3d | STATUS: %s\n",
        $uid,
        count($dbDirectIds),
        count($immediateChildIds),
        count($missingDirects),
        count($dups),
        $status
    );

    if (count($missingDirects) > 0) {
        echo "  --> MISSING: " . implode(", ", $missingDirects) . "\n";
    }
    if (count($dups) > 0) {
        echo "  --> DUPLICATES: " . implode(", ", $dups) . "\n";
    }
}

echo "=========================================================================\n";
echo sprintf("TOTAL TESTED SPONSOR USERS: %d | PASSED: %d | FAILED: %d\n", $testedCount, $testedCount - $failedCount, $failedCount);
echo "=========================================================================\n";
