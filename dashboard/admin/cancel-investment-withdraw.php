<?php
    include("common/connection.php");
    
    $uid=$_GET['uid'];
    $tid=$_GET['tid'];
    $tamt=$_GET['amt'];
    $inv_id=$_GET['investment_id'];
    
    $updateStatus=$pdo->prepare("UPDATE tbl_transaction SET a_status='2', subject='Cancel Withdrawal', type='Credit' WHERE id=:id");
    $updateStatus->execute([':id'=>$tid]);
    if ($updateStatus) 
    {
        
        $updateAmount=$pdo->prepare("UPDATE tbl_roi_one SET status=0 WHERE id=:iid");
        $updateAmount->execute([':iid'=>$inv_id]);
        if ($updateAmount) 
        {
            $updateAmount=$pdo->prepare("UPDATE user SET total_package=total_package+:amt WHERE userid=:uid");
            $updateAmount->execute([':amt'=>$tamt,
                                    ':uid'=>$uid]);
            echo "<script>alert('Withdrawal Cancel Successfully');window.location.assign('withdraw-history.php?type=2');</script>";
        }
    }  
?>