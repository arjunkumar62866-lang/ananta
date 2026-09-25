<?php
ob_start();
session_start();

$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: dashboard/user1/login.php" . $queryString);
exit();
?>
