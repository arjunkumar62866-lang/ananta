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

                <div class="row mt-3">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="card shadow-lg border-0" style="border-radius: 20px; background: #ffffff;">
                            <div class="card-body p-4 p-md-5">

                                <div class="mb-4 pb-3 border-bottom text-center text-md-left">
                                    <h4 class="font-weight-bold text-dark mb-1" style="color: #0f172a;">Request Fund Deposit</h4>
                                    <p class="text-muted small mb-0">Submit payment details and transaction proof for fund approval</p>
                                </div>

                                <form method="POST" enctype="multipart/form-data">
                                    <?php if(isset($_GET['responce'])) { ?>
                                        <div class="alert alert-info border-0 mb-4" style="border-radius: 12px;">
                                            <?php echo ($_GET['responce'] =="SUCCESS") ? "Withdrawal/Fund Request Sent Successfully" : "Something went wrong. Please contact admin!"; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if(isset($error)){ ?>
                                        <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px; background: #fef2f2; color: #991b1b;">
                                            <?php echo $error; ?>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Payment Request For</label>
                                                <input type="text" name="title" class="form-control font-weight-bold" id="price" value="Fund Request" readonly style="border-radius: 12px; border-color: #cbd5e1; height: 48px; background: #f8fafc;">
                                            </div>
                                            
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Transaction ID</label>
                                                <input type="text" name="tr_id" class="form-control" id="fname" placeholder="Enter Transaction Txn ID" required style="border-radius: 12px; border-color: #cbd5e1; height: 48px;">
                                            </div>
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Order ID</label>
                                                <input type="text" name="order_id" class="form-control" id="fname" placeholder="Enter Reference Order ID" required style="border-radius: 12px; border-color: #cbd5e1; height: 48px;">
                                            </div>
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Select Mode Of Transaction</label>
                                                <select class="form-control" required="" name="mode" id="price" style="border-radius: 12px; border-color: #cbd5e1; height: 48px;">
                                                   <option value="">-- Select Mode --</option>
                                                   <option value="Cash">Cash</option>
                                                   <option value="Google Pay">Google Pay</option>
                                                   <option value="Phone Pay">Phone Pay</option>
                                                   <option value="UPI">UPI</option>
                                                   <option value="Paytm">Paytm</option>
                                                   <option value="IMPS">IMPS</option>
                                                   <option value="NEFT">NEFT</option>
                                                </select>   
                                            </div>
                                        </div>
                                        
                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Amount ($ / ₹)</label>
                                                <input type="number" name="amount" class="form-control" id="fname" placeholder="Enter Amount" required style="border-radius: 12px; border-color: #cbd5e1; height: 48px;">
                                            </div>
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Transaction Date</label>
                                                <input type="date" name="tr_date" class="form-control" id="fname" required style="border-radius: 12px; border-color: #cbd5e1; height: 48px;">
                                            </div>
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Remark</label>
                                                <textarea name="remark" rows="4" class="form-control" id="fname" placeholder="Enter any notes or remarks..." required style="border-radius: 12px; border-color: #cbd5e1;"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-block font-weight-bold text-white shadow-sm mt-3" style="border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border: none; height: 50px; font-size: 16px;">
                                        <i class="zmdi zmdi-upload me-1"></i> Submit Payment Request
                                    </button>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>

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