<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ?>
<?php
// Ensure $userid is strictly derived from session
$sessionUserId = $_SESSION['userid'] ?? '';
if (empty($sessionUserId)) {
    header("Location: login.php");
    exit();
}
$userid = $sessionUserId;

// Fetch current user KYC status & BEP20 address
$stmtUser = $pdo->prepare("SELECT kyc, bep20_address FROM user WHERE userid = :userid");
$stmtUser->execute([':userid' => $userid]);
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

$kycStatusVal = isset($userRow['kyc']) ? (int)$userRow['kyc'] : 0;
$userBep20    = $userRow['bep20_address'] ?? '';

if ($kycStatusVal === 0) {
    $k_status = "Not Submitted";
    $color    = "#FF6C60";
} else if ($kycStatusVal === 1) {
    $k_status = "Pending";
    $color    = '#FEFC95';
} else if ($kycStatusVal === 2) {
    $k_status = "Clear";
    $color    = '#C4FBC7';
} else if ($kycStatusVal === 3) {
    $k_status = "Rejected";
    $color    = 'red';
} else {
    $k_status = "Not Submitted";
    $color    = "#FF6C60";
}

$errorMsg   = '';
$successMsg = '';

// Helper for file uploads
if (!function_exists('secureUploadKycDoc')) {
    function secureUploadKycDoc($fileKey, $allowedExts = ['jpg', 'jpeg', 'png', 'pdf']) {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            return ['status' => 'empty'];
        }
        $file = $_FILES[$fileKey];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => 'File upload error occurred code: ' . $file['error']];
        }
        // Size limit: 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'File size exceeds 5MB limit.'];
        }

        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($origExt, $allowedExts)) {
            return ['status' => 'error', 'message' => 'Invalid file extension. Permitted: JPG, JPEG, PNG, PDF.'];
        }

        // MIME validation
        $fileMime = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMime = strtolower(finfo_file($finfo, $file['tmp_name']) ?: '');
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $fileMime = strtolower(@mime_content_type($file['tmp_name']) ?: '');
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 'image/pjpeg', 'image/x-png'];
        if (!empty($fileMime) && !in_array($fileMime, $allowedMimes)) {
            return ['status' => 'error', 'message' => 'Invalid file MIME type (' . htmlspecialchars($fileMime) . '). Permitted: JPG, PNG, PDF.'];
        }

        // Random filename
        $newFilename = 'kyc_' . bin2hex(random_bytes(16)) . '.' . $origExt;
        $uploadDir   = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        // Ensure .htaccess inside uploads prevents script execution
        $htaccessPath = $uploadDir . '/.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "<FilesMatch \"\\.(php|phtml|php3|php4|php5|phps|phar|exe|pl|py|cgi|sh|js|htm|html)$\">\n";
            $htaccessContent .= "    Order allow,deny\n";
            $htaccessContent .= "    Deny from all\n";
            $htaccessContent .= "</FilesMatch>\n";
            $htaccessContent .= "RemoveHandler .php .phtml .php3 .php4 .php5 .phps .phar\n";
            $htaccessContent .= "RemoveType .php .phtml .php3 .php4 .php5 .phps .phar\n";
            @file_put_contents($htaccessPath, $htaccessContent);
        }

        $destPath = $uploadDir . '/' . $newFilename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            if (!@copy($file['tmp_name'], $destPath)) {
                return ['status' => 'error', 'message' => 'Failed to save uploaded file.'];
            }
        }
        return ['status' => 'success', 'filename' => $newFilename];
    }
}

// Handle Form Submission
if (isset($_POST['update'])) {
    $holder_name   = trim($_POST['holder_name'] ?? '');
    $ac_number1    = trim($_POST['ac_number1'] ?? '');
    $ac_number2    = trim($_POST['ac_number2'] ?? '');
    $bank          = trim($_POST['bank'] ?? '');
    $branch        = trim($_POST['branch'] ?? '');
    $ifsc          = strtoupper(trim($_POST['ifsc'] ?? ''));
    $upi_id        = trim($_POST['upi_id'] ?? '');
    $bep20_address = trim($_POST['bep20_address'] ?? '');
    $pan           = strtoupper(trim($_POST['pan'] ?? ''));
    $mimo          = trim($_POST['mimo'] ?? ''); // Aadhaar Number

    // Validation checks
    if ($ac_number1 !== $ac_number2) {
        $errorMsg = "Account Number and Confirm Account Number do not match.";
    } elseif (!empty($ifsc) && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) {
        $errorMsg = "Invalid IFSC Code format. Example: SBIN0001234";
    } elseif (!empty($pan) && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan)) {
        $errorMsg = "Invalid PAN Card Number format. Example: ABCDE1234F";
    } elseif (!empty($mimo) && !preg_match('/^[0-9]{12}$/', $mimo)) {
        $errorMsg = "Aadhaar Number must be exactly 12 digits.";
    } else {
        // Fetch existing KYC row to retain old uploaded images if new files aren't provided
        $stmtChk = $pdo->prepare("SELECT * FROM kyc WHERE userid = :userid");
        $stmtChk->execute([':userid' => $userid]);
        $existKyc = $stmtChk->fetch(PDO::FETCH_ASSOC);

        $adhar_front_img = $existKyc['adhar_front_img'] ?? '';
        $adhar_back_img  = $existKyc['adhar_back_img'] ?? '';
        $pan_img         = $existKyc['pan_img'] ?? '';

        // Handle Aadhaar Card Upload (Front)
        $upAdhar = secureUploadKycDoc('adhar_front_img');
        if ($upAdhar['status'] === 'error') {
            $errorMsg = "Aadhaar Card Upload Error: " . $upAdhar['message'];
        } elseif ($upAdhar['status'] === 'success') {
            $adhar_front_img = $upAdhar['filename'];
        }

        // Handle Aadhaar Card Upload (Back if provided)
        if (empty($errorMsg)) {
            $upAdharBack = secureUploadKycDoc('adhar_back_img');
            if ($upAdharBack['status'] === 'error') {
                $errorMsg = "Aadhaar Back Image Error: " . $upAdharBack['message'];
            } elseif ($upAdharBack['status'] === 'success') {
                $adhar_back_img = $upAdharBack['filename'];
            }
        }

        // Handle PAN Card Upload
        if (empty($errorMsg)) {
            $upPan = secureUploadKycDoc('pan_img');
            if ($upPan['status'] === 'error') {
                $errorMsg = "PAN Card Upload Error: " . $upPan['message'];
            } elseif ($upPan['status'] === 'success') {
                $pan_img = $upPan['filename'];
            }
        }

        if (empty($errorMsg)) {
            if ($existKyc) {
                // UPDATE kyc record
                $stmtUpd = $pdo->prepare("UPDATE kyc SET 
                    holder_name = :holder_name,
                    ac_number   = :ac_number,
                    bank        = :bank,
                    branch      = :branch,
                    ifsc        = :ifsc,
                    bhim        = :bhim,
                    pan         = :pan,
                    mimo        = :mimo,
                    adhar_front_img = :adhar_front_img,
                    adhar_back_img  = :adhar_back_img,
                    pan_img         = :pan_img,
                    status      = '0'
                    WHERE userid = :userid");
                $stmtUpd->execute([
                    ':holder_name'     => $holder_name,
                    ':ac_number'       => $ac_number1,
                    ':bank'            => $bank,
                    ':branch'          => $branch,
                    ':ifsc'            => $ifsc,
                    ':bhim'            => $upi_id,
                    ':pan'             => $pan,
                    ':mimo'            => $mimo,
                    ':adhar_front_img' => $adhar_front_img,
                    ':adhar_back_img'  => $adhar_back_img,
                    ':pan_img'         => $pan_img,
                    ':userid'          => $userid
                ]);
            } else {
                // INSERT kyc record
                $stmtIns = $pdo->prepare("INSERT INTO kyc (
                    userid, holder_name, ac_number, bank, branch, ifsc, bhim, pan, mimo, adhar_front_img, adhar_back_img, pan_img, status
                ) VALUES (
                    :userid, :holder_name, :ac_number, :bank, :branch, :ifsc, :bhim, :pan, :mimo, :adhar_front_img, :adhar_back_img, :pan_img, '0'
                )");
                $stmtIns->execute([
                    ':userid'          => $userid,
                    ':holder_name'     => $holder_name,
                    ':ac_number'       => $ac_number1,
                    ':bank'            => $bank,
                    ':branch'          => $branch,
                    ':ifsc'            => $ifsc,
                    ':bhim'            => $upi_id,
                    ':pan'             => $pan,
                    ':mimo'            => $mimo,
                    ':adhar_front_img' => $adhar_front_img,
                    ':adhar_back_img'  => $adhar_back_img,
                    ':pan_img'         => $pan_img
                ]);
            }

            // Update user table (kyc = 1 for pending, bep20_address)
            $stmtUserUpd = $pdo->prepare("UPDATE user SET kyc = '1', bep20_address = :bep20 WHERE userid = :userid");
            $stmtUserUpd->execute([
                ':bep20'  => $bep20_address,
                ':userid' => $userid
            ]);

            echo "<script>alert('KYC updated successfully!'); window.location.href = 'kyc.php';</script>";
            exit();
        }
    }
}

// Select active KYC record for display
$stmtFetch = $pdo->prepare("SELECT * FROM kyc WHERE userid = :userid");
$stmtFetch->execute([':userid' => $userid]);
$row1 = $stmtFetch->fetch(PDO::FETCH_ASSOC);

if (!$row1 || !is_array($row1)) {
    $row1 = [
        'holder_name'     => '',
        'ac_number'       => '',
        'bank'            => '',
        'branch'          => '',
        'ifsc'            => '',
        'bhim'            => '',
        'pan'             => '',
        'mimo'            => '',
        'adhar_front_img' => '',
        'adhar_back_img'  => '',
        'pan_img'         => ''
    ];
}
?>

<style>
/* KYC Clean UI */
body.ananta-user-dashboard {
    background: #f6f8fb !important;
    color: #111827 !important;
}

.kyc-page {
    width: 100%;
    min-height: calc(100vh - 80px);
    background: #f6f8fb;
    padding: 22px 24px 100px;
}

.kyc-card {
    width: 100%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.07);
    overflow: hidden;
}

.kyc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 25px 30px;
    border-bottom: 1px solid #e5eaf0;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 65%, #f4fbf7 100%);
}

.kyc-title-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
}

.kyc-header-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    background: linear-gradient(135deg, rgba(11, 94, 215, 0.10), rgba(34, 164, 71, 0.12));
    border: 1px solid rgba(11, 94, 215, 0.10);
    color: #0B5ED7;
    font-size: 21px;
}

.kyc-title {
    margin: 0;
    color: #111827 !important;
    font-size: 22px;
    font-weight: 800;
}

.kyc-subtitle {
    margin: 4px 0 0;
    color: #64748b !important;
    font-size: 13px;
    font-weight: 500;
}

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
}

.kyc-form-area {
    padding: 30px;
}

.section-title-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    background: #f8fafc;
    border-left: 4px solid #0B5ED7;
    border-radius: 8px;
    margin-bottom: 20px;
    margin-top: 10px;
}

.section-title-box h5 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kyc-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px 26px;
    margin-bottom: 25px;
}

.kyc-field {
    width: 100%;
}

.kyc-field label {
    display: block;
    margin-bottom: 8px;
    color: #111827 !important;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.55px;
    text-transform: uppercase;
}

.kyc-field .form-control {
    width: 100%;
    height: 49px;
    padding: 0 14px;
    border-radius: 11px !important;
    border: 1px solid #d7dee8 !important;
    background: #ffffff !important;
    color: #111827 !important;
    font-size: 14px;
    font-weight: 600;
}

.kyc-field input[type="file"].form-control {
    padding: 10px 14px;
    height: auto;
}

.kyc-field .form-control:focus {
    border-color: #0B5ED7 !important;
    box-shadow: 0 0 0 3px rgba(11, 94, 215, 0.08) !important;
}

#passwordWarning {
    display: block;
    margin-top: 6px;
    color: #dc2626 !important;
    font-size: 11px !important;
    font-weight: 700;
}

.kyc-submit-area {
    display: flex;
    justify-content: center;
    padding-top: 20px;
}

.kyc-submit-btn {
    min-width: 220px;
    height: 50px;
    padding: 0 30px;
    border: none !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, #0B5ED7 0%, #0788c9 50%, #22A447 100%) !important;
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.3px;
    box-shadow: 0 8px 20px rgba(11, 94, 215, 0.20);
    transition: all 0.2s ease;
    cursor: pointer;
}

.kyc-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 26px rgba(11, 94, 215, 0.28);
}

.kyc-submit-btn:disabled {
    opacity: 0.55;
    cursor: not-allowed !important;
    transform: none;
}

.doc-preview-badge {
    display: inline-block;
    margin-top: 6px;
    padding: 4px 10px;
    background: #e0f2fe;
    color: #0369a1;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    text-decoration: none;
}

@media (max-width: 767px) {
    .kyc-grid {
        grid-template-columns: 1fr;
    }
    .kyc-page {
        padding: 12px 10px 90px;
    }
}
</style>

<body class="ananta-user-dashboard">
<div id="wrapper">
    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="container-fluid pt-3 px-4">
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #475569; font-weight: 600;">Settings</span>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">Update KYC</span>
                </div>
            </nav>
        </div>

        <div class="kyc-page">
            <div class="kyc-card">
                <!-- Header -->
                <div class="kyc-header">
                    <div class="kyc-title-wrapper">
                        <div class="kyc-header-icon">
                            <i class="fa fa-id-card"></i>
                        </div>
                        <div>
                            <h3 class="kyc-title">Update KYC Details</h3>
                            <p class="kyc-subtitle">Bank account &amp; identity verification documents</p>
                        </div>
                    </div>
                    <div>
                        <span class="kyc-status" style="background-color: <?php echo $color; ?>; color: #000 !important;">
                            STATUS: <?php echo htmlspecialchars($k_status); ?>
                        </span>
                    </div>
                </div>

                <div class="kyc-form-area">
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger font-weight-bold mb-4" style="border-radius: 10px;">
                            <i class="fa fa-exclamation-triangle mr-2"></i><?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data" id="registration_form">
                        
                        <!-- SECTION 1: BANK DETAILS -->
                        <div class="section-title-box">
                            <i class="fa fa-university text-primary"></i>
                            <h5>1. Bank Details</h5>
                        </div>

                        <div class="kyc-grid">
                            <!-- 1. Account Holder Name -->
                            <div class="kyc-field">
                                <label>1. Account Holder Name</label>
                                <input type="text" name="holder_name" value="<?php echo htmlspecialchars($row1['holder_name'] ?? ''); ?>" class="form-control" placeholder="Enter Account Holder Name" required>
                            </div>

                            <!-- 4. Bank Name -->
                            <div class="kyc-field">
                                <label>4. Bank Name</label>
                                <input type="text" name="bank" value="<?php echo htmlspecialchars($row1['bank'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Name" required>
                            </div>

                            <!-- 2. Account Number -->
                            <div class="kyc-field">
                                <label>2. Account Number</label>
                                <input type="text" name="ac_number1" id="ac_number1" value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control" placeholder="Enter Bank Account Number" required>
                            </div>

                            <!-- 3. Confirm Account Number -->
                            <div class="kyc-field">
                                <label>3. Confirm Account Number</label>
                                <input type="text" name="ac_number2" id="ac_number2" value="<?php echo htmlspecialchars($row1['ac_number'] ?? ''); ?>" class="form-control" placeholder="Re-enter Bank Account Number" required>
                                <span id="passwordWarning"></span>
                            </div>

                            <!-- 5. Branch Name -->
                            <div class="kyc-field">
                                <label>5. Branch Name</label>
                                <input type="text" name="branch" value="<?php echo htmlspecialchars($row1['branch'] ?? ''); ?>" class="form-control" placeholder="Enter Branch Name" required>
                            </div>

                            <!-- 6. IFSC Code -->
                            <div class="kyc-field">
                                <label>6. IFSC Code</label>
                                <input type="text" name="ifsc" value="<?php echo htmlspecialchars($row1['ifsc'] ?? ''); ?>" class="form-control" placeholder="Enter IFSC Code (e.g. SBIN0001234)" required>
                            </div>
                        </div>

                        <!-- SECTION 2: PAYMENT / WALLET -->
                        <div class="section-title-box">
                            <i class="fa fa-credit-card text-primary"></i>
                            <h5>2. Payment / Wallet</h5>
                        </div>

                        <div class="kyc-grid">
                            <!-- 7. UPI ID -->
                            <div class="kyc-field">
                                <label>7. UPI ID</label>
                                <input type="text" name="upi_id" value="<?php echo htmlspecialchars($row1['bhim'] ?? ''); ?>" class="form-control" placeholder="Enter UPI ID (e.g. user@upi)">
                            </div>

                            <!-- 8. BEP20 Wallet Address -->
                            <div class="kyc-field">
                                <label>8. BEP20 Wallet Address</label>
                                <input type="text" name="bep20_address" value="<?php echo htmlspecialchars($userBep20); ?>" class="form-control" placeholder="Enter BEP20 Wallet Address (0x...)">
                            </div>
                        </div>

                        <!-- SECTION 3: IDENTITY -->
                        <div class="section-title-box">
                            <i class="fa fa-address-card text-primary"></i>
                            <h5>3. Identity</h5>
                        </div>

                        <div class="kyc-grid">
                            <!-- 9. PAN Card Number -->
                            <div class="kyc-field">
                                <label>9. PAN Card Number</label>
                                <input type="text" name="pan" value="<?php echo htmlspecialchars($row1['pan'] ?? ''); ?>" class="form-control" placeholder="Enter PAN Number (e.g. ABCDE1234F)">
                            </div>

                            <!-- 10. Aadhaar Number -->
                            <div class="kyc-field">
                                <label>10. Aadhaar Number</label>
                                <input type="text" name="mimo" value="<?php echo htmlspecialchars($row1['mimo'] ?? ''); ?>" class="form-control" placeholder="Enter 12-digit Aadhaar Number">
                            </div>
                        </div>

                        <!-- SECTION 4: DOCUMENTS -->
                        <div class="section-title-box">
                            <i class="fa fa-upload text-primary"></i>
                            <h5>4. Upload ID Proof</h5>
                        </div>

                        <div class="kyc-grid">
                            <!-- Aadhaar Card Upload (Front) -->
                            <div class="kyc-field">
                                <label>Aadhaar Card Upload (Front Image / PDF)</label>
                                <input type="file" name="adhar_front_img" class="form-control" accept="image/*,.pdf">
                                <?php if (!empty($row1['adhar_front_img'])): ?>
                                    <a href="uploads/<?php echo htmlspecialchars($row1['adhar_front_img']); ?>" target="_blank" class="doc-preview-badge">
                                        <i class="fa fa-file-image-o mr-1"></i> View Existing Aadhaar Front
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- PAN Card Upload -->
                            <div class="kyc-field">
                                <label>PAN Card Upload (Image / PDF)</label>
                                <input type="file" name="pan_img" class="form-control" accept="image/*,.pdf">
                                <?php if (!empty($row1['pan_img'])): ?>
                                    <a href="uploads/<?php echo htmlspecialchars($row1['pan_img']); ?>" target="_blank" class="doc-preview-badge">
                                        <i class="fa fa-file-image-o mr-1"></i> View Existing PAN Card
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="kyc-submit-area">
                            <button type="submit" id="submitBtn" class="kyc-submit-btn" name="update">
                                <i class="fa fa-check-circle mr-2"></i> UPDATE KYC DETAILS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'common/footer.php' ?>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/sidebar-menu.js"></script>
<script src="assets/js/app-script.js"></script>

<script>
$(document).ready(function () {
    $('#registration_form').on('submit keyup change', function (e) {
        const pass1 = $('#ac_number1').val().trim();
        const pass2 = $('#ac_number2').val().trim();

        if (pass1 !== "" && pass2 !== "" && pass1 !== pass2) {
            $('#passwordWarning').text('Account Number & Confirm Account Number do not match.');
            $('#submitBtn').attr('disabled', true).css('cursor', 'not-allowed');
            if (e.type === 'submit') {
                e.preventDefault();
            }
            return;
        }

        $('#passwordWarning').text('');
        $('#submitBtn').removeAttr('disabled').css('cursor', 'pointer');
    });
});
</script>

</body>
</html>