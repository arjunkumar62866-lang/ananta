<?php
include 'common/connection.php';

// Fetch GET values safely
$tid     = $_GET['id'] ?? null;
$title   = $_GET['title'] ?? null;
$amt     = $_GET['amt'] ?? null;
$userid  = $_GET['uid'] ?? null;

if ($title === "Approved") {
    $date = date('Y-m-d H:i:s');
    $time = date('H:i:s');

    // Update user pin_wallet
    $stmt1 = $pdo->prepare("UPDATE user SET pin_wallet = pin_wallet + :amt WHERE userid = :userid");
    $stmt1->execute([
        ':amt' => $amt,
        ':userid' => $userid
    ]);

    // Update payment status
    $stmt2 = $pdo->prepare("UPDATE tbl_payment SET status = 1 WHERE tr_id = :tid");
    $stmt2->execute([':tid' => $tid]);
    
    // Insert transaction
    $subject = "$amt Amount Add To Wallet";
    $stmt3 = $pdo->prepare("INSERT into tbl_transaction (user_id, type, subject, time, created_date, status, amount) values (:u_id, :type, :sub, :time, :date, 1, :amount)");
    $stmt3->execute([
            ':u_id'   => $userid,
            ':type'   => 'Credit',
            ':sub'    => $subject,
            ':time'   => $time,
            ':date'   => $date,
            ':amount' => $amt
        
        ]);

    if ($stmt2->rowCount()) {
        echo "<script>window.location.assign('pending-fund-request.php');</script>";
    }

} elseif ($title === "Cancel") {

    // Update payment status to Cancel
    $stmt = $pdo->prepare("UPDATE tbl_payment SET status = 2 WHERE tr_id = :tid");
    $stmt->execute([':tid' => $tid]);

    if ($stmt->rowCount()) {
        echo "<script>window.location.assign('pending-fund-request.php');</script>";
    }
}
?>
