<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php 
include("common/header.php"); 
include("common/connection.php"); // this already has your PDO instance as $db

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
//   if (GETWITHDREWAL($userid) == '0') {
  if ($kyc==1) {
    $amount = $_POST['amount'];

    if ($widhtrwal === "TRUE") {
    //   if ($ocday == 'Monday') {
      if (1==1) {
        // if ($currentTime >= "10:00" && $currentTime <= "17:00") {
        if (1==1) {
          if ($useramount >= 10) {
            // Assuming getmydirectactive() condition is satisfied
            if (1 == 1) {
              if ($useramount >= $amount) {
                if ($amount >= 10) {

                    // STEP 1: Generate OTP
                    $otp = rand(100000, 999999);
                
                    // Save withdraw data for OTP verification
                    $_SESSION['withdraw_otp'] = $otp;
                    $_SESSION['withdraw_amount'] = $amount;
                    $_SESSION['withdraw_time'] = $currentTime;
                
                    // Email of user
                    $to = $useremail;
                    $subject = $hmtitle . " Confirm Withdraw Request ";
                    $headers = "From: " . strip_tags($hmemailfrom) . "\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $message = '<html><body>';
                    $message .= '<table>';
                    $message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>" . strip_tags($username) . "</td></tr>";
                    // $message .= "<tr><td><strong>Email:</strong></td><td>" . htmlspecialchars($email) . "</td></tr>";
                    $message .= "<tr><td><strong>User id:</strong></td><td>" . htmlspecialchars($userid) . "</td></tr>";
                    $message .= "<tr><td><strong>OTP:</strong></td><td>" . htmlspecialchars($otp) . "</td></tr>";
                    $message .= "</table></body></html>";
                    mail($to, $subject, $message, $headers);
                
                    // Redirect user to OTP page
                    // echo "<script>
                    //         alert('OTP sent to your registered email');
                    //         window.location.href = 'otp.php';
                    //       </script>";
                    // exit();
                    
                    // Fetch user amount
                    $stmt = $pdo->prepare("SELECT amount FROM user WHERE userid = :userid");
                    $stmt->execute([':userid' => $userid]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $useramount = $row['amount'];
            
                    // -- Withdrawal Logic Run Here --
                    $shoppingamt = ($amount * 8) / 100;
                    $useramountleft = $useramount - $amount;
                    $leftamount = $amount - $shoppingamt;
            
                    // Insert transaction
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
            
                    // Update user wallet
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
    // $error = "You Can Withdrawal Only Once Per Day";
     echo "<script>alert('Please! complete/Update your KYC first..');window.location.href = 'withdraw.php';</script>";
  }
}
?>

<body class="bg-theme bg-theme1">

<!-- start loader -->
<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner"><div class="loader"></div></div>
  </div>
</div>
<!-- end loader -->

<!-- Start wrapper-->
<div id="wrapper" class="ananta-user-dashboard">
  <div class="clearfix"></div>
  
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row mt-3">
        <div class="col-lg-8 offset-lg-2">
          <div class="card shadow-lg border-0" style="border-radius: 20px; background: #ffffff;">
            <div class="card-body p-4 p-md-5">

              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                <div>
                  <h4 class="font-weight-bold text-dark mb-1" style="color: #0f172a;">Wallet Withdrawal</h4>
                  <p class="text-muted small mb-0">Withdraw your earnings directly to your registered bank account</p>
                </div>
                <div class="mt-3 mt-md-0 px-3 py-2" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%); border-radius: 12px; border: 1px solid rgba(2, 132, 199, 0.2);">
                  <span class="text-muted small font-weight-bold d-block">Available Balance</span>
                  <span class="h5 font-weight-bold mb-0" style="color: #16a34a;">₹<?= number_format((float)$wallet_amount, 2); ?></span>
                </div>
              </div>

              <?php if (isset($success)) { ?>
                <div class="alert alert-success border-0" style="border-radius: 12px; background: #f0fdf4; color: #166534; font-weight: 600;"><?= $success ?></div>
              <?php } ?>
              <?php if (isset($error)) { ?>
                <div class="alert alert-danger border-0" style="border-radius: 12px; background: #fef2f2; color: #991b1b; font-weight: 600;"><?= $error ?></div>
              <?php } ?>

              <form method="POST" enctype="multipart/form-data">

                <div class="row mb-4">
                  <div class="col-md-6 mb-3 mb-md-0">
                    <div class="p-3 bg-light" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                      <span class="text-muted small font-weight-bold d-block mb-1">TDS + Admin Deductions</span>
                      <span class="font-weight-bold text-dark" style="font-size: 15px;">5% TDS + 3% Admin Charge</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-3 bg-light" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                      <span class="text-muted small font-weight-bold d-block mb-1">Minimum Limit</span>
                      <span class="font-weight-bold text-danger" style="font-size: 15px;">₹360.00</span>
                    </div>
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label class="font-weight-bold small text-uppercase" style="color: #475569; letter-spacing: 0.5px;">Withdrawal Amount (₹)</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light border-right-0" style="border-radius: 12px 0 0 12px; border-color: #cbd5e1;"><i class="zmdi zmdi-money text-success"></i></span>
                    </div>
                    <input type="hidden" id="qty" value="5">
                    <input min="360" id="amt" name="amount" type="number" required placeholder="Enter amount (min ₹360)" class="form-control border-left-0" style="border-radius: 0 12px 12px 0; border-color: #cbd5e1; height: 48px;">
                  </div>
                </div>

                <button type="submit" class="btn btn-block font-weight-bold text-white shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border: none; height: 50px; font-size: 16px;">
                  <i class="zmdi zmdi-mail-send me-1"></i> Submit Withdrawal Request
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
