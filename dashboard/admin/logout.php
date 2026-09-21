<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['auserid']);
header("Location: /dashboard/user1/index.php");
exit();
?>