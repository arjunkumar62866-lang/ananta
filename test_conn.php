<?php
header('Content-Type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Step 1: Check connection file\n";
if (file_exists(__DIR__ . '/dashboard/user1/common/connection.php')) {
    echo "connection.php exists\n";
    include_once __DIR__ . '/dashboard/user1/common/connection.php';
    echo "PDO Object: " . (isset($pdo) && $pdo instanceof PDO ? 'YES' : 'NO') . "\n";
} else {
    echo "connection.php NOT FOUND\n";
}

echo "Step 2: Check user1 db_method\n";
if (file_exists(__DIR__ . '/dashboard/user1/common/db_method.php')) {
    include_once __DIR__ . '/dashboard/user1/common/db_method.php';
    echo "user1 db_method loaded\n";
}

echo "Step 3: Check admin db_method\n";
if (file_exists(__DIR__ . '/dashboard/admin/common/db_method.php')) {
    include_once __DIR__ . '/dashboard/admin/common/db_method.php';
    echo "admin db_method loaded\n";
}

echo "Step 4: Audit User 540599\n";
if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT userid, name, active, status, total_package, sponserid, underuserid, join_side FROM user WHERE userid = '540599' OR userid = 'AN540599'");
    $stmt->execute();
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "User 540599: " . json_encode($r) . "\n";

    $tstmt = $pdo->prepare("SELECT userid, left_id, right_id FROM tree WHERE userid = '540599' OR userid = 'AN540599'");
    $tstmt->execute();
    $tr = $tstmt->fetch(PDO::FETCH_ASSOC);
    echo "Tree 540599: " . json_encode($tr) . "\n";

    if (function_exists('getUserTeamMembersDetailed')) {
        $leftM = getUserTeamMembersDetailed('540599', 'LEFT', $pdo);
        $rightM = getUserTeamMembersDetailed('540599', 'RIGHT', $pdo);
        echo "540599 Left Count: " . count($leftM) . "\n";
        echo "540599 Right Count: " . count($rightM) . "\n";
    }
}
echo "Done!\n";
