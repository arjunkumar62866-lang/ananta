<?php
$token = $_GET['t'] ?? '';
if ($token !== 'ananta_diag_2025') { http_response_code(403); die('Forbidden'); }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/dashboard/user1/common/connection.php';

echo "=== GLOBAL BATCH FIX: Sync user.underuserid + user.join_side FROM tree table ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Get all mismatched users using tree as source of truth
$sql = "
    SELECT 
        u.userid,
        u.underuserid AS old_underuserid,
        u.join_side AS old_join_side,
        t.userid AS correct_parent,
        CASE WHEN t.left_id = u.userid THEN 'left' ELSE 'right' END AS correct_side
    FROM user u
    JOIN tree t ON (t.left_id = u.userid OR t.right_id = u.userid)
    WHERE u.underuserid != t.userid
       OR u.join_side != CASE WHEN t.left_id = u.userid THEN 'left' ELSE 'right' END
       OR u.join_side = '' OR u.join_side IS NULL
    ORDER BY u.userid
";

$mismatches = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "Total mismatches to fix: " . count($mismatches) . "\n\n";

$fixed = 0;
$failed = 0;

foreach ($mismatches as $m) {
    $stmt = $pdo->prepare(
        "UPDATE user SET underuserid = ?, join_side = ? WHERE userid = ?"
    );
    $result = $stmt->execute([$m['correct_parent'], $m['correct_side'], $m['userid']]);
    $rows = $stmt->rowCount();

    if ($result && $rows > 0) {
        echo "FIXED: userid=" . $m['userid'] 
            . " | underuserid: " . $m['old_underuserid'] . " -> " . $m['correct_parent']
            . " | join_side: '" . $m['old_join_side'] . "' -> " . $m['correct_side'] . "\n";
        $fixed++;
    } else {
        echo "SKIP/FAIL: userid=" . $m['userid'] . " (rows=" . $rows . ")\n";
        $failed++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Fixed: $fixed\n";
echo "Skipped/Failed: $failed\n";

// Post-fix verification
echo "\n=== POST-FIX VERIFICATION ===\n";
$verify = $pdo->query("
    SELECT COUNT(*) as cnt FROM user u
    JOIN tree t ON (t.left_id = u.userid OR t.right_id = u.userid)
    WHERE u.underuserid != t.userid
       OR u.join_side != CASE WHEN t.left_id = u.userid THEN 'left' ELSE 'right' END
       OR u.join_side = '' OR u.join_side IS NULL
")->fetch(PDO::FETCH_ASSOC);

$remaining = $verify['cnt'];
if ($remaining == 0) {
    echo "ALL CLEAN - 0 mismatches remaining!\n";
} else {
    echo "WARNING: $remaining mismatches still remaining.\n";
}

// Duplicate check
$dups = $pdo->query("
    SELECT child_id, COUNT(*) as cnt FROM (
        SELECT left_id AS child_id FROM tree WHERE left_id != '' AND left_id IS NOT NULL
        UNION ALL
        SELECT right_id AS child_id FROM tree WHERE right_id != '' AND right_id IS NOT NULL
    ) x GROUP BY child_id HAVING COUNT(*) > 1
")->fetchAll(PDO::FETCH_ASSOC);
echo "Duplicate parent check: " . (empty($dups) ? "ALL CLEAN - No duplicates!" : "FOUND " . count($dups) . " DUPLICATES!") . "\n";

echo "\n=== DONE ===\n";
