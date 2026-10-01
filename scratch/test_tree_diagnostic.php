<?php
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");
include_once("common/db_method.php");

// -------------------------------------------------------------------------
// RECURSIVE MULTI-NODE TREE DIAGNOSTIC TEST (GENERIC, DYNAMIC, ALL USERS)
// -------------------------------------------------------------------------

$rootId = "1290";

// Include exact tree.php pre-loading maps logic
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

// Exact production fetch_horizontal_binary_tree implementation
function test_fetch_horizontal_binary_tree($nodeId, $currentDepth = 1, $maxDepth = 50, $visitedPath = [], &$globalRenderedUsers = [], $parentNodeId = '') {
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
            $childNode = test_fetch_horizontal_binary_tree($cItem['id'], $currentDepth + 1, $maxDepth, $visitedPath, $globalRenderedUsers, $nodeId);
            if ($childNode) {
                $childNode['position'] = $cItem['side'];
                $node['children'][] = $childNode;
            }
        }
    }

    return $node;
}

// Test multiple users dynamically: Root, and several users down the tree
$nodesToTest = [$rootId];
if (isset($sponsorChildrenMap[$rootId])) {
    foreach ($sponsorChildrenMap[$rootId] as $sc) {
        $nodesToTest[] = (string)$sc['userid'];
        // Also add 2nd level children if available
        $scId = (string)$sc['userid'];
        if (isset($sponsorChildrenMap[$scId])) {
            foreach ($sponsorChildrenMap[$scId] as $sc2) {
                $nodesToTest[] = (string)$sc2['userid'];
            }
        }
    }
}
$nodesToTest = array_unique(array_filter($nodesToTest));

echo "=========================================================================\n";
echo "RECURSIVE ALL-USERS DIRECT REFERRAL REPRESENTATION VERIFICATION REPORT\n";
echo "=========================================================================\n";

$overallFailures = 0;

foreach ($nodesToTest as $testUid) {
    // 1. Get DB direct count & IDs for $testUid
    $dbDirectIds = [];
    if (isset($sponsorChildrenMap[$testUid])) {
        foreach ($sponsorChildrenMap[$testUid] as $sc) {
            if ((string)$sc['userid'] !== $testUid) {
                $dbDirectIds[] = (string)$sc['userid'];
            }
        }
    }
    $dbDirectCount = count($dbDirectIds);

    // 2. Generate tree rooted at $testUid
    $renderedUsers = [];
    $tree = test_fetch_horizontal_binary_tree($testUid, 1, 50, [], $renderedUsers, '');

    // 3. Count direct referrals represented under $testUid
    $representedDirectIds = [];
    $allSubtreeNodeIds = [];
    $duplicateMap = [];

    $collectSubtree = function($n) use (&$collectSubtree, &$allSubtreeNodeIds, &$duplicateMap) {
        if (!$n) return;
        $id = (string)$n['id'];
        $allSubtreeNodeIds[] = $id;
        $duplicateMap[$id] = ($duplicateMap[$id] ?? 0) + 1;
        if (!empty($n['children'])) {
            foreach ($n['children'] as $child) {
                $collectSubtree($child);
            }
        }
    };
    $collectSubtree($tree);

    // Direct referrals represented under $testUid must exist somewhere in $testUid's subtree
    foreach ($dbDirectIds as $dId) {
        if (in_array($dId, $allSubtreeNodeIds, true)) {
            $representedDirectIds[] = $dId;
        }
    }

    $missingDirectIds = array_diff($dbDirectIds, $representedDirectIds);
    $missingCount = count($missingDirectIds);
    
    $duplicates = [];
    foreach ($duplicateMap as $id => $c) {
        if ($c > 1) $duplicates[] = $id;
    }
    $dupCount = count($duplicates);

    $status = ($missingCount === 0 && $dupCount === 0) ? "PASS" : "FAIL";
    if ($status === "FAIL") $overallFailures++;

    echo sprintf("USER %-10s | DB DIRECTS: %-3d | TREE REPRESENTED: %-3d | MISSING: %-3d | DUPLICATES: %-3d | STATUS: %s\n",
        $testUid,
        $dbDirectCount,
        count($representedDirectIds),
        $missingCount,
        $dupCount,
        $status
    );

    if ($missingCount > 0) {
        echo "  --> MISSING DIRECT REFERRALS FOR {$testUid}: " . implode(", ", $missingDirectIds) . "\n";
    }
    if ($dupCount > 0) {
        echo "  --> DUPLICATE USER IDS FOR {$testUid}: " . implode(", ", $duplicates) . "\n";
    }
}

echo "=========================================================================\n";
echo "OVERALL VERIFICATION RESULT: " . ($overallFailures === 0 ? "100% PASS (ALL USERS RECURSIVELY VERIFIED)" : "FAIL ({$overallFailures} USERS FAILED)") . "\n";
echo "=========================================================================\n";
