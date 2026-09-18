<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>



<?php
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $title=$_POST["title"];
    $tr_id=$_POST["tr_id"];
    $order_id=$_POST["order_id"];
    $mode=$_POST["mode"];
    $amount=$_POST["amount"];
    $tr_date=$_POST["tr_date"];
    $remark=$_POST["remark"];
    $subject=$title." ".$userid;
    date_default_timezone_set('Asia/Kolkata');
    
    $time=date('h i:a');
    $date=date('Y-m-d');
    
    $selectCheckTxnId=$pdo->prepare("SELECT * FROM tbl_payment WHERE tr_id=:tr_id");
    $selectCheckTxnId->execute([':tr_id'=>$tr_id]);
    if($selectCheckTxnId->fetch())
    {
        echo "<script>alert('This Transaction Id Is Already Exist');window.location.assign('fund-request.php');</script>"; 
    }
    else
    {
        $insertRequestPayment=$pdo->prepare("INSERT INTO tbl_payment(userid,tr_id,mode,subject,image,amount,remark,plantype,date,time,status)
        VALUES(:userid,:tr_id,:mode,:subject,:image,:amount,:remark,:plantype,:date,:time,:status)");
        $insertRequestPayment->execute([
                ':userid'=>$userid,
                ':tr_id'=>$tr_id,
                ':mode'=>$mode,
                ':subject'=>$subject,
                ':image'=>'',
                ':amount'=>$amount,
                ':remark'=>$remark,
                ':plantype'=>'',
                ':date'=>$date,
                ':time'=>$time,
                ':status'=>0
            ]);
        if($insertRequestPayment)
        {
            echo "<script>alert('Fund Request Generated Successfully Done.')</script>";
        }
        else
        {
            echo "<script>alert('Fund Request Not Generated Successfully.')</script>";
        }
    }
}
?>





<body class="bg-theme bg-theme1">

    <!-- start loader -->
    <div id="pageloader-overlay" class="visible incoming">
        <div class="loader-wrapper-outer">
            <div class="loader-wrapper-inner">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <!-- end loader -->

    <!-- Start wrapper-->
    <div id="wrapper">

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <h4 class="text-center mb-4">Send Payment Request</h4>

                <form method="POST" enctype="multipart/form-data">
                    <h6> 
                        <?php 
                        if(isset($_GET['responce']))
                        { 
                            if($_GET['responce'] =="SUCCESS")
                            { 
                                echo "Withdrawal Request Send Successfuly";
                            }
                            else 
                            {
                                echo "Something went worng pls contact admin for more details!!!";
                            } 
                        } 
                        ?></h6>
                        <h6 style="text-align: center;color: red;">  <?php if(isset($error)){ echo $error;} ?></h6>
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!--<div class="form-group">-->
                            <!--<label for="input-7">Payment Request For</label>-->
                            <!--    <select class="form-control" required="" name="title" id="price">-->
                            <!--        <option value="">--Select Request--</option>-->
                            <!--        <option value="Fund Request">Fund Request</option>-->
                            <!--        <option value="Shopping Fund Request">Shopping Fund Request</option>-->
                            <!--    </select>   -->
                            <!--</div>-->

                            <div class="form-group">
					        <label for="input-7">Payment Request For</label>
					        <input type="text"  name="title" class="form-control" id="price"  value="Fund Request" readonly>
					    </div>
					    
                            <div class="form-group">
					        <label for="input-7">Transaction Id</label>
					        <input type="text"  name="tr_id" class="form-control" id="fname"  required>
					    </div>
					    <div class="form-group">
        				    <label for="input-7">Order Id</label>
        					<input type="text"  name="order_id" class="form-control" id="fname"  required>
        				</div>
					    <div class="form-group">
                            <label for="input-7">Select Mode Of Transaction</label>
                                <?php
                                //  $sqlpack="SELECT * FROM  tbl_package  where status='1' ";
                                // $resultpack=mysqli_query($db,$sqlpack);
                                // if(mysqli_num_rows($resultpack)>0); 
                                    ?>
                                <select class="form-control" required="" name="mode" id="price">
                                   <option value="">--Select Mode--</option>
                                   <option value="Cash">--Cash--</option>
                                   <option value="Google Pay">--Google Pay--</option>
                                   <option value="Phone Pay">--Phone Pay--</option>
                                   <option value="UPI">--UPI--</option>
                                   <option value="Paytm">--Paytm--</option>
                                   <option value="IMPS">--IMPS--</option>
                                   <option value="NEFT">--NEFT--</option>
                                </select>   
                         </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
					        <label for="input-7">Amount</label>
					        <input type="number"  name="amount"   class="form-control" id="fname"  required>
                         </div>
    					    <div class="form-group">
					        <label for="input-7">Date</label>
					        <input type="date"  name="tr_date"   class="form-control" id="fname"  required>
                         </div>
					     <div class="form-group">
					        <label for="input-7">Remark</label>
					            <textarea type="text"  name="remark" rows="5" columns="40" class="form-control" id="fname"  required></textarea>
    					 </div>
                        </div>
                    </div>
                    <div class="form-group">
					       <center><button type="submit" value="submit" name="submit" class="btn btn-primary shadow-primary px-5"><i class="fa fa-lock"></i> Proceed</button></center>
					   </div>
                </form>

            </div>
        </div><!--End content-wrapper-->

        <!--Start Back To Top Button-->
        <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

        <!--Start footer-->
        <?php include 'common/footer.php' ?>

    </div><!--End wrapper-->

</body>

<!-- Mirrored from themewagon.github.io/dashtreme/profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:59 GMT -->

</html>