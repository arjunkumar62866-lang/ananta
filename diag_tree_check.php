<?php
$token = $_GET['t'] ?? '';
if ($token !== 'ananta_diag_2025') { http_response_code(403); die('Forbidden'); }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/dashboard/user1/common/connection.php';

echo "=== GLOBAL TREE vs USER TABLE MISMATCH AUDIT ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Find all users who appear as left_id or right_id in tree table
// Then check if their user.underuserid matches the actual tree parent
$sql = "
    SELECT 
        u.userid,
        u.underuserid AS user_underuserid,
        u.join_side AS user_join_side,
        t.userid AS tree_parent,
        CASE WHEN t.left_id = u.userid THEN 'left' ELSE 'right' END AS tree_side
    FROM user u
    JOIN tree t ON (t.left_id = u.userid OR t.right_id = u.userid)
    WHERE u.underuserid != t.userid
       OR u.join_side != CASE WHEN t.left_id = u.userid THEN 'left' ELSE 'right' END
    ORDER BY u.userid
";

$mismatches = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "=== MISMATCHED USERS (user.underuserid != tree parent) ===\n";
if (empty($mismatches)) {
    echo "ALL CLEAN - No mismatches found!\n";
} else {
    echo "FOUND " . count($mismatches) . " MISMATCH(ES):\n\n";
    foreach ($mismatches as $m) {
        echo "userid: " . $m['userid'] . "\n";
        echo "  user.underuserid = " . $m['user_underuserid'] . " (WRONG)\n";
        echo "  user.join_side   = " . $m['user_join_side'] . " (WRONG)\n";
        echo "  tree parent      = " . $m['tree_parent'] . " (CORRECT)\n";
        echo "  tree side        = " . $m['tree_side'] . " (CORRECT)\n";
        echo "---\n";
    }
}

// Also check duplicate parents (same child in 2 parents)
echo "\n=== DUPLICATE PARENT CHECK (same child in 2+ tree nodes) ===\n";
$dupSql = "
    SELECT child_id, COUNT(*) as parent_count, GROUP_CONCAT(parent_id) as parents
    FROM (
        SELECT left_id AS child_id, userid AS parent_id FROM tree WHERE left_id != '' AND left_id IS NOT NULL
        UNION ALL
        SELECT right_id AS child_id, userid AS parent_id FROM tree WHERE right_id != '' AND right_id IS NOT NULL
    ) all_children
    GROUP BY child_id
    HAVING COUNT(*) > 1
";
$dups = $pdo->query($dupSql)->fetchAll(PDO::FETCH_ASSOC);
if (empty($dups)) {
    echo "ALL CLEAN - No duplicate parents!\n";
} else {
    echo "FOUND " . count($dups) . " DUPLICATE(S):\n";
    foreach ($dups as $d) {
        echo "child: " . $d['child_id'] . " has " . $d['parent_count'] . " parents: " . $d['parents'] . "\n";
    }
}

echo "\n=== DONE ===\n";
