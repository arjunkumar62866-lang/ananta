<?php 
session_start();
require 'common/db_method.php'; // contains session, DB connection, and loginUser()
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$date = date('Y-m-d');
$time = date('h:i a');

require_once 'common/login_reg_control_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check Login & Registration Access Control
    $lrcState = getLoginRegControlState($pdo);
    if ($lrcState['status'] === 'OFF' && $lrcState['message_type'] === 'ERROR') {
        $errMsg = urlencode($lrcState['message_text']);
        header("Location: login.php?error={$errMsg}");
        exit;
    }

    $result = loginUser($userid, $password, $pdo);

    if ($result['status']) {
        if ($lrcState['status'] === 'OFF' && $lrcState['message_type'] === 'WARNING') {
            $_SESSION['lrc_warning_message'] = $lrcState['message_text'];
        }
        header('Location: index.php');
        exit;
    } else {
        $errMsg = urlencode($result['message']);
        header("Location: login.php?error={$errMsg}");
        exit;
    }
}


$homeset = getHomeSettings($pdo);
$hmmobile = $homeset['mobile'] ?? '';
$hmemail = $homeset['email'] ?? '';
$hmaddress = $homeset['address'] ?? '';
$hmtitle = $homeset['title'] ?? '';
$hmurl = $homeset['url'] ?? '';
$hmpackage = $homeset['package'] ?? '';
$hmpre = $homeset['pre'] ?? '';
$hmbitly = $homeset['bitly'] ?? '';
$hmemailfrom = $homeset['emailfrom'] ?? '';
$hmbg = $homeset['background'] ?? '';
$hmlogo = $homeset['logo'] ?? '';
$hmfavicon = $homeset['favicon'] ?? '';
$hmcolor = $homeset['color'] ?? '';

?>



<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from themewagon.github.io/dashtreme/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:59 GMT -->
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
  <link rel="icon" href="<?php echo $hmfavicon;?>" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />

</head>

  <!-- Custom CSS for Modern Floating White Card Login -->
  <style>
    body.ananta-auth-page {
      background: radial-gradient(circle at 50% 30%, rgba(16, 185, 129, 0.08), rgba(15, 23, 42, 0.75)), url('assets/images/bg-1.jpg') center/cover no-repeat fixed !important;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      margin: 0;
      padding: 20px 15px;
    }
    #particles-js {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }
    .auth-wrapper {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 440px;
      margin: 0 auto;
    }
    .auth-card {
      background: #ffffff !important;
      border-radius: 24px !important;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.2) !important;
      border: none !important;
      padding: 35px 30px;
      color: #1e293b !important;
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 20px;
    }
    .auth-logo img {
      max-height: 110px;
      width: auto;
      object-fit: contain;
    }
    .auth-header {
      text-align: center;
      margin-bottom: 28px;
    }
    .auth-header h3 {
      font-size: 26px;
      font-weight: 700;
      color: #0f172a !important;
      margin: 0 0 6px 0;
    }
    .auth-header p {
      font-size: 11px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      font-weight: 600;
      color: #64748b !important;
      margin: 0;
    }
    .auth-card .form-group {
      margin-bottom: 18px;
    }
    .auth-card .input-group-custom {
      position: relative;
    }
    .auth-card .form-control {
      background-color: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 12px !important;
      height: 48px;
      padding: 10px 42px 10px 18px;
      font-size: 14px;
      color: #0f172a !important;
      box-shadow: none !important;
      transition: all 0.2s ease;
    }
    .auth-card .form-control::placeholder {
      color: #64748b !important;
      opacity: 1 !important;
    }
    .auth-card .form-control:-ms-input-placeholder {
      color: #64748b !important;
    }
    .auth-card .form-control::-ms-input-placeholder {
      color: #64748b !important;
    }
    .auth-card .form-control:focus {
      border-color: #00b4d8 !important;
      box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15) !important;
    }
    .auth-card .input-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 16px;
      pointer-events: none;
    }
    .auth-card .btn-primary-action {
      background: linear-gradient(135deg, #00b4d8 0%, #10b981 100%) !important;
      border: none !important;
      border-radius: 12px !important;
      height: 48px;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #ffffff !important;
      text-transform: uppercase;
      width: 100%;
      box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
      transition: all 0.25s ease;
      cursor: pointer;
    }
    .auth-card .btn-primary-action:hover {
      transform: translateY(-1px);
      box-shadow: 0 14px 24px -5px rgba(16, 185, 129, 0.5);
      opacity: 0.96;
    }
    .auth-card .forgot-link {
      display: inline-block;
      color: #00b4d8;
      font-weight: 600;
      font-size: 13px;
      text-decoration: none;
      transition: color 0.2s;
    }
    .auth-card .forgot-link:hover {
      color: #0284c7;
      text-decoration: underline;
    }
    .auth-card .signup-text {
      text-align: center;
      font-size: 13px;
      color: #64748b;
      margin-top: 22px;
      font-weight: 500;
    }
    .auth-card .signup-text a {
      color: #10b981;
      font-weight: 700;
      text-decoration: none;
      margin-left: 4px;
    }
    .auth-card .signup-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body class="ananta-auth-page">
  <div id="particles-js"></div>

  <!-- Start wrapper-->
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo">
        <img src="/assets/images/logo-stacked.png" alt="Ananta Logo">
      </div>
      <div class="auth-header">
        <h3>Login</h3>
        <p>PLEASE LOGIN TO YOUR ACCOUNT TO CONTINUE</p>
      </div>

      <?php if (!empty($_GET['error'])): ?>
        <div class="text-center" style="font-size: 13.5px; font-weight: 600; color: #dc2626; margin-bottom: 20px; line-height: 1.5; background: transparent; padding: 0;">
          <i class="icon-exclamation" style="margin-right: 6px; color: #dc2626;"></i> <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="form-group">
          <div class="input-group-custom">
            <input type="text" name="userid" id="exampleInputUsername" class="form-control" placeholder="Email or User ID" required>
            <i class="icon-user input-icon"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="input-group-custom">
            <input type="password" name="password" id="exampleInputPassword" class="form-control" placeholder="Password" required>
            <i class="icon-lock input-icon"></i>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="custom-control custom-checkbox" style="padding-left: 1.5rem;">
            <input type="checkbox" class="custom-control-input" id="user-checkbox" checked>
            <label class="custom-control-label" for="user-checkbox" style="color: #64748b; font-size: 13px; font-weight: 500;">Remember me</label>
          </div>
          <div>
            <a href="reset-password.php" class="forgot-link">Forgot Password?</a>
          </div>
        </div>

        <button type="submit" class="btn btn-primary-action">LOGIN</button>

        <div class="signup-text">
          Don't have an account? <a href="new_binary_registration_form.php">Signup</a>
        </div>
      </form>
      <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #090909ff; font-size: 12px; color: #090909ff;">
        <button type="button" onclick="openLrcModal();" class="d-none d-lg-inline-block" style="background: transparent; border: none; color: #080808ff; font-size: 12px; font-weight: 700; cursor: pointer; padding: 0 2px; outline: none; vertical-align: baseline;">©</button>Copyright <span class="d-lg-none">©</span> <?php echo date('Y'); ?> Ananta. All Rights Reserved.
      </div>
    </div>  
  </div><!--wrapper--> 

  <?php include_once __DIR__ . '/common/login_reg_control_modal.php'; ?>

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- Custom scripts -->
  <script src="particles.js"></script>
  <script src="app.js"></script>
</body>


<!-- Mirrored from themewagon.github.io/dashtreme/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:02:00 GMT -->

</html>