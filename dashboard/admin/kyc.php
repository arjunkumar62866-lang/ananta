<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/common/header.php'; ?>
<?php
// Safely initialize $kyc variable from DB
$stmtUserKyc = $pdo->prepare("SELECT kyc FROM user WHERE userid = :userid");
$stmtUserKyc->execute([':userid' => $userid]);
$userKycVal = $stmtUserKyc->fetchColumn();
$kyc = ($userKycVal !== false && $userKycVal !== null) ? (int)$userKycVal : 0;

if ($kyc == 0) {
	$k_status = "Not Submitted";
	$color    = "#FF6C60";
} else if ($kyc == 1) {
	$k_status = "Pending";
	$color    = '#FEFC95';
} else if ($kyc == 2) {
	$k_status = "Clear";
	$color    = '#C4FBC7';
} else if ($kyc == 3) {
	$k_status = "Rejected";
	$color    = 'red';
} else {
	$k_status = "Not Submitted";
	$color    = "#FF6C60";
}


if (isset($_POST['update'])) {
    $bit_coin    = trim($_POST['bit_coin'] ?? '');
    $holder_name = trim($_POST['holder_name'] ?? '');
    $ac_number   = trim($_POST['ac_number'] ?? '');
    $bank        = trim($_POST['bank'] ?? '');
    $branch      = trim($_POST['branch'] ?? '');
    $ifsc        = trim($_POST['ifsc'] ?? '');
    $paytm       = trim($_POST['paytm'] ?? '');
    $phone_pe    = trim($_POST['phone_pe'] ?? '');
    $g_pay       = trim($_POST['g_pay'] ?? '');
    $mimo        = trim($_POST['mimo'] ?? '');
    $bhim        = trim($_POST['bhim'] ?? '');
    $idproof     = trim($_POST['idproof'] ?? '');
    $card_no     = trim($_POST['card_no'] ?? '');
    $pan         = trim($_POST['pan'] ?? '');  
    $nominee     = trim($_POST['nominee'] ?? '');  

    // Check if record exists in kyc table
    $chk = $pdo->prepare("SELECT COUNT(*) FROM kyc WHERE userid = :userid");
    $chk->execute([':userid' => $userid]);
    $exists = $chk->fetchColumn();

    if ($exists > 0) {
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
    } else {
        $insert = "INSERT INTO kyc (userid, bit_coin, holder_name, ac_number, bank, branch, ifsc, paytm, phone_pe, g_pay, mimo, bhim, idproof, card_no, pan, nominee) 
                   VALUES (:userid, :bit_coin, :holder_name, :ac_number, :bank, :branch, :ifsc, :paytm, :phone_pe, :g_pay, :mimo, :bhim, :idproof, :card_no, :pan, :nominee)";

        $stmt = $pdo->prepare($insert);
        $stmt->execute([
            ':userid'      => $userid,
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
            ':nominee'     => $nominee
        ]);
    }

    // Update user table status
    $stmt2 = $pdo->prepare("UPDATE user SET kyc = '1' WHERE userid = :userid");
    $stmt2->execute([':userid' => $userid]);
    $kyc = 1;
    $k_status = "Pending";
    $color = "#FEFC95";
}


$stmt = $pdo->prepare("SELECT * FROM kyc WHERE userid = :userid");
$stmt->execute([':userid' => $userid]);
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row1 || !is_array($row1)) {
    $row1 = [
        'bit_coin'    => '',
        'holder_name' => '',
        'ac_number'   => '',
        'bank'        => '',
        'branch'      => '',
        'ifsc'        => '',
        'paytm'       => '',
        'phone_pe'    => '',
        'g_pay'       => '',
        'mimo'        => '',
        'bhim'        => '',
        'idproof'     => '',
        'card_no'     => '',
        'pan'         => '',
        'nominee'     => ''
    ];
}

?>
<style>
.form-control, select.form-control, textarea.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    opacity: 1 !important;
}
.form-control:focus, select.form-control:focus, textarea.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
}
.form-control[readonly], .form-control:disabled, select.form-control:disabled {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border-color: #cbd5e1 !important;
}
select.form-control option {
    background-color: #ffffff !important;
    color: #0f172a !important;
}
label {
    font-weight: 600 !important;
    color: #334155 !important;
    margin-bottom: 6px !important;
}
</style>

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

<body class="bg-theme bg-theme1">

   <!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

 <!-- Start wrapper-->
 <div id="wrapper">

<div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card border-0" style="background: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <div class="card-title text-center mb-3">
                            <h3 class="font-weight-bold text-dark">Edit KYC</h3>
                            <span class="badge px-3 py-2" style="background-color: <?php echo $color; ?>; color: #000; font-size: 13px; border-radius: 20px;">
                                Status: <?php echo htmlspecialchars($k_status); ?>
                            </span>
                        </div>
                        <hr style="border-top: 1px solid #e2e8f0;">

                        <?php if ($kyc == 1 || $kyc == 0) { ?>
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>Tron Wallet Address</label>
                                            <input type="text" name="bit_coin" value="<?php echo htmlspecialchars($row1['bit_coin'] ?? ''); ?>" class="form-control" placeholder="Enter Tron wallet address">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>A/C Holder Name</label>
                                            <input type="text" name="holder_name" value="<?php echo htmlspecialchars($row1['holder_name'] ?? ''); ?>" class="form-control" placeholder="Enter Account Holder Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>A/C Number</label>
                                            <input type="text" name="ac_number" value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Account Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Bank Name</label>
                                            <input type="text" name="bank" value="<?php echo htmlspecialchars($row1['bank'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Google Pay</label>
                                            <input type="text" name="g_pay" value="<?php echo htmlspecialchars($row1['g_pay'] ?? ''); ?>" class="form-control" placeholder="Enter Google Pay Number/UPI">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>UPI BHIM</label>
                                            <input type="text" name="bhim" value="<?php echo htmlspecialchars($row1['bhim'] ?? ''); ?>" class="form-control" placeholder="Enter BHIM UPI ID">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>ID Proof</label>
                                            <select class="form-control" name="idproof">
                                                <?php $currProof = $row1['idproof'] ?? ''; ?>
                                                <?php if (empty($currProof)) { ?>
                                                    <option value="">-SELECT-</option>
                                                <?php } else { ?>
                                                    <option value="<?php echo htmlspecialchars($currProof); ?>"><?php echo htmlspecialchars($currProof); ?></option>
                                                <?php } ?>
                                                <option value="Adhaar Card">Adhaar Card</option>
                                                <option value="Voter Id">Voter Id</option>
                                                <option value="Passport">Passport</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>Branch Name</label>
                                            <input type="text" name="branch" value="<?php echo htmlspecialchars($row1['branch'] ?? ''); ?>" class="form-control" placeholder="Enter Branch Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>IFSC Code</label>
                                            <input type="text" name="ifsc" value="<?php echo htmlspecialchars($row1['ifsc'] ?? ''); ?>" class="form-control" placeholder="Enter IFSC Code">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>ID Card Number</label>
                                            <input type="text" name="card_no" value="<?php echo htmlspecialchars($row1['card_no'] ?? ''); ?>" class="form-control" placeholder="Enter ID Card Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Phone Pay</label>
                                            <input type="text" name="phone_pe" value="<?php echo htmlspecialchars($row1['phone_pe'] ?? ''); ?>" class="form-control" placeholder="Enter PhonePe Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Paytm</label>
                                            <input type="text" name="paytm" value="<?php echo htmlspecialchars($row1['paytm'] ?? ''); ?>" class="form-control" placeholder="Enter Paytm Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>PAN Card Number</label>
                                            <input type="text" name="pan" onblur="Validatepancard(this);" value="<?php echo htmlspecialchars($row1['pan'] ?? ''); ?>" class="form-control" placeholder="Enter PAN Card Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Nominee</label>
                                            <input type="text" name="nominee" value="<?php echo htmlspecialchars($row1['nominee'] ?? ''); ?>" class="form-control" placeholder="Enter Nominee Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Aadhar Number</label>
                                            <input type="text" name="mimo" value="<?php echo htmlspecialchars($row1['mimo'] ?? ''); ?>" class="form-control" placeholder="Enter Aadhar Card Number">
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
                                            <label>Tron Wallet Address</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['bit_coin'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>A/C Holder Name</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['holder_name'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>A/C Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Bank Name</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['bank'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Google Pay</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['g_pay'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>UPI BHIM</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['bhim'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>ID Proof</label>
                                            <select class="form-control" disabled>
                                                <?php $currProof = $row1['idproof'] ?? ''; ?>
                                                <?php if (empty($currProof)) { ?>
                                                    <option value="">-SELECT-</option>
                                                <?php } else { ?>
                                                    <option value="<?php echo htmlspecialchars($currProof); ?>"><?php echo htmlspecialchars($currProof); ?></option>
                                                <?php } ?>
                                                <option value="Adhaar Card">Adhaar Card</option>
                                                <option value="Voter Id">Voter Id</option>
                                                <option value="Passport">Passport</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>Branch Name</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['branch'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>IFSC Code</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['ifsc'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>ID Card Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['card_no'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Phone Pay</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['phone_pe'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Paytm</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['paytm'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>PAN Card Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['pan'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Nominee</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['nominee'] ?? ''); ?>" class="form-control">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Aadhar Number</label>
                                            <input type="text" readonly value="<?php echo htmlspecialchars($row1['mimo'] ?? ''); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div><!--End Row-->

        <div class="overlay toggle-menu"></div>

    </div>
</div>

<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

<?php include 'common/footer.php'; ?>

</div>
</body>
</html>
