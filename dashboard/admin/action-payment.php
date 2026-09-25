<?php
include 'common/connection.php';

// Fetch GET/POST values safely
$tid     = $_GET['id'] ?? $_POST['id'] ?? null;
$title   = $_GET['title'] ?? $_POST['title'] ?? null;
$amt     = floatval($_GET['amt'] ?? $_POST['amt'] ?? 0);
$userid  = $_GET['uid'] ?? $_POST['uid'] ?? null;
$remark  = trim($_GET['remark'] ?? $_POST['remark'] ?? '');

if (!$remark) {
    $remark = ($title === "Approved") ? "Approved by Admin" : "Rejected by Admin";
}

if ($title === "Approved") {
    $date = date('Y-m-d H:i:s');
    $time = date('H:i:s');

    // Credit user's pin_wallet
    $stmt1 = $pdo->prepare("UPDATE user SET pin_wallet = pin_wallet + :amt WHERE userid = :userid");
    $stmt1->execute([
        ':amt' => $amt,
        ':userid' => $userid
    ]);

    // Update payment status to 1 (Approved) & set admin remark
    $stmt2 = $pdo->prepare("UPDATE tbl_payment SET status = 1, remark = :remark WHERE tr_id = :tid OR id = :tid");
    $stmt2->execute([
        ':tid' => $tid,
        ':remark' => $remark
    ]);
    
    // Insert transaction log
    $subject = "$amt Amount Add To Wallet";
    $stmt3 = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, time, created_date, status, amount) VALUES (:u_id, :type, :sub, :time, :date, 1, :amount)");
    $stmt3->execute([
        ':u_id'   => $userid,
        ':type'   => 'Credit',
        ':sub'    => $subject,
        ':time'   => $time,
        ':date'   => $date,
        ':amount' => $amt
    ]);

    // Include user db_method for notification helper
    require_once __DIR__ . '/../user1/common/db_method.php';
    if (function_exists('createUserNotification')) {
        createUserNotification(
            $userid,
            'DEPOSIT',
            'Deposit Request Approved',
            "Your deposit request of $" . number_format($amt, 2) . " has been APPROVED by Admin. $" . number_format($amt, 2) . " has been credited to your Main Wallet. Remark: {$remark}",
            $tid,
            $pdo
        );
    }

    echo "<script>alert('Deposit Request Approved Successfully.');window.location.assign('pending-fund-request.php');</script>";
    exit;

} elseif ($title === "Cancel" || $title === "Reject") {

    // Update payment status to 2 (Rejected) & set admin remark
    $stmt = $pdo->prepare("UPDATE tbl_payment SET status = 2, remark = :remark WHERE tr_id = :tid OR id = :tid");
    $stmt->execute([
        ':tid' => $tid,
        ':remark' => $remark
    ]);

    require_once __DIR__ . '/../user1/common/db_method.php';
    if (function_exists('createUserNotification')) {
        createUserNotification(
            $userid,
            'DEPOSIT',
            'Deposit Request Rejected',
            "Your deposit request of $" . number_format($amt, 2) . " has been REJECTED by Admin. Remark: {$remark}",
            $tid,
            $pdo
        );
    }

    echo "<script>alert('Deposit Request Rejected.');window.location.assign('pending-fund-request.php');</script>";
    exit;
}
?>

