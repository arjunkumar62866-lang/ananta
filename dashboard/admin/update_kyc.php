<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php';
$uid=$_GET['uid'];

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
if(isset($_POST['update'])){
    // echo "<script>alert('$uid');</script>";
   
	$holder_name=$_POST['holder_name'];
	$ac_number=$_POST['ac_number'];
	$bank=$_POST['bank'];
	$branch=$_POST['branch'];
	$ifsc=$_POST['ifsc'];
	$paytm=$_POST['paytm'];
	$phone_pe=$_POST['phone_pe'];
	$g_pay=$_POST['g_pay'];
	$bit_coin=$_POST['bit_coin'];
	$bhim=$_POST['bhim'];
	$idproof=$_POST['idproof'];
	$card_no=$_POST['card_no'];
	$pan=$_POST['pan'];  
	$nominee=$_POST['nominee'];
	$mimo=$_POST['mimo'];
	
// $adhar_front_img_name=$_FILES['adhar_front_img']['name'];
// 		$adhar_front_img_size  =$_FILES['adhar_front_img']['size'];
// 		$adhar_front_img_type  =$_FILES['adhar_front_img']['type'];
// 		if($adhar_front_img_name !=''){
// 		  $upload=move_uploaded_file($_FILES['adhar_front_img']['tmp_name'],'images/'.$_FILES['adhar_front_img']['name']);
// 		  mysqli_query($con,"update kyc set adhar_front_img='$adhar_front_img_name' where userid='$userid'");
// 		}

// 			$adhar_back_img_name=$_FILES['adhar_back_img']['name'];
// 		$adhar_back_img_size  =$_FILES['adhar_back_img']['size'];
// 		$adhar_back_img_type  =$_FILES['adhar_back_img']['type'];
// 		if($adhar_back_img_name !=''){
// 		  $upload=move_uploaded_file($_FILES['adhar_back_img']['tmp_name'],'images/'.$_FILES['adhar_back_img']['name']);
// 		  mysqli_query($con,"update kyc set adhar_back_img='$adhar_back_img_name' where userid='$userid'");
// 		}

// 			$pan_img_name=$_FILES['pan_img']['name'];
// 		$pan_img_size  =$_FILES['pan_img']['size'];
// 		$pan_img_type  =$_FILES['pan_img']['type'];
// 		if($pan_img_name !=''){
// 		  $upload=move_uploaded_file($_FILES['pan_img']['tmp_name'],'images/'.$_FILES['pan_img']['name']);
// 		  mysqli_query($con,"update kyc set pan_img='$pan_img_name' where userid='$userid'");
// 		}
 
// 		$bank_img_name=$_FILES['bank_img']['name'];
// 		$bank_img_size  =$_FILES['bank_img']['size'];
// 		$bank_img_type  =$_FILES['bank_img']['type'];
// 		if($bank_img_name !=''){
// 		  $upload=move_uploaded_file($_FILES['bank_img']['tmp_name'],'images/'.$_FILES['bank_img']['name']);
// 		  mysqli_query($con,"update kyc set bank_img='$bank_img_name' where userid='$userid'");
// 		}
		
// 	$update="UPDATE `kyc` SET `bit_coin`='$bit_coin',`holder_name`='$holder_name',`ac_number`='$ac_number',`bank`='$bank',`branch`='$branch', 
// 	`ifsc`='$ifsc',`paytm`='',`phone_pe`='', `bhim`='',`idproof`='$idproof',`card_no`='$card_no',`pan`='$pan',`paytm`='$paytm',
// 	`phone_pe`='$phone_pe',`g_pay`='$g_pay',`bhim`='$bhim',`nominee`='$nominee',mimo='$mimo' WHERE userid='$uid'";
	$updateKyc=$pdo->prepare("UPDATE kyc SET bit_coin=:bit_coin, holder_name=:holder_name, ac_number=:ac_number, 
	bank=:bank, branch=:branch, ifsc=:ifsc, paytm=:paytm, phone_pe=:phone_pe, bhim=:bhim, idproof=:idproof, 
	card_no=:card_no, pan=:pan, paytm=:paytm, phone_pe=:phone_pe, g_pay=:g_pay, bhim=:bhim, nominee=:nominee, mimo=:mimo WHERE userid=:userid");
	$updateKyc->execute([
	        ':bit_coin'=>$bit_coin,
	        ':holder_name'=>$holder_name,
	        ':ac_number'=>$ac_number,
	        ':bank'=>$bank,
	        ':branch'=>$branch,
	        ':ifsc'=>$ifsc,
	        ':paytm'=>'',
	        ':phone_pe'=>'',
	        ':bhim'=>'',
	        ':idproof'=>$idproof,
	        ':card_no'=>$card_no,
	        ':pan'=>$pan,
	        ':paytm'=>$paytm,
	        ':phone_pe'=>$phone_pe,
	        ':g_pay'=>$g_pay,
	        ':bhim'=>$bhim,
	        ':nominee'=>$nominee,
	        ':mimo'=>$mimo,
	        ':userid'=>$uid
	    ]);
// 	$query_update=mysqli_query($pdo,$update);
// 	mysqli_query($pdo,"update user set kyc='1' where userid='$uid'");
	$updateStatus=$pdo->prepare("UPDATE user SET kyc='1' WHERE userid=:userid");
	$updateStatus->execute([':userid'=>$uid]);
}
	 
// $query1=mysqli_query($con,"select * from kyc where userid='$uid'");
// $row1=mysqli_fetch_array($query1);

$kycData=$pdo->prepare("SELECT * FROM kyc WHERE userid='$uid'");
$kycData->execute();
$row1=$kycData->fetch(PDO::FETCH_ASSOC);
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
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3>Update KYC</h3></div>
                        <hr>
                        <div class="row">
                 
                    <div class="col-md-6">
          <div class="tile">
            <!--<h3 class="tile-title text-center">Edit Profile</h3>-->
            <div class="tile-body">
             <form method="post" enctype="multipart/form-data">
                 <div class="form-group">
                  <label class="control-label">Tron Wallet Address</label>
                  <input type="text" name="bit_coin" value="<?php echo $row1['bit_coin']; ?>" class="form-control" >
                </div>
                <div class="form-group">
                  <label class="control-label">A/C HOLDER NAME</label>
                  <input type="text" name="holder_name" value="<?php echo $row1['holder_name']; ?>" class="form-control" >
                </div>
                <div class="form-group">
                  <label class="control-label">A/C NUMBER</label>
                  <input type="text" name="ac_number" value="<?php echo $row1['ac_number']; ?>" class="form-control" >
                </div>
                 <div class="form-group">
                  <label class="control-label">BANK NAME</label>
                  <input type="text" name="bank" value="<?php echo $row1['bank']; ?>"  class="form-control" >
                </div>
                  <div class="form-group">
                  <label class="control-label">GOOGLE PAY</label>
                  <input type="text" name="g_pay" value="<?php echo $row1['g_pay']; ?>" class="form-control" >
                </div>
                  <div class="form-group">
                  <label class="control-label">UPI BHIM</label>
                  <input type="text" name="bhim" value="<?php echo $row1['bhim']; ?>" class="form-control" >
                </div>
                 <div class="form-group">
    <label for="exampleFormControlSelect2">ID Proof</label>
    
    <select class="form-control" id="exampleFormControlSelect2"  name="idproof" >
<?php if($row1['idproof']==''){ ?>
	  <option value="" class="form-control">--SELECT--</option>
	<?php }else{ ?>
	<option  value="<?php echo $row1['idproof']; ?>" class="form-control"><?php echo $row1['idproof']; ?></option>
	<?php } ?>
	  <option value="Adhaar Card" class="form-control">Adhaar card</option>
	  <option value="Voter Id" class="form-control">Voter Id</option>
	  <option value="Passport" class="form-control">Passport</option>
                </select>
  </div>
               
                     </div>
                
              
            </div>
        </div>
          <div class="col-md-6">
          <div class="tile">
            <!--<h3 class="tile-title text-center">Edit Profile</h3>-->
            <div class="tile-body">
             
                <div class="form-group">
                  <label class="control-label">BRANCH NAME</label>
                  <input type="text" name="branch" value="<?php echo $row1['branch']; ?>" class="form-control" >
                </div>
                <div class="form-group">
                  <label class="control-label">IFSC CODE</label>
                  <input type="text" name="ifsc" value="<?php echo $row1['ifsc']; ?>"  class="form-control" >
                </div>
                 <div class="form-group">
                  <label class="control-label">ID CARD NUMBER</label>
                  <input type="text" name="card_no" value="<?php echo $row1['card_no']; ?>" class="form-control" >
                </div>
           
                 <div class="form-group">
                  <label class="control-label">PHONE PAY</label>
                  <input type="text" name="phone_pe" value="<?php echo $row1['phone_pe']; ?>" class="form-control" >
                </div>
                     </div>
                <div class="form-group">
                  <label class="control-label">PAYTM</label>
                  <input type="text" name="paytm" value="<?php echo $row1['paytm']; ?>" class="form-control" >
                </div>
                  <div class="form-group">
                  <label class="control-label">PAN CARD NUMBER</label>
                  <input type="text" name="pan" onblur="Validatepancard(this);" value="<?php echo $row1['pan']; ?>" class="form-control" >
                </div>
                 <div class="form-group">
                  <label class="control-label"> NOMINEE</label>
                  <input type="text" name="nominee" onblur="Validatepancard(this);" value="<?php echo $row1['nominee']; ?>" class="form-control" >
                </div>
                
                 <div class="form-group">
                  <label class="control-label"> Aadhar Number</label>
                  <input type="text" name="mimo" value="<?php echo $row1['mimo']; ?>" class="form-control" >
                </div>
               
              
            </div>
            
          </div>
        </div>
        <div class="tile text-center">
                     <div class="form-group">
                  
                   <div class="tile-footer">
              <button class="btn btn-primary" type="submit" value="submit" name="update">Submit</button>
            </div>
                </div>
              </div>
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
