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
<div id="wrapper">
  <div class="clearfix"></div>
  
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row mt-3">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="card-title text-center"><h3>Withdrawal</h3></div>
              <hr>
              <div class="text-center mb-3">
                <h6>💰 Available Balance: <span class="text-success">₹<?= $wallet_amount; ?></span></h6>
                <!--<h6>🕙 Withdrawal Time: 10 AM - 5 PM</h6>-->
                <!--<h6>📅 Every Monday Only</h6>-->
                <h6 class="text-danger">⚠️ Minimum Withdrawal: ₹360</h6>
              </div>
              
              <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <h6 class="text-center text-success"><?= isset($success) ? $success : '' ?></h6>
                <h6 class="text-center text-danger"><?= isset($error) ? $error : '' ?></h6>

                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <tbody>
                      <tr>
                        <td><strong>Available Balance :</strong></td>
                        <td>₹ <?= $wallet_amount; ?></td>
                      </tr>
                      <tr>
                        <td><strong>TDS + Admin Charge :</strong></td>
                        <td>5% + 3%</td>
                      </tr>
                      <tr>
                        <td><strong>Enter Withdrawal Amount :</strong></td>
                        <td>
                          <input type="hidden" id="qty" value="5" class="form-control">
                          <input min="360" id="amt" name="amount" type="number" required class="form-control mt-2">
                        </td>
                      </tr>
                      <tr>
                        <td></td>
                        <td>
                          <button type="submit" class="btn btn-primary px-4 py-2 mt-2">Submit</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
