<?php
require 'common/connection.php';
header('Content-Type: application/json');

$date = date('Y-m-d');
$time = date('H:i:s');

if(isset($_POST['userid']) && isset($_POST['amount'])) {
    $sponserid = substr($_POST['userid'], 2);
    $amount = $_POST['amount'];

    // Check user exists
    $stmt = $pdo->prepare("SELECT name, pin_wallet FROM user WHERE userid=:userid AND status=1");
    $stmt->execute(['userid' => $sponserid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        $name = $user['name'];
        $pin_amount = $user['pin_wallet'];
        $p_amount = $pin_amount + $amount;

        // Insert transaction
        $stmt2 = $pdo->prepare("INSERT INTO tbl_transaction (amount, user_id, subject, type, status, a_status, created_date, time)
                                VALUES (:amount, :user_id, :subject, 'Credit', 1, 0, :created_date, :time)");
        $result = $stmt2->execute([
            'amount' => $amount,
            'user_id' => $sponserid,
            'subject' => "$amount Amount Add To Fund Wallet",
            'created_date' => $date,
            'time' => $time
        ]);

        if($result) {
            // Update user's pin_wallet
            $stmt3 = $pdo->prepare("UPDATE user SET pin_wallet = pin_wallet + :amount WHERE userid = :userid");
            $stmt3->execute(['amount' => $amount, 'userid' => $sponserid]);

            echo json_encode(['status' => 'success', 'message' => 'Fund Wallet Updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong']);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid User ID']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
}
