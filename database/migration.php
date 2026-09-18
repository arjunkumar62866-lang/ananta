<?php
/**
 * Safe Database Migration CLI Runner for anantamtptl.com
 *
 * Usage:
 *   php database/migration.php status
 *   php database/migration.php migrate
 *
 * Safety Rules:
 *   - Never executes automatically on web page requests.
 *   - Uses PDO database connection from common/connection.php.
 *   - Tracks executed migrations in `schema_migrations` table.
 *   - Stops immediately on any SQL failure without marking failed migrations.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access Denied: The migration runner can only be executed from the Command Line Interface (CLI).\n");
}

$baseDir = dirname(__DIR__);
$connFile = $baseDir . '/common/connection.php';

if (!file_exists($connFile)) {
    die("ERROR: Connection file not found at: {$connFile}\nPlease copy 'common/connection.example.php' to 'common/connection.php' and configure database credentials.\n");
}

require_once $connFile;

if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("ERROR: \$pdo connection instance not found in {$connFile}\n");
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure migration history table exists
function initMigrationTable(PDO $pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS `schema_migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `version` VARCHAR(100) NOT NULL UNIQUE,
        `filename` VARCHAR(255) NOT NULL,
        `applied_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `checksum` VARCHAR(64) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
}

function getAppliedMigrations(PDO $pdo): array {
    $stmt = $pdo->query("SELECT `version` FROM `schema_migrations` ORDER BY `version` ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getMigrationFiles(string $migrationsDir): array {
    if (!is_dir($migrationsDir)) {
        return [];
    }
    $files = glob($migrationsDir . '/*.sql');
    sort($files, SORT_STRING);
    $migrations = [];
    foreach ($files as $filePath) {
        $filename = basename($filePath);
        if (preg_match('/^(\d+_\w+)\.sql$/', $filename, $matches)) {
            $migrations[$matches[1]] = [
                'version' => $matches[1],
                'filename' => $filename,
                'path' => $filePath
            ];
        }
    }
    return $migrations;
}

$action = $argv[1] ?? 'status';
$migrationsDir = __DIR__ . '/migrations';

initMigrationTable($pdo);
$appliedVersions = getAppliedMigrations($pdo);
$allMigrations = getMigrationFiles($migrationsDir);

if ($action === 'status') {
    echo "========================================================\n";
    echo " MIGRATION STATUS: anantamtptl.com\n";
    echo "========================================================\n";
    if (empty($allMigrations)) {
        echo "No migration files found in database/migrations/\n";
        exit(0);
    }
    printf("%-30s | %-12s\n", "Migration Version", "Status");
    echo "--------------------------------------------------------\n";
    foreach ($allMigrations as $version => $info) {
        $status = in_array($version, $appliedVersions) ? "[APPLIED]" : "[PENDING]";
        printf("%-30s | %-12s\n", $info['filename'], $status);
    }
    echo "========================================================\n";
    exit(0);
}

if ($action === 'migrate') {
    echo "Starting database migrations...\n\n";
    $pendingCount = 0;
    foreach ($allMigrations as $version => $info) {
        if (in_array($version, $appliedVersions)) {
            continue;
        }
        $pendingCount++;
        echo "Applying: {$info['filename']} ... ";
        $sql = file_get_contents($info['path']);
        $checksum = hash('sha256', $sql);
        
        try {
            $pdo->beginTransaction();
            $pdo->exec($sql);
            
            $stmt = $pdo->prepare("INSERT INTO `schema_migrations` (`version`, `filename`, `checksum`) VALUES (:version, :filename, :checksum)");
            $stmt->execute([
                ':version' => $version,
                ':filename' => $info['filename'],
                ':checksum' => $checksum
            ]);
            
            $pdo->commit();
            echo "[SUCCESS]\n";
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo "[FAILED]\n";
            echo "\n--------------------------------------------------------\n";
            echo "MIGRATION ERROR IN FILE: {$info['filename']}\n";
            echo "Error details: " . $e->getMessage() . "\n";
            echo "--------------------------------------------------------\n";
            echo "Migration stopped immediately. Fix the issue and rerun 'php database/migration.php migrate'\n";
            exit(1);
        }
    }
    if ($pendingCount === 0) {
        echo "Database is already up to date. No pending migrations.\n";
    } else {
        echo "\nSuccessfully applied {$pendingCount} migration(s).\n";
    }
    exit(0);
}

echo "Unknown action: {$action}\n";
echo "Usage:\n";
echo "  php database/migration.php status\n";
echo "  php database/migration.php migrate\n";
exit(1);
