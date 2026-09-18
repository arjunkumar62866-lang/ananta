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
  if (1==1) {
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
                if ($amount >= '10') {
                  $shoppingamt = (int)$amount * 10 / 100;
                  $useramountleft = $useramount - $amount;
                  $leftamount = (int)$amount - (int)$amount * 10 / 100;

                  // Insert transaction
                  $sql = "INSERT INTO tbl_transaction 
                            (amount, act_amount, user_id, subject, type, status, a_status, created_date, time)
                          VALUES 
                            (:amount, :act_amount, :user_id, 'Withdrawal Request', 'Debit', '1', '0', CURDATE(), :time)";
                  $stmt = $pdo->prepare($sql);
                  $result = $stmt->execute([
                    ':amount' => $leftamount,
                    ':act_amount' => $amount,
                    ':user_id' => $userid,
                    ':time' => $currentTime
                  ]);

                  if ($result) {
                    // Update user table
                    $sql1 = "UPDATE user 
                                SET amount = :useramountleft, 
                                    shop_amount = shop_amount + :shoppingamt 
                              WHERE userid = :userid 
                                AND status = '1' 
                                AND active = '1'";
                    $stmt1 = $pdo->prepare($sql1);
                    $result1 = $stmt1->execute([
                      ':useramountleft' => $useramountleft,
                      ':shoppingamt' => $shoppingamt,
                      ':userid' => $userid
                    ]);
                    if ($result1) {
                      $success = "Withdrawal Request Sent Successfully";
                    }
                  } else {
                    echo "Error inserting record.";
                  }
                } else {
                  echo "<script>alert('Minimum Withdrawal amount is Rs. 10');window.location.assign('withdraw');</script>";
                }
              } else {
                $error = "Your Wallet Amount is Low. Please Try Again !!!";
              }
            } else {
              echo "<script>alert('Please Do 2 Direct Id');window.location.assign('withdraw');</script>";
            }
          } else {
            $error = "MINIMUM WITHDRAWAL LIMIT IS 10 ₹. YOUR WALLET BALANCE IS LOW !!!";
          }
        } else {
          $error = "YOU CAN WITHDRAWAL DAILY BETWEEN 10:00 AM TO 05:00 PM";
          echo "<script>alert('YOU CAN WITHDRAWAL DAILY BETWEEN 10:00 AM TO 05:00 PM');window.location.assign('withdraw');</script>";
        }
      } else {
        echo "<script>alert('Withdrawal Day Only Monday');window.location.assign('withdraw');</script>";
      }
    }
  } else {
    $error = "You Can Withdrawal Only Once Per Day";
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
                <h6 class="text-danger">⚠️ Minimum Withdrawal: ₹10</h6>
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
                        <td>5% + 5%</td>
                      </tr>
                      <tr>
                        <td><strong>Enter Withdrawal Amount :</strong></td>
                        <td>
                          <input type="hidden" id="qty" value="5" class="form-control">
                          <input min="10" id="amt" name="amount" type="number" required class="form-control mt-2">
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
