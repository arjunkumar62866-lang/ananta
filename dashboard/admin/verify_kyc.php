<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php';
include('common/connection.php'); // MUST contain $pdo connection

$uid = $_GET['uid'];

/* -----------------------------------------
   VERIFY BUTTON (PDO)
------------------------------------------ */
if (isset($_POST['submit'])) {

    $stmt = $pdo->prepare("UPDATE kyc SET status='1' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

    $stmt = $pdo->prepare("UPDATE user SET kyc='2' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

    header("Location: verify_kyc.php?uid=".$uid);
    exit;
}

/* -----------------------------------------
   CANCEL BUTTON (PDO)
------------------------------------------ */
if (isset($_POST['cancel'])) {

    $stmt = $pdo->prepare("UPDATE user SET kyc='3' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

    header("Location: verify_kyc.php?uid=".$uid);
    exit;
}

/* -----------------------------------------
   FETCH KYC RECORD DETAILS
------------------------------------------ */
$selectKyc = $pdo->prepare("SELECT * FROM kyc WHERE userid = :userid");
$selectKyc->execute(['userid' => $uid]);
$row = $selectKyc->fetch(PDO::FETCH_ASSOC);

/* -----------------------------------------
   FETCH USER KYC STATUS
------------------------------------------ */
$q1 = $pdo->prepare("SELECT kyc FROM user WHERE userid = :userid");
$q1->execute(['userid' => $uid]);
$r1 = $q1->fetch(PDO::FETCH_ASSOC);

if ($r1['kyc'] == 0) {
    $k_status = "Not Submitted";
    $color = "#fff";
} elseif ($r1['kyc'] == 1) {
    $k_status = "Pending";
    $color = "#FEFC95";
} elseif ($r1['kyc'] == 2) {
    $k_status = "Clear";
    $color = "#C4FBC7";
} elseif ($r1['kyc'] == 3) {
    $k_status = "Rejected";
    $color = "red";
}

$newmemberid = $hmpre . $uid;
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

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3>KYC Detail (<?php echo $k_status; ?>)</h3></div>
                        <hr>
                    </div>

                    <!-- KYC STATUS BAR -->
                    <div class="card-header" >
                        <form method="post">
                            <input type="submit" class="btn btn-primary" value="Verify" name="submit" style="float: right;">
                            <input type="submit" class="btn btn-danger" value="Cancel" name="cancel" style="float: right; margin-right:10px;">
                        </form>
                    </div>

                    <!-- KYC DETAILS TABLE -->
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <tr>
                                    <td>User ID</td>
                                    <td><?php echo $newmemberid; ?></td>
                                </tr>
                                
                                <tr>
                                    <td>Tron Wallet Address</td>
                                    <td><?php echo $row['bit_coin']; ?></td>
                                </tr>

                                <tr>
                                    <td>A/C Holder Name</td>
                                    <td><?php echo $row['holder_name']; ?></td>
                                </tr>

                                <tr>
                                    <td>A/C Number</td>
                                    <td><?php echo $row['ac_number']; ?></td>
                                </tr>

                                <tr>
                                    <td>Bank</td>
                                    <td><?php echo $row['bank']; ?></td>
                                </tr>

                                <tr>
                                    <td>Branch</td>
                                    <td><?php echo $row['branch']; ?></td>
                                </tr>

                                <tr>
                                    <td>IFSC</td>
                                    <td><?php echo $row['ifsc']; ?></td>
                                </tr>

                                <tr>
                                    <td>ID Proof</td>
                                    <td><?php echo $row['idproof']; ?></td>
                                </tr>

                                <tr>
                                    <td>Card Number</td>
                                    <td><?php echo $row['card_no']; ?></td>
                                </tr>

                                <tr>
                                    <td>PAN Number</td>
                                    <td><?php echo $row['pan']; ?></td>
                                </tr>

                                <tr>
                                    <td>Google Pay</td>
                                    <td><?php echo $row['g_pay']; ?></td>
                                </tr>

                                <tr>
                                    <td>PhonePe</td>
                                    <td><?php echo $row['phone_pe']; ?></td>
                                </tr>

                                <tr>
                                    <td>Paytm</td>
                                    <td><?php echo $row['paytm']; ?></td>
                                </tr>

                                <tr>
                                    <td>Bhim UPI</td>
                                    <td><?php echo $row['bhim']; ?></td>
                                </tr>

                            </table>

                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="overlay toggle-menu"></div>
    </div>
</div>

<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

<?php include 'common/footer.php' ?>

</div><!-- wrapper -->

</body>
</html>
