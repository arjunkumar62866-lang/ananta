<!DOCTYPE html>
<?php
@session_start();
require("common/connection.php");   // contains $pdo
require("common/db_method.php");    // still used for gethomeset()

$homeset = getHomeSettings($pdo);
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

if(function_exists('date_default_timezone_set')){
    date_default_timezone_set("Asia/Kolkata");
}

$date = date('Y-m-d');
$time = date('h:i a');

if(isset($_POST['userid'])){

    $userid = $_POST['userid'];
    $userid = substr($userid, 2);   // remove prefix
    $userid = trim($userid);

    // SECURITY FIX
    $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = ?");
    $stmt->execute([$userid]);

    if($stmt->rowCount() > 0){

        $rowheader = $stmt->fetch(PDO::FETCH_ASSOC);
        $password = $rowheader['pass'];
        $email = $rowheader['email'];
        $txn_pass = $rowheader['txn_pass'];

        // -----------------------
        //  EMAIL SEND
        // -----------------------

        $to = $email;
        $subject = "Your Login Credentials - $hmtitle";

        $headers = "From: " . strip_tags($hmemail) . "\r\n";
        $headers .= "Reply-To: " . strip_tags($hmemail) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = '<html><body>';
        $message .= '<h2>Login Credentials</h2>';
        $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
        $message .= "<tr><td><strong>User ID:</strong></td><td>" . strip_tags($hmpre.$userid) . "</td></tr>";
        $message .= "<tr><td><strong>Password:</strong></td><td>" . strip_tags($password) . "</td></tr>";
        $message .= "<tr><td><strong>Transaction Password:</strong></td><td>" . strip_tags($txn_pass) . "</td></tr>";
        $message .= "</table>";
        $message .= "<br><br><b>Regards,</b><br>$hmtitle";
        $message .= "</body></html>";

        mail($to, $subject, $message, $headers);

        echo "<script>alert('Password sent successfully to your email');window.location.assign('login.php');</script>";
        exit();
    }
    else{
        echo "<script>alert('Invalid User ID');window.location.assign('reset-password.php');</script>";
        exit();
    }
}
?>


<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <title><?php echo $hmtitle; ?> | Forgot Password</title>

  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet" />
  <script src="assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="<?php echo $hmfavicon ?>" type="image/x-icon">
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
      max-height: 85px;
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
        <h3>Forgot Password</h3>
        <p>RECOVER YOUR ACCOUNT PASSWORD</p>
      </div>

      <form method="POST">
        <div class="form-group mb-4 position-relative">
          <input type="text" name="userid" class="form-control ananta-modal-input" placeholder="Enter User ID" required>
          <i class="fa fa-user" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </div>

        <button type="submit" class="btn btn-ananta-primary w-100">SEND PASSWORD</button>

        <div class="text-center mt-3" style="font-size: 13px; color: #64748b;">
          Return to <a href="login.php" style="color: #10b981; font-weight: 700; text-decoration: none;">Sign In</a>
        </div>
      </form>
      <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
        Copyright © <?php echo date('Y'); ?> Ananta. All Rights Reserved.
      </div>
    </div>  
  </div>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

