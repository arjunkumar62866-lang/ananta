<?php
require_once __DIR__ . '/../dashboard/admin/common/connection.php';
$tables = $pdo->query("SHOW TABLES LIKE '%withdraw%'")->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($tables, JSON_PRETTY_PRINT) . "\n";
foreach ($tables as $t) {
    echo "=== {$t} ===\n";
    $cols = $pdo->query("DESCRIBE {$t}")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['Field']} ({$c['Type']})\n";
    }
}
