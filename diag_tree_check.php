<?php
$token = $_GET['t'] ?? '';
if ($token !== 'ananta_diag_2025') { http_response_code(403); die('Forbidden'); }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/dashboard/user1/common/connection.php';

echo "=== PRODUCTION FIX: user table 224891 underuserid + join_side ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// BEFORE STATE
echo "--- BEFORE ---\n";
$before = $pdo->query("SELECT userid, sponserid, underuserid, join_side FROM user WHERE userid = '224891'")->fetch(PDO::FETCH_ASSOC);
print_r($before);

// Confirm tree table source of truth
echo "\n--- tree table: 540599 left_id ---\n";
$tree540 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE userid = '540599'")->fetch(PDO::FETCH_ASSOC);
print_r($tree540);

// VERIFY: 224891 is indeed left child of 540599
if ($tree540 && $tree540['left_id'] == '224891') {
    echo "\nVERIFIED: 224891 is LEFT child of 540599 in tree table.\n";
    echo "Fixing user table: underuserid=540599, join_side=left ...\n";

    $stmt = $pdo->prepare("UPDATE user SET underuserid = '540599', join_side = 'left' WHERE userid = '224891'");
    $result = $stmt->execute();
    $affected = $stmt->rowCount();

    echo "UPDATE result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    echo "Rows affected: $affected\n";

    echo "\n--- AFTER ---\n";
    $after = $pdo->query("SELECT userid, sponserid, underuserid, join_side FROM user WHERE userid = '224891'")->fetch(PDO::FETCH_ASSOC);
    print_r($after);

    if ($after['underuserid'] == '540599' && $after['join_side'] == 'left') {
        echo "\nFIX CONFIRMED: underuserid=540599, join_side=left\n";
    } else {
        echo "\nWARNING: Values not updated as expected!\n";
    }
} else {
    echo "\nERROR: tree table does NOT confirm 224891 as left child of 540599. Aborting fix.\n";
}

echo "\n=== DONE ===\n";
