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

    $user_id = $_POST['userid'] ?? '';
    $amount  = $_POST['amount'] ?? '';

    // Remove first 2 chars
    $user_id = substr($user_id, 2);

    // Validate user
    if (checkuserid($pdo, $user_id) > 0) {

        $stmt = $pdo->prepare("SELECT name, pin_wallet FROM user WHERE userid = :userid");
        $stmt->execute([':userid' => $user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Insert transaction
        $stmt = $pdo->prepare("
            INSERT INTO tbl_transaction 
            (amount, user_id, subject, type, status, a_status, created_date, time)
            VALUES (:amount, :user_id, :subject, 'Credit', '1', '0', NOW(), NOW())
        ");

        $success = $stmt->execute([
            ':amount' => $amount,
            ':user_id' => $user_id,
            ':subject' => "$amount Amount Add To Wallet"
        ]);

        if ($success) {

            $stmt = $pdo->prepare("UPDATE user 
                                   SET pin_wallet = pin_wallet + :amount 
                                   WHERE userid = :userid AND status = '1'");
            $stmt->execute([
                ':amount' => $amount,
                ':userid' => $user_id
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Amount transferred successfully']);
            exit;

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong']);
            exit;
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid User ID']);
        exit;
    }
}

?>
