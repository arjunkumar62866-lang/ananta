<?php
session_start();
if (!isset($_SESSION["auserid"])) {
    header("Location:index");
    exit();
}

include("common/connection.php");
include "common/db_method.php";

/******************************************************************************
 * LEGACY ROI TWO / DIRECT BONUS SCRIPT - ISOLATED & DISABLED FOR REQ #12
 * Direct Bonus payouts are now handled via the 10-Month Schedule system in
 * processDirectBonusInstallments() during Monthly Profit Closing.
 * Historical records in tbl_roi_two remain preserved for audit.
 ******************************************************************************/
die("Legacy Direct Bonus script is disabled. Direct Bonus payouts are now managed via Monthly Profit Closing and 10-Month Schedules.");

foreach ($rows as $r) {

    $id          = $r['id'];
    $user_id     = $r['user_id'];
    $package     = $r['package'];
    $percent     = $r['percentage'];   // usually 10%
    $count       = $r['count'];
    $lock_day    = $r['lock_day'];
    $closingdate = $r['closingdate'];
    
    $active_directs = getmydirectactive($user_id);

    // Avoid duplicate payment for same day
    
    // ------------------------------------------------------------- aaj ka changes --------------------------------------
    
    if ($closingdate == $date || $active_directs < 2) {
        continue;
    }
    

    // Calculate  direct bonus Income
    $income = ($package * $percent) / 100;

    /**********************************************
     * Insert transaction log
     **********************************************/
    $insert = $pdo->prepare("
        INSERT INTO tbl_transaction 
            (user_id, type, subject, amount, created_date, status)
        VALUES 
            (:user_id, 'Credit', 'Direct Bonus', :amount, :date, '1')
    ");

    $insert->execute([
        ':user_id' => $user_id,
        ':amount'  => $income,
        ':date'    => $date
    ]);
    
    /**********************************************
     * Update user income wallet
     **********************************************/
    updatenonworkwallet($user_id,$income,$pdo);

    /**********************************************
     * Update ROI TWO table
     **********************************************/
    $update = $pdo->prepare("
        UPDATE tbl_roi_two 
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

    /**********************************************
     * Lock completed (48/48)
     **********************************************/
    if (($count + 1) >= $lock_day) {
        $complete = $pdo->prepare("UPDATE tbl_roi_two SET status = '1' WHERE id = :id");
        $complete->execute([':id' => $id]);
    }
}

?>
<meta http-equiv="refresh" content="0; url=index.php" />
