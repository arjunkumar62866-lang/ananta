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
    <div class="card card-authentication1 mx-auto my-5">
      <div class="card-body">
        <div class="card-content p-2">
          <div class="text-center">
            <img src="<?php echo $hmlogo;?>" alt="logo icon" height="80px">
          </div>
          <div class="card-title text-uppercase text-center py-3">Thanks For Registration</div>
          <div style="text-align: left; font-family: Arial, sans-serif; font-size: 15px; color: #444;">
      <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; color:white">
        <strong>User ID</strong> <span><?php echo $hmpre . $userdata['userid']; ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; color:white">
        <strong>Password</strong> <span><?php echo $userdata['pass']; ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; color:white">
        <strong>User Name</strong> <span><?php echo $userdata['name']; ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; color:white">
        <strong>Mobile</strong> <span><?php echo $userdata['mobile']; ?></span>
      </div>
      <!--<div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; color:white">-->
      <!--  <strong>Sponsor Name</strong> <span><php echo $userdata['sponsername']; ?></span>-->
      <!--</div>-->
      <div style="display: flex; justify-content: space-between; padding: 10px 0; color:white">
        <strong>Sponsor ID</strong> <span><?php echo $hmpre . $userdata['sponserid']; ?></span>
      </div>
    </div>

    <!-- Login Button -->
    <a href="login.php" 
       style="display: block; margin-top: 20px; padding: 12px; background: linear-gradient(to right, #4caf05, #3e8e00); color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; font-family: Arial, sans-serif;">
      Login
    </a>
        </div>
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