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

// Handle Net Balance Withdrawal Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_withdrawal') {
    $method = trim($_POST['withdrawal_method'] ?? 'INR');
    $amount = (float)($_POST['amount'] ?? 0);
    $txnKey = trim($_POST['txn_key'] ?? '');

    $res = processUserWithdrawalRequest($userid, $method, $amount, $txnKey, $pdo);
    if ($res['status'] === 'success') {
        $msg = $res['message'];
        $msgType = 'success';
    } else {
        $msg = $res['message'];
        $msgType = 'danger';
    }
}

// Handle Capital Withdrawal Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'capital_withdrawal') {
    $investmentId = (int)($_POST['investment_id'] ?? 0);
    $txnKey = trim($_POST['txn_key'] ?? '');

    $res = processCapitalWithdrawal($userid, $investmentId, $txnKey, $pdo);
    if ($res['status'] === 'success') {
        $msg = $res['message'];
        $msgType = 'success';
    } else {
        $msg = $res['message'];
        $msgType = 'danger';
    }
}

// Fetch User Info & Registered Bank KYC Details
$stmtU = $pdo->prepare("SELECT amount, bep20_address, withdrawal_status, kyc FROM user WHERE userid = :uid");
$stmtU->execute([':uid' => $userid]);
$uData = $stmtU->fetch(PDO::FETCH_ASSOC) ?: [];
$userBal = (float)($uData['amount'] ?? 0);
$bep20Addr = $uData['bep20_address'] ?? '';

$stmtKyc = $pdo->prepare("SELECT holder_name, ac_number, bank, branch, ifsc, status FROM kyc WHERE userid = :uid ORDER BY id DESC LIMIT 1");
$stmtKyc->execute([':uid' => $userid]);
$kycData = $stmtKyc->fetch(PDO::FETCH_ASSOC) ?: [];

$bankAccNo  = trim($kycData['ac_number'] ?? '');
$bankName   = trim($kycData['bank'] ?? '');
$bankHolder = trim($kycData['holder_name'] ?? '');
$bankIfsc   = trim($kycData['ifsc'] ?? '');

// Check Global Withdrawal setting
$stmtSys = $pdo->prepare("SELECT setting_value FROM tbl_system_control WHERE setting_key = 'withdrawal_enable' LIMIT 1");
$stmtSys->execute();
$globalWd = $stmtSys->fetchColumn();
$isGlobalDisabled = ($globalWd !== false && (int)$globalWd === 0);
$isUserDisabled = (isset($uData['withdrawal_status']) && (string)$uData['withdrawal_status'] === '0');
$isWithdrawalDisabled = ($isGlobalDisabled || $isUserDisabled);

// Fetch User Investments for Capital Withdrawal
$stmtInv = $pdo->prepare("SELECT * FROM tbl_roi_one WHERE user_id = :uid ORDER BY id DESC");
$stmtInv->execute([':uid' => $userid]);
$userInvestments = $stmtInv->fetchAll(PDO::FETCH_ASSOC);
$cDate = date('Y-m-d');

// Fetch Net Balance Withdrawal History
$stmtNetHist = $pdo->prepare("
    SELECT id, amount, act_amount, type, subject, withdrawal_method, status, created_date, time
    FROM tbl_transaction
    WHERE user_id = :uid AND (withdrawal_method IS NOT NULL OR subject LIKE '%Withdrawal%')
    ORDER BY id DESC LIMIT 20
");
$stmtNetHist->execute([':uid' => $userid]);
$netWithdrawalHistory = $stmtNetHist->fetchAll(PDO::FETCH_ASSOC);

// Fetch Capital Withdrawal History
$stmtCapHist = $pdo->prepare("
    SELECT c.*, r.name as package_name, r.package_code
    FROM tbl_capital_withdrawal_request c
    LEFT JOIN tbl_roi_one r ON c.investment_id = r.id
    WHERE c.user_id = :uid
    ORDER BY c.id DESC LIMIT 20
");
$stmtCapHist->execute([':uid' => $userid]);
$capitalWithdrawalHistory = $stmtCapHist->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
body.ananta-user-dashboard,
body.bg-theme,
body.bg-theme1 {
    background: #f4f6f8 !important;
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

/* Header Welcome Card */
.income-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
    color: #ffffff;
    font-size: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

/* Section Cards & Dark High-Contrast Titles */
.withdrawal-section-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.withdrawal-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
    border-radius: 22px 22px 0 0;
}

.withdrawal-card-header h5 {
    margin: 0;
    font-size: 19px;
    font-weight: 800;
    color: #0f172a !important; /* DARK BLACK HIGH CONTRAST */
    display: flex;
    align-items: center;
    gap: 10px;
}

.withdrawal-card-header p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #475569 !important;
    font-weight: 600;
}

label.form-label, label {
    color: #0f172a !important; /* DARK VISIBLE TEXT */
    font-weight: 800 !important;
    font-size: 12.5px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control, input.form-control, select.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 14.5px !important;
    font-weight: 600 !important;
    padding: 10px 16px !important;
    box-shadow: none !important;
}

.form-control:focus, input.form-control:focus, select.form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
}

.btn-withdrawal-submit {
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    height: 50px !important;
    font-weight: 700 !important;
    font-size: 15.5px !important;
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25) !important;
    cursor: pointer !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}

/* History Tables */
.table-history {
    font-size: 13px;
    color: #0f172a;
}

.table-history thead th {
    background: #f8fafc !important;
    color: #334155 !important;
    font-weight: 800 !important;
    border-bottom: 1.5px solid #e2e8f0 !important;
    text-transform: uppercase;
    font-size: 11.5px;
    letter-spacing: 0.5px;
}
</style>

<body class="ananta-user-dashboard">

<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner"><div class="loader"></div></div>
  </div>
</div>

<div id="wrapper">
  <div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

      <!-- Header Banner -->
      <div class="row mb-4">
          <div class="col-12">
              <div class="card income-header-card border-0 p-4">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                      <div class="d-flex align-items-center gap-3">
                          <div class="income-header-icon">
                              <i class="fa fa-university"></i>
                          </div>
                          <div>
                              <div class="d-flex align-items-center gap-2 mb-1">
                                  <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 800; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">WITHDRAWAL MANAGEMENT</span>
                                  <span style="font-size: 12px; color: #64748b; font-weight: 600;">PAYOUT PORTAL</span>
                              </div>
                              <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                  Withdrawal <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Portal</span> 🏦
                              </h4>
                              <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                  Manage Net Balance Payouts and Package Capital Withdrawals simultaneously.
                              </p>
                          </div>
                      </div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                          <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(2, 132, 199, 0.25); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                              <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Net Available Balance</span>
                              <span class="font-weight-bold" style="font-size: 18px; color: #0284c7; font-weight: 800;">
                                  <?php echo formatCurrency($userBal, $selectedCurrency); ?>
                              </span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <?php if (!empty($msg)): ?>
          <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show border-0 shadow-sm rounded-lg mb-4" role="alert" style="border-radius: 12px; font-weight: 600;">
              <i class="fa fa-info-circle me-2"></i> <?php echo htmlspecialchars($msg); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      <?php endif; ?>

      <?php if ($isWithdrawalDisabled): ?>
          <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 12px; background: rgba(234, 179, 8, 0.12); color: #854d0e; border: 1px solid rgba(234, 179, 8, 0.3);">
              <i class="fa fa-lock me-2" style="font-size: 18px;"></i>
              <strong>Withdrawal Suspended:</strong> 
              <?php echo $isGlobalDisabled ? 'Global withdrawals are currently paused by System Admin.' : 'Withdrawal permission is disabled for your account.'; ?>
          </div>
      <?php endif; ?>

      <!-- BOTH WITHDRAWAL SECTIONS SIDE-BY-SIDE (LEFT & RIGHT COLUMNS) -->
      <div class="row mb-5" style="display: flex; flex-wrap: wrap;">
          
          <!-- LEFT COLUMN: 1. NET BALANCE WITHDRAWAL -->
          <div class="col-lg-6 mb-4 mb-lg-0">
              <div class="withdrawal-section-card">
                  <div class="withdrawal-card-header">
                      <h5>
                          <i class="fa fa-university text-primary"></i> 1. Net Balance Withdrawal
                      </h5>
                      <p>Withdraw from your available Net Balance via Bank or BEP20 USDT</p>
                  </div>
                  <div class="p-4 p-md-4 flex-grow-1 d-flex flex-column justify-content-between">
                      <form method="POST" action="withdraw.php">
                          <input type="hidden" name="action" value="request_withdrawal">
                          
                          <div class="form-group mb-4">
                              <label for="withdrawal_method">Withdrawal Method</label>
                              <select class="form-control" id="withdrawal_method" name="withdrawal_method" required <?php echo $isWithdrawalDisabled ? 'disabled' : ''; ?> style="height: 48px;">
                                  <option value="INR">1. INR Direct Bank Withdrawal (Bank Transfer)</option>
                                  <option value="BEP20">2. BEP20 Crypto Transfer (USDT / BEP20)</option>
                              </select>
                          </div>

                          <div class="form-group mb-4">
                              <label for="amount">Withdrawal Amount ($ USD)</label>
                              <input type="number" step="0.01" min="10" class="form-control" id="amount" name="amount" placeholder="Enter amount (Minimum $10)" required <?php echo $isWithdrawalDisabled ? 'disabled' : ''; ?> style="height: 48px;">
                              <small class="text-muted mt-1 d-block font-weight-bold">Available Net Balance: <?php echo formatCurrency($userBal, $selectedCurrency); ?></small>
                          </div>

                          <div class="form-group mb-4">
                              <div class="d-flex justify-content-between align-items-center mb-1">
                                  <label for="txn_key" class="mb-0">Transaction Key (Security PIN) <span class="text-danger">*</span></label>
                                  <a href="profile.php#security_section" class="small font-weight-bold text-primary text-decoration-none"><i class="zmdi zmdi-lock-outline mr-1"></i>Forgot Transaction Key?</a>
                              </div>
                              <input type="password" class="form-control" id="txn_key" name="txn_key" placeholder="Enter 4-digit Transaction Key" required <?php echo $isWithdrawalDisabled ? 'disabled' : ''; ?> style="height: 48px;">
                          </div>

                          <!-- Registered Bank Account Display for INR Withdrawal -->
                          <div class="form-group mb-4" id="inr_info_box">
                              <label class="font-weight-bold" style="color: #334155;">Registered Bank Account Number (INR Destination)</label>
                              <div class="input-group">
                                  <div class="input-group-prepend">
                                      <span class="input-group-text bg-white border-right-0" style="border-color: #cbd5e1;"><i class="fa fa-university text-primary"></i></span>
                                  </div>
                                  <input type="text" class="form-control font-weight-bold border-left-0" value="<?php echo !empty($bankAccNo) ? htmlspecialchars($bankAccNo) : 'Not Registered / Missing'; ?>" readonly style="height: 48px; background: #f8fafc; color: #0f172a; font-size: 15px;">
                              </div>
                              
                              <?php if (!empty($bankAccNo)): ?>
                                  <div class="p-3 mt-2" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; font-size: 13px;">
                                      <div class="d-flex justify-content-between mb-1">
                                          <span class="text-muted">Account Holder:</span>
                                          <strong class="text-dark"><?php echo htmlspecialchars($bankHolder ?: 'N/A'); ?></strong>
                                      </div>
                                      <div class="d-flex justify-content-between mb-1">
                                          <span class="text-muted">Bank Name:</span>
                                          <strong class="text-dark"><?php echo htmlspecialchars($bankName ?: 'N/A'); ?></strong>
                                      </div>
                                      <div class="d-flex justify-content-between">
                                          <span class="text-muted">IFSC Code:</span>
                                          <strong class="text-dark"><?php echo htmlspecialchars($bankIfsc ?: 'N/A'); ?></strong>
                                      </div>
                                  </div>
                                  <small class="text-success font-weight-bold d-block mt-2">
                                      <i class="fa fa-check-circle me-1"></i> Your INR withdrawal will be credited directly to this verified bank account number.
                                  </small>
                              <?php else: ?>
                                  <small class="text-danger font-weight-bold d-block mt-2">
                                      <i class="fa fa-warning me-1"></i> Registered Bank Account Number missing! Please update your Bank details in <a href="kyc.php" class="text-primary font-weight-bold">KYC Verification</a> first.
                                  </small>
                              <?php endif; ?>
                          </div>

                          <div class="form-group mb-4" id="bep20_info_box" style="display: none;">
                              <label class="font-weight-bold" style="color: #334155;">BEP20 Destination Address</label>
                              <input type="text" class="form-control font-weight-bold" value="<?php echo htmlspecialchars($bep20Addr); ?>" readonly style="height: 48px; background: #f8fafc;">
                              <?php if (empty($bep20Addr)): ?>
                                  <small class="text-danger font-weight-bold d-block mt-2"><i class="fa fa-warning me-1"></i> BEP20 Address missing! Update in <a href="settings.php" class="text-primary">Settings</a> first.</small>
                              <?php endif; ?>
                          </div>

                          <button type="submit" class="btn-withdrawal-submit mt-2" <?php echo ($isWithdrawalDisabled) ? 'disabled' : ''; ?>>
                              <i class="fa fa-paper-plane me-1"></i> Submit Net Balance Withdrawal
                          </button>
                      </form>
                  </div>
              </div>
          </div>

          <!-- RIGHT COLUMN: 2. CAPITAL WITHDRAWAL -->
          <div class="col-lg-6">
              <div class="withdrawal-section-card">
                  <div class="withdrawal-card-header">
                      <h5>
                          <i class="fa fa-unlock-alt text-success"></i> 2. Capital Withdrawal
                      </h5>
                      <p>View package lock periods, maturity eligibility & claim capital payouts</p>
                  </div>
                  <div class="p-4 p-md-4 flex-grow-1">
                      <?php if (empty($userInvestments)): ?>
                          <div class="text-center py-4">
                              <i class="fa fa-cubes text-muted mb-2" style="font-size: 40px;"></i>
                              <h6 class="font-weight-bold text-dark mb-1">No Active Investments</h6>
                              <p class="text-muted small">You currently have no package investments.</p>
                              <a href="package_buy.php" class="btn btn-sm btn-primary font-weight-bold px-3 py-2 mt-1" style="border-radius: 10px;">Buy Package Now</a>
                          </div>
                      <?php else: ?>
                          <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                              <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                  <thead>
                                      <tr>
                                          <th>Inv #</th>
                                          <th>Package</th>
                                          <th>Real Fund</th>
                                          <th>Maturity</th>
                                          <th>Status</th>
                                          <th>Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($userInvestments as $inv): 
                                          $invId = $inv['id'];
                                          $pkgName = htmlspecialchars($inv['name'] ?: $inv['package_code'] ?: 'Standard Package');
                                          $realFundUsd = (float)($inv['real_fund_usd'] > 0 ? $inv['real_fund_usd'] : round(((float)$inv['package']) / 90.0, 2));
                                          $lockMonths = (int)($inv['lock_period_months'] ?: 48);
                                          $invDate = $inv['date'];
                                          $maturityDate = $inv['maturity_date'] ?: date('Y-m-d', strtotime("+{$lockMonths} months", strtotime($invDate)));
                                          $deductPct = (float)($inv['deduction_percent_snapshot'] ?: 15.00);
                                          $deductAmtUsd = round($realFundUsd * ($deductPct / 100.0), 2);
                                          $netWdUsd = round($realFundUsd - $deductAmtUsd, 2);

                                          $isWithdrawn = ($inv['capital_withdrawal_status'] === 'WITHDRAWN');
                                          $isMatured = ($cDate >= $maturityDate);
                                          $isLocked = (!$isWithdrawn && !$isMatured);
                                      ?>
                                      <tr>
                                          <td class="font-weight-bold">#<?= $invId; ?></td>
                                          <td class="font-weight-bold text-primary"><?= $pkgName; ?></td>
                                          <td class="font-weight-bold text-success">$<?= number_format($realFundUsd, 0); ?></td>
                                          <td><small class="font-weight-bold"><?= $maturityDate; ?></small></td>
                                          <td>
                                              <?php if ($isWithdrawn): ?>
                                                  <span class="badge badge-secondary px-2 py-1 font-weight-bold">WITHDRAWN</span>
                                              <?php elseif ($isLocked): ?>
                                                  <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold">🔒 LOCKED</span>
                                              <?php else: ?>
                                                  <span class="badge badge-success px-2 py-1 font-weight-bold">✅ ELIGIBLE</span>
                                              <?php endif; ?>
                                          </td>
                                          <td>
                                              <?php if ($isWithdrawn): ?>
                                                  <button type="button" class="btn btn-xs btn-light font-weight-bold" disabled>Claimed</button>
                                              <?php elseif ($isLocked): ?>
                                                  <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold" disabled>Locked</button>
                                              <?php else: ?>
                                                  <button type="button" class="btn btn-xs btn-success font-weight-bold px-2" data-toggle="modal" data-target="#capWdModal<?= $invId; ?>">
                                                      Claim
                                                  </button>

                                                  <!-- Modal -->
                                                  <div class="modal fade" id="capWdModal<?= $invId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-centered" role="document">
                                                          <div class="modal-content" style="border-radius: 18px; border: none;">
                                                              <div class="modal-header border-0 pb-0">
                                                                  <h5 class="modal-title font-weight-bold text-dark">Confirm Capital Withdrawal</h5>
                                                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                      <span aria-hidden="true">&times;</span>
                                                                  </button>
                                                              </div>
                                                              <form method="POST" action="withdraw.php">
                                                                  <div class="modal-body p-4">
                                                                      <input type="hidden" name="action" value="capital_withdrawal">
                                                                      <input type="hidden" name="investment_id" value="<?= $invId; ?>">

                                                                      <div class="p-3 mb-3" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                                                                          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Package:</span><strong class="text-dark"><?= $pkgName; ?></strong></div>
                                                                          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Real Capital:</span><strong class="text-dark">$<?= number_format($realFundUsd, 2); ?></strong></div>
                                                                          <div class="d-flex justify-content-between mb-1"><span class="text-muted">15% Deduction:</span><strong class="text-danger">-$<?= number_format($deductAmtUsd, 2); ?></strong></div>
                                                                          <hr class="my-2">
                                                                          <div class="d-flex justify-content-between"><span class="font-weight-bold text-dark">Net Cash Payout:</span><strong class="text-success font-weight-bold" style="font-size: 16px;">$<?= number_format($netWdUsd, 2); ?></strong></div>
                                                                      </div>

                                                                      <div class="form-group mb-0">
                                                                          <div class="d-flex justify-content-between align-items-center mb-1">
                                                                              <label for="txn_key_<?= $invId; ?>" class="mb-0">Transaction Key (Security PIN) <span class="text-danger">*</span></label>
                                                                              <a href="profile.php#security_section" class="small font-weight-bold text-primary text-decoration-none"><i class="zmdi zmdi-lock-outline mr-1"></i>Forgot Transaction Key?</a>
                                                                          </div>
                                                                          <input type="password" class="form-control" id="txn_key_<?= $invId; ?>" name="txn_key" placeholder="Enter Transaction Key" required style="height: 48px;">
                                                                      </div>
                                                                  </div>
                                                                  <div class="modal-footer border-0 pt-0 p-4">
                                                                      <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                                                                      <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 10px;">Confirm & Claim</button>
                                                                  </div>
                                                              </form>
                                                          </div>
                                                      </div>
                                                  </div>
                                              <?php endif; ?>
                                          </td>
                                      </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      <?php endif; ?>
                  </div>
              </div>
          </div>
      </div>

      <!-- BOTH WITHDRAWAL HISTORIES SIDE-BY-SIDE (LEFT & RIGHT COLUMNS) -->
      <div class="row">
          
          <!-- LEFT HISTORY: NET BALANCE WITHDRAWAL HISTORY -->
          <div class="col-lg-6 mb-4 mb-lg-0">
              <div class="withdrawal-section-card">
                  <div class="withdrawal-card-header">
                      <h5>
                          <i class="fa fa-history text-primary"></i> Net Balance Withdrawal History
                      </h5>
                      <p>Logs of your past Net Balance payout requests</p>
                  </div>
                  <div class="p-4">
                      <?php if (empty($netWithdrawalHistory)): ?>
                          <div class="text-center py-4 text-muted font-weight-bold small">
                              No Net Balance withdrawal records found.
                          </div>
                      <?php else: ?>
                          <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                              <table class="table table-hover align-middle table-history mb-0">
                                  <thead>
                                      <tr>
                                          <th>#</th>
                                          <th>Method</th>
                                          <th>Amount</th>
                                          <th>Date & Time</th>
                                          <th>Status</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($netWithdrawalHistory as $row): 
                                          $st = (string)$row['status'];
                                          $stBadge = ($st === '1' || strtolower($row['subject'] ?? '') === 'approved') ? '<span class="badge badge-success px-2 py-1">PAID</span>' : (($st === '2') ? '<span class="badge badge-danger px-2 py-1">REJECTED</span>' : '<span class="badge badge-warning text-dark px-2 py-1">PENDING</span>');
                                      ?>
                                      <tr>
                                          <td class="font-weight-bold">#<?= $row['id']; ?></td>
                                          <td><span class="badge badge-info px-2 py-1"><?= htmlspecialchars($row['withdrawal_method'] ?: 'INR'); ?></span></td>
                                          <td class="font-weight-bold text-dark">$<?= number_format((float)$row['amount'], 2); ?></td>
                                          <td><small class="text-muted font-weight-bold"><?= $row['created_date']; ?> <?= $row['time']; ?></small></td>
                                          <td><?= $stBadge; ?></td>
                                      </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      <?php endif; ?>
                  </div>
              </div>
          </div>

          <!-- RIGHT HISTORY: CAPITAL WITHDRAWAL HISTORY -->
          <div class="col-lg-6">
              <div class="withdrawal-section-card">
                  <div class="withdrawal-card-header">
                      <h5>
                          <i class="fa fa-list-alt text-success"></i> Capital Withdrawal History
                      </h5>
                      <p>Logs of your matured capital claims and payouts</p>
                  </div>
                  <div class="p-4">
                      <?php if (empty($capitalWithdrawalHistory)): ?>
                          <div class="text-center py-4 text-muted font-weight-bold small">
                              No capital withdrawal history records found.
                          </div>
                      <?php else: ?>
                          <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                              <table class="table table-hover align-middle table-history mb-0">
                                  <thead>
                                      <tr>
                                          <th>#</th>
                                          <th>Package</th>
                                          <th>Real Fund</th>
                                          <th>Net Paid</th>
                                          <th>Date</th>
                                          <th>Status</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($capitalWithdrawalHistory as $cRow): ?>
                                      <tr>
                                          <td class="font-weight-bold">#<?= $cRow['id']; ?></td>
                                          <td class="font-weight-bold text-primary"><?= htmlspecialchars($cRow['package_name'] ?: $cRow['package_code'] ?: 'ANANTA'); ?></td>
                                          <td class="font-weight-bold">$<?= number_format((float)$cRow['real_fund_usd'], 2); ?></td>
                                          <td class="font-weight-bold text-success">$<?= number_format((float)$cRow['net_withdrawal_usd'], 2); ?></td>
                                          <td><small class="text-muted font-weight-bold"><?= substr($cRow['requested_at'] ?: $cRow['created_at'] ?: '', 0, 10); ?></small></td>
                                          <td><span class="badge badge-success px-2 py-1">PAID</span></td>
                                      </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      <?php endif; ?>
                  </div>
              </div>
          </div>

      </div>

    </div>
  </div>

  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
  <?php include 'common/footer.php' ?>

</div>

<script>
function updateWithdrawalMethodBoxes() {
    var methodSelect = document.getElementById('withdrawal_method');
    if (!methodSelect) return;
    var val = methodSelect.value;
    var bep20Box = document.getElementById('bep20_info_box');
    var inrBox = document.getElementById('inr_info_box');

    if (val === 'BEP20') {
        if (bep20Box) bep20Box.style.display = 'block';
        if (inrBox) inrBox.style.display = 'none';
    } else {
        if (bep20Box) bep20Box.style.display = 'none';
        if (inrBox) inrBox.style.display = 'block';
    }
}

document.getElementById('withdrawal_method').addEventListener('change', updateWithdrawalMethodBoxes);
updateWithdrawalMethodBoxes();
</script>

</body>
</html>
