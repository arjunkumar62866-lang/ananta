<?php ob_start();?>

<!DOCTYPE html>
<html lang="en">


<?php
session_start();
include("common/header.php"); 
include("common/connection.php");

if (!isset($_SESSION['withdraw_otp'])) {
    echo "<script>alert('Session Expired! Please try again.'); window.location='withdraw.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['withdraw_otp']) {

        

        $amount = $_SESSION['withdraw_amount'];
        $currentTime = $_SESSION['withdraw_time'];
        $userid = $_SESSION['userid'];

        // Fetch user amount
        $stmt = $pdo->prepare("SELECT amount FROM user WHERE userid = :userid");
        $stmt->execute([':userid' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $useramount = $row['amount'];

        // -- Withdrawal Logic Run Here --
        $shoppingamt = ($amount * 8) / 100;
        $useramountleft = $useramount - $amount;
        $leftamount = $amount - $shoppingamt;

        // Insert transaction
        $sql = "INSERT INTO tbl_transaction 
                    (amount, act_amount, user_id, subject, type, status, a_status, created_date, time)
                VALUES 
                    (:amount, :act_amount, :user_id, 'Withdrawal Request', 'Debit', '1', '0', CURDATE(), :time)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':amount' => $leftamount,
            ':act_amount' => $amount,
            ':user_id' => $userid,
            ':time' => $currentTime
        ]);

        // Update user wallet
        $sql1 = "UPDATE user 
                    SET amount = :useramountleft, 
                        shop_amount = shop_amount + :shoppingamt 
                WHERE userid = :userid AND status = '1' AND active = '1'";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':useramountleft' => $useramountleft,
            ':shoppingamt' => $shoppingamt,
            ':userid' => $userid
        ]);

        // clear otp session
        unset($_SESSION['withdraw_otp']);
        unset($_SESSION['withdraw_amount']);
        unset($_SESSION['withdraw_time']);

        echo "<script>alert('Withdrawal Request Sent Successfully'); window.location='withdraw.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>





<body class="bg-theme bg-theme1">

<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

 <!--Start sidebar-wrapper-->

   <!--End sidebar-wrapper-->
  

<!--Start topbar header-->

<!--End topbar header-->
<div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h4>An OTP will sent to your email address</h4></div>
                        <hr>
                        <form method="POST">
                            <input type="text" name="otp" placeholder="Enter OTP sent on email : <?php echo $useremail;?>" required class="form-control mt-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 mt-2">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div><!--End Row-->

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

    </div>
    <!-- End container-fluid-->
</div>
<!--End content-wrapper-->
   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	<!--Start footer-->
	<?php include 'common/footer.php' ?>
	<!--End footer-->
	
	
   
  </div><!--End wrapper-->

	
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/forms.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:55 GMT -->
</html>
