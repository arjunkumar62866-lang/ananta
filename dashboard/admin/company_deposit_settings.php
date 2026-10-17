<?php 
ob_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php'; 
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - COMPANY DEPOSIT SETTINGS
   Matches all other admin pages (Fund Request, Withdrawals, Index)
========================================================= */
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

/* Header Banner Card */
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

@media (max-width: 767.98px) {
    .content-wrapper {
        padding-top: 70px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
    .income-header-card {
        padding: 16px !important;
        border-radius: 18px !important;
        margin-bottom: 16px !important;
    }
    .income-header-icon {
        width: 44px !important;
        height: 44px !important;
        font-size: 18px !important;
        border-radius: 12px !important;
    }
    .header-content-box {
        width: 100% !important;
    }
    .header-badge-row {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 6px !important;
    }
    .header-title-text {
        font-size: 18px !important;
        line-height: 1.3 !important;
    }
    .header-sub-text {
        font-size: 12.5px !important;
        line-height: 1.4 !important;
    }
    .header-action-btn {
        width: 100% !important;
        text-align: center !important;
        margin-top: 10px !important;
    }
    .breadcrumb-mobile-hide {
        display: none !important;
    }
}

/* Breadcrumb Styling - Force Black / Dark Gray */
.breadcrumb-section .page-title,
h4.page-title {
    color: #0f172a !important;
    font-weight: 800 !important;
}

.breadcrumb {
    background: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
}

.breadcrumb-item,
.breadcrumb-item a {
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
}

.breadcrumb-item a:hover {
    color: #0284c7 !important;
    text-decoration: underline !important;
}

.breadcrumb-item.active {
    color: #0f172a !important;
    font-weight: 700 !important;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #94a3b8 !important;
}

/* Fintech Card Styling */
.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1.5px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.05) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
}
</style>

<div id="wrapper">
<?php
$msg = '';
$msgType = '';

// Default values
$company_upi_id = 'ananta@upi';
$company_bep20_address = '0x89205A3A3b2A69De6Dbf7f01ED13B2108B2c43e7';
$company_upi_qr = '';
$company_bep20_qr = '';

// Load existing values from tbl_system_control
try {
    $stmtConfig = $pdo->prepare("SELECT setting_key, setting_value FROM tbl_system_control WHERE setting_key IN ('company_upi_id', 'company_bep20_address', 'company_upi_qr', 'company_bep20_qr')");
    $stmtConfig->execute();
    $configRows = $stmtConfig->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!empty($configRows['company_upi_id'])) {
        $company_upi_id = $configRows['company_upi_id'];
    }
    if (!empty($configRows['company_bep20_address'])) {
        $company_bep20_address = $configRows['company_bep20_address'];
    }
    if (!empty($configRows['company_upi_qr'])) {
        $company_upi_qr = $configRows['company_upi_qr'];
    }
    if (!empty($configRows['company_bep20_qr'])) {
        $company_bep20_qr = $configRows['company_bep20_qr'];
    }
} catch (Exception $e) {
    // defaults
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_deposit_settings') {
    $new_upi_id = trim($_POST['company_upi_id'] ?? '');
    $new_bep20_address = trim($_POST['company_bep20_address'] ?? '');

    $uploadDir = '../../img/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    $new_upi_qr = $company_upi_qr;
    $new_bep20_qr = $company_bep20_qr;

    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // Handle UPI QR Code Upload
    if (!empty($_FILES['company_upi_qr']['name']) && $_FILES['company_upi_qr']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['company_upi_qr']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts)) {
            $fileName = 'upi_qr_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($_FILES['company_upi_qr']['tmp_name'], $uploadDir . $fileName)) {
                $new_upi_qr = $fileName;
            } else {
                $msg = 'Failed to upload UPI QR image.';
                $msgType = 'danger';
            }
        } else {
            $msg = 'Invalid file type for UPI QR. Please upload JPG, PNG, WEBP, or GIF.';
            $msgType = 'danger';
        }
    }

    // Handle BEP20 QR Code Upload
    if (empty($msg) && !empty($_FILES['company_bep20_qr']['name']) && $_FILES['company_bep20_qr']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['company_bep20_qr']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts)) {
            $fileName = 'bep20_qr_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($_FILES['company_bep20_qr']['tmp_name'], $uploadDir . $fileName)) {
                $new_bep20_qr = $fileName;
            } else {
                $msg = 'Failed to upload BEP20 QR image.';
                $msgType = 'danger';
            }
        } else {
            $msg = 'Invalid file type for BEP20 QR. Please upload JPG, PNG, WEBP, or GIF.';
            $msgType = 'danger';
        }
    }

    if (empty($msg)) {
        try {
            $settingsToSave = [
                'company_upi_id'        => $new_upi_id,
                'company_bep20_address' => $new_bep20_address,
                'company_upi_qr'        => $new_upi_qr,
                'company_bep20_qr'      => $new_bep20_qr
            ];

            foreach ($settingsToSave as $k => $v) {
                $stmtCheck = $pdo->prepare("SELECT id FROM tbl_system_control WHERE setting_key = :k LIMIT 1");
                $stmtCheck->execute([':k' => $k]);
                $exists = $stmtCheck->fetchColumn();
                if ($exists) {
                    $stmtUpd = $pdo->prepare("UPDATE tbl_system_control SET setting_value = :v, updated_at = NOW() WHERE setting_key = :k");
                    $stmtUpd->execute([':v' => $v, ':k' => $k]);
                } else {
                    $stmtIns = $pdo->prepare("INSERT INTO tbl_system_control (setting_key, setting_value, description) VALUES (:k, :v, :desc)");
                    $stmtIns->execute([':k' => $k, ':v' => $v, ':desc' => 'Company Deposit Configuration']);
                }
            }

            $company_upi_id = $new_upi_id;
            $company_bep20_address = $new_bep20_address;
            $company_upi_qr = $new_upi_qr;
            $company_bep20_qr = $new_bep20_qr;

            $msg = 'Company Deposit Settings & QR Codes updated successfully! All users will now see these updated payment details.';
            $msgType = 'success';
        } catch (Exception $e) {
            $msg = 'Database error: ' . $e->getMessage();
            $msgType = 'danger';
        }
    }
}
?>

<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Banner Card (Clean Mobile & Desktop Layout) -->
        <div class="card income-header-card p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3 header-content-box">
                    <div class="income-header-icon">
                        <i class="fa fa-qrcode"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 800; border-radius: 100px; padding: 3px 10px; letter-spacing: 0.5px;">DEPOSIT CONTROL</span>
                            <span class="text-muted d-none d-md-inline" style="font-size: 12.5px;">
                                <a href="index.php" style="color: #64748b; font-weight: 600; text-decoration: none;">Dashboard</a> / 
                                <a href="fund-request.php" style="color: #64748b; font-weight: 600; text-decoration: none;">Deposit Management</a> / 
                                <strong style="color: #0f172a;">Company Deposit</strong>
                            </span>
                        </div>
                        <h3 class="mb-1 header-title-text" style="font-weight: 800; color: #0f172a; font-size: 22px;">Company Deposit Settings</h3>
                        <p class="mb-0 header-sub-text" style="color: #64748b; font-weight: 500; font-size: 13.5px;">Manage Company UPI ID, BEP20 Address & Custom QR Code images.</p>
                    </div>
                </div>
                <div class="header-action-btn">
                    <a href="fund-request.php" class="btn btn-outline-secondary font-weight-bold px-3 py-2 w-100-mobile" style="border-radius: 12px; font-size: 13px;">
                        <i class="fa fa-history mr-1"></i> Full Deposit History
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; font-weight: 600;">
                <i class="fa fa-<?php echo ($msgType === 'success') ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i> <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_deposit_settings">

            <div class="row">

                <!-- 1. COMPANY INR UPI & QR CODE CONFIGURATION -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm" style="border-radius: 18px; border: 1.5px solid #e2e8f0;">
                        <div class="card-header bg-white border-bottom py-3" style="border-radius: 18px 18px 0 0;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(22, 163, 74, 0.12); color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fa fa-inr"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold" style="color: #0f172a;">1. Company INR UPI & QR Code</h5>
                                    <small class="text-muted">Users will scan this uploaded QR & copy this UPI ID to deposit INR</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Company UPI ID <span class="text-danger">*</span></label>
                                <input type="text" name="company_upi_id" class="form-control" value="<?php echo htmlspecialchars($company_upi_id); ?>" placeholder="e.g. ananta@upi" required style="height: 48px; border-radius: 10px; font-weight: 700; color: #0284c7;">
                                <small class="text-muted">This UPI ID is displayed on the user dashboard with a direct "Copy UPI ID" button.</small>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Upload Custom UPI QR Code Image</label>
                                <input type="file" name="company_upi_qr" class="form-control" accept="image/*" style="height: 48px; border-radius: 10px; padding-top: 8px;">
                                <small class="text-muted d-block mt-1">Upload PhonePe, GPay, Paytm, or BHIM QR Image (JPG, PNG, WEBP).</small>
                            </div>

                            <div class="p-3 text-center" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
                                <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 12px; text-transform: uppercase;">Current UPI QR Code Displayed to Users:</span>
                                <?php if (!empty($company_upi_qr) && file_exists('../../img/' . $company_upi_qr)): ?>
                                    <img src="../../img/<?php echo htmlspecialchars($company_upi_qr); ?>" alt="UPI QR Code" style="max-width: 180px; max-height: 180px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #fff; padding: 6px;">
                                    <small class="d-block text-success font-weight-bold mt-2"><i class="fa fa-check-circle"></i> Custom QR Active</small>
                                <?php else: ?>
                                    <div class="py-3 text-muted">
                                        <i class="fa fa-qrcode fa-3x text-secondary mb-2 d-block"></i>
                                        <span class="font-weight-bold small">No custom QR uploaded yet.</span>
                                        <small class="d-block text-muted">Upload an image above to show your official QR.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. COMPANY BEP20 USDT / BNB WALLET & QR CODE CONFIGURATION -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm" style="border-radius: 18px; border: 1.5px solid #e2e8f0;">
                        <div class="card-header bg-white border-bottom py-3" style="border-radius: 18px 18px 0 0;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fa fa-btc"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold" style="color: #0f172a;">2. Company BEP20 (USDT / BNB) & QR Code</h5>
                                    <small class="text-muted">Users will scan this uploaded QR & send crypto to this wallet address</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Company BEP20 Wallet Address <span class="text-danger">*</span></label>
                                <input type="text" name="company_bep20_address" class="form-control" value="<?php echo htmlspecialchars($company_bep20_address); ?>" placeholder="e.g. 0x89205A..." required style="height: 48px; border-radius: 10px; font-weight: 700; color: #0284c7; font-family: monospace;">
                                <small class="text-muted">This address is displayed on the user dashboard with a direct "Copy Address" button.</small>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Upload Custom BEP20 QR Code Image</label>
                                <input type="file" name="company_bep20_qr" class="form-control" accept="image/*" style="height: 48px; border-radius: 10px; padding-top: 8px;">
                                <small class="text-muted d-block mt-1">Upload Trust Wallet, Binance, or MetaMask QR Image (JPG, PNG, WEBP).</small>
                            </div>

                            <div class="p-3 text-center" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
                                <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 12px; text-transform: uppercase;">Current BEP20 QR Code Displayed to Users:</span>
                                <?php if (!empty($company_bep20_qr) && file_exists('../../img/' . $company_bep20_qr)): ?>
                                    <img src="../../img/<?php echo htmlspecialchars($company_bep20_qr); ?>" alt="BEP20 QR Code" style="max-width: 180px; max-height: 180px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #fff; padding: 6px;">
                                    <small class="d-block text-success font-weight-bold mt-2"><i class="fa fa-check-circle"></i> Custom QR Active</small>
                                <?php else: ?>
                                    <div class="py-3 text-muted">
                                        <i class="fa fa-qrcode fa-3x text-secondary mb-2 d-block"></i>
                                        <span class="font-weight-bold small">No custom QR uploaded yet.</span>
                                        <small class="d-block text-muted">Upload an image above to show your official QR.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Button Bar -->
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success px-5 py-3 font-weight-bold shadow-sm" style="border-radius: 12px; font-size: 16px;">
                        <i class="fa fa-save mr-2"></i> Save Deposit Settings & Update All Users
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

<div class="overlay toggle-menu"></div>
<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

<?php include 'common/footer.php'; ?>
</div>
</body>
</html>
