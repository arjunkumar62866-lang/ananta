<?php
$isCli = (php_sapi_name() === "cli");
$key = $_GET["key"] ?? ($argv[1] ?? "");
if (!$isCli && $key !== "aX9zK7mQ") { http_response_code(403); die("Forbidden\n"); }
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

global $pdo;

echo "=== TRACING 540599 SUBTREE NODES ===\n";

// 1. Get all nodes when starting from 540599 as root of getRootBranchTreeDetailed
$allFrom540 = getRootBranchTreeDetailed('540599', $pdo, 'RIGHT');
echo "Total nodes returned starting from 540599: " . count($allFrom540) . "\n";

$active540 = array_filter($allFrom540, function($m) { return ($m['status'] ?? '') === 'Active'; });
echo "Active nodes starting from 540599: " . count($active540) . "\n";
foreach ($active540 as $a) {
    echo "  Active: ID={$a['userid']} Name={$a['name']} Level={$a['level']} Parent={$a['parent_id']} Pos={$a['position']} InvUSD={$a['investment_usd']}\n";
}

// 2. Now check 540599's left child (224891)
$leftTree = getRootBranchTreeDetailed('224891', $pdo, 'LEFT');
echo "\nTotal nodes starting from 224891 (540599 left): " . count($leftTree) . "\n";
$activeLeft = array_filter($leftTree, function($m) { return ($m['status'] ?? '') === 'Active'; });
echo "Active nodes under 224891: " . count($activeLeft) . "\n";

// 3. Now check 540599's right child (251381)
$rightTree = getRootBranchTreeDetailed('251381', $pdo, 'RIGHT');
echo "\nTotal nodes starting from 251381 (540599 right): " . count($rightTree) . "\n";
$activeRight = array_filter($rightTree, function($m) { return ($m['status'] ?? '') === 'Active'; });
echo "Active nodes under 251381: " . count($activeRight) . "\n";

// 4. In $allFrom540, find all nodes that are NOT in $leftTree AND NOT in $rightTree AND NOT 540599
$leftIds = array_column($leftTree, 'userid');
$rightIds = array_column($rightTree, 'userid');
$missing = [];
foreach ($allFrom540 as $node) {
    $nid = $node['userid'];
    if ($nid === '540599') continue;
    if (!in_array($nid, $leftIds) && !in_array($nid, $rightIds)) {
        $missing[] = $node;
    }
}
echo "\nNodes attached to 540599 but neither in 224891 nor in 251381: " . count($missing) . "\n";
foreach ($missing as $m) {
    echo "  MISSING: ID={$m['userid']} Parent={$m['parent_id']} Pos={$m['position']} Status={$m['status']} InvUSD={$m['investment_usd']}\n";
}

// 5. Look at underuserid pointing to 540599 in user table
$under540 = $pdo->query("SELECT userid, name, underuserid, sponserid, join_side FROM user WHERE underuserid = '540599' OR underuserid = 'AN540599'")->fetchAll(PDO::FETCH_ASSOC);
echo "\nUsers in `user` table with underuserid = 540599 (" . count($under540) . "):\n";
foreach ($under540 as $u) {
    echo "  User {$u['userid']} ({$u['name']}): join_side={$u['join_side']} sponserid={$u['sponserid']}\n";
}

// 6. Look at tree table for 540599
$tree540 = $pdo->query("SELECT * FROM tree WHERE userid = '540599'")->fetch(PDO::FETCH_ASSOC);
echo "\nTree row for 540599: " . json_encode($tree540) . "\n";

