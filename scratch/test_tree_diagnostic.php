<?php
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");
include_once("common/db_method.php");

// -------------------------------------------------------------------------
// BROWSER D3 LEVEL RECURSIVE EXPANSION SIMULATION & VERIFICATION SCRIPT
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

// SIMULATE D3 HIERARCHY NODE AND EXPAND / COLLAPSE
class D3NodeSimulation {
    public $data;
    public $children = null;
    public $_children = null;
    public $depth = 0;

    public function __construct($data, $depth = 0) {
        $this->data = $data;
        $this->depth = $depth;
        if (!empty($data['children'])) {
            $this->children = [];
            foreach ($data['children'] as $childData) {
                $this->children[] = new D3NodeSimulation($childData, $depth + 1);
            }
        }
    }

    public function collapseSubtree() {
        if ($this->children !== null) {
            $this->_children = $this->children;
            foreach ($this->_children as $c) {
                $c->collapseSubtree();
            }
            $this->children = null;
        }
    }

    public function expandSingleLevel() {
        if ($this->_children !== null) {
            $this->children = $this->_children;
            $this->_children = null;
            if ($this->children !== null) {
                foreach ($this->children as $c) {
                    $c->collapseSubtree();
                }
            }
        }
    }
}

echo "=========================================================================================================\n";
echo "BROWSER D3 LEVEL RECURSIVE EXPANSION & IMMEDIATE CHILD AUDIT\n";
echo "=========================================================================================================\n";
echo sprintf("%-15s | %-12s | %-12s | %-12s | %-12s | %-12s | %-8s\n",
    "USER ID", "DB DIRECTS", "API CHILDREN", "D3 CHILDREN", "MISSING", "DUPLICATES", "STATUS");
echo str_repeat("-", 95) . "\n";

$testedCount = 0;
$failedCount = 0;

// Test D3 Hierarchy simulation starting from Root 1290
$renderedUsers = [];
$apiData = test_fetch_tree("1290", 1, 50, [], $renderedUsers, '');
$d3Root = new D3NodeSimulation($apiData, 0);

// Initial state: root.children is visible, all children collapsed
if ($d3Root->children !== null) {
    foreach ($d3Root->children as $ch) {
        $ch->collapseSubtree();
    }
}

// Function to recursively inspect each node's expansion state
function auditD3NodeExpansion($node, &$testedCount, &$failedCount, $sponsorChildrenMap) {
    $uid = (string)$node->data['id'];

    // DB direct referral IDs
    $dbDirectIds = [];
    if (isset($sponsorChildrenMap[$uid])) {
        foreach ($sponsorChildrenMap[$uid] as $sc) {
            if ((string)$sc['userid'] !== $uid) {
                $dbDirectIds[] = (string)$sc['userid'];
            }
        }
    }
    $dbDirectCount = count($dbDirectIds);

    // API direct children IDs
    $apiChildren = $node->data['children'] ?? [];
    $apiChildIds = array_map(function($c) { return (string)$c['id']; }, $apiChildren);
    $apiChildCount = count($apiChildIds);

    // Simulate clicking node to expand immediate children
    $node->expandSingleLevel();

    // D3 visible children when expanded
    $d3VisibleChildren = $node->children ?? [];
    $d3VisibleChildIds = array_map(function($c) { return (string)$c->data['id']; }, $d3VisibleChildren);
    $d3ChildCount = count($d3VisibleChildIds);

    // Check missing direct referral IDs in D3 visible children
    $missingIds = array_diff($dbDirectIds, $d3VisibleChildIds);

    // Check duplicates in D3 visible children
    $counts = array_count_values($d3VisibleChildIds);
    $dupIds = [];
    foreach ($counts as $id => $cnt) {
        if ($cnt > 1) $dupIds[] = $id;
    }

    // Verify Grandchildren remain collapsed
    $grandchildrenOpen = 0;
    foreach ($d3VisibleChildren as $cNode) {
        if ($cNode->children !== null) {
            $grandchildrenOpen++;
        }
    }

    $status = (count($missingIds) === 0 && count($dupIds) === 0 && $grandchildrenOpen === 0) ? "PASS" : "FAIL";

    if ($dbDirectCount > 0 || $apiChildCount > 0) {
        $testedCount++;
        if ($status === "FAIL") $failedCount++;

        echo sprintf("%-15s | %-12d | %-12d | %-12d | %-12d | %-12d | %-8s\n",
            $uid,
            $dbDirectCount,
            $apiChildCount,
            $d3ChildCount,
            count($missingIds),
            count($dupIds),
            $status
        );
    }

    // Recurse down to child nodes
    foreach ($d3VisibleChildren as $cNode) {
        auditD3NodeExpansion($cNode, $testedCount, $failedCount, $sponsorChildrenMap);
    }
}

auditD3NodeExpansion($d3Root, $testedCount, $failedCount, $sponsorChildrenMap);

echo "=========================================================================================================\n";
echo sprintf("TOTAL D3 NODES TESTED: %d | PASSED: %d | FAILED: %d\n", $testedCount, $testedCount - $failedCount, $failedCount);
echo "=========================================================================================================\n";
