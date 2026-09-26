<?php
header('Content-Type: application/json');   // Important: prevents extra HTML being sent
ob_start();                                  // Start buffer to block unwanted output

include 'common/connection.php';
include 'common/db_method.php';

$errors = ob_get_clean();       // Clear ANY output from included files

if (!empty($errors)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Error: extra output detected'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $raw_user_id = $_POST['userid'] ?? '';
    $amount  = (float)($_POST['amount'] ?? 0);
    $admin_id = $_SESSION['auserid'] ?? 'AN1290';

    $clean_uid = preg_replace('/^(AN|ANANTA)/i', '', $raw_user_id);

    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Transfer amount must be greater than 0']);
        exit;
    }

    // Validate user
    $stmtUser = $pdo->prepare("SELECT userid, name, pin_wallet FROM user WHERE userid = :uid OR userid = :clean LIMIT 1");
    $stmtUser->execute([':uid' => $raw_user_id, ':clean' => $clean_uid]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not found']);
        exit;
    }

    $target_userid = $user['userid'];
    $prev_bal = (float)$user['pin_wallet'];
    $new_bal  = round($prev_bal + $amount, 2);

    // Insert transaction
    $stmtTxn = $pdo->prepare("
        INSERT INTO tbl_transaction 
        (amount, user_id, subject, type, status, a_status, created_date, time)
        VALUES (:amount, :user_id, :subject, 'Credit', '1', '0', CURDATE(), NOW())
    ");

    $success = $stmtTxn->execute([
        ':amount'  => $amount,
        ':user_id' => $target_userid,
        ':subject' => "Admin Fund Transfer ($amount Add To Main/Pin Wallet)"
    ]);

    if ($success) {
        $txn_id = $pdo->lastInsertId();

        $stmtUpd = $pdo->prepare("UPDATE user SET pin_wallet = pin_wallet + :amount, deposite_wallet = deposite_wallet + :amount, amount = amount + :amount, total_deposit = total_deposit + :amount WHERE userid = :userid OR userid = :clean OR userid = :prefixed");
        $stmtUpd->execute([
            ':amount'   => $amount,
            ':userid'   => $target_userid,
            ':clean'    => $clean_uid,
            ':prefixed' => 'AN' . $clean_uid
        ]);

        // Complete 8-field Audit Record
        logAdminAuditAction(
            $admin_id,
            'CREDIT',
            $target_userid,
            $amount,
            'pin_wallet',
            $prev_bal,
            $new_bal,
            "Admin Fund Transfer ($amount Added)",
            (string)$txn_id,
            $pdo
        );

        echo json_encode(['status' => 'success', 'message' => "Amount ₹" . number_format($amount, 2) . " transferred successfully to user " . $target_userid]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database transaction failed']);
        exit;
    }
}

?>
