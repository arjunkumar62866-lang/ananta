<?php
session_start();
require_once 'common/connection.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header('Location: index.php');
    exit;
}

$userid = $_SESSION['userid'];
$selectedCurrency = getUserCurrency();
$userBalUSD = getUserWalletBalance($userid, $pdo);

$msg = '';
$msgType = '';
$searchResult = null;

// Handle AJAX User Search
if (isset($_GET['action']) && $_GET['action'] === 'search_user') {
    header('Content-Type: application/json');
    $targetId = $_GET['target_id'] ?? '';
    $res = searchUserForActivation($targetId, $pdo);
    echo json_encode($res);
    exit;
}

// Handle Form Submissions (Self Activation & Other User Activation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $txnKey = $_POST['txn_key'] ?? '';
    
    if ($action === 'self_activate') {
        $res = processAccountActivation($userid, $userid, $txnKey, $pdo);
        if ($res['status'] === 'success') {
            $msg = $res['message'];
            $msgType = 'success';
        } else {
            $msg = $res['message'];
            $msgType = 'danger';
        }
    } elseif ($action === 'other_activate') {
        $targetUserid = $_POST['target_user_id'] ?? '';
        $res = processAccountActivation($userid, $targetUserid, $txnKey, $pdo);
        if ($res['status'] === 'success') {
            $msg = $res['message'];
            $msgType = 'success';
        } else {
            $msg = $res['message'];
            $msgType = 'danger';
        }
    }
}

// Refresh status & history after actions
$actStatus = getUserAccountActivationStatus($userid, $pdo);

// Date filters for history
$myFromDate = $_GET['my_from_date'] ?? null;
$myToDate   = $_GET['my_to_date'] ?? null;
$myHistory  = getMyActivationHistory($userid, $myFromDate, $myToDate, $pdo);

$otherFromDate = $_GET['other_from_date'] ?? null;
$otherToDate   = $_GET['other_to_date'] ?? null;
$otherHistory  = getOtherUserActivationHistory($userid, $otherFromDate, $otherToDate, $pdo);

$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'other' ? 'other' : 'my';

include 'common/header.php';
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3" style="border-bottom: 2px solid #e2e8f0;">
            <div class="mb-2 mb-md-0">
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="zmdi zmdi-shield-check mr-2" style="color: #9333ea;"></i> User Account Activation ($11 Unlock Access)
                </h4>
                <p class="small mb-0" style="color: #64748b !important;">Activate your account or sponsor another member for 4-Year access validity</p>
            </div>
            <div>
                <nav aria-label="breadcrumb">
                    <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                        <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                        <span style="color: #94a3b8; font-weight: 400;">/</span>
                        <span style="color: #0f172a; font-weight: 700;">Activate Account</span>
                    </div>
                </nav>
            </div>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show border-0 shadow-sm rounded-lg mb-4" role="alert" style="border-radius: 12px;">
                <i class="zmdi zmdi-info-outline mr-2"></i> <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Stat Overview Row: Account Status & Wallet Balance -->
        <div class="row mb-4">
            <!-- Current Account Status Card -->
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="card border-0 shadow-sm p-4 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 6px solid <?php echo $actStatus['is_active'] ? '#16a34a' : ($actStatus['is_expired'] ? '#dc2626' : '#ca8a04'); ?> !important; border-radius: 18px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 mr-3" style="background: <?php echo $actStatus['is_active'] ? 'rgba(22, 163, 74, 0.12)' : ($actStatus['is_expired'] ? 'rgba(220, 38, 38, 0.12)' : 'rgba(202, 138, 4, 0.12)'); ?>; color: <?php echo $actStatus['is_active'] ? '#16a34a' : ($actStatus['is_expired'] ? '#dc2626' : '#ca8a04'); ?>;">
                                <i class="zmdi <?php echo $actStatus['is_active'] ? 'zmdi-check-all' : ($actStatus['is_expired'] ? 'zmdi-time-restore' : 'zmdi-shield-security'); ?> zmdi-hc-3x"></i>
                            </div>
                            <div>
                                <span class="small text-uppercase font-weight-bold text-muted" style="letter-spacing: 0.5px;">Account Status</span>
                                <h3 class="mb-0 font-weight-bold" style="color: <?php echo $actStatus['is_active'] ? '#16a34a' : ($actStatus['is_expired'] ? '#dc2626' : '#ca8a04'); ?>;">
                                    <?php echo $actStatus['status']; ?>
                                </h3>
                            </div>
                        </div>
                        <div class="text-right">
                            <?php if ($actStatus['is_active']): ?>
                                <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-size: 13px;">
                                    <?php echo $actStatus['remaining_days']; ?> Days Remaining
                                </span>
                            <?php elseif ($actStatus['is_expired']): ?>
                                <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(220, 38, 38, 0.15); color: #dc2626; font-size: 13px;">
                                    EXPIRED
                                </span>
                            <?php else: ?>
                                <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(202, 138, 4, 0.15); color: #ca8a04; font-size: 13px;">
                                    UNACTIVATED
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($actStatus['is_active']): ?>
                        <div class="mt-3 pt-3 border-top d-flex justify-content-between small text-muted font-weight-semibold">
                            <span><i class="zmdi zmdi-calendar mr-1"></i> Activated: <?php echo date('d-M-Y', strtotime($actStatus['start_date'])); ?></span>
                            <span><i class="zmdi zmdi-time mr-1"></i> Valid Until: <?php echo date('d-M-Y', strtotime($actStatus['expiry_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Balance & Fee Card -->
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="card border-0 shadow-sm p-4 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 6px solid #9333ea !important; border-radius: 18px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 mr-3" style="background: rgba(147, 51, 234, 0.12); color: #9333ea;">
                                <i class="zmdi zmdi-balance-wallet zmdi-hc-3x"></i>
                            </div>
                            <div>
                                <span class="small text-uppercase font-weight-bold text-muted" style="letter-spacing: 0.5px;">Available Wallet Balance</span>
                                <h3 class="mb-0 font-weight-bold" style="color: #0f172a;">
                                    <?php echo formatCurrency($userBalUSD, $selectedCurrency); ?>
                                </h3>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="small text-uppercase font-weight-bold text-muted d-block">Activation Fee</span>
                            <span class="font-weight-bold" style="color: #9333ea; font-size: 18px;">
                                <?php echo formatCurrency(11.00, $selectedCurrency); ?> / 4 Years
                            </span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top small text-muted font-weight-semibold">
                        <i class="zmdi zmdi-info-outline mr-1 text-primary"></i> 4-Year Access unlocks team bonuses, ROI yield eligibility & full platform features.
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Column Action Cards: Self Activation & Other User Activation -->
        <div class="row mb-4">
            <!-- Self Account Activation Card -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                            <i class="zmdi zmdi-account-check mr-2" style="color: #16a34a;"></i> Self Account Activation / Renewal
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($actStatus['is_active']): ?>
                            <div class="alert alert-success border-0 p-3 mb-3" style="border-radius: 12px; background: rgba(22, 163, 74, 0.1); color: #16a34a;">
                                <i class="zmdi zmdi-check-circle mr-2" style="font-size: 18px;"></i>
                                <strong>Account Already Active!</strong> You have <strong><?php echo $actStatus['remaining_days']; ?> days</strong> remaining until <?php echo date('d-M-Y', strtotime($actStatus['expiry_date'])); ?>. Duplicate charge is blocked.
                            </div>
                            <button class="btn btn-block py-3 font-weight-bold text-muted" disabled style="border-radius: 10px; background: #e2e8f0; border: none;">
                                <i class="zmdi zmdi-lock mr-2"></i> ACCOUNT ACTIVE (Renewal Allowed After Expiry)
                            </button>
                        <?php else: ?>
                            <form method="POST" action="activate_account.php">
                                <input type="hidden" name="action" value="self_activate">
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-uppercase" style="color: #475569;">Activation Charge</label>
                                    <input type="text" class="form-control form-control-lg" value="<?php echo formatCurrency(11.00, $selectedCurrency); ?> (Valid for 4 Years)" readonly style="background: #f8fafc; font-weight: 700; color: #16a34a;">
                                </div>

                                <div class="form-group mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="self_txn_key" class="font-weight-bold small text-uppercase mb-0" style="color: #475569;">Transaction Key (Security PIN)</label>
                                        <a href="profile.php#security_section" class="small font-weight-bold text-primary text-decoration-none"><i class="zmdi zmdi-lock-outline mr-1"></i>Forgot Key?</a>
                                    </div>
                                    <input type="password" class="form-control form-control-lg" id="self_txn_key" name="txn_key" placeholder="Enter Transaction Key" required style="border-radius: 10px; border: 1px solid #cbd5e1;">
                                </div>

                                <button type="submit" class="btn btn-block py-3 font-weight-bold" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                                    <i class="zmdi zmdi-flash mr-2"></i> <?php echo $actStatus['is_expired'] ? 'RENEW ACCOUNT ($11 / ₹990)' : 'ACTIVATE ACCOUNT ($11 / ₹990)'; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Other User Account Activation Card -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                            <i class="zmdi zmdi-accounts-add mr-2" style="color: #9333ea;"></i> Activate Another User Account
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Step 1: User Search Form -->
                        <div class="form-group mb-3">
                            <label for="search_user_id" class="font-weight-bold small text-uppercase" style="color: #475569;">Target User ID</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" id="search_user_id" placeholder="Enter User ID (e.g. AN1002)" style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1;">
                                <div class="input-group-append">
                                    <button type="button" id="btnSearchUser" class="btn px-4 font-weight-bold" style="background: #9333ea; color: #ffffff; border-radius: 0 10px 10px 0;">
                                        <i class="zmdi zmdi-search mr-1"></i> SEARCH
                                    </button>
                                </div>
                            </div>
                            <small id="searchError" class="text-danger font-weight-bold d-block mt-2" style="display: none;"></small>
                        </div>

                        <!-- Step 2: Confirmation Box (Hidden initially) -->
                        <div id="otherUserResultBox" style="display: none;" class="p-3 mb-3 border rounded-lg background: #f8fafc;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="font-weight-bold" style="color: #0f172a; font-size: 15px;" id="dispTargetName">User Found</span>
                                <span class="badge badge-pill px-3 py-1 font-weight-bold" id="dispTargetStatusBadge">INACTIVE</span>
                            </div>
                            <div class="small text-muted mb-2">
                                <span>User ID: <strong id="dispTargetId" style="color: #9333ea;">-</strong></span> | 
                                <span>Activation Fee: <strong style="color: #16a34a;"><?php echo formatCurrency(11.00, $selectedCurrency); ?></strong></span>
                            </div>
                        </div>

                        <!-- Activation Submission Form (Hidden initially until user search) -->
                        <form method="POST" action="activate_account.php" id="otherActivateForm" style="display: none;">
                            <input type="hidden" name="action" value="other_activate">
                            <input type="hidden" name="target_user_id" id="hidden_target_id">

                            <div class="form-group mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="other_txn_key" class="font-weight-bold small text-uppercase mb-0" style="color: #475569;">Transaction Key (Security PIN)</label>
                                    <a href="profile.php#security_section" class="small font-weight-bold text-primary text-decoration-none"><i class="zmdi zmdi-lock-outline mr-1"></i>Forgot Key?</a>
                                </div>
                                <input type="password" class="form-control form-control-lg" id="other_txn_key" name="txn_key" placeholder="Enter your Transaction Key" required style="border-radius: 10px; border: 1px solid #cbd5e1;">
                            </div>

                            <button type="button" class="btn btn-block py-3 font-weight-bold" id="btnConfirmOtherActivate" style="background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(147, 51, 234, 0.25);">
                                <i class="zmdi zmdi-check-circle mr-2"></i> CONFIRM ACTIVATION FOR USER
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Section with 2 Tabs (My History & Other User History) + Date Range Filters -->
        <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <!-- Pill Navigation Tabs -->
                <div class="d-flex flex-row flex-wrap align-items-center" style="gap: 12px; border: none; margin: 0; padding: 0; width: 100%;">
                    <a class="nav-link font-weight-bold <?php echo ($activeTab === 'my') ? 'active' : ''; ?>" 
                       data-toggle="tab" 
                       href="#tab-my-history" 
                       style="border-radius: 10px; padding: 10px 22px; font-size: 14px; text-transform: none; letter-spacing: normal; white-space: nowrap !important; display: inline-flex !important; align-items: center !important; margin: 0 !important; text-decoration: none; position: relative !important; float: none !important; <?php echo ($activeTab === 'my') ? 'background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);' : 'background: #f1f5f9 !important; color: #475569 !important;'; ?>">
                        <i class="zmdi zmdi-account-box-o mr-2" style="font-size: 16px;"></i> My Activation History
                    </a>
                    <a class="nav-link font-weight-bold <?php echo ($activeTab === 'other') ? 'active' : ''; ?>" 
                       data-toggle="tab" 
                       href="#tab-other-history" 
                       style="border-radius: 10px; padding: 10px 22px; font-size: 14px; text-transform: none; letter-spacing: normal; white-space: nowrap !important; display: inline-flex !important; align-items: center !important; margin: 0 !important; text-decoration: none; position: relative !important; float: none !important; <?php echo ($activeTab === 'other') ? 'background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%) !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(147, 51, 234, 0.25);' : 'background: #f1f5f9 !important; color: #475569 !important;'; ?>">
                        <i class="zmdi zmdi-accounts-list-alt mr-2" style="font-size: 16px;"></i> Other User Activation History
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    
                    <!-- TAB 1: MY ACTIVATION HISTORY -->
                    <div id="tab-my-history" class="tab-pane <?php echo ($activeTab === 'my') ? 'active' : 'fade'; ?>">
                        
                        <!-- Date Filter Form for My History -->
                        <form method="GET" action="activate_account.php" class="mb-4">
                            <input type="hidden" name="tab" value="my">
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-4 mb-2 mb-md-0">
                                    <label class="font-weight-bold small text-uppercase" style="color: #475569;">From Date</label>
                                    <input type="date" class="form-control" name="my_from_date" value="<?php echo htmlspecialchars($myFromDate ?? ''); ?>" style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-md-4 mb-2 mb-md-0">
                                    <label class="font-weight-bold small text-uppercase" style="color: #475569;">To Date</label>
                                    <input type="date" class="form-control" name="my_to_date" value="<?php echo htmlspecialchars($myToDate ?? ''); ?>" style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-md-4 mb-0 d-flex gap-2">
                                    <button type="submit" class="btn px-4 font-weight-bold" style="background: #0284c7; color: #fff; border-radius: 10px; height: 42px;">
                                        <i class="zmdi zmdi-filter-list mr-1"></i> Filter
                                    </button>
                                    <a href="activate_account.php?tab=my" class="btn btn-outline-secondary px-3 font-weight-bold" style="border-radius: 10px; height: 42px; line-height: 28px;">
                                        Reset (Latest 5)
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                                <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="py-3 px-4 text-center">#</th>
                                        <th class="py-3 px-3">Txn ID</th>
                                        <th class="py-3 px-3">Type</th>
                                        <th class="py-3 px-3 text-right">Amount</th>
                                        <th class="py-3 px-3 text-center">Activation Date</th>
                                        <th class="py-3 px-3 text-center">Expiry Date</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <?php if (!empty($myHistory)): ?>
                                        <?php $sr = 1; foreach ($myHistory as $h): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td class="py-3 px-4 text-center font-weight-bold text-muted"><?php echo $sr++; ?></td>
                                                <td class="py-3 px-3">
                                                    <code style="background: #f1f5f9; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-weight: 700;"><?php echo htmlspecialchars($h['transaction_id']); ?></code>
                                                </td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #334155;"><?php echo htmlspecialchars($h['activation_type']); ?></td>
                                                <td class="py-3 px-3 text-right font-weight-bold" style="color: #16a34a;">
                                                    <?php echo formatCurrency((float)$h['amount_usd'], $selectedCurrency); ?>
                                                </td>
                                                <td class="py-3 px-3 text-center text-muted small"><?php echo date('d-M-Y', strtotime($h['activation_start_date'])); ?></td>
                                                <td class="py-3 px-3 text-center text-muted small"><?php echo date('d-M-Y', strtotime($h['activation_expiry_date'])); ?></td>
                                                <td class="py-3 px-3 text-center">
                                                    <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;"><?php echo htmlspecialchars($h['status']); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="zmdi zmdi-folder-outline zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                                No personal activation history records found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: OTHER USER ACTIVATION HISTORY -->
                    <div id="tab-other-history" class="tab-pane <?php echo ($activeTab === 'other') ? 'active' : 'fade'; ?>">
                        
                        <!-- Date Filter Form for Other User History -->
                        <form method="GET" action="activate_account.php" class="mb-4">
                            <input type="hidden" name="tab" value="other">
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-4 mb-2 mb-md-0">
                                    <label class="font-weight-bold small text-uppercase" style="color: #475569;">From Date</label>
                                    <input type="date" class="form-control" name="other_from_date" value="<?php echo htmlspecialchars($otherFromDate ?? ''); ?>" style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-md-4 mb-2 mb-md-0">
                                    <label class="font-weight-bold small text-uppercase" style="color: #475569;">To Date</label>
                                    <input type="date" class="form-control" name="other_to_date" value="<?php echo htmlspecialchars($otherToDate ?? ''); ?>" style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-md-4 mb-0 d-flex gap-2">
                                    <button type="submit" class="btn px-4 font-weight-bold" style="background: #9333ea; color: #fff; border-radius: 10px; height: 42px;">
                                        <i class="zmdi zmdi-filter-list mr-1"></i> Filter
                                    </button>
                                    <a href="activate_account.php?tab=other" class="btn btn-outline-secondary px-3 font-weight-bold" style="border-radius: 10px; height: 42px; line-height: 28px;">
                                        Reset (Latest 5)
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                                <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="py-3 px-4 text-center">#</th>
                                        <th class="py-3 px-3">Txn ID</th>
                                        <th class="py-3 px-3">Activated User Name</th>
                                        <th class="py-3 px-3">Activated User ID</th>
                                        <th class="py-3 px-3 text-right">Amount Paid</th>
                                        <th class="py-3 px-3 text-center">Activation Date</th>
                                        <th class="py-3 px-3 text-center">Expiry Date</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <?php if (!empty($otherHistory)): ?>
                                        <?php $sr = 1; foreach ($otherHistory as $h): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td class="py-3 px-4 text-center font-weight-bold text-muted"><?php echo $sr++; ?></td>
                                                <td class="py-3 px-3">
                                                    <code style="background: #f1f5f9; color: #9333ea; padding: 4px 8px; border-radius: 6px; font-weight: 700;"><?php echo htmlspecialchars($h['transaction_id']); ?></code>
                                                </td>
                                                <td class="py-3 px-3 font-weight-bold" style="color: #0f172a;"><?php echo htmlspecialchars($h['target_name'] ?: 'N/A'); ?></td>
                                                <td class="py-3 px-3 font-weight-bold" style="color: #9333ea;"><?php echo htmlspecialchars($h['target_user_id']); ?></td>
                                                <td class="py-3 px-3 text-right font-weight-bold" style="color: #16a34a;">
                                                    <?php echo formatCurrency((float)$h['amount_usd'], $selectedCurrency); ?>
                                                </td>
                                                <td class="py-3 px-3 text-center text-muted small"><?php echo date('d-M-Y', strtotime($h['activation_start_date'])); ?></td>
                                                <td class="py-3 px-3 text-center text-muted small"><?php echo date('d-M-Y', strtotime($h['activation_expiry_date'])); ?></td>
                                                <td class="py-3 px-3 text-center">
                                                    <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;"><?php echo htmlspecialchars($h['status']); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="zmdi zmdi-accounts-outline zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                                No other user activations performed yet.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Confirmation Modal for Other User Activation -->
<div class="modal fade" id="modalOtherConfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header bg-white py-3 border-bottom">
                <h5 class="modal-title font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-shield-check text-purple mr-2"></i> Confirm Account Activation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle p-3 d-inline-flex mb-3" style="background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                    <i class="zmdi zmdi-account-box-phone zmdi-hc-3x"></i>
                </div>
                <h5 class="font-weight-bold mb-1" id="modalTargetName" style="color: #0f172a;">User Name</h5>
                <p class="text-muted small mb-3">User ID: <strong id="modalTargetId" style="color: #9333ea;">-</strong></p>
                
                <div class="p-3 border rounded-lg background: #f8fafc; text-left mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Activation Fee:</span>
                        <strong class="text-success"><?php echo formatCurrency(11.00, $selectedCurrency); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Access Validity:</span>
                        <strong style="color: #0f172a;">4 Years (1461 Days)</strong>
                    </div>
                </div>
                
                <p class="small text-muted mb-0">Are you sure you want to deduct <?php echo formatCurrency(11.00, $selectedCurrency); ?> from your wallet to activate this member?</p>
            </div>
            <div class="modal-footer bg-light border-top p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 10px;">CANCEL</button>
                <button type="button" class="btn px-4 font-weight-bold" id="btnModalSubmitForm" style="background: #9333ea; color: #fff; border-radius: 10px;">CONFIRM & ACTIVATE</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSearch = document.getElementById('btnSearchUser');
    const inputSearch = document.getElementById('search_user_id');
    const searchError = document.getElementById('searchError');
    const resultBox = document.getElementById('otherUserResultBox');
    const dispName = document.getElementById('dispTargetName');
    const dispId = document.getElementById('dispTargetId');
    const dispBadge = document.getElementById('dispTargetStatusBadge');
    const formOther = document.getElementById('otherActivateForm');
    const hiddenTarget = document.getElementById('hidden_target_id');
    const btnConfirm = id => document.getElementById(id);

    btnSearch.addEventListener('click', function() {
        const val = inputSearch.value.trim();
        searchError.style.display = 'none';
        resultBox.style.display = 'none';
        formOther.style.display = 'none';

        if (!val) {
            searchError.textContent = 'Please enter a target User ID.';
            searchError.style.display = 'block';
            return;
        }

        btnSearch.disabled = true;
        btnSearch.innerHTML = '<i class="zmdi zmdi-spinner zmdi-hc-spin"></i> Searching...';

        fetch('activate_account.php?action=search_user&target_id=' + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                btnSearch.disabled = false;
                btnSearch.innerHTML = '<i class="zmdi zmdi-search mr-1"></i> SEARCH';

                if (data.status === 'success') {
                    const u = data.user;
                    dispName.textContent = u.name;
                    dispId.textContent = u.userid;
                    hiddenTarget.value = u.userid;

                    if (u.act_status === 'ACTIVE') {
                        dispBadge.className = 'badge badge-pill px-3 py-1 font-weight-bold badge-success';
                        dispBadge.textContent = 'ACTIVE (' + u.rem_days + ' Days Rem)';
                    } else if (u.act_status === 'EXPIRED') {
                        dispBadge.className = 'badge badge-pill px-3 py-1 font-weight-bold badge-danger';
                        dispBadge.textContent = 'EXPIRED';
                    } else {
                        dispBadge.className = 'badge badge-pill px-3 py-1 font-weight-bold badge-warning';
                        dispBadge.textContent = 'INACTIVE';
                    }

                    resultBox.style.display = 'block';

                    if (u.act_status === 'ACTIVE') {
                        formOther.style.display = 'none';
                        searchError.textContent = 'User ' + u.name + ' (' + u.userid + ') is already active!';
                        searchError.style.display = 'block';
                    } else {
                        formOther.style.display = 'block';
                    }
                } else {
                    searchError.textContent = data.message;
                    searchError.style.display = 'block';
                }
            })
            .catch(err => {
                btnSearch.disabled = false;
                btnSearch.innerHTML = '<i class="zmdi zmdi-search mr-1"></i> SEARCH';
                searchError.textContent = 'Error searching user ID.';
                searchError.style.display = 'block';
            });
    });

    // Confirmation Modal Handler
    const btnTriggerConfirm = document.getElementById('btnConfirmOtherActivate');
    btnTriggerConfirm.addEventListener('click', function() {
        const txnKey = document.getElementById('other_txn_key').value.trim();
        if (!txnKey) {
            alert('Please enter your Transaction Key before proceeding.');
            return;
        }

        document.getElementById('modalTargetName').textContent = dispName.textContent;
        document.getElementById('modalTargetId').textContent = hiddenTarget.value;
        $('#modalOtherConfirm').modal('show');
    });

    document.getElementById('btnModalSubmitForm').addEventListener('click', function() {
        $('#modalOtherConfirm').modal('hide');
        formOther.submit();
    });
});
</script>

<?php include 'common/footer.php'; ?>
