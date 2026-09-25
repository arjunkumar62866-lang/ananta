<?php
require_once "common/connection.php";
require_once "common/db_method.php";

$uid = trim($_GET['uid'] ?? '');
$tid = (int)($_GET['tid'] ?? 0);
$tamt = (float)($_GET['amt'] ?? 0);

if (!empty($uid) && $tid > 0) {
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
    }

    try {
        // Lock transaction row
        $stmtTxn = $pdo->prepare("SELECT * FROM tbl_transaction WHERE id = :id FOR UPDATE");
        $stmtTxn->execute([':id' => $tid]);
        $txnRow = $stmtTxn->fetch(PDO::FETCH_ASSOC);

        if (!$txnRow) {
            throw new Exception("Withdrawal transaction record not found.");
        }

        if ((string)$txnRow['a_status'] === '2') {
            throw new Exception("Withdrawal request #{$tid} is already cancelled.");
        }

        $refundAmt = ($tamt > 0) ? $tamt : (float)($txnRow['amount'] ?? 0);

        // Update transaction status
        $updTxn = $pdo->prepare("UPDATE tbl_transaction SET a_status = '2', status = 2, subject = 'Cancel Withdrawal', type = 'Credit' WHERE id = :id");
        $updTxn->execute([':id' => $tid]);

        // Refund wallet balance to user
        $updUser = $pdo->prepare("UPDATE user SET amount = amount + :amt WHERE userid = :uid");
        $updUser->execute([':amt' => $refundAmt, ':uid' => $uid]);

        // Log Notification for user
        if (function_exists('createUserNotification')) {
            createUserNotification(
                $uid,
                'WITHDRAWAL',
                'Withdrawal Request Cancelled & Refunded',
                "Your withdrawal request #{$tid} of $" . number_format($refundAmt, 2) . " has been cancelled and refunded to your Net Balance.",
                $tid,
                $pdo
            );
        }

        // Log Admin Audit Action
        if (function_exists('logAdminAuditAction')) {
            logAdminAuditAction(
                $_SESSION['auserid'] ?? 'ADMIN',
                'CANCEL_WITHDRAWAL',
                $uid,
                $refundAmt,
                'amount',
                0,
                0,
                "Cancelled withdrawal #{$tid} and refunded $" . number_format($refundAmt, 2) . " to Net Balance.",
                (string)$tid,
                $pdo
            );
        }

        $pdo->commit();

        echo "<script>alert('Withdrawal Request #{$tid} Cancelled & $" . number_format($refundAmt, 2) . " Refunded to User Net Balance Successfully.'); window.location.assign('withdraw-history.php?type=2');</script>";
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "<script>alert('Error cancelling withdrawal: " . addslashes($e->getMessage()) . "'); window.location.assign('withdraw-history.php?type=0');</script>";
        exit;
    }
}

echo "<script>alert('Invalid request parameters.'); window.location.assign('withdraw-history.php?type=0');</script>";
exit;