<?php
$isCli = (php_sapi_name() === "cli");
$key = $_GET["key"] ?? ($argv[1] ?? "");
if (!$isCli && $key !== "aX9zK7mQ") { http_response_code(403); die("Forbidden\n"); }
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/common/connection.php';
global $pdo;

echo "=== INSPECTING TREE AND USER ANOMALIES ===\n\n";

// Check 251381 details
$t251 = $pdo->query("SELECT * FROM tree WHERE userid = '251381'")->fetch(PDO::FETCH_ASSOC);
echo "tree row for 251381: " . json_encode($t251) . "\n";

// Check 364639 details
$t364 = $pdo->query("SELECT * FROM tree WHERE userid = '364639'")->fetch(PDO::FETCH_ASSOC);
echo "tree row for 364639: " . json_encode($t364) . "\n";

// Who has underuserid pointing to them with more than 2 users?
$multiUnder = $pdo->query("
    SELECT underuserid, COUNT(*) as cnt 
    FROM user 
    WHERE underuserid IS NOT NULL AND underuserid != '' 
    GROUP BY underuserid 
    HAVING cnt > 2
")->fetchAll(PDO::FETCH_ASSOC);
echo "\nNodes with MORE THAN 2 direct underuserid in user table:\n";
foreach ($multiUnder as $mu) {
    echo "  Parent {$mu['underuserid']}: {$mu['cnt']} children\n";
    $kids = $pdo->query("SELECT userid, name, join_side, sponserid, joining_date FROM user WHERE underuserid = '{$mu['underuserid']}'")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($kids as $k) {
        echo "    -> Kid: {$k['userid']} ({$k['name']}) side={$k['join_side']} sp={$k['sponserid']} date={$k['joining_date']}\n";
    }
}

// Check tree table where 364639 or 251381 appears
echo "\nAll tree rows mentioning 364639:\n";
$trows364 = $pdo->query("SELECT * FROM tree WHERE userid='364639' OR left_id='364639' OR right_id='364639'")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($trows364) . "\n";

echo "\nAll tree rows mentioning 251381:\n";
$trows251 = $pdo->query("SELECT * FROM tree WHERE userid='251381' OR left_id='251381' OR right_id='251381'")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($trows251) . "\n";

