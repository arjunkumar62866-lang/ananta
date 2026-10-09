<?php
require_once "common/connection.php";
require_once "common/db_method.php";

$tid = (int)($_REQUEST['tid'] ?? 0);
$adminRemarks = trim($_REQUEST['admin_remarks'] ?? $_REQUEST['remarks'] ?? 'Withdrawal approved successfully.');

if ($tid > 0) {
    ensureWithdrawalRemarksColumnExists($pdo);

    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
    }

    try {
        // Lock transaction row FOR UPDATE
        $stmtTxn = $pdo->prepare("SELECT * FROM tbl_transaction WHERE id = :id FOR UPDATE");
        $stmtTxn->execute([':id' => $tid]);
        $txnRow = $stmtTxn->fetch(PDO::FETCH_ASSOC);

        if (!$txnRow) {
            throw new Exception("Withdrawal transaction record not found.");
        }

        if ((string)$txnRow['a_status'] === '1') {
            throw new Exception("Withdrawal request #{$tid} is ALREADY approved.");
        }

        $userId = $txnRow['user_id'];
        $amount = (float)($txnRow['amount'] ?? 0);
        $method = $txnRow['withdrawal_method'] ?? 'INR';
        $invId  = (int)($txnRow['api_txn_no'] ?? 0);

        // Update tbl_transaction status & remarks
        $updateStatus = $pdo->prepare("UPDATE tbl_transaction SET a_status = '1', status = 1, paid_date = CURDATE(), admin_remarks = :rem WHERE id = :id");
        $updateStatus->execute([':rem' => $adminRemarks, ':id' => $tid]);

        // If this is a Capital Withdrawal, update tbl_roi_one & tbl_capital_withdrawal_request
        if ($method === 'Capital' || strpos(strtolower($txnRow['subject'] ?? ''), 'capital') !== false) {
            if ($invId > 0) {
                $updRoi = $pdo->prepare("UPDATE tbl_roi_one SET capital_withdrawal_status = 'WITHDRAWN', status = '1' WHERE id = :iid AND user_id = :uid");
                $updRoi->execute([':iid' => $invId, ':uid' => $userId]);

                $updCapReq = $pdo->prepare("UPDATE tbl_capital_withdrawal_request SET status = 'PAID', admin_remarks = :rem, processed_at = NOW() WHERE investment_id = :iid AND user_id = :uid AND status = 'PENDING'");
                $updCapReq->execute([':rem' => $adminRemarks, ':iid' => $invId, ':uid' => $userId]);
            }
        }

        // Log Notification for user
        if (function_exists('createUserNotification')) {
            $notifMsg = "Your {$method} withdrawal request of $" . number_format($amount, 2) . " has been approved and paid by Admin. Remarks: " . $adminRemarks;
            createUserNotification(
                $userId,
                'WITHDRAWAL',
                'Withdrawal Request Approved & Paid',
                $notifMsg,
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
                "Approved withdrawal request #{$tid}. Remarks: {$adminRemarks}",
                (string)$tid,
                $pdo
            );
        }

        $pdo->commit();

        $redirectPage = ($method === 'Capital' || strpos(strtolower($txnRow['subject'] ?? ''), 'capital') !== false) ? 'investment-withdraw-history.php?type=1' : 'withdraw-history.php?type=1';
        echo "<script>alert('Withdrawal Request #{$tid} Approved & Paid Successfully.'); window.location.assign('{$redirectPage}');</script>";
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "<script>alert('Error approving withdrawal: " . addslashes($e->getMessage()) . "'); window.location.assign('withdraw-history.php?type=0');</script>";
        exit;
    }
}

echo "<script>alert('Invalid withdrawal transaction specified.'); window.location.assign('withdraw-history.php?type=0');</script>";
exit;