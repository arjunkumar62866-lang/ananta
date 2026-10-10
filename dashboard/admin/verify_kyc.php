<?php 
ob_start(); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

$uid = trim($_GET['uid'] ?? '');
if (empty($uid)) {
    header("Location: completed_kyc.php");
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
$q1 = $pdo->prepare("SELECT kyc, name, mobile, bep20_address FROM user WHERE userid = :userid");
$q1->execute(['userid' => $uid]);
$r1 = $q1->fetch(PDO::FETCH_ASSOC) ?: [];

// Auto-verify if user has submitted details and status is not yet set
$hasDetails = (!empty($row['ac_number']) || !empty($row['holder_name']) || !empty($row['pan']) || !empty($row['mimo']));
if ($hasDetails) {
    if (($row['status'] ?? 0) != 1 || ($r1['kyc'] ?? 0) != 2) {
        $pdo->prepare("UPDATE kyc SET status = '1' WHERE userid = :uid")->execute([':uid' => $uid]);
        $pdo->prepare("UPDATE user SET kyc = '2' WHERE userid = :uid")->execute([':uid' => $uid]);
        $row['status'] = 1;
        $r1['kyc'] = 2;
    }
}

$kycCode = (int)($r1['kyc'] ?? 0);
if ($hasDetails || $kycCode == 2) {
    $k_status = "Verified & Active";
    $statusBadge = '<span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px; background:#16a34a !important; color:#ffffff !important;"><i class="fa fa-check-circle mr-1"></i> VERIFIED & ACTIVE</span>';
} else {
    $k_status = "Not Submitted";
    $statusBadge = '<span class="badge badge-secondary px-3 py-1 font-weight-bold" style="border-radius:100px; font-size:12px; background:#64748b !important; color:#ffffff !important;"><i class="fa fa-clock-o mr-1"></i> NOT SUBMITTED</span>';
}

$newmemberid = ($hmpre ?? 'AN') . $uid;
?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* Clean High-Contrast Admin Layout */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-admin-dashboard,
body.bg-theme,
body.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

#wrapper {
    background: #f4f6f8 !important;
    min-height: 100vh !important;
}

.content-wrapper {
    background-color: #f4f6f8 !important;
    padding-top: 85px !important;
    padding-bottom: 60px !important;
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
    color: #334155 !important;
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
}
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- HEADER BANNER -->
        <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
            <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700; background:#ffffff; color:#0284c7 !important;">KYC AUDIT & VERIFICATION</span>
                    <h3 class="mb-1 text-white font-weight-bold">Member Bank KYC Verification</h3>
                    <p class="mb-0 text-white-50 small">Automatic verification enabled. Review member bank details, PAN and Aadhaar records.</p>
                </div>
                <div>
                    <a href="completed_kyc.php" class="btn btn-light font-weight-bold px-3 py-2 text-dark" style="border-radius: 100px; background:#ffffff !important; color:#0f172a !important; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                        <i class="fa fa-arrow-left mr-1 text-primary"></i> Back to KYC Records
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN KYC DETAILS CARD -->
        <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 font-weight-bold text-dark" style="font-size: 18px; color:#0f172a !important;">
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

            <!-- AUTO-VERIFIED STATUS BAR (NO MANUAL APPROVAL REQUIRED) -->
            <div class="card-body p-4 border-bottom bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-check-circle text-success" style="font-size: 24px;"></i>
                    <div>
                        <div class="text-dark font-weight-bold small" style="color:#0f172a !important;">
                            Auto-Verified Status: <?php echo ($hasDetails || $kycCode == 2) ? '<span class="text-success">Verified & Active</span>' : '<span class="text-secondary">Awaiting Member Submission</span>'; ?>
                        </div>
                        <div class="text-muted small">
                            Admin approval is not required. Member KYC is automatically verified upon entering valid details.
                        </div>
                    </div>
                </div>
                <div>
                    <a href="update_kyc.php?uid=<?php echo urlencode($uid); ?>" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 10px; background: #0284c7; border: none; color:#ffffff !important;">
                        <i class="fa fa-pencil mr-1"></i> Edit / Update Details
                    </a>
                </div>
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
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['holder_name'] ?? $r1['name'] ?? 'N/A'); ?></td>
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
                                <td class="table-kyc-value"><span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="color:#0f172a !important; background:#f1f5f9;"><?php echo htmlspecialchars($row['ifsc'] ?? 'N/A'); ?></span></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-mobile text-secondary mr-2"></i> UPI ID / VPA</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['bhim'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-briefcase text-secondary mr-2"></i> BEP20 Wallet Address</td>
                                <td class="table-kyc-value">
                                    <?php 
                                        $bepVal = $r1['bep20_address'] ?? '';
                                        if (!empty($bepVal)):
                                    ?>
                                        <code style="background:#f1f5f9; color:#0f172a; padding:4px 8px; border-radius:6px; font-size:13px; font-weight:700;"><?php echo htmlspecialchars($bepVal); ?></code>
                                    <?php else: ?>
                                        <span class="text-muted font-weight-normal">Not Provided</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-address-card-o text-secondary mr-2"></i> PAN Card Number</td>
                                <td class="table-kyc-value"><span class="font-weight-bold text-dark" style="color:#0f172a !important;"><?php echo htmlspecialchars($row['pan'] ?? 'N/A'); ?></span></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-id-badge text-secondary mr-2"></i> Aadhaar Number</td>
                                <td class="table-kyc-value"><span class="font-weight-bold text-dark" style="color:#0f172a !important;"><?php echo htmlspecialchars($row['mimo'] ?? 'N/A'); ?></span></td>
                            </tr>
                            <tr>
                                <td class="table-kyc-label"><i class="fa fa-calendar text-secondary mr-2"></i> Updated Date</td>
                                <td class="table-kyc-value"><?php echo htmlspecialchars($row['updated_at'] ?? 'N/A'); ?></td>
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
