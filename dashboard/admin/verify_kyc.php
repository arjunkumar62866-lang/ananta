<?php 
ob_start(); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

$uid = trim($_GET['uid'] ?? '');

/* -----------------------------------------
   VERIFY BUTTON (PDO)
------------------------------------------ */
if (isset($_POST['submit']) && !empty($uid)) {

    $stmt = $pdo->prepare("UPDATE kyc SET status='1' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

    $stmt = $pdo->prepare("UPDATE user SET kyc='2' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

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

    header("Location: verify_kyc.php?uid=" . urlencode($uid));
    exit();
}

/* -----------------------------------------
   CANCEL BUTTON (PDO)
------------------------------------------ */
if (isset($_POST['cancel']) && !empty($uid)) {

    $stmt = $pdo->prepare("UPDATE user SET kyc='3' WHERE userid=:uid");
    $stmt->execute(['uid' => $uid]);

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

    header("Location: verify_kyc.php?uid=" . urlencode($uid));
    exit();
}

/* -----------------------------------------
   FETCH KYC RECORD DETAILS
------------------------------------------ */
$selectKyc = $pdo->prepare("SELECT * FROM kyc WHERE userid = :userid");
$selectKyc->execute(['userid' => $uid]);
$row = $selectKyc->fetch(PDO::FETCH_ASSOC) ?: [];

/* -----------------------------------------
   FETCH USER KYC STATUS
------------------------------------------ */
$q1 = $pdo->prepare("SELECT kyc, name, mobile FROM user WHERE userid = :userid");
$q1->execute(['userid' => $uid]);
$r1 = $q1->fetch(PDO::FETCH_ASSOC) ?: [];

$kycCode = (int)($r1['kyc'] ?? 0);
if ($kycCode == 0) {
    $k_status = "Not Submitted";
    $statusBadge = '<span class="badge badge-secondary px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px;">NOT SUBMITTED</span>';
} elseif ($kycCode == 1) {
    $k_status = "Pending";
    $statusBadge = '<span class="badge badge-warning px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px;">PENDING REVIEW</span>';
} elseif ($kycCode == 2) {
    $k_status = "Clear / Verified";
    $statusBadge = '<span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px;">VERIFIED & ACTIVE</span>';
} elseif ($kycCode == 3) {
    $k_status = "Rejected";
    $statusBadge = '<span class="badge badge-danger px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px;">REJECTED</span>';
} else {
    $k_status = "Unknown";
    $statusBadge = '<span class="badge badge-light border text-dark px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px;">UNKNOWN</span>';
}

$newmemberid = ($hmpre ?? 'AN') . $uid;

// Include header AFTER all POST handlers and redirects complete
include __DIR__ . '/common/header.php';
?>

<style>
.form-control, select.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 12px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    opacity: 1 !important;
    width: 100% !important;
    box-sizing: border-box !important;
    height: 44px !important;
}

.table-responsive {
    width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

.table-kyc-details td {
    padding: 14px 18px !important;
    vertical-align: middle !important;
    font-size: 14px !important;
    border-bottom: 1px solid #f1f5f9 !important;
}

.table-kyc-label {
    color: #475569 !important;
    font-weight: 700 !important;
    width: 35%;
    background: #f8fafc;
}

.table-kyc-value {
    color: #0f172a !important;
    font-weight: 600 !important;
}

@media (max-width: 767.98px) {
    .content-wrapper {
        padding-top: 75px !important;
        padding-bottom: 110px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
    .table-kyc-label {
        width: 45%;
        font-size: 13px !important;
    }
    .table-kyc-value {
        font-size: 13px !important;
    }
    .action-btn-group {
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
    }
    .action-btn-group .btn {
        width: 100% !important;
        margin: 0 !important;
    }
}
</style>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- HEADER BANNER -->
        <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
            <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">KYC AUDIT & VERIFICATION</span>
                    <h3 class="mb-1 text-white font-weight-bold">Member Bank KYC Verification</h3>
                    <p class="mb-0 text-white-50 small">Inspect member bank payout details, wallet addresses, and verify or reject KYC status.</p>
                </div>
                <div>
                    <a href="pending_kyc.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 100px;">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Pending List
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN KYC DETAILS CARD -->
        <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 font-weight-bold text-dark" style="font-size: 18px;">
                        <i class="fa fa-id-card text-primary mr-2"></i> KYC Record: <?php echo htmlspecialchars($newmemberid); ?>
                    </h5>
                    <span class="small text-muted font-weight-bold">Member Name: <?php echo htmlspecialchars($r1['name'] ?? $row['holder_name'] ?? 'N/A'); ?></span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="mr-2">
                        <span class="small text-muted font-weight-bold mr-1">Current Status:</span>
                        <?php echo $statusBadge; ?>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS BAR -->
            <div class="card-body p-4 border-bottom bg-light">
                <form method="post" class="d-flex align-items-center justify-content-between flex-wrap gap-3 action-btn-group m-0">
                    <div class="text-dark font-weight-bold small">
                        <i class="fa fa-info-circle text-info mr-1"></i> Click below to update KYC status for User <strong><?php echo htmlspecialchars($uid); ?></strong>:
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap action-btn-group">
                        <button type="submit" name="submit" class="btn btn-success font-weight-bold px-4 py-2" style="border-radius: 10px; background: #16a34a; border: none;" onclick="return confirm('Are you sure you want to APPROVE this KYC?');">
                            <i class="fa fa-check-circle mr-1"></i> Approve & Verify KYC
                        </button>
                        <button type="submit" name="cancel" class="btn btn-danger font-weight-bold px-4 py-2" style="border-radius: 10px; background: #dc2626; border: none;" onclick="return confirm('Are you sure you want to REJECT this KYC?');">
                            <i class="fa fa-times-circle mr-1"></i> Reject / Cancel KYC
                        </button>
                    </div>
                </form>
            </div>

            <!-- KYC DETAILS TABLE -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-items-center mb-0 table-kyc-details">
                        <tbody>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-user-circle text-secondary mr-2"></i> User ID / Member ID</td>
                                <td class="table-kyc-value"><strong class="text-primary"><?php echo htmlspecialchars($newmemberid); ?></strong> (System ID: <?php echo htmlspecialchars($uid); ?>)</td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-user text-secondary mr-2"></i> Account Holder Name</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['holder_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-credit-card text-secondary mr-2"></i> Bank Account Number</td>
                                <td class="table-kyc-value font-weight-bold text-dark"><?php echo htmlspecialchars($row['ac_number'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-university text-secondary mr-2"></i> Bank Name</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['bank'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-building text-secondary mr-2"></i> Branch Name</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['branch'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-code text-secondary mr-2"></i> IFSC Code</td>
                                <td class="table-kyc-value"><span class="badge badge-light border text-dark font-weight-bold px-2 py-1"><?php echo htmlspecialchars($row['ifsc'] ?? 'N/A'); ?></span></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-mobile text-secondary mr-2"></i> UPI ID / VPA</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['bhim'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-briefcase text-secondary mr-2"></i> BEP20 Wallet Address</td>
                                <td class="table-kyc-value">
                                    <?php 
                                        $stmtBep = $pdo->prepare("SELECT bep20_address FROM user WHERE userid = :uid");
                                        $stmtBep->execute(['uid' => $uid]);
                                        $bepVal = $stmtBep->fetchColumn();
                                        if (!empty($bepVal)):
                                    ?>
                                        <code style="background:#f1f5f9; color:#0f172a; padding:4px 8px; border-radius:6px; font-size:13px;"><?php echo htmlspecialchars($bepVal); ?></code>
                                    <?php else: ?>
                                        <span class="text-muted font-weight-normal">Not Provided</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-address-card-o text-secondary mr-2"></i> PAN Card Number</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['pan'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-id-badge text-secondary mr-2"></i> Aadhaar Number</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['mimo'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-file-image-o text-secondary mr-2"></i> Aadhaar Documents</td>
                                <td class="table-kyc-value">
                                    <?php if (!empty($row['adhar_front_img'])): ?>
                                        <a href="../user1/uploads/<?php echo htmlspecialchars($row['adhar_front_img']); ?>" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-1 mr-2 mb-1" style="border-radius:100px; font-size:12px;">
                                            <i class="fa fa-eye mr-1"></i> Aadhaar Front
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small font-weight-normal mr-2">Front: Not Uploaded</span>
                                    <?php endif; ?>

                                    <?php if (!empty($row['adhar_back_img'])): ?>
                                        <a href="../user1/uploads/<?php echo htmlspecialchars($row['adhar_back_img']); ?>" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-1 mb-1" style="border-radius:100px; font-size:12px;">
                                            <i class="fa fa-eye mr-1"></i> Aadhaar Back
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-file-image-o text-secondary mr-2"></i> PAN Card Document</td>
                                <td class="table-kyc-value">
                                    <?php if (!empty($row['pan_img'])): ?>
                                        <a href="../user1/uploads/<?php echo htmlspecialchars($row['pan_img']); ?>" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-1" style="border-radius:100px; font-size:12px;">
                                            <i class="fa fa-eye mr-1"></i> View PAN Card
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small font-weight-normal">Not Uploaded</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
</div>

<?php include __DIR__ . '/common/footer.php'; ?>
</body>
</html>
