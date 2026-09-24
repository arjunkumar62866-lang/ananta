<?php
require_once 'common/connection.php'; 

if (!function_exists('newtime')) {
    function newtime($st)
    {
        // Set timezone if possible
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set("Asia/Kolkata");
        }

        $date = date('Y-m-d');
        $time = date('h:i a');

        return ($st === "time") ? $time : $date;
    }
}

if (!function_exists('getHomeSettings')) {
    function getHomeSettings($pdo) 
    {
        $sql = "SELECT * FROM tbl_homest LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'mobile'      => $row['mobile'] ?? '',
            'email'       => $row['email'] ?? '',
            'emailfrom'   => $row['emailfrom'] ?? '',
            'address'     => $row['address'] ?? '',
            'title'       => $row['title'] ?? '',
            'url'         => $row['url'] ?? '',
            'package'     => $row['package'] ?? '',
            'pre'         => $row['pre'] ?? '',
            'logo'        => $row['logo'] ?? '',
            'favicon'     => $row['favicon'] ?? '',
            'background'  => $row['background'] ?? '',
            'website'     => $row['website'] ?? '',
            'offer_image' => $row['offer_image'] ?? '',
            'color'       => $row['color'] ?? '',
            'coin'        => $row['coin'] ?? '',
            'currency'    => $row['currency'] ?? '',
            'bitly'       => $row['bitly'] ?? ''
        ];
    }

    return null;
    }
}


function loginAdmin($auserid, $password, $pdo)
{
    

    // Optimized query: only fetch required columns, use LIMIT 1
    $stmt = $pdo->prepare("
        SELECT auserid, pass
        FROM admin
        WHERE auserid = :auserid
        LIMIT 1
    ");
    $stmt->execute(['auserid' => $auserid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Compare passwords (plain-text for now to match old system)
    if ($user && $user['pass'] === $password) {
        $_SESSION['auserid'] = $user['auserid']; // match old system

        return [
            'status' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
    }

    return [
        'status' => false,
        'message' => 'Invalid User ID / Password'
    ];
}




function registerUser($referrer_id, $name, $email, $mobile, $password, $terms_accepted, $pdo)
{
    
    if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
        return [
            'status' => false,
            'message' => 'All fields are required!'
        ];
    }
  
    if (!$terms_accepted) {
        return [
            'status' => false,
            'message' => 'You must agree to the terms and conditions.'
        ];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        return [
            'status' => false,
            'message' => 'Email already registered!'
        ];
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO users (referrer_id, name, email, mobile, password, terms_accepted)
                           VALUES (:referrer_id, :name, :email, :mobile, :password, :terms_accepted)");

    $success = $stmt->execute([
        'referrer_id'    => $referrer_id,
        'name'           => $name,
        'email'          => $email,
        'mobile'         => $mobile,
        'password'       => $hashedPassword,
        'terms_accepted' => $terms_accepted
    ]);

    if ($success) {
        return [
            'status' => true,
            'message' => 'Registration successful!'
        ];
    } else {
        return [
            'status' => false,
            'message' => 'Registration failed. Try again.'
        ];
    }
}


function updatenonworkwallet($sponsorcode, $newamount,$pdo)
{
    $sql = "UPDATE user SET amount = amount + :newamount, total_inc=total_inc+:totalinc WHERE userid = :sponsorcode";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':newamount', $newamount, PDO::PARAM_STR);
    $stmt->bindParam(':totalinc', $newamount, PDO::PARAM_STR);
    $stmt->bindParam(':sponsorcode', $sponsorcode, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        // Update successful
        return true;
    } else {
        // Update failed
        return false;
    }
}


function insertSponsor($pdo, $sponsorId, $referralId, $createdDate) 
{
    $sql = "INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$sponsorId, $referralId, $createdDate]);
}

function insertUser($pdo, $data) 
{
    $sql = "INSERT INTO user (
        userid, name, mobile, email, pan, pass, txn_pass, sponserid, sponsername, underuserid,
        active, status, join_side, package, joining_date, plan, pin, kyc, club, upgrade_date,
        time, country, amount, capping, rank, closingdate, country_code, level, atime, pool,
        state, father, gender, pin_code, address, otp, coin_wallet
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?
    )";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

function insertKYC($pdo, $userId, $aadhar) {
    $sql = "INSERT INTO kyc (
        userid, holder_name, ac_number, bank, branch, ifsc, paytm, phone_pe, bhim,
        idproof, card_no, adhar_front_img, adhar_back_img, pan, pan_img, status
    ) VALUES (
        ?, '', '', '', '', '', '', '', '', '', ?, '', '', '', '', '0'
    )";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId, $aadhar]);
}

function insertUserLevel($pdo, $sponsorId, $userId, $level) 
{
    $sql = "INSERT INTO user_level (sponsorid, downid, level) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$sponsorId, $userId, $level]);
}



function getMetaInfo($pdo)
{
    $sql = "SELECT title, logo, favicon FROM meta_info LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'status'  => true,
            'title'   => $row['title'],
            'logo'    => $row['logo'],
            'favicon' => $row['favicon']
        ];
    } else {
        return [
            'status' => false,
            'message' => 'No meta information found.'
        ];
    }
}

function userid($userid) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = ?");
    $stmt->execute([$userid]);

    if ($stmt->rowCount() > 0) {
        return 1;
    } else {
        return 0;
    }
}

function getalluserpackage(PDO $pdo)
{
    $stmt = $pdo->query("SELECT SUM(total_package) AS total FROM user");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total'] ?? 0;
}



function getuserdatabysponserid($userid)
{ 
    global $pdo;
    $sql = "SELECT * FROM user WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            "id" => $rowuser['id'],
            "name" => $rowuser["name"],
            "mobile" => $rowuser["mobile"],
            "email" => $rowuser["email"],
            "pass" => $rowuser["pass"],
            "txn_pass" => $rowuser["txn_pass"],
            "amount" => $rowuser["amount"],
            "userid" => $rowuser["userid"],
            "status" => $rowuser["status"],
            "pool" => $rowuser["pool"],
            "sponsername" => $rowuser["sponsername"],
            "sponserid" => $rowuser["sponserid"],
            "total_package" => $rowuser["total_package"],
            "idactive" => $rowuser["active"],
            "upgrade_date" => $rowuser["upgrade_date"],
            "joining_date" => $rowuser["joining_date"],
            "inc_limit" => $rowuser["inc_limit"],
            "total_inc" => $rowuser["total_inc"]
        ];

        
    }

    return null;
}

function checkuseridregister($mysponsernew)
{
    global $pdo;
    $sql = "SELECT * FROM user WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $mysponsernew, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return 1;
    } else {
        return 0;
    }
}


function getmydirectactiveright($sponsorId)
{
    global $pdo;

    $sql = "
        SELECT COUNT(DISTINCT u.userid) AS total
        FROM user u
        INNER JOIN (
            SELECT downline_id 
            FROM tbl_userlevel_b 
            WHERE sponser_id = :sponsor_id
        ) d ON d.downline_id = u.userid
        WHERE u.active = 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponsor_id', $sponsorId, PDO::PARAM_INT);
    $stmt->execute();

    return (int)$stmt->fetchColumn();
}
function getmydirectactiveleft($sponsorId)
{
    global $pdo;

    $sql = "
        SELECT COUNT(DISTINCT u.userid) AS total
        FROM user u
        INNER JOIN (
            SELECT downline_id 
            FROM tbl_userlevel_a 
            WHERE sponser_id = :sponsor_id
        ) d ON d.downline_id = u.userid
        WHERE u.active = 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponsor_id', $sponsorId, PDO::PARAM_INT);
    $stmt->execute();

    return (int)$stmt->fetchColumn();
}


function getmysponserid($userid)
{
    global $pdo; 
    $sql = "SELECT sponsor_id FROM tbl_sponsor WHERE referral_id = :referral_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':referral_id', $userid, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['sponsor_id'];
    }

    return null; // If no sponsor found
}


function getmydirectactive($direct)
{
    global $pdo;
    $sql = "SELECT COUNT(*) as alluser 
            FROM user tbsign  
            INNER JOIN tbl_sponsor tbspon 
            ON tbspon.referral_id = tbsign.userid  
            WHERE tbspon.sponsor_id = :sponsor_id 
            AND tbsign.active = '1'";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponsor_id', $direct, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $rowac = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rowac['alluser'];
    }

    return 0; // If no records found
}

function insert_userlevel($sponserid, $downlineid, $level)
{
    global $pdo;

    $sql = "INSERT INTO tbl_userlevel (sponser_id, downline_id, level, date) 
            VALUES (:sponser_id, :downline_id, :level, CURDATE())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponser_id', $sponserid, PDO::PARAM_STR);
    $stmt->bindParam(':downline_id', $downlineid, PDO::PARAM_STR);
    $stmt->bindParam(':level', $level, PDO::PARAM_INT);

    $stmt->execute();
}

function incometotalnew($pdo, $table, $subject)
{
    $sql = "SELECT SUM(amount) AS totalamount 
            FROM {$table} 
            WHERE subject LIKE :subject";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':subject' => "%{$subject}%"
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $row['totalamount'] : 0;
}



function incometotalnewdate($date, $table, $arg3, $arg4 = null)
{
    global $pdo;

    if ($arg4 === null) {
        // 3 arguments passed: ($date, $table, $subject)
        $subject = $arg3;
        $sql = "SELECT SUM(amount) AS totalamount 
                FROM {$table} 
                WHERE created_date = :date 
                AND subject LIKE :subject";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':date' => $date,
            ':subject' => "%{$subject}%"
        ]);
    } else {
        // 4 arguments passed: ($date, $table, $userid, $subject)
        $userid = $arg3;
        $subject = $arg4;
        $sql = "SELECT SUM(amount) AS totalamount 
                FROM {$table} 
                WHERE created_date = :date 
                AND user_id = :userid 
                AND subject LIKE :subject";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':date' => $date,
            ':userid' => $userid,
            ':subject' => "%{$subject}%"
        ]);
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? ($row['totalamount'] ?? 0) : 0;
}


function getlevelDirectbusiness($userid, $level)
{
    global $pdo; // Assuming $pdo is your PDO connection
    $sqlac = "SELECT SUM(total_package) as totalbusiness  
              FROM user 
              WHERE sponserid = '$userid'";

    $resultac = $pdo->query($sqlac);

    if ($resultac->rowCount() > 0) {
        while ($rowac = $resultac->fetch(PDO::FETCH_ASSOC)) {
            $alluser = $rowac['totalbusiness'];
            return $alluser;
        }
    }
}

// function getlevelbusiness_left($userid, $level)
// {
//     global $pdo; // Assuming $pdo is your PDO connection
//     $sqlac = "SELECT SUM(total_package) as totalbusiness  
//               FROM tbl_userlevel_a tblevel  
//               INNER JOIN user tbuser ON tblevel.downline_id = tbuser.userid  
//               WHERE tblevel.sponser_id = '$userid' 
//               AND tblevel.level = '$level'";

//     $resultac = $pdo->query($sqlac);

//     if ($resultac->rowCount() > 0) {
//         while ($rowac = $resultac->fetch(PDO::FETCH_ASSOC)) {
//             $alluser = $rowac['totalbusiness'];
//             return $alluser;
//         }
//     }
// }
function getlevelbusiness_left($userid, $level)
{
    global $pdo;

    $sql = "
        SELECT SUM(u.total_package) AS leftbusiness
        FROM user u
        INNER JOIN tbl_userlevel_a t 
            ON t.downline_id = u.userid
        WHERE t.sponser_id = :userid 
          AND t.level = :level
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindValue(':level', $level, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() ?: 0;
}
function getlevelbusiness_right($userid, $level)
{
    global $pdo;

    $sql = "
        SELECT SUM(u.total_package) AS rightbusiness
        FROM user u
        INNER JOIN tbl_userlevel_b t 
            ON t.downline_id = u.userid
        WHERE t.sponser_id = :userid 
          AND t.level = :level
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindValue(':level', $level, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() ?: 0;
}

function gettotallevelbusiness_left($userid)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $totallevelbusiness =
        getlevelbusiness_left($userid, 1) +
        getlevelbusiness_left($userid, 2) +
        getlevelbusiness_left($userid, 3) +
        getlevelbusiness_left($userid, 4) +
        getlevelbusiness_left($userid, 5) +
        getlevelbusiness_left($userid, 6) +
        getlevelbusiness_left($userid, 7) +
        getlevelbusiness_left($userid, 8) +
        getlevelbusiness_left($userid, 9) +
        getlevelbusiness_left($userid, 10) +
        getlevelbusiness_left($userid, 11) +
        getlevelbusiness_left($userid, 12) +
        getlevelbusiness_left($userid, 13) +
        getlevelbusiness_left($userid, 14) +
        getlevelbusiness_left($userid, 15) +
        getlevelbusiness_left($userid, 16) +
        getlevelbusiness_left($userid, 17) +
        getlevelbusiness_left($userid, 18) +
        getlevelbusiness_left($userid, 19) +
        getlevelbusiness_left($userid, 20);

    return $totallevelbusiness;
}
function gettotallevelbusiness_right($userid)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $totallevelbusiness =
        getlevelbusiness_right($userid, 1) +
        getlevelbusiness_right($userid, 2) +
        getlevelbusiness_right($userid, 3) +
        getlevelbusiness_right($userid, 4) +
        getlevelbusiness_right($userid, 5) +
        getlevelbusiness_right($userid, 6) +
        getlevelbusiness_right($userid, 7) +
        getlevelbusiness_right($userid, 8) +
        getlevelbusiness_right($userid, 9) +
        getlevelbusiness_right($userid, 10) +
        getlevelbusiness_right($userid, 11) +
        getlevelbusiness_right($userid, 12) +
        getlevelbusiness_right($userid, 13) +
        getlevelbusiness_right($userid, 14) +
        getlevelbusiness_right($userid, 15) +
        getlevelbusiness_right($userid, 16) +
        getlevelbusiness_right($userid, 17) +
        getlevelbusiness_right($userid, 18) +
        getlevelbusiness_right($userid, 19) +
        getlevelbusiness_right($userid, 20);

    return $totallevelbusiness;
}

function getnews()
{
    global $pdo; 

    $sqluser = "SELECT * FROM tbl_news WHERE id = '1'";
    $resultuser = $pdo->query($sqluser);

    if ($resultuser->rowCount() > 0) {
        while ($rowuser = $resultuser->fetch(PDO::FETCH_ASSOC)) {
            $newsdata = array(
                "news" => $rowuser['news'],
            );
            return $newsdata;
        }
    }
}

function webtistime()
{
    if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
     }


return date('h:i a');
}

function webtisdate()
{
    if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
   }


return date('Y-m-d');
}

function getuserdatabyid($userid)
{
    global $pdo;

    $sql = "SELECT * FROM user WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

        $userdata = array(
            "signup_id"     => $rowuser['signup_id'],
            "name"          => $rowuser["name"],
            "mobile"        => $rowuser["mobile"],
            "type"          => $rowuser["type"],
            "password"      => $rowuser["password"],
            "wallet_amount" => $rowuser["wallet_amount"],
            "sponsor_code"  => $rowuser["sponsor_code"],
            "status"        => $rowuser["status"],
            "active_date"   => $rowuser["active_date"],
            "created_date"  => $rowuser["created_date"],
        );

        return $userdata;
    }

    return null; 
}


function paybinaycloing($user_sponsor_code, $weekly_earning, $record_id)
{
    global $pdo;

    try {
        $getdate = date("Y-m-d");
        $time = newtime("time");

        // Check pending income and user details
        $userfinalincome = checkpedningincome($user_sponsor_code, 'thursday', $getdate);
        $sponserdetails = getuserdatabysponserid($user_sponsor_code);
        $userpackage = isset($sponserdetails['capping']) ? $sponserdetails['capping'] : 9999999999999999999999999999999;

        if ($userfinalincome >= $userpackage) {
            $messagenew = "Matching Income";
            $cdtype = "Credit";

            if (!empty($user_sponsor_code) && $userpackage > 0) {
                updateuseramount($userpackage, $user_sponsor_code);
                updateuserincome($user_sponsor_code, $getdate);
                insert_transction($user_sponsor_code, $userpackage, $messagenew, $time, $cdtype);
            }
        } else {
            if (!empty($user_sponsor_code) && $userfinalincome > 0) {
                $messagenew = "Matching Income";
                $cdtype = "Credit";

                updateuseramount($userfinalincome, $user_sponsor_code);
                updateuserincome($user_sponsor_code, $getdate);
                insert_transction($user_sponsor_code, $userfinalincome, $messagenew, $time, $cdtype);
            }
        }

        // ✅ Mark record as paid
        $updateStmt = $pdo->prepare("UPDATE tbl_temp_data SET paid_date = :paid_date WHERE id = :id");
        $updateStmt->execute([
            'paid_date' => $getdate,
            'id' => $record_id
        ]);

    } catch (PDOException $e) {
        error_log("Error in paybinaycloing(): " . $e->getMessage());
    }
}


function insert_transction($table, $userid, $amount, $transaction_type, $time, $cdtype)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $stmt = $pdo->prepare("
        INSERT INTO $table 
            (name, user_id, subject, type, amount, status, a_status, time, created_date, wallet_type, beneficiary_id, api_status, api_txn_no, api_bank_ref_no, api_message)
        VALUES 
            ('', :userid, :subject, :type, :amount, 1, 0, :time, CURDATE(), '', '', '', '', '', '')
    ");

    $stmt->execute([
        ':userid'  => $userid,
        ':subject' => $transaction_type,
        ':type'    => $cdtype,
        ':amount'  => $amount,
        ':time' =>$time
    ]);
}


function updateuserincome($sponserid, $ytdate)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $stmt = $pdo->prepare("UPDATE tbl_temp_data SET status = 1, paid_date = :ytdate WHERE user_id = :sponserid");
    $stmt->execute([
        ':ytdate'     => $ytdate,
        ':sponserid'  => $sponserid
    ]);
}


function updateuseramount($newamount, $sponsorcode)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $stmt = $pdo->prepare("UPDATE user SET amount = amount + :newamount WHERE userid = :sponsorcode");
    $stmt->execute([
        ':newamount'   => $newamount,
        ':sponsorcode' => $sponsorcode
    ]);
}

function checkpedningincome($userid, $day, $getdate)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $stmt = $pdo->prepare("SELECT SUM(weekly_earning) AS totalamount FROM tbl_temp_data WHERE user_id = :userid AND status = :status");
    $stmt->execute([
        'userid' => $userid,
        'status' => 0
    ]);

    $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowuser && isset($rowuser['totalamount'])) {
        return $rowuser['totalamount'];
    }

    return 0; // Return 0 if no pending income
}


function repurchasecheckpedningincome($userid, $day, $getdate)
{
    global $pdo; // Use PDO connection

    $stmt = $pdo->prepare("
        SELECT SUM(weekly_earning) AS totalamount 
        FROM tbl_repurchase_data 
        WHERE user_id = :userid AND status = '0'
    ");
    
    $stmt->execute([':userid' => $userid]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['totalamount'] !== null) {
        return $row['totalamount'];
    } else {
        return 0;
    }
}


function repurchaseupdateuseramount($newamount, $sponsorcode)
{
    global $pdo; // Use your PDO connection variable

    $stmt = $pdo->prepare("
        UPDATE user 
        SET amount = amount + :newamount 
        WHERE userid = :sponsorcode
    ");

    $stmt->execute([
        ':newamount' => $newamount,
        ':sponsorcode' => $sponsorcode
    ]);
}



function repurchaseupdateuserincome($sponserid, $ytdate)
{
    global $pdo; // your PDO connection

    $stmt = $pdo->prepare("
        UPDATE tbl_repurchase_data 
        SET status = 1, paid_date = :ytdate 
        WHERE user_id = :sponserid
    ");

    $stmt->execute([
        ':ytdate' => $ytdate,
        ':sponserid' => $sponserid
    ]);
}



function repurchasecheckcloing( $ytdate)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS alluser FROM tbl_repurchase_data WHERE paid_date = :paid_date AND status = :status");
    $stmt->execute([
        ':paid_date' => $ytdate,
        ':status' => 1
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['alluser'] : 0;
}


function repurchasepaybinaycloing()
{
    global $pdo;
    $getdate = newtime("date");
    $time = newtime("time");

    // ✅ Fetch all users with status = 0
    $stmt = $pdo->prepare("SELECT * FROM tbl_repurchase_data WHERE status = :status");
    $stmt->execute([':status' => 0]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        foreach ($rows as $rowuser) {

            $updated_date = $rowuser['paid_date'];

            // ✅ Process only if paid_date != today's date
            if ($updated_date !== $getdate) {

                $id = $rowuser['id'];
                $userid = $rowuser['user_id'];
                $amount = $rowuser['weekly_earning']; // previously undefined `$getincomedata['weekly_earning']`
                $user_sponsor_code = $userid;

                // ✅ Get pending income
                $userfinalincome = repurchasecheckpedningincome($user_sponsor_code, 'Friday', $getdate);

                // ✅ Get sponsor details
                $sponserdetails = getuserdatabysponserid($user_sponsor_code);
                $userpackage = $sponserdetails['capping'];

                // ✅ Compare income with package
                if ($userfinalincome >= $userpackage) {
                    $messagenew = "Repurchase Income";
                    $cdtype = "Credit";

                    if (!empty($user_sponsor_code) && !empty($userpackage)) {

                        repurchaseupdateuseramount($userpackage, $user_sponsor_code);
                        repurchaseupdateuserincome($user_sponsor_code, $getdate);
                        insert_transction($user_sponsor_code, $userpackage, $messagenew, $time, $cdtype);

                        // ✅ (Optional) Level income logic commented intentionally for clarity
                        // You can re-enable that loop here if you wish.
                    }
                } 
                else {
                    // ✅ If income < package
                    if (!empty($user_sponsor_code) && !empty($userfinalincome)) {

                        $messagenew = "Repurchase Income";
                        $cdtype = "Credit";

                        repurchaseupdateuseramount($userfinalincome, $user_sponsor_code);
                        repurchaseupdateuserincome($user_sponsor_code, $getdate);
                        insert_transction($user_sponsor_code, $userfinalincome, $messagenew, $time, $cdtype);

                        
                    }
                }
            }
        }
    }
}

function updatedatabysponserid1($userid, $transactionamounta, $transactionamountb)
{
    global $pdo;

    $sqluser = "UPDATE `user` 
                SET `profit_sharing_wallet` = `profit_sharing_wallet` + :transactionamounta
                WHERE userid = :userid";

    $stmt = $pdo->prepare($sqluser);

    $stmt->bindParam(':transactionamounta', $transactionamounta);
    $stmt->bindParam(':userid', $userid);

    $stmt->execute();
}
function updatedatabysponserid($userid, $transactionamounta, $transactionamountb)
{
    global $pdo;

    $sqluser = "UPDATE `user` 
                SET `amount` = `amount` + :transactionamounta, 
                    total_inc = total_inc + :transactionamountb 
                WHERE userid = :userid";

    $stmt = $pdo->prepare($sqluser);

    $stmt->bindParam(':transactionamounta', $transactionamounta);
    $stmt->bindParam(':transactionamountb', $transactionamountb);
    $stmt->bindParam(':userid', $userid);

    if ($stmt->execute()) {

    }
}


function getroidatabysponserid($userid)
{
    global $pdo;

    $sqluser = "SELECT * FROM tbl_roi_one WHERE id = :userid";
    $stmt = $pdo->prepare($sqluser);
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    if ($stmt->rowCount() > 0);
    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $userdata = array(
            "id" => $rowuser['id'],
            "amount" => $rowuser["amount"],
            "capping" => $rowuser["capping"],
            "percentage" => $rowuser["percentage"],
        );

        return $userdata;
    }
}



/** ROI One Plan **/
function pay_roi_one_income($sponserid, $package, $roipercentage, $newid, $userlevelid, $closing_month = null)
{

    global $pdo;

    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set("Asia/Kolkata");
    }

    $time = date('h:i a');
    $date = date('Y-m-d');

    if (empty($closing_month)) {
        $closing_month = date('Y-m');
    }

    $sponserdetails = getroidatabysponserid($newid);
    $income_limit = isset($sponserdetails['capping']) ? $sponserdetails['capping'] : 999999999;
    $total_income = isset($sponserdetails['amount']) ? $sponserdetails['amount'] : 0;

    // $newicome = getpercent($package, $roipercentage);
    $newicome = $package;

    $check_amt = (int)$total_income + (int)$newicome;

    if ($total_income >= $income_limit) {

        $stmt = $pdo->prepare("UPDATE tbl_roi_one SET status='1' WHERE id=?");
        $stmt->execute([$userlevelid]);
    }

    else {

        if (1==1) {

            $trasction_type = "Daily Profit Sharing Income";
            $cdtype = "Credit";

            $price = $newicome;
            $pinfinal = $sponserid;
            $activateuserid = $sponserid;
            $packageid = $newicome;
            $userid = $sponserid;
            $pinfinal = $userid;

            // Prepare statements for idempotency check and insertion
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM tbl_daily_levelinc 
                WHERE source_investment_id = :source_id 
                  AND closing_month = :closing_month 
                  AND user_id = :recipient_id 
                  AND level_num = :level_num
            ");

            $insTxnStmt = $pdo->prepare("
                INSERT INTO tbl_daily_levelinc 
                    (name, user_id, source_investment_id, closing_month, level_num, subject, type, amount, status, a_status, time, created_date, wallet_type, beneficiary_id, api_status, api_txn_no, api_bank_ref_no, api_message)
                VALUES 
                    ('', :user_id, :source_id, :closing_month, :level_num, :subject, 'Credit', :amount, 1, 0, :time, CURDATE(), '', '', '', '', '', '')
            ");

            for ($i = 0; $i < 15; $i++) {

                $mysponserid = getmysponserid($pinfinal);
                if (empty($mysponserid)) {
                    break;
                }
                $sponserdetails = getuserdatabysponserid($mysponserid);

                if ($pinfinal !== '1290') {

                    $spcode1 = $sponserdetails['userid'];
                    $spamont = $sponserdetails['amount'];
                    $isidactive = $sponserdetails['idactive'];

                    $directactive = getmydirectactive($spcode1);

                    if ($isidactive == 1) {
                        $level_num = $i + 1;
                        $transactionamount = 0;
                        $eligible = false;

                        if ($i == 0 && $directactive >= 0) {
                            $transactionamount = (float)$price * 15.0 / 100.0;
                            $eligible = true;
                        } else if ($i == 1 && $directactive >= 2) {
                            $transactionamount = (float)$price * 7.0 / 100.0;
                            $eligible = true;
                        } else if ($i == 2 && $directactive >= 3) {
                            $transactionamount = (float)$price * 5.0 / 100.0;
                            $eligible = true;
                        } else if ($i == 3 && $directactive >= 4) {
                            $transactionamount = (float)$price * 3.0 / 100.0;
                            $eligible = true;
                        } else if ($i == 4 && $directactive >= 5) {
                            $transactionamount = (float)$price * 2.0 / 100.0;
                            $eligible = true;
                        } else if ($i == 5 && $directactive >= 6) {
                            $transactionamount = (float)$price * 1.0 / 100.0;
                            $eligible = true;
                        } else if ($i == 6 && $directactive >= 7) {
                            $transactionamount = (float)$price * 0.75 / 100.0;
                            $eligible = true;
                        } else if ($i == 7 && $directactive >= 8) {
                            $transactionamount = (float)$price * 0.50 / 100.0;
                            $eligible = true;
                        } else if ($i == 8 && $directactive >= 9) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        } else if ($i == 9 && $directactive >= 10) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        } else if ($i == 10 && $directactive >= 11) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        } else if ($i == 11 && $directactive >= 12) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        } else if ($i == 12 && $directactive >= 13) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        } else if ($i == 13 && $directactive >= 14) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        } else if ($i == 14 && $directactive >= 15) {
                            $transactionamount = (float)$price * 0.25 / 100.0;
                            $eligible = true;
                        }

                        if ($eligible && $transactionamount > 0) {
                            // Idempotency Check: Check if this exact payout already exists
                            $checkStmt->execute([
                                ':source_id' => $newid,
                                ':closing_month' => $closing_month,
                                ':recipient_id' => $spcode1,
                                ':level_num' => $level_num
                            ]);
                            $exists = (int)$checkStmt->fetchColumn();

                            if ($exists === 0) {
                                $messagenew = "Profit Sharing Income on Level-{$level_num}.of Id ({$userid})";
                                
                                // Credit dedicated Profit Sharing Wallet
                                updatedatabysponserid1($spcode1, $transactionamount, $transactionamount);

                                // Insert authoritative transaction record with composite unique keys
                                try {
                                    $insTxnStmt->execute([
                                        ':user_id' => $spcode1,
                                        ':source_id' => $newid,
                                        ':closing_month' => $closing_month,
                                        ':level_num' => $level_num,
                                        ':subject' => $messagenew,
                                        ':amount' => $transactionamount,
                                        ':time' => $time
                                    ]);
                                } catch (PDOException $ex) {
                                    // Duplicate key catch guard: If database unique index prevents duplicate, safely ignore
                                    if ($ex->getCode() !== '23000') {
                                        throw $ex;
                                    }
                                }
                            }
                        }
                    }

                    $pinfinal = $spcode1;
                }

                else {
                    $pinfinal = $spcode1;
                }
            }
        }
    }
}
function manual_pay_roi_one_income($uid)
{
    global $pdo;

    date_default_timezone_set("Asia/Kolkata");
    $date = date('Y-m-d');

    // First get pending income
    $stmt = $pdo->prepare("SELECT pending_geninc FROM user WHERE userid = ?");
    $stmt->execute([$uid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['pending_geninc'] <= 0) {
        return false;
    }

    $income = $row['pending_geninc'];

    // Update user
    $sqluser = "UPDATE user 
                SET amount = amount + pending_geninc,
                    total_inc = total_inc + pending_geninc,
                    pending_geninc = 0
                WHERE userid = ?";

    $stmt = $pdo->prepare($sqluser);

    if ($stmt->execute([$uid])) {

        $insert = $pdo->prepare("
            INSERT INTO tbl_transaction 
                (user_id, type, subject, amount, created_date, status)
            VALUES 
                (:user_id, 'Pending Generation Income', 
                 'Pending Generation Income Payout', 
                 :amount, :date, '1')
        ");

        $insert->execute([
            ':user_id' => $uid,
            ':amount'  => $income,
            ':date'    => $date
        ]);
    }
}


function getpercent($amount, $percent)
{
    $per_amount = $amount * $percent / 100;
    return $per_amount;
}

function getroipercentage()
{
    global $pdo;

    $sql2 = "SELECT * FROM tbl_roipercentage";
    $stmt = $pdo->query($sql2);

    while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $roiset = array(
            "id" => $row2['id'],
            "percentage" => $row2['percentage'],
            "level_percentage" => $row2['level_percentage'],
            "status" => $row2['status']
        );

        return $roiset;
    }
}

/** ROI One Plan **/



function checkuserid(PDO $pdo, $mysponsernew)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS alluser FROM user WHERE userid = :userid");
    $stmt->execute([':userid' => $mysponsernew]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $row['alluser'] : 0;
}

function getproduct()
{
    global $pdo; // Make sure your PDO connection is in $pdo

    $stmt = $pdo->prepare("SELECT * FROM tbl_product WHERE status = 1");
    $stmt->execute();
    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row1) {
        $productdata = [
            "name"     => $row1['name'],
            "image"    => $row1['image'],
            "quantity" => $row1['quantity'],
            "pv"       => $row1['pv'],
            "price"    => $row1['price'],
            "mrp"      => $row1['mrp']
        ];
        return $productdata;
    }

    return null; // Return null if no product found
}
function gettransactiondata($userid)
{
    global $pdo; // Make sure your PDO connection is in $pdo

    $stmt = $pdo->prepare("SELECT * FROM tbl_transaction_details WHERE tr_id = :tr_id LIMIT 1");
    $stmt->execute(['tr_id' => $userid]);
    $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowuser) {
        $userdata = [
            "id" => $rowuser['id'],
            "pro_code" => $rowuser['pro_code'],
        ];
        return $userdata;
    }

    return null; // return null if no transaction found
}



function checkcloing($ytdate)
{
    global $pdo;
    // Prepare SQL query with a placeholder
    $sql = "SELECT COUNT(*) AS alluser 
            FROM tbl_temp_data 
            WHERE paid_date = :ytdate 
              AND status = '1'";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind parameter safely
    $stmt->bindParam(':ytdate', $ytdate, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the count or 0 if not found
    return $row ? $row['alluser'] : 0;
}


/****************========================================
 * REQUIREMENT #12 — DIRECT BONUS SYSTEM HELPERS
 * ========================================================*/

if (!defined('DIRECT_BONUS_PERCENT')) {
    define('DIRECT_BONUS_PERCENT', 6.0);
}
if (!defined('DIRECT_BONUS_MONTHS')) {
    define('DIRECT_BONUS_MONTHS', 10);
}
if (!defined('MIN_QUALIFIED_INVESTMENT')) {
    define('MIN_QUALIFIED_INVESTMENT', 13000.0);
}
if (!defined('REQUIRED_QUALIFIED_DIRECTS')) {
    define('REQUIRED_QUALIFIED_DIRECTS', 2);
}

/**
 * Get count of Qualified Directs for a user.
 * A Qualified Direct is a direct referral who:
 * 1. Has active access / user.active = '1'
 * 2. Has total active investment (SUM of tbl_roi_one packages) >= ₹13,000
 */
function getQualifiedDirectCount($userid, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$userid) return 0;

    $sql = "SELECT s.referral_id
            FROM tbl_sponsor s
            INNER JOIN user u ON u.userid = s.referral_id
            INNER JOIN tbl_roi_one r ON r.user_id = u.userid
            WHERE s.sponsor_id = :sponsor_id
              AND u.active = '1'
            GROUP BY s.referral_id
            HAVING SUM(r.package) >= :min_inv";

    $stmt = $db->prepare($sql);
    $minInv = MIN_QUALIFIED_INVESTMENT;
    $stmt->bindParam(':sponsor_id', $userid, PDO::PARAM_STR);
    $stmt->bindParam(':min_inv', $minInv);
    $stmt->execute();
    
    return $stmt->rowCount();
}

/**
 * Generate 10-month Direct Bonus Schedule for an eligible investment.
 * Direct Bonus = Eligible Investment * 6% divided into 10 monthly installments.
 * Only generated if investment >= ₹13,000 and user has active access.
 */
function generateDirectBonusSchedule($investment_id, $source_user_id, $investment_amount, $investment_date = null, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$investment_id || !$source_user_id || (float)$investment_amount < MIN_QUALIFIED_INVESTMENT) {
        return false;
    }

    // Find direct sponsor
    $stmtSpon = $db->prepare("SELECT sponsor_id FROM tbl_sponsor WHERE referral_id = :ref_id LIMIT 1");
    $stmtSpon->execute([':ref_id' => $source_user_id]);
    $beneficiary_id = $stmtSpon->fetchColumn();

    if (!$beneficiary_id) {
        return false; // No sponsor found
    }

    $investment_amount = (float)$investment_amount;
    $total_bonus = round($investment_amount * (DIRECT_BONUS_PERCENT / 100.0), 2);
    
    // Calculate monthly installment with rounding safety
    $base_installment = floor(($total_bonus / DIRECT_BONUS_MONTHS) * 100) / 100;
    $remainder = round($total_bonus - ($base_installment * DIRECT_BONUS_MONTHS), 2);

    $invDateObj = $investment_date ? new DateTime($investment_date) : new DateTime();
    
    // Check if schedule already exists for this investment to prevent duplicates
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM tbl_direct_bonus_schedule WHERE investment_id = :inv_id");
    $checkStmt->execute([':inv_id' => $investment_id]);
    if ($checkStmt->fetchColumn() > 0) {
        return true; // Already generated
    }

    $insertStmt = $db->prepare("
        INSERT INTO tbl_direct_bonus_schedule
        (investment_id, beneficiary_id, source_user_id, investment_amount, total_bonus, installment_amount, installment_number, installment_month, status)
        VALUES
        (:investment_id, :beneficiary_id, :source_user_id, :investment_amount, :total_bonus, :installment_amount, :installment_number, :installment_month, 'PENDING')
    ");

    for ($i = 1; $i <= DIRECT_BONUS_MONTHS; $i++) {
        $monthDate = clone $invDateObj;
        $monthDate->modify("+" . ($i - 1) . " month");
        $installment_month = $monthDate->format('Y-m');

        // Add remainder paise to final installment if needed
        $inst_amount = ($i == DIRECT_BONUS_MONTHS) ? round($base_installment + $remainder, 2) : $base_installment;

        $insertStmt->execute([
            ':investment_id'     => $investment_id,
            ':beneficiary_id'    => $beneficiary_id,
            ':source_user_id'   => $source_user_id,
            ':investment_amount' => $investment_amount,
            ':total_bonus'       => $total_bonus,
            ':installment_amount'=> $inst_amount,
            ':installment_number'=> $i,
            ':installment_month' => $installment_month
        ]);
    }

    return true;
}

/**
 * Process Direct Bonus Monthly Closing Installments.
 * Executed during admin Monthly Profit Closing.
 */
function processDirectBonusInstallments($closing_month, $closing_date = null, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$closing_month) {
        return ['processed' => 0, 'total_paid' => 0.0, 'eligible_users' => 0];
    }

    $cDate = $closing_date ?: date('Y-m-d');

    // Fetch all pending installments for or up to the target closing_month
    $stmt = $db->prepare("
        SELECT * FROM tbl_direct_bonus_schedule
        WHERE status = 'PENDING'
          AND installment_month <= :closing_month
        FOR UPDATE
    ");
    $stmt->execute([':closing_month' => $closing_month]);
    $pendingInstallments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $processedCount = 0;
    $totalPaid = 0.0;
    $beneficiariesPaid = [];

    $updSchedule = $db->prepare("
        UPDATE tbl_direct_bonus_schedule
        SET status = 'CREDITED', credited_at = NOW(), closing_id = :closing_month
        WHERE id = :id AND status = 'PENDING'
    ");

    $updWallet = $db->prepare("
        UPDATE user
        SET direct_bonus_wallet = direct_bonus_wallet + :amount
        WHERE userid = :userid
    ");

    $insTxn = $db->prepare("
        INSERT INTO tbl_transaction
        (user_id, type, subject, amount, created_date, status)
        VALUES
        (:user_id, 'Credit', :subject, :amount, :created_date, 1)
    ");

    foreach ($pendingInstallments as $inst) {
        $benId = $inst['beneficiary_id'];
        $qCount = getQualifiedDirectCount($benId, $db);

        // Requirement #3 & #8: Credit only if qualified directs >= 2
        if ($qCount >= REQUIRED_QUALIFIED_DIRECTS) {
            $amount = (float)$inst['installment_amount'];
            
            // 1. Update schedule status
            $updSchedule->execute([
                ':closing_month' => $closing_month,
                ':id'            => $inst['id']
            ]);

            if ($updSchedule->rowCount() > 0) {
                // 2. Credit direct_bonus_wallet
                $updWallet->execute([
                    ':amount' => $amount,
                    ':userid' => $benId
                ]);

                // 3. Create transaction entry
                $subject = "Direct Bonus Installment " . $inst['installment_number'] . "/10 - Investment #" . $inst['investment_id'] . " - " . $inst['installment_month'];
                $insTxn->execute([
                    ':user_id'      => $benId,
                    ':subject'      => $subject,
                    ':amount'       => $amount,
                    ':created_date' => $cDate
                ]);

                $processedCount++;
                $totalPaid += $amount;
                $beneficiariesPaid[$benId] = true;
            }
        }
    }

    return [
        'processed'      => $processedCount,
        'total_paid'     => round($totalPaid, 2),
        'eligible_users' => count($beneficiariesPaid)
    ];
}

/**
 * Get detailed breakdown of direct referrals for a user to verify qualification.
 * Qualification criteria: active unlock access (user.active = '1') AND total active investment >= ₹13,000.
 */
function getQualifiedDirectDetails($userid, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$userid) return [];

    $sql = "SELECT 
                u.userid,
                u.name,
                u.active as is_active,
                COALESCE(SUM(r.package), 0) as total_investment
            FROM tbl_sponsor s
            INNER JOIN user u ON u.userid = s.referral_id
            LEFT JOIN tbl_roi_one r ON r.user_id = u.userid
            WHERE s.sponsor_id = :sponsor_id
            GROUP BY u.userid, u.name, u.active";

    $stmt = $db->prepare($sql);
    $stmt->execute([':sponsor_id' => $userid]);
    $directs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = [];
    foreach ($directs as $d) {
        $isActive = ($d['is_active'] == 1);
        $totalInv = (float)$d['total_investment'];
        $isQualified = ($isActive && $totalInv >= MIN_QUALIFIED_INVESTMENT);

        $reason = "";
        if ($isQualified) {
            $reason = "Fully Qualified ($11 Activation YES & Investment ₹" . number_format($totalInv, 2) . " >= ₹13,000)";
        } elseif (!$isActive && $totalInv >= MIN_QUALIFIED_INVESTMENT) {
            $reason = "Not Qualified (Missing $11 / ₹990 Activation)";
        } elseif ($isActive && $totalInv < MIN_QUALIFIED_INVESTMENT) {
            $reason = "Not Qualified (Investment ₹" . number_format($totalInv, 2) . " < ₹13,000)";
        } else {
            $reason = "Not Qualified (Missing $11 Activation & Investment < ₹13,000)";
        }

        $results[] = [
            'userid'           => $d['userid'],
            'name'             => $d['name'],
            'activation_status'=> $isActive ? 'YES' : 'NO',
            'total_investment' => $totalInv,
            'is_qualified'     => $isQualified,
            'qualification_status' => $isQualified ? 'QUALIFIED' : 'NOT QUALIFIED',
            'reason'           => $reason
        ];
    }

    return $results;
}

/**
 * Process Authorized Admin Direct Bonus Wallet Financial Adjustment.
 * Strict rules:
 * 1. Admin authenticated.
 * 2. PDO transaction + row lock (FOR UPDATE).
 * 3. DEBIT requires direct_bonus_wallet >= amount (prevents negative balances).
 * 4. Affects ONLY user.direct_bonus_wallet (profit_income_wallet & profit_sharing_wallet untouched).
 * 5. Logs to tbl_transaction & tbl_direct_bonus_admin_audit.
 */
function processAdminDirectBonusAdjustment($admin_id, $target_user_id, $adjustment_type, $amount, $reason, $reference = '', $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$admin_id || !$target_user_id) {
        return ['status' => 'error', 'message' => 'Invalid parameters specified.'];
    }

    $amount = (float)$amount;
    if ($amount <= 0) {
        return ['status' => 'error', 'message' => 'Adjustment amount must be a positive number greater than 0.'];
    }

    $adjType = strtoupper(trim($adjustment_type));
    if (!in_array($adjType, ['CREDIT', 'DEBIT'])) {
        return ['status' => 'error', 'message' => 'Adjustment type must be CREDIT or DEBIT.'];
    }

    if (empty(trim($reason))) {
        return ['status' => 'error', 'message' => 'A mandatory reason is required for any admin balance adjustment.'];
    }

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        // Lock user row FOR UPDATE
        $stmtUser = $db->prepare("SELECT userid, direct_bonus_wallet FROM user WHERE userid = :userid FOR UPDATE");
        $stmtUser->execute([':userid' => $target_user_id]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
        }

        $prevBal = (float)$userRow['direct_bonus_wallet'];

        if ($adjType === 'DEBIT') {
            if ($prevBal < $amount) {
                if ($inLocalTxn) $db->rollBack();
                return [
                    'status'  => 'error',
                    'message' => "Insufficient Direct Bonus Wallet balance. Current: ₹" . number_format($prevBal, 2) . ", Requested Debit: ₹" . number_format($amount, 2) . ". Negative balance is blocked."
                ];
            }
            $newBal = round($prevBal - $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET direct_bonus_wallet = direct_bonus_wallet - :amt WHERE userid = :userid");
        } else {
            $newBal = round($prevBal + $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET direct_bonus_wallet = direct_bonus_wallet + :amt WHERE userid = :userid");
        }

        $updStmt->execute([':amt' => $amount, ':userid' => $target_user_id]);

        // Insert Transaction Record
        $txnType = ($adjType === 'CREDIT') ? 'Credit' : 'Debit';
        $txnSub  = "Admin Direct Bonus Adjustment (" . $adjType . ") - " . $reason;
        $insTxn  = $db->prepare("INSERT INTO tbl_transaction (user_id, type, subject, amount, created_date, status) VALUES (:user_id, :type, :subject, :amount, CURDATE(), 1)");
        $insTxn->execute([
            ':user_id' => $target_user_id,
            ':type'    => $txnType,
            ':subject' => $txnSub,
            ':amount'  => $amount
        ]);

        // Insert Audit Log Record
        $insAudit = $db->prepare("INSERT INTO tbl_direct_bonus_admin_audit (admin_id, action, user_id, amount, wallet, previous_balance, new_balance, reason, reference) VALUES (:admin_id, :action, :user_id, :amount, 'direct_bonus_wallet', :prev_bal, :new_bal, :reason, :reference)");
        $insAudit->execute([
            ':admin_id' => $admin_id,
            ':action'   => $adjType,
            ':user_id'  => $target_user_id,
            ':amount'   => $amount,
            ':prev_bal' => $prevBal,
            ':new_bal'  => $newBal,
            ':reason'   => $reason,
            ':reference'=> $reference
        ]);

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'           => 'success',
            'message'          => "Successfully processed Direct Bonus Adjustment ({$adjType} ₹" . number_format($amount, 2) . ") for user {$target_user_id}.",
            'previous_balance' => $prevBal,
            'new_balance'      => $newBal
        ];

    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Adjustment execution failed: ' . $e->getMessage()];
    }
}

/**
 * =========================================================================
 * REQUIREMENT #14 — MENTOR INCOME SYSTEM FUNCTIONS
 * =========================================================================
 */
if (!defined('MENTOR_INCOME_PERCENT')) {
    define('MENTOR_INCOME_PERCENT', 2.0);
}

/**
 * Get list of direct users under a mentor along with their recorded contribution percentage.
 * Source of truth: tbl_mentor_direct_contribution
 */
function getMentorDirectContributions($mentor_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$mentor_id) return [];

    $stmt = $db->prepare("
        SELECT 
            s.referral_id as direct_user_id,
            u.name as direct_user_name,
            u.active as is_active,
            COALESCE(c.contribution_percentage, 0.00) as contribution_percentage,
            c.updated_at
        FROM tbl_sponsor s
        INNER JOIN user u ON u.userid = CONVERT(s.referral_id USING utf8mb4)
        LEFT JOIN tbl_mentor_direct_contribution c ON c.mentor_id = CONVERT(s.sponsor_id USING utf8mb4) AND c.direct_user_id = CONVERT(s.referral_id USING utf8mb4)
        WHERE s.sponsor_id = :mentor_id
        ORDER BY u.userid ASC
    ");
    $stmt->execute([':mentor_id' => $mentor_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Save / Update a Direct User's contribution percentage under a Mentor.
 * Validation: 0 <= % <= 100
 */
function saveMentorDirectContribution($mentor_id, $direct_user_id, $contribution_percentage, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$mentor_id || !$direct_user_id) {
        return ['status' => 'error', 'message' => 'Invalid parameters specified.'];
    }

    $pct = (float)$contribution_percentage;
    if ($pct < 0 || $pct > 100) {
        return ['status' => 'error', 'message' => 'Contribution percentage must be between 0% and 100%.'];
    }

    // Verify direct sponsor relationship exists
    $chkStmt = $db->prepare("SELECT COUNT(*) FROM tbl_sponsor WHERE sponsor_id = :m_id AND referral_id = :d_id");
    $chkStmt->execute([':m_id' => $mentor_id, ':d_id' => $direct_user_id]);
    if ($chkStmt->fetchColumn() == 0) {
        return ['status' => 'error', 'message' => "User {$direct_user_id} is not a direct referral of Mentor {$mentor_id}."];
    }

    $stmt = $db->prepare("
        INSERT INTO tbl_mentor_direct_contribution (mentor_id, direct_user_id, contribution_percentage)
        VALUES (:mentor_id, :direct_user_id, :pct)
        ON DUPLICATE KEY UPDATE contribution_percentage = :pct_dup, updated_at = NOW()
    ");
    $stmt->execute([
        ':mentor_id'      => $mentor_id,
        ':direct_user_id' => $direct_user_id,
        ':pct'            => $pct,
        ':pct_dup'        => $pct
    ]);

    return ['status' => 'success', 'message' => 'Contribution percentage updated successfully.'];
}

/**
 * Validate total contribution percentage for all direct users under a Mentor.
 * Rule: Sum of all direct users' contribution percentages MUST equal exactly 100.00%.
 */
function validateMentorContributions($mentor_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$mentor_id) {
        return ['valid' => false, 'total_percentage' => 0.0, 'reason' => 'Invalid parameters specified.'];
    }

    $stmt = $db->prepare("
        SELECT COALESCE(SUM(contribution_percentage), 0.00) as total_pct
        FROM tbl_mentor_direct_contribution
        WHERE mentor_id = :mentor_id
    ");
    $stmt->execute([':mentor_id' => $mentor_id]);
    $total = round((float)$stmt->fetchColumn(), 2);

    if (abs($total - 100.00) < 0.001) {
        return ['valid' => true, 'total_percentage' => 100.00, 'reason' => 'Total contribution is exactly 100%.'];
    } else {
        return [
            'valid'            => false,
            'total_percentage' => $total,
            'reason'           => "Total contribution percentage is {$total}%, which does NOT equal exactly 100.00%. Payout blocked."
        ];
    }
}

/**
 * Process Monthly Mentor Income Payouts during Monthly Closing.
 * Calculates 2% Mentor Income from Mentor's Monthly Profit Income,
 * validates total contribution = 100%, and credits eligible Direct Users' mentor_income_wallet.
 */
function processMentorIncome($closing_month, $closing_date = null, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$closing_month) {
        return ['processed' => 0, 'total_paid' => 0.0, 'mentors_processed' => 0, 'blocked_mentors' => 0];
    }

    $cDate = $closing_date ?: date('Y-m-d');

    // 1. Identify all Mentors who earned Profit Income in this closing month
    $stmtMentors = $db->prepare("
        SELECT user_id as mentor_id, SUM(amount) as monthly_income
        FROM tbl_transaction
        WHERE type = 'Profit Income'
          AND created_date = :cDate
        GROUP BY user_id
        HAVING monthly_income > 0
    ");
    $stmtMentors->execute([':cDate' => $cDate]);
    $mentors = $stmtMentors->fetchAll(PDO::FETCH_ASSOC);

    $processedCount = 0;
    $totalPaid = 0.0;
    $mentorsProcessed = 0;
    $blockedMentors = 0;

    $insSchedule = $db->prepare("
        INSERT INTO tbl_mentor_income_schedule
        (mentor_id, direct_user_id, closing_month, mentor_monthly_income, mentor_income_rate, total_mentor_income, contribution_percentage, payout_amount, status, credited_at, closing_id)
        VALUES
        (:mentor_id, :direct_user_id, :closing_month, :mentor_monthly_income, :mentor_income_rate, :total_mentor_income, :contribution_percentage, :payout_amount, 'CREDITED', NOW(), :closing_month)
    ");

    $updWallet = $db->prepare("
        UPDATE user
        SET mentor_income_wallet = mentor_income_wallet + :amount
        WHERE userid = :userid
    ");

    $insTxn = $db->prepare("
        INSERT INTO tbl_transaction
        (user_id, type, subject, amount, created_date, status)
        VALUES
        (:user_id, 'Credit', :subject, :amount, :created_date, 1)
    ");

    foreach ($mentors as $m) {
        $mentorId = $m['mentor_id'];
        $monthlyInc = (float)$m['monthly_income'];
        $totalMentorInc = round($monthlyInc * (MENTOR_INCOME_PERCENT / 100.0), 2);

        if ($totalMentorInc <= 0) continue;

        // 2. Validate Contribution Total for this Mentor
        $val = validateMentorContributions($mentorId, $db);
        if (!$val['valid']) {
            $blockedMentors++;
            continue; // STRICT RULE: If total contribution != 100%, ZERO payouts executed for this mentor
        }

        // 3. Fetch Recorded Direct Contributions
        $contribs = getMentorDirectContributions($mentorId, $db);
        $mentorPaidAny = false;

        foreach ($contribs as $c) {
            $directUserId = $c['direct_user_id'];
            $contribPct = (float)$c['contribution_percentage'];

            if ($contribPct <= 0) continue; // 0% contribution means ₹0 payout (skipped)

            $payoutAmount = round(($totalMentorInc * $contribPct) / 100.0, 2);
            if ($payoutAmount <= 0) continue;

            // Check database-level idempotency to prevent duplicate credit for (mentor, direct_user, closing_month)
            $chkIdem = $db->prepare("SELECT COUNT(*) FROM tbl_mentor_income_schedule WHERE mentor_id = :m_id AND direct_user_id = :d_id AND closing_month = :c_month");
            $chkIdem->execute([':m_id' => $mentorId, ':d_id' => $directUserId, ':c_month' => $closing_month]);
            if ($chkIdem->fetchColumn() > 0) {
                continue; // Already processed
            }

            // A. Record Payout Schedule
            $insSchedule->execute([
                ':mentor_id'             => $mentorId,
                ':direct_user_id'        => $directUserId,
                ':closing_month'         => $closing_month,
                ':mentor_monthly_income' => $monthlyInc,
                ':mentor_income_rate'    => MENTOR_INCOME_PERCENT,
                ':total_mentor_income'   => $totalMentorInc,
                ':contribution_percentage' => $contribPct,
                ':payout_amount'         => $payoutAmount
            ]);

            // B. Credit mentor_income_wallet of Direct User
            $updWallet->execute([
                ':amount' => $payoutAmount,
                ':userid' => $directUserId
            ]);

            // C. Record Transaction Entry
            $subject = "Mentor Income Payout ({$closing_month} - Mentor {$mentorId} - {$contribPct}% Contribution)";
            $insTxn->execute([
                ':user_id'      => $directUserId,
                ':subject'      => $subject,
                ':amount'       => $payoutAmount,
                ':created_date' => $cDate
            ]);

            $processedCount++;
            $totalPaid += $payoutAmount;
            $mentorPaidAny = true;
        }

        if ($mentorPaidAny) {
            $mentorsProcessed++;
        }
    }

    return [
        'processed'         => $processedCount,
        'total_paid'        => round($totalPaid, 2),
        'mentors_processed' => $mentorsProcessed,
        'blocked_mentors'   => $blockedMentors
    ];
}

/**
 * Process Authorized Admin Mentor Income Wallet Financial Adjustment.
 * Strict rules:
 * 1. Admin authenticated using $_SESSION['auserid'].
 * 2. PDO transaction + row lock (FOR UPDATE).
 * 3. DEBIT requires mentor_income_wallet >= amount (prevents negative balances).
 * 4. Affects ONLY user.mentor_income_wallet (profit_income_wallet, profit_sharing_wallet & direct_bonus_wallet untouched).
 * 5. Logs to tbl_transaction & tbl_mentor_income_admin_audit.
 */
function processAdminMentorIncomeAdjustment($admin_id, $target_user_id, $adjustment_type, $amount, $reason, $reference = '', $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$admin_id || !$target_user_id) {
        return ['status' => 'error', 'message' => 'Invalid parameters specified.'];
    }

    $amount = (float)$amount;
    if ($amount <= 0) {
        return ['status' => 'error', 'message' => 'Adjustment amount must be a positive number greater than 0.'];
    }

    $adjType = strtoupper(trim($adjustment_type));
    if (!in_array($adjType, ['CREDIT', 'DEBIT'])) {
        return ['status' => 'error', 'message' => 'Adjustment type must be CREDIT or DEBIT.'];
    }

    if (empty(trim($reason))) {
        return ['status' => 'error', 'message' => 'A mandatory reason is required for any admin balance adjustment.'];
    }

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        // Lock user row FOR UPDATE
        $stmtUser = $db->prepare("SELECT userid, mentor_income_wallet FROM user WHERE userid = :userid FOR UPDATE");
        $stmtUser->execute([':userid' => $target_user_id]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
        }

        $prevBal = (float)$userRow['mentor_income_wallet'];

        if ($adjType === 'DEBIT') {
            if ($prevBal < $amount) {
                if ($inLocalTxn) $db->rollBack();
                return [
                    'status'  => 'error',
                    'message' => "Insufficient Mentor Income Wallet balance. Current: ₹" . number_format($prevBal, 2) . ", Requested Debit: ₹" . number_format($amount, 2) . ". Negative balance is blocked."
                ];
            }
            $newBal = round($prevBal - $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET mentor_income_wallet = mentor_income_wallet - :amt WHERE userid = :userid");
        } else {
            $newBal = round($prevBal + $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET mentor_income_wallet = mentor_income_wallet + :amt WHERE userid = :userid");
        }

        $updStmt->execute([':amt' => $amount, ':userid' => $target_user_id]);

        // Transaction log entry
        $txnType = ($adjType === 'CREDIT') ? 'Credit' : 'Debit';
        $subject = "Admin Mentor Income Adjustment ({$adjType}) - Reason: {$reason}";
        if (!empty($reference)) {
            $subject .= " (Ref: {$reference})";
        }

        $insTxn = $db->prepare("
            INSERT INTO tbl_transaction
            (user_id, type, subject, amount, created_date, status)
            VALUES
            (:user_id, :type, :subject, :amount, NOW(), 1)
        ");
        $insTxn->execute([
            ':user_id' => $target_user_id,
            ':type'    => $txnType,
            ':subject' => $subject,
            ':amount'  => $amount
        ]);

        // Audit log entry
        $insAudit = $db->prepare("
            INSERT INTO tbl_mentor_income_admin_audit
            (admin_id, action, user_id, amount, wallet, previous_balance, new_balance, reason, reference)
            VALUES
            (:admin_id, :action, :user_id, :amount, 'mentor_income_wallet', :previous_balance, :new_balance, :reason, :reference)
        ");
        $insAudit->execute([
            ':admin_id'         => $admin_id,
            ':action'           => $adjType,
            ':user_id'          => $target_user_id,
            ':amount'           => $amount,
            ':previous_balance' => $prevBal,
            ':new_balance'      => $newBal,
            ':reason'           => $reason,
            ':reference'        => $reference
        ]);

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'           => 'success',
            'message'          => "Successfully processed {$adjType} of ₹" . number_format($amount, 2) . " for user {$target_user_id}.",
            'previous_balance' => $prevBal,
            'new_balance'      => $newBal
        ];
    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Adjustment error: ' . $e->getMessage()];
    }
}

/**
 * Get summary report statistics for Mentor Income Admin Portal.
 */
function getMentorIncomeReportStats($pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db) return ['total_generated' => 0.0, 'total_credited' => 0.0, 'total_blocked' => 0, 'total_adjusted' => 0.0];

    $totGenerated = (float)$db->query("SELECT COALESCE(SUM(total_mentor_income), 0) FROM tbl_mentor_income_schedule")->fetchColumn();
    $totCredited  = (float)$db->query("SELECT COALESCE(SUM(payout_amount), 0) FROM tbl_mentor_income_schedule WHERE status = 'CREDITED'")->fetchColumn();
    $totBlocked   = (int)$db->query("SELECT COUNT(DISTINCT mentor_id) FROM tbl_mentor_direct_contribution WHERE mentor_id NOT IN (SELECT DISTINCT mentor_id FROM tbl_mentor_income_schedule)")->fetchColumn();
    $totAdjusted  = (float)$db->query("SELECT COALESCE(SUM(CASE WHEN action = 'CREDIT' THEN amount ELSE -amount END), 0) FROM tbl_mentor_income_admin_audit")->fetchColumn();

    return [
        'total_generated' => round($totGenerated, 2),
        'total_credited'  => round($totCredited, 2),
        'total_blocked'   => $totBlocked,
        'total_adjusted'  => round($totAdjusted, 2)
    ];
}

/**
 * Helper: Recursively or iteratively collect all descendant user IDs in a binary subtree.
 */
function getBinarySubtreeUserIds($start_user_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$start_user_id) return [];

    $visited = [];
    $queue = [$start_user_id];

    $stmt = $db->prepare("SELECT left_id, right_id FROM tree WHERE userid = :uid LIMIT 1");

    while (!empty($queue)) {
        $curr = array_shift($queue);
        if (isset($visited[$curr])) continue;
        $visited[$curr] = true;

        $stmt->execute([':uid' => $curr]);
        $node = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($node) {
            if (!empty($node['left_id']) && !isset($visited[$node['left_id']])) {
                $queue[] = $node['left_id'];
            }
            if (!empty($node['right_id']) && !isset($visited[$node['right_id']])) {
                $queue[] = $node['right_id'];
            }
        }
    }

    // Return list of descendants excluding start node itself
    unset($visited[$start_user_id]);
    return array_keys($visited);
}

/**
 * Get detailed Left Leg and Right Leg statistics (ID counts and Business in USD) for a user.
 */
function getBinaryLegDetails($user_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$user_id) {
        return [
            'left_id' => '', 'right_id' => '',
            'left_ids_count' => 0, 'right_ids_count' => 0,
            'left_business_usd' => 0.0, 'right_business_usd' => 0.0,
            'weaker_leg_business_usd' => 0.0
        ];
    }

    $stmtTree = $db->prepare("SELECT left_id, right_id FROM tree WHERE userid = :uid LIMIT 1");
    $stmtTree->execute([':uid' => $user_id]);
    $treeNode = $stmtTree->fetch(PDO::FETCH_ASSOC);

    $leftId  = $treeNode['left_id'] ?? '';
    $rightId = $treeNode['right_id'] ?? '';

    $leftSubtreeUsers  = $leftId ? array_merge([$leftId], getBinarySubtreeUserIds($leftId, $db)) : [];
    $rightSubtreeUsers = $rightId ? array_merge([$rightId], getBinarySubtreeUserIds($rightId, $db)) : [];

    // Helper to calculate active count & business in USD ($1 = ₹90)
    $calcLegStats = function($userList) use ($db) {
        if (empty($userList)) return ['count' => 0, 'business_usd' => 0.0];

        $inClause = "'" . implode("','", array_map('addslashes', $userList)) . "'";
        $stmtCount = $db->query("SELECT COUNT(*) FROM user WHERE userid IN ({$inClause}) AND active = '1'");
        $activeCount = (int)$stmtCount->fetchColumn();

        $stmtBiz = $db->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one WHERE user_id IN ({$inClause})");
        $bizInr = (float)$stmtBiz->fetchColumn();
        $bizUsd = round($bizInr / 90.0, 2);

        return ['count' => $activeCount, 'business_usd' => $bizUsd];
    };

    $leftStats  = $calcLegStats($leftSubtreeUsers);
    $rightStats = $calcLegStats($rightSubtreeUsers);

    $weakerBizUsd = min($leftStats['business_usd'], $rightStats['business_usd']);

    return [
        'left_id'                 => $leftId,
        'right_id'                => $rightId,
        'left_ids_count'          => $leftStats['count'],
        'right_ids_count'         => $rightStats['count'],
        'left_business_usd'       => $leftStats['business_usd'],
        'right_business_usd'      => $rightStats['business_usd'],
        'weaker_leg_business_usd' => $weakerBizUsd
    ];
}

/**
 * Evaluate and process VIP Club Qualifications & One-Time Rewards for a user.
 */
function evaluateUserVIPQualifications($user_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$user_id) return ['newly_qualified' => [], 'all_qualified' => []];

    $legDetails = getBinaryLegDetails($user_id, $db);

    $configs = $db->query("SELECT * FROM tbl_vip_level_config ORDER BY level_id ASC")->fetchAll(PDO::FETCH_ASSOC);

    $insQual = $db->prepare("
        INSERT INTO tbl_vip_user_qualification
        (user_id, vip_level, left_ids_achieved, right_ids_achieved, left_business_achieved, right_business_achieved, weaker_leg_business, reward_amount, reward_status)
        VALUES
        (:user_id, :vip_level, :left_ids, :right_ids, :left_biz, :right_biz, :weaker_biz, :reward_amt, 'CREDITED')
    ");

    $updWallet = $db->prepare("UPDATE user SET vip_club_wallet = vip_club_wallet + :amt WHERE userid = :uid");

    $insTxn = $db->prepare("
        INSERT INTO tbl_transaction
        (user_id, type, subject, amount, created_date, status)
        VALUES
        (:user_id, 'Credit', :subject, :amount, NOW(), 1)
    ");

    $chkQual = $db->prepare("SELECT COUNT(*) FROM tbl_vip_user_qualification WHERE user_id = :uid AND vip_level = :lvl");

    $newlyQualified = [];
    $allQualified = [];

    foreach ($configs as $c) {
        $lvl = (int)$c['level_id'];
        $reqLeftIds  = (int)$c['req_left_ids'];
        $reqRightIds = (int)$c['req_right_ids'];
        $reqLeftBiz  = (float)$c['req_left_business'];
        $reqRightBiz = (float)$c['req_right_business'];
        $rewardAmt   = (float)$c['reward_amount'];

        $isQualified = (
            $legDetails['left_ids_count'] >= $reqLeftIds &&
            $legDetails['right_ids_count'] >= $reqRightIds &&
            $legDetails['left_business_usd'] >= $reqLeftBiz &&
            $legDetails['right_business_usd'] >= $reqRightBiz
        );

        if ($isQualified) {
            $allQualified[] = $lvl;

            $chkQual->execute([':uid' => $user_id, ':lvl' => $lvl]);
            if ($chkQual->fetchColumn() == 0) {
                // Idempotent first-time qualification & reward distribution
                $insQual->execute([
                    ':user_id'    => $user_id,
                    ':vip_level'  => $lvl,
                    ':left_ids'   => $legDetails['left_ids_count'],
                    ':right_ids'  => $legDetails['right_ids_count'],
                    ':left_biz'   => $legDetails['left_business_usd'],
                    ':right_biz'  => $legDetails['right_business_usd'],
                    ':weaker_biz' => $legDetails['weaker_leg_business_usd'],
                    ':reward_amt' => $rewardAmt
                ]);

                if ($rewardAmt > 0) {
                    $updWallet->execute([':amt' => $rewardAmt, ':uid' => $user_id]);
                    $subject = "VIP Club Level {$lvl} Reward ($ " . number_format($rewardAmt, 2) . ")";
                    $insTxn->execute([':user_id' => $user_id, ':subject' => $subject, ':amount' => $rewardAmt]);
                }

                $newlyQualified[] = ['level' => $lvl, 'reward' => $rewardAmt];
            }
        }
    }

    return [
        'leg_details'     => $legDetails,
        'newly_qualified' => $newlyQualified,
        'all_qualified'   => $allQualified
    ];
}

/**
 * Process Monthly VIP Club Income & Company Turnover Share Payouts during Monthly Closing.
 * Rule: VIP Monthly closing MUST occur on the 11th date of the month (unless explicitly bypassed via $skip_date_check).
 */
function processVIPMonthlyIncome($closing_month, $closing_date = null, $pdoConnection = null, $skip_date_check = false) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$closing_month) {
        return ['processed' => 0, 'total_paid' => 0.0, 'eligible_users' => 0, 'status' => 'error', 'message' => 'Invalid parameters.'];
    }

    $cDate = $closing_date ?: date('Y-m-d');
    $closingDay = (int)date('d', strtotime($cDate));

    // Requirement #17 Rule: VIP Monthly closing is restricted to 11th date of the month
    if (!$skip_date_check && $closingDay !== 11) {
        return [
            'processed'      => 0,
            'total_paid'     => 0.0,
            'eligible_users' => 0,
            'status'         => 'blocked_non_11th_date',
            'message'        => "VIP Club Monthly Income closing is strictly restricted to the 11th date of the month. Given date: {$cDate} (Day: {$closingDay})."
        ];
    }

    // Total Company Monthly Turnover in USD ($1 = ₹90)
    $stmtTurnover = $db->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one");
    $companyTurnoverInr = (float)$stmtTurnover->fetchColumn();
    $companyTurnoverUsd = round($companyTurnoverInr / 90.0, 2);

    // Fetch all users who qualified for VIP levels
    $stmtQuals = $db->query("
        SELECT q.user_id, MAX(q.vip_level) as highest_level
        FROM tbl_vip_user_qualification q
        GROUP BY q.user_id
    ");
    $qualUsers = $stmtQuals->fetchAll(PDO::FETCH_ASSOC);

    $processedCount = 0;
    $totalPaid = 0.0;
    $eligibleUsersCount = 0;

    $insSchedule = $db->prepare("
        INSERT INTO tbl_vip_monthly_schedule
        (user_id, vip_level, closing_month, closing_date, weaker_leg_business, weaker_leg_rate, weaker_leg_payout, company_turnover, turnover_rate, turnover_payout, total_payout, status, credited_at, closing_id)
        VALUES
        (:user_id, :vip_level, :closing_month, :closing_date, :weaker_leg_business, :weaker_leg_rate, :weaker_leg_payout, :company_turnover, :turnover_rate, :turnover_payout, :total_payout, 'CREDITED', NOW(), :closing_id)
    ");

    $updWallet = $db->prepare("UPDATE user SET vip_club_wallet = vip_club_wallet + :amt WHERE userid = :uid");

    $insTxn = $db->prepare("
        INSERT INTO tbl_transaction
        (user_id, type, subject, amount, created_date, status)
        VALUES
        (:user_id, 'Credit', :subject, :amount, :created_date, 1)
    ");

    $chkIdem = $db->prepare("SELECT COUNT(*) FROM tbl_vip_monthly_schedule WHERE user_id = :uid AND vip_level = :lvl AND closing_month = :c_month");
    $getConfig = $db->prepare("SELECT * FROM tbl_vip_level_config WHERE level_id = :lvl LIMIT 1");

    foreach ($qualUsers as $u) {
        $userId = $u['user_id'];
        $highestLvl = (int)$u['highest_level'];

        $getConfig->execute([':lvl' => $highestLvl]);
        $c = $getConfig->fetch(PDO::FETCH_ASSOC);
        if (!$c) continue;

        $legDetails = getBinaryLegDetails($userId, $db);
        $weakerBizUsd = (float)$legDetails['weaker_leg_business_usd'];
        $repeatReqUsd = (float)$c['monthly_repeat_business'];

        // Check Monthly Repeat requirement
        if ($weakerBizUsd < $repeatReqUsd) {
            continue; // Weaker leg repeat condition not met
        }

        // Weaker Leg Component Calculation
        $incomeRate = (float)$c['vip_income_rate'];
        $weakerLegPayout = round($weakerBizUsd * ($incomeRate / 100.0), 2);

        // Company Turnover Component Calculation (Levels 7-10)
        $hasTurnoverShare = ((int)$c['has_turnover_share'] === 1);
        $turnoverRate = $hasTurnoverShare ? 0.50 : 0.00;
        $turnoverPayout = $hasTurnoverShare ? round($companyTurnoverUsd * 0.005, 2) : 0.00;

        $totalPayout = round($weakerLegPayout + $turnoverPayout, 2);
        if ($totalPayout <= 0) continue;

        // Check Idempotency
        $chkIdem->execute([':uid' => $userId, ':lvl' => $highestLvl, ':c_month' => $closing_month]);
        if ($chkIdem->fetchColumn() > 0) {
            continue; // Already processed for this month
        }

        // 1. Record Schedule Entry
        $insSchedule->execute([
            ':user_id'             => $userId,
            ':vip_level'           => $highestLvl,
            ':closing_month'        => $closing_month,
            ':closing_date'        => $cDate,
            ':weaker_leg_business' => $weakerBizUsd,
            ':weaker_leg_rate'     => $incomeRate,
            ':weaker_leg_payout'   => $weakerLegPayout,
            ':company_turnover'    => $companyTurnoverUsd,
            ':turnover_rate'       => $turnoverRate,
            ':turnover_payout'     => $turnoverPayout,
            ':total_payout'        => $totalPayout,
            ':closing_id'          => $closing_month
        ]);

        // 2. Credit vip_club_wallet
        $updWallet->execute([':amt' => $totalPayout, ':uid' => $userId]);

        // 3. Record Transaction Entry
        $subject = "VIP Club Level {$highestLvl} Monthly Income ({$closing_month}) - Weaker Leg: $" . number_format($weakerLegPayout, 2);
        if ($hasTurnoverShare) {
            $subject .= " + Turnover Share: $" . number_format($turnoverPayout, 2);
        }
        $insTxn->execute([
            ':user_id'      => $userId,
            ':subject'      => $subject,
            ':amount'       => $totalPayout,
            ':created_date' => $cDate
        ]);

        $processedCount++;
        $totalPaid += $totalPayout;
        $eligibleUsersCount++;
    }

    return [
        'processed'      => $processedCount,
        'total_paid'     => round($totalPaid, 2),
        'eligible_users' => $eligibleUsersCount,
        'status'         => 'success',
        'message'        => 'VIP Club Monthly Income processed successfully.'
    ];
}

/**
 * Process Authorized Admin VIP Club Wallet Financial Adjustment.
 * Strict rules:
 * 1. Admin authenticated using $_SESSION['auserid'].
 * 2. PDO transaction + row lock (FOR UPDATE).
 * 3. DEBIT requires vip_club_wallet >= amount (prevents negative balances).
 * 4. Affects ONLY user.vip_club_wallet (all other wallets untouched).
 * 5. Logs to tbl_transaction & tbl_vip_admin_audit.
 */
function processAdminVIPAdjustment($admin_id, $target_user_id, $adjustment_type, $amount, $reason, $reference = '', $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$admin_id || !$target_user_id) {
        return ['status' => 'error', 'message' => 'Invalid parameters specified.'];
    }

    $amount = (float)$amount;
    if ($amount <= 0) {
        return ['status' => 'error', 'message' => 'Adjustment amount must be a positive number greater than 0.'];
    }

    $adjType = strtoupper(trim($adjustment_type));
    if (!in_array($adjType, ['CREDIT', 'DEBIT'])) {
        return ['status' => 'error', 'message' => 'Adjustment type must be CREDIT or DEBIT.'];
    }

    if (empty(trim($reason))) {
        return ['status' => 'error', 'message' => 'A mandatory reason is required for any admin balance adjustment.'];
    }

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        // Lock user row FOR UPDATE
        $stmtUser = $db->prepare("SELECT userid, vip_club_wallet FROM user WHERE userid = :userid FOR UPDATE");
        $stmtUser->execute([':userid' => $target_user_id]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
        }

        $prevBal = (float)$userRow['vip_club_wallet'];

        if ($adjType === 'DEBIT') {
            if ($prevBal < $amount) {
                if ($inLocalTxn) $db->rollBack();
                return [
                    'status'  => 'error',
                    'message' => "Insufficient VIP Club Wallet balance. Current: $" . number_format($prevBal, 2) . ", Requested Debit: $" . number_format($amount, 2) . ". Negative balance is blocked."
                ];
            }
            $newBal = round($prevBal - $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET vip_club_wallet = vip_club_wallet - :amt WHERE userid = :userid");
        } else {
            $newBal = round($prevBal + $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET vip_club_wallet = vip_club_wallet + :amt WHERE userid = :userid");
        }

        $updStmt->execute([':amt' => $amount, ':userid' => $target_user_id]);

        // Transaction log entry
        $txnType = ($adjType === 'CREDIT') ? 'Credit' : 'Debit';
        $subject = "Admin VIP Club Adjustment ({$adjType}) - Reason: {$reason}";
        if (!empty($reference)) {
            $subject .= " (Ref: {$reference})";
        }

        $insTxn = $db->prepare("
            INSERT INTO tbl_transaction
            (user_id, type, subject, amount, created_date, status)
            VALUES
            (:user_id, :type, :subject, :amount, NOW(), 1)
        ");
        $insTxn->execute([
            ':user_id' => $target_user_id,
            ':type'    => $txnType,
            ':subject' => $subject,
            ':amount'  => $amount
        ]);

        // Audit log entry
        $insAudit = $db->prepare("
            INSERT INTO tbl_vip_admin_audit
            (admin_id, action, user_id, amount, wallet, previous_balance, new_balance, reason, reference)
            VALUES
            (:admin_id, :action, :user_id, :amount, 'vip_club_wallet', :previous_balance, :new_balance, :reason, :reference)
        ");
        $insAudit->execute([
            ':admin_id'         => $admin_id,
            ':action'           => $adjType,
            ':user_id'          => $target_user_id,
            ':amount'           => $amount,
            ':previous_balance' => $prevBal,
            ':new_balance'      => $newBal,
            ':reason'           => $reason,
            ':reference'        => $reference
        ]);

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'           => 'success',
            'message'          => "Successfully processed {$adjType} of $" . number_format($amount, 2) . " for user {$target_user_id}.",
            'previous_balance' => $prevBal,
            'new_balance'      => $newBal
        ];
    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Adjustment error: ' . $e->getMessage()];
    }
}

/**
 * Get all system control settings as key-value pairs.
 */
function getSystemControls($pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db) return [];
    try {
        $stmt = $db->query("SELECT setting_key, setting_value FROM tbl_system_control");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Update system control setting.
 */
function setSystemControl($setting_key, $setting_value, $admin_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$setting_key) return false;
    $val = ($setting_value == '1' || $setting_value === 'true' || $setting_value === true) ? '1' : '0';
    $stmt = $db->prepare("UPDATE tbl_system_control SET setting_value = :val WHERE setting_key = :key");
    $res = $stmt->execute([':val' => $val, ':key' => $setting_key]);

    if ($res && function_exists('logAdminAuditAction')) {
        logAdminAuditAction($admin_id, 'TOGGLE_SETTING', null, 0.00, $setting_key, 0.00, 0.00, "Toggled setting {$setting_key} to {$val}", $setting_key, $db);
    }
    return $res;
}

/**
 * Log unified admin audit action in tbl_admin_audit_log.
 */
function logAdminAuditAction($admin_id, $action, $target_user_id, $amount, $wallet_type, $prev_bal, $new_bal, $reason, $ref_id = null, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db) return false;
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI/Admin', 0, 250);
        $stmt = $db->prepare("
            INSERT INTO tbl_admin_audit_log
            (admin_id, action, target_user_id, amount, wallet_type, previous_balance, new_balance, reason, reference_id, ip_address, user_agent)
            VALUES
            (:admin_id, :action, :target_user_id, :amount, :wallet_type, :prev_bal, :new_bal, :reason, :ref_id, :ip, :ua)
        ");
        return $stmt->execute([
            ':admin_id'       => $admin_id,
            ':action'         => strtoupper($action),
            ':target_user_id' => $target_user_id,
            ':amount'         => (float)$amount,
            ':wallet_type'    => $wallet_type,
            ':prev_bal'       => (float)$prev_bal,
            ':new_bal'        => (float)$new_bal,
            ':reason'         => $reason,
            ':ref_id'         => $ref_id,
            ':ip'             => $ip,
            ':ua'             => $ua
        ]);
    } catch (Exception $e) {
        error_log("logAdminAuditAction Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Process Universal Admin Wallet Adjustment (Credit / Debit) across any isolated wallet with row lock & audit.
 */
function processUniversalAdminWalletAdjustment($admin_id, $target_user_id, $wallet_column, $adjustment_type, $amount, $reason, $reference = '', $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$admin_id || !$target_user_id) {
        return ['status' => 'error', 'message' => 'Invalid parameters. Admin ID & User ID required.'];
    }

    $validWallets = [
        'profit_income_wallet',
        'profit_sharing_wallet',
        'direct_bonus_wallet',
        'mentor_income_wallet',
        'vip_club_wallet',
        'deposite_wallet',
        'working_wallet',
        'nonwork_wallet',
        'amount'
    ];

    if (!in_array($wallet_column, $validWallets)) {
        return ['status' => 'error', 'message' => "Invalid wallet type specified: {$wallet_column}"];
    }

    $adjType = strtoupper(trim($adjustment_type));
    if (!in_array($adjType, ['CREDIT', 'DEBIT'])) {
        return ['status' => 'error', 'message' => 'Adjustment type must be CREDIT or DEBIT.'];
    }

    $amount = (float)$amount;
    if ($amount <= 0) {
        return ['status' => 'error', 'message' => 'Adjustment amount must be a positive number > 0.'];
    }

    if (empty(trim($reason))) {
        return ['status' => 'error', 'message' => 'A mandatory reason is required for any admin balance adjustment.'];
    }

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        $stmtUser = $db->prepare("SELECT userid, `{$wallet_column}` FROM user WHERE userid = :userid FOR UPDATE");
        $stmtUser->execute([':userid' => $target_user_id]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
        }

        $prevBal = (float)$userRow[$wallet_column];

        if ($adjType === 'DEBIT') {
            if ($prevBal < $amount) {
                if ($inLocalTxn) $db->rollBack();
                return [
                    'status'  => 'error',
                    'message' => "Insufficient balance in {$wallet_column}. Current: " . number_format($prevBal, 2) . ", Requested Debit: " . number_format($amount, 2) . ". Negative balance is blocked."
                ];
            }
            $newBal = round($prevBal - $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET `{$wallet_column}` = `{$wallet_column}` - :amt WHERE userid = :userid");
        } else {
            $newBal = round($prevBal + $amount, 2);
            $updStmt = $db->prepare("UPDATE user SET `{$wallet_column}` = `{$wallet_column}` + :amt WHERE userid = :userid");
        }

        $updStmt->execute([':amt' => $amount, ':userid' => $target_user_id]);

        $txnType = ($adjType === 'CREDIT') ? 'Credit' : 'Debit';
        $subject = "Admin Adjustment ({$adjType}) - Wallet: {$wallet_column} - Reason: {$reason}";
        if (!empty($reference)) {
            $subject .= " (Ref: {$reference})";
        }

        $insTxn = $db->prepare("
            INSERT INTO tbl_transaction
            (user_id, type, subject, amount, created_date, status)
            VALUES
            (:user_id, :type, :subject, :amount, NOW(), 1)
        ");
        $insTxn->execute([
            ':user_id' => $target_user_id,
            ':type'    => $txnType,
            ':subject' => $subject,
            ':amount'  => $amount
        ]);

        logAdminAuditAction($admin_id, $adjType, $target_user_id, $amount, $wallet_column, $prevBal, $newBal, $reason, $reference, $db);

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'           => 'success',
            'message'          => "Successfully processed {$adjType} of " . number_format($amount, 2) . " on {$wallet_column} for user {$target_user_id}.",
            'previous_balance' => $prevBal,
            'new_balance'      => $newBal
        ];
    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Adjustment error: ' . $e->getMessage()];
    }
}

/**
 * Comprehensive real-time Dashboard statistics calculator for Requirement #18.
 */
function getAdminComprehensiveDashboardStats($pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db) return [];

    $totUsers     = (int)$db->query("SELECT COUNT(*) FROM user")->fetchColumn();
    $activeUsers  = (int)$db->query("SELECT COUNT(*) FROM user WHERE active = '1'")->fetchColumn();
    $inactiveUsers= (int)$db->query("SELECT COUNT(*) FROM user WHERE active = '0'")->fetchColumn();

    $totUnlockAcc = (int)$db->query("SELECT COUNT(*) FROM user WHERE active = '1'")->fetchColumn();
    $totRevenueUnlock = round($totUnlockAcc * 990.00, 2); // ₹990 per $11 unlock access

    $stmtBiz      = $db->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one");
    $totBizInr    = (float)$stmtBiz->fetchColumn();

    $stmtToday    = $db->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one WHERE DATE(date) = CURDATE()");
    $todayBizInr  = (float)$stmtToday->fetchColumn();

    $stmtMonth    = $db->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one WHERE DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')");
    $monthBizInr  = (float)$stmtMonth->fetchColumn();

    $totWdPaid    = (float)$db->query("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE subject LIKE '%Withdraw%' AND type='Credit' AND status=1")->fetchColumn();
    $totWdPend    = (float)$db->query("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE subject LIKE '%Withdraw%' AND status=0")->fetchColumn();

    $piPaid       = (float)$db->query("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_roiinc")->fetchColumn();
    $psPaid       = (float)$db->query("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_daily_levelinc")->fetchColumn();
    $dbPaid       = (float)$db->query("SELECT COALESCE(SUM(installment_amount), 0) FROM tbl_direct_bonus_schedule WHERE status='CREDITED'")->fetchColumn();
    $miPaid       = (float)$db->query("SELECT COALESCE(SUM(payout_amount), 0) FROM tbl_mentor_income_schedule WHERE status='CREDITED'")->fetchColumn();
    $vipPaid      = (float)$db->query("SELECT COALESCE(SUM(reward_amount), 0) FROM tbl_vip_user_qualification WHERE reward_status='CREDITED'")->fetchColumn();
    $vipMonthly   = (float)$db->query("SELECT COALESCE(SUM(total_payout), 0) FROM tbl_vip_monthly_schedule WHERE status='CREDITED'")->fetchColumn();

    $totIncomePaid= round($piPaid + $psPaid + $dbPaid + $miPaid + $vipPaid + $vipMonthly, 2);

    $kycPending   = (int)$db->query("SELECT COUNT(*) FROM kyc WHERE status = '0'")->fetchColumn();
    $openTickets  = (int)$db->query("SELECT COUNT(*) FROM tbl_support_tickets WHERE status = 'OPEN'")->fetchColumn();

    return [
        'total_users'             => $totUsers,
        'active_users'            => $activeUsers,
        'inactive_users'          => $inactiveUsers,
        'total_unlock_access'     => $totUnlockAcc,
        'unlock_revenue_inr'      => $totRevenueUnlock,
        'total_investment_inr'    => round($totBizInr, 2),
        'today_business_inr'      => round($todayBizInr, 2),
        'monthly_business_inr'    => round($monthBizInr, 2),
        'total_withdrawal_paid'   => round($totWdPaid, 2),
        'pending_withdrawal'      => round($totWdPend, 2),
        'profit_income_paid'      => round($piPaid, 2),
        'profit_sharing_paid'     => round($psPaid, 2),
        'direct_bonus_paid'       => round($dbPaid, 2),
        'mentor_income_paid'      => round($miPaid, 2),
        'vip_club_income_paid'    => round($vipPaid + $vipMonthly, 2),
        'total_income_distributed'=> $totIncomePaid,
        'kyc_pending_count'       => $kycPending,
        'support_tickets_open'    => $openTickets
    ];
}

/**
 * Get active/all Ananta Package Configs.
 */
function getAnantaPackageConfigs($only_active = true, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db) return [];
    $sql = "SELECT * FROM tbl_ananta_package_config";
    if ($only_active) {
        $sql .= " WHERE status = 1";
    }
    $sql .= " ORDER BY id ASC";
    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Server-side Package Investment Validation.
 */
function validatePackageInvestment($package_id, $amount_usd, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || empty($package_id)) {
        return ['status' => false, 'message' => 'Package ID is required.'];
    }

    $amount_usd = (float)$amount_usd;
    if ($amount_usd <= 0) {
        return ['status' => false, 'message' => 'Investment amount must be a positive number greater than 0.'];
    }

    $stmt = $db->prepare("SELECT * FROM tbl_ananta_package_config WHERE package_id = :pkg_id LIMIT 1");
    $stmt->execute([':pkg_id' => strtoupper(trim($package_id))]);
    $pkg = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pkg) {
        return ['status' => false, 'message' => "Selected package '{$package_id}' does not exist."];
    }

    if ((int)$pkg['status'] !== 1) {
        return ['status' => false, 'message' => "Package '{$pkg['package_name']}' is currently inactive for new investments."];
    }

    $minUsd = (float)$pkg['min_investment_usd'];
    $maxUsd = ($pkg['max_investment_usd'] !== null) ? (float)$pkg['max_investment_usd'] : null;

    if ($amount_usd < $minUsd) {
        return ['status' => false, 'message' => "Investment amount $" . number_format($amount_usd, 2) . " is below minimum limit $" . number_format($minUsd, 2) . " for {$pkg['package_name']}."];
    }

    if ($maxUsd !== null && $amount_usd > $maxUsd) {
        return ['status' => false, 'message' => "Investment amount $" . number_format($amount_usd, 2) . " exceeds maximum limit $" . number_format($maxUsd, 2) . " for {$pkg['package_name']}."];
    }

    return [
        'status'  => true,
        'message' => 'Validation successful.',
        'package' => $pkg
    ];
}

/**
 * Process Ananta Package Investment with Immutable Historical Rules Snapshot.
 */
function processAnantaPackageInvestment($user_id, $package_id, $amount_usd, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$user_id) {
        return ['status' => 'error', 'message' => 'User authentication required.'];
    }

    $val = validatePackageInvestment($package_id, $amount_usd, $db);
    if (!$val['status']) {
        return ['status' => 'error', 'message' => $val['message']];
    }

    $pkg = $val['package'];
    $realFundUsd = (float)$amount_usd;
    $bonusPct    = (float)$pkg['bonus_percentage'];
    $bonusAmtUsd = ($bonusPct > 0) ? round($realFundUsd * ($bonusPct / 100.0), 2) : 0.00;
    $lockMonths  = (int)$pkg['lock_period_months'];
    $deductPct   = (float)$pkg['withdrawal_deduction_percent'];

    $inrAmount   = round($realFundUsd * 90.0, 2); // $1 = ₹90 conversion factor
    $cDate       = date('Y-m-d');
    $cTime       = date('H:i:s');
    $maturityDate= date('Y-m-d', strtotime("+{$lockMonths} months"));

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        // Lock user row FOR UPDATE
        $stmtUser = $db->prepare("SELECT userid, pin_wallet, bonus_30_wallet, total_package FROM user WHERE userid = :uid FOR UPDATE");
        $stmtUser->execute([':uid' => $user_id]);
        $uRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$uRow) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "User {$user_id} not found."];
        }

        $pinWalletBal = (float)$uRow['pin_wallet'];
        if ($pinWalletBal < $inrAmount) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Insufficient Fund Wallet balance. Required: ₹" . number_format($inrAmount, 2) . " ($" . number_format($realFundUsd, 2) . "), Available: ₹" . number_format($pinWalletBal, 2)];
        }

        // 1. Update user pin_wallet & total_package
        $updUser = $db->prepare("
            UPDATE user SET
                pin_wallet = pin_wallet - :inr_amt,
                total_package = total_package + :inr_amt,
                bonus_30_wallet = bonus_30_wallet + :bonus_usd,
                upgrade_date = :cdate,
                atime = :ctime
            WHERE userid = :uid
        ");
        $updUser->execute([
            ':inr_amt'   => $inrAmount,
            ':bonus_usd' => $bonusAmtUsd,
            ':cdate'     => $cDate,
            ':ctime'     => $cTime,
            ':uid'       => $user_id
        ]);

        // 2. Insert Investment Record into tbl_roi_one with Immutable Snapshot
        $insRoi = $db->prepare("
            INSERT INTO tbl_roi_one (
                user_id, name, package_code, real_fund_usd, bonus_percent_snapshot, bonus_amount_usd,
                lock_period_months, maturity_date, deduction_percent_snapshot, capital_withdrawal_status,
                package, percentage, date, closingdate, time, status, lock_day, capping, level, amount, totalincome, count
            ) VALUES (
                :uid, :pkg_name, :pkg_code, :real_fund_usd, :bonus_pct, :bonus_amt,
                :lock_months, :maturity_date, :deduct_pct, 'LOCKED',
                :inr_pkg, 3.00, :cdate, :cdate, :ctime, '0', :lock_days, :capping, 1, 0, 0, 0
            )
        ");
        $incLimitCapping = round($inrAmount * 2.0, 2);
        $insRoi->execute([
            ':uid'           => $user_id,
            ':pkg_name'      => substr($pkg['package_name'], 0, 20),
            ':pkg_code'      => $pkg['package_id'],
            ':real_fund_usd' => $realFundUsd,
            ':bonus_pct'     => $bonusPct,
            ':bonus_amt'     => $bonusAmtUsd,
            ':lock_months'   => $lockMonths,
            ':maturity_date' => $maturityDate,
            ':deduct_pct'    => $deductPct,
            ':inr_pkg'       => $inrAmount,
            ':cdate'         => $cDate,
            ':ctime'         => $cTime,
            ':lock_days'     => round($lockMonths * 30.4),
            ':capping'       => $incLimitCapping
        ]);

        $invId = $db->lastInsertId();

        // 3. Record Investment Transaction in tbl_transaction
        $insTxn = $db->prepare("
            INSERT INTO tbl_transaction
            (user_id, amount, type, subject, time, created_date, status)
            VALUES
            (:uid, :inr_amt, 'Credit', :subject, :ctime, :cdate, '1')
        ");
        $subject = "Ananta Package Investment - {$pkg['package_name']} ($ " . number_format($realFundUsd, 2) . ")";
        $insTxn->execute([
            ':uid'     => $user_id,
            ':inr_amt' => $inrAmount,
            ':subject' => $subject,
            ':ctime'   => $cTime,
            ':cdate'   => $cDate
        ]);

        // 4. Record Bonus Wallet Transaction if applicable
        if ($bonusAmtUsd > 0) {
            $insBonusTxn = $db->prepare("
                INSERT INTO tbl_transaction
                (user_id, amount, type, subject, time, created_date, status)
                VALUES
                (:uid, :bonus_usd, 'Credit', :subject, :ctime, :cdate, '1')
            ");
            $bonusSubject = "30% Bonus Package Credit ($ " . number_format($bonusAmtUsd, 2) . ") to 30% Bonus Wallet";
            $insBonusTxn->execute([
                ':uid'       => $user_id,
                ':bonus_usd' => $bonusAmtUsd,
                ':subject'   => $bonusSubject,
                ':ctime'     => $cTime,
                ':cdate'     => $cDate
            ]);
        }

        // 5. Generate Direct Bonus Schedule if helper exists
        if (function_exists('generateDirectBonusSchedule') && $invId) {
            generateDirectBonusSchedule($invId, $user_id, $inrAmount, $cDate, $db);
        }

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'            => 'success',
            'message'           => "Investment of $" . number_format($realFundUsd, 2) . " in {$pkg['package_name']} completed successfully!",
            'investment_id'     => $invId,
            'package_name'      => $pkg['package_name'],
            'real_fund_usd'     => $realFundUsd,
            'bonus_amount_usd'  => $bonusAmtUsd,
            'maturity_date'     => $maturityDate,
            'lock_period_months'=> $lockMonths
        ];

    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Investment error: ' . $e->getMessage()];
    }
}

/**
 * Server-side Capital Withdrawal Validation & Processing.
 */
function processCapitalWithdrawal($user_id, $investment_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$user_id || !$investment_id) {
        return ['status' => 'error', 'message' => 'User session & valid investment ID required.'];
    }

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        // Lock investment row FOR UPDATE
        $stmtInv = $db->prepare("SELECT * FROM tbl_roi_one WHERE id = :id AND user_id = :uid FOR UPDATE");
        $stmtInv->execute([':id' => $investment_id, ':uid' => $user_id]);
        $inv = $stmtInv->fetch(PDO::FETCH_ASSOC);

        if (!$inv) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Investment #{$investment_id} not found for user {$user_id}."];
        }

        if ($inv['capital_withdrawal_status'] === 'WITHDRAWN') {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Capital for investment #{$investment_id} has ALREADY been withdrawn."];
        }

        $cDate = date('Y-m-d');
        $maturityDate = $inv['maturity_date'];
        $lockMonths   = (int)$inv['lock_period_months'];
        $invDate      = $inv['date'];

        // Strict Server-Side Lock Check: Calculate if lock period / maturity date has passed
        $computedMaturity = $maturityDate ?: date('Y-m-d', strtotime("+{$lockMonths} months", strtotime($invDate)));
        if ($cDate < $computedMaturity) {
            if ($inLocalTxn) $db->rollBack();
            return [
                'status'  => 'error',
                'message' => "Capital is locked for {$lockMonths} months. Maturity date is {$computedMaturity}. Early capital withdrawal is blocked."
            ];
        }

        $realFundUsd  = (float)($inv['real_fund_usd'] > 0 ? $inv['real_fund_usd'] : round(((float)$inv['package']) / 90.0, 2));
        $deductPct    = (float)($inv['deduction_percent_snapshot'] > 0 ? $inv['deduction_percent_snapshot'] : 15.00);
        $deductAmtUsd = round($realFundUsd * ($deductPct / 100.0), 2);
        $netWdUsd     = round($realFundUsd - $deductAmtUsd, 2);
        $netWdInr     = round($netWdUsd * 90.0, 2);
        $bonusAmtUsd  = (float)$inv['bonus_amount_usd'];

        // 1. Mark investment capital withdrawal status as WITHDRAWN
        $updInv = $db->prepare("UPDATE tbl_roi_one SET capital_withdrawal_status = 'WITHDRAWN', status = '1' WHERE id = :id");
        $updInv->execute([':id' => $investment_id]);

        // 2. Reconcile Bonus Wallet if 30% Bonus Package
        if ($bonusAmtUsd > 0) {
            $stmtUser = $db->prepare("SELECT bonus_30_wallet FROM user WHERE userid = :uid FOR UPDATE");
            $stmtUser->execute([':uid' => $user_id]);
            $userBonusBal = (float)$stmtUser->fetchColumn();

            $reconciledBonus = min($userBonusBal, $bonusAmtUsd);
            if ($reconciledBonus > 0) {
                $db->prepare("UPDATE user SET bonus_30_wallet = bonus_30_wallet - :b_amt WHERE userid = :uid")->execute([':b_amt' => $reconciledBonus, ':uid' => $user_id]);

                // Bonus reconciliation transaction log
                $db->prepare("
                    INSERT INTO tbl_transaction (user_id, amount, type, subject, time, created_date, status)
                    VALUES (:uid, :b_amt, 'Debit', :sub, CURTIME(), CURDATE(), '1')
                ")->execute([
                    ':uid'   => $user_id,
                    ':b_amt' => $reconciledBonus,
                    ':sub'   => "30% Bonus Wallet Reconciled/Deducted on Capital Withdrawal (Inv #{$investment_id})"
                ]);
            }
        }

        // 3. Credit Net Withdrawal Amount to Main Amount / Wallet
        $db->prepare("UPDATE user SET amount = amount + :net_inr WHERE userid = :uid")->execute([':net_inr' => $netWdInr, ':uid' => $user_id]);

        // 4. Record Capital Withdrawal Transaction
        $subject = "Capital Withdrawal Paid - Inv #{$investment_id} (Real Fund: $" . number_format($realFundUsd, 2) . " - 15% Deduction: $" . number_format($deductAmtUsd, 2) . " = Net: $" . number_format($netWdUsd, 2) . ")";
        $db->prepare("
            INSERT INTO tbl_transaction (user_id, amount, type, subject, time, created_date, status)
            VALUES (:uid, :net_usd, 'Credit', :sub, CURTIME(), CURDATE(), '1')
        ")->execute([
            ':uid'     => $user_id,
            ':net_usd' => $netWdUsd,
            ':sub'     => $subject
        ]);

        // 5. Record Audit Entry in tbl_capital_withdrawal_request
        $db->prepare("
            INSERT INTO tbl_capital_withdrawal_request
            (user_id, investment_id, package_code, real_fund_usd, deduction_percent, deduction_amount_usd, net_withdrawal_usd, bonus_reconciled_usd, status, requested_at, processed_at)
            VALUES
            (:uid, :inv_id, :pkg_code, :real_fund, :deduct_pct, :deduct_amt, :net_usd, :bonus_rec, 'PAID', NOW(), NOW())
        ")->execute([
            ':uid'        => $user_id,
            ':inv_id'     => $investment_id,
            ':pkg_code'   => $inv['package_code'] ?: 'ANANTA',
            ':real_fund'  => $realFundUsd,
            ':deduct_pct' => $deductPct,
            ':deduct_amt' => $deductAmtUsd,
            ':net_usd'    => $netWdUsd,
            ':bonus_rec'  => $bonusAmtUsd
        ]);

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'               => 'success',
            'message'              => "Capital withdrawal of $" . number_format($netWdUsd, 2) . " processed successfully after 15% deduction.",
            'investment_id'        => $investment_id,
            'real_fund_usd'        => $realFundUsd,
            'deduction_amount_usd' => $deductAmtUsd,
            'net_withdrawal_usd'   => $netWdUsd,
            'net_withdrawal_inr'   => $netWdInr
        ];

    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Withdrawal processing error: ' . $e->getMessage()];
    }
}

if (!function_exists('getAdminActivationRevenueTotal')) {
    function getAdminActivationRevenueTotal($pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db) return ['total_usd' => 0.0, 'total_inr' => 0.0, 'count' => 0];

        try {
            $stmt = $db->prepare("SELECT COUNT(*) as cnt, COALESCE(SUM(amount_usd), 0) as tot_usd, COALESCE(SUM(amount_inr), 0) as tot_inr FROM tbl_account_activation");
            $stmt->execute();
            $r = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            return [
                'total_usd' => round((float)($r['tot_usd'] ?? 0), 2),
                'total_inr' => round((float)($r['tot_inr'] ?? 0), 2),
                'count'     => (int)($r['cnt'] ?? 0)
            ];
        } catch (Exception $e) {
            return ['total_usd' => 0.0, 'total_inr' => 0.0, 'count' => 0];
        }
    }
}

if (!defined('DEFAULT_USD_TO_INR')) {
    define('DEFAULT_USD_TO_INR', 90.0);
}

if (!function_exists('getUSDToINRRate')) {
    function getUSDToINRRate($pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT setting_value FROM tbl_system_control WHERE setting_key = 'usd_to_inr' LIMIT 1");
                $stmt->execute();
                $val = $stmt->fetchColumn();
                if ($val !== false && is_numeric($val) && (float)$val > 0) {
                    return (float)$val;
                }
            } catch (Exception $e) {
                // fallback
            }
        }
        return DEFAULT_USD_TO_INR;
    }
}

if (!function_exists('getUserCurrency')) {
    function getUserCurrency($userid = null, $pdoConnection = null) {
        if (isset($_SESSION['currency']) && in_array(strtoupper($_SESSION['currency']), ['USD', 'INR'])) {
            return strtoupper($_SESSION['currency']);
        }
        $uid = $userid ?: ($_SESSION['userid'] ?? null);
        if ($uid) {
            global $pdo;
            $db = $pdoConnection ?: $pdo;
            if ($db) {
                try {
                    $stmt = $db->prepare("SELECT currency_preference FROM user WHERE userid = :uid");
                    $stmt->execute([':uid' => $uid]);
                    $pref = $stmt->fetchColumn();
                    if ($pref && in_array(strtoupper($pref), ['USD', 'INR'])) {
                        $_SESSION['currency'] = strtoupper($pref);
                        return strtoupper($pref);
                    }
                } catch (Exception $e) {
                    // fallback
                }
            }
        }
        return 'USD';
    }
}

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($currency = null) {
        $curr = $currency ? strtoupper(trim($currency)) : getUserCurrency();
        return ($curr === 'INR') ? '₹' : '$';
    }
}

if (!function_exists('convertCurrency')) {
    function convertCurrency($amountInUSD, $targetCurrency = null, $pdoConnection = null) {
        $curr = $targetCurrency ? strtoupper(trim($targetCurrency)) : getUserCurrency(null, $pdoConnection);
        $amt = (float)$amountInUSD;
        if ($curr === 'INR') {
            $rate = getUSDToINRRate($pdoConnection);
            return round($amt * $rate, 2);
        }
        return round($amt, 2);
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amountInUSD, $targetCurrency = null, $includeSymbol = true, $pdoConnection = null) {
        $curr = $targetCurrency ? strtoupper(trim($targetCurrency)) : getUserCurrency(null, $pdoConnection);
        $converted = convertCurrency($amountInUSD, $curr, $pdoConnection);
        $symbol = $includeSymbol ? getCurrencySymbol($curr) : '';
        return $symbol . number_format($converted, 2);
    }
}

if (!function_exists('getUserAccountActivationStatus')) {
    function getUserAccountActivationStatus($userid, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || !$userid) {
            return [
                'status'         => 'INACTIVE',
                'active_flag'    => 0,
                'is_active'      => false,
                'is_expired'     => false,
                'start_date'     => null,
                'expiry_date'    => null,
                'remaining_days' => 0,
                'status_label'   => 'INACTIVE'
            ];
        }

        $stmt = $db->prepare("
            SELECT userid, name, active, activation_start_date, activation_expiry_date 
            FROM user WHERE userid = :uid
        ");
        $stmt->execute([':uid' => $userid]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$u) {
            return [
                'status'         => 'INACTIVE',
                'active_flag'    => 0,
                'is_active'      => false,
                'is_expired'     => false,
                'start_date'     => null,
                'expiry_date'    => null,
                'remaining_days' => 0,
                'status_label'   => 'INACTIVE'
            ];
        }

        $activeFlag = (int)($u['active'] ?? 0);
        $startDate  = $u['activation_start_date'] ?: null;
        $expiryDate = $u['activation_expiry_date'] ?: null;
        $nowTs      = time();

        if ($activeFlag === 1 && $expiryDate) {
            $expiryTs = strtotime($expiryDate);
            if ($expiryTs > $nowTs) {
                $remSecs = $expiryTs - $nowTs;
                $remDays = (int)ceil($remSecs / 86400);
                return [
                    'status'         => 'ACTIVE',
                    'active_flag'    => 1,
                    'is_active'      => true,
                    'is_expired'     => false,
                    'start_date'     => $startDate,
                    'expiry_date'    => $expiryDate,
                    'remaining_days' => $remDays,
                    'status_label'   => 'ACTIVE'
                ];
            } else {
                $db->prepare("UPDATE user SET active = '0' WHERE userid = :uid AND active = '1'")->execute([':uid' => $userid]);
                return [
                    'status'         => 'EXPIRED',
                    'active_flag'    => 0,
                    'is_active'      => false,
                    'is_expired'     => true,
                    'start_date'     => $startDate,
                    'expiry_date'    => $expiryDate,
                    'remaining_days' => 0,
                    'status_label'   => 'EXPIRED'
                ];
            }
        } elseif ($expiryDate && strtotime($expiryDate) <= $nowTs) {
            return [
                'status'         => 'EXPIRED',
                'active_flag'    => 0,
                'is_active'      => false,
                'is_expired'     => true,
                'start_date'     => $startDate,
                'expiry_date'    => $expiryDate,
                'remaining_days' => 0,
                'status_label'   => 'EXPIRED'
            ];
        }

        return [
            'status'         => 'INACTIVE',
            'active_flag'    => 0,
            'is_active'      => false,
            'is_expired'     => false,
            'start_date'     => null,
            'expiry_date'    => null,
            'remaining_days' => 0,
            'status_label'   => 'INACTIVE'
        ];
    }
}

if (!function_exists('getAdminActivationHistory')) {
    function getAdminActivationHistory($filters = [], $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db) return [];

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = "(a.activator_user_id LIKE :uid OR a.target_user_id LIKE :uid)";
            $params[':uid'] = '%' . trim($filters['user_id']) . '%';
        }
        if (!empty($filters['activator_id'])) {
            $where[] = "a.activator_user_id LIKE :actid";
            $params[':actid'] = '%' . trim($filters['activator_id']) . '%';
        }
        if (!empty($filters['target_id'])) {
            $where[] = "a.target_user_id LIKE :tgtid";
            $params[':tgtid'] = '%' . trim($filters['target_id']) . '%';
        }
        if (!empty($filters['type'])) {
            $where[] = "a.activation_type = :type";
            $params[':type'] = trim($filters['type']);
        }
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $where[] = "DATE(a.created_at) BETWEEN :fdate AND :tdate";
            $params[':fdate'] = trim($filters['from_date']);
            $params[':tdate'] = trim($filters['to_date']);
        }

        $whereSql = implode(" AND ", $where);
        $sql = "
            SELECT 
                a.*,
                u1.name as activator_name,
                u2.name as target_name
            FROM tbl_account_activation a
            LEFT JOIN user u1 ON u1.userid = a.activator_user_id
            LEFT JOIN user u2 ON u2.userid = a.target_user_id
            WHERE {$whereSql}
            ORDER BY a.id DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
