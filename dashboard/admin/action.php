<?php 
include "common/header.php"; 
include "common/connection.php";

$uid  = $_GET['uid'] ?? null;
$type = $_GET['type'] ?? null;

if ($type == "act") {
    $stmt = $pdo->prepare("UPDATE user SET status = '1' WHERE userid = :uid");
    $stmt->execute(['uid' => $uid]);
    echo '<script>window.location = "all_user.php";</script>';
} 
elseif ($type == "deact") {
    $stmt = $pdo->prepare("UPDATE user SET status = '2' WHERE userid = :uid");
    $stmt->execute(['uid' => $uid]);
    echo '<script>window.location = "all_user.php";</script>';
}

?>
