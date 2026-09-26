<?php
ob_start();
session_start();
include("common/connection.php");

// -------------------------------------------------------------
// 1. DYNAMIC API ENDPOINT FOR HORIZONTAL TREE DATA (AJAX)
// -------------------------------------------------------------
if (isset($_GET['api']) && $_GET['api'] === 'get_tree') {
    header('Content-Type: application/json');

    $sessionUserid = $_SESSION['userid'] ?? $_SESSION['user_id'] ?? '';
    $reqNodeId = !empty($_GET['node_id']) ? trim($_GET['node_id']) : $sessionUserid;

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

    // Recursive function to fetch real user tree nodes (no empty placeholders)
    function fetch_horizontal_binary_tree($nodeId, $currentDepth = 1, $maxDepth = 6) {
        global $pdo;

        if (empty($nodeId)) return null;

        // Fetch User Info
        $uStmt = $pdo->prepare("SELECT userid, name, active, status, package, sponserid, joining_date, mobile FROM user WHERE userid = :id LIMIT 1");
        $uStmt->execute([':id' => $nodeId]);
        $user = $uStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return null;

        // Fetch Tree Placement
        $tStmt = $pdo->prepare("SELECT left_id, right_id, leftcount, rightcount, lefttotal, righttotal FROM tree WHERE userid = :id LIMIT 1");
        $tStmt->execute([':id' => $nodeId]);
        $tree = $tStmt->fetch(PDO::FETCH_ASSOC) ?: [
            'left_id' => '', 'right_id' => '',
            'leftcount' => 0, 'rightcount' => 0,
            'lefttotal' => 0, 'righttotal' => 0
        ];

        $node = [
            'id'           => $user['userid'],
            'name'         => !empty($user['name']) ? $user['name'] : $user['userid'],
            'active'       => ($user['active'] == '1'),
            'status'       => ($user['active'] == '1') ? 'Active' : 'Inactive',
            'sponserid'    => $user['sponserid'] ?? '',
            'joining_date' => $user['joining_date'] ?? '',
            'mobile'       => $user['mobile'] ?? '',
            'leftcount'    => intval($tree['leftcount']),
            'rightcount'   => intval($tree['rightcount']),
            'lefttotal'    => floatval($tree['lefttotal']),
            'righttotal'   => floatval($tree['righttotal']),
            'children'     => []
        ];

        // Gather real children IDs
        $childrenList = [];

        // 1. From tree table (left_id and right_id)
        if (!empty($tree['left_id'])) {
            $childrenList[] = ['id' => $tree['left_id'], 'side' => 'LEFT'];
        }
        if (!empty($tree['right_id'])) {
            $childrenList[] = ['id' => $tree['right_id'], 'side' => 'RIGHT'];
        }

        // 2. Also check user table for downlines under this user
        $uChildStmt = $pdo->prepare("SELECT userid, join_side FROM user WHERE underuserid = :id OR (sponserid = :id AND (underuserid IS NULL OR underuserid = '' OR underuserid = :id))");
        $uChildStmt->execute([':id' => $nodeId]);
        $uChildren = $uChildStmt->fetchAll(PDO::FETCH_ASSOC);

        $existingChildIds = array_column($childrenList, 'id');
        foreach ($uChildren as $uc) {
            $cId = $uc['userid'];
            if ($cId !== $nodeId && !in_array($cId, $existingChildIds)) {
                $side = !empty($uc['join_side']) ? strtoupper($uc['join_side']) : 'DOWNLINE';
                $childrenList[] = ['id' => $cId, 'side' => $side];
                $existingChildIds[] = $cId;
            }
        }

        // Recurse children if within depth
        if ($currentDepth < $maxDepth) {
            foreach ($childrenList as $cItem) {
                $childNode = fetch_horizontal_binary_tree($cItem['id'], $currentDepth + 1, $maxDepth);
                if ($childNode) {
                    $childNode['position'] = $cItem['side'];
                    $node['children'][] = $childNode;
                }
            }
        }

        return $node;
    }

    $treeStructure = fetch_horizontal_binary_tree($reqNodeId, 1, 6);

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

/* SVG Canvas Wrapper */
#tree-canvas-container {
    width: 100%;
    height: 720px;
    min-height: 600px;
    background: #ffffff;
    position: relative;
    cursor: grab;
    overflow: hidden;
    touch-action: none;
}

#tree-canvas-container:active {
    cursor: grabbing;
}

/* Smooth Horizontal Wire Links */
.tree-link {
    fill: none;
    stroke: #cbd5e1;
    stroke-width: 1.8px;
    transition: all 0.35s ease;
}

.tree-link.link-active {
    stroke: #94a3b8;
}

/* Node Styling */
.tree-node {
    cursor: pointer;
}

.node-circle {
    stroke-width: 2px;
    stroke: #ffffff;
    transition: transform 0.2s ease, r 0.2s ease;
}

.node-circle.active-node {
    fill: #22c55e; /* Green for Active */
}

.node-circle.inactive-node {
    fill: #ef4444; /* Red for Inactive */
}

.tree-node:hover .node-circle {
    transform: scale(1.3);
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
    width: 240px;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.22);
    pointer-events: none;
    display: none;
    font-size: 12.5px;
    color: #0f172a;
}

.tree-popover-tooltip strong {
    color: #0f172a;
    font-size: 14px;
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
                                Horizontal Collapsible Tree. Click any node to Expand/Collapse downline. Green = Active, Red = Inactive.
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
                                <button type="button" id="btn-zoom-reset" class="btn-tree-ctrl" title="Reset View">
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
     3. JAVASCRIPT HORIZONTAL BEZIER BINARY TREE ENGINE
------------------------------------------------------------- -->
<script>
(function() {
    'use strict';

    const rootUserId = "<?php echo htmlspecialchars($search); ?>";
    const container = document.getElementById('tree-canvas-container');
    const tooltip = document.getElementById('tree-tooltip');

    let svg, gCanvas, zoomBehavior;
    let rootNode;
    let i = 0;

    // Load Data
    function loadTreeData(searchId) {
        fetch(`tree.php?api=get_tree&node_id=${encodeURIComponent(searchId)}`)
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

        svg = d3.select('#tree-canvas-container')
            .append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('viewBox', `0 0 ${width} ${height}`);

        // Pan & Zoom Behavior (Mousewheel, drag, touch pinch zoom)
        zoomBehavior = d3.zoom()
            .scaleExtent([0.25, 3.0])
            .on('zoom', (event) => {
                gCanvas.attr('transform', event.transform);
            });

        svg.call(zoomBehavior).on("dblclick.zoom", null);

        gCanvas = svg.append('g')
            .attr('class', 'tree-viewport');

        rootNode = d3.hierarchy(data, d => d.children);
        rootNode.x0 = height / 2;
        rootNode.y0 = 80;

        // Collapse nodes after first 2 levels initially
        if (rootNode.children) {
            rootNode.children.forEach(collapseSubtree);
        }

        // Center Initial Position (Root on left, extending to right)
        const initialTransform = d3.zoomIdentity
            .translate(80, height / 2 - 20)
            .scale(0.95);

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
            .nodeSize([48, 220]); // Vertical spacing = 48, Horizontal depth = 220

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
            .attr('r', 6);

        // Toggle Sign (+ / -) above node
        nodeEnter.append('text')
            .attr('class', 'node-toggle-sign')
            .attr('dy', -9)
            .attr('dx', -3)
            .text(d => (d.children || d._children) ? (d.children ? '-' : '+') : '');

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

        // Click Event (Toggle Expand / Collapse)
        nodeEnter.on('click', (event, d) => {
            event.stopPropagation();
            if (d.children) {
                d._children = d.children;
                d.children = null;
            } else if (d._children) {
                d.children = d._children;
                d._children = null;
            }
            updateTree(d);
        });

        // Hover Tooltip Handlers
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
            .text(d => (d.children || d._children) ? (d.children ? '-' : '+') : '');

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
            <span>Joining: ${data.joining_date || 'N/A'}</span><hr style="margin: 6px 0; border-color: #e2e8f0;">
            <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 11.5px;">
                <span>Left: ${data.leftcount}</span>
                <span>Right: ${data.rightcount}</span>
            </div>
        `;

        const bounds = container.getBoundingClientRect();
        const mouseX = event.clientX - bounds.left;
        const mouseY = event.clientY - bounds.top;

        tooltip.style.left = (mouseX + 15) + 'px';
        tooltip.style.top = (mouseY + 15) + 'px';
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
        const height = container.clientHeight || 720;
        const initialTransform = d3.zoomIdentity.translate(80, height / 2 - 20).scale(0.95);
        svg.transition().duration(400).call(zoomBehavior.transform, initialTransform);
    });

    // Start Engine
    loadTreeData(rootUserId);

})();
</script>

</body>
</html>
