<?php
session_start();
// if (!isset($_SESSION["auserid"])) {
//     header("Location:index");
//     exit();
// }

include("common/connection.php");   // MUST contain $con as PDO instance
include "common/db_method.php";

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}

$date = date("Y-m-d");



$sql = "SELECT * FROM user WHERE pending_geninc >0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($rows as $r) {

    $id          = $r['id'];
    $user_id     = $r['userid'];

 
    manual_pay_roi_one_income($user_id);
}

?>
<meta http-equiv="refresh" content="0; url=index.php" />
