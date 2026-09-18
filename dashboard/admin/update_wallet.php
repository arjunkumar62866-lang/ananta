<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ;
$userid=$_GET['uid'];

if($kyc==0){
	$k_status="Not Submitted";
	$color="#FF6C60";
}else if($kyc==1){
	$k_status="Pending";
	$color='#FEFC95';
}else if($kyc==2){
	$k_status="Clear";
	$color='#C4FBC7';
}else if($kyc==3){
	$k_status="Rejected";
	$color='red';
}

if(isset($_POST['update']))
{
    $bit_coin=$_POST['bit_coin'];
    $kycUpdate=$pdo->prepare("UPDATE kyc SET bit_coin=:bit_coin WHERE userid=:userid");
    $kycUpdate->execute([':bit_coin'=>$bit_coin, ':userid'=>$userid]);
    
    $kycStatus=$pdo->prepare("UPDATE user SET kyc='1' WHERE userid=:userid");
    $kycStatus->execute([':userid'=>$userid]);
    
    if($kycStatus)
    {
		echo '<script>alert("Wallet Address Updated Successfully");</script>';	
	}
	else
	{
		echo "<script>alert('Wallet Address not updated');</script>";
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

         <main class="app-content">
      <!--<div class="app-title">-->
      <!--  <div>-->
      <!--    <h1><i class="fa fa-edit"></i> Form Samples</h1>-->
      <!--    <p>Sample forms</p>-->
      <!--  </div>-->
      <!--  <ul class="app-breadcrumb breadcrumb">-->
      <!--    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>-->
      <!--    <li class="breadcrumb-item">Forms</li>-->
      <!--    <li class="breadcrumb-item"><a href="#">Sample Forms</a></li>-->
      <!--  </ul>-->
      <!--</div>-->
        <div class="row">
          <div class="col-md-12 p-4">
            <div class="tile text-center">
                    <h3 class="tile-title ">Update Wallet</h3>
              </div>
          <div class="tile">
              <div class="row">
              <div class="col-md-3"></div>  
              <div class="col-md-6">
               		   	    <form method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Wallet Address only Lunc Coin(Terra Classic)</label>
                    <input type="text" name="bit_coin" value="<?php echo $row1['bit_coin']; ?>" required class="form-control" id="exampleInputEmail1"  aria-describedby="emailHelp" placeholder="Wallet Address">
                  </div>
                  <!--<div class="form-group">-->
                  <!--  <label for="exampleInputPassword1">Please Enter OTP</label>-->
                  <!--  <input type="number" name="otp_email"   placeholder="Enter OTP" required  class="form-control" id="exampleInputPassword1"  placeholder="Please Enter OTP">-->
                  <!--</div>-->
                  
                  
                   <div class="tile-footer text-center">
              <button class="btn btn-primary" type="submit" value="submit" name="update">Update</button>
            </div>
                </form>
                
            <!--    <form method="post" enctype="multipart/form-data">-->
            <!--      <div class="tile-footer text-center">-->
            <!--  <button class="btn btn-primary" type="submit" value="submit" name="otpsend"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send OTP on mail</button>-->
            <!--</div>-->
                  
            <!--     	</form> -->
                  
              </div>
              <div class="col-md-3"></div>
              </div>
              
            <!--<div class="tile-body">Create a beautiful dashboard</div>-->
          </div>
        </div>
      </div>
    </main>

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
