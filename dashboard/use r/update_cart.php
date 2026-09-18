<?php
include 'common/connection.php';
session_start();

$action = $_POST['action'];
$id = $_POST['id'];

switch ($action) {
    case 'increase':
        $pdo->query("UPDATE tbl_cart SET qty = qty + 1 WHERE id = $id");
        break;
    case 'decrease':
        $pdo->query("UPDATE tbl_cart SET qty = GREATEST(qty - 1, 1) WHERE id = $id");
        break;
    case 'remove':
        $pdo->query("DELETE FROM tbl_cart WHERE id = $id");
        break;
}
echo "success";
?>
