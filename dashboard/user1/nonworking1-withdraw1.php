<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include "common/header.php";
include 'common/connection.php'; // contains $pdo

$userid = $_SESSION['userid'];   // FIX as per your system

date_default_timezone_set("Asia/Kolkata");
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$todayObj   = new DateTime();

// =============================
// FETCH USER DATA
// =============================

// =============================
// FUNCTION: CHECK TODAY WITHDRAWAL
// =============================
function GETWITHDREWAL($pdo,$userid){
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) FROM tbl_transaction 
            WHERE user_id=? AND created_date=? AND subject='Investment Withdrawal Request'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userid,$today]);
    return $stmt->fetchColumn();
}

// =============================
// SUBMIT WITHDRAWAL
// =============================
if(isset($_POST['submit'])){

    // if($interval->days < 45){
    //     echo "<script>alert('Withdraw allowed only after 45 days. Completed: {$interval->days} days');</script>";
    //     exit;
    // }

    // SPLITTING amount,stackid FROM SELECT BOX
    list($amount,$stackid) = explode(",", $_POST['amount']);
    

    // if(GETWITHDREWAL($pdo,$userid) != 0){
    //     echo "<script>alert('You already submitted withdrawal today');</script>";
    //     exit;
    // }

    $uniqueId = rand(100000,999999);

    // MARK stack as withdrawn
    $stmt = $pdo->prepare("UPDATE tbl_roi_one SET status='1' WHERE id=?");
    $stmt->execute([$stackid]);

    // Amount after deduction 10%
    $leftamount = $amount - ($amount * 8 / 100);

    // INSERT TRANSACTION
    $sql = "INSERT INTO tbl_transaction 
        (amount, act_amount, user_id, subject, type, status, a_status, created_date, time, api_txn_no)
        VALUES (?,?,?,?,?,?,?,?,?,?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $leftamount,
        $amount,
        $userid,
        'Investment Withdrawal Request',
        'Debit',
        '1',
        '0',
        $currentDate,
        $currentTime,
        $stackid
    ]);
    
    if($stmt){
        $updateAmount=$pdo->prepare("UPDATE user SET total_package=total_package-:amt WHERE userid=:uid");
        $updateAmount->execute([':amt'=>$amount,':uid'=>$userid]);
    }

    echo "<script>alert('Withdrawal Request Submitted Successfully');</script>";
    echo "<script>window.location.href = 'nonworking1-withdraw1.php';</script>";
    exit();
}

// =============================
// FETCH INVESTMENTS LIST
// =============================
$list = $pdo->prepare("SELECT * FROM tbl_roi_one WHERE user_id=? AND status=0");
$list->execute([$userid]);
$investments = $list->fetchAll(PDO::FETCH_ASSOC);

?>



<style>
/* =========================================================
   ANANTA FINTECH THEME - INVESTMENT WITHDRAWAL REDESIGN
   Matches Dashboard (index.php) & Wallet Withdrawal Styling
========================================================= */

html,
body {
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
html::before,
html::after,
body::before,
body::after,
#wrapper::before,
#wrapper::after,
.content-wrapper::before,
.content-wrapper::after {
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

/* Main Card Container */
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

/* Form Controls Styling */
label.form-label,
label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control,
select.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 14.5px !important;
    font-weight: 600 !important;
    padding: 10px 16px !important;
    transition: all 0.2s ease-in-out !important;
    box-shadow: none !important;
}

.form-control:focus,
select.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    outline: none !important;
}

/* Submit Button */
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

<!-- Start wrapper-->
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
                              <i class="fa fa-line-chart"></i>
                          </div>
                          <div>
                              <div class="d-flex align-items-center gap-2 mb-1">
                                  <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">INVESTMENT WITHDRAWAL</span>
                                  <span style="font-size: 12px; color: #64748b; font-weight: 600;">ROI & NON-WORKING</span>
                              </div>
                              <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                  Investment <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Withdrawal Request</span> 📈
                              </h4>
                              <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                  Request withdrawal for active completed investment principal/packages.
                              </p>
                          </div>
                      </div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                          <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(2, 132, 199, 0.25); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                              <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Investment</span>
                              <span class="font-weight-bold" style="font-size: 18px; color: #0284c7; font-weight: 800;">₹<?php echo number_format((float)($usertotal_package ?? 0), 2); ?></span>
                          </div>
                          <a href="withdraw-history.php" class="btn btn-outline-primary font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                              <i class="fa fa-history me-1"></i> History
                          </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Form Section -->
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="ananta-fintech-card">
            
            <div class="card-header-bar">
                <div class="card-header-title">
                    <h4><i class="fa fa-money text-success me-2"></i> Choose Active Investment Package</h4>
                    <p>Select your package to submit a withdrawal request</p>
                </div>
            </div>

            <div class="p-4 p-md-5">

              <?php if (isset($success)) { ?>
                <div class="alert alert-success border-0 mb-4" style="border-radius: 12px; background: #f0fdf4; color: #166534; font-weight: 600;"><?= $success ?></div>
              <?php } ?>
              <?php if (isset($error)) { ?>
                <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px; background: #fef2f2; color: #991b1b; font-weight: 600;"><?= $error ?></div>
              <?php } ?>

              <form method="POST">

                <div class="row mb-4">
                  <div class="col-md-6 mb-3 mb-md-0">
                    <div class="p-3 bg-light" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                      <span class="text-muted small font-weight-bold d-block mb-1">DEDUCTIONS</span>
                      <span class="font-weight-bold text-dark" style="font-size: 15px;">5% TDS + 3% Admin Charge (Total 8%)</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-3 bg-light" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                      <span class="text-muted small font-weight-bold d-block mb-1">AVAILABLE PACKAGES</span>
                      <span class="font-weight-bold text-primary" style="font-size: 15px;"><?php echo count($investments); ?> Active Package(s)</span>
                    </div>
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label>Choose Investment Package</label>
                  <select name="amount" class="form-control" style="height: 50px;" required>
                      <option value="">-- Select Investment --</option>
                      <?php foreach($investments as $inv){ ?>
                          <option value="<?php echo $inv['package'].",".$inv['id']; ?>">
                              Package Amount: ₹<?php echo number_format((float)$inv['package'], 2); ?> (ID: #<?php echo $inv['id']; ?>)
                          </option>
                      <?php } ?>
                  </select>
                </div>

                <button class="btn-ananta-submit mt-2" name="submit">
                  <i class="fa fa-check-circle me-1"></i> Submit Investment Withdrawal
                </button>

              </form>

            </div>
          </div>
        </div>
      </div><!--End Row-->

      <div class="overlay toggle-menu"></div>
    </div>
  </div>

  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
  <?php include 'common/footer.php'; ?>
</div>

</body>
</html>

