<?php
require_once "common/connection.php";
require_once "common/db_method.php";

$uid = trim($_REQUEST['uid'] ?? '');
$tid = (int)($_REQUEST['tid'] ?? 0);
$tamt = (float)($_REQUEST['amt'] ?? 0);
$adminRemarks = trim($_REQUEST['admin_remarks'] ?? $_REQUEST['remarks'] ?? $_REQUEST['reason'] ?? '');

if (empty($adminRemarks)) {
    echo "<script>alert('Rejection reason / remarks are MANDATORY when rejecting a withdrawal request. Please provide a reason.'); window.history.back();</script>";
    exit;
}

if (!empty($uid) && $tid > 0) {
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

        if ((string)$txnRow['a_status'] === '2') {
            throw new Exception("Withdrawal request #{$tid} is ALREADY rejected/cancelled.");
        }

        $fxRate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
        if ($fxRate <= 0) $fxRate = 90.0;

        $method = $txnRow['withdrawal_method'] ?? 'INR';
        $invId  = (int)($txnRow['api_txn_no'] ?? 0);
        // Gross requested amount in USD (act_amount stores gross USD; if 0 fallback to amount or $tamt)
        $grossUsd = ((float)($txnRow['act_amount'] ?? 0) > 0) ? (float)$txnRow['act_amount'] : (($tamt > 0) ? $tamt : (float)($txnRow['amount'] ?? 0));
        $refundAmtInr = round($grossUsd * $fxRate, 2);

        // Update transaction status & admin_remarks
        $updTxn = $pdo->prepare("UPDATE tbl_transaction SET a_status = '2', status = 2, subject = 'Cancel Withdrawal', admin_remarks = :rem WHERE id = :id");
        $updTxn->execute([':rem' => $adminRemarks, ':id' => $tid]);

        if ($method === 'Capital' || strpos(strtolower($txnRow['subject'] ?? ''), 'capital') !== false) {
            // Capital Withdrawal Rejection: Restore investment status to ACTIVE so user retains capital; NO payout
            if ($invId > 0) {
                $updRoi = $pdo->prepare("UPDATE tbl_roi_one SET capital_withdrawal_status = 'ACTIVE', status = '0' WHERE id = :iid AND user_id = :uid");
                $updRoi->execute([':iid' => $invId, ':uid' => $uid]);

                $updCapReq = $pdo->prepare("UPDATE tbl_capital_withdrawal_request SET status = 'REJECTED', admin_remarks = :rem, processed_at = NOW() WHERE investment_id = :iid AND user_id = :uid AND status = 'PENDING'");
                $updCapReq->execute([':rem' => $adminRemarks, ':iid' => $invId, ':uid' => $uid]);
            }

            $notifMsg = "Your capital withdrawal request #{$tid} for investment #{$invId} was rejected by Admin. Reason: " . $adminRemarks;
            $alertMsg = "Capital Withdrawal Request #{$tid} Rejected. Reason saved and investment capital restored to active state.";
            $redirectPage = 'investment-withdraw-history.php?type=2';

        } else {
            // Net Balance Withdrawal Rejection: Refund net balance wallet in INR (matching the initial INR deduction)
            $updTxnType = $pdo->prepare("UPDATE tbl_transaction SET type = 'Credit' WHERE id = :id");
            $updTxnType->execute([':id' => $tid]);

            $updUser = $pdo->prepare("UPDATE user SET amount = amount + :amt WHERE userid = :uid");
            $updUser->execute([':amt' => $refundAmtInr, ':uid' => $uid]);

            $notifMsg = "Your withdrawal request #{$tid} of $" . number_format($grossUsd, 2) . " (₹" . number_format($refundAmtInr, 2) . ") has been rejected/cancelled by Admin. Reason: " . $adminRemarks;
            $alertMsg = "Withdrawal Request #{$tid} Rejected & $" . number_format($grossUsd, 2) . " (₹" . number_format($refundAmtInr, 2) . ") Refunded to User Net Balance Successfully.";
            $redirectPage = 'withdraw-history.php?type=2';
        }

        // Log Notification for user
        if (function_exists('createUserNotification')) {
            createUserNotification(
                $uid,
                'WITHDRAWAL',
                'Withdrawal Request Rejected',
                $notifMsg,
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
                "Rejected withdrawal #{$tid}. Reason: {$adminRemarks}",
                (string)$tid,
                $pdo
            );
        }

        $pdo->commit();

        echo "<script>alert('{$alertMsg}'); window.location.assign('{$redirectPage}');</script>";
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "<script>alert('Error rejecting withdrawal: " . addslashes($e->getMessage()) . "'); window.location.assign('withdraw-history.php?type=0');</script>";
        exit;
    }
}

echo "<script>alert('Invalid request parameters.'); window.location.assign('withdraw-history.php?type=0');</script>";
exit;