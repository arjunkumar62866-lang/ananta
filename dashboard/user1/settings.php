<?php
ob_start();
session_start();
require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$msg = '';
$msgType = '';

// Handle BEP20 Address Update Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_bep20') {
    $bep20Addr = trim($_POST['bep20_address'] ?? '');
    $res = updateUserBEP20Address($userid, $bep20Addr, $pdo);

    if ($res['status'] === 'success') {
        $msg = $res['message'];
        $msgType = 'success';
    } else {
        $msg = $res['message'];
        $msgType = 'danger';
    }
}

// Fetch user settings data
$stmt = $pdo->prepare("SELECT userid, name, email, mobile, bep20_address, kyc FROM user WHERE userid = :uid");
$stmt->execute([':uid' => $userid]);
$uData = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">BEP20 Address Settings</h4>
                <p class="text-muted small mb-0">Manage your crypto withdrawal address and bank KYC verification status</p>
            </div>
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #475569; font-weight: 600;">Settings</span>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">BEP20 Address</span>
                </div>
            </nav>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show border-0 shadow-sm rounded-lg mb-4" role="alert" style="border-radius: 12px;">
                <i class="zmdi zmdi-info-outline mr-2"></i> <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- BEP20 Address Management Card -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                            <i class="zmdi zmdi-qr-code mr-2" style="color: #0284c7;"></i> BEP20 (Binance Smart Chain) Address
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="settings.php">
                            <input type="hidden" name="action" value="update_bep20">
                            <div class="form-group mb-3">
                                <label for="bep20_address" class="font-weight-bold small text-uppercase" style="color: #475569;">BEP20 Wallet Address (EVM / 0x...)</label>
                                <input type="text" class="form-control form-control-lg" id="bep20_address" name="bep20_address" 
                                       value="<?php echo htmlspecialchars($uData['bep20_address'] ?? ''); ?>" 
                                       placeholder="e.g. 0x71C765...d897" required style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;">
                                <small class="form-text text-muted mt-2">
                                    <i class="zmdi zmdi-info-outline mr-1"></i> Ensure your USDT / BEP20 payout address is accurate before requesting crypto withdrawals.
                                </small>
                            </div>
                            <button type="submit" class="btn px-4 py-2 font-weight-bold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                                <i class="zmdi zmdi-save mr-2"></i> Save BEP20 Address
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bank KYC Status Card -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                            <i class="zmdi zmdi-assignment-check mr-2" style="color: #16a34a;"></i> Bank KYC Verification Status
                        </h6>
                    </div>
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                        <?php if ((int)($uData['kyc'] ?? 0) === 1): ?>
                            <div class="rounded-circle p-3 mb-3 d-inline-flex" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
                                <i class="zmdi zmdi-check-circle zmdi-hc-4x"></i>
                            </div>
                            <h5 class="font-weight-bold mb-1" style="color: #0f172a;">Bank KYC Verified</h5>
                            <p class="text-muted small mb-4" style="max-width: 320px;">Your Bank KYC verification is active and approved for INR withdrawals.</p>
                            <a href="kyc.php" class="btn btn-outline-success px-4 py-2 font-weight-bold" style="border-radius: 10px;">
                                <i class="zmdi zmdi-eye mr-2"></i> View KYC Details
                            </a>
                        <?php else: ?>
                            <div class="rounded-circle p-3 mb-3 d-inline-flex" style="background: rgba(234, 179, 8, 0.1); color: #ca8a04;">
                                <i class="zmdi zmdi-time-restore zmdi-hc-4x"></i>
                            </div>
                            <h5 class="font-weight-bold mb-1" style="color: #0f172a;">Bank KYC Pending / Incomplete</h5>
                            <p class="text-muted small mb-4" style="max-width: 320px;">Please upload your Bank and PAN details to complete verification for INR payouts.</p>
                            <a href="kyc.php" class="btn px-4 py-2 font-weight-bold" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%); color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(234, 179, 8, 0.25);">
                                <i class="zmdi zmdi-edit mr-2"></i> Complete Bank KYC
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'common/footer.php'; ?>

