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

    require_once __DIR__ . '/../user1/common/db_method.php';
    if (function_exists('createUserNotification')) {
        createUserNotification(
            $uid,
            'KYC',
            'KYC Application Approved',
            "Your Bank KYC verification has been APPROVED by Admin. Your bank payout details are now active.",
            $uid,
            $pdo
        );
    }

    header("Location: verify_kyc.php?uid=".$uid);
    exit;
}

/* -----------------------------------------
   CANCEL BUTTON (PDO)
------------------------------------------ */
if (isset($_POST['cancel'])) {

    $stmt = $pdo->prepare("UPDATE user SET kyc='3' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

    require_once __DIR__ . '/../user1/common/db_method.php';
    if (function_exists('createUserNotification')) {
        createUserNotification(
            $uid,
            'KYC',
            'KYC Application Rejected',
            "Your Bank KYC application has been REJECTED by Admin. Please re-check your details and resubmit.",
            $uid,
            $pdo
        );
    }

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
                                    <td class="font-weight-bold" style="width: 30%;">User ID</td>
                                    <td><?php echo htmlspecialchars($newmemberid); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">A/C Holder Name</td>
                                    <td><?php echo htmlspecialchars($row['holder_name'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">A/C Number</td>
                                    <td><?php echo htmlspecialchars($row['ac_number'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">Bank Name</td>
                                    <td><?php echo htmlspecialchars($row['bank'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">Branch Name</td>
                                    <td><?php echo htmlspecialchars($row['branch'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">IFSC Code</td>
                                    <td><?php echo htmlspecialchars($row['ifsc'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">UPI ID</td>
                                    <td><?php echo htmlspecialchars($row['bhim'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">BEP20 Wallet Address</td>
                                    <td><?php 
                                        $stmtBep = $pdo->prepare("SELECT bep20_address FROM user WHERE userid = :uid");
                                        $stmtBep->execute(['uid' => $uid]);
                                        $bepVal = $stmtBep->fetchColumn();
                                        echo htmlspecialchars($bepVal ?: 'N/A');
                                    ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">PAN Card Number</td>
                                    <td><?php echo htmlspecialchars($row['pan'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">Aadhaar Number</td>
                                    <td><?php echo htmlspecialchars($row['mimo'] ?? ''); ?></td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">Aadhaar Card Upload</td>
                                    <td>
                                        <?php if (!empty($row['adhar_front_img'])): ?>
                                            <a href="../user1/uploads/<?php echo htmlspecialchars($row['adhar_front_img']); ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fa fa-file-image-o mr-1"></i> View Aadhaar Front
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Not Uploaded</span>
                                        <?php endif; ?>

                                        <?php if (!empty($row['adhar_back_img'])): ?>
                                            <a href="../user1/uploads/<?php echo htmlspecialchars($row['adhar_back_img']); ?>" target="_blank" class="btn btn-sm btn-info ml-2">
                                                <i class="fa fa-file-image-o mr-1"></i> View Aadhaar Back
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold">PAN Card Upload</td>
                                    <td>
                                        <?php if (!empty($row['pan_img'])): ?>
                                            <a href="../user1/uploads/<?php echo htmlspecialchars($row['pan_img']); ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fa fa-file-image-o mr-1"></i> View PAN Card
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Not Uploaded</span>
                                        <?php endif; ?>
                                    </td>
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
