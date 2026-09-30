<?php
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");
include_once("common/db_method.php");

$rootId = "1290";

// STEP 1: Query actual database direct referrals for root 1290
$stmtDirects = $pdo->prepare("SELECT userid, name, sponserid, underuserid, join_side FROM user WHERE sponserid = :rootId ORDER BY userid");
$stmtDirects->execute([':rootId' => $rootId]);
$dbDirectRows = $stmtDirects->fetchAll(PDO::FETCH_ASSOC);
$dbDirectIds = array_map('strval', array_column($dbDirectRows, 'userid'));
$dbDirectCount = count($dbDirectIds);

// STEP 2: Load global maps exactly as tree.php does
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

$hasPlacementParent = [];
foreach ($globalUserMap as $uid => $u) {
    if (!empty($u['underuserid']) && isset($globalUserMap[(string)$u['underuserid']])) {
        $hasPlacementParent[$uid] = (string)$u['underuserid'];
    }
}

$stmtAllInv = $pdo->query("SELECT user_id, COALESCE(SUM(real_fund_usd), COALESCE(SUM(package), 0)) as total_usd FROM tbl_roi_one GROUP BY user_id");
$globalInvMap = [];
while ($r = $stmtAllInv->fetch(PDO::FETCH_ASSOC)) {
    $globalInvMap[(string)$r['user_id']] = (float)$r['total_usd'];
}

$currSelection = "USD";

function fetch_horizontal_binary_tree_diag($nodeId, $currentDepth = 1, $maxDepth = 50, $visitedPath = [], &$globalRenderedUsers = [], $rootUserId = '') {
    global $currSelection, $globalUserMap, $globalInvMap, $underUserChildrenMap, $sponsorChildrenMap, $hasPlacementParent;

    $nodeId = (string)$nodeId;
    if (empty($nodeId) || !isset($globalUserMap[$nodeId])) return null;

    if (isset($visitedPath[$nodeId])) return null;
    $visitedPath[$nodeId] = true;

    if ($currentDepth > 2 && isset($globalRenderedUsers[$nodeId])) return null;
    $globalRenderedUsers[$nodeId] = true;

    $user = $globalUserMap[$nodeId];
    if (empty($rootUserId)) $rootUserId = $nodeId;

    $childrenList = [];
    $assignedChildIds = [];

    // 1. LEFT SLOT
    $leftId = (!empty($user['left_id']) && isset($globalUserMap[(string)$user['left_id']])) ? (string)$user['left_id'] : '';
    if (empty($leftId) && isset($underUserChildrenMap[$nodeId])) {
        foreach ($underUserChildrenMap[$nodeId] as $uc) {
            $cId = (string)$uc['userid'];
            if ($cId !== $nodeId && strtolower($uc['join_side'] ?? '') === 'left' && !isset($globalRenderedUsers[$cId])) {
                if ($nodeId !== $rootUserId && !empty($uc['sponserid']) && (string)$uc['sponserid'] === (string)$rootUserId) {
                    continue;
                }
                $leftId = $cId;
                break;
            }
        }
    }
    if (!empty($leftId) && !isset($globalRenderedUsers[$leftId])) {
        $childrenList[] = ['id' => $leftId, 'side' => 'LEFT'];
        $assignedChildIds[$leftId] = true;
    }

    // 2. RIGHT SLOT
    $rightId = (!empty($user['right_id']) && isset($globalUserMap[(string)$user['right_id']])) ? (string)$user['right_id'] : '';
    if (empty($rightId) && isset($underUserChildrenMap[$nodeId])) {
        foreach ($underUserChildrenMap[$nodeId] as $uc) {
            $cId = (string)$uc['userid'];
            if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && strtolower($uc['join_side'] ?? '') === 'right' && !isset($globalRenderedUsers[$cId])) {
                if ($nodeId !== $rootUserId && !empty($uc['sponserid']) && (string)$uc['sponserid'] === (string)$rootUserId) {
                    continue;
                }
                $rightId = $cId;
                break;
            }
        }
        if (empty($rightId)) {
            foreach ($underUserChildrenMap[$nodeId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                    if ($nodeId !== $rootUserId && !empty($uc['sponserid']) && (string)$uc['sponserid'] === (string)$rootUserId) {
                        continue;
                    }
                    $rightId = $cId;
                    break;
                }
            }
        }
    }
    if (!empty($rightId) && !isset($globalRenderedUsers[$rightId]) && !isset($assignedChildIds[$rightId])) {
        $childrenList[] = ['id' => $rightId, 'side' => 'RIGHT'];
        $assignedChildIds[$rightId] = true;
    }

    // 3. Additional placement children
    if (isset($underUserChildrenMap[$nodeId])) {
        foreach ($underUserChildrenMap[$nodeId] as $uc) {
            $cId = (string)$uc['userid'];
            if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                if ($nodeId !== $rootUserId && !empty($uc['sponserid']) && (string)$uc['sponserid'] === (string)$rootUserId) {
                    continue;
                }
                $childrenList[] = ['id' => $cId, 'side' => 'DIRECT'];
                $assignedChildIds[$cId] = true;
            }
        }
    }

    // 4. Direct Sponsor Referrals
    if (isset($sponsorChildrenMap[$nodeId])) {
        foreach ($sponsorChildrenMap[$nodeId] as $sc) {
            $cId = (string)$sc['userid'];
            if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                if ($nodeId !== $rootUserId && !empty($hasPlacementParent[$cId]) && $hasPlacementParent[$cId] !== $nodeId && isset($globalUserMap[$hasPlacementParent[$cId]])) {
                    continue;
                }
                $childrenList[] = ['id' => $cId, 'side' => 'DIRECT'];
                $assignedChildIds[$cId] = true;
            }
        }
    }

    $node = [
        'id'                    => $user['userid'],
        'name'                  => !empty($user['name']) ? $user['name'] : $user['userid'],
        'active'                => ($user['active'] == '1'),
        'status'                => ($user['active'] == '1') ? 'Active' : 'Inactive',
        'sponserid'             => $user['sponserid'] ?? '',
        'sponsor_id'            => $user['sponserid'] ?? '',
        'underuserid'           => $user['underuserid'] ?? '',
        'placement_parent_id'   => $user['underuserid'] ?? '',
        'join_side'             => $user['join_side'] ?? '',
        'is_direct_to_root'     => (!empty($user['sponserid']) && (string)$user['sponserid'] === (string)$rootUserId),
        'children'              => []
    ];

    if ($currentDepth < $maxDepth) {
        if ($nodeId === $rootUserId) {
            foreach ($childrenList as $cItem) {
                $globalRenderedUsers[(string)$cItem['id']] = true;
            }
        }
        foreach ($childrenList as $cItem) {
            $childNode = fetch_horizontal_binary_tree_diag($cItem['id'], $currentDepth + 1, $maxDepth, $visitedPath, $globalRenderedUsers, $rootUserId);
            if ($childNode) {
                $childNode['position'] = $cItem['side'];
                $node['children'][] = $childNode;
            }
        }
    }

    return $node;
}

$globalRenderedUsers = [];
$apiTreeData = fetch_horizontal_binary_tree_diag($rootId, 1, 50, [], $globalRenderedUsers, $rootId);

// SWEEP PASS
if ($apiTreeData && isset($sponsorChildrenMap[$rootId])) {
    foreach ($sponsorChildrenMap[$rootId] as $sc) {
        $dId = (string)$sc['userid'];
        if ($dId !== $rootId && !isset($globalRenderedUsers[$dId])) {
            $directNode = fetch_horizontal_binary_tree_diag($dId, 2, 50, [$rootId => true], $globalRenderedUsers, $rootId);
            if ($directNode) {
                $directNode['position'] = 'DIRECT';
                $apiTreeData['children'][] = $directNode;
            }
        }
    }
}

// STEP 3: Parse API Tree Data recursively
$apiAllNodes = [];
$apiDirectNodesFound = [];
$apiNodeCounts = [];

function collectApiNodesDetailed($node, $parentId, &$apiAllNodes, &$apiDirectNodesFound, &$apiNodeCounts, $dbDirectIds) {
    if (!$node) return;
    $id = (string)$node['id'];
    $apiAllNodes[] = $id;
    $apiNodeCounts[$id] = ($apiNodeCounts[$id] ?? 0) + 1;
    
    if (in_array($id, $dbDirectIds, true) || (($node['sponserid'] ?? '') === "1290")) {
        $apiDirectNodesFound[$id] = [
            'id'                 => $id,
            'name'               => $node['name'] ?? '',
            'sponserid'          => $node['sponserid'] ?? '',
            'db_underuserid'     => $node['underuserid'] ?? '',
            'tree_visual_parent' => $parentId ?: 'ROOT',
            'position'           => $node['position'] ?? 'ROOT',
            'is_direct_to_root'  => $node['is_direct_to_root'] ?? false
        ];
    }
    
    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            collectApiNodesDetailed($child, $id, $apiAllNodes, $apiDirectNodesFound, $apiNodeCounts, $dbDirectIds);
        }
    }
}

collectApiNodesDetailed($apiTreeData, '', $apiAllNodes, $apiDirectNodesFound, $apiNodeCounts, $dbDirectIds);

$apiDirectCount = count($apiDirectNodesFound);
$frontendReceivedCount = count($apiAllNodes);

// STEP 4: Simulate D3 Initial Collapse (Only Root Level 0 + Root's Direct Referrals Level 1 visible initially)
$d3RootChildIds = [];
if (!empty($apiTreeData['children'])) {
    foreach ($apiTreeData['children'] as $l1) {
        $d3RootChildIds[] = (string)$l1['id'];
    }
}

$initialVisibleNodes = array_merge([$rootId], $d3RootChildIds);

$duplicateRenderedIds = [];
foreach ($apiNodeCounts as $id => $cnt) {
    if ($cnt > 1) {
        $duplicateRenderedIds[] = $id;
    }
}
$duplicateCount = count($duplicateRenderedIds);

$missingDirectIds = array_diff($dbDirectIds, array_keys($apiDirectNodesFound));
$missingCount = count($missingDirectIds);

echo "=========================================================================\n";
echo "PRODUCTION DIRECT-REFERRAL VERIFICATION & TREE AUDIT REPORT (ROOT 1290)\n";
echo "=========================================================================\n";
echo "ROOT USER ID                  : " . $rootId . "\n";
echo "DB DIRECT COUNT               : " . $dbDirectCount . "\n";
echo "API DIRECT COUNT              : " . $apiDirectCount . "\n";
echo "FRONTEND RECEIVED COUNT       : " . $frontendReceivedCount . "\n";
echo "D3 ROOT CHILDREN COUNT        : " . count($d3RootChildIds) . " (ALL Direct Referrals)\n";
echo "INITIAL VISIBLE NODES COUNT   : " . count($initialVisibleNodes) . " (Root + ALL Direct Referrals)\n";
echo "MISSING DIRECT IDS COUNT      : " . $missingCount . "\n";
echo "DUPLICATE RENDERED USER IDS   : " . $duplicateCount . "\n\n";

echo "EXACT DB DIRECT REFERRAL LIST:\n";
echo implode(", ", $dbDirectIds) . "\n\n";

echo "D3 ROOT CHILD IDS (LEVEL 1 VISIBLE INITIALLY):\n";
echo implode(", ", $d3RootChildIds) . "\n\n";

echo "INITIAL VISIBLE NODE IDS ON FIRST LOAD:\n";
echo implode(", ", $initialVisibleNodes) . "\n\n";

echo "REPRESENTATION DETAIL FOR EVERY DIRECT REFERRAL:\n";
echo sprintf("%-12s | %-12s | %-18s | %-18s | %-10s | %-12s\n", "DIRECT ID", "SPONSOR ID", "TREE VISUAL PARENT", "DB UNDERUSERID", "POSITION", "DIRECT BADGE");
echo str_repeat("-", 95) . "\n";
foreach ($dbDirectIds as $dId) {
    if (isset($apiDirectNodesFound[$dId])) {
        $info = $apiDirectNodesFound[$dId];
        echo sprintf("%-12s | %-12s | %-18s | %-18s | %-10s | %-12s\n", 
            $dId, 
            $info['sponserid'] ?: 'N/A', 
            $info['tree_visual_parent'], 
            $info['db_underuserid'] ?: 'NONE', 
            $info['position'], 
            $info['is_direct_to_root'] ? 'YES' : 'NO'
        );
    } else {
        echo sprintf("%-12s | %-12s | %-18s | %-18s | %-10s | %-12s\n", $dId, 'MISSING', 'MISSING', 'MISSING', 'MISSING', 'NO');
    }
}

if ($missingCount > 0) {
    echo "\nMISSING DIRECT IDS:\n" . implode(", ", $missingDirectIds) . "\n";
}
if ($duplicateCount > 0) {
    echo "\nDUPLICATE RENDERED IDS:\n" . implode(", ", $duplicateRenderedIds) . "\n";
}
echo "=========================================================================\n";
