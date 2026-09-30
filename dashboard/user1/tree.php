<?php
ob_start();
session_start();
include("common/connection.php");
include("common/db_method.php");

// -------------------------------------------------------------
// 1. DYNAMIC API ENDPOINT FOR UNLIMITED HORIZONTAL TREE DATA (AJAX)
// -------------------------------------------------------------
if (isset($_GET['api']) && $_GET['api'] === 'get_tree') {
    header('Content-Type: application/json');

    $sessionUserid = $_SESSION['userid'] ?? $_SESSION['user_id'] ?? '';
    $reqNodeId = !empty($_GET['node_id']) ? trim($_GET['node_id']) : $sessionUserid;
    $reqDepth = isset($_GET['depth']) ? max(1, min(50, intval($_GET['depth']))) : 10;
    $currSelection = getUserCurrency();

    if (empty($reqNodeId)) {
        echo json_encode(['status' => 'error', 'message' => 'No User ID specified']);
        exit;
    }

    // Verify User Exists
    $stmt = $pdo->prepare("SELECT userid, name, active, status, package, sponserid, joining_date, mobile FROM user WHERE userid = :uid LIMIT 1");
    $stmt->execute([':uid' => $reqNodeId]);
    $rootUserData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rootUserData) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not found in database']);
        exit;
    }

    // Pre-load all users, tree placement, and investments into memory maps for ultra-fast, zero-timeout execution
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
        if (!empty($pId)) {
            $underUserChildrenMap[$pId][] = $row;
        }

        $spId = !empty($row['sponserid']) ? (string)$row['sponserid'] : '';
        if (!empty($spId)) {
            $sponsorChildrenMap[$spId][] = $row;
        }
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
        if ($business <= 0 && (float)($u['user_amount'] ?? 0) > 0) {
            $business = parseInputToUSD((float)$u['user_amount'], 'INR');
        }

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

    function fetch_horizontal_binary_tree($nodeId, $currentDepth = 1, $maxDepth = 10, $visitedPath = [], &$globalRenderedUsers = []) {
        global $currSelection, $globalUserMap, $globalInvMap, $underUserChildrenMap, $sponsorChildrenMap, $hasPlacementParent;

        $nodeId = (string)$nodeId;
        if (empty($nodeId) || !isset($globalUserMap[$nodeId])) return null;

        // 1. Ancestor Path Cycle Detection (per branch)
        if (isset($visitedPath[$nodeId])) return null;
        $visitedPath[$nodeId] = true;

        // 2. Global Duplicate Prevention (a user can be rendered only once across the entire tree)
        if (isset($globalRenderedUsers[$nodeId])) return null;
        $globalRenderedUsers[$nodeId] = true;

        $user = $globalUserMap[$nodeId];

        // Left Branch Stats
        $leftStats = ['count' => 0, 'business_usd' => 0.0];
        if (!empty($user['left_id'])) {
            $leftStats = calcBranchStatsFast((string)$user['left_id'], $globalUserMap, $globalInvMap);
        }
        $leftCount = max(intval($user['leftcount'] ?? 0), $leftStats['count']);
        $leftBusiness = $leftStats['business_usd'];
        if ($leftBusiness <= 0 && floatval($user['lefttotal'] ?? 0) > 0) {
            $leftBusiness = parseInputToUSD(floatval($user['lefttotal']), 'INR');
        }

        // Right Branch Stats
        $rightStats = ['count' => 0, 'business_usd' => 0.0];
        if (!empty($user['right_id'])) {
            $rightStats = calcBranchStatsFast((string)$user['right_id'], $globalUserMap, $globalInvMap);
        }
        $rightCount = max(intval($user['rightcount'] ?? 0), $rightStats['count']);
        $rightBusiness = $rightStats['business_usd'];
        if ($rightBusiness <= 0 && floatval($user['righttotal'] ?? 0) > 0) {
            $rightBusiness = parseInputToUSD(floatval($user['righttotal']), 'INR');
        }

        $personalBusiness = (float)($globalInvMap[$nodeId] ?? 0);
        if ($personalBusiness <= 0 && floatval($user['user_amount'] ?? 0) > 0) {
            $personalBusiness = parseInputToUSD(floatval($user['user_amount']), 'INR');
        }

        $totalBusiness = $personalBusiness + $leftBusiness + $rightBusiness;

        // Gather Children (Binary Placement Left & Right + Direct Referrals with Zero Duplicates)
        $childrenList = [];
        $assignedChildIds = [];

        // 1. LEFT SLOT (At most 1)
        $leftId = (!empty($user['left_id']) && isset($globalUserMap[(string)$user['left_id']])) 
            ? (string)$user['left_id'] 
            : '';
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

        // 2. RIGHT SLOT (At most 1)
        $rightId = (!empty($user['right_id']) && isset($globalUserMap[(string)$user['right_id']])) 
            ? (string)$user['right_id'] 
            : '';
        if (empty($rightId) && isset($underUserChildrenMap[$nodeId])) {
            // First try join_side = right
            foreach ($underUserChildrenMap[$nodeId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && strtolower($uc['join_side'] ?? '') === 'right' && !isset($globalRenderedUsers[$cId])) {
                    $rightId = $cId;
                    break;
                }
            }
            // If right slot still empty, take next unassigned placement child with underuserid = nodeId
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

        // 3. Additional placement children (if parent has >2 users with underuserid = nodeId)
        if (isset($underUserChildrenMap[$nodeId])) {
            foreach ($underUserChildrenMap[$nodeId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                    $childrenList[] = ['id' => $cId, 'side' => 'DIRECT'];
                    $assignedChildIds[$cId] = true;
                }
            }
        }

        // 4. Direct Sponsor Referrals (where sponserid = nodeId)
        // If a direct referral has a valid placement parent elsewhere in the tree, let them be rendered under their actual placement parent to maintain genuine binary placement.
        if (isset($sponsorChildrenMap[$nodeId])) {
            foreach ($sponsorChildrenMap[$nodeId] as $sc) {
                $cId = (string)$sc['userid'];
                if ($cId !== $nodeId && !isset($assignedChildIds[$cId]) && !isset($globalRenderedUsers[$cId])) {
                    if (!empty($hasPlacementParent[$cId]) && $hasPlacementParent[$cId] !== $nodeId) {
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
            'joining_date'          => $user['joining_date'] ?? '',
            'mobile'                => $user['mobile'] ?? '',
            'leftcount'             => $leftCount,
            'rightcount'            => $rightCount,
            'personal_business_fmt' => formatCurrency($personalBusiness, $currSelection),
            'left_business_fmt'     => formatCurrency($leftBusiness, $currSelection),
            'right_business_fmt'    => formatCurrency($rightBusiness, $currSelection),
            'total_business_fmt'    => formatCurrency($totalBusiness, $currSelection),
            'has_children_db'       => count($childrenList) > 0,
            'children'              => []
        ];

        if ($currentDepth < $maxDepth) {
            foreach ($childrenList as $cItem) {
                $childNode = fetch_horizontal_binary_tree($cItem['id'], $currentDepth + 1, $maxDepth, $visitedPath, $globalRenderedUsers);
                if ($childNode) {
                    $childNode['position'] = $cItem['side'];
                    $node['children'][] = $childNode;
                }
            }
        }

        return $node;
    }

    $globalRenderedUsers = [];
    $treeStructure = fetch_horizontal_binary_tree($reqNodeId, 1, $reqDepth, [], $globalRenderedUsers);

    echo json_encode([
        'status' => 'success',
        'data'   => $treeStructure
    ]);
    exit;
}

// -------------------------------------------------------------
// 2. MAIN PAGE RENDERING
// -------------------------------------------------------------
include 'common/header.php';
date_default_timezone_set('Asia/Kolkata');

$search = $userid;
if (isset($_GET['search-id']) && !empty(trim($_GET['search-id']))) {
    $search_id = trim($_GET['search-id']);
    $stmt = $pdo->prepare("SELECT userid FROM user WHERE userid = :userid LIMIT 1");
    $stmt->execute([':userid' => $search_id]);

    if ($stmt->rowCount() > 0) {
        $search = $search_id;
    } else {
        echo "<script>alert('User ID not found in database');window.location.assign('tree.php');</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title>Binary Tree View - ANANTA</title>

<!-- Include D3.js v7 for Interactive Vector Tree & Smooth Zoom/Pan -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>

<style>
body.bg-theme {
    background-color: #f8fafc !important;
}

.tree-card-wrapper {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    position: relative;
}

/* Control Toolbar Styling */
.tree-toolbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.tree-controls-btn-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-tree-ctrl {
    background: #f1f5f9;
    color: #0f172a !important;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.btn-tree-ctrl:hover {
    background: #0284c7;
    color: #ffffff !important;
    border-color: #0284c7;
    transform: translateY(-1px);
}

/* SVG Canvas Wrapper - Catch-All Drag & Pan Surface for Mobile & Desktop */
#tree-canvas-container,
#tree-canvas-container svg,
#tree-canvas-container rect {
    width: 100%;
    height: 720px;
    min-height: 600px;
    background: #ffffff;
    position: relative;
    cursor: grab;
    overflow: hidden;
    touch-action: none !important;
    user-select: none !important;
    -webkit-user-select: none !important;
    -webkit-touch-callout: none !important;
}

#tree-canvas-container:active {
    cursor: grabbing;
}

/* Smooth Horizontal Wire Links */
.tree-link {
    fill: none;
    stroke: #cbd5e1;
    stroke-width: 1.8px;
    transition: stroke 0.35s ease, stroke-width 0.35s ease;
}

.tree-node {
    cursor: pointer;
}

.node-circle {
    stroke-width: 2px;
    stroke: #ffffff;
    transition: transform 0.2s ease, r 0.2s ease;
}

.node-circle.active-node {
    fill: #22c55e; /* Bright Green */
}

.node-circle.inactive-node {
    fill: #ef4444; /* Bright Red */
}

.tree-node:hover .node-circle {
    transform: scale(1.35);
}

.node-name-text {
    font-size: 13px;
    font-weight: 800;
    fill: #000000;
    font-family: system-ui, -apple-system, sans-serif;
}

.node-id-text {
    font-size: 11.5px;
    font-weight: 600;
    fill: #64748b;
    font-family: system-ui, -apple-system, sans-serif;
}

.node-toggle-sign {
    font-size: 11px;
    font-weight: 900;
    fill: #475569;
    user-select: none;
}

/* Hover Detail Tooltip */
.tree-popover-tooltip {
    position: absolute;
    z-index: 99999;
    background: #ffffff;
    border: 2px solid #0284c7;
    border-radius: 14px;
    padding: 14px 16px;
    width: 260px;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.22);
    pointer-events: none;
    display: none;
    font-size: 12.5px;
    color: #0f172a;
    line-height: 1.45;
}

.tree-popover-tooltip strong {
    color: #0f172a;
    font-size: 14.5px;
    display: block;
    margin-bottom: 4px;
}
</style>
</head>

<body class="bg-theme bg-theme1">
<div id="wrapper">
  <div class="content-wrapper py-4" style="background-color: #f8fafc !important;">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="tree-card-wrapper">
                    
                    <!-- Header Toolbar -->
                    <div class="tree-toolbar">
                        <div>
                            <h4 class="font-weight-bold mb-1" style="color: #0f172a !important;">
                                <i class="fa fa-sitemap mr-2" style="color: #0284c7;"></i> Binary Tree View
                            </h4>
                            <p class="mb-0 small" style="color: #64748b !important; font-weight: 600;">
                                Mobile & Desktop 360° Touch Drag/Pan Canvas. Click nodes to Expand/Collapse. Green = Active, Red = Inactive.
                            </p>
                        </div>

                        <!-- Controls & Search -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <!-- Search Input -->
                            <form id="tree-search-form" action="tree.php" method="GET" class="d-flex align-items-center">
                                <div class="input-group" style="width: 240px;">
                                    <input type="text" name="search-id" class="form-control" placeholder="Search User ID..." value="<?php echo htmlspecialchars($search); ?>" required style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1; font-weight: 700; color: #0f172a;">
                                    <button type="submit" class="btn" style="background: #0f172a; color: #ffffff !important; border-radius: 0 10px 10px 0; font-weight: 700;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </form>

                            <!-- Control Buttons -->
                            <div class="tree-controls-btn-group">
                                <button type="button" id="btn-zoom-in" class="btn-tree-ctrl" title="Zoom In (+)">
                                    <i class="fa fa-search-plus"></i> +
                                </button>
                                <button type="button" id="btn-zoom-out" class="btn-tree-ctrl" title="Zoom Out (-)">
                                    <i class="fa fa-search-minus"></i> -
                                </button>
                                <button type="button" id="btn-zoom-reset" class="btn-tree-ctrl" title="Reset Center">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <?php if (strtoupper($search) !== strtoupper($userid)): ?>
                                    <a href="tree.php" class="btn-tree-ctrl" style="background: #0284c7; color: #ffffff !important; border-color: #0284c7;">
                                        <i class="fa fa-home"></i> My Root
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tree Container Canvas -->
                    <div id="tree-canvas-container">
                        <!-- SVG Element Injected via JavaScript -->
                    </div>

                    <!-- Detail Popover Tooltip -->
                    <div id="tree-tooltip" class="tree-popover-tooltip"></div>

                </div>
            </div>
        </div>

    </div>
  </div>
</div>

<?php include 'common/footer.php'; ?>

<!-- -------------------------------------------------------------
     3. JAVASCRIPT MOBILE TOUCH & DESKTOP SLIDING BEZIER TREE ENGINE
------------------------------------------------------------- -->
<script>
(function() {
    'use strict';

    const rootUserId = "<?php echo htmlspecialchars($search); ?>";
    const container = document.getElementById('tree-canvas-container');
    const tooltip = document.getElementById('tree-tooltip');

    let svg, gCanvas, zoomBehavior;
    let rootNode;

    // Prevent default mobile touch scrolling over tree canvas to allow free 1-finger sliding
    container.addEventListener('touchstart', function(e) {
        if (e.touches.length === 1 || e.touches.length === 2) {
            e.stopPropagation();
        }
    }, { passive: false });

    container.addEventListener('touchmove', function(e) {
        if (e.touches.length === 1 || e.touches.length === 2) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, { passive: false });

    // Load Data
    function loadTreeData(searchId) {
        fetch(`tree.php?api=get_tree&depth=10&node_id=${encodeURIComponent(searchId)}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    initHorizontalD3Tree(res.data);
                } else {
                    alert(res.message || 'Failed to load tree data');
                }
            })
            .catch(err => {
                console.error('Tree fetch error:', err);
                alert('Error loading tree data. Please refresh.');
            });
    }

    function initHorizontalD3Tree(data) {
        container.innerHTML = '';

        const width = container.clientWidth || 1000;
        const height = container.clientHeight || 720;
        const isMobile = (width < 768);

        // 1. Create Main SVG
        svg = d3.select('#tree-canvas-container')
            .append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('viewBox', `0 0 ${width} ${height}`);

        // 2. Full Background Overlay to Capture Mouse & Touch Drag Events Everywhere
        svg.append('rect')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('fill', '#ffffff')
            .attr('pointer-events', 'all');

        // 3. Mobile & Desktop Pan & Zoom Behavior
        zoomBehavior = d3.zoom()
            .scaleExtent([0.12, 3.5])
            .filter(function(event) {
                if (event.type === 'wheel') return true;
                if (event.type === 'touchstart' || event.type === 'touchmove') return true;
                if (event.type === 'mousedown') return event.button === 0;
                return !event.ctrlKey;
            })
            .on('zoom', (event) => {
                gCanvas.attr('transform', event.transform);
            });

        svg.call(zoomBehavior).on("dblclick.zoom", null);

        // 4. Viewport Group for Transformations
        gCanvas = svg.append('g')
            .attr('class', 'tree-viewport');

        rootNode = d3.hierarchy(data, d => d.children);
        rootNode.x0 = height / 2;
        rootNode.y0 = isMobile ? 40 : 100;

        // Clean Step-by-Step View: Only root and its immediate children (depth 1) are open on initial load
        if (rootNode.children) {
            rootNode.children.forEach(collapseSubtree);
        }

        // Center Initial Position according to screen size
        const initialScale = isMobile ? 0.72 : 0.92;
        const initialTranslateX = isMobile ? 35 : 110;
        const initialTranslateY = height / 2 - 20;

        const initialTransform = d3.zoomIdentity
            .translate(initialTranslateX, initialTranslateY)
            .scale(initialScale);

        svg.call(zoomBehavior.transform, initialTransform);

        updateTree(rootNode);
    }

    function collapseSubtree(d) {
        if (d.children) {
            d._children = d.children;
            d._children.forEach(collapseSubtree);
            d.children = null;
        }
    }

    function updateTree(source) {
        const treeLayout = d3.tree()
            .nodeSize([52, 230]); // Vertical spacing = 52, Horizontal depth = 230

        const treeData = treeLayout(rootNode);
        const nodes = treeData.descendants();
        const links = treeData.links();

        // -------------------------------------------------------------
        // A. RENDER HORIZONTAL CURVED BEZIER LINKS
        // -------------------------------------------------------------
        const linkSelection = gCanvas.selectAll('path.tree-link')
            .data(links, d => d.target.data.id);

        linkSelection.enter()
            .append('path')
            .attr('class', 'tree-link')
            .attr('d', d => {
                const o = { x: source.x0, y: source.y0 };
                return generateHorizontalLink({ source: o, target: o });
            })
            .merge(linkSelection)
            .transition()
            .duration(350)
            .attr('d', generateHorizontalLink);

        linkSelection.exit()
            .transition()
            .duration(350)
            .attr('d', d => {
                const o = { x: source.x, y: source.y };
                return generateHorizontalLink({ source: o, target: o });
            })
            .remove();

        // -------------------------------------------------------------
        // B. RENDER NODES (CIRCLE JUNCTION + NAME & ID TEXT)
        // -------------------------------------------------------------
        const nodeSelection = gCanvas.selectAll('g.tree-node')
            .data(nodes, d => d.data.id);

        const nodeEnter = nodeSelection.enter()
            .append('g')
            .attr('class', 'tree-node')
            .attr('transform', d => `translate(${source.y0}, ${source.x0})`);

        // Circle Junction
        nodeEnter.append('circle')
            .attr('class', d => d.data.active ? 'node-circle active-node' : 'node-circle inactive-node')
            .attr('r', 6.5);

        // Toggle Sign (+ / -) above node circle
        nodeEnter.append('text')
            .attr('class', 'node-toggle-sign')
            .attr('dy', -10)
            .attr('dx', -3.5)
            .text(d => (d.children || d._children || d.data.has_children_db) ? (d.children ? '-' : '+') : '');

        // User Name Text
        nodeEnter.append('text')
            .attr('class', 'node-name-text')
            .attr('dx', 14)
            .attr('dy', -2)
            .text(d => d.data.name);

        // User ID & Position Text
        nodeEnter.append('text')
            .attr('class', 'node-id-text')
            .attr('dx', 14)
            .attr('dy', 14)
            .text(d => {
                const pos = d.data.position ? ` (${d.data.position})` : '';
                return `${d.data.id}${pos}`;
            });

        // Click / Tap Event (UNLIMITED EXPANSION & STRICT SINGLE-LEVEL TOGGLE)
        nodeEnter.on('click', (event, d) => {
            event.stopPropagation();
            
            if (d.children) {
                // Collapse this node's branch
                d._children = d.children;
                d.children = null;
                updateTree(d);
            } else if (d._children) {
                // Expand ONLY immediate next level (collapse all sub-children recursively)
                d.children = d._children;
                d._children = null;
                if (d.children) {
                    d.children.forEach(collapseSubtree);
                }
                updateTree(d);
            } else if (d.data.has_children_db) {
                // Fetch deeper downlines dynamically via AJAX for Unlimited Depth
                fetch(`tree.php?api=get_tree&depth=10&node_id=${encodeURIComponent(d.data.id)}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success' && res.data && res.data.children && res.data.children.length > 0) {
                            d.data.children = res.data.children;
                            
                            // Build subHierarchy for target node directly
                            const subHierarchy = d3.hierarchy(res.data, child => child.children);
                            d.children = subHierarchy.children;
                            if (d.children) {
                                function syncNodeDepth(node, parentNode) {
                                    node.parent = parentNode;
                                    node.depth = parentNode.depth + 1;
                                    if (node.children) {
                                        node.children.forEach(ch => syncNodeDepth(ch, node));
                                    }
                                    if (node._children) {
                                        node._children.forEach(ch => syncNodeDepth(ch, node));
                                    }
                                }
                                d.children.forEach(c => {
                                    syncNodeDepth(c, d);
                                    collapseSubtree(c); // Collapse all deeper descendants beyond immediate level
                                });
                            }

                            updateTree(d);
                        } else {
                            d.data.has_children_db = false;
                            updateTree(d);
                        }
                    })
                    .catch(err => {
                        console.error('Failed to load deeper downlines:', err);
                    });
            }
        });

        // Hover / Touch Tooltip Handlers
        nodeEnter.on('mouseover', (event, d) => {
            showTooltip(event, d.data);
        }).on('mouseout', () => {
            hideTooltip();
        });

        // Node Update (Positions)
        const nodeUpdate = nodeEnter.merge(nodeSelection);

        nodeUpdate.transition()
            .duration(350)
            .attr('transform', d => `translate(${d.y}, ${d.x})`);

        nodeUpdate.select('circle')
            .attr('class', d => d.data.active ? 'node-circle active-node' : 'node-circle inactive-node');

        nodeUpdate.select('.node-toggle-sign')
            .text(d => (d.children || d._children || d.data.has_children_db) ? (d.children ? '-' : '+') : '');

        // Node Exit
        nodeSelection.exit()
            .transition()
            .duration(350)
            .attr('transform', d => `translate(${source.y}, ${source.x})`)
            .remove();

        // Stash positions
        nodes.forEach(d => {
            d.x0 = d.x;
            d.y0 = d.y;
        });
    }

    // Horizontal Cubic Bezier Curve Link Generator
    function generateHorizontalLink(d) {
        return d3.linkHorizontal()({
            source: [d.source.y, d.source.x],
            target: [d.target.y, d.target.x]
        });
    }

    // Floating Tooltip Detail
    function showTooltip(event, data) {
        tooltip.innerHTML = `
            <strong>${data.name}</strong>
            <span style="color: #0284c7; font-weight: 700;">User ID: ${data.id}</span><br>
            <span>Sponsor ID: ${data.sponserid || 'N/A'}</span><br>
            <span>Status: <b style="color: ${data.active ? '#22c55e' : '#ef4444'};">${data.active ? 'Active' : 'Inactive'}</b></span><br>
            <span>Joining Date: ${data.joining_date || 'N/A'}</span><hr style="margin: 8px 0; border-color: #cbd5e1;">
            <div style="font-size: 12px; margin-bottom: 4px;">
                <strong>Personal Investment:</strong> <span style="color: #10b981; font-weight: 700;">${data.personal_business_fmt}</span>
            </div>
            <div style="font-size: 12px; margin-bottom: 4px;">
                <strong>Total Team Business:</strong> <span style="color: #0284c7; font-weight: 700;">${data.total_business_fmt}</span>
            </div>
            <hr style="margin: 6px 0; border-color: #e2e8f0;">
            <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 11.5px; color: #334155;">
                <span>Left Team: <b>${data.leftcount} Members</b> (${data.left_business_fmt})</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 11.5px; color: #334155; margin-top: 2px;">
                <span>Right Team: <b>${data.rightcount} Members</b> (${data.right_business_fmt})</span>
            </div>
        `;

        const bounds = container.getBoundingClientRect();
        const mouseX = (event.clientX || (event.touches && event.touches[0] ? event.touches[0].clientX : bounds.left + bounds.width / 2)) - bounds.left;
        const mouseY = (event.clientY || (event.touches && event.touches[0] ? event.touches[0].clientY : bounds.top + bounds.height / 2)) - bounds.top;

        tooltip.style.left = Math.min(bounds.width - 270, Math.max(10, mouseX + 15)) + 'px';
        tooltip.style.top = Math.min(bounds.height - 220, Math.max(10, mouseY + 15)) + 'px';
        tooltip.style.display = 'block';
    }

    function hideTooltip() {
        tooltip.style.display = 'none';
    }

    // Controls Event Listeners
    document.getElementById('btn-zoom-in').addEventListener('click', () => {
        svg.transition().duration(300).call(zoomBehavior.scaleBy, 1.25);
    });

    document.getElementById('btn-zoom-out').addEventListener('click', () => {
        svg.transition().duration(300).call(zoomBehavior.scaleBy, 0.8);
    });

    document.getElementById('btn-zoom-reset').addEventListener('click', () => {
        const width = container.clientWidth || 1000;
        const height = container.clientHeight || 720;
        const isMobile = (width < 768);

        const initialScale = isMobile ? 0.72 : 0.92;
        const initialTranslateX = isMobile ? 35 : 110;
        const initialTranslateY = height / 2 - 20;

        const initialTransform = d3.zoomIdentity
            .translate(initialTranslateX, initialTranslateY)
            .scale(initialScale);
            
        svg.transition().duration(400).call(zoomBehavior.transform, initialTransform);
    });

    // Start Engine
    loadTreeData(rootUserId);

})();
</script>

</body>
</html>
