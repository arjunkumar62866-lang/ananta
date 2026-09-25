<?php 
session_start();
require("common/connection.php");
require 'common/db_method.php'; // contains session, DB connection, and loginUser()


$usponserid= $_GET['msg'];
$userdata=getuserdatabysponserid($usponserid);

$homeset= getHomeSettings($pdo);
$hmmobile = $homeset['mobile'];
$hmemail = $homeset['email'];
$hmaddress = $homeset['address'];
$hmtitle = $homeset['title'];
$hmurl = $homeset['url'];
$hmpackage = $homeset['package'];
$hmpre = $homeset['pre'];
$hmbitly = $homeset['bitly'];
$hmemailfrom = $homeset['emailfrom'];
$hmbg = $homeset['background'];
$hmlogo = $homeset['logo'];
$hmfavicon = $homeset['favicon'];
$hmcolor = $homeset['color'];



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

<body class="bg-theme bg-theme1">
<div id="particles-js"></div>
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

    <div class="loader-wrapper">
      <div class="lds-ring">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <div class="card mx-auto my-5" style="max-width: 520px; border-radius: 22px; border: 1px solid #cbd5e1; background: #ffffff; box-shadow: 0 15px 35px rgba(0,0,0,0.15); overflow: hidden;">
      <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
          <?php if (!empty($hmlogo)): ?>
            <img src="<?php echo $hmlogo;?>" alt="ANANTA Logo" height="70px" style="max-width: 220px; object-fit: contain;">
          <?php else: ?>
            <h2 style="font-weight: 800; color: #0284c7; letter-spacing: -0.5px; margin: 0;">ANANTA</h2>
          <?php endif; ?>
          <h4 class="mt-3 mb-1" style="font-size: 22px; font-weight: 800; color: #0f172a;">Welcome to ANANTA</h4>
          <p class="text-muted mb-0" style="font-size: 14px; font-weight: 600;">Hello, <span style="color: #0284c7;"><?php echo htmlspecialchars($userdata['name']); ?></span>!</p>
          <div class="badge mt-2" style="background: rgba(22, 163, 74, 0.12); color: #16a34a; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px;">
            🎉 Account Successfully Created
          </div>
        </div>

        <div style="background: #f8fafc; border-radius: 16px; border: 1.5px solid #cbd5e1; padding: 18px; margin-bottom: 24px;">
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase;">Login ID / Username</span>
            <span style="font-size: 15px; font-weight: 800; color: #0284c7; font-family: monospace;" id="msgUserId"><?php echo $hmpre . $userdata['userid']; ?></span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase;">Login Password</span>
            <span style="font-size: 15px; font-weight: 800; color: #0f172a; font-family: monospace;"><?php echo htmlspecialchars($userdata['pass']); ?></span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase;">Transaction Key</span>
            <span style="font-size: 15px; font-weight: 800; color: #16a34a; font-family: monospace;"><?php echo htmlspecialchars($userdata['txn_pass']); ?></span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase;">Mobile Number</span>
            <span style="font-size: 14px; font-weight: 700; color: #334155;"><?php echo htmlspecialchars($userdata['mobile']); ?></span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2">
            <span style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase;">Sponsor ID</span>
            <span style="font-size: 14px; font-weight: 700; color: #334155;"><?php echo $hmpre . $userdata['sponserid']; ?></span>
          </div>
        </div>

        <div style="background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 10px; padding: 12px 14px; margin-bottom: 24px;">
          <p style="color: #991b1b; margin: 0; font-size: 12.5px; font-weight: 600;">
            🔒 <strong>Security Warning:</strong> Please keep these details secure. Do not share your password or Transaction Key with anyone.
          </p>
        </div>

        <!-- Continue / Login Button -->
        <a href="login.php" class="btn text-white font-weight-bold w-100 d-inline-flex align-items-center justify-content-center gap-2" style="height: 50px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 12px; font-size: 16px; text-decoration: none; box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25);">
          Continue to Login <i class="fa fa-arrow-right"></i>
        </a>
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

  <script src="particles.js"></script>
  <script src="app.js"></script>
  
  <style>
#particles-js {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}
</style>
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:02:00 GMT -->

</html>