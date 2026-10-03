<?php
$token = $_GET['t'] ?? '';
if ($token !== 'ananta_diag_2025') { http_response_code(403); die('Forbidden'); }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/dashboard/user1/common/connection.php';
echo "=== PRODUCTION DB DIAGNOSTIC ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";
echo "=== 1. USER TABLE: 540599 ===\n";
$r = $pdo->query("SELECT userid, sponserid, underuserid, join_side, status FROM user WHERE userid = '540599'")->fetchAll(PDO::FETCH_ASSOC);
echo empty($r) ? "540599 NOT FOUND in user table.\n" : print_r($r, true);
echo "\n=== 2. TREE TABLE: 540599 ===\n";
$r2 = $pdo->query("SELECT userid, left_id, right_id, join_side FROM tree WHERE userid = '540599'")->fetchAll(PDO::FETCH_ASSOC);
echo empty($r2) ? "540599 NOT FOUND in tree table.\n" : print_r($r2, true);
echo "\n=== 3. WHO IS PARENT OF 224891 IN TREE TABLE ===\n";
$r3 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE left_id = '224891' OR right_id = '224891'")->fetchAll(PDO::FETCH_ASSOC);
if (empty($r3)) { echo "224891 has NO parent in tree table.\n"; }
else {
    foreach ($r3 as $row) {
        $side = ($row['left_id'] == '224891') ? 'LEFT' : 'RIGHT';
        echo "Parent: " . $row['userid'] . " -> " . $side . " -> 224891\n";
    }
    echo "Total parents: " . count($r3) . "\n";
    echo (count($r3) > 1) ? "!! DUPLICATE DETECTED !!\n" : "CLEAN - Only 1 parent.\n";
}
echo "\n=== 4. TREE TABLE: 789260 ===\n";
$r4 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE userid = '789260'")->fetchAll(PDO::FETCH_ASSOC);
echo empty($r4) ? "789260 NOT FOUND in tree.\n" : print_r($r4, true);
echo "\n=== 5. TREE TABLE: 540599 as parent ===\n";
$r5 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE userid = '540599'")->fetchAll(PDO::FETCH_ASSOC);
echo empty($r5) ? "540599 NOT FOUND as parent in tree.\n" : print_r($r5, true);
echo "\n=== 6. USER TABLE: 224891 ===\n";
$r6 = $pdo->query("SELECT userid, sponserid, underuserid, join_side FROM user WHERE userid = '224891'")->fetchAll(PDO::FETCH_ASSOC);
echo empty($r6) ? "224891 NOT FOUND.\n" : print_r($r6, true);
echo "\n=== DONE ===\n";
