<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<?php
// Fetch Company Deposit Settings from tbl_system_control or default fallbacks
$company_upi_id = 'ananta@upi';
$company_bep20_address = '0x89205A3A3b2A69De6Dbf7f01ED13B2108B2c43e7';

try {
    $stmtConfig = $pdo->prepare("SELECT setting_key, setting_value FROM tbl_system_control WHERE setting_key IN ('company_upi_id', 'company_bep20_address')");
    $stmtConfig->execute();
    $configRows = $stmtConfig->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!empty($configRows['company_upi_id'])) {
        $company_upi_id = $configRows['company_upi_id'];
    }
    if (!empty($configRows['company_bep20_address'])) {
        $company_bep20_address = $configRows['company_bep20_address'];
    }
} catch (Exception $e) {
    // fallback defaults remain intact
}

$error_msg = '';
$success_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deposit_type = $_POST["deposit_type"] ?? 'INR'; // INR or BEP20
    $amount = floatval($_POST["amount"] ?? 0);
    $tr_id = trim($_POST["tr_id"] ?? '');
    $remark = trim($_POST["remark"] ?? '');
    
    date_default_timezone_set('Asia/Kolkata');
    $time = date('h:i a');
    $date = date('Y-m-d');
    
    if ($amount <= 0) {
        $error_msg = "Please enter a valid deposit amount greater than zero.";
    } elseif ($deposit_type === 'INR' && empty($tr_id)) {
        $error_msg = "UTR Number / Transaction Reference ID is compulsory for INR Deposit.";
    } elseif (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Payment Slip / Screenshot proof upload is compulsory.";
    } else {
        // Handle File Upload safely
        $file = $_FILES['proof_image'];
        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($origExt, $allowedExts)) {
            $error_msg = "Invalid image file type. Permitted formats: JPG, JPEG, PNG, WEBP.";
        } else {
            // Check MIME type if possible
            $fileMime = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileMime = strtolower(finfo_file($finfo, $file['tmp_name']) ?: '');
                finfo_close($finfo);
            } elseif (function_exists('mime_content_type')) {
                $fileMime = strtolower(@mime_content_type($file['tmp_name']) ?: '');
            }
            
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/pjpeg', 'image/x-png'];
            if (!empty($fileMime) && !in_array($fileMime, $allowedMimes)) {
                $error_msg = "Invalid image file MIME type (" . htmlspecialchars($fileMime) . "). Permitted: JPG, PNG, WEBP.";
            } else {
                // If UTR provided, check uniqueness in tbl_payment
                if (!empty($tr_id)) {
                    $selectCheckTxnId = $pdo->prepare("SELECT id FROM tbl_payment WHERE tr_id = :tr_id");
                    $selectCheckTxnId->execute([':tr_id' => $tr_id]);
                    if ($selectCheckTxnId->fetch()) {
                        $error_msg = "This UTR / Transaction ID already exists. Please check your transaction record.";
                    }
                } else {
                    // For BEP20 if tr_id empty, auto-generate unique reference
                    $tr_id = 'BEP20-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
                }
                
                if (empty($error_msg)) {
                    // Save uploaded proof image into dashboard/img directory
                    $uploadDir = __DIR__ . '/../img';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0755, true);
                    }
                    
                    // Add .htaccess inside img dir if missing
                    $htaccessPath = $uploadDir . '/.htaccess';
                    if (!file_exists($htaccessPath)) {
                        $htaccessContent = "<FilesMatch \"\\.(php|phtml|php3|php4|php5|phps|phar|exe|pl|py|cgi|sh|js|htm|html)$\">\n";
                        $htaccessContent .= "    Order allow,deny\n";
                        $htaccessContent .= "    Deny from all\n";
                        $htaccessContent .= "</FilesMatch>\n";
                        @file_put_contents($htaccessPath, $htaccessContent);
                    }
                    
                    $newFilename = 'proof_' . strtolower($deposit_type) . '_' . $userid . '_' . time() . '_' . rand(100, 999) . '.' . $origExt;
                    $destPath = $uploadDir . '/' . $newFilename;
                    
                    $uploadOk = move_uploaded_file($file['tmp_name'], $destPath);
                    if (!$uploadOk) {
                        $uploadOk = @copy($file['tmp_name'], $destPath);
                    }
                    
                    if (!$uploadOk) {
                        $error_msg = "Failed to upload payment proof screenshot. Please check file permissions.";
                    } else {
                        $subject = ($deposit_type === 'INR') ? 'INR Deposit Request' : 'BEP20 Deposit Request';
                        $mode = $deposit_type;
                        
                        $insertRequestPayment = $pdo->prepare("INSERT INTO tbl_payment(userid, tr_id, mode, subject, image, amount, remark, plantype, date, time, status)
                            VALUES(:userid, :tr_id, :mode, :subject, :image, :amount, :remark, :plantype, :date, :time, :status)");
                        $saved = $insertRequestPayment->execute([
                            ':userid' => $userid,
                            ':tr_id' => $tr_id,
                            ':mode' => $mode,
                            ':subject' => $subject,
                            ':image' => $newFilename,
                            ':amount' => $amount,
                            ':remark' => $remark,
                            ':plantype' => 'DEPOSIT',
                            ':date' => $date,
                            ':time' => $time,
                            ':status' => 0 // PENDING
                        ]);
                        
                        if ($saved) {
                            $depReqId = $pdo->lastInsertId();
                            if (function_exists('createUserNotification')) {
                                createUserNotification(
                                    $userid,
                                    'DEPOSIT',
                                    'Deposit Request Submitted',
                                    "Your {$deposit_type} deposit request of $" . number_format($amount, 2) . " [Ref/UTR: {$tr_id}] has been submitted successfully and is pending approval.",
                                    $tr_id,
                                    $pdo
                                );
                            }
                            echo "<script>alert('Deposit Request submitted successfully! Status: PENDING.');window.location.assign('request-history.php');</script>";
                            exit;
                        } else {
                            $error_msg = "Failed to submit deposit request. Please try again.";
                        }
                    }
                }
            }
        }
    }
}
?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - DEPOSIT FUND PAGE REDESIGN
   Matches Dashboard (index.php), Profile & Withdrawal Styling
========================================================= */

html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-user-dashboard,
body.bg-theme,
body.bg-theme1,
body.ananta-user-dashboard.bg-theme,
body.ananta-user-dashboard.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

/* Remove old legacy dark overlays */
html::before, html::after, body::before, body::after, #wrapper::before, #wrapper::after, .content-wrapper::before, .content-wrapper::after {
    content: none !important;
    display: none !important;
    background: none !important;
    background-color: transparent !important;
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

/* Header Banner */
.income-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

/* Tab Navigation */
.deposit-nav-tabs {
    display: flex;
    gap: 12px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 28px;
}

.deposit-nav-tab {
    padding: 12px 24px;
    font-size: 15px;
    font-weight: 800;
    color: #64748b;
    border: none;
    background: transparent;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: -2px;
}

.deposit-nav-tab:hover {
    color: #0284c7;
}

.deposit-nav-tab.active {
    color: #0284c7;
    border-bottom-color: #0284c7;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
}

/* Deposit Card Container */
.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 24px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
}

.card-header-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
}

/* Payment Info Box */
.payment-info-box {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.qr-code-img {
    width: 140px;
    height: 140px;
    border-radius: 14px;
    border: 2px solid #e2e8f0;
    padding: 6px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.copy-badge-btn {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.copy-badge-btn:hover {
    background: #0284c7;
    color: #ffffff;
}

/* Form Controls Styling */
label.form-label, label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control, input.form-control, select.form-control, textarea.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    padding: 10px 16px !important;
    transition: all 0.2s ease-in-out !important;
    box-shadow: none !important;
}

.form-control:focus, input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    outline: none !important;
}

.btn-ananta-submit {
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    height: 52px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}

.btn-ananta-submit:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.35) !important;
    color: #ffffff !important;
}
</style>

<body class="ananta-user-dashboard">

    <!-- Loader -->
    <div id="pageloader-overlay" class="visible incoming">
        <div class="loader-wrapper-outer">
            <div class="loader-wrapper-inner">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <!-- End Loader -->

    <!-- Wrapper -->
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!-- Header Welcome Banner -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card income-header-card border-0 p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="income-header-icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">DEPOSIT FUND</span>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 600;">INR & BEP20 CRYPTO</span>
                                        </div>
                                        <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                            Deposit <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Fund</span> 💳
                                        </h4>
                                        <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                            Make payment via Company UPI or BEP20 Wallet & upload proof for Admin Approval.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <a href="request-history.php" class="btn btn-outline-primary font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                                        <i class="fa fa-history me-1"></i> View Deposit History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($error_msg)): ?>
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px; background: #fef2f2; color: #991b1b; font-weight: 600;">
                                <i class="fa fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Card Section -->
                <div class="row">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="ananta-fintech-card">
                            
                            <!-- Deposit Tabs -->
                            <div class="px-4 pt-4">
                                <div class="deposit-nav-tabs">
                                    <button class="deposit-nav-tab active" id="tab-inr-btn" onclick="switchDepositTab('INR')">
                                        <i class="fa fa-inr text-primary"></i> 1. INR Deposit
                                    </button>
                                    <button class="deposit-nav-tab" id="tab-bep20-btn" onclick="switchDepositTab('BEP20')">
                                        <i class="fa fa-btc text-warning"></i> 2. BEP20 Deposit
                                    </button>
                                </div>
                            </div>

                            <!-- Tab Content: INR DEPOSIT -->
                            <div id="deposit-inr-section" class="p-4 p-md-5 pt-0">
                                
                                <div class="payment-info-box">
                                    <?php 
                                    $inrQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode("upi://pay?pa=" . $company_upi_id . "&pn=Ananta%20Fintech");
                                    ?>
                                    <img src="<?php echo $inrQrUrl; ?>" alt="Company UPI QR Code" class="qr-code-img">
                                    <div style="flex: 1;">
                                        <span class="badge mb-2" style="background: rgba(22, 163, 74, 0.12); color: #16a34a; font-size: 11px; font-weight: 700; border-radius: 6px; padding: 4px 8px;">OFFICIAL COMPANY UPI</span>
                                        <h5 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Company UPI Details</h5>
                                        <p style="font-size: 13.5px; color: #475569; margin-bottom: 10px;">Scan QR Code using Google Pay, PhonePe, Paytm, or BHIM UPI.</p>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div style="font-family: monospace; font-size: 15px; font-weight: 800; color: #0284c7; background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px;">
                                                <i class="fa fa-qrcode me-1 text-muted"></i> <span id="upiIdText"><?php echo htmlspecialchars($company_upi_id); ?></span>
                                            </div>
                                            <button type="button" class="copy-badge-btn" onclick="copyToClipboard('upiIdText', 'UPI ID')">
                                                <i class="fa fa-copy me-1"></i> Copy UPI ID
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="deposit_type" value="INR">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label>Deposit Amount (₹) <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="1" name="amount" class="form-control" placeholder="Enter Deposit Amount in INR" required style="height: 48px;">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>UTR Number / Txn Ref ID <span class="text-danger">*</span></label>
                                                <input type="text" name="tr_id" class="form-control" placeholder="12-digit UTR or Txn Reference ID" required style="height: 48px;">
                                                <small class="text-muted" style="font-size: 11.5px;">Compulsory 12-digit UTR from your UPI payment app.</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label>Payment Slip / Screenshot <span class="text-danger">*</span></label>
                                                <input type="file" name="proof_image" class="form-control" accept="image/*" required style="height: 48px; padding-top: 8px;">
                                                <small class="text-muted" style="font-size: 11.5px;">Upload payment receipt screenshot (JPG, PNG, WEBP).</small>
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Remark / Notes (Optional)</label>
                                                <input type="text" name="remark" class="form-control" placeholder="Optional notes (e.g. PhonePe / GPay)" style="height: 48px;">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn-ananta-submit mt-2">
                                        <i class="fa fa-paper-plane me-1"></i> Submit INR Deposit Request
                                    </button>
                                </form>

                            </div>

                            <!-- Tab Content: BEP20 DEPOSIT -->
                            <div id="deposit-bep20-section" class="p-4 p-md-5 pt-0" style="display: none;">
                                
                                <div class="payment-info-box">
                                    <?php 
                                    $bep20QrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($company_bep20_address);
                                    ?>
                                    <img src="<?php echo $bep20QrUrl; ?>" alt="Company BEP20 QR Code" class="qr-code-img">
                                    <div style="flex: 1;">
                                        <span class="badge mb-2" style="background: rgba(245, 158, 11, 0.15); color: #d97706; font-size: 11px; font-weight: 700; border-radius: 6px; padding: 4px 8px;">COMPANY BEP20 WALLET</span>
                                        <h5 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Company BEP20 (USDT / BNB) Address</h5>
                                        <p style="font-size: 13.5px; color: #475569; margin-bottom: 10px;">Send BEP20 USDT / Crypto to the official wallet address below.</p>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div style="font-family: monospace; font-size: 13.5px; font-weight: 800; color: #0284c7; background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px; word-break: break-all;">
                                                <i class="fa fa-btc me-1 text-warning"></i> <span id="bep20AddressText"><?php echo htmlspecialchars($company_bep20_address); ?></span>
                                            </div>
                                            <button type="button" class="copy-badge-btn" onclick="copyToClipboard('bep20AddressText', 'BEP20 Address')">
                                                <i class="fa fa-copy me-1"></i> Copy Address
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="deposit_type" value="BEP20">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label>Deposit Amount ($) <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="1" name="amount" class="form-control" placeholder="Enter Deposit Amount in USD / USDT" required style="height: 48px;">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Txn Hash / Reference (Optional)</label>
                                                <input type="text" name="tr_id" class="form-control" placeholder="Enter Blockchain Transaction Hash / Txn ID" style="height: 48px;">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label>Payment Slip / Screenshot <span class="text-danger">*</span></label>
                                                <input type="file" name="proof_image" class="form-control" accept="image/*" required style="height: 48px; padding-top: 8px;">
                                                <small class="text-muted" style="font-size: 11.5px;">Upload wallet transfer screenshot (JPG, PNG, WEBP).</small>
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Remark / Notes (Optional)</label>
                                                <input type="text" name="remark" class="form-control" placeholder="Optional notes (e.g. Trust Wallet / Binance)" style="height: 48px;">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn-ananta-submit mt-2">
                                        <i class="fa fa-paper-plane me-1"></i> Submit BEP20 Deposit Request
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- Overlay -->
                <div class="overlay toggle-menu"></div>

            </div>
        </div>

        <!-- Back To Top Button -->
        <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

        <!-- Footer -->
        <?php include 'common/footer.php' ?>

    </div>

    <script>
    function switchDepositTab(type) {
        if (type === 'INR') {
            document.getElementById('deposit-inr-section').style.display = 'block';
            document.getElementById('deposit-bep20-section').style.display = 'none';
            document.getElementById('tab-inr-btn').classList.add('active');
            document.getElementById('tab-bep20-btn').classList.remove('active');
        } else {
            document.getElementById('deposit-inr-section').style.display = 'none';
            document.getElementById('deposit-bep20-section').style.display = 'block';
            document.getElementById('tab-inr-btn').classList.remove('active');
            document.getElementById('tab-bep20-btn').classList.add('active');
        }
    }

    function copyToClipboard(elementId, labelText) {
        var text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(function() {
            alert(labelText + ' copied to clipboard: ' + text);
        }).catch(function() {
            var input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert(labelText + ' copied to clipboard!');
        });
    }
    </script>

</body>
</html>