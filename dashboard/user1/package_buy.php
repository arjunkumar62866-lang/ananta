<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php
include "common/header.php";

$anantaPackages = getAnantaPackageConfigs(true, $pdo);

// Fallback if database table is empty for any reason
if (empty($anantaPackages)) {
    $anantaPackages = [
        ['package_id' => 'BASIC', 'package_name' => 'Basic Package', 'min_investment_usd' => 145.00, 'max_investment_usd' => 1000.00, 'lock_period_months' => 48, 'withdrawal_deduction_percent' => 15.00, 'bonus_percentage' => 0.00, 'status' => 1],
        ['package_id' => 'ADVANCE', 'package_name' => 'Advance Package', 'min_investment_usd' => 1001.00, 'max_investment_usd' => null, 'lock_period_months' => 48, 'withdrawal_deduction_percent' => 15.00, 'bonus_percentage' => 0.00, 'status' => 1],
        ['package_id' => 'PREMIUM', 'package_name' => 'Premium Package', 'min_investment_usd' => 12501.00, 'max_investment_usd' => null, 'lock_period_months' => 48, 'withdrawal_deduction_percent' => 15.00, 'bonus_percentage' => 0.00, 'status' => 1],
        ['package_id' => 'BONUS_30', 'package_name' => '30% Bonus Package', 'min_investment_usd' => 145.00, 'max_investment_usd' => null, 'lock_period_months' => 6, 'withdrawal_deduction_percent' => 15.00, 'bonus_percentage' => 30.00, 'status' => 1],
        ['package_id' => 'TOUR', 'package_name' => 'Tour Package', 'min_investment_usd' => 145.00, 'max_investment_usd' => null, 'lock_period_months' => 48, 'withdrawal_deduction_percent' => 15.00, 'bonus_percentage' => 0.00, 'status' => 1],
    ];
}

// Map ID numeric fallback if sent
$pkgCodeMap = [
    '1' => 'BASIC',
    '2' => 'ADVANCE',
    '3' => 'PREMIUM',
    '4' => 'BONUS_30',
    '5' => 'TOUR'
];

$alertMsg = null;
$alertType = null;

if (isset($_POST["submit"])) {
    $rawPkgId = trim($_POST['package_id'] ?? '');
    $priceInput = (float)($_POST['price'] ?? $_POST['amount'] ?? 0);

    $package_code = isset($pkgCodeMap[$rawPkgId]) ? $pkgCodeMap[$rawPkgId] : strtoupper($rawPkgId);

    // If user enters amount in INR, convert to USD ($1 = ₹90 factor) if > 5000 and matches INR
    $amount_usd = $priceInput;
    if (isset($_POST['currency_mode']) && $_POST['currency_mode'] === 'INR') {
        $amount_usd = round($priceInput / 90.0, 2);
    } elseif ($priceInput >= 13000 && !empty($priceInput)) {
        // High amount input assumes INR if over $1000 equivalent
        $amount_usd = round($priceInput / 90.0, 2);
    }

    $res = processAnantaPackageInvestment($userid, $package_code, $amount_usd, $pdo);

    if ($res['status'] === 'success') {
        $msg = addslashes($res['message']);
        echo "<script>alert('{$msg}'); window.location.href = 'index.php';</script>";
        exit();
    } else {
        $alertMsg = $res['message'];
        $alertType = 'danger';
    }
}
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

/* Header Card */
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

/* Package Grid Cards */
.package-card {
    background: #ffffff;
    border-radius: 20px;
    border: 2px solid #e2e8f0;
    padding: 22px 20px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.package-card:hover {
    transform: translateY(-4px);
    border-color: #0284c7;
    box-shadow: 0 14px 30px rgba(2, 132, 199, 0.12);
}

.package-card.selected {
    border-color: #16a34a;
    background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
    box-shadow: 0 12px 28px rgba(22, 163, 74, 0.15);
}

.package-card.selected::after {
    content: '✓ Selected';
    position: absolute;
    top: 14px;
    right: 14px;
    background: #16a34a;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 100px;
    letter-spacing: 0.5px;
}

.package-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 100px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.badge-basic { background: rgba(2, 132, 199, 0.12); color: #0284c7; }
.badge-advance { background: rgba(147, 51, 234, 0.12); color: #9333ea; }
.badge-premium { background: rgba(234, 88, 12, 0.12); color: #ea580c; }
.badge-bonus { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
.badge-tour { background: rgba(14, 165, 233, 0.12); color: #0ea5e9; }

.package-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}

.package-range {
    font-size: 16px;
    font-weight: 800;
    color: #16a34a;
    margin-bottom: 14px;
}

.package-feature-list {
    list-style: none;
    padding: 0;
    margin: 0 0 18px 0;
    font-size: 13px;
    color: #475569;
}

.package-feature-list li {
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.package-feature-list li i {
    color: #16a34a;
    font-size: 14px;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 22px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
}

.card-header-title h4 {
    margin: 0;
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
}

label.form-label, label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
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

.btn-ananta-submit {
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    height: 52px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25) !important;
    cursor: pointer !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}

.btn-ananta-submit:hover {
    transform: translateY(-2px) !important;
    color: #ffffff !important;
}

/* Calculation summary box */
.calc-summary-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 24px;
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
                              <i class="fa fa-shopping-cart"></i>
                          </div>
                          <div>
                              <div class="d-flex align-items-center gap-2 mb-1">
                                  <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">ANANTA NIVESH PACKAGES</span>
                                  <span style="font-size: 12px; color: #64748b; font-weight: 600;">PORTFOLIO GROWTH</span>
                              </div>
                              <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                  Select & Buy <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Investment Package</span> 🚀
                              </h4>
                              <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                  Select an official package, enter your investment amount, and lock in guaranteed capital growth.
                              </p>
                          </div>
                      </div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                          <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(2, 132, 199, 0.25); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                              <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Fund Balance</span>
                              <span class="font-weight-bold" style="font-size: 18px; color: #0284c7; font-weight: 800;">
                                  $<?php echo number_format((float)(($pin_wallet ?? 0) / 90.0), 2); ?>
                                  <small style="font-size: 12px; color: #64748b;">(₹<?php echo number_format((float)($pin_wallet ?? 0), 2); ?>)</small>
                              </span>
                          </div>
                          <a href="fund-request.php" class="btn btn-outline-success font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                              <i class="fa fa-plus-circle me-1"></i> Add Fund
                          </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <?php if (!empty($alertMsg)): ?>
      <div class="alert alert-<?= $alertType; ?> alert-dismissible fade show mb-4" role="alert" style="border-radius: 14px; font-weight: 600;">
          <i class="fa fa-exclamation-circle me-2"></i> <?= htmlspecialchars($alertMsg); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <?php endif; ?>

      <!-- Package Cards Grid -->
      <div class="row mb-4">
          <div class="col-12">
              <h5 class="font-weight-bold mb-3" style="color: #0f172a; font-size: 17px;">
                  <i class="fa fa-cubes text-primary me-2"></i> Available Ananta Nivesh Packages
              </h5>
          </div>

          <?php 
          $badgeClasses = [
              'BASIC' => 'badge-basic',
              'ADVANCE' => 'badge-advance',
              'PREMIUM' => 'badge-premium',
              'BONUS_30' => 'badge-bonus',
              'TOUR' => 'badge-tour'
          ];
          foreach ($anantaPackages as $p): 
              $pCode = strtoupper($p['package_id']);
              $pMin = (float)$p['min_investment_usd'];
              $pMax = isset($p['max_investment_usd']) && $p['max_investment_usd'] !== null ? (float)$p['max_investment_usd'] : null;
              $pLock = (int)$p['lock_period_months'];
              $pDeduct = (float)$p['withdrawal_deduction_percent'];
              $pBonus = (float)$p['bonus_percentage'];

              $rangeStr = "$" . number_format($pMin, 0);
              if ($pMax !== null) {
                  $rangeStr .= " – $" . number_format($pMax, 0);
              } else {
                  $rangeStr .= " – No Limit";
              }
              $badgeCls = $badgeClasses[$pCode] ?? 'badge-basic';
          ?>
          <div class="col-xl-4 col-md-6 mb-4">
              <div class="package-card" id="card-<?= $pCode; ?>" onclick="selectPackageCard('<?= $pCode; ?>', <?= $pMin; ?>, '<?= $pMax !== null ? $pMax : 'NULL'; ?>', <?= $pLock; ?>, <?= $pBonus; ?>, <?= $pDeduct; ?>)">
                  <div>
                      <span class="package-badge <?= $badgeCls; ?>"><?= htmlspecialchars($pCode); ?></span>
                      <h4 class="package-title"><?= htmlspecialchars($p['package_name']); ?></h4>
                      <div class="package-range"><?= $rangeStr; ?></div>

                      <ul class="package-feature-list">
                          <li><i class="fa fa-check-circle"></i> <strong>Lock Period:</strong> <?= $pLock; ?> Months</li>
                          <li><i class="fa fa-check-circle"></i> <strong>Capital Deduction:</strong> <?= number_format($pDeduct, 0); ?>% on Maturity</li>
                          <?php if ($pBonus > 0): ?>
                          <li><i class="fa fa-star text-warning"></i> <strong>Bonus Credit:</strong> <?= number_format($pBonus, 0); ?>% Bonus Wallet</li>
                          <?php else: ?>
                          <li><i class="fa fa-shield text-info"></i> <strong>Standard Yield:</strong> Daily Portfolio Income</li>
                          <?php endif; ?>
                      </ul>
                  </div>

                  <button type="button" class="btn btn-sm btn-outline-primary w-100 font-weight-bold" style="border-radius: 10px;">
                      Select <?= htmlspecialchars($p['package_name']); ?>
                  </button>
              </div>
          </div>
          <?php endforeach; ?>
      </div>

      <!-- Purchase Form Row -->
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="ananta-fintech-card">
            
            <div class="card-header-bar">
                <div class="card-header-title">
                    <h4><i class="fa fa-check-circle text-primary me-2"></i> Investment Confirmation</h4>
                    <p>Enter your desired investment amount to complete activation</p>
                </div>
            </div>

            <div class="p-4 p-md-5">

              <form method="post" id="form-data">
                
                <div class="form-group mb-4">
                  <label>User ID</label>
                  <input type="text" name="userid" class="form-control font-weight-bold" value="<?php echo $hmpre; ?><?php echo $userid;?>" readonly style="height: 48px; background: #f8fafc;">    
                </div>
                
                <div class="form-group mb-4">
                  <label>Select Package</label>
                  <select name="package_id" id="package_id" class="form-control" required style="height: 48px;" onchange="onPackageSelectChange()">
                      <option value="">-- Choose Package --</option>
                      <?php foreach ($anantaPackages as $p): 
                          $pCode = strtoupper($p['package_id']);
                          $pMin = (float)$p['min_investment_usd'];
                          $pMax = isset($p['max_investment_usd']) && $p['max_investment_usd'] !== null ? (float)$p['max_investment_usd'] : null;
                          $rangeLabel = "$" . number_format($pMin, 0) . ($pMax !== null ? " – $" . number_format($pMax, 0) : " – No Limit");
                      ?>
                      <option value="<?= $pCode; ?>" data-min="<?= $pMin; ?>" data-max="<?= $pMax !== null ? $pMax : 'NULL'; ?>" data-lock="<?= (int)$p['lock_period_months']; ?>" data-bonus="<?= (float)$p['bonus_percentage']; ?>" data-deduct="<?= (float)$p['withdrawal_deduction_percent']; ?>">
                          <?= htmlspecialchars($p['package_name']); ?> (<?= $rangeLabel; ?>)
                      </option>
                      <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group mb-4">
                  <label>Investment Amount ($ USD)</label>
                  <input type="number" step="1" name="price" id="price" class="form-control" placeholder="Enter amount in USD (e.g. 145)" required style="height: 48px;" oninput="updateCalcSummary()">
                  <small class="text-muted mt-1 d-block" id="inr-equivalent-text">Conversion: $1 = ₹90</small>
                </div>

                <!-- Live Summary Box -->
                <div class="calc-summary-box" id="calc-summary-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-weight-bold text-muted" style="font-size: 12px; text-transform: uppercase;">Selected Package:</span>
                        <span class="font-weight-bold text-primary" id="sum-pkg-name">None Selected</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-weight-bold text-muted" style="font-size: 12px; text-transform: uppercase;">Allowed Investment Range:</span>
                        <span class="font-weight-bold text-dark" id="sum-pkg-range">$0 – $0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-weight-bold text-muted" style="font-size: 12px; text-transform: uppercase;">Lock Period:</span>
                        <span class="font-weight-bold text-dark" id="sum-lock-period">0 Months</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-weight-bold text-muted" style="font-size: 12px; text-transform: uppercase;">Estimated Maturity Date:</span>
                        <span class="font-weight-bold text-success" id="sum-maturity-date">N/A</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" id="bonus-row" style="display: none !important;">
                        <span class="font-weight-bold text-muted" style="font-size: 12px; text-transform: uppercase;">30% Bonus Wallet Credit:</span>
                        <span class="font-weight-bold text-warning" id="sum-bonus-amt">$0.00</span>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-ananta-submit">
                  <i class="fa fa-shopping-cart me-1"></i> Confirm & Buy Investment
                </button>
                
              </form>

            </div>
          </div>
        </div>
      </div>

      <div class="overlay toggle-menu"></div>

    </div>
  </div>

  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
  <?php include 'common/footer.php' ?>

</div>

<script>
let currentSelectedCode = '';

function selectPackageCard(code, minAmt, maxAmt, lockMonths, bonusPct, deductPct) {
    currentSelectedCode = code;
    
    // Highlight Card
    $('.package-card').removeClass('selected');
    $('#card-' + code).addClass('selected');

    // Set Select dropdown
    $('#package_id').val(code);

    // Auto-set min investment if input is empty
    let curVal = parseFloat($('#price').val()) || 0;
    if (curVal < minAmt) {
        $('#price').val(minAmt);
    }

    updateCalcSummary();
}

function onPackageSelectChange() {
    let code = $('#package_id').val();
    $('.package-card').removeClass('selected');
    if (code) {
        $('#card-' + code).addClass('selected');
        let opt = $('#package_id option:selected');
        let minAmt = parseFloat(opt.data('min')) || 0;
        let curVal = parseFloat($('#price').val()) || 0;
        if (curVal < minAmt) {
            $('#price').val(minAmt);
        }
    }
    updateCalcSummary();
}

function updateCalcSummary() {
    let opt = $('#package_id option:selected');
    let code = $('#package_id').val();

    if (!code) {
        $('#sum-pkg-name').text('None Selected');
        $('#sum-pkg-range').text('$0 – $0');
        $('#sum-lock-period').text('0 Months');
        $('#sum-maturity-date').text('N/A');
        $('#bonus-row').attr('style', 'display: none !important');
        $('#inr-equivalent-text').text('Conversion: $1 = ₹90');
        return;
    }

    let pkgName = opt.text().split('(')[0].trim();
    let minAmt = parseFloat(opt.data('min')) || 0;
    let maxAmtRaw = opt.data('max');
    let lockMonths = parseInt(opt.data('lock')) || 48;
    let bonusPct = parseFloat(opt.data('bonus')) || 0;

    let maxStr = (maxAmtRaw !== 'NULL' && maxAmtRaw !== null && maxAmtRaw > 0) ? ('$' + parseFloat(maxAmtRaw).toLocaleString()) : 'No Limit';
    let rangeStr = '$' + minAmt.toLocaleString() + ' – ' + maxStr;

    $('#sum-pkg-name').text(pkgName);
    $('#sum-pkg-range').text(rangeStr);
    $('#sum-lock-period').text(lockMonths + ' Months');

    // Calculate Maturity Date
    let d = new Date();
    d.setMonth(d.getMonth() + lockMonths);
    let matStr = d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    $('#sum-maturity-date').text(matStr);

    let priceUsd = parseFloat($('#price').val()) || 0;
    let priceInr = priceUsd * 90;
    $('#inr-equivalent-text').text('Amount in ₹: ₹' + priceInr.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

    if (bonusPct > 0 && priceUsd > 0) {
        let bonusAmt = priceUsd * (bonusPct / 100.0);
        $('#sum-bonus-amt').text('$' + bonusAmt.toFixed(2));
        $('#bonus-row').removeAttr('style');
    } else {
        $('#bonus-row').attr('style', 'display: none !important');
    }
}

$(document).ready(function() {
    // Select BASIC by default if nothing selected
    if ($('#package_id option').length > 1) {
        $('#package_id').val('BASIC');
        selectPackageCard('BASIC', 145, 1000, 48, 0, 15);
    }
});
</script>

</body>
</html>
