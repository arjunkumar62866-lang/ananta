<?php
ob_start();
session_start();

$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: register.php" . $queryString);
exit();

$hmtitle = $home['title'] ?? 'Ananta';
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

// If sponsor ID is passed in URL, use it
$defaultSponsorFromURL = isset($_GET['uid']) ? $_GET['uid'] : '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'common/login_reg_control_helper.php';
    $lrcState = getLoginRegControlState($pdo);
    if ($lrcState['status'] === 'OFF' && $lrcState['message_type'] === 'ERROR') {
        $errMsg = urlencode($lrcState['message_text']);
        header("Location: new_binary_registration_form.php?error={$errMsg}");
        exit;
    }

        // --- NEW SPONSOR LOGIC ---
    if (!empty($defaultSponsorFromURL)) {
        // sponsor id from URL
        $sponserid = $defaultSponsorFromURL;
    } else {
        // sponsor id from form
        $sponserid = $_POST['refferalId'];
    }
    // $sponserid = $_POST['refferalId'];
    $sponserid1 = substr($sponserid, 2);
    $userid = rand(100000, 999999);
    $otpreg = rand(1000, 9999);
    $transaction_password = rand(100000, 999999);
    $name = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['pass1'];
    $conpassword = $_POST['pass2'];
    $mobile = $_POST['mobilecode'] . $_POST['mobile'];
    $position = $_POST['position']; // LEFT / RIGHT chosen in form
    $side = $position;
    
    $aadhar = '';
    $father = '';
    $state = '';
    $address = '';
    $gender = '';
    $pin_code = '';
    
   
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid");
    $stmt->execute(['userid' => $sponserid1]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // if (!$row) {
    //     echo '<script>alert("Sponsor ID not found in tree table.");window.location="new_binary_registration_form.php";</script>';
    //     exit();
    // }
    
    $mside = $row[$position."_id"];
    
    
    if($mside==''){
      $underuserid=$sponserid;
    } else {
      $a= find_parent($sponserid,$position,$pdo);
      $underuserid=$a;
    }
    $userdata = getuserdatabysponserid($sponserid1);
    if ($userdata !== null) {
        $idactive = $userdata['idactive'];

        if (userid($userid) == true) {
            if (checkuseridregister($sponserid1) == 1) {
                $flag = 1;
                $stmt = $pdo->prepare("SELECT COUNT(*) as email_check FROM user WHERE email = ?");
                $stmt->execute([$email]);
                $row = $stmt->fetch();
                $emailcheck = $row['email_check'];

                // if ($emailcheck > 0) {
                //     echo '<script>alert("You Can Register Only 1 ID From Same Email");window.location = "new_binary_registration_form.php";</script>';
                //     exit();
                // }

                $stmt = $pdo->prepare("SELECT COUNT(*) as mobile_check FROM user WHERE mobile = ?");
                $stmt->execute([$mobile]);
                $row = $stmt->fetch();
                $mobilecheck = $row['mobile_check'];

                if ($mobilecheck > 0) {
                    echo '<script>alert("You Can Register Only 1 ID From Same Mobile Number");window.location = "new_binary_registration_form.php";</script>';
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

                    // Attempt to insert into tree
                    // $result = insertIntoTree($pdo, $sponserid1, $userid, $position,$underuserid);
                    // if (!$result) {
                    //     echo '<script>alert("Unable to place user in the tree. Please try again.");window.location = "new_binary_registration_form.php";</script>';
                    //     exit();
                    // }

                    // Insert user data after successful tree placement
                    $userData = [
                        $userid, $name, $mobile, $email, '', $password, $transaction_password,
                        $sponserid1, $sponsername, $sponserid1, '0', '1', $side, '', $date, '', '', '0', 'active',
                        '', $time, '', '', '0', 'NA', '', '', $userid, '', '0', '', '', '', '', '', $otpreg, $coinbonus
                    ];
                    
                    $query_register = insertUser($pdo, $userData);
                    
                    ////********UPDATE TREE TOTAL CODE (PDO VERSION) ***//////////////////

                    // Find user’s parent where this user is placed as left or right
                    // $stmt = $pdo->prepare("SELECT * FROM tree WHERE `left_id` = :uid OR `right_id` = :uid");
                    // $stmt->execute([':uid' => $userid]);
                    // $rf = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // if (!$rf) {
                    //     die("No data found for the given user ID: $userid");
                    // }
                    
                    // $temp_underuserid = $rf["userid"];   // Parent ID
                    // $temp_side_count  = $side . 'total'; // lefttotal or righttotal
                    // $temp_side        = $side;
                    // $total_count      = 1;
                    // $i                = 1;
                    
                    // while ($total_count > 0) {
                    
                    //     // Fetch current row
                    //     $stmt2 = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid");
                    //     $stmt2->execute([':uid' => $temp_underuserid]);
                    //     $r = $stmt2->fetch(PDO::FETCH_ASSOC);
                    
                    //     if (!$r) {
                    //         $total_count = 0;
                    //         continue;
                    //     }
                    
                    //     // Calculate updated total
                    //     $current_temp_side_count = $r[$temp_side_count] + 1;
                    
                    //     // Update the tree total
                    //     $stmt3 = $pdo->prepare("UPDATE tree SET `$temp_side_count` = :count WHERE userid = :uid");
                    //     $stmt3->execute([
                    //         ':count' => $current_temp_side_count,
                    //         ':uid'   => $temp_underuserid
                    //     ]);
                    
                    //     // Move to next upper parent
                    //     if ($temp_underuserid != "") {
                    
                    //         // getUnderId() must now use $pdo
                    //         $next_under_userid = getUnderId($pdo, $temp_underuserid);
                    
                    //         // getUnderIdPlace() must also use $pdo
                    //         $temp_side = getUnderIdPlace($pdo, $temp_underuserid);
                    
                    //         $temp_side_count = $temp_side . 'total';
                    //         $temp_underuserid = $next_under_userid;
                    
                    //         $i++;
                    //     }
                    
                    //     // Break loop if no further parent
                    //     if ($temp_underuserid == "") {
                    //         $total_count = 0;
                    //     }
                    // }
                    
                    ////********UPDATE TREE TOTAL CODE END (PDO VERSION) ***//////////////////
                    

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
                        $message .= "<tr><td><strong>Email:</strong></td><td>" . htmlspecialchars($email) . "</td></tr>";
                        $message .= "<tr><td><strong>User id:</strong></td><td>" . htmlspecialchars($hmpre.$userid) . "</td></tr>";
                        $message .= "<tr><td><strong>Password:</strong></td><td>" . htmlspecialchars($password) . "</td></tr>";
                        // $message .= "<tr><td><strong>Transaction Password:</strong></td><td>" . htmlspecialchars($transaction_password) . "</td></tr>";
                        $message .= "</table></body></html>";

                        mail($to, $subject, $message, $headers);

                        $pinfinal = $userid;
                        if($position == "left"){
                            for ($i = 0; $i < 20; $i++) {
                                $mysponserid = getmysponserid($pinfinal);
                                $sponserdetails = getuserdatabysponserid($mysponserid);
                                if ($pinfinal !== '1290') {
                                    $spcode1 = $sponserdetails['userid'];
                                    $spamont = $sponserdetails['amount'];
                                    $isidactive = $sponserdetails['idactive'];
                                    $directactive = getmydirectactive($spcode1);
    
                                    $level = $i + 1;
                                    insert_userlevel_a($mysponserid, $userid, $level);
                                    $pinfinal = $spcode1;
                                }
                            }
                        }
                        else{
                            for ($i = 0; $i < 20; $i++) {
                                $mysponserid = getmysponserid($pinfinal);
                                $sponserdetails = getuserdatabysponserid($mysponserid);
                                if ($pinfinal !== '1290') {
                                    $spcode1 = $sponserdetails['userid'];
                                    $spamont = $sponserdetails['amount'];
                                    $isidactive = $sponserdetails['idactive'];
                                    $directactive = getmydirectactive($spcode1);
    
                                    $level = $i + 1;
                                    insert_userlevel_b($mysponserid, $userid, $level);
                                    $pinfinal = $spcode1;
                                }
                            }
                        }
                        ?>
                        <script>
                            window.location = "message.php?msg=<?php echo $userid; ?>";
                        </script>
                        <?php
                    }
                  
                }
            } else {
                echo '<script>alert("Your sponsor ID does not exist");window.location = "new_binary_registration_form.php";</script>';
            }
        } else {
            echo '<script>alert("You Can Register Only 1 ID From Same Email");</script>';
        }
    } else {
        echo '<script>alert("Invalid sponsor ID.");window.location = "new_binary_registration_form.php";</script>';
    }
}

// ----------------------
// HELPER FUNCTIONS
// ----------------------
function insertIntoTree($pdo, $sponsorId, $newUserId, $preferredSide, $underuserid) {

    $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = ?");
    $stmt->execute([$sponsorId]);
    $sponsorTree = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sponsorTree) {
        $pdo->prepare("INSERT INTO tree (userid, created_at) VALUES (?, NOW())")
            ->execute([$sponsorId]);
        $stmt->execute([$sponsorId]);
        $sponsorTree = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($preferredSide == "left" && empty($sponsorTree['left_id'])) {
        $pdo->prepare("UPDATE tree SET left_id = ? WHERE userid = ?")
            ->execute([$newUserId, $sponsorId]);
        $side = "left";
    } elseif ($preferredSide == "right" && empty($sponsorTree['right_id'])) {
        $pdo->prepare("UPDATE tree SET right_id = ? WHERE userid = ?")
            ->execute([$newUserId, $sponsorId]);
        $side = "right";
    } else {
        $side = findAvailableSlot($pdo, $sponsorId, $preferredSide, $newUserId);
        if (!$side) return false;
    }

    $pdo->prepare("INSERT INTO tree (userid, join_side, created_at) VALUES (?, ?, NOW())")
        ->execute([$newUserId, $side]);

    $temp_underuserid = $underuserid;
    $temp_side = $side;
    $temp_side_count = $side . 'total';

    while ($temp_underuserid != "") {

        $stmt2 = $pdo->prepare("SELECT * FROM tree WHERE userid = ?");
        $stmt2->execute([$temp_underuserid]);
        $r = $stmt2->fetch(PDO::FETCH_ASSOC);

        if (!$r) break;

        $current_temp_side_count = $r[$temp_side_count] + 1;

        $stmt3 = $pdo->prepare("UPDATE tree SET `$temp_side_count` = ? WHERE userid = ?");
        $stmt3->execute([$current_temp_side_count, $temp_underuserid]);

        $next_under_userid = getUnderId($pdo, $temp_underuserid);
        $temp_side = getUnderIdPlace($pdo, $temp_underuserid);
        $temp_side_count = $temp_side . 'total';
        $temp_underuserid = $next_under_userid;
    }

    return true;
}

/**
 * Find an available slot for $newUserId under subtree of $currentId.
 * Tries the $preferredSide subtree first (BFS), preserving side-preference at every level.
 * If no slot is found in the preferred-side subtree, tries the opposite side the same way.
 *
 * @param PDO    $pdo
 * @param int    $currentId       The parent id where search starts
 * @param string $preferredSide   'left' or 'right'
 * @param int    $newUserId       The userid to place into a free slot
 * @return string|false           'left' or 'right' if placed, otherwise false
 */
function findAvailableSlot(PDO $pdo, $currentId, $preferredSide, $newUserId) {
    if ($preferredSide !== 'left' && $preferredSide !== 'right') {
        throw new InvalidArgumentException('preferredSide must be "left" or "right".');
    }

    // Helper which does a BFS preferring $sidePref at every node
    $bfsPrefer = function($sidePref) use ($pdo, $currentId, $newUserId) {
        $queue = new SplQueue();
        $visited = []; // to avoid infinite loops if DB has cycles (defensive)
        $queue->enqueue($currentId);

        while (!$queue->isEmpty()) {
            $nodeId = $queue->dequeue();
            if (isset($visited[$nodeId])) continue;
            $visited[$nodeId] = true;

            // get children
            $stmt = $pdo->prepare("SELECT left_id, right_id FROM tree WHERE userid = ? LIMIT 1");
            $stmt->execute([$nodeId]);
            $tree = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$tree) continue;

            // map side to db column
            $colPref = ($sidePref === 'left') ? 'left_id' : 'right_id';
            $colOpp  = ($sidePref === 'left') ? 'right_id' : 'left_id';

            // 1) check the preferred side on current node
            if (empty($tree[$colPref]) || $tree[$colPref] === null || $tree[$colPref] == 0) {
                // place new user here
                $sql = ($colPref === 'left_id')
                    ? "UPDATE tree SET left_id = ? WHERE userid = ?"
                    : "UPDATE tree SET right_id = ? WHERE userid = ?";
                $pdo->prepare($sql)->execute([$newUserId, $nodeId]);
                return ($colPref === 'left_id') ? 'left' : 'right';
            }

            // 2) if preferred side is occupied, but opposite is free we do NOT place here for preferred pass.
            //    We only enqueue children to continue searching deeper into preferred subtree first.
            // Enqueue children preserving the preferred-first order:
            // enqueue the child from the preferred side first, then the other child.
            if (!empty($tree[$colPref])) {
                $queue->enqueue($tree[$colPref]);
            }
            if (!empty($tree[$colOpp])) {
                $queue->enqueue($tree[$colOpp]);
            }
        }

        return false; // not found in this pass
    };

    // First try preferred side BFS
    $placed = $bfsPrefer($preferredSide);
    if ($placed !== false) return $placed;

    // If not placed in preferred subtree, try the opposite side
    $opposite = ($preferredSide === 'left') ? 'right' : 'left';
    $placed = $bfsPrefer($opposite);
    return $placed === false ? false : $opposite;
}


// <!--function updateCounts($pdo, $sponsorId, $side) {-->
// <!--    if (!$sponsorId) return;-->

//     // Update this sponsor’s counts
// <!--    if ($side == "left") {-->
// <!--        $pdo->prepare("UPDATE tree SET lefttotal = lefttotal + 1 WHERE userid = ?")->execute([$sponsorId]);-->
// <!--    } else {-->
// <!--        $pdo->prepare("UPDATE tree SET righttotal = righttotal + 1 WHERE userid = ?")->execute([$sponsorId]);-->
// <!--    }-->

//     // Find sponsor’s parent
// <!--    $stmt = $pdo->prepare("SELECT u.sponserid, t.left_id, t.right_id -->
// <!--                           FROM user u -->
// <!--                           JOIN tree t ON u.userid = t.userid-->
// <!--                           WHERE u.userid = ?");-->
// <!--    $stmt->execute([$sponsorId]);-->
// <!--    $row = $stmt->fetch(PDO::FETCH_ASSOC);-->

// <!--    if ($row && $row['sponserid']) {-->
//         // Check whether this sponsorId is left or right child of parent
// <!--        $parentSide = ($row['left_id'] == $sponsorId) ? "left" : "right";-->
// <!--        updateCounts($pdo, $row['sponserid'], $parentSide);-->
// <!--    }-->


?>

<!DOCTYPE html>
<html lang="en">
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

  <!-- Custom CSS for Modern Floating White Card Registration -->
  <style>
    body.ananta-auth-page {
      background: radial-gradient(circle at 50% 30%, rgba(16, 185, 129, 0.08), rgba(15, 23, 42, 0.75)), url('assets/images/bg-1.jpg') center/cover no-repeat fixed !important;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      margin: 0;
      padding: 30px 15px;
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
      max-width: 480px;
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
      margin-bottom: 25px;
    }
    .auth-header h3 {
      font-size: 26px;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 6px 0;
    }
    .auth-header p {
      font-size: 11px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      font-weight: 600;
      color: #64748b;
      margin: 0;
    }
    .auth-card .form-group {
      margin-bottom: 16px;
    }
    .auth-card .input-group-custom {
      position: relative;
    }
    .auth-card .form-control, .auth-card select.form-control {
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
    .auth-card select.form-control {
      padding-right: 18px !important;
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
    .auth-card .position-selector {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .auth-card .position-selector label.title {
      font-weight: 600;
      font-size: 13px;
      color: #475569;
      margin: 0;
    }
    .auth-card .position-selector .radio-options {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    .auth-card .position-selector .radio-options label {
      margin: 0;
      font-size: 13px;
      font-weight: 600;
      color: #1e293b;
      cursor: pointer;
    }
    .auth-card .signin-text {
      text-align: center;
      font-size: 13px;
      color: #64748b;
      margin-top: 22px;
      font-weight: 500;
    }
    .auth-card .signin-text a {
      color: #10b981;
      font-weight: 700;
      text-decoration: none;
      margin-left: 4px;
    }
    .auth-card .signin-text a:hover {
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
        <h3>Create Account</h3>
        <p>JOIN ANANTA TO START YOUR JOURNEY</p>
      </div>

      <?php if (!empty($_GET['error'])): ?>
        <div class="text-center" style="font-size: 13.5px; font-weight: 600; color: #dc2626; margin-bottom: 20px; line-height: 1.5; background: transparent; padding: 0;">
          <i class="icon-exclamation" style="margin-right: 6px; color: #dc2626;"></i> <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form action="new_binary_registration_form.php" method="post" id="registration_form">
        <!-- Referrer ID -->
        <div class="form-group">
          <div class="input-group-custom">
            <input type="text" name="refferalId" id="referrerId" class="form-control"
              placeholder="Enter Referrer ID" required value="<?php echo $defaultSponsorFromURL; ?>" <?php if(!empty($defaultSponsorFromURL)) echo 'readonly'; ?> >
            <i class="icon-link input-icon"></i>
          </div>
        </div>

        <!-- Sponsor Name -->
        <div class="form-group" id="sponsor_name" style="display: none;">
          <div class="input-group-custom">
            <input type="text" name="refferalid" id="response2" class="form-control" style="background-color: #f1f5f9 !important; font-weight: 600;" readonly>
          </div>
        </div>

        <!-- Name -->
        <div class="form-group">
          <div class="input-group-custom">
            <input type="text" name="userName" id="exampleInputName" class="form-control"
              placeholder="Full Name" required>
            <i class="icon-user input-icon"></i>
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <div class="input-group-custom">
            <input type="email" name="email" id="exampleInputEmailId" class="form-control"
              placeholder="Email Address" required>
            <i class="icon-envelope-open input-icon"></i>
          </div>
        </div>

        <!-- Mobile -->
        <div class="form-group">
          <div class="d-flex align-items-center" style="gap: 10px;">
            <select class="form-control" name="mobilecode" id="countryCode" style="flex: 0 0 40%; min-width: 0; padding-left: 8px; padding-right: 20px;">
              <option value="+1">United States (+1)</option>
              <option value="+91" selected>India (+91)</option>
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
            <div class="input-group-custom flex-grow-1" style="flex: 1 1 60%; min-width: 0;">
              <input type="text" name="mobile" id="mobileNumber" class="form-control"
                placeholder="Mobile Number" required>
              <i class="icon-phone input-icon"></i>
            </div>
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <div class="input-group-custom">
            <input type="password" name="pass1" id="exampleInputPassword" class="form-control"
              placeholder="Password" required>
            <i class="icon-lock input-icon"></i>
          </div>
          <span id="passwordWarning" style="color: #ef4444; font-size: 12px; font-weight: 500; display: block; margin-top: 4px;"></span>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <div class="input-group-custom">
            <input type="password" name="pass2" id="confirmPassword" class="form-control"
              placeholder="Confirm Password" required>
            <i class="icon-lock input-icon"></i>
          </div>
        </div>
        
        <!-- Position --> 
        <div class="form-group">
          <div class="position-selector">
            <label class="title">Select Position:</label> 
            <div class="radio-options">
              <label for="left"><input type="radio" id="left" name="position" value="left" required checked> Left</label> 
              <label for="right"><input type="radio" id="right" name="position" value="right"> Right</label> 
            </div>
          </div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="form-group mb-4">
          <div class="custom-control custom-checkbox" style="padding-left: 1.5rem;">
            <input type="checkbox" class="custom-control-input" id="user-checkbox" name="terms_accepted" checked required />
            <label class="custom-control-label" for="user-checkbox" style="color: #64748b; font-size: 13px; font-weight: 500;">I Agree With Terms & Conditions</label>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitBtn" class="btn btn-primary-action">REGISTER</button>

        <div class="signin-text">
          Already have an account? <a href="login.php">Sign In</a>
        </div>
      </form>
      <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8;">
        Copyright <button type="button" onclick="openLrcModal();" class="d-none d-lg-inline-block" title="Access Control" style="background: transparent; border: none; color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; padding: 0 2px; outline: none; vertical-align: baseline;">©</button><span class="d-lg-none">©</span> <?php echo date('Y'); ?> Ananta. All Rights Reserved.
      </div>
    </div>
  </div><!--wrapper-->

  <?php include_once __DIR__ . '/common/login_reg_control_modal.php'; ?>

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

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
  <script src="particles.js"></script>
  <script src="app.js"></script>
</body>
</html>
