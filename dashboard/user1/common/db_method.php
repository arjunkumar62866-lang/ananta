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


function userid($userid) {
    global $pdo; // assuming $pdo is your PDO connection

    try {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
        $stmt->execute([':userid' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return false; // User exists
        } else {
            return true;  // User does not exist
        }
    } catch (PDOException $e) {
        // Optionally log error
        return false;
    }
}

function sponser($sponserid) {
    global $pdo; // assuming $pdo is your PDO connection

    try {
        $stmt = $pdo->prepare("SELECT userid FROM user WHERE userid = :sponserid");
        $stmt->execute([':sponserid' => $sponserid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return true;  // Sponsor exists
        } else {
            return false; // Sponsor does not exist
        }
    } catch (PDOException $e) {
        // Optionally log error
        return false;
    }
}


function rank_reward($userid)
{
    global $pdo;
    $time = date("H:i:s");

    // Step 1: get current user data
    $stmt = $pdo->prepare("SELECT rank FROM user WHERE userid = :id");
    $stmt->execute([':id' => $userid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) return false;

    // Existing values
    $current_rank = $user['rank'];   

    // Step 2: Get left & right directs
    $left = getmydirectactiveleft($userid);
    $right = getmydirectactiveright($userid);
    

    // Step 3: Fetch all reward levels sorted by requirement
    $stmt = $pdo->prepare("SELECT * FROM tbl_rewardlevel ORDER BY act_req_direct DESC, id DESC");
    $stmt->execute();
    $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($levels as $lvl) {

        $req = (int)$lvl['req_direct'];   // required directs (left+right)
        $reward = (int)$lvl['reward'];        // reward amount
        $rank = $lvl['rank'];                 // Manager, Gold, etc.
        $rank_percentage = $lvl['ranking_percentage'];
        
        // Skip already achieved ranks
        if ($current_rank == $rank){
            if($rank == "Red Diamond" || $rank == "Director"){
                $stmt = $pdo->prepare("SELECT * FROM tbl_royalty_user WHERE userid= :uid");
                $stmt->execute([':uid'=>$userid]);
                $exists = $stmt->fetchColumn();
                
                if($exists==0){
                    $stmt = $pdo->prepare("INSERT INTO tbl_royalty_user (userid, level, created_date, time, full_status)
                    VALUES (:uid, :level, NOW(), :time, 0)");
                    
                    $stmt->execute([
                        ':uid'=> $userid,
                        ':level'=> $rank,
                        ':time'=> $time
                    ]);
                }
            }
            return;
        };

        // Step 4: Check eligibility → both sides must match requirement
          
        if ($left >= $req && $right >= $req &&(getmydirectactive($userid)>=10)) {

            // Step 5: Update user wallet & income

            $stmtu = $pdo->prepare("
                UPDATE user 
                SET amount = amount + :amt, total_inc = total_inc + :amt, rank = :rk , ranking_percentage = :ranking_per
                WHERE userid = :uid
            ");

            $stmtu->execute([
                ':amt' => $reward,
                ':rk'  => $rank,
                ':uid' => $userid,
                ':ranking_per'=> $rank_percentage,
            ]);

            // Step 6: Insert rewardinc table transaction
            $subject = "$reward Reward Income ($rank) Achieved";
            

            $stmt3 = $pdo->prepare("
                INSERT INTO tbl_rewardinc
                    (user_id, type, subject, time, created_date, status, amount)
                VALUES
                    (:uid, 'reward', :subj, :t, NOW(), 1, :amt)
            ");

            $stmt3->execute([
                ':uid' => $userid,
                ':subj'=> $subject,
                ':t'   => $time,
                ':amt' => $reward
            ]);
            
            // Step 6: Insert transaction
            $subject = "$reward Reward Income ($rank) Achieved";
            $time = date("H:i:s");

            $stmt3 = $pdo->prepare("
                INSERT INTO tbl_transaction 
                    (user_id, type, subject, time, created_date, status, amount)
                VALUES
                    (:uid, 'credit', :subj, :t, NOW(), 1, :amt)
            ");

            $stmt3->execute([
                ':uid' => $userid,
                ':subj'=> $subject,
                ':t'   => $time,
                ':amt' => $reward
            ]);

            return true; // reward granted
        }
    }

    return false; // no reward matched
}




function getmydirectidleft($userid)
{
    global $pdo;

    try {
        $sql = "
            SELECT COUNT(DISTINCT downline_id) AS total
            FROM tbl_userlevel_a
            WHERE sponser_id = :userid
              AND level = 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}


function getmydirectidright($userid)
{
    global $pdo;

    try {
        $sql = "
            SELECT COUNT(DISTINCT downline_id) AS total
            FROM tbl_userlevel_b
            WHERE sponser_id = :userid
              AND level = 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}

// function getmydirectidleft($userid, $side)
// {
//     global $pdo; // assuming $pdo is your PDO connection

//     try {
//         $stmt = $pdo->prepare("SELECT COUNT(*) AS alluser FROM user WHERE sponserid = :userid AND join_side = :side");
//         $stmt->execute([
//             ':userid' => $userid,
//             ':side'   => $side
//         ]);

//         $row = $stmt->fetch(PDO::FETCH_ASSOC);
//         return $row ? (int)$row['alluser'] : 0;

//     } catch (PDOException $e) {
//         // Optionally log or handle the error
//         return 0;
//     }
// }


// function getmydirectidright($userid, $side)
// {
//     global $pdo; // assuming $pdo is your PDO connection

//     try {
//         $stmt = $pdo->prepare("SELECT COUNT(*) AS alluser FROM user WHERE sponserid = :userid AND join_side = :side");
//         $stmt->execute([
//             ':userid' => $userid,
//             ':side'   => $side
//         ]);

//         $row = $stmt->fetch(PDO::FETCH_ASSOC);
//         return $row ? (int)$row['alluser'] : 0;

//     } catch (PDOException $e) {
//         // Optionally log or handle the error
//         return 0;
//     }
// }



function getUserTreeData($userid)
{
    global $pdo; // assuming $pdo is your PDO connection

    $sql = "SELECT * FROM tree WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userid' => $userid]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $userdata = [
            "left"       => $row['left_id'],
            "right"      => $row['right_id'],
            "leftcount"  => $row['leftcount'],
            "rightcount" => $row['rightcount'],
            "leftpv"     => $row['leftpv'],
            "rightpv"    => $row['rightpv'],
            "lefttotal"  => $row['lefttotal'],
            "righttotal" => $row['righttotal'],
            "rightsp" => $row['rightsp'],
            "leftsp" => $row['leftsp'],
        ];

        return $userdata;
    }

    return null; // return null if no user found
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
            'bitly'       => $row['bitly'] ?? '',
            'matching_amount' => $row['matching_amount'] ?? ''
        ];
    }

    return null;
}


function loginUser($userid, $password, $pdo)
{
    // Remove first two characters from user ID
    $userid = substr($userid, 2);

    // Optimized query: only fetch required columns, use LIMIT 1
    $stmt = $pdo->prepare("
        SELECT userid, pass, status
        FROM user
        WHERE userid = :userid 
        AND status IN (0, 1)
        LIMIT 1
    ");
    $stmt->execute(['userid' => $userid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Compare passwords (plain-text for now to match old system)
    if ($user && $user['pass'] === $password) {
        $_SESSION['userid'] = $user['userid']; // match old system

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



// function registerUser($referrer_id, $name, $email, $mobile, $password, $terms_accepted, $pdo)
// {
    
//     if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
//         return [
//             'status' => false,
//             'message' => 'All fields are required!'
//         ];
//     }
  
//     if (!$terms_accepted) {
//         return [
//             'status' => false,
//             'message' => 'You must agree to the terms and conditions.'
//         ];
//     }

//     $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
//     $stmt->execute(['email' => $email]);

//     if ($stmt->rowCount() > 0) {
//         return [
//             'status' => false,
//             'message' => 'Email already registered!'
//         ];
//     }

//     // Hash the password
//     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

//     // Insert into database
//     $stmt = $pdo->prepare("INSERT INTO users (referrer_id, name, email, mobile, password, terms_accepted)
//                           VALUES (:referrer_id, :name, :email, :mobile, :password, :terms_accepted)");

//     $success = $stmt->execute([
//         'referrer_id'    => $referrer_id,
//         'name'           => $name,
//         'email'          => $email,
//         'mobile'         => $mobile,
//         'password'       => $hashedPassword,
//         'terms_accepted' => $terms_accepted
//     ]);

//     if ($success) {
//         return [
//             'status' => true,
//             'message' => 'Registration successful!'
//         ];
//     } else {
//         return [
//             'status' => false,
//             'message' => 'Registration failed. Try again.'
//         ];
//     }
// }

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
            "total_inc" => $rowuser["total_inc"],
            "join_side"=> $rowuser["join_side"],
            "plan" => $rowuser["plan"]
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


function getmydirect($direct)
{
    global $pdo;
    $sql = "SELECT COUNT(*) as alluser 
            FROM user tbsign  
            INNER JOIN tbl_sponsor tbspon 
            ON tbspon.referral_id = tbsign.userid  
            WHERE tbspon.sponsor_id = :sponsor_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponsor_id', $direct, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $rowac = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rowac['alluser'];
    }

    return 0; // If no records found
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


function getActiveDownlineCount($sponsorId)
{
    global $pdo;

    $sql = "
        SELECT COUNT(DISTINCT u.userid) AS total
        FROM user u
        INNER JOIN (
            SELECT downline_id FROM tbl_userlevel_a WHERE sponser_id = :sponsor_id
            UNION ALL
            SELECT downline_id FROM tbl_userlevel_b WHERE sponser_id = :sponsor_id
        ) d ON d.downline_id = u.userid
        
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponsor_id', $sponsorId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() ?: 0;
}


function insert_userlevel_a($sponserid, $downlineid, $level)
{
    global $pdo;

    $sql = "INSERT INTO tbl_userlevel_a (sponser_id, downline_id, level, date) 
            VALUES (:sponser_id, :downline_id, :level, CURDATE())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponser_id', $sponserid, PDO::PARAM_STR);
    $stmt->bindParam(':downline_id', $downlineid, PDO::PARAM_STR);
    $stmt->bindParam(':level', $level, PDO::PARAM_INT);

    $stmt->execute();
}
function insert_userlevel_b($sponserid, $downlineid, $level)
{
    global $pdo;

    $sql = "INSERT INTO tbl_userlevel_b (sponser_id, downline_id, level, date) 
            VALUES (:sponser_id, :downline_id, :level, CURDATE())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sponser_id', $sponserid, PDO::PARAM_STR);
    $stmt->bindParam(':downline_id', $downlineid, PDO::PARAM_STR);
    $stmt->bindParam(':level', $level, PDO::PARAM_INT);

    $stmt->execute();
}

function incometotalnew($pdo, $table, $userid, $subject)
{
    $sql = "SELECT SUM(amount) AS totalamount 
            FROM {$table} 
            WHERE user_id = :userid 
            AND subject LIKE :subject";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':userid'  => $userid,
        ':subject' => "%{$subject}%"
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $row['totalamount'] : 0;
}

function incometotalnew_exact_subject($pdo, $table, $userid, $subject)
{
    $sql = "SELECT SUM(amount) AS totalamount 
            FROM {$table} 
            WHERE user_id = :userid 
            AND subject LIKE :subject";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':userid'  => $userid,
        ':subject' => $subject
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $row['totalamount'] : 0;
}



function incometotalnewdate($date, $table, $userid, $subject)
{
    global $pdo; // Assuming $pdo is your PDO connection
    $sqluser = "SELECT SUM(amount) as totalamount 
                FROM $table 
                WHERE created_date = '$date' 
                AND user_id = '$userid' 
                AND subject LIKE '%$subject%'";

    $resultuser = $pdo->query($sqluser);

    if ($resultuser->rowCount() > 0) {
        while ($rowuser = $resultuser->fetch(PDO::FETCH_ASSOC)) {
            $userdata = $rowuser['totalamount'];
            return $userdata;
        }
    }
}


function getlevelDirectbusiness($userid, $level)
{
    global $pdo;
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

function getlevelbusiness($userid, $level)
{
    global $pdo;

    $sql = "
        SELECT SUM(u.total_package) AS totalbusiness
        FROM user u
        INNER JOIN (
            SELECT downline_id
            FROM tbl_userlevel_a
            WHERE sponser_id = :userid AND level = :level

            UNION ALL

            SELECT downline_id
            FROM tbl_userlevel_b
            WHERE sponser_id = :userid AND level = :level
        ) t ON t.downline_id = u.userid
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindValue(':level', $level, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() ?: 0;
}

function leftLevelBusiness($userid, $level)
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

function rightLevelBusiness($userid, $level)
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




function gettotallevelbusiness($userid)
{
    global $pdo; 

    $totallevelbusiness =
        getlevelbusiness($userid, 1) +
        getlevelbusiness($userid, 2) +
        getlevelbusiness($userid, 3) +
        getlevelbusiness($userid, 4) +
        getlevelbusiness($userid, 5) +
        getlevelbusiness($userid, 6) +
        getlevelbusiness($userid, 7) +
        getlevelbusiness($userid, 8) +
        getlevelbusiness($userid, 9) +
        getlevelbusiness($userid, 10) +
        getlevelbusiness($userid, 11) +
        getlevelbusiness($userid, 12) +
        getlevelbusiness($userid, 13) +
        getlevelbusiness($userid, 14) +
        getlevelbusiness($userid, 15) +
        getlevelbusiness($userid, 16) +
        getlevelbusiness($userid, 17) +
        getlevelbusiness($userid, 18) +
        getlevelbusiness($userid, 19)+
        getlevelbusiness($userid, 20);

    return $totallevelbusiness;
}
function gettotallevelbusinessleft($userid)
{
    global $pdo; 

    $totallevelbusiness =
        leftLevelBusiness($userid, 1) +
        leftLevelBusiness($userid, 2) +
        leftLevelBusiness($userid, 3) +
        leftLevelBusiness($userid, 4) +
        leftLevelBusiness($userid, 5) +
        leftLevelBusiness($userid, 6) +
        leftLevelBusiness($userid, 7) +
        leftLevelBusiness($userid, 8) +
        leftLevelBusiness($userid, 9) +
        leftLevelBusiness($userid, 10) +
        leftLevelBusiness($userid, 11) +
        leftLevelBusiness($userid, 12) +
        leftLevelBusiness($userid, 13) +
        leftLevelBusiness($userid, 14) +
        leftLevelBusiness($userid, 15) +
        leftLevelBusiness($userid, 16) +
        leftLevelBusiness($userid, 17) +
        leftLevelBusiness($userid, 18) +
        leftLevelBusiness($userid, 19)+
        leftLevelBusiness($userid, 20);

    return $totallevelbusiness;
}
function gettotallevelbusinessright($userid)
{
    global $pdo; 

    $totallevelbusiness =
        rightLevelBusiness($userid, 1) +
        rightLevelBusiness($userid, 2) +
        rightLevelBusiness($userid, 3) +
        rightLevelBusiness($userid, 4) +
        rightLevelBusiness($userid, 5) +
        rightLevelBusiness($userid, 6) +
        rightLevelBusiness($userid, 7) +
        rightLevelBusiness($userid, 8) +
        rightLevelBusiness($userid, 9) +
        rightLevelBusiness($userid, 10) +
        rightLevelBusiness($userid, 11) +
        rightLevelBusiness($userid, 12) +
        rightLevelBusiness($userid, 13) +
        rightLevelBusiness($userid, 14) +
        rightLevelBusiness($userid, 15) +
        rightLevelBusiness($userid, 16) +
        rightLevelBusiness($userid, 17) +
        rightLevelBusiness($userid, 18) +
        rightLevelBusiness($userid, 19)+
        rightLevelBusiness($userid, 20);

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


function getroionedatanew($userid)
{
    global $pdo;

    $sqluser = "SELECT * 
                FROM tbl_roi_one 
                WHERE user_id = :userid AND status = '0' 
                ORDER BY id DESC 
                LIMIT 1";

    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([':userid' => $userid]);

    // fetch result
    $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowuser) {
        $roionedata = array(
            "level"      => $rowuser['level'],
            "package"    => $rowuser["package"],
            "percentage" => $rowuser["percentage"],
            "count"      => $rowuser["count"],
            "amount"     => $rowuser["amount"],
            "date"       => $rowuser["date"],
            "time"       => $rowuser["time"], 
            "status"     => $rowuser["status"],
        );
        return $roionedata;
    }

    // return null if no record found
    return null;
}

function getpercent($amount,$percent)
{
    $registrtionamont=$amount*$percent/100;
    return $registrtionamont;
}

function getpercentage()
{
    global $pdo; 

    $sql = "SELECT * FROM tbl_levelpercantage LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $percenset = array(
            "level1" => $row['level1'],
            "level2" => $row['level2'],
            "level3" => $row['level3'],
            "level4" => $row['level4'],
            "level5" => $row['level5'],
            "level6" => $row['level6'],
            "level7" => $row['level7']
        );
        return $percenset;
    }

    return null; 
}


function updatedatabysponserid($userid, $amount, $transactionamount)
{
    try {
        $sql = "UPDATE user 
                   SET amount = amount + :amount, 
                       total_inc = total_inc + :transactionamount 
                 WHERE userid = :userid";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':transactionamount', $transactionamount, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

        return $stmt->execute(); 
    } catch (PDOException $e) {
        error_log("Error updating sponsor: " . $e->getMessage());
        return false;
    }
}

function insert_transction($table, $userid, $amount, $trasction_type, $time, $cdtype)
{
    global $pdo;

    $updatedate = newtime("date");
    $updatetime = newtime("time");

    try {
        $sql = "INSERT INTO $table (user_id, subject, type, amount, status, a_status, time, created_date) 
                VALUES (:userid, :trasction_type, :cdtype, :amount, '1', '0', :time, :updatedate)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':trasction_type', $trasction_type, PDO::PARAM_STR);
        $stmt->bindParam(':cdtype', $cdtype, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':updatedate', $updatedate, PDO::PARAM_STR);

        $stmt->execute();

    } catch (PDOException $e) {
        echo "Error inserting transaction: " . $e->getMessage();
    }
}



function getroionedata($userid)
{
    global $pdo; 
    $sql = "SELECT * 
            FROM tbl_roi_one 
            WHERE user_id = :userid AND status = '1' 
            ORDER BY id DESC 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':userid' => $userid]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $roionedata = [
            "level"      => $row['level'],
            "package"    => $row["package"],
            "percentage" => $row["percentage"],
            "count"      => $row["count"],
            "amount"     => $row["amount"],
            "date"       => $row["date"],
            "time"       => $row["time"], 
            "status"     => $row["status"],
        ];

        return $roionedata;
    }

    return null;
}

// Count only active descendants
function getSubtreeCount($pdo, $userid) {
    if (!$userid) return 0;

    $stmt = $pdo->prepare("SELECT left_id, right_id, is_active FROM tree WHERE userid = :uid LIMIT 1");
    $stmt->execute([':uid' => $userid]);
    $node = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$node) return 0;

    $count = 0;

    if ($node['left_id']) {
        // check if left child is active
        $stmt2 = $pdo->prepare("SELECT is_active FROM tree WHERE userid = :uid LIMIT 1");
        $stmt2->execute([':uid' => $node['left_id']]);
        $child = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($child && $child['is_active']) {
            $count += 1 + getSubtreeCount($pdo, $node['left_id']);
        }
    }

    if ($node['right_id']) {
        // check if right child is active
        $stmt3 = $pdo->prepare("SELECT is_active FROM tree WHERE userid = :uid LIMIT 1");
        $stmt3->execute([':uid' => $node['right_id']]);
        $child = $stmt3->fetch(PDO::FETCH_ASSOC);

        if ($child && $child['is_active']) {
            $count += 1 + getSubtreeCount($pdo, $node['right_id']);
        }
    }

    return $count;
}

function activateUser($pdo, $userid) {
    // mark this user as active
    $pdo->prepare("UPDATE tree SET is_active = 1 WHERE userid = :uid")->execute([':uid' => $userid]);

    // update counts up the chain
    updateParentCounts($pdo, $userid);
}




function updateTreeCounts($pdo, $userid) {
    
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE left_id = :uid OR right_id = :uid");
    $stmt->execute([':uid' => $userid]);
    $rf = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$rf){ 
        return; 
    }

    $temp_underuserid = $rf["userid"];  
    $side = ($rf["left_id"] == $userid) ? "left" : "right"; 
    
    $temp_side_count = $side.'count'; 
    $total_count = 1;

    while($total_count > 0){
        $stmt2 = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid");
        $stmt2->execute([':uid' => $temp_underuserid]);
        $r = $stmt2->fetch(PDO::FETCH_ASSOC);

        if(!$r){ break; }

       
        $current_temp_side_count = $r[$temp_side_count] + 1;
        $stmt3 = $pdo->prepare("UPDATE tree SET $temp_side_count = :count WHERE userid = :uid");
        $stmt3->execute([':count' => $current_temp_side_count, ':uid' => $temp_underuserid]);

        
        $next_under_userid = getUnderId($pdo, $temp_underuserid);    
        $temp_side = getUnderIdPlace($pdo, $temp_underuserid);      
        $temp_side_count = $temp_side.'count';
        $temp_underuserid = $next_under_userid; 
        
        
        if(empty($temp_underuserid)){
            $total_count = 0;
        }
    }
}






function getUnderId($pdo, $userid){
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE left_id = :uid OR right_id = :uid");
    $stmt->execute([':uid' => $userid]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return $r ? $r['userid'] : null;
}

function getUnderIdPlace($pdo, $userid){
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE left_id = :uid OR right_id = :uid");
    $stmt->execute([':uid' => $userid]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$r) return null;
    return ($r['left_id'] == $userid) ? "left" : "right";
}


function find_parent($sponser, $side, $pdo) {
    // Prepare the query
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid");
    $stmt->bindParam(':userid', $sponser, PDO::PARAM_STR);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Sponsor not found in tree
        return null;
    }

    // Determine which side to follow
    $side_column = $side . '_id';
    $side_user = $row[$side_column] ?? '';

    if (!empty($side_user)) {
        // Recursive call
        return find_parent($side_user, $side, $pdo);
    } else {
        // Leaf found
        return $sponser;
    }
}





?>
