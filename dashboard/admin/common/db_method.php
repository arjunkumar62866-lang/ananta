<?php
require_once 'common/connection.php'; 

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

?>
