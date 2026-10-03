<?php
$isCli = (php_sapi_name() === "cli");
$key = $_GET["key"] ?? ($argv[1] ?? "");
if (!$isCli && $key !== "aX9zK7mQ") { http_response_code(403); die("Forbidden\n"); }
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/common/connection.php';
global $pdo;

echo "=== INSPECTING 251381 and 364639 ===\n\n";

$u251 = $pdo->query("SELECT * FROM user WHERE userid = '251381'")->fetch(PDO::FETCH_ASSOC);
echo "USER 251381: " . json_encode($u251) . "\n";
$t251 = $pdo->query("SELECT * FROM tree WHERE userid = '251381'")->fetch(PDO::FETCH_ASSOC);
echo "TREE 251381: " . json_encode($t251) . "\n";

$u364 = $pdo->query("SELECT * FROM user WHERE userid = '364639'")->fetch(PDO::FETCH_ASSOC);
echo "USER 364639: " . json_encode($u364) . "\n";
$t364 = $pdo->query("SELECT * FROM tree WHERE userid = '364639'")->fetch(PDO::FETCH_ASSOC);
echo "TREE 364639: " . json_encode($t364) . "\n";

// Parents referencing 251381
$p251 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE left_id = '251381' OR right_id = '251381'")->fetchAll(PDO::FETCH_ASSOC);
echo "Parents referencing 251381 in tree: " . json_encode($p251) . "\n";

// Parents referencing 364639
$p364 = $pdo->query("SELECT userid, left_id, right_id FROM tree WHERE left_id = '364639' OR right_id = '364639'")->fetchAll(PDO::FETCH_ASSOC);
echo "Parents referencing 364639 in tree: " . json_encode($p364) . "\n";

// Check 251381's children and members
$t251_children = $pdo->query("SELECT userid, underuserid, join_side FROM user WHERE underuserid = '251381'")->fetchAll(PDO::FETCH_ASSOC);
echo "Users with underuserid = 251381: " . json_encode($t251_children) . "\n";

// Check 364639's children and members
$t364_children = $pdo->query("SELECT userid, underuserid, join_side FROM user WHERE underuserid = '364639'")->fetchAll(PDO::FETCH_ASSOC);
echo "Users with underuserid = 364639: " . json_encode($t364_children) . "\n";

