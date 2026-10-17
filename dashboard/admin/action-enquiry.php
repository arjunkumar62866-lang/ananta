<?php
require_once 'common/connection.php';
require_once 'common/db_method.php';
session_start();

$admin_id = $_SESSION['auserid'] ?? 'AN1290';

// Handle POST reply submission from view_enquiry.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply_enquiry') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '1';
    $reply = trim($_POST['admin_reply'] ?? '');

    if ($id > 0) {
        try {
            $stmtQ = $pdo->prepare("SELECT * FROM tbl_query WHERE id = :id LIMIT 1");
            $stmtQ->execute([':id' => $id]);
            $qRow = $stmtQ->fetch(PDO::FETCH_ASSOC);

            if ($qRow) {
                $stmtUpd = $pdo->prepare("UPDATE tbl_query SET status = :st, admin_reply = :reply, reply_date = NOW(), replied_by = :aid WHERE id = :id");
                $stmtUpd->execute([
                    ':st'    => $status,
                    ':reply' => $reply,
                    ':aid'   => $admin_id,
                    ':id'    => $id
                ]);

                // Also sync to tbl_support_tickets if found
                try {
                    $stmtTkt = $pdo->prepare("UPDATE tbl_support_tickets SET status = :st, admin_reply = :reply, replied_by = :aid, replied_at = NOW() WHERE user_id = :uid AND subject = :sub");
                    $tktSt = ($status == '1') ? 'RESOLVED' : (($status == '2') ? 'CLOSED' : 'PENDING');
                    $stmtTkt->execute([
                        ':st'    => $tktSt,
                        ':reply' => $reply,
                        ':aid'   => $admin_id,
                        ':uid'   => $qRow['userid'],
                        ':sub'   => $qRow['sub']
                    ]);
                } catch (Exception $eTkt) {}

                // Send in-app notification to user
                if (function_exists('createUserNotification')) {
                    $statusLabel = ($status == '1') ? 'Approved / Resolved' : (($status == '2') ? 'Rejected' : 'Under Review');
                    $notifTitle  = "Support Enquiry Update: {$qRow['sub']}";
                    $notifMsg    = "Your enquiry has been {$statusLabel}. Admin Note: " . ($reply ? mb_substr($reply, 0, 100) : 'Reviewed by Admin Team.');
                    createUserNotification($qRow['userid'], 'SYSTEM', $notifTitle, $notifMsg, (string)$id, $pdo);
                }

                echo "<script>alert('Enquiry reply posted and user notified successfully!');window.location.assign('user_enquiry.php');</script>";
                exit;
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "');window.location.assign('view_enquiry.php?id=" . $id . "');</script>";
            exit;
        }
    }
}

// Get GET parameters safely for quick action links
$id = $_GET['id'] ?? null;
$title = $_GET['title'] ?? null;

if ($id && $title) {
    try {
        $stmtQ = $pdo->prepare("SELECT * FROM tbl_query WHERE id = :id LIMIT 1");
        $stmtQ->execute([':id' => $id]);
        $qRow = $stmtQ->fetch(PDO::FETCH_ASSOC);

        if ($title == "Approved") {
            $stmt = $pdo->prepare("UPDATE tbl_query SET status = '1', replied_by = :aid, reply_date = NOW() WHERE id = :id");
            $stmt->execute([':id' => $id, ':aid' => $admin_id]);

            if ($qRow && function_exists('createUserNotification')) {
                createUserNotification($qRow['userid'], 'SYSTEM', "Enquiry Approved", "Your enquiry '{$qRow['sub']}' has been reviewed and approved by admin.", (string)$id, $pdo);
            }

            echo "<script>alert('Enquiry Approved Successfully');window.location.assign('user_enquiry.php');</script>";
        } elseif ($title == "Reject") {
            $stmt = $pdo->prepare("UPDATE tbl_query SET status = '2', replied_by = :aid, reply_date = NOW() WHERE id = :id");
            $stmt->execute([':id' => $id, ':aid' => $admin_id]);

            if ($qRow && function_exists('createUserNotification')) {
                createUserNotification($qRow['userid'], 'SYSTEM', "Enquiry Update", "Your enquiry '{$qRow['sub']}' has been reviewed.", (string)$id, $pdo);
            }

            echo "<script>alert('Enquiry Rejected Successfully');window.location.assign('user_enquiry.php');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "');window.location.assign('user_enquiry.php');</script>";
    }
} else {
    echo "<script>alert('Invalid Request');window.location.assign('user_enquiry.php');</script>";
}
?>
