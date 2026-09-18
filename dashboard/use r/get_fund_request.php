<?php
require 'common/connection.php'; // Your PDO connection
header('Content-Type: application/json');

$userid = $_GET['userid'];
// $user = $_GET['user'];
// echo "<script>alert('$userid')</script>";
// echo "<script>alert('$user')</script>";
switch (1) {
    case 1:
        $stmt = $pdo->prepare("SELECT userid,tr_id,subject,amount,mode,image,date,status FROM tbl_payment WHERE userid=:userid");
        break;
    default:
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user ORDER BY id DESC");
}

$stmt->execute(['userid'=>$userid]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
exit;