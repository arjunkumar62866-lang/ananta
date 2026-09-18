<?php
session_start();
if (!isset($_SESSION["auserid"])) {
    header("Location:index");
    exit();
}

include("common/connection.php");   // MUST contain $con as PDO instance
include "common/db_method.php";

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}

$date = date("Y-m-d");

// Fetch all ROI records where count < lock_day (not completed)
$sql = "SELECT * FROM tbl_roi_one WHERE count < lock_day AND status = '0'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($rows as $r) {

    $id          = $r['id'];
    $user_id     = $r['user_id'];
    $package     = $r['package'];
    // $percent     = $r['percentage'];  // usually 3
    $count       = $r['count'];
    $lock_day    = $r['lock_day'];
    $closingdate = $r['closingdate'];

    // Prevent duplicate payment on same day
    if ($closingdate == $date) {
        continue;
    }

    // Calculate income
    $income = ($package * $percent) / 100;

    /**********************************************
     * Update user income wallet
     **********************************************/
    updatenonworkwallet($user_id,$income,$pdo);
     
    /**********************************************
     * Update tbl_roi_one record
     **********************************************/
    $update = $pdo->prepare("
        UPDATE tbl_roi_one 
        SET 
            count = count + 1,
            amount = :amount,
            totalincome = totalincome + :amount,
            closingdate = :date
        WHERE id = :id
    ");

    $update->execute([
        ':amount' => $income,
        ':date'   => $date,
        ':id'     => $id
    ]);
    
    
    profit_sharing_income_pay($user_id,$income);

    /**********************************************
     * Mark completed (48 days finished)
     **********************************************/
    if (($count + 1) >= $lock_day) {
        $complete = $pdo->prepare("UPDATE tbl_roi_one SET status = '1' WHERE id = :id");
        $complete->execute([':id' => $id]);
    }
}

?>
<meta http-equiv="refresh" content="0; url=index.php" />
