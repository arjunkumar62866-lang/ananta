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
</script><style>

/* =========================================================
   KYC PAGE — PREMIUM CLEAN UI
========================================================= */

html,
body {
    min-height: 100%;
}

body.ananta-user-dashboard {
    background: #f6f8fb !important;
    color: #111827 !important;
}

/* Main content */
.kyc-page {
    width: 100%;
    min-height: calc(100vh - 80px);
    background: #f6f8fb;
    padding: 22px 24px 100px;
}

/* Main Card */
.kyc-card {
    width: 100%;
    max-width: none;

    margin: 0;

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 22px;

    box-shadow:
        0 10px 35px rgba(15, 23, 42, 0.07),
        0 2px 8px rgba(15, 23, 42, 0.03);

    overflow: hidden;
}

/* =========================================================
   HEADER
========================================================= */

.kyc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 25px 30px;

    border-bottom: 1px solid #e5eaf0;

    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #fbfdff 65%,
        #f4fbf7 100%
    );
}

.kyc-title-wrapper {
    display: flex;
    align-items: center;

    gap: 15px;
}

.kyc-header-icon {
    width: 52px;
    height: 52px;

    min-width: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 15px;

    background: linear-gradient(
        135deg,
        rgba(11, 94, 215, 0.10),
        rgba(34, 164, 71, 0.12)
    );

    border: 1px solid rgba(11, 94, 215, 0.10);

    color: #0B5ED7;

    font-size: 21px;
}

.kyc-title {
    margin: 0;

    color: #111827 !important;

    font-size: 22px;

    font-weight: 800;

    letter-spacing: -0.3px;
}

.kyc-subtitle {
    margin: 4px 0 0;

    color: #64748b !important;

    font-size: 13px;

    font-weight: 500;
}

/* Status */
.kyc-status {
    display: inline-flex;

    align-items: center;

    padding: 8px 14px;

    border-radius: 999px;

    background: #eff6ff;

    color: #0B5ED7 !important;

    border: 1px solid #dbeafe;

    font-size: 11px;

    font-weight: 800;

    white-space: nowrap;
}

/* =========================================================
   FORM
========================================================= */

.kyc-form-area {
    padding: 30px;
}

.kyc-grid {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 20px 26px;
}

/* Form group */
.kyc-field {
    width: 100%;
}

/* IMPORTANT:
   Force labels BLACK so inherited white text
   doesn't make them invisible.
*/

.kyc-field label {
    display: block;

    margin-bottom: 8px;

    color: #111827 !important;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 0.55px;

    text-transform: uppercase;
}

/* Inputs */
.kyc-field .form-control,
.kyc-field select.form-control {

    width: 100%;

    height: 49px;

    padding: 0 14px;

    border-radius: 11px !important;

    border: 1px solid #d7dee8 !important;

    background: #ffffff !important;

    color: #111827 !important;

    font-size: 14px;

    font-weight: 600;

    box-shadow: none !important;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.kyc-field .form-control:focus,
.kyc-field select.form-control:focus {

    border-color: #0B5ED7 !important;

    background: #ffffff !important;

    color: #111827 !important;

    box-shadow:
        0 0 0 3px rgba(11, 94, 215, 0.08) !important;
}

/* Placeholder */
.kyc-field .form-control::placeholder {
    color: #94a3b8 !important;
}

/* Select */
.kyc-field select.form-control {
    cursor: pointer;
}

/* Readonly */
.kyc-field .form-control[readonly] {

    background: #f8fafc !important;

    color: #334155 !important;
}

/* =========================================================
   ACCOUNT NUMBER WARNING
========================================================= */

#passwordWarning {

    display: block;

    margin-top: 6px;

    color: #dc2626 !important;

    font-size: 11px !important;

    font-weight: 700;
}

/* =========================================================
   SUBMIT BUTTON
========================================================= */

.kyc-submit-area {

    grid-column: 1 / -1;

    display: flex;

    justify-content: center;

    padding-top: 10px;
}

.kyc-submit-btn {

    min-width: 210px;

    height: 48px;

    padding: 0 28px;

    border: none !important;

    border-radius: 12px !important;

    background: linear-gradient(
        135deg,
        #0B5ED7 0%,
        #0788c9 50%,
        #22A447 100%
    ) !important;

    color: #ffffff !important;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: 0.3px;

    box-shadow:
        0 8px 20px rgba(11, 94, 215, 0.20);

    transition: all 0.2s ease;
}

.kyc-submit-btn:hover {

    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 12px 26px rgba(11, 94, 215, 0.28);
}

.kyc-submit-btn:disabled {

    opacity: 0.55;

    transform: none;

    cursor: not-allowed !important;
}

/* =========================================================
   FORCE ALL KYC TEXT VISIBILITY
========================================================= */

.kyc-card h1,
.kyc-card h2,
.kyc-card h3,
.kyc-card h4,
.kyc-card h5,
.kyc-card h6,
.kyc-card p,
.kyc-card label,
.kyc-card span,
.kyc-card input,
.kyc-card select,
.kyc-card option {

    /* Inputs/button override their own styles below */
    color: #111827;
}

.kyc-card .kyc-subtitle {
    color: #64748b !important;
}

.kyc-card .kyc-status {
    color: #0B5ED7 !important;
}

.kyc-card .kyc-submit-btn {
    color: #ffffff !important;
}

/* =========================================================
   REMOVE OLD COLORFUL BACKGROUND
========================================================= */

.content-wrapper:has(.kyc-page),
.content-wrapper:has(.kyc-page) .container-fluid {
    background: #f6f8fb !important;
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 991px) {

    .kyc-page {
        padding: 18px 18px 95px;
    }

    .kyc-header {
        padding: 22px;
    }

    .kyc-form-area {
        padding: 22px;
    }

    .kyc-grid {
        gap: 18px;
    }
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 767px) {

    .kyc-page {
        padding: 12px 10px 90px;
    }

    .kyc-card {
        border-radius: 17px;
    }

    .kyc-header {

        align-items: flex-start;

        padding: 18px;

        flex-direction: column;
    }

    .kyc-title-wrapper {
        width: 100%;
    }

    .kyc-header-icon {

        width: 45px;
        height: 45px;

        min-width: 45px;

        border-radius: 13px;

        font-size: 18px;
    }

    .kyc-title {
        font-size: 18px;
    }

    .kyc-subtitle {
        font-size: 11px;
    }

    .kyc-status {
        font-size: 10px;

        padding: 7px 12px;
    }

    .kyc-form-area {
        padding: 18px;
    }

    .kyc-grid {

        grid-template-columns: 1fr;

        gap: 16px;
    }

    .kyc-submit-area {
        grid-column: 1;
    }

    .kyc-submit-btn {
        width: 100%;
    }
}

/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {

    .kyc-page {
        padding: 8px 7px 85px;
    }

    .kyc-header {
        padding: 16px;
    }

    .kyc-form-area {
        padding: 14px;
    }

    .kyc-title {
        font-size: 17px;
    }

    .kyc-field .form-control,
    .kyc-field select.form-control {
        height: 47px;

        font-size: 13px;
    }

    .kyc-field label {
        font-size: 10px;
    }
}

</style>


<body class="ananta-user-dashboard">

<div id="wrapper">

    <div class="clearfix"></div>


    <!-- =====================================================
         KYC CONTENT
    ====================================================== -->

    <div class="content-wrapper">

        <div class="kyc-page">

            <div class="kyc-card">


                <!-- =================================================
                     KYC HEADER
                ================================================== -->

                <div class="kyc-header">

                    <div class="kyc-title-wrapper">

                        <div class="kyc-header-icon">
                            <i class="fa fa-id-card"></i>
                        </div>

                        <div>

                            <h3 class="kyc-title">
                                Update KYC Details
                            </h3>

                            <p class="kyc-subtitle">
                                Bank account &amp; verification documents
                            </p>

                        </div>

                    </div>


                    <div>

                        <span class="kyc-status">

                            STATUS:
                            <?php echo $k_status; ?>

                        </span>

                    </div>

                </div>


                <!-- =================================================
                     KYC FORM
                ================================================== -->

                <div class="kyc-form-area">

                    <?php if (1 == 1) { ?>

                    <form
                        method="post"
                        enctype="multipart/form-data"
                        id="registration_form"
                    >

                        <div class="kyc-grid">


                            <!-- =====================================
                                 LEFT COLUMN
                            ====================================== -->

                            <div class="kyc-field">

                                <label>
                                    Tron Wallet Address
                                </label>

                                <input
                                    type="text"
                                    name="bit_coin"
                                    value="<?php echo $row1['bit_coin']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    A/C Holder Name
                                </label>

                                <input
                                    type="text"
                                    name="holder_name"
                                    value="<?php echo $row1['holder_name']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    A/C Number
                                </label>

                                <input
                                    type="text"
                                    name="ac_number"
                                    id="ac_number1"
                                    value="<?php echo $row1['ac_number']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Confirm A/C Number
                                </label>

                                <input
                                    type="text"
                                    name="ac_number"
                                    id="ac_number2"
                                    value="<?php echo $row1['ac_number']; ?>"
                                    class="form-control"
                                >

                                <span id="passwordWarning"></span>

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Bank Name
                                </label>

                                <input
                                    type="text"
                                    name="bank"
                                    value="<?php echo $row1['bank']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Google Pay
                                </label>

                                <input
                                    type="text"
                                    name="g_pay"
                                    value="<?php echo $row1['g_pay']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    UPI BHIM
                                </label>

                                <input
                                    type="text"
                                    name="bhim"
                                    value="<?php echo $row1['bhim']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    ID Proof
                                </label>

                                <select
                                    class="form-control"
                                    name="idproof"
                                >

                                    <?php if ($row1['idproof'] == '') { ?>

                                        <option value="">
                                            -SELECT-
                                        </option>

                                    <?php } else { ?>

                                        <option value="<?php echo $row1['idproof']; ?>">
                                            <?php echo $row1['idproof']; ?>
                                        </option>

                                    <?php } ?>

                                    <option value="Adhaar Card">
                                        Adhaar Card
                                    </option>

                                    <option value="Voter Id">
                                        Voter Id
                                    </option>

                                    <option value="Passport">
                                        Passport
                                    </option>

                                </select>

                            </div>


                            <!-- =====================================
                                 RIGHT COLUMN
                            ====================================== -->

                            <div class="kyc-field">

                                <label>
                                    Branch Name
                                </label>

                                <input
                                    type="text"
                                    name="branch"
                                    value="<?php echo $row1['branch']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    IFSC Code
                                </label>

                                <input
                                    type="text"
                                    name="ifsc"
                                    value="<?php echo $row1['ifsc']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    ID Card Number
                                </label>

                                <input
                                    type="text"
                                    name="card_no"
                                    value="<?php echo $row1['card_no']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Phone Pay
                                </label>

                                <input
                                    type="text"
                                    name="phone_pe"
                                    value="<?php echo $row1['phone_pe']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Paytm
                                </label>

                                <input
                                    type="text"
                                    name="paytm"
                                    value="<?php echo $row1['paytm']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    PAN Card Number
                                </label>

                                <input
                                    type="text"
                                    name="pan"
                                    onblur="Validatepancard(this);"
                                    value="<?php echo $row1['pan']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Nominee
                                </label>

                                <input
                                    type="text"
                                    name="nominee"
                                    value="<?php echo $row1['nominee']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <div class="kyc-field">

                                <label>
                                    Aadhar Number
                                </label>

                                <input
                                    type="text"
                                    name="mimo"
                                    value="<?php echo $row1['mimo']; ?>"
                                    class="form-control"
                                >

                            </div>


                            <!-- =====================================
                                 SUBMIT
                            ====================================== -->

                            <div class="kyc-submit-area">

                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="kyc-submit-btn"
                                    name="update"
                                >

                                    <i class="fa fa-check-circle me-1"></i>

                                    UPDATE KYC DETAILS

                                </button>

                            </div>

                        </div>

                    </form>


                    <?php } else { ?>

                    <!-- READ ONLY KYC STATE -->

                    <form>

                        <div class="kyc-grid">

                            <div class="kyc-field">
                                <label>Tron Wallet Address</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['bit_coin']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>A/C Holder Name</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['holder_name']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>A/C Number</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['ac_number']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>Bank Name</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['bank']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>Google Pay</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['g_pay']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>UPI BHIM</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['bhim']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>ID Proof</label>

                                <select
                                    class="form-control"
                                    disabled
                                >

                                    <?php if ($row1['idproof'] == '') { ?>

                                        <option value="">
                                            -SELECT-
                                        </option>

                                    <?php } else { ?>

                                        <option value="<?php echo $row1['idproof']; ?>">
                                            <?php echo $row1['idproof']; ?>
                                        </option>

                                    <?php } ?>

                                    <option value="Adhaar Card">
                                        Adhaar Card
                                    </option>

                                    <option value="Voter Id">
                                        Voter Id
                                    </option>

                                    <option value="Passport">
                                        Passport
                                    </option>

                                </select>
                            </div>

                            <div class="kyc-field">
                                <label>Branch Name</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['branch']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>IFSC Code</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['ifsc']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>ID Card Number</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['card_no']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>Phone Pay</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['phone_pe']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>Paytm</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['paytm']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>PAN Card Number</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['pan']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>Nominee</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['nominee']; ?>"
                                    class="form-control"
                                >
                            </div>

                            <div class="kyc-field">
                                <label>Aadhar Number</label>
                                <input
                                    type="text"
                                    readonly
                                    value="<?php echo $row1['mimo']; ?>"
                                    class="form-control"
                                >
                            </div>

                        </div>

                    </form>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>


    <!-- Back To Top -->
    <a
        href="javaScript:void();"
        class="back-to-top"
    >
        <i class="fa fa-angle-double-up"></i>
    </a>


    <!-- Footer -->
    <?php include 'common/footer.php' ?>


</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script src="assets/js/sidebar-menu.js"></script>

<script src="assets/js/app-script.js"></script>


<script>

$(document).ready(function () {

    $('#registration_form').on('submit keyup', function (e) {

        const pass1 = $('#ac_number1').val();
        const pass2 = $('#ac_number2').val();

        if (pass1 !== pass2) {

            $('#passwordWarning')
                .text('A/C no. not match');

            $('#submitBtn')
                .attr('disabled', true)
                .css('cursor', 'not-allowed');

            if (e.type === 'submit') {
                e.preventDefault();
            }

            return;
        }

        $('#passwordWarning').text('');

        $('#submitBtn')
            .removeAttr('disabled')
            .css('cursor', 'pointer');

    });

});

</script>

</body>
</html>