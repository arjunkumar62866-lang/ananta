<?php
   include("common/connection.php");

$tid=$_GET['tid'];

$transactionApproved=$pdo->prepare("UPDATE tbl_transaction SET a_status='1' WHERE id=:id");
$transactionApproved->execute(['id'=>$tid]);
 
// $sql = "update tbl_transaction SET a_status='1' where id='$tid'";
        
       
//     $result=mysqli_query($db,$sql);
if ($transactionApproved) {
    
     echo "<script>alert('Withdrawal Approved Succesfully.');window.location.assign('withdraw-history.php?type=1');</script>";
    
// header("location:manage-withdrawal?type=0");
  // $success="Gallery Updated successfully";   
}  
 
 
 
?>