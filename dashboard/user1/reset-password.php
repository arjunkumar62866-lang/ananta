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

  <link href="assets/css/pace.min.css" rel="stylesheet"/>
  <script src="assets/js/pace.min.js"></script>

  <link rel="icon" href="<?php echo $hmfavicon ?>" type="image/png">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="assets/css/animate.css" rel="stylesheet"/>
  <link href="assets/css/icons.css" rel="stylesheet"/>
  <link href="assets/css/app-style.css" rel="stylesheet"/>
</head>

<body class="bg-theme bg-theme1">

<div id="wrapper">
 <div class="height-100v d-flex align-items-center justify-content-center">

	<div class="card card-authentication1 mb-0">
		<div class="card-body">
		 <div class="card-content p-2">

		  <div class="text-center mb-3">
		    <img src="<?php echo $hmlogo ?>" style="height:90px;border-radius:15px;">
		  </div>

		  <div class="card-title text-uppercase pb-2">Forgot Password</div>

		    <form method="POST">
			  <div class="form-group">
			  <label>User ID</label>
			   <div class="position-relative has-icon-right">
				  <input type="text" name="userid" class="form-control input-shadow" placeholder="Enter User ID" required>
				  <div class="form-control-position">
					  <i class="icon-user"></i>
				  </div>
			   </div>
			  </div>

			  <button type="submit" class="btn btn-light btn-block mt-3">Send Password</button>
			 </form>

		   </div>
		  </div>

		   <div class="card-footer text-center py-3">
		    <p class="text-warning mb-0">Return to <a href="login.php"> Sign In</a></p>
		   </div>
	</div>

 </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/app-script.js"></script>

</body>
</html>
