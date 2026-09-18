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

/**********************************************
 *  GENERATION INCOME PAYOUT – PDO VERSION
 **********************************************/

// Fetch ROI percentage(Generation income percentage)
$sql1 = "SELECT percentage FROM tbl_roipercentage";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute();
$rows1 = $stmt1->fetch(PDO::FETCH_ASSOC);
$percent = $rows1['percentage']; 

// Fetch all ROI records where count < lock_day (not completed)
// $sql = "SELECT * FROM tbl_roi_one WHERE count < lock_day AND status = '0' AND DAY(`date`) = DAY($date)";
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

    // ---------------------------------------- aaj ki condition----------------------------
    
    // $diff=0;
    // // Convert to DateTime
    // if (is_null($closingdate) || $closingdate == "" || $closingdate == "0000-00-00") {
    //     // No previous closing date
    //     $closing = null;
    //     $diff = 999; // allow payout
    // } else {
    //     // $today = new DateTime();
    //     $closing = new DateTime($closingdate);
    //     $diff = $date->diff($closing)->days;
    // }
    // Prevent duplicate payment on same day
    // if ($closingdate == $date ) {
    //     continue;
    // }

    // Calculate income
    $income = ($package * $percent) / 100;

    /**********************************************
     * Insert transaction into tbl_transaction
     **********************************************/
    $insert = $pdo->prepare("
        INSERT INTO tbl_transaction 
            (user_id, type, subject, amount, created_date, status)
        VALUES 
            (:user_id, 'Generation Income', 'Generation Income Payout', :amount, :date, '1')
    ");

    $insert->execute([
        ':user_id' => $user_id,
        ':amount'  => $income,
        ':date'    => $date
    ]);
    
    /**********************************************
     * Update user record
     **********************************************/
    $update = $pdo->prepare("
        UPDATE user 
        SET 
            pending_geninc = pending_geninc + :amount,
            closingdate = :date
        WHERE userid = :id
    ");

    $update->execute([
        ':amount' => $income,
        ':date'   => $date,
        ':id'     => $user_id
    ]);

    /**********************************************
     * Update user income wallet
     **********************************************/
    // updatenonworkwallet($user_id,$income,$pdo);
     
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
    

    
    pay_roi_one_income($user_id, $income, $percent, $id, $id);

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
