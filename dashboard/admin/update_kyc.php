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
	$holder_name   = trim($_POST['holder_name'] ?? '');
	$ac_number     = trim($_POST['ac_number'] ?? '');
	$bank          = trim($_POST['bank'] ?? '');
	$branch        = trim($_POST['branch'] ?? '');
	$ifsc          = strtoupper(trim($_POST['ifsc'] ?? ''));
	$upi_id        = trim($_POST['upi_id'] ?? '');
	$bep20_address = trim($_POST['bep20_address'] ?? '');
	$pan           = strtoupper(trim($_POST['pan'] ?? ''));
	$mimo          = trim($_POST['mimo'] ?? '');
	
	$updateKyc=$pdo->prepare("UPDATE kyc SET holder_name=:holder_name, ac_number=:ac_number, 
	bank=:bank, branch=:branch, ifsc=:ifsc, bhim=:bhim, pan=:pan, mimo=:mimo WHERE userid=:userid");
	$updateKyc->execute([
	        ':holder_name'=>$holder_name,
	        ':ac_number'=>$ac_number,
	        ':bank'=>$bank,
	        ':branch'=>$branch,
	        ':ifsc'=>$ifsc,
	        ':bhim'=>$upi_id,
	        ':pan'=>$pan,
	        ':mimo'=>$mimo,
	        ':userid'=>$uid
	    ]);

	$updateStatus=$pdo->prepare("UPDATE user SET kyc='1', bep20_address=:bep20 WHERE userid=:userid");
	$updateStatus->execute([':bep20'=>$bep20_address, ':userid'=>$uid]);
}
	 
$kycData=$pdo->prepare("SELECT * FROM kyc WHERE userid='$uid'");
$kycData->execute();
$row1=$kycData->fetch(PDO::FETCH_ASSOC);

$userBepData=$pdo->prepare("SELECT bep20_address FROM user WHERE userid='$uid'");
$userBepData->execute();
$adminBep20=$userBepData->fetchColumn() ?: '';
?>

<body class="bg-theme bg-theme1">

   <!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

 <!-- Start wrapper-->
 <div id="wrapper">

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
    } else {
		alert("Enter Pan No..");
	}
}  
</script>
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card border-0" style="background: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <div class="card-title text-center mb-3">
                            <h3 class="font-weight-bold text-dark">Update User KYC</h3>
                            <span class="badge px-3 py-2" style="background-color: <?php echo $color; ?>; color: #000; font-size: 13px; border-radius: 20px;">
                                Status: <?php echo htmlspecialchars($k_status); ?>
                            </span>
                        </div>
                        <hr style="border-top: 1px solid #e2e8f0;">

                        <?php if ($kyc == 1 || $kyc == 0 || $kyc == 3) { ?>
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>1. Account Holder Name</label>
                                            <input type="text" name="holder_name" value="<?php echo htmlspecialchars($row1['holder_name'] ?? ''); ?>" class="form-control" placeholder="Enter Account Holder Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>2. Account Number</label>
                                            <input type="text" name="ac_number" value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Account Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>4. Bank Name</label>
                                            <input type="text" name="bank" value="<?php echo htmlspecialchars($row1['bank'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>5. Branch Name</label>
                                            <input type="text" name="branch" value="<?php echo htmlspecialchars($row1['branch'] ?? ''); ?>" class="form-control" placeholder="Enter Branch Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>6. IFSC Code</label>
                                            <input type="text" name="ifsc" value="<?php echo htmlspecialchars($row1['ifsc'] ?? ''); ?>" class="form-control" placeholder="Enter IFSC Code">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>7. UPI ID</label>
                                            <input type="text" name="upi_id" value="<?php echo htmlspecialchars($row1['bhim'] ?? ''); ?>" class="form-control" placeholder="Enter UPI ID">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>8. BEP20 Wallet Address</label>
                                            <input type="text" name="bep20_address" value="<?php echo htmlspecialchars($adminBep20); ?>" class="form-control" placeholder="Enter BEP20 Wallet Address">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>9. PAN Card Number</label>
                                            <input type="text" name="pan" onblur="Validatepancard(this);" value="<?php echo htmlspecialchars($row1['pan'] ?? ''); ?>" class="form-control" placeholder="Enter PAN Card Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>10. Aadhaar Number</label>
                                            <input type="text" name="mimo" value="<?php echo htmlspecialchars($row1['mimo'] ?? ''); ?>" class="form-control" placeholder="Enter Aadhaar Number">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5 py-2" name="update" style="border-radius: 8px; font-weight: 600; background: #0284c7; border: none;">Submit</button>
                                </div>
                            </form>

                        <?php } else { ?>
                            <form>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>Account Holder Name</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['holder_name'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Account Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Bank Name</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['bank'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Branch Name</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['branch'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>IFSC Code</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['ifsc'] ?? ''); ?>" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>UPI ID</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['bhim'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>BEP20 Wallet Address</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($adminBep20); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>PAN Card Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['pan'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Aadhaar Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['mimo'] ?? ''); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>
