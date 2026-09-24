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

// Handle Withdrawal Request Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_withdrawal') {
    $method = trim($_POST['withdrawal_method'] ?? 'INR');
    $amount = (float)($_POST['amount'] ?? 0);

    $res = processUserWithdrawalRequest($userid, $method, $amount, $pdo);
    if ($res['status'] === 'success') {
        $msg = $res['message'];
        $msgType = 'success';
    } else {
        $msg = $res['message'];
        $msgType = 'danger';
    }
}

// Fetch user info for UI
$stmtU = $pdo->prepare("SELECT amount, bep20_address, withdrawal_status, kyc FROM user WHERE userid = :uid");
$stmtU->execute([':uid' => $userid]);
$uData = $stmtU->fetch(PDO::FETCH_ASSOC) ?: [];
$userBal = (float)($uData['amount'] ?? 0);
$bep20Addr = $uData['bep20_address'] ?? '';

// Check Global Withdrawal setting
$stmtSys = $pdo->prepare("SELECT setting_value FROM tbl_system_control WHERE setting_key = 'withdrawal_enable' LIMIT 1");
$stmtSys->execute();
$globalWd = $stmtSys->fetchColumn();
$isGlobalDisabled = ($globalWd !== false && (int)$globalWd === 0);
$isUserDisabled = (isset($uData['withdrawal_status']) && (string)$uData['withdrawal_status'] === '0');
$isWithdrawalDisabled = ($isGlobalDisabled || $isUserDisabled);
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">INR & BEP20 Withdrawal</h4>
                <p class="text-muted small mb-0">Request payout via INR Bank Transfer or BEP20 USDT Crypto Transfer</p>
            </div>
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #475569; font-weight: 600;">Withdrawal</span>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">INR & BEP20 Withdrawal</span>
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

        <?php if ($isWithdrawalDisabled): ?>
            <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 12px; background: rgba(234, 179, 8, 0.12); color: #854d0e; border: 1px solid rgba(234, 179, 8, 0.3);">
                <i class="zmdi zmdi-lock mr-2" style="font-size: 18px;"></i>
                <strong>Withdrawal Suspended:</strong> 
                <?php echo $isGlobalDisabled ? 'Global withdrawals are currently paused by System Admin.' : 'Withdrawal permission is disabled for your account.'; ?>
            </div>
        <?php endif; ?>

        <!-- Balance Stat Cards -->
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm p-4" style="background: linear-gradient(135deg, rgba(22, 163, 74, 0.08) 0%, rgba(2, 132, 199, 0.08) 100%), #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #ffffff; box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);">
                            <i class="zmdi zmdi-balance-wallet zmdi-hc-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold">Available Wallet Balance</span>
                            <h3 class="mb-0 font-weight-bold" style="color: #0f172a;">
                                <?php echo formatCurrency($userBal, $selectedCurrency); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Form Card -->
        <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-money-off mr-2" style="color: #16a34a;"></i> Submit Withdrawal Request
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="withdraw.php">
                    <input type="hidden" name="action" value="request_withdrawal">
                    
                    <div class="form-group mb-3">
                        <label for="withdrawal_method" class="font-weight-bold small text-uppercase" style="color: #475569;">Withdrawal Method</label>
                        <select class="form-control form-control-lg" id="withdrawal_method" name="withdrawal_method" required <?php echo $isWithdrawalDisabled ? 'disabled' : ''; ?> style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;">
                            <option value="INR">1. INR Direct Bank Withdrawal (Bank Transfer)</option>
                            <option value="BEP20">2. BEP20 Crypto Transfer (USDT / BEP20)</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="amount" class="font-weight-bold small text-uppercase" style="color: #475569;">Withdrawal Amount ($ USD)</label>
                        <input type="number" step="0.01" min="10" class="form-control form-control-lg" id="amount" name="amount" placeholder="Enter amount (Minimum $10)" required <?php echo $isWithdrawalDisabled ? 'disabled' : ''; ?> style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;">
                    </div>

                    <div class="form-group mb-4" id="bep20_info_box" style="display: none;">
                        <label class="font-weight-bold small text-uppercase" style="color: #475569;">BEP20 Address Destination</label>
                        <input type="text" class="form-control form-control-lg" value="<?php echo htmlspecialchars($bep20Addr); ?>" readonly style="border-radius: 10px; background: #f8fafc; border: 1px solid #cbd5e1; font-size: 14px; font-weight: 600;">
                        <?php if (empty($bep20Addr)): ?>
                            <small class="text-danger font-weight-semibold d-block mt-2"><i class="zmdi zmdi-alert-triangle mr-1"></i> BEP20 Address missing! Please update your BEP20 address in <a href="settings.php" class="text-primary font-weight-bold">Settings</a> first.</small>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn px-4 py-3 font-weight-bold" <?php echo ($isWithdrawalDisabled) ? 'disabled' : ''; ?> style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                        <i class="zmdi zmdi-send mr-2"></i> Submit Withdrawal Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('withdrawal_method').addEventListener('change', function() {
    var bep20Box = document.getElementById('bep20_info_box');
    if (this.value === 'BEP20') {
        bep20Box.style.display = 'block';
    } else {
        bep20Box.style.display = 'none';
    }
});
</script>

<?php include 'common/footer.php'; ?>

