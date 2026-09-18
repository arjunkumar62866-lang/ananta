<?php
include("common/connection.php");

$uid=$_GET['uid'];
$tid=$_GET['tid'];
$tamt=$_GET['amt'];

$userData=$pdo->prepare("SELECT * FROM user WHERE userid=:userid");
$userData->execute([':userid'=>$uid]);
if($userData->rowCount() > 0);


    while($row = $userData->fetchAll(PDO::FETCH_ASSOC)) 
    {
        if($row)
        {
            $signup_id=  $row['id'];
            $name=  $row['name'];
            $mobile=  $row['mobile'];
            $email=  $row['email'];
            $password=  $row['password'];
            $amount=  $row['amount'];
            $status=  $row['status'];
            $userid=  $row['userid'];
            $active_date=  $row['active_date'];
            $created_date=  $row['created_date'];
        }
    }
    $updateStatus=$pdo->prepare("UPDATE tbl_transaction SET a_status='2', subject='Cancel Withdrawal', type='Credit' WHERE id=:id");
    $updateStatus->execute([':id'=>$tid]);
    if ($updateStatus) 
    {
        $newwallet_amount=$tamt+$amount;
        $updateAmount=$pdo->prepare("UPDATE user SET amount=amount+:amount WHERE userid=:userid");
        $updateAmount->execute([':amount'=>$tamt,':userid'=>$uid]);
        if ($updateAmount) 
        {
            echo "<script>alert('Withdrawal Cancel Successfully');window.location.assign('withdraw-history.php?type=2');</script>";
        }
    }  
?>