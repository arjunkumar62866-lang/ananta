<?php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== HOSTINGER LIVE VERIFICATION TEST ===\n";

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

echo "1. Connection: " . (isset($pdo) ? "CONNECTED" : "FAILED") . "\n";

// Test 540599
$leftM = getUserTeamMembersDetailed('540599', 'LEFT', $pdo);
$rightM = getUserTeamMembersDetailed('540599', 'RIGHT', $pdo);
$leftBus = array_sum(array_column($leftM, 'investment_usd'));
$rightBus = array_sum(array_column($rightM, 'investment_usd'));

echo "2. User 540599 Left Team Count: " . count($leftM) . ", Business: $" . number_format($leftBus, 2) . "\n";
echo "3. User 540599 Right Team Count: " . count($rightM) . ", Business: $" . number_format($rightBus, 2) . "\n";

// Test 789260
$l789 = getUserTeamMembersDetailed('789260', 'LEFT', $pdo);
$r789 = getUserTeamMembersDetailed('789260', 'RIGHT', $pdo);
echo "4. User 789260 Left Team Count: " . count($l789) . "\n";
echo "5. User 789260 Right Team Count: " . count($r789) . "\n";

// Check 224891 parent references in tree table
$stmt = $pdo->prepare("SELECT userid, left_id, right_id FROM tree WHERE left_id = '224891' OR right_id = '224891' OR left_id = 'AN224891' OR right_id = 'AN224891'");
$stmt->execute();
$parents = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "6. User 224891 Parent References in Tree Table: " . json_encode($parents) . "\n";

echo "\n=== VERIFICATION COMPLETE ===\n";
