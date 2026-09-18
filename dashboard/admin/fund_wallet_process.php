<?php
include("common/connection.php"); 
date_default_timezone_set('Asia/Kolkata'); 

$date = date('Y-m-d');
$time = date('H:i:s');

if(isset($_POST['userid'], $_POST['amount'])) {

    $sponserid = substr($_POST['userid'], 2);
    $amount = $_POST['amount'];

    // Fetch user details
    $stmt = $pdo->prepare("SELECT name, amount FROM user WHERE userid = :userid");
    $stmt->execute([':userid' => $sponserid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        $name = $user['name'];
        $pin_amount = $user['amount'];
        $p_amount = $pin_amount + $amount;

        // Insert transaction
        $stmtInsert = $pdo->prepare("INSERT INTO tbl_transaction 
            (amount, user_id, subject, type, status, a_status, created_date, time)
            VALUES (:amount, :user_id, :subject, :type, :status, :a_status, :created_date, :time)");

        $result = $stmtInsert->execute([
            ':amount' => $amount,
            ':user_id' => $sponserid,
            ':subject' => $amount . " Amount Add To Main Wallet",
            ':type' => 'Credit',
            ':status' => 1,
            ':a_status' => 0,
            ':created_date' => $date,
            ':time' => $time
        ]);

        if($result) {
            // Update user wallet
            $stmtUpdate = $pdo->prepare("UPDATE user SET amount = amount + :amount WHERE userid = :userid AND status='1'");
            $stmtUpdate->execute([
                ':amount' => $amount,
                ':userid' => $sponserid
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Amount transferred successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong']);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}
?>
