<?php
include 'common/connection.php';
include 'common/db_method.php';
// include("common/password.php");

$home = getHomeSettings($pdo);
$hmtitle = $home['title'];
$hm_mobile = $home['mobile'];
$hm_email = $home['email'];
$hmemailfrom = $hm_email;
$hm_address = $home['address'];
$hm_logo = $home['logo'];
$hmlogo = $hm_logo;
$hm_favicon = $home['favicon'];
$hmfavicon = $hm_favicon;
$hm_pre = $home['pre'];
$hmpre = $hm_pre;
$hm_url = $home['url'];
$hm_color = $home['color'];
$hm_background = $home['background'];
$hmwebsite = $home['website'];

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$date = date('Y-m-d');
$time = date('h:i a');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sponserid = $_POST['refferalId'];
    $sponserid1 = substr($sponserid, 2);
    $userid = rand(100000, 999999);
    $otpreg = rand(1000, 9999);
    $transaction_password = rand(100000, 999999);
    $name = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['pass1'];
    $conpassword = $_POST['pass2'];
    $mobile = $_POST['mobile'];
    $mobilecode = $_POST['mobilecode'];
    $mobile = $mobilecode . $mobile;

    
    $aadhar = '';
    $father = '';
    $state = '';
    $address = '';
    $gender = '';
    $pin_code = '';

    $userdata = getuserdatabysponserid($sponserid1);
    if ($userdata !== null) {
        $idactive = $userdata['idactive'];

        if (userid($userid) === true) {
            if (checkuseridregister($sponserid1) == 1) {
                $flag = 1;

                $stmt = $pdo->prepare("SELECT COUNT(*) as mobile_check FROM user WHERE mobile = ?");
                $stmt->execute([$mobile]);
                $row = $stmt->fetch();
                $mobilecheck = $row['mobile_check'];

                if ($mobilecheck > 0) {
                  echo '<script>alert("You Can Register Only 1 ID From Same Mobile Number");window.location = "register.php";</script>';
                  exit();
                }

                $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = ?");

                $stmt->execute([$sponserid1]);
                $count = $stmt->rowCount();

                if ($count == 1) {
                    $select_active = $pdo->prepare("SELECT id, name FROM user WHERE userid = ?");
                    $select_active->execute([$sponserid1]);
                    $select_row = $select_active->fetch();
                    $active = $select_row['id'];
                    $sponsername = $select_row['name'];
                    $coinbonus = '';

                    // Insert sponsor
                    insertSponsor($pdo, $sponserid1, $userid, $date);

                    $userData = [
                        $userid, $name, $mobile, $email, '', $password, $transaction_password,
                        $sponserid1, $sponsername, $sponserid1, '0', '1', '', '', $date, '', '', '0', 'active',
                        '', $time, '', '', '0', '', '', '', $userid, '', '0', '', '', '', '', '', $otpreg, $coinbonus
                    ];

                    $query_register = insertUser($pdo, $userData);

                    if ($query_register) {
                        insertKYC($pdo, $userid, $aadhar);

                        $to = $email;
                        $subject = $hmtitle . " Registration Successfully ";
                        $headers = "From: " . strip_tags($hm_email) . "\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        $message = '<html><body>';
                        $message .= '<table>';
                        $message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>" . strip_tags($name) . "</td></tr>";
                        $message .= "<tr><td><strong>Email</strong></td><td>" . htmlspecialchars($email) . "</td></tr>";
                        $message .= "<tr><td><strong>Password</strong></td><td>" . htmlspecialchars($password) . "</td></tr>";
                        $message .= "<tr><td><strong>Transaction Password</strong></td><td>" . htmlspecialchars($transaction_password) . "</td></tr>";
                        $message .= "</table></body></html>";

                        mail($to, $subject, $message, $headers);

                        $pinfinal = $userid;
                        for ($i = 0; $i < 20; $i++) {
                            $mysponserid = getmysponserid($pinfinal);
                            $sponserdetails = getuserdatabysponserid($mysponserid);
                            if ($pinfinal !== '1290') {
                                $spcode1 = $sponserdetails['userid'];
                                $spamont = $sponserdetails['amount'];
                                $isidactive = $sponserdetails['idactive'];
                                $directactive = getmydirectactive($spcode1);

                                $level = $i + 1;
                                insert_userlevel($mysponserid, $userid, $level);
                                $pinfinal = $spcode1;
                            }
                        }
                        ?>
                        <script>
                            window.location = "login.php";
                        </script>
                        <?php
                    }
                }
            } else {
                echo '<script>alert("Your sponsor ID does not exist");window.location = "register.php";</script>';
            }
        } else {
            echo '<script>alert("User ID generation error. Please try again.");window.location = "register.php";</script>';
        }
    } else {
        echo '<script>alert("Invalid sponsor ID.");window.location = "register.php";</script>';
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from themewagon.github.io/dashtreme/register.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:02:00 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title><?php echo $hmtitle;?></title>
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet" />
  <script src="assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="<?php echo $hmfavicon?>" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-theme bg-theme1">

  <!-- start loader -->
  <div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
      <div class="loader-wrapper-inner">
        <div class="loader"></div>
      </div>
    </div>
  </div>
  <!-- end loader -->

  <!-- Start wrapper-->
  <div id="wrapper">

    <div class="card card-authentication1 mx-auto my-4">
      <div class="card-body">
        <div class="card-content p-2">
          <div class="text-center">
            <img src="assets/images/logo-icon.png" alt="logo icon">
          </div>
          <div class="card-title text-uppercase text-center py-3">Sign Up</div>
          <form action="register.php" method="post" id="registration_form">
            <!-- Referrer ID -->
            <div class="form-group">
              <label for="referrerId" class="sr-only"></label>
              <div class="position-relative has-icon-right">
                <input type="text" name="refferalId" id="referrerId" class="form-control input-shadow"
                  placeholder="Enter Referrer ID" required>
                <div class="form-control-position">
                  <i class="icon-link"></i>
                </div>
                 <!-- <span id="response2"></span>  -->
              </div>
            </div>

            <!-- Sponsor Name -->
            <div class="form-group" id="sponsor_name" style="display: none;>
              <label for="referrerId" class="sr-only"></label>
              <div class="position-relative has-icon-right">
                <input type="text" name="refferaId" id="response2" class="form-control input-shadow" readonly>
              </div>
            </div>

            <!-- Name -->
            <div class="form-group">
              <label for="exampleInputName" class="sr-only">Name</label>
              <div class="position-relative has-icon-right">
                <input type="text" name="userName" id="exampleInputName" class="form-control input-shadow"
                  placeholder="Enter User-Name" required>
                <div class="form-control-position">
                  <i class="icon-user"></i>
                </div>
              </div>
            </div>

            <!-- Email -->
            <div class="form-group">
              <label for="exampleInputEmailId" class="sr-only">Email ID</label>
              <div class="position-relative has-icon-right">
                <input type="email" name="email" id="exampleInputEmailId" class="form-control input-shadow"
                  placeholder="Enter User-Email" required>
                <div class="form-control-position">
                  <i class="icon-envelope-open"></i>
                </div>
              </div>
            </div>

            <!-- Mobile -->
            <div class="form-group">
              <label for="mobileNumber" class="sr-only">Mobile</label>
              <div class="d-flex align-items-center" style="gap: 10px;">
                <!-- Country Code Dropdown -->
                <select class="form-control" name="mobilecode" id="countryCode" style="width: 35%; color:black;">
                  <option value="+1">United States (+1)</option>
                  <option value="+91">India (+91)</option>
                  <option value="+44">United Kingdom (+44)</option>
                  <option value="+61">Australia (+61)</option>
                  <option value="+81">Japan (+81)</option>
                  <option value="+49">Germany (+49)</option>
                  <option value="+33">France (+33)</option>
                  <option value="+86">China (+86)</option>
                  <option value="+39">Italy (+39)</option>
                  <option value="+34">Spain (+34)</option>
                  <option value="+7">Russia (+7)</option>
                  <option value="+55">Brazil (+55)</option>
                  <option value="+27">South Africa (+27)</option>
                  <option value="+62">Indonesia (+62)</option>
                  <option value="+234">Nigeria (+234)</option>
                  <option value="+52">Mexico (+52)</option>
                  <option value="+31">Netherlands (+31)</option>
                  <option value="+63">Philippines (+63)</option>
                  <option value="+46">Sweden (+46)</option>
                  <option value="+64">New Zealand (+64)</option>
                  <option value="+20">Egypt (+20)</option>
                  <option value="+90">Turkey (+90)</option>
                  <option value="+66">Thailand (+66)</option>
                  <option value="+41">Switzerland (+41)</option>
                  <option value="+82">South Korea (+82)</option>
                  <option value="+65">Singapore (+65)</option>
                  <option value="+351">Portugal (+351)</option>
                  <option value="+48">Poland (+48)</option>
                  <option value="+886">Taiwan (+886)</option>
                  <option value="+94">Sri Lanka (+94)</option>
                  <option value="+880">Bangladesh (+880)</option>
                  <option value="+98">Iran (+98)</option>
                  <option value="+30">Greece (+30)</option>
                  <option value="+354">Iceland (+354)</option>
                  <option value="+372">Estonia (+372)</option>
                  <option value="+60">Malaysia (+60)</option>
                </select>
                <div class="position-relative has-icon-right">
                  <input type="text" name="mobile" id="mobileNumber" class="form-control input-shadow"
                    placeholder="Enter User-Mobile" required>
                  <div class="form-control-position">
                    <i class="icon-phone"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Password -->
            <div class="form-group">
              <label for="exampleInputPassword" class="sr-only">Password</label>
              <div class="position-relative has-icon-right">
                <input type="password" name="pass1" id="exampleInputPassword" class="form-control input-shadow"
                  placeholder="Enter Password" required>
                <div class="form-control-position">
                  <i class="icon-lock"></i>
                </div>
                <span id="passwordWarning" style="color: red; font-size: 14px;"></span>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
              <label for="confirmPassword" class="sr-only">Confirm Password</label>
              <div class="position-relative has-icon-right">
                <input type="password" name="pass2" id="confirmPassword" class="form-control input-shadow"
                  placeholder="Confirm Password" required>
                <div class="form-control-position">
                  <i class="icon-lock"></i>
                </div>
              </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-group">
              <div class="icheck-material-white">
                <input type="checkbox" id="user-checkbox" name="terms_accepted" />
                <label for="user-checkbox">I Agree With Terms & Conditions</label>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn btn-light btn-block waves-effect waves-light">Sign Up</button>

            <div class="text-center mt-3">Sign Up With</div>

            <div class="form-row mt-4">
              <div class="form-group mb-0 col-6">
                <button type="button" class="btn btn-light btn-block"><i class="fa fa-facebook-square"></i>
                  Facebook</button>
              </div>
              <div class="form-group mb-0 col-6 text-right">
                <button type="button" class="btn btn-light btn-block"><i class="fa fa-twitter-square"></i>
                  Twitter</button>
              </div>
            </div>
          </form>

        </div>
      </div>
      <div class="card-footer text-center py-3">
        <p class="text-warning mb-0">Already have an account? <a href="login.php"> Sign In here</a></p>
      </div>
    </div>

    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

    <!--start color switcher-->
    <div class="right-sidebar">
      <div class="switcher-icon">
        <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
      </div>
      <div class="right-sidebar-content">

        <p class="mb-0">Gaussion Texture</p>
        <hr>

        <ul class="switcher">
          <li id="theme1"></li>
          <li id="theme2"></li>
          <li id="theme3"></li>
          <li id="theme4"></li>
          <li id="theme5"></li>
          <li id="theme6"></li>
        </ul>

        <p class="mb-0">Gradient Background</p>
        <hr>

        <ul class="switcher">
          <li id="theme7"></li>
          <li id="theme8"></li>
          <li id="theme9"></li>
          <li id="theme10"></li>
          <li id="theme11"></li>
          <li id="theme12"></li>
          <li id="theme13"></li>
          <li id="theme14"></li>
          <li id="theme15"></li>
        </ul>

      </div>
    </div>
    <!--end color switcher-->

  </div><!--wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  
  <!-- Ajax for auto-matic Name fetching -->
  <script>
  $(document).ready(function () {
    $('#referrerId').on('blur', function () {
      var refId = $(this).val();

      if (refId.length > 2) {
        $.ajax({
          type: 'POST',
          url: 'checkName.php',
          data: { data: refId },
          success: function (response) {
            if (response != 0) {
                $('#response2').val(response);
                $('#sponsor_name').slideDown();
                $('#submitBtn').removeAttr('disabled').css('cursor','pointer');
              
            } else {
                $('#response2').val("Wrong Sponsor ID");
                $('#sponsor_name').slideDown();
                $('#submitBtn').attr('disabled','true').css('cursor', 'not-allowed');
              
            }
          }
        });
      } 
    });
  });
</script>
  
<!-- logic for password and confirm password should be same -->
<script>
  $(document).ready(function () {
    $('#registration_form').on('submit keyup', function (e) {
      const pass1 = $('#exampleInputPassword').val();
      const pass2 = $('#confirmPassword').val();

     
      const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{6,}$/;


      if (pass1 !== pass2) {
        $('#passwordWarning').text('Passwords do not match');
        $('#submitBtn').attr('disabled', true).css('cursor', 'not-allowed');
        if (e.type === 'submit') e.preventDefault();
        return;
      }

      
      if (!strongPasswordRegex.test(pass1)) {
        $('#passwordWarning').text('Password must be at least 6 characters long and include 1 uppercase, 1 lowercase, and 1 special character.');
        $('#submitBtn').attr('disabled', true).css('cursor', 'not-allowed');
        if (e.type === 'submit') e.preventDefault();
        return;
      }

      
      $('#passwordWarning').text('');
      $('#submitBtn').removeAttr('disabled').css('cursor', 'pointer');
    });
  });
</script>





</body>

<!-- Mirrored from themewagon.github.io/dashtreme/register.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:02:00 GMT -->


</html>
