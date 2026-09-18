<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ?>
<?php
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


if (isset($_POST['update'])) {
    $bit_coin    = $_POST['bit_coin'];
    $holder_name = $_POST['holder_name'];
    $ac_number   = $_POST['ac_number'];
    $bank        = $_POST['bank'];
    $branch      = $_POST['branch'];
    $ifsc        = $_POST['ifsc'];
    $paytm       = $_POST['paytm'];
    $phone_pe    = $_POST['phone_pe'];
    $g_pay       = $_POST['g_pay'];
    $mimo        = $_POST['mimo'];
    $bhim        = $_POST['bhim'];
    $idproof     = $_POST['idproof'];
    $card_no     = $_POST['card_no'];
    $pan         = $_POST['pan'];  
    $nominee     = $_POST['nominee'];  

    // Update kyc table
    $update = "UPDATE kyc 
               SET bit_coin = :bit_coin,
                   holder_name = :holder_name,
                   ac_number = :ac_number,
                   bank = :bank,
                   branch = :branch,
                   ifsc = :ifsc,
                   paytm = :paytm,
                   phone_pe = :phone_pe,
                   g_pay = :g_pay,
                   mimo = :mimo,
                   bhim = :bhim,
                   idproof = :idproof,
                   card_no = :card_no,
                   pan = :pan,
                   nominee = :nominee
               WHERE userid = :userid";

    $stmt = $pdo->prepare($update);
    $stmt->execute([
        ':bit_coin'    => $bit_coin,
        ':holder_name' => $holder_name,
        ':ac_number'   => $ac_number,
        ':bank'        => $bank,
        ':branch'      => $branch,
        ':ifsc'        => $ifsc,
        ':paytm'       => $paytm,
        ':phone_pe'    => $phone_pe,
        ':g_pay'       => $g_pay,
        ':mimo'        => $mimo,
        ':bhim'        => $bhim,
        ':idproof'     => $idproof,
        ':card_no'     => $card_no,
        ':pan'         => $pan,
        ':nominee'     => $nominee,
        ':userid'      => $userid
    ]);

    // Update user table
    $stmt2 = $pdo->prepare("UPDATE user SET kyc = '1' WHERE userid = :userid");
    $stmt2->execute([':userid' => $userid]);
echo "<script>alert('Kyc updated');window.location.href = 'kyc.php';</script>";
}


$stmt = $pdo->prepare("SELECT * FROM kyc WHERE userid = :userid");
$stmt->execute([':userid' => $userid]);
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);


?>
<script>
function Validatepancard(thisField) {  
          if (thisField.value != "") {
			thisFieldVal = thisField.value;
            var panPat = /^([a-zA-Z]{5})(\d{4})([a-zA-Z]{1})$/;
            if (thisFieldVal.search(panPat) == -1) {
                alert("Invalid Pan No");
                
                return false;
            }
        }else{
		alert("Enter Pan No..");
		}
  }  
</script>
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
                        <div class="card-title text-center"><h3>Edit KYC</h3></div>
                        <hr>

                        <!--<php if ($kyc == 1) { ?>-->
                        <?php if (1 == 1) { ?>
                            <form method="post" enctype="multipart/form-data" id="registration_form">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Tron Wallet Address</label>
                                            <input type="text" name="bit_coin" value="<?php echo $row1['bit_coin']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>A/C Holder Name</label>
                                            <input type="text" name="holder_name" value="<?php echo $row1['holder_name']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>A/C Number</label>
                                            <input type="text" name="ac_number" id="ac_number1" value="<?php echo $row1['ac_number']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm A/C Number</label>
                                            <input type="text" name="ac_number" id="ac_number2" value="<?php echo $row1['ac_number']; ?>" class="form-control">
                                        <span id="passwordWarning" style="color: red; font-size: 14px;"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Bank Name</label>
                                            <input type="text" name="bank" value="<?php echo $row1['bank']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Google Pay</label>
                                            <input type="text" name="g_pay" value="<?php echo $row1['g_pay']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>UPI BHIM</label>
                                            <input type="text" name="bhim" value="<?php echo $row1['bhim']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>ID Proof</label>
                                            <select class="form-control" name="idproof">
                                                <?php if ($row1['idproof'] == '') { ?>
                                                    <option value="">-SELECT-</option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $row1['idproof']; ?>"><?php echo $row1['idproof']; ?></option>
                                                <?php } ?>
                                                <option value="Adhaar Card">Adhaar Card</option>
                                                <option value="Voter Id">Voter Id</option>
                                                <option value="Passport">Passport</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Branch Name</label>
                                            <input type="text" name="branch" value="<?php echo $row1['branch']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>IFSC Code</label>
                                            <input type="text" name="ifsc" value="<?php echo $row1['ifsc']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>ID Card Number</label>
                                            <input type="text" name="card_no" value="<?php echo $row1['card_no']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Phone Pay</label>
                                            <input type="text" name="phone_pe" value="<?php echo $row1['phone_pe']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Paytm</label>
                                            <input type="text" name="paytm" value="<?php echo $row1['paytm']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>PAN Card Number</label>
                                            <input type="text" name="pan" onblur="Validatepancard(this);" value="<?php echo $row1['pan']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Nominee</label>
                                            <input type="text" name="nominee" value="<?php echo $row1['nominee']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Aadhar Number</label>
                                            <input type="text" name="mimo" value="<?php echo $row1['mimo']; ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center mt-3">
                                    <button type="submit" id="submitBtn" class="btn btn-primary px-5" name="update">Submit</button>
                                </div>
                            </form>

                        <?php } else { ?>
                            <form>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Tron Wallet Address</label>
                                            <input type="text" readonly value="<?php echo $row1['bit_coin']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>A/C Holder Name</label>
                                            <input type="text" readonly value="<?php echo $row1['holder_name']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>A/C Number</label>
                                            <input type="text" readonly value="<?php echo $row1['ac_number']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Bank Name</label>
                                            <input type="text" readonly value="<?php echo $row1['bank']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Google Pay</label>
                                            <input type="text" readonly value="<?php echo $row1['g_pay']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>UPI BHIM</label>
                                            <input type="text" readonly value="<?php echo $row1['bhim']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>ID Proof</label>
                                            <select class="form-control" disabled>
                                                <?php if ($row1['idproof'] == '') { ?>
                                                    <option value="">-SELECT-</option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $row1['idproof']; ?>"><?php echo $row1['idproof']; ?></option>
                                                <?php } ?>
                                                <option value="Adhaar Card">Adhaar Card</option>
                                                <option value="Voter Id">Voter Id</option>
                                                <option value="Passport">Passport</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Branch Name</label>
                                            <input type="text" readonly value="<?php echo $row1['branch']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>IFSC Code</label>
                                            <input type="text" readonly value="<?php echo $row1['ifsc']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>ID Card Number</label>
                                            <input type="text" readonly value="<?php echo $row1['card_no']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Phone Pay</label>
                                            <input type="text" readonly value="<?php echo $row1['phone_pe']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Paytm</label>
                                            <input type="text" readonly value="<?php echo $row1['paytm']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>PAN Card Number</label>
                                            <input type="text" readonly value="<?php echo $row1['pan']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Nominee</label>
                                            <input type="text" readonly value="<?php echo $row1['nominee']; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Aadhar Number</label>
                                            <input type="text" readonly value="<?php echo $row1['mimo']; ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>
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
    <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>

<script>
  $(document).ready(function () {
    $('#registration_form').on('submit keyup', function (e) {
      const pass1 = $('#ac_number1').val();
      const pass2 = $('#ac_number2').val();
      

      if (pass1 !== pass2) {
        $('#passwordWarning').text('A/C no. not match');
        $('#submitBtn').attr('disabled', true).css('cursor', 'not-allowed');
        if (e.type === 'submit') e.preventDefault();
        return;
      }
      
      
      $('#passwordWarning').text('');
      $('#submitBtn').removeAttr('disabled').css('cursor', 'pointer');
    });
  });
  </script>
	
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/forms.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:55 GMT -->
</html>
