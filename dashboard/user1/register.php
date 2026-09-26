<?php
include 'common/connection.php';
include 'common/db_method.php';

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

// Extract URL Referral & Position Parameters
$defaultSponsorFromURL = '';
if (!empty($_GET['refferalId'])) {
    $defaultSponsorFromURL = trim($_GET['refferalId']);
} elseif (!empty($_GET['ref'])) {
    $defaultSponsorFromURL = trim($_GET['ref']);
} elseif (!empty($_GET['referral'])) {
    $defaultSponsorFromURL = trim($_GET['referral']);
} elseif (!empty($_GET['sponsor'])) {
    $defaultSponsorFromURL = trim($_GET['sponsor']);
} elseif (!empty($_GET['sponsorid'])) {
    $defaultSponsorFromURL = trim($_GET['sponsorid']);
} elseif (!empty($_GET['sponsor_id'])) {
    $defaultSponsorFromURL = trim($_GET['sponsor_id']);
} elseif (!empty($_GET['referral_code'])) {
    $defaultSponsorFromURL = trim($_GET['referral_code']);
} elseif (!empty($_GET['uid'])) {
    $defaultSponsorFromURL = trim($_GET['uid']);
}

if (!empty($defaultSponsorFromURL) && is_numeric($defaultSponsorFromURL) && strpos(strtoupper($defaultSponsorFromURL), 'AN') !== 0) {
    $defaultSponsorFromURL = 'AN' . $defaultSponsorFromURL;
}

$defaultPositionFromURL = '';
if (!empty($_GET['position'])) {
    $defaultPositionFromURL = strtolower(trim($_GET['position']));
} elseif (!empty($_GET['type'])) {
    $defaultPositionFromURL = strtolower(trim($_GET['type']));
}

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
    $position = $_POST['position']; // LEFT / RIGHT chosen in form

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
                $stmt = $pdo->prepare("SELECT COUNT(*) as mobile_check FROM user WHERE mobile = ?");
                $stmt->execute([$mobile]);
                $row = $stmt->fetch();
                if ($row['mobile_check'] > 0) {
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

                    // Insert sponsor relation
                    insertSponsor($pdo, $sponserid1, $userid, $date);

                    $userData = [
                        $userid, $name, $mobile, $email, '', $password, $transaction_password,
                        $sponserid1, $sponsername, $sponserid1, '0', '1', '', '', $date, '', '', '0', 'active',
                        '', $time, '', '', '0', '', '', '', $userid, '', '0', '', '', '', '', '', $otpreg, $coinbonus
                    ];

                    // Insert user into tree with spillover logic
                    $result = insertIntoTree($pdo, $sponserid1, $userid, strtolower($position));
                    if (!$result) {
                        echo "<script>alert('No space available in $position branch of sponsor');window.location = 'register.php';</script>";
                        exit();
                    }

                    $query_register = insertUser($pdo, $userData);

                    if ($query_register) {
                        insertKYC($pdo, $userid, $aadhar);

                        // Set active default user_image from tbl_homest if set by Admin
                        try {
                            $stmtDefImg = $pdo->query("SELECT default_user_image FROM tbl_homest WHERE default_user_image IS NOT NULL AND default_user_image != '' LIMIT 1");
                            if ($stmtDefImg && $defRow = $stmtDefImg->fetch(PDO::FETCH_ASSOC)) {
                                if (!empty($defRow['default_user_image'])) {
                                    $pdo->prepare("UPDATE user SET user_image = :def_img WHERE userid = :uid")->execute([
                                        ':def_img' => $defRow['default_user_image'],
                                        ':uid' => $userid
                                    ]);
                                }
                            }
                        } catch (Exception $e) {
                            // Silent catch
                        }

                        // Send branded Welcome Email with Hostinger-compatible headers
                        $to = $email;
                        $subject = "Welcome to ANANTA — Your Account Details & OTP";
                        
                        $fromEmailDomain = !empty($home['emailfrom']) ? $home['emailfrom'] : 'no-reply@anantamtptl.com';
                        if (strpos($fromEmailDomain, '@gmail.com') !== false || strpos($fromEmailDomain, '@yahoo.com') !== false) {
                            $fromEmailDomain = 'no-reply@anantamtptl.com';
                        }
                        $replyToEmail = !empty($hm_email) ? $hm_email : $fromEmailDomain;

                        $headers = "From: ANANTA Security <" . strip_tags($fromEmailDomain) . ">\r\n";
                        $headers .= "Reply-To: " . strip_tags($replyToEmail) . "\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        
                        $loginUrl = (!empty($hmurl) ? rtrim($hmurl, '/') : 'http://localhost:8000') . "/dashboard/user1/login.php";
                        
                        $message = '
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Welcome to ANANTA</title>
                        </head>
                        <body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: \'Plus Jakarta Sans\', Arial, sans-serif;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                                <tr>
                                    <td align="center" style="padding: 40px 15px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;">
                                            <tr>
                                                <td align="center" style="padding: 35px 30px 25px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);">
                                                    <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px;">ANANTA</h1>
                                                    <p style="color: rgba(255,255,255,0.9); margin: 6px 0 0; font-size: 14px; font-weight: 600;">Welcome to the Platform</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 35px 30px;">
                                                    <h2 style="color: #0f172a; margin: 0 0 10px; font-size: 20px; font-weight: 800;">Hello, ' . htmlspecialchars($name) . '! 🎉</h2>
                                                    <p style="color: #475569; margin: 0 0 24px; font-size: 14.5px; line-height: 1.6;">Your account has been successfully created. Here are your account credentials and security details.</p>
                                                    
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #f8fafc; border-radius: 14px; border: 1.5px solid #cbd5e1; margin-bottom: 25px; overflow: hidden;">
                                                        <tr>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; font-weight: 700; color: #64748b;">Full Name</td>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14.5px; font-weight: 800; color: #0f172a;">' . htmlspecialchars($name) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; font-weight: 700; color: #64748b;">Login ID / Username</td>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14.5px; font-weight: 800; color: #0284c7; font-family: monospace;">' . htmlspecialchars($hmpre . $userid) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; font-weight: 700; color: #64748b;">Login Password</td>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14.5px; font-weight: 800; color: #0f172a; font-family: monospace;">' . htmlspecialchars($password) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; font-weight: 700; color: #64748b;">Transaction Key</td>
                                                            <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14.5px; font-weight: 800; color: #16a34a; font-family: monospace;">' . htmlspecialchars($transaction_password) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 14px 18px; font-size: 13.5px; font-weight: 700; color: #64748b;">Security PIN / OTP</td>
                                                            <td style="padding: 14px 18px; font-size: 14.5px; font-weight: 800; color: #0284c7; font-family: monospace;">' . htmlspecialchars($otpreg) . '</td>
                                                        </tr>
                                                    </table>

                                                    <div style="background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: 12px 16px; margin-bottom: 28px;">
                                                        <p style="color: #991b1b; margin: 0; font-size: 13px; font-weight: 600;">🔒 <strong>Security Reminder:</strong> Please keep these details secure and do not share your Password or Transaction Key with anyone.</p>
                                                    </div>

                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td align="center">
                                                                <a href="' . $loginUrl . '" target="_blank" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); color: #ffffff; text-decoration: none; border-radius: 12px; font-size: 15px; font-weight: 800; box-shadow: 0 6px 20px rgba(2,132,199,0.25);">Continue to Login &rarr;</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding: 20px 30px; background: #f8fafc; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 12px; font-weight: 500;">
                                                    &copy; ' . date('Y') . ' ANANTA Multi Trade. All rights reserved.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>
                        ';

                        @mail($to, $subject, $message, $headers);

                        echo "<script>window.location = 'message.php?msg=$userid';</script>";
                        exit();
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

// ----------------------
// HELPER FUNCTIONS
// ----------------------

function insertIntoTree($pdo, $sponsorId, $newUserId, $side) {
    // Find first available node under sponsor in given side
    $availableNode = findAvailableNode($pdo, $sponsorId, $side);

    if (!$availableNode) {
        return false; // no space found
    }

    // Place new user under available node
    if ($side == "left") {
        $pdo->prepare("UPDATE tree SET left_id = ? WHERE userid = ?")
            ->execute([$newUserId, $availableNode]);
    } else {
        $pdo->prepare("UPDATE tree SET right_id = ? WHERE userid = ?")
            ->execute([$newUserId, $availableNode]);
    }

    // Insert new user’s tree record
    $pdo->prepare("INSERT INTO tree (userid, join_side, created_at) VALUES (?, ?, NOW())")
        ->execute([$newUserId, $side]);

    // Update counts upwards
    updateCounts($pdo, $availableNode, $side);

    return true;
}

function findAvailableNode($pdo, $rootUserId, $side) {
    $queue = [$rootUserId];

    while (!empty($queue)) {
        $current = array_shift($queue);

        $stmt = $pdo->prepare("SELECT left_id, right_id FROM tree WHERE userid = ?");
        $stmt->execute([$current]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) continue;

        if ($side == "left") {
            if (empty($row['left_id'])) {
                return $current; // space found on left
            } else {
                $queue[] = $row['left_id'];
            }
        } else {
            if (empty($row['right_id'])) {
                return $current; // space found on right
            } else {
                $queue[] = $row['right_id'];
            }
        }
    }
    return false; // no space found
}

function updateCounts($pdo, $sponsorId, $side) {
    if (!$sponsorId) return;

    if ($side == "left") {
        $pdo->prepare("UPDATE tree SET lefttotal = lefttotal + 1 WHERE userid = ?")
            ->execute([$sponsorId]);
    } else {
        $pdo->prepare("UPDATE tree SET righttotal = righttotal + 1 WHERE userid = ?")
            ->execute([$sponsorId]);
    }

    // Find sponsor's parent
    $stmt = $pdo->prepare("SELECT u.sponserid, t.left_id, t.right_id 
                           FROM user u 
                           JOIN tree t ON u.sponserid = t.userid
                           WHERE u.userid = ?");
    $stmt->execute([$sponsorId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['sponserid']) {
        $parentSide = ($row['left_id'] == $sponsorId) ? "left" : "right";
        updateCounts($pdo, $row['sponserid'], $parentSide);
    }
}
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
  <link rel="icon" href="<?php echo $hmfavicon?>" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- FontAwesome / Icons CSS-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
      max-width: 450px;
      margin: 0 auto;
    }
    .ananta-auth-modal-card {
      background: #ffffff !important;
      border-radius: 24px !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
      border: none !important;
      padding: 30px 26px;
      color: #1e293b !important;
      position: relative;
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 10px;
    }
    .auth-logo img {
      max-height: 85px;
      width: auto;
      object-fit: contain;
    }
    .auth-header {
      text-align: center;
      margin-bottom: 20px;
    }
    .auth-header h3 {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a !important;
      margin: 0 0 4px 0;
    }
    .auth-header p {
      font-size: 10px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      font-weight: 600;
      color: #64748b !important;
      margin: 0;
    }
    .ananta-modal-input {
      background-color: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 10px !important;
      height: 42px !important;
      padding-right: 36px !important;
      font-size: 13.5px;
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
      border-radius: 10px !important;
      height: 42px;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #ffffff !important;
      text-transform: uppercase;
      width: 100%;
      box-shadow: 0 8px 18px -4px rgba(16, 185, 129, 0.4);
      transition: all 0.25s ease;
      cursor: pointer;
    }
    .btn-ananta-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 12px 22px -4px rgba(16, 185, 129, 0.5);
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
        <h3>Create Account</h3>
        <p>JOIN ANANTA TO START YOUR JOURNEY</p>
      </div>

      <?php if (!empty($_GET['error'])): ?>
        <div class="text-center" style="font-size: 13.5px; font-weight: 600; color: #dc2626; margin-bottom: 16px; line-height: 1.5; background: transparent; padding: 0;">
          <i class="fa fa-exclamation-circle me-1" style="color: #dc2626;"></i> <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form action="" method="post" id="registration_form">
        <!-- Referrer ID -->
        <div class="form-group mb-2 position-relative">
          <input type="text" name="refferalId" id="referrerId" class="form-control ananta-modal-input" placeholder="Referrer ID" required value="<?php echo htmlspecialchars($defaultSponsorFromURL, ENT_QUOTES, 'UTF-8'); ?>">
          <i class="fa fa-link" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </div>

        <!-- Sponsor Name -->
        <div class="form-group mb-2" id="sponsor_name" style="display: none;">
          <input type="text" id="response2" class="form-control" style="background-color: #f1f5f9; font-size: 13px; font-weight: 600; border-radius: 10px; height: 38px; color: #0f172a;" readonly>
        </div>

        <!-- Full Name -->
        <div class="form-group mb-2 position-relative">
          <input type="text" name="userName" id="exampleInputName" class="form-control ananta-modal-input" placeholder="Full Name" required>
          <i class="fa fa-user" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </div>

        <!-- Email Address -->
        <div class="form-group mb-2 position-relative">
          <input type="email" name="email" id="exampleInputEmailId" class="form-control ananta-modal-input" placeholder="Email Address" required>
          <i class="fa fa-envelope" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </div>

        <!-- Mobile Number & Code -->
        <div class="form-group mb-2 d-flex gap-2">
          <select class="form-control" name="mobilecode" id="countryCode" style="flex: 0 0 42%; min-width: 0; border-radius: 10px; height: 42px; font-size: 12.5px; padding-left: 8px; padding-right: 8px; border: 1px solid #cbd5e1; color: #0f172a;">
            <option value="+91" selected>India (+91)</option>
            <option value="+1">US (+1)</option>
            <option value="+44">UK (+44)</option>
            <option value="+971">UAE (+971)</option>
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
          <div class="position-relative flex-grow-1" style="flex: 1 1 58%; min-width: 0;">
            <input type="text" name="mobile" id="mobileNumber" class="form-control ananta-modal-input" placeholder="Mobile Number" required>
            <i class="fa fa-phone" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
          </div>
        </div>

        <!-- Password -->
        <div class="form-group mb-2 position-relative">
          <input type="password" name="pass1" id="exampleInputPassword" class="form-control ananta-modal-input" placeholder="Password" required style="padding-right: 42px;">
          <button type="button" onclick="togglePasswordInput(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; z-index: 5;" title="Toggle Password Visibility">
            <i class="fa fa-eye-slash"></i>
          </button>
        </div>

        <!-- Confirm Password -->
        <div class="form-group mb-2 position-relative">
          <input type="password" name="pass2" id="confirmPassword" class="form-control ananta-modal-input" placeholder="Confirm Password" required style="padding-right: 42px;">
          <button type="button" onclick="togglePasswordInput(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; z-index: 5;" title="Toggle Password Visibility">
            <i class="fa fa-eye-slash"></i>
          </button>
        </div>
        
        <div id="passwordWarning" class="mb-2" style="color: #dc2626; font-size: 12px; font-weight: 600;"></div>

        <!-- Position Selection -->
        <div class="d-flex justify-content-between align-items-center mb-2 px-2 py-1" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12.5px;">
          <span style="font-weight: 600; color: #475569;">Position:</span>
          <div class="d-flex gap-3">
            <label class="mb-0" style="cursor: pointer; color: #334155;"><input type="radio" id="left" name="position" value="left" <?php echo ($defaultPositionFromURL !== 'right') ? 'checked' : ''; ?> style="accent-color: #10b981;"> Left</label>
            <label class="mb-0" style="cursor: pointer; color: #334155;"><input type="radio" id="right" name="position" value="right" <?php echo ($defaultPositionFromURL === 'right') ? 'checked' : ''; ?> style="accent-color: #10b981;"> Right</label>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitBtn" class="btn btn-ananta-primary w-100 mt-2">REGISTER</button>

        <?php
        $loginLinkUrl = 'login.php';
        if (!empty($_GET)) {
            $loginLinkUrl .= '?' . http_build_query($_GET);
        }
        ?>
        <div class="text-center mt-2" style="font-size: 12.5px; color: #64748b;">
          Already have an account? <a href="<?php echo htmlspecialchars($loginLinkUrl, ENT_QUOTES, 'UTF-8'); ?>" style="color: #10b981; font-weight: 700; text-decoration: none;">Sign In</a>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <script src="particles.js"></script>
  <script src="app.js"></script>

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

    if ($('#referrerId').val().trim().length > 2) {
      $('#referrerId').trigger('blur');
    }
  });
  </script>
  
  <!-- Password matching & validation -->
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

  window.togglePasswordInput = function(btn) {
    const input = btn.previousElementSibling;
    if (input && (input.type === 'password' || input.type === 'text')) {
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="fa fa-eye" style="color: #0284c7;"></i>';
      } else {
        input.type = 'password';
        btn.innerHTML = '<i class="fa fa-eye-slash" style="color: #94a3b8;"></i>';
      }
    }
  };
  </script>
</body>
</html>

