<?php
chdir(__DIR__ . '/../dashboard/user1');
require_once 'common/connection.php';
require_once 'common/db_method.php';

$users = ['1290', '215567', '224891', '509701', '773330', '843217', '970637'];

foreach ($users as $uid) {
    $stmt = $pdo->prepare("SELECT left_id, right_id FROM tree WHERE userid = :uid");
    $stmt->execute([':uid' => $uid]);
    $treeRow = $stmt->fetch(PDO::FETCH_ASSOC);

    $leftid = $treeRow['left_id'] ?? '';
    $rightid = $treeRow['right_id'] ?? '';

    $leftMembers = !empty($leftid) ? getRootBranchTreeDetailed($leftid, $pdo, 'LEFT') : [];
    $rightMembers = !empty($rightid) ? getRootBranchTreeDetailed($rightid, $pdo, 'RIGHT') : [];

    $activeLeft = array_filter($leftMembers, function($m) { return $m['status'] === 'Active'; });
    $activeRight = array_filter($rightMembers, function($m) { return $m['status'] === 'Active'; });

    echo "------------------------------------------------------------------------\n";
    echo "USER ID: $uid\n";
    echo "Tree Left Root : " . ($leftid ?: 'NONE') . "\n";
    echo "Tree Right Root: " . ($rightid ?: 'NONE') . "\n";
    echo "Left Branch  -> Total: " . count($leftMembers) . " | Active: " . count($activeLeft) . " | Inactive: " . (count($leftMembers) - count($activeLeft)) . "\n";
    echo "  Active Left IDs : " . (count($activeLeft) ? implode(', ', array_column($activeLeft, 'userid')) : 'NONE') . "\n";
    echo "Right Branch -> Total: " . count($rightMembers) . " | Active: " . count($activeRight) . " | Inactive: " . (count($rightMembers) - count($activeRight)) . "\n";
    echo "  Active Right IDs: " . (count($activeRight) ? implode(', ', array_column($activeRight, 'userid')) : 'NONE') . "\n";
    echo "Dashboard Active Team: " . count($activeLeft) . " / " . count($activeRight) . "\n";
}
