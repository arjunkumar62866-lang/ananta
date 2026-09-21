<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include("common/header.php"); 
include("common/connection.php");

getmydirectactive($userid);

// Fetch user wallet
$sql1 = "SELECT * FROM user WHERE userid = :userid";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute([':userid' => $userid]);
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
if ($row1) {
  $wallet_amount = $row1["amount"];
}

// Set timezone
if (function_exists('date_default_timezone_set')) {
  date_default_timezone_set("Asia/Kolkata");
}
$currentTime = date("H:i:s");
$time = date('h:i a');
$date = date('d');
$ocday = date("l");

// Set withdrawal availability
$widhtrwal = "TRUE";

// Function to check withdrawal requests
function GETWITHDREWAL($userid)
{
  global $pdo;
  $sqlac = "SELECT COUNT(*) as alluser FROM tbl_transaction 
            WHERE user_id = :userid 
              AND created_date = CURDATE() 
              AND subject = 'Withdrawal Request'";
  $stmtac = $pdo->prepare($sqlac);
  $stmtac->execute([':userid' => $userid]);
  $rowac = $stmtac->fetch(PDO::FETCH_ASSOC);
  return $rowac ? $rowac['alluser'] : 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if ($kyc==1) {
    $amount = $_POST['amount'];

    if ($widhtrwal === "TRUE") {
      if (1==1) {
        if (1==1) {
          if ($useramount >= 10) {
            if (1 == 1) {
              if ($useramount >= $amount) {
                if ($amount >= 10) {

                    $otp = rand(100000, 999999);
                
                    $_SESSION['withdraw_otp'] = $otp;
                    $_SESSION['withdraw_amount'] = $amount;
                    $_SESSION['withdraw_time'] = $currentTime;
                
                    $to = $useremail;
                    $subject = $hmtitle . " Confirm Withdraw Request ";
                    $headers = "From: " . strip_tags($hmemailfrom) . "\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $message = '<html><body>';
                    $message .= '<table>';
                    $message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>" . strip_tags($username) . "</td></tr>";
                    $message .= "<tr><td><strong>User id:</strong></td><td>" . htmlspecialchars($userid) . "</td></tr>";
                    $message .= "<tr><td><strong>OTP:</strong></td><td>" . htmlspecialchars($otp) . "</td></tr>";
                    $message .= "</table></body></html>";
                    mail($to, $subject, $message, $headers);
                    
                    $stmt = $pdo->prepare("SELECT amount FROM user WHERE userid = :userid");
                    $stmt->execute([':userid' => $userid]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $useramount = $row['amount'];
            
                    $shoppingamt = ($amount * 8) / 100;
                    $useramountleft = $useramount - $amount;
                    $leftamount = $amount - $shoppingamt;
            
                    $sql = "INSERT INTO tbl_transaction 
                                (amount, act_amount, user_id, subject, type, status, a_status, created_date, time)
                            VALUES 
                                (:amount, :act_amount, :user_id, 'Withdrawal Request', 'Debit', '1', '0', CURDATE(), :time)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':amount' => $leftamount,
                        ':act_amount' => $amount,
                        ':user_id' => $userid,
                        ':time' => $currentTime
                    ]);
            
                    $sql1 = "UPDATE user 
                                SET amount = :useramountleft, 
                                    shop_amount = shop_amount + :shoppingamt 
                            WHERE userid = :userid AND status = '1' AND active = '1'";
                    $stmt1 = $pdo->prepare($sql1);
                    $stmt1->execute([
                        ':useramountleft' => $useramountleft,
                        ':shoppingamt' => $shoppingamt,
                        ':userid' => $userid
                    ]);
            
                    echo "<script>alert('Withdrawal Request Sent Successfully'); window.location='withdraw.php';</script>";
                    exit();
                }
                else {
                  echo "<script>alert('Minimum Withdrawal amount is Rs. 360');window.location.assign('withdraw.php');</script>";
                }
              } else {
                $error = "Your Wallet Amount is Low. Please Try Again !!!";
              }
            } else {
              echo "<script>alert('Please Do 2 Direct Id');window.location.assign('withdraw.php');</script>";
            }
          } else {
            $error = "MINIMUM WITHDRAWAL LIMIT IS 360 ₹. YOUR WALLET BALANCE IS LOW !!!";
          }
        } else {
          $error = "YOU CAN WITHDRAWAL DAILY BETWEEN 10:00 AM TO 05:00 PM";
          echo "<script>alert('YOU CAN WITHDRAWAL DAILY BETWEEN 10:00 AM TO 05:00 PM');window.location.assign('withdraw.php');</script>";
        }
      } else {
        echo "<script>alert('Withdrawal Day Only Monday');window.location.assign('withdraw.php');</script>";
      }
    }
  } else {
     echo "<script>alert('Please! complete/Update your KYC first..');window.location.href = 'withdraw.php';</script>";
  }
}
?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - WALLET WITHDRAWAL REDESIGN
   Matches Dashboard (index.php) & Profile Styling
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
input.form-control {
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
input.form-control:focus {
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

<!-- Loader -->
<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner"><div class="loader"></div></div>
  </div>
</div>
<!-- End Loader -->

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
                              <i class="fa fa-bank"></i>
                          </div>
                          <div>
                              <div class="d-flex align-items-center gap-2 mb-1">
                                  <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">WALLET WITHDRAWAL</span>
                                  <span style="font-size: 12px; color: #64748b; font-weight: 600;">BANK TRANSFER</span>
                              </div>
                              <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                  Wallet <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Withdrawal</span> 🏦
                              </h4>
                              <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                  Withdraw your available wallet balance directly to your registered bank account.
                              </p>
                          </div>
                      </div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                          <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(22, 163, 74, 0.25); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                              <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Available Balance</span>
                              <span class="font-weight-bold" style="font-size: 18px; color: #16a34a; font-weight: 800;">₹<?= number_format((float)$wallet_amount, 2); ?></span>
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
                    <h4><i class="fa fa-money text-success me-2"></i> Submit Withdrawal Request</h4>
                    <p>Enter your requested amount below (Minimum limit: ₹360.00)</p>
                </div>
            </div>

            <div class="p-4 p-md-5">

              <?php if (isset($success)) { ?>
                <div class="alert alert-success border-0 mb-4" style="border-radius: 12px; background: #f0fdf4; color: #166534; font-weight: 600;"><?= $success ?></div>
              <?php } ?>
              <?php if (isset($error)) { ?>
                <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px; background: #fef2f2; color: #991b1b; font-weight: 600;"><?= $error ?></div>
              <?php } ?>

              <form method="POST" enctype="multipart/form-data">

                <div class="row mb-4">
                  <div class="col-md-6 mb-3 mb-md-0">
                    <div class="p-3 bg-light" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                      <span class="text-muted small font-weight-bold d-block mb-1">TDS + ADMIN DEDUCTIONS</span>
                      <span class="font-weight-bold text-dark" style="font-size: 15px;">5% TDS + 3% Admin Charge</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-3 bg-light" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                      <span class="text-muted small font-weight-bold d-block mb-1">MINIMUM WITHDRAWAL LIMIT</span>
                      <span class="font-weight-bold text-danger" style="font-size: 15px;">₹360.00</span>
                    </div>
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label>Withdrawal Amount (₹)</label>
                  <input type="hidden" id="qty" value="5">
                  <input min="360" id="amt" name="amount" type="number" required placeholder="Enter amount (min ₹360)" class="form-control" style="height: 50px;">
                </div>

                <button type="submit" class="btn-ananta-submit mt-2">
                  <i class="fa fa-paper-plane me-1"></i> Submit Withdrawal Request
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
  <?php include 'common/footer.php' ?>
</div>

<script type="text/javascript">
  $("#qty,#amt").on("change keyup", function(e){  
    var quan = parseFloat($("#qty").val()); 
    var rate = parseFloat($("#amt").val()); 
    var result = rate - rate * quan / 100;  
    if (!isNaN(result)) { 
      $("#total").val(result); 
    }  
  });
</script>
</body>
</html>
