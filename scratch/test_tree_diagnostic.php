<?php
session_start();
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");

$rootId = "1290";

// Stage A: DB Direct Referrals
$stmtDirects = $pdo->prepare("SELECT userid, name, sponserid, underuserid, join_side FROM user WHERE sponserid = :rootId");
$stmtDirects->execute([':rootId' => $rootId]);
$dbDirectRows = $stmtDirects->fetchAll(PDO::FETCH_ASSOC);
$dbDirectIds = array_map('strval', array_column($dbDirectRows, 'userid'));
$dbDirectCount = count($dbDirectIds);

// Execute tree building logic directly to avoid script termination via exit;
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

function calcBranchStatsFast($startNodeId, &$globalUserMap, &$globalInvMap, $visited = []) {
    if (empty($startNodeId) || !isset($globalUserMap[$startNodeId]) || isset($visited[$startNodeId])) {
        return ['count' => 0, 'business_usd' => 0.0];
    }
    $visited[$startNodeId] = true;
    $u = $globalUserMap[$startNodeId];
    $count = 1;
    $business = (float)($globalInvMap[$startNodeId] ?? 0);
    $cList = [];
    if (!empty($u['left_id'])) $cList[] = (string)$u['left_id'];
    if (!empty($u['right_id'])) $cList[] = (string)$u['right_id'];
    foreach ($cList as $cId) {
        $sub = calcBranchStatsFast($cId, $globalUserMap, $globalInvMap, $visited);
        $count += $sub['count'];
        $business += $sub['business_usd'];
    }
    return ['count' => $count, 'business_usd' => $business];
}

function fetch_horizontal_binary_tree_diag($nodeId, $currentDepth = 1, $maxDepth = 50, $visitedPath = [], &$globalRenderedUsers = [], $rootUserId = '') {
    global $globalUserMap, $globalInvMap, $underUserChildrenMap, $sponsorChildrenMap, $hasPlacementParent;

    $nodeId = (string)$nodeId;
    if (empty($nodeId) || !isset($globalUserMap[$nodeId])) return null;

    if (isset($visitedPath[$nodeId])) return null;
    $visitedPath[$nodeId] = true;

    if (isset($globalRenderedUsers[$nodeId])) return null;
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
                $rightId = $cId;
                break;
            }
        }
        if (empty($rightId)) {
            foreach ($underUserChildrenMap[$nodeId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
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
                if (!empty($hasPlacementParent[$cId]) && $hasPlacementParent[$cId] !== $nodeId && isset($globalUserMap[$hasPlacementParent[$cId]])) {
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

// Stage B & C: API Nodes parsing
$apiAllNodes = [];
$apiDirectNodesFound = [];
$apiNodeCounts = [];

function collectApiNodes($node, &$apiAllNodes, &$apiDirectNodesFound, &$apiNodeCounts, $dbDirectIds) {
    if (!$node) return;
    $id = (string)$node['id'];
    $apiAllNodes[] = $id;
    $apiNodeCounts[$id] = ($apiNodeCounts[$id] ?? 0) + 1;
    
    if (in_array($id, $dbDirectIds, true) || (($node['sponserid'] ?? '') === "1290")) {
        $apiDirectNodesFound[$id] = [
            'id' => $id,
            'name' => $node['name'] ?? '',
            'sponserid' => $node['sponserid'] ?? '',
            'underuserid' => $node['underuserid'] ?? '',
            'position' => $node['position'] ?? 'ROOT',
            'is_direct_to_root' => $node['is_direct_to_root'] ?? false
        ];
    }
    
    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            collectApiNodes($child, $apiAllNodes, $apiDirectNodesFound, $apiNodeCounts, $dbDirectIds);
        }
    }
}

collectApiNodes($apiTreeData, $apiAllNodes, $apiDirectNodesFound, $apiNodeCounts, $dbDirectIds);

$apiDirectCount = count($apiDirectNodesFound);
$frontendReceivedCount = count($apiAllNodes);

// Stage D: Simulate Frontend Hierarchy (D3 descendants)
$frontendRenderedIds = [];
$frontendNodeCounts = [];
$frontendDirectsFound = [];

function simulateD3Hierarchy($node, &$frontendRenderedIds, &$frontendNodeCounts, &$frontendDirectsFound, $dbDirectIds) {
    if (!$node) return;
    $id = (string)$node['id'];
    $frontendRenderedIds[] = $id;
    $frontendNodeCounts[$id] = ($frontendNodeCounts[$id] ?? 0) + 1;
    
    if (in_array($id, $dbDirectIds, true)) {
        $frontendDirectsFound[$id] = true;
    }
    
    if (!empty($node['children'])) {
        foreach ($node["children"] as $child) {
            simulateD3Hierarchy($child, $frontendRenderedIds, $frontendNodeCounts, $frontendDirectsFound, $dbDirectIds);
        }
    }
}

simulateD3Hierarchy($apiTreeData, $frontendRenderedIds, $frontendNodeCounts, $frontendDirectsFound, $dbDirectIds);

$frontendRenderedCount = count($frontendRenderedIds);
$uniqueRenderedCount = count(array_unique($frontendRenderedIds));

$duplicateRenderedIds = [];
foreach ($frontendNodeCounts as $id => $cnt) {
    if ($cnt > 1) {
        $duplicateRenderedIds[] = $id;
    }
}
$duplicateCount = count($duplicateRenderedIds);

$missingDirectIds = array_diff($dbDirectIds, array_keys($frontendDirectsFound));
$missingCount = count($missingDirectIds);

echo "=========================================================\n";
echo "TREE RENDERING & DIRECT REFERRAL DIAGNOSTIC REPORT\n";
echo "=========================================================\n";
echo "ROOT USER ID             : " . $rootId . "\n";
echo "DB DIRECT COUNT          : " . $dbDirectCount . "\n";
echo "API DIRECT COUNT         : " . $apiDirectCount . "\n";
echo "FRONTEND RECEIVED COUNT  : " . $frontendReceivedCount . "\n";
echo "FRONTEND RENDERED COUNT  : " . $frontendRenderedCount . "\n";
echo "UNIQUE RENDERED COUNT    : " . $uniqueRenderedCount . "\n";
echo "MISSING DIRECT IDS COUNT : " . $missingCount . "\n";
echo "DUPLICATE RENDERED COUNT : " . $duplicateCount . "\n\n";

echo "DB DIRECT REFERRAL ID LIST:\n";
echo implode(", ", $dbDirectIds) . "\n\n";

echo "REPRESENTED DIRECT REFERRALS IN TREE:\n";
echo sprintf("%-12s | %-18s | %-18s | %-10s | %-15s\n", "DIRECT ID", "SPONSOR ID", "PLACEMENT PARENT", "POSITION", "DIRECT TO ROOT");
echo str_repeat("-", 80) . "\n";
foreach ($dbDirectIds as $dId) {
    if (isset($apiDirectNodesFound[$dId])) {
        $info = $apiDirectNodesFound[$dId];
        echo sprintf("%-12s | %-18s | %-18s | %-10s | %-15s\n", 
            $dId, 
            $info['sponserid'] ?: 'N/A', 
            $info['underuserid'] ?: 'ROOT', 
            $info['position'], 
            $info['is_direct_to_root'] ? 'YES' : 'NO'
        );
    } else {
        echo sprintf("%-12s | %-18s | %-18s | %-10s | %-15s\n", $dId, 'MISSING', 'MISSING', 'MISSING', 'NO');
    }
}

if ($missingCount > 0) {
    echo "\nMISSING DIRECT IDS:\n" . implode(", ", $missingDirectIds) . "\n";
}
if ($duplicateCount > 0) {
    echo "\nDUPLICATE RENDERED IDS:\n" . implode(", ", $duplicateRenderedIds) . "\n";
}
echo "=========================================================\n";
