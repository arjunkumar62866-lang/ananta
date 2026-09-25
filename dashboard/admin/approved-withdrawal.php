<?php
require_once "common/connection.php";
require_once "common/db_method.php";

$tid = $_GET['tid'] ?? '';
$tid = (int)$tid;

if ($tid > 0) {
    // Lock transaction row
    $stmtTxn = $pdo->prepare("SELECT * FROM tbl_transaction WHERE id = :id FOR UPDATE");
    $stmtTxn->execute([':id' => $tid]);
    $txnRow = $stmtTxn->fetch(PDO::FETCH_ASSOC);

    if ($txnRow) {
        $updateStatus = $pdo->prepare("UPDATE tbl_transaction SET a_status = '1', status = 1, paid_date = CURDATE() WHERE id = :id");
        $updateStatus->execute([':id' => $tid]);

        $userId = $txnRow['user_id'];
        $amount = (float)($txnRow['amount'] ?? 0);
        $method = $txnRow['withdrawal_method'] ?? 'INR';

        // Log Notification for user
        if (function_exists('createUserNotification')) {
            createUserNotification(
                $userId,
                'WITHDRAWAL',
                'Withdrawal Request Approved & Paid',
                "Your {$method} withdrawal request of $" . number_format($amount, 2) . " has been approved and paid by Admin.",
                $tid,
                $pdo
            );
        }

        // Log Admin Audit Action
        if (function_exists('logAdminAuditAction')) {
            logAdminAuditAction(
                $_SESSION['auserid'] ?? 'ADMIN',
                'APPROVE_WITHDRAWAL',
                $userId,
                $amount,
                'amount',
                0,
                0,
                "Approved and paid withdrawal request #{$tid} of $" . number_format($amount, 2),
                (string)$tid,
                $pdo
            );
        }

        echo "<script>alert('Withdrawal Request #{$tid} Approved & Paid Successfully.'); window.location.assign('withdraw-history.php?type=1');</script>";
        exit;
    }
}

echo "<script>alert('Invalid withdrawal transaction specified.'); window.location.assign('withdraw-history.php?type=0');</script>";
exit;