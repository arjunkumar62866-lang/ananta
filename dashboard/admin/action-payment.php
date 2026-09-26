<?php
include 'common/connection.php';

// Fetch GET/POST values safely
$tid      = $_GET['id'] ?? $_POST['id'] ?? null;
$title    = $_GET['title'] ?? $_POST['title'] ?? null;
$amtParam = floatval($_GET['amt'] ?? $_POST['amt'] ?? 0);
$uidParam = $_GET['uid'] ?? $_POST['uid'] ?? null;
$remark   = trim($_GET['remark'] ?? $_POST['remark'] ?? '');

if (!$tid) {
    echo "<script>alert('Invalid request. Missing request ID.');window.location.assign('pending-fund-request.php');</script>";
    exit;
}

try {
    // 1. Fetch exact deposit request record from tbl_payment
    $stmtFind = $pdo->prepare("SELECT id, userid, tr_id, amount, status FROM tbl_payment WHERE (tr_id = :tid OR id = :tid) LIMIT 1");
    $stmtFind->execute([':tid' => $tid]);
    $payRow = $stmtFind->fetch(PDO::FETCH_ASSOC);

    if (!$payRow) {
        echo "<script>alert('Deposit request record not found.');window.location.assign('pending-fund-request.php');</script>";
        exit;
    }

    $reqId     = $payRow['id'];
    $trId      = $payRow['tr_id'] ?: $payRow['id'];
    $reqStatus = (int)$payRow['status'];
    $userid    = !empty($payRow['userid']) ? $payRow['userid'] : $uidParam;
    $amt       = ($amtParam > 0) ? $amtParam : (float)$payRow['amount'];

    // 2. Prevent Duplicate Approval / Re-processing
    if ($reqStatus !== 0) {
        $statusLabel = ($reqStatus === 1) ? 'Approved' : 'Rejected';
        echo "<script>alert('This deposit request has already been processed (Status: {$statusLabel}).');window.location.assign('pending-fund-request.php');</script>";
        exit;
    }

    if (!$remark) {
        $remark = ($title === "Approved") ? "Approved by Admin" : "Rejected by Admin";
    }

    if ($title === "Approved") {
        $date = date('Y-m-d');
        $time = date('H:i:s');

        // Begin atomic database transaction
        $pdo->beginTransaction();

        // Step A: Update payment status to 1 (Approved) strictly if currently status = 0
        $stmtStatus = $pdo->prepare("UPDATE tbl_payment SET status = 1, remark = :remark WHERE id = :id AND status = 0");
        $stmtStatus->execute([
            ':id'     => $reqId,
            ':remark' => $remark
        ]);

        if ($stmtStatus->rowCount() === 0) {
            $pdo->rollBack();
            echo "<script>alert('Failed to update status. Request may have already been processed.');window.location.assign('pending-fund-request.php');</script>";
            exit;
        }

        // Step B: Credit user's Main Wallet (deposite_wallet) AND update total_deposit
        $stmtUser = $pdo->prepare("UPDATE user SET deposite_wallet = deposite_wallet + :amt, total_deposit = total_deposit + :amt WHERE userid = :userid");
        $stmtUser->execute([
            ':amt'    => $amt,
            ':userid' => $userid
        ]);

        // Step C: Insert transaction log into tbl_transaction
        $subject = "Deposit Request Approved — $" . number_format($amt, 2) . " credited to Main Wallet";
        $stmtTxn = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, time, created_date, status, amount) VALUES (:u_id, 'Credit', :sub, :time, :date, 1, :amount)");
        $stmtTxn->execute([
            ':u_id'   => $userid,
            ':sub'    => $subject,
            ':time'   => $time,
            ':date'   => $date,
            ':amount' => $amt
        ]);

        // Commit transaction atomically
        $pdo->commit();

        // Include user notification helper
        require_once __DIR__ . '/../user1/common/db_method.php';
        if (function_exists('createUserNotification')) {
            createUserNotification(
                $userid,
                'DEPOSIT',
                'Deposit Request Approved',
                "Your deposit request of $" . number_format($amt, 2) . " has been APPROVED by Admin. $" . number_format($amt, 2) . " has been credited to your Main Wallet. Remark: {$remark}",
                $trId,
                $pdo
            );
        }

        echo "<script>alert('Deposit Request Approved Successfully. $" . number_format($amt, 2) . " credited to Main Wallet.');window.location.assign('pending-fund-request.php');</script>";
        exit;

    } elseif ($title === "Cancel" || $title === "Reject") {

        $pdo->beginTransaction();

        // Update payment status to 2 (Rejected)
        $stmtReject = $pdo->prepare("UPDATE tbl_payment SET status = 2, remark = :remark WHERE id = :id AND status = 0");
        $stmtReject->execute([
            ':id'     => $reqId,
            ':remark' => $remark
        ]);

        if ($stmtReject->rowCount() === 0) {
            $pdo->rollBack();
            echo "<script>alert('Failed to reject request. Request may have already been processed.');window.location.assign('pending-fund-request.php');</script>";
            exit;
        }

        $pdo->commit();

        require_once __DIR__ . '/../user1/common/db_method.php';
        if (function_exists('createUserNotification')) {
            createUserNotification(
                $userid,
                'DEPOSIT',
                'Deposit Request Rejected',
                "Your deposit request of $" . number_format($amt, 2) . " has been REJECTED by Admin. Remark: {$remark}",
                $trId,
                $pdo
            );
        }

        echo "<script>alert('Deposit Request Rejected.');window.location.assign('pending-fund-request.php');</script>";
        exit;
    } else {
        echo "<script>alert('Invalid action specified.');window.location.assign('pending-fund-request.php');</script>";
        exit;
    }
} catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("action-payment.php Exception: " . $e->getMessage());
    echo "<script>alert('An error occurred while processing the deposit request. Please try again.');window.location.assign('pending-fund-request.php');</script>";
    exit;
}
?>
