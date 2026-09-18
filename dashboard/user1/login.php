<?php 
session_start();
require 'common/db_method.php'; // contains session, DB connection, and loginUser()
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$date = date('Y-m-d');
$time = date('h:i a');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($userid, $password, $pdo);

    if ($result['status']) {
        header('Location: index.php');
        exit;
    } else {
        echo "<script>alert('{$result['message']}'); window.location.href='login.php';</script>";
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
          <div class="card-title text-uppercase text-center py-3">Sign In</div>
          <form action="login.php" method="POST">
            <div class="form-group">
              <label for="exampleInputUsername" class="sr-only">UserId</label>
              <div class="position-relative has-icon-right">
                <input type="text" name="userid" id="exampleInputUsername" class="form-control input-shadow"
                  placeholder="Enter UserId">
                <div class="form-control-position">
                  <i class="icon-user"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword" class="sr-only">Password</label>
              <div class="position-relative has-icon-right">
                <input type="password" name="password" id="exampleInputPassword" class="form-control input-shadow"
                  placeholder="Enter Password">
                <div class="form-control-position">
                  <i class="icon-lock"></i>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-6">
                <div class="icheck-material-white">
                  <input type="checkbox" id="user-checkbox" checked="" />
                  <label for="user-checkbox">Remember me</label>
                </div>
              </div>
              <div class="form-group col-6 text-right">
                <a href="reset-password.php">Reset Password</a>
              </div>
            </div>
            <button type="submit" class="btn btn-light btn-block">Sign In</button>
            <!--<div class="text-center mt-3">Sign In With</div>-->

            <!--<div class="form-row mt-4">-->
            <!--  <div class="form-group mb-0 col-6">-->
            <!--    <button type="button" class="btn btn-light btn-block"><i class="fa fa-facebook-square"></i>-->
            <!--      Facebook</button>-->
            <!--  </div>-->
            <!--  <div class="form-group mb-0 col-6 text-right">-->
            <!--    <button type="button" class="btn btn-light btn-block"><i class="fa fa-twitter-square"></i>-->
            <!--      Twitter</button>-->
            <!--  </div>-->
            <!--</div>-->

          </form>
        </div>
      </div>
      <div class="card-footer text-center py-3">
        <p class="text-warning mb-0">Do not have an account? <a href="new_binary_registration_form.php"> Sign Up here</a></p>
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