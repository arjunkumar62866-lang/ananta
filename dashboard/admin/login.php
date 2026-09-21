<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Location: /dashboard/user1/index.php");
exit();
?>