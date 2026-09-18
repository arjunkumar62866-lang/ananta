<?php
/**
 * Safe Database Connection Example Template
 * Rename or copy this file to 'connection.php' for local development or production.
 * DO NOT commit 'connection.php' containing real credentials to Git!
 */

$host     = getenv('DB_HOST') ?: "localhost";
$dbname   = getenv('DB_NAME') ?: "ananta_local_db";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
