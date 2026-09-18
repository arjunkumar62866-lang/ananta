<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'common/connection.php'; // provides $pdo (PDO instance)
include 'common/db_method.php';  // keep your helper functions if any

// --- Helper: ensure $pdo exists
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("PDO connection (\$pdo) not available. Check common/connection.php");
}

// Load home settings (your existing function)
$home = getHomeSettings($pdo);
$hmtitle = $home['title'] ?? '';
$hm_mobile = $home['mobile'] ?? '';
$hm_email = $home['email'] ?? '';
$hmwebsite = $home['website'] ?? '';

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$date = date('Y-m-d');
$time = date('h:i a');

// ---------- Helper functions (PDO-safe) ----------

function random_userid() {
    return (string) rand(100000, 999999);
}

function is_userid_unique($pdo, $userid) {
    $stmt = $pdo->prepare("SELECT 1 FROM user WHERE userid = ?");
    $stmt->execute([$userid]);
    return ($stmt->fetchColumn() === false);
}

function check_email_unique($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() == 0;
}

function check_mobile_unique($pdo, $mobile) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE mobile = ?");
    $stmt->execute([$mobile]);
    return $stmt->fetchColumn() == 0;
}

function get_tree_row($pdo, $userid) {
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = ? LIMIT 1");
    $stmt->execute([$userid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sponsor_exists($pdo, $sponsorId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE userid = ?");
    $stmt->execute([$sponsorId]);
    return $stmt->fetchColumn() > 0;
}

function get_user_by_userid($pdo, $userid) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = ? LIMIT 1");
    $stmt->execute([$userid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



function getmydirectid($pdo, $sponsorid) {
    // total direct children (left + right)
    $stmt = $pdo->prepare("SELECT left_id, right_id FROM tree WHERE userid = ? LIMIT 1");
    $stmt->execute([$sponsorid]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = 0;
    if ($r) {
        if (!empty($r['left_id'])) $count++;
        if (!empty($r['right_id'])) $count++;
    }
    return $count;
}

function pin_valid_for_sponsor($pdo, $sponsorid, $pin) {
    if (!$pin) return false;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pin_list WHERE userid = ? AND pin = ? AND status = '1'");
    $stmt->execute([$sponsorid, $pin]);
    return $stmt->fetchColumn() > 0;
}

function find_parent_old_logic($pdo, $sponsorid, $sidePref) {
    // Walk down the sponsor subtree to find first available slot preserving side preference (BFS)
    // Returns userid where new user should be placed (the underuserid)
    $queue = new SplQueue();
    $queue->enqueue($sponsorid);
    $visited = [];

    while (!$queue->isEmpty()) {
        $cur = $queue->dequeue();
        if (isset($visited[$cur])) continue;
        $visited[$cur] = true;

        $row = get_tree_row($pdo, $cur);
        if (!$row) continue;

        $left = $row['left_id'] ?? null;
        $right = $row['right_id'] ?? null;

        $colPref = ($sidePref === 'left') ? 'left_id' : 'right_id';
        $colOpp  = ($sidePref === 'left') ? 'right_id' : 'left_id';

        // If preferred side of this node is empty, this node is the underuserid
        if (empty($row[$colPref]) || $row[$colPref] == 0) {
            return $cur;
        }

        // otherwise enqueue children preferring preferred side first
        if (!empty($row[$colPref])) $queue->enqueue($row[$colPref]);
        if (!empty($row[$colOpp])) $queue->enqueue($row[$colOpp]);
    }

    // if not found, fallback to sponsor itself
    return $sponsorid;
}


function insertIntoTreeAndPlace($pdo, $sponsorId, $newUserId, $preferredSide, $underuserid) {
    // This function attempts to put $newUserId under the sponsor's subtree preserving side preference.
    // Returns true on success, false on failure.

    // Ensure sponsor row exists in tree
    $sponsorTree = get_tree_row($pdo, $sponsorId);
    if (!$sponsorTree) {
        $pdo->prepare("INSERT INTO tree (userid, created_at) VALUES (?, NOW())")->execute([$sponsorId]);
        $sponsorTree = get_tree_row($pdo, $sponsorId);
    }

    // If sponsor preferred side is free directly, use it
    $col = ($preferredSide === 'left') ? 'left_id' : 'right_id';
    if (empty($sponsorTree[$col]) || $sponsorTree[$col] == 0) {
        $pdo->prepare("UPDATE tree SET {$col} = ? WHERE userid = ?")->execute([$newUserId, $sponsorId]);
        $side = $preferredSide;
    } else {
        // find an underuserid (from passed one or compute via BFS)
        if (empty($underuserid)) {
            $underuserid = find_parent_old_logic($pdo, $sponsorId, $preferredSide);
        }

        // place under underuserid preserving side preference where available
        $underTree = get_tree_row($pdo, $underuserid);
        if (!$underTree) {
            $pdo->prepare("INSERT INTO tree (userid, created_at) VALUES (?, NOW())")->execute([$underuserid]);
            $underTree = get_tree_row($pdo, $underuserid);
        }
        $colUnder = ($preferredSide === 'left') ? 'left_id' : 'right_id';
        if (empty($underTree[$colUnder]) || $underTree[$colUnder] == 0) {
            $pdo->prepare("UPDATE tree SET {$colUnder} = ? WHERE userid = ?")->execute([$newUserId, $underuserid]);
            $side = $preferredSide;
        } else {
            // fallback: use find_parent_old_logic to find any available slot in subtree
            $placementNode = find_parent_old_logic($pdo, $sponsorId, $preferredSide);
            if (!$placementNode) return false;
            $placementTree = get_tree_row($pdo, $placementNode);
            $colTryLeft = 'left_id';
            if (empty($placementTree[$colTryLeft]) || $placementTree[$colTryLeft] == 0) {
                $pdo->prepare("UPDATE tree SET left_id = ? WHERE userid = ?")->execute([$newUserId, $placementNode]);
                $side = 'left';
            } else {
                $pdo->prepare("UPDATE tree SET right_id = ? WHERE userid = ?")->execute([$newUserId, $placementNode]);
                $side = 'right';
            }
        }
    }

    // Insert new user's tree row
    $pdo->prepare("INSERT INTO tree (userid, join_side, created_at) VALUES (?, ?, NOW())")
        ->execute([$newUserId, $side]);

    return $side; // return side where placed (left/right)
}

function updateAncestorsTotals($pdo, $startNodeUserId, $side) {
    // Walk up ancestors and increment side totals (lefttotal/righttotal)
    $temp_underuserid = $startNodeUserId;
    $temp_side = $side;
    while (!empty($temp_underuserid)) {
        $row = get_tree_row($pdo, $temp_underuserid);
        if (!$row) break;
        $colCount = $temp_side . 'total';
        // use null coalescing in case column absent
        $current = isset($row[$colCount]) ? (int)$row[$colCount] : 0;
        $current++;
        $pdo->prepare("UPDATE tree SET {$colCount} = ? WHERE userid = ?")->execute([$current, $temp_underuserid]);

        // get parent (who has this userid as left_id or right_id)
        $stmt = $pdo->prepare("SELECT userid FROM tree WHERE left_id = ? OR right_id = ? LIMIT 1");
        $stmt->execute([$temp_underuserid, $temp_underuserid]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);
        $temp_underuserid = $parent ? $parent['userid'] : '';
        // Decide next side based on how this node is placed under its parent
        if (!empty($temp_underuserid)) {
            $parentRow = get_tree_row($pdo, $temp_underuserid);
            if ($parentRow && isset($parentRow['left_id']) && $parentRow['left_id'] == $row['userid']) {
                $temp_side = 'left';
            } else {
                $temp_side = 'right';
            }
        }
    }
}

// Basic direct income distribution (simplified)
function distribute_direct_income($pdo, $sponsorId, $amount, $date, $time, $remark = 'Direct Income') {
    // Add a transaction record to sponsor
    $stmt = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, time, created_date, status, amount, final_amount) VALUES (?, 'CREDIT', ?, ?, ?, 1, ?, ?)");
    // For now final_amount same as amount; adapt according to logic
    return $stmt->execute([$sponsorId, $remark, $time, $date, $amount, $amount]);
}

function distribute_pair_income($pdo, $sponsorId, $amount, $date, $time, $remark = 'Pair Income') {
    // simplified pair income insertion
    $stmt = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, time, created_date, status, amount, final_amount) VALUES (?, 'CREDIT', ?, ?, ?, 1, ?, ?)");
    return $stmt->execute([$sponsorId, $remark, $time, $date, $amount, $amount]);
}



// Optional: a thin wrapper for sendsms() if available in db_method.php
function send_registration_sms($mobile, $message) {
    if (function_exists('sendsms')) {
        sendsms($mobile, $message);
        return true;
    }
    // fallback: nothing
    return false;
}

// ----------------- End helpers -----------------

// ---------- MAIN registration handling ----------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $sponserid = trim($_POST['refferalId'] ?? '');
    if (!$sponserid) {
        echo '<script>alert("Sponsor ID required"); window.location="new_binary_registration_form.php";</script>';
        exit;
    }
    // Provided sponsor id in format with prefix (e.g. "RH12345"), remove prefix digits if needed:
    $sponserid1 = preg_replace('/\D/', '', $sponserid); // digits only
    if (!$sponserid1) {
        echo '<script>alert("Invalid Sponsor ID format"); window.location="new_binary_registration_form.php";</script>';
        exit;
    }

    // generate unique userid
    $userid = random_userid();
    $try = 0;
    while (!is_userid_unique($pdo, $userid) && $try < 10) {
        $userid = random_userid();
        $try++;
    }
    if (!is_userid_unique($pdo, $userid)) {
        die("Unable to generate unique userid. Try again.");
    }

    $otpreg = rand(1000, 9999);
    $transaction_password = rand(100000, 999999);
    $name = trim($_POST['userName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pass1'] ?? '';
    $conpassword = $_POST['pass2'] ?? '';
    $mobilecode = trim($_POST['mobilecode'] ?? '');
    $mobilepart = trim($_POST['mobile'] ?? '');
    $mobile = $mobilecode . $mobilepart;
    $position = strtolower(trim($_POST['position'] ?? 'left')); // expected 'left' or 'right'
    $pin = trim($_POST['pin'] ?? '');

    // Additional optional fields
    $aadhar = trim($_POST['aadhar'] ?? '');
    $pan = trim($_POST['card_no'] ?? '');
    $profession = trim($_POST['profession'] ?? '');
    $marital_status = trim($_POST['marital_status'] ?? '');

    // Basic validations
    if (empty($name) || empty($email) || empty($password) || empty($conpassword) || empty($mobile) || empty($position)) {
        echo '<script>alert("Please fill required fields."); window.location="new_binary_registration_form.php";</script>';
        exit;
    }
    if ($password !== $conpassword) {
        echo '<script>alert("Password did not match"); window.location="new_binary_registration_form.php";</script>';
        exit;
    }
    if (!in_array($position, ['left', 'right'])) {
        echo '<script>alert("Invalid position selected"); window.location="new_binary_registration_form.php";</script>';
        exit;
    }
    // mobile length basic check - adjust if including country code
    if (strlen(preg_replace('/\D/', '', $mobile)) < 6) {
        echo '<script>alert("Invalid mobile number"); window.location="new_binary_registration_form.php";</script>';
        exit;
    }

    // Sponsor existence check
    if (!sponsor_exists($pdo, $sponserid1)) {
        echo '<script>alert("Invalid sponsor ID.");window.location = "new_binary_registration_form.php";</script>';
        exit;
    }

    // Check direct limits: left / right (only 1 direct each allowed) and max 2 directs
    $left_child = getmydirectidleft($sponserid1, $position);
    $right_child = getmydirectidright($sponserid1, $position);
    $total_directs = getmydirectid($pdo, $sponserid1);

    // if ($position === 'left' && $left_child > 0) {
    //     echo '<script>alert("You can register only 1 Left Direct"); window.location="new_binary_registration_form.php";</script>';
    //     exit;
    // }
    // if ($position === 'right' && $right_child > 0) {
    //     echo '<script>alert("You can register only 1 Right Direct"); window.location="new_binary_registration_form.php";</script>';
    //     exit;
    // }
    // if ($total_directs >= 2) {
    //     echo '<script>alert("You can do only 2 Direct"); window.location="new_binary_registration_form.php";</script>';
    //     exit;
    // }

    // PIN validation (if required)
    if (!empty($pin)) {
        if (!pin_valid_for_sponsor($pdo, $sponserid1, $pin)) {
            echo '<script>alert("Invalid Activation Pin"); window.location="new_binary_registration_form.php";</script>';
            exit;
        }
    }

    // Unique email/mobile checks
    // if (!check_email_unique($pdo, $email)) {
    //     echo '<script>alert("You Can Register Only 1 ID From Same Email"); window.location = "new_binary_registration_form.php";</script>';
    //     exit;
    // }
    if (!check_mobile_unique($pdo, $mobile)) {
        echo '<script>alert("You Can Register Only 1 ID From Same Mobile Number"); window.location = "new_binary_registration_form.php";</script>';
        exit;
    }

    // Determine underuserid: if sponsor preferred side empty then sponsor; else use find_parent logic
    $sponsorTree = get_tree_row($pdo, $sponserid1);
    if (!$sponsorTree) {
        // create sponsor tree row if absent
        $pdo->prepare("INSERT INTO tree (userid, created_at) VALUES (?, NOW())")->execute([$sponserid1]);
        $sponsorTree = get_tree_row($pdo, $sponserid1);
    }

    $mside_col = ($position === 'left') ? 'left_id' : 'right_id';
    $mside_val = $sponsorTree[$mside_col] ?? null;
    if (empty($mside_val) || $mside_val == 0) {
        $underuserid = $sponserid1;
    } else {
        $underuserid = find_parent_old_logic($pdo, $sponserid1, $position);
    }

    // Prepare to insert: start transaction
    try {
        $pdo->beginTransaction();

        // Insert sponsor mapping
        insertSponsor($pdo, $sponserid1, $userid, $date);

        // Place in tree (attempt)
        $placedSide = insertIntoTreeAndPlace($pdo, $sponserid1, $userid, $position, $underuserid);
        if ($placedSide === false) {
            $pdo->rollBack();
            echo '<script>alert("Unable to place user in the tree. Please try again.");window.location = "new_binary_registration_form.php";</script>';
            exit;
        }

        // Hash password (recommended)
        // $pass_hashed = password_hash($password, PASSWORD_DEFAULT);
        // Hash txn pass or store as numeric - here we keep plain txn but hashed would be better
        // $txn_hashed = password_hash($transaction_password, PASSWORD_DEFAULT);

        // Insert user record (columns must match your schema)
        // Map userData as per your user table structure
        $userInsertStmt = $pdo->prepare("INSERT INTO user (userid, name, mobile, email, pan, pass, txn_pass, sponserid, sponsername, underuserid, active, status, join_side, joining_date, plan, pin, kyc, time, father, country, amount, capping, otp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // sponsername fetch
        $sRow = get_user_by_userid($pdo, $sponserid1);
        $sponsername = $sRow['name'] ?? '';
        $params = [
            $userid, $name, $mobile, $email, $pan, $password, $transaction_password,
            $sponserid1, $sponsername, $underuserid, 0, 1, $placedSide, $date, '', $pin, 0, $time, '', '', 0,0, $otpreg
        ];
        $userInsertStmt->execute($params);

        // Insert KYC (minimal)
        insertKYC($pdo, $userid, $aadhar);

        // Update ancestors totals: Find the parent where this user was attached (the immediate parent)
        // Find parent that has this userid as left_id or right_id
        $stmt = $pdo->prepare("SELECT userid FROM tree WHERE left_id = ? OR right_id = ? LIMIT 1");
        $stmt->execute([$userid, $userid]);
        $parentRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $parentUserid = $parentRow['userid'] ?? null;
        if ($parentUserid) {
            // Update totals walking up from parent
            updateAncestorsTotals($pdo, $parentUserid, $placedSide);
        }

        // MARK: Distribute direct income to sponsor (example: 500)
        // $direct_income_amount = 500;
        // distribute_direct_income($pdo, $sponserid1, $direct_income_amount, $date, $time, 'Referral Income');

        // Pair income logic (simplified placeholder) - apply business logic as needed
        // Optionally call distribute_pair_income($pdo, $someUserId, $amount,...)

        // Mark used PIN as status=0 if pin used
        if (!empty($pin)) {
            $stmtPin = $pdo->prepare("UPDATE pin_list SET status = '0' WHERE userid = ? AND pin = ? LIMIT 1");
            $stmtPin->execute([$sponserid1, $pin]);
        }

        $pdo->commit();

        // After commit: send email & SMS
        $to = $email;
        $subject = $hmtitle . " Registration Successfully ";
        $headers = "From: " . strip_tags($hm_email) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $message = '<html><body>';
        $message .= '<table>';
        $message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>" . htmlspecialchars($name) . "</td></tr>";
        $message .= "<tr><td><strong>Email</strong></td><td>" . htmlspecialchars($email) . "</td></tr>";
        $message .= "<tr><td><strong>Password</strong></td><td>" . htmlspecialchars($password) . "</td></tr>";
        $message .= "<tr><td><strong>Transaction Password</strong></td><td>" . htmlspecialchars($transaction_password) . "</td></tr>";
        $message .= "</table></body></html>";
        @mail($to, $subject, $message, $headers);
        
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
        // SMS (if sendsms exists)
        $smsmsg = "Thank you for joining " . $hmtitle . ". UserId: " . $userid . " Password: " . $password . " " . $hmwebsite;
        send_registration_sms($mobile, $smsmsg);

        // Redirect to message page
        echo '<script>window.location = "message.php?msg=' . htmlspecialchars($userid) . '";</script>';
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Registration error: " . $e->getMessage());
        echo '<script>alert("Error during registration: ' . addslashes($e->getMessage()) . '"); window.location="new_binary_registration_form.php";</script>';
        exit;
    }
}
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
    <div class="card card-authentication1 mx-auto my-4">
      <div class="card-body">
        <div class="card-content p-2">
          <div class="text-center">
            <img src="assets/images/logo-icon.png" alt="logo icon">
          </div>
          <div class="card-title text-uppercase text-center py-3">Sign Up</div>
          <form action="add_user_binary_registration_form.php" method="post" id="registration_form">
            <!-- Referrer ID -->
            <div class="form-group">
              <label for="referrerId" class="sr-only"></label>
              <div class="position-relative has-icon-right">
                <input type="text" name="refferalId" id="referrerId" class="form-control input-shadow"
                    placeholder="Enter Referrer ID" value="<?php echo htmlspecialchars("DM".$_GET['sponsorid']); ?>" readonly required>
                <div class="form-control-position">
                  <i class="icon-link"></i>
                </div>
              </div>
            </div>

            <!-- Sponsor Name -->
            <div class="form-group" id="sponsor_name" style="display: none;">
              <div class="position-relative has-icon-right">
                <input type="text" name="refferalid" id="response2" class="form-control input-shadow" readonly>
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
            
            <!--Radio button--> 
            <div class="form-group">
                <div class="icheck-material-white">
                    <label>Position: </label> 
                    <label for="left">Left</label> 
                    <input type="radio" id="left" name="position" value="left" <?php echo ($_GET['type'] === 'left') ? 'checked' : 'disabled'; ?> required>
                    <label for="right">Right</label> 
                    <input type="radio" id="right" name="position" value="right" <?php echo ($_GET['type'] === 'right') ? 'checked' : 'disabled'; ?> ><br><br>
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
</html>