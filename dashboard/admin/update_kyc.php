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

$successMsg = '';
$errorMsg   = '';

// Handle Admin KYC Update
if (isset($_POST['update'])) {
    $holder_name   = trim($_POST['holder_name'] ?? '');
    $ac_number     = trim($_POST['ac_number'] ?? '');
    $bank          = trim($_POST['bank'] ?? '');
    $branch        = trim($_POST['branch'] ?? '');
    $ifsc          = strtoupper(trim($_POST['ifsc'] ?? ''));
    $upi_id        = trim($_POST['upi_id'] ?? '');
    $bep20_address = trim($_POST['bep20_address'] ?? '');
    $pan           = strtoupper(trim($_POST['pan'] ?? ''));
    $mimo          = trim($_POST['mimo'] ?? ''); // Aadhaar Number

    if (!empty($ifsc) && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) {
        $errorMsg = "Invalid IFSC Code format. Example: SBIN0001234";
    } elseif (!empty($pan) && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan)) {
        $errorMsg = "Invalid PAN Card Number format. Example: ABCDE1234F";
    } elseif (!empty($mimo) && !preg_match('/^[0-9]{12}$/', str_replace(' ', '', $mimo))) {
        $errorMsg = "Aadhaar Number must be exactly 12 digits.";
    } else {
        $cleanMimo = str_replace(' ', '', $mimo);

        // Check if record exists in kyc table
        $chk = $pdo->prepare("SELECT COUNT(*) FROM kyc WHERE userid = :uid");
        $chk->execute([':uid' => $uid]);
        if ($chk->fetchColumn() > 0) {
            $updateKyc = $pdo->prepare("UPDATE kyc SET 
                holder_name = :holder_name, 
                ac_number   = :ac_number, 
                bank        = :bank, 
                branch      = :branch, 
                ifsc        = :ifsc, 
                bhim        = :bhim, 
                pan         = :pan, 
                mimo        = :mimo, 
                status      = '1' 
                WHERE userid = :userid");
            $updateKyc->execute([
                ':holder_name' => $holder_name,
                ':ac_number'   => $ac_number,
                ':bank'        => $bank,
                ':branch'      => $branch,
                ':ifsc'        => $ifsc,
                ':bhim'        => $upi_id,
                ':pan'         => $pan,
                ':mimo'        => $cleanMimo,
                ':userid'      => $uid
            ]);
        } else {
            $insertKyc = $pdo->prepare("INSERT INTO kyc (
                userid, holder_name, ac_number, bank, branch, ifsc, bhim, pan, mimo, status
            ) VALUES (
                :userid, :holder_name, :ac_number, :bank, :branch, :ifsc, :bhim, :pan, :mimo, '1'
            )");
            $insertKyc->execute([
                ':userid'      => $uid,
                ':holder_name' => $holder_name,
                ':ac_number'   => $ac_number,
                ':bank'        => $bank,
                ':branch'      => $branch,
                ':ifsc'        => $ifsc,
                ':bhim'        => $upi_id,
                ':pan'         => $pan,
                ':mimo'        => $cleanMimo
            ]);
        }

        // Automatically set user kyc = '2' (Verified & Active)
        $updateStatus = $pdo->prepare("UPDATE user SET kyc = '2', bep20_address = :bep20 WHERE userid = :userid");
        $updateStatus->execute([':bep20' => $bep20_address, ':userid' => $uid]);

        if (function_exists('createUserNotification')) {
            createUserNotification(
                $uid,
                'KYC',
                'KYC Details Updated & Verified',
                'Your Bank & Identity KYC details have been updated and auto-verified successfully.',
                $uid,
                $pdo
            );
        }

        $successMsg = "Member KYC details updated and automatically verified successfully!";
    }
}

// Fetch fresh KYC data
$kycData = $pdo->prepare("SELECT * FROM kyc WHERE userid = :uid");
$kycData->execute([':uid' => $uid]);
$row1 = $kycData->fetch(PDO::FETCH_ASSOC) ?: [];

// Fetch user data
$userStmt = $pdo->prepare("SELECT name, kyc, bep20_address FROM user WHERE userid = :uid");
$userStmt->execute([':uid' => $uid]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$kycStatusVal = (int)($userData['kyc'] ?? 0);
$adminBep20   = $userData['bep20_address'] ?? '';
$memberName   = $userData['name'] ?? ($row1['holder_name'] ?? 'N/A');

$hasSubmitted = (!empty($row1['ac_number']) || !empty($row1['holder_name']) || !empty($row1['pan']) || !empty($row1['mimo']));
if ($hasSubmitted || $kycStatusVal == 2) {
    $k_status = "Verified & Active";
    $statusColor = "#16a34a";
} else {
    $k_status = "Not Submitted";
    $statusColor = "#64748b";
}

$newmemberid = ($hmpre ?? 'AN') . $uid;
?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
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

.form-control, select.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    opacity: 1 !important;
    height: 44px !important;
}

.form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}

label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 13.5px !important;
    margin-bottom: 6px !important;
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
                    <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700; background:#ffffff; color:#0284c7 !important;">ADMIN KYC CONTROLLER</span>
                    <h3 class="mb-1 text-white font-weight-bold">Update & Auto-Verify KYC: <?php echo htmlspecialchars($newmemberid); ?></h3>
                    <p class="mb-0 text-white-50 small">Submitting this form immediately marks the member as Verified & Active. No admin approval required.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="verify_kyc.php?uid=<?php echo urlencode($uid); ?>" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 100px; background:#ffffff !important; color:#0f172a !important;">
                        <i class="fa fa-eye mr-1 text-primary"></i> View Record
                    </a>
                    <a href="completed_kyc.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 100px; background:#ffffff !important; color:#0f172a !important;">
                        <i class="fa fa-arrow-left mr-1 text-success"></i> All Records
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success font-weight-bold mb-4" style="border-radius: 12px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46;">
                <i class="fa fa-check-circle mr-2"></i> <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger font-weight-bold mb-4" style="border-radius: 12px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b;">
                <i class="fa fa-exclamation-triangle mr-2"></i> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <!-- FORM CARD -->
        <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 font-weight-bold text-dark" style="font-size: 18px; color:#0f172a !important;">
                        Member: <?php echo htmlspecialchars($memberName); ?> (ID: <?php echo htmlspecialchars($uid); ?>)
                    </h5>
                    <span class="small text-muted font-weight-bold">Automatic KYC Verification Form</span>
                </div>
                <div>
                    <span class="badge px-3 py-2 font-weight-bold" style="background-color: <?php echo $statusColor; ?>; color: #ffffff !important; font-size: 12px; border-radius: 100px;">
                        STATUS: <?php echo htmlspecialchars($k_status); ?>
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="post">
                    <div class="row">
                        <!-- Left Column: Bank Details -->
                        <div class="col-lg-6">
                            <h6 class="font-weight-bold mb-3 text-primary"><i class="fa fa-university mr-2"></i> Bank Payout Details</h6>
                            
                            <div class="form-group mb-3">
                                <label>1. Account Holder Name</label>
                                <input type="text" name="holder_name" value="<?php echo htmlspecialchars($row1['holder_name'] ?? $memberName); ?>" class="form-control" placeholder="Enter Account Holder Name" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>2. Bank Account Number</label>
                                <input type="text" name="ac_number" value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Account Number" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>3. Bank Name</label>
                                <input type="text" name="bank" value="<?php echo htmlspecialchars($row1['bank'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Name" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>4. Branch Name</label>
                                <input type="text" name="branch" value="<?php echo htmlspecialchars($row1['branch'] ?? ''); ?>" class="form-control" placeholder="Enter Branch Name">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>5. IFSC Code</label>
                                <input type="text" name="ifsc" value="<?php echo htmlspecialchars($row1['ifsc'] ?? ''); ?>" class="form-control" placeholder="Enter IFSC Code (e.g. SBIN0001234)" required>
                            </div>
                        </div>

                        <!-- Right Column: Digital & Identity -->
                        <div class="col-lg-6">
                            <h6 class="font-weight-bold mb-3 text-primary"><i class="fa fa-id-card mr-2"></i> UPI & Identity Numbers</h6>
                            
                            <div class="form-group mb-3">
                                <label>6. UPI ID / VPA</label>
                                <input type="text" name="upi_id" value="<?php echo htmlspecialchars($row1['bhim'] ?? ''); ?>" class="form-control" placeholder="Enter UPI ID (e.g. user@upi)">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>7. BEP20 Wallet Address (USDT)</label>
                                <input type="text" name="bep20_address" value="<?php echo htmlspecialchars($adminBep20); ?>" class="form-control" placeholder="Enter BEP20 Wallet Address (0x...)">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>8. PAN Card Number</label>
                                <input type="text" name="pan" value="<?php echo htmlspecialchars($row1['pan'] ?? ''); ?>" class="form-control" placeholder="Enter PAN Number (e.g. ABCDE1234F)" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>9. Aadhaar Number (12 Digits)</label>
                                <input type="text" name="mimo" value="<?php echo htmlspecialchars($row1['mimo'] ?? ''); ?>" class="form-control" placeholder="Enter 12-digit Aadhaar Number" required>
                            </div>

                            <div class="p-3 bg-light rounded-lg border mt-3 text-muted small">
                                <i class="fa fa-info-circle text-info mr-1"></i> Document image uploads are disabled. Aadhaar and PAN card numbers are verified directly upon submission.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-top: 1px solid #e2e8f0;">

                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <a href="completed_kyc.php" class="btn btn-secondary px-4 py-2 font-weight-bold" style="border-radius: 10px;">
                            Cancel
                        </a>
                        <button type="submit" name="update" class="btn btn-success px-5 py-2 font-weight-bold" style="border-radius: 10px; background: #16a34a; border: none; box-shadow: 0 4px 12px rgba(22,163,74,0.25);">
                            <i class="fa fa-check-circle mr-1"></i> Save & Auto-Verify KYC
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
</div>

<?php include __DIR__ . '/common/footer.php'; ?>
</body>
</html>
