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
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title><?php echo $hmtitle;?></title>
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet" />
  <script src="assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="<?php echo $hmfavicon;?>" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- FontAwesome / Icons CSS-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }
    body.ananta-auth-page {
      background: rgba(15, 23, 42, 0.75) url('assets/images/bg-1.jpg') center/cover no-repeat fixed !important;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      margin: 0;
      padding: 20px 15px;
    }
    .auth-wrapper {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 440px;
      margin: 0 auto;
    }
    .ananta-auth-modal-card {
      background: #ffffff !important;
      border-radius: 24px !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
      border: none !important;
      padding: 32px 28px;
      color: #1e293b !important;
      position: relative;
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 12px;
    }
    .auth-logo img {
      max-height: 95px;
      width: auto;
      object-fit: contain;
    }
    .auth-header {
      text-align: center;
      margin-bottom: 24px;
    }
    .auth-header h3 {
      font-size: 24px;
      font-weight: 700;
      color: #0f172a !important;
      margin: 0 0 4px 0;
    }
    .auth-header p {
      font-size: 10.5px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      font-weight: 600;
      color: #64748b !important;
      margin: 0;
    }
    .ananta-modal-input {
      background-color: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 12px !important;
      height: 46px !important;
      padding-right: 40px !important;
      font-size: 14px;
      color: #0f172a !important;
      box-shadow: none !important;
      transition: all 0.2s ease;
    }
    .ananta-modal-input::placeholder {
      color: #94a3b8 !important;
    }
    .ananta-modal-input:focus {
      border-color: #00b4d8 !important;
      box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15) !important;
    }
    .btn-ananta-primary {
      background: linear-gradient(135deg, #00b4d8 0%, #10b981 100%) !important;
      border: none !important;
      border-radius: 12px !important;
      height: 46px;
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
    .btn-ananta-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 14px 24px -5px rgba(16, 185, 129, 0.5);
      opacity: 0.96;
    }
  </style>
</head>

<body class="ananta-auth-page">

  <div class="auth-wrapper">
    <div class="ananta-auth-modal-card">
      <div class="auth-logo">
        <img src="/assets/images/pwa-icon.png" alt="Ananta Logo">
      </div>
      <div class="auth-header">
        <h3>Login</h3>
        <p>PLEASE LOGIN TO YOUR ACCOUNT TO CONTINUE</p>
      </div>

      <?php if (!empty($_GET['error'])): ?>
        <div class="text-center" style="font-size: 13.5px; font-weight: 600; color: #dc2626; margin-bottom: 20px; line-height: 1.5; background: transparent; padding: 0;">
          <i class="fa fa-exclamation-circle me-1" style="color: #dc2626;"></i> <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="form-group mb-3 position-relative">
          <input type="text" name="userid" class="form-control ananta-modal-input" placeholder="Email or User ID" required>
          <i class="fa fa-user" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </div>
        <div class="form-group mb-3 position-relative">
          <input type="password" name="password" class="form-control ananta-modal-input" placeholder="Password" required>
          <i class="fa fa-lock" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4" style="font-size: 13px;">
          <label class="mb-0 d-flex align-items-center gap-1" style="color: #64748b; cursor: pointer;">
            <input type="checkbox" checked style="accent-color: #10b981;"> Remember me
          </label>
          <a href="reset-password.php" style="color: #00b4d8; font-weight: 600; text-decoration: none;">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-ananta-primary w-100">LOGIN</button>

        <?php
        $regUrl = 'register.php';
        if (!empty($_GET)) {
            $regUrl .= '?' . http_build_query($_GET);
        }
        ?>
        <div class="text-center mt-3" style="font-size: 13px; color: #64748b;">
          Don't have an account? <a href="<?php echo htmlspecialchars($regUrl, ENT_QUOTES, 'UTF-8'); ?>" style="color: #10b981; font-weight: 700; text-decoration: none;">Signup</a>
        </div>
      </form>
      <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
        <button type="button" onclick="openLrcModal();" class="d-none d-lg-inline-block" style="background: transparent; border: none; color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; padding: 0 2px; outline: none; vertical-align: baseline;">©</button>Copyright <span class="d-lg-none">©</span> <?php echo date('Y'); ?> Ananta. All Rights Reserved.
      </div>
    </div>  
  </div>

  <?php include_once __DIR__ . '/common/login_reg_control_modal.php'; ?>

  <!-- JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
