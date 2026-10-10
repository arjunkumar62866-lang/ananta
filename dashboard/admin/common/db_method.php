<?php

if (!function_exists('cleanUserId')) {
    function cleanUserId($userid) {
        if (empty($userid)) return '';
        $str = trim((string)$userid);
        if (preg_match('/^(AN|ANANTA)([0-9]+)$/i', $str, $matches)) {
            return $matches[2];
        }
        return $str;
    }
}

if (!function_exists('ensureWithdrawalRemarksColumnExists')) {
    function ensureWithdrawalRemarksColumnExists($pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db) return;

        try {
            $stmt = $db->query("SHOW COLUMNS FROM tbl_transaction LIKE 'admin_remarks'");
            if ($stmt->fetch() === false) {
                $db->exec("ALTER TABLE tbl_transaction ADD COLUMN admin_remarks TEXT DEFAULT NULL AFTER api_message");
            }
        } catch (Exception $e) {}

        try {
            $stmt2 = $db->query("SHOW COLUMNS FROM tbl_capital_withdrawal_request LIKE 'admin_remarks'");
            if ($stmt2->fetch() === false) {
                $db->exec("ALTER TABLE tbl_capital_withdrawal_request ADD COLUMN admin_remarks TEXT DEFAULT NULL AFTER processed_at");
            }
        } catch (Exception $e) {}
    }
}

if (!function_exists('cleanupGlobalTreeDuplicates')) {
    /**
     * Generic global tree duplicate cleaner.
     * Uses TREE TABLE as the sole source of truth — NOT user.underuserid.
     * When a child appears under multiple parents: keeps the LAST parent
     * (highest userid = most recently assigned), removes all stale ones.
     * After cleanup, syncs user.underuserid + user.join_side from tree.
     * Never modifies: sponserid, wallet, income, investment records.
     */
if (!function_exists('cleanupGlobalTreeDuplicates')) {
    function cleanupGlobalTreeDuplicates($pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db) return;

        // Step 1: Build child -> [parents] map from tree table (tree is authoritative)
        $stmt = $db->query('SELECT userid, left_id, right_id FROM tree');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $childParentMap = []; // child_id => [['parent'=>..., 'slot'=>..., 'pNumeric'=>...], ...]
        foreach ($rows as $r) {
            $pId      = (string)$r['userid'];
            $pNumeric = (int)preg_replace('/[^0-9]/', '', $pId);
            $l        = (string)($r['left_id']  ?? '');
            $rId      = (string)($r['right_id'] ?? '');

            if (!empty($l)) {
                $childParentMap[$l][] = ['parent' => $pId, 'slot' => 'left_id',  'pNumeric' => $pNumeric];
            }
            if (!empty($rId) && $rId !== $l) {
                $childParentMap[$rId][] = ['parent' => $pId, 'slot' => 'right_id', 'pNumeric' => $pNumeric];
            }
        }

        $updClear = $db->prepare(
            'UPDATE tree SET left_id = IF(left_id = :cid, "", left_id), right_id = IF(right_id = :cid, "", right_id) WHERE userid = :pid'
        );
        $updUserPlacement = $db->prepare(
            'UPDATE user SET underuserid = :pid, join_side = :side WHERE userid = :cid'
        );

        // Step 2: For each child with multiple parents, keep the one with highest parent ID (latest move)
        foreach ($childParentMap as $cId => $parents) {
            if (count($parents) <= 1) {
                continue; // No duplicate — skip
            }

            // Sort parents descending by numeric part of parent ID (highest = most recent placement)
            usort($parents, function($a, $b) {
                return $b['pNumeric'] - $a['pNumeric'];
            });

            $authoritative = $parents[0]; // Keep highest parent ID as authoritative

            // Remove all stale parent references
            foreach ($parents as $idx => $pInfo) {
                if ($idx === 0) continue; // Skip the authoritative one
                $updClear->execute([':cid' => $cId, ':pid' => $pInfo['parent']]);
            }

            // Sync user table from the authoritative tree placement
            $authSide = ($authoritative['slot'] === 'left_id') ? 'left' : 'right';
            $updUserPlacement->execute([
                ':pid'  => $authoritative['parent'],
                ':side' => $authSide,
                ':cid'  => $cId,
            ]);
        }

        // Step 3: Also sync user.underuserid + join_side for ALL users where tree disagrees
        // (Fixes cases where user table is stale but tree is correct — no duplicates involved)
        $allTreeRows = $db->query('SELECT userid, left_id, right_id FROM tree')->fetchAll(PDO::FETCH_ASSOC);
        $treeParentOf = []; // child_id => ['parent'=>..., 'side'=>...]
        foreach ($allTreeRows as $tr) {
            if (!empty($tr['left_id'])) {
                $treeParentOf[(string)$tr['left_id']] = ['parent' => (string)$tr['userid'], 'side' => 'left'];
            }
            if (!empty($tr['right_id'])) {
                $treeParentOf[(string)$tr['right_id']] = ['parent' => (string)$tr['userid'], 'side' => 'right'];
            }
        }

        $allUserRows = $db->query('SELECT userid, underuserid, join_side FROM user')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allUserRows as $u) {
            $uid = (string)$u['userid'];
            if (!isset($treeParentOf[$uid])) continue; // Root or unplaced — skip

            $correctParent = $treeParentOf[$uid]['parent'];
            $correctSide   = $treeParentOf[$uid]['side'];
            $currentParent = (string)($u['underuserid'] ?? '');
            $currentSide   = strtolower((string)($u['join_side'] ?? ''));

            if ($currentParent !== $correctParent || $currentSide !== $correctSide) {
                $updUserPlacement->execute([
                    ':pid'  => $correctParent,
                    ':side' => $correctSide,
                    ':cid'  => $uid,
                ]);
            }
        }
    }
}
}

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
if (!function_exists('loginAdmin')) {


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
}
if (!function_exists('registerUser')) {




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
}


if (!function_exists('updatenonworkwallet')) {
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
}


if (!function_exists('insertSponsor')) {
    function insertSponsor($pdo, $sponsorId, $referralId, $createdDate) 
    {
        $sql = "INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$sponsorId, $referralId, $createdDate]);
    }
}

if (!function_exists('insertUser')) {
    function insertUser($pdo, $data) 
    {
        $sql = "INSERT INTO `user` (
            `userid`, `name`, `mobile`, `email`, `pan`, `pass`, `txn_pass`, `sponserid`, `sponsername`, `underuserid`,
            `active`, `status`, `join_side`, `package`, `joining_date`, `plan`, `pin`, `kyc`, `club`, `upgrade_date`,
            `time`, `country`, `amount`, `capping`, `rank`, `closingdate`, `country_code`, `level`, `atime`, `pool`,
            `state`, `father`, `gender`, `pin_code`, `address`, `otp`, `coin_wallet`
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?
        )";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    }
}

if (!function_exists('insertKYC')) {
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
}

if (!function_exists('insertUserLevel')) {
    function insertUserLevel($pdo, $sponsorId, $userId, $level) 
    {
        $sql = "INSERT INTO user_level (sponsorid, downid, level) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$sponsorId, $userId, $level]);
    }
}
if (!function_exists('getMetaInfo')) {



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
}
if (!function_exists('userid')) {

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
}
if (!function_exists('getalluserpackage')) {

function getalluserpackage(PDO $pdo)
{
    $stmt = $pdo->query("SELECT SUM(total_package) AS total FROM user");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total'] ?? 0;
}
}
if (!function_exists('getuserdatabysponserid')) {



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
}
if (!function_exists('checkuseridregister')) {

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
}
if (!function_exists('getmydirectactiveright')) {


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
}
if (!function_exists('getmydirectactiveleft')) {
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
}
if (!function_exists('getmysponserid')) {


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
}
if (!function_exists('getmydirectactive')) {


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
}
if (!function_exists('insert_userlevel')) {

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
}
if (!function_exists('incometotalnew')) {

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
}
if (!function_exists('incometotalnewdate')) {



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
}
if (!function_exists('getlevelDirectbusiness')) {


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
if (!function_exists('getlevelbusiness_left')) {
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
}
if (!function_exists('getlevelbusiness_right')) {
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
}
if (!function_exists('gettotallevelbusiness_left')) {

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
}
if (!function_exists('gettotallevelbusiness_right')) {
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
}
if (!function_exists('getnews')) {

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
}
if (!function_exists('webtistime')) {

function webtistime()
{
    if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
     }


return date('h:i a');
}
}
if (!function_exists('webtisdate')) {

function webtisdate()
{
    if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
   }


return date('Y-m-d');
}
}
if (!function_exists('getuserdatabyid')) {

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
            "signup_id"     => $rowuser['signup_id'] ?? ($rowuser['userid'] ?? ''),
            "name"          => $rowuser["name"] ?? '',
            "mobile"        => $rowuser["mobile"] ?? '',
            "type"          => $rowuser["type"] ?? 'user',
            "password"      => $rowuser["password"] ?? ($rowuser['pass'] ?? ''),
            "wallet_amount" => $rowuser["wallet_amount"] ?? ($rowuser['amount'] ?? 0.00),
            "sponsor_code"  => $rowuser["sponsor_code"] ?? ($rowuser['sponserid'] ?? ''),
            "status"        => $rowuser["status"] ?? 1,
            "active_date"   => $rowuser["active_date"] ?? ($rowuser['upgrade_date'] ?? ''),
            "created_date"  => $rowuser["created_date"] ?? ($rowuser['joining_date'] ?? ''),
        );

        return $userdata;
    }

    return null; 
}
}
if (!function_exists('paybinaycloing')) {


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
}
if (!function_exists('insert_transction')) {


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
}
if (!function_exists('updateuserincome')) {


function updateuserincome($sponserid, $ytdate)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $stmt = $pdo->prepare("UPDATE tbl_temp_data SET status = 1, paid_date = :ytdate WHERE user_id = :sponserid");
    $stmt->execute([
        ':ytdate'     => $ytdate,
        ':sponserid'  => $sponserid
    ]);
}
}
if (!function_exists('updateuseramount')) {


function updateuseramount($newamount, $sponsorcode)
{
    global $pdo; // Assuming $pdo is your PDO connection

    $stmt = $pdo->prepare("UPDATE user SET amount = amount + :newamount WHERE userid = :sponsorcode");
    $stmt->execute([
        ':newamount'   => $newamount,
        ':sponsorcode' => $sponsorcode
    ]);
}
}
if (!function_exists('checkpedningincome')) {

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
}
if (!function_exists('repurchasecheckpedningincome')) {


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
}
if (!function_exists('repurchaseupdateuseramount')) {


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
}
if (!function_exists('repurchaseupdateuserincome')) {



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
}
if (!function_exists('repurchasecheckcloing')) {



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
}
if (!function_exists('repurchasepaybinaycloing')) {


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
}
if (!function_exists('updatedatabysponserid1')) {

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
}
if (!function_exists('updatedatabysponserid')) {
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
}
if (!function_exists('getroidatabysponserid')) {


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
}



/** ROI One Plan **/
if (!function_exists('pay_roi_one_income')) {
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
}
if (!function_exists('manual_pay_roi_one_income')) {
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
}
if (!function_exists('getpercent')) {


function getpercent($amount, $percent)
{
    $per_amount = $amount * $percent / 100;
    return $per_amount;
}
}
if (!function_exists('getroipercentage')) {

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
}

/** ROI One Plan **/
if (!function_exists('checkuserid')) {



function checkuserid(PDO $pdo, $mysponsernew)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS alluser FROM user WHERE userid = :userid");
    $stmt->execute([':userid' => $mysponsernew]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $row['alluser'] : 0;
}
}
if (!function_exists('getproduct')) {

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
}
if (!function_exists('gettransactiondata')) {
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
}
if (!function_exists('checkcloing')) {



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
if (!function_exists('getQualifiedDirectCount')) {
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
}

/**
 * Generate 10-month Direct Bonus Schedule for an eligible investment.
 * Direct Bonus = Eligible Investment * 6% divided into 10 monthly installments.
 * Only generated if investment >= ₹13,000 and user has active access.
 */
if (!function_exists('generateDirectBonusSchedule')) {
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
}

/**
 * Process Direct Bonus Monthly Closing Installments.
 * Executed during admin Monthly Profit Closing.
 */
if (!function_exists('processDirectBonusInstallments')) {
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
}

/**
 * Get detailed breakdown of direct referrals for a user to verify qualification.
 * Qualification criteria: active unlock access (user.active = '1') AND total active investment >= ₹13,000.
 */
if (!function_exists('getQualifiedDirectDetails')) {
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

        $rate = getUSDToINRRate($db);
        if ($rate <= 0) $rate = 90.0;
        $totalInvUsd = round($totalInv / $rate, 2);

        $results[] = [
            'userid'               => $d['userid'],
            'name'                 => $d['name'],
            'activation_status'    => $isActive ? 'YES' : 'NO',
            'total_investment_inr' => $totalInv,
            'total_investment_usd' => $totalInvUsd,
            'total_investment'     => $totalInvUsd,
            'is_qualified'         => $isQualified,
            'qualification_status' => $isQualified ? 'QUALIFIED' : 'NOT QUALIFIED',
            'reason'               => $reason
        ];
    }

    return $results;
}
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
if (!function_exists('processAdminDirectBonusAdjustment')) {
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
if (!function_exists('getMentorDirectContributions')) {
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
}

/**
 * Save / Update a Direct User's contribution percentage under a Mentor.
 * Validation: 0 <= % <= 100
 */
if (!function_exists('saveMentorDirectContribution')) {
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
}

/**
 * Validate total contribution percentage for all direct users under a Mentor.
 * Rule: Sum of all direct users' contribution percentages MUST equal exactly 100.00%.
 */
if (!function_exists('validateMentorContributions')) {
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
}

/**
 * Process Monthly Mentor Income Payouts during Monthly Closing.
 * Calculates 2% Mentor Income from Mentor's Monthly Profit Income,
 * validates total contribution = 100%, and credits eligible Direct Users' mentor_income_wallet.
 */
if (!function_exists('processMentorIncome')) {
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
if (!function_exists('processAdminMentorIncomeAdjustment')) {
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
}

/**
 * Get summary report statistics for Mentor Income Admin Portal.
 */
if (!function_exists('getMentorIncomeReportStats')) {
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
}

/**
 * Helper: Recursively or iteratively collect all descendant user IDs in a binary subtree.
 */
if (!function_exists('getBinarySubtreeUserIds')) {
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
}

/**
 * Get detailed Left Leg and Right Leg statistics (ID counts and Business in USD) for a user.
 */
if (!function_exists('getBinaryLegDetails')) {
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
}

/**
 * Evaluate and process VIP Club Qualifications & One-Time Rewards for a user.
 */
if (!function_exists('evaluateUserVIPQualifications')) {
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
}

/**
 * Process Monthly VIP Club Income & Company Turnover Share Payouts during Monthly Closing.
 * Rule: VIP Monthly closing MUST occur on the 11th date of the month (unless explicitly bypassed via $skip_date_check).
 */
if (!function_exists('processVIPMonthlyIncome')) {
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
if (!function_exists('processAdminVIPAdjustment')) {
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
}

/**
 * Get all system control settings as key-value pairs.
 */
if (!function_exists('getSystemControls')) {
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
}

/**
 * Check if a specific system control setting is enabled ('1').
 */
if (!function_exists('isSystemControlEnabled')) {
function isSystemControlEnabled($setting_key, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || empty($setting_key)) return true;
    try {
        $stmt = $db->prepare("SELECT setting_value FROM tbl_system_control WHERE setting_key = :key LIMIT 1");
        $stmt->execute([':key' => $setting_key]);
        $val = $stmt->fetchColumn();
        return ($val === false || $val === '1' || $val === 1);
    } catch (Exception $e) {
        return true;
    }
}
}


/**
 * Update system control setting.
 */
if (!function_exists('setSystemControl')) {
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
}

/**
 * Log unified admin audit action in tbl_admin_audit_log.
 */
if (!function_exists('logAdminAuditAction')) {
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
}

/**
 * Process Universal Admin Wallet Adjustment (Credit / Debit) across any isolated wallet with row lock & audit.
 */
if (!function_exists('processUniversalAdminWalletAdjustment')) {
function processUniversalAdminWalletAdjustment($admin_id, $target_user_id, $wallet_column, $adjustment_type, $amount, $reason, $reference = '', $pdoConnection = null, $currency = 'USD') {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$admin_id || !$target_user_id) {
        return ['status' => 'error', 'message' => 'Invalid parameters. Admin ID & User ID required.'];
    }

    $validWallets = [
        'deposite_wallet',
        'amount',
        'net_balance',
        'active_investment',
        'total_withdrawal',
        'profit_income_wallet',
        'profit_sharing_wallet',
        'direct_bonus_wallet',
        'mentor_income_wallet',
        'rank_reward_wallet',
        'vip_club_wallet',
        'user_growth_wallet',
        'company_turnover_wallet',
        'pin_wallet',
        'working_wallet',
        'nonwork_wallet'
    ];

    $walletNames = [
        'deposite_wallet'         => 'Main Wallet',
        'pin_wallet'              => 'Main Wallet',
        'amount'                  => 'Net Balance',
        'net_balance'             => 'Net Balance',
        'active_investment'       => 'Active Investment',
        'total_withdrawal'        => 'All Withdrawal',
        'profit_income_wallet'    => 'Profit Income',
        'profit_sharing_wallet'   => 'Profit Sharing',
        'direct_bonus_wallet'     => 'Direct Bonus',
        'mentor_income_wallet'    => 'Mentor Income',
        'rank_reward_wallet'      => 'Rank Reward',
        'vip_club_wallet'         => 'VIP Club Income',
        'user_growth_wallet'      => 'User Growth',
        'company_turnover_wallet' => 'Company Turnover Income'
    ];

    if (!in_array($wallet_column, $validWallets)) {
        return ['status' => 'error', 'message' => "Invalid wallet type specified: {$wallet_column}"];
    }

    $walletDisplayName = $walletNames[$wallet_column] ?? ucwords(str_replace('_', ' ', $wallet_column));

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

    $currency = in_array(strtoupper(trim($currency)), ['INR', 'USD']) ? strtoupper(trim($currency)) : 'USD';
    $rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($db) : 90.0;
    if ($rate <= 0) $rate = 90.0;

    if ($currency === 'INR') {
        $amountInUSD = round($amount / $rate, 2);
        $amountInINR = round($amount, 2);
        $currSymbol  = '₹';
        $dispAmount  = '₹' . number_format($amountInINR, 2);
    } else {
        $amountInUSD = round($amount, 2);
        $amountInINR = round($amount * $rate, 2);
        $currSymbol  = '$';
        $dispAmount  = '$' . number_format($amountInUSD, 2);
    }

    // Generate unique transaction ID (ADM-XXXXXX)
    $txnId = !empty($reference) ? $reference : ('ADM-' . str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT));

    $inLocalTxn = false;
    if (!$db->inTransaction()) {
        $db->beginTransaction();
        $inLocalTxn = true;
    }

    try {
        $cleanUid    = preg_replace('/^(AN|ANANTA)/i', '', (string)$target_user_id);
        $prefixedUid = 'AN' . $cleanUid;

        $isMainWallet       = in_array($wallet_column, ['deposite_wallet', 'pin_wallet']);
        $isNetBalance       = in_array($wallet_column, ['amount', 'net_balance']);
        $isActiveInvestment = ($wallet_column === 'active_investment');
        $isTotalWithdrawal  = ($wallet_column === 'total_withdrawal');

        if ($isMainWallet) {
            // Main Wallet operates ONLY on deposite_wallet & pin_wallet (isolated from Net Balance)
            $stmtUser = $db->prepare("SELECT userid, deposite_wallet, pin_wallet FROM user WHERE userid = :userid OR userid = :clean OR userid = :prefixed FOR UPDATE");
            $stmtUser->execute([
                ':userid'   => $target_user_id,
                ':clean'    => $cleanUid,
                ':prefixed' => $prefixedUid
            ]);
            $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
            }

            $target_user_id = $userRow['userid'];
            $depBal = (float)($userRow['deposite_wallet'] ?? 0.00);
            $pinBal = (float)($userRow['pin_wallet'] ?? 0.00);
            $prevBalUSD = max($depBal, $pinBal);

            if ($adjType === 'DEBIT') {
                if ($prevBalUSD < $amountInUSD) {
                    if ($inLocalTxn) $db->rollBack();
                    $dispPrev = ($currency === 'INR') ? ('₹' . number_format($prevBalUSD * $rate, 2)) : ('$' . number_format($prevBalUSD, 2));
                    return [
                        'status'  => 'error',
                        'message' => "Insufficient balance in {$walletDisplayName}. Current: {$dispPrev}, Requested Debit: {$dispAmount}. Negative balance is blocked."
                    ];
                }
                $newBalUSD = round($prevBalUSD - $amountInUSD, 2);
                $updStmt = $db->prepare("UPDATE user SET 
                    deposite_wallet = GREATEST(0, deposite_wallet - :amt),
                    pin_wallet = GREATEST(0, pin_wallet - :amt)
                    WHERE userid = :userid");
            } else {
                $newBalUSD = round($prevBalUSD + $amountInUSD, 2);
                $updStmt = $db->prepare("UPDATE user SET 
                    deposite_wallet = deposite_wallet + :amt,
                    pin_wallet = pin_wallet + :amt
                    WHERE userid = :userid");
            }

            $updStmt->execute([':amt' => $amountInUSD, ':userid' => $target_user_id]);

        } elseif ($isNetBalance) {
            // Net Balance operates ONLY on amount & net_balance (isolated from Main Wallet)
            $stmtUser = $db->prepare("SELECT userid, amount, net_balance FROM user WHERE userid = :userid OR userid = :clean OR userid = :prefixed FOR UPDATE");
            $stmtUser->execute([
                ':userid'   => $target_user_id,
                ':clean'    => $cleanUid,
                ':prefixed' => $prefixedUid
            ]);
            $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
            }

            $target_user_id = $userRow['userid'];
            $amtBal = (float)($userRow['amount'] ?? 0.00);
            $netBal = (float)($userRow['net_balance'] ?? 0.00);
            $prevBalUSD = max($amtBal, $netBal);

            if ($adjType === 'DEBIT') {
                if ($prevBalUSD < $amountInUSD) {
                    if ($inLocalTxn) $db->rollBack();
                    $dispPrev = ($currency === 'INR') ? ('₹' . number_format($prevBalUSD * $rate, 2)) : ('$' . number_format($prevBalUSD, 2));
                    return [
                        'status'  => 'error',
                        'message' => "Insufficient balance in {$walletDisplayName}. Current: {$dispPrev}, Requested Debit: {$dispAmount}. Negative balance is blocked."
                    ];
                }
                $newBalUSD = round($prevBalUSD - $amountInUSD, 2);
                $updStmt = $db->prepare("UPDATE user SET 
                    amount = GREATEST(0, amount - :amt),
                    net_balance = GREATEST(0, net_balance - :amt)
                    WHERE userid = :userid");
            } else {
                $newBalUSD = round($prevBalUSD + $amountInUSD, 2);
                $updStmt = $db->prepare("UPDATE user SET 
                    amount = amount + :amt,
                    net_balance = net_balance + :amt
                    WHERE userid = :userid");
            }

            $updStmt->execute([':amt' => $amountInUSD, ':userid' => $target_user_id]);

        } elseif ($isActiveInvestment) {
            // Active Investment operates on tbl_roi_one (source of truth) and synchronizes user.active_investment & user.total_package
            $stmtUser = $db->prepare("SELECT userid, name, active_investment, total_package FROM user WHERE userid = :userid OR userid = :clean OR userid = :prefixed FOR UPDATE");
            $stmtUser->execute([
                ':userid'   => $target_user_id,
                ':clean'    => $cleanUid,
                ':prefixed' => $prefixedUid
            ]);
            $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
            }

            $target_user_id = $userRow['userid'];

            // Query active investment records from tbl_roi_one with row-locking
            $stmtInv = $db->prepare("
                SELECT id, package_code, real_fund_usd, package, status, capital_withdrawal_status
                FROM tbl_roi_one
                WHERE user_id = :uid 
                  AND status = '0' 
                  AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
                ORDER BY id ASC
                FOR UPDATE
            ");
            $stmtInv->execute([':uid' => $target_user_id]);
            $activeRows = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

            $prevBalUSD = 0.00;
            foreach ($activeRows as $arow) {
                $pkgUsd = (float)($arow['real_fund_usd'] ?? 0);
                $pkgInr = (float)($arow['package'] ?? 0);
                if ($pkgUsd <= 0 && $pkgInr > 0) {
                    $pkgUsd = round($pkgInr / $rate, 2);
                }
                $prevBalUSD += $pkgUsd;
            }
            $prevBalUSD = round($prevBalUSD, 2);

            // Fallback: If no records in tbl_roi_one, check user.active_investment or user.total_package
            if ($prevBalUSD <= 0.00) {
                $actInv = (float)($userRow['active_investment'] ?? 0);
                $totPkg = (float)($userRow['total_package'] ?? 0);
                if ($actInv > 0) {
                    $prevBalUSD = round($actInv, 2);
                } elseif ($totPkg > 0) {
                    $prevBalUSD = round($totPkg / $rate, 2);
                }
            }

            if ($adjType === 'DEBIT') {
                if ($prevBalUSD < $amountInUSD) {
                    if ($inLocalTxn) $db->rollBack();
                    $dispPrev = ($currency === 'INR') ? ('₹' . number_format($prevBalUSD * $rate, 2)) : ('$' . number_format($prevBalUSD, 2));
                    return [
                        'status'  => 'error',
                        'message' => "Insufficient balance in {$walletDisplayName}. Current: {$dispPrev}, Requested Debit: {$dispAmount}. Negative balance is blocked."
                    ];
                }

                $remainingToDebit = $amountInUSD;
                foreach ($activeRows as $arow) {
                    if ($remainingToDebit <= 0) {
                        break;
                    }
                    $rowId  = (int)$arow['id'];
                    $pkgUsd = (float)($arow['real_fund_usd'] ?? 0);
                    $pkgInr = (float)($arow['package'] ?? 0);
                    if ($pkgUsd <= 0 && $pkgInr > 0) {
                        $pkgUsd = round($pkgInr / $rate, 2);
                    }

                    if ($pkgUsd <= ($remainingToDebit + 0.0001)) {
                        // Fully consumed this investment row
                        $updRoi = $db->prepare("UPDATE tbl_roi_one SET real_fund_usd = 0.00, package = 0, status = '1', capital_withdrawal_status = 'WITHDRAWN' WHERE id = :id");
                        $updRoi->execute([':id' => $rowId]);
                        $remainingToDebit = round(max(0, $remainingToDebit - $pkgUsd), 2);
                    } else {
                        // Partially debit this investment row
                        $newPkgUsd = round($pkgUsd - $remainingToDebit, 2);
                        $newPkgInr = round($newPkgUsd * $rate, 2);
                        $updRoi = $db->prepare("UPDATE tbl_roi_one SET real_fund_usd = :usd, package = :inr WHERE id = :id");
                        $updRoi->execute([
                            ':usd' => $newPkgUsd,
                            ':inr' => $newPkgInr,
                            ':id'  => $rowId
                        ]);
                        $remainingToDebit = 0.00;
                    }
                }

                $newBalUSD = round($prevBalUSD - $amountInUSD, 2);
                $newBalINR = round($newBalUSD * $rate, 2);

                $updUser = $db->prepare("UPDATE user SET 
                    active_investment = :new_usd,
                    total_package = :new_inr
                    WHERE userid = :userid");
                $updUser->execute([
                    ':new_usd' => $newBalUSD,
                    ':new_inr' => $newBalINR,
                    ':userid'  => $target_user_id
                ]);

            } else {
                // CREDIT
                $newBalUSD = round($prevBalUSD + $amountInUSD, 2);
                $newBalINR = round($newBalUSD * $rate, 2);

                $insRoi = $db->prepare("
                    INSERT INTO tbl_roi_one 
                    (user_id, level, name, package_code, real_fund_usd, bonus_percent_snapshot, bonus_amount_usd, lock_period_months, maturity_date, deduction_percent_snapshot, capital_withdrawal_status, package, percentage, count, amount, totalincome, capping, lock_day, date, time, closingdate, status)
                    VALUES
                    (:uid, 1, 'Admin Investment Credit', 'ADMIN_ADJ', :fund_usd, 0.00, 0.00, 48, DATE_ADD(CURDATE(), INTERVAL 48 MONTH), 15.00, 'LOCKED', :pkg_inr, 3.00, 0, 0, 0, :capping, 1460, CURDATE(), CURTIME(), CURDATE(), '0')
                ");
                $insRoi->execute([
                    ':uid'      => $target_user_id,
                    ':fund_usd' => $amountInUSD,
                    ':pkg_inr'  => $amountInINR,
                    ':capping'  => $amountInINR * 2
                ]);

                $updUser = $db->prepare("UPDATE user SET 
                    active_investment = :new_usd,
                    total_package = :new_inr
                    WHERE userid = :userid");
                $updUser->execute([
                    ':new_usd' => $newBalUSD,
                    ':new_inr' => $newBalINR,
                    ':userid'  => $target_user_id
                ]);
            }

        } elseif ($isTotalWithdrawal) {
            // All Withdrawal operates on user.total_withdrawal and tbl_transaction
            $stmtUser = $db->prepare("SELECT userid, name, total_withdrawal FROM user WHERE userid = :userid OR userid = :clean OR userid = :prefixed FOR UPDATE");
            $stmtUser->execute([
                ':userid'   => $target_user_id,
                ':clean'    => $cleanUid,
                ':prefixed' => $prefixedUid
            ]);
            $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
            }

            $target_user_id = $userRow['userid'];

            // Query current withdrawal balance: check user.total_withdrawal, or fallback to tbl_transaction matching user_profile.php
            $prevBalUSD = (float)($userRow['total_withdrawal'] ?? 0.00);
            if ($prevBalUSD <= 0.00) {
                $stmtWd = $db->prepare("
                    SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) 
                    FROM tbl_transaction 
                    WHERE (user_id = :uid OR user_id = :clean OR user_id = :an) 
                      AND (subject LIKE '%Withdraw%' OR type = 'Withdrawal Request')
                      AND subject NOT LIKE 'Admin Adjustment%'
                ");
                $stmtWd->execute([':uid' => $target_user_id, ':clean' => $cleanUid, ':an' => $prefixedUid]);
                $prevBalUSD = (float)$stmtWd->fetchColumn();
            }
            $prevBalUSD = round($prevBalUSD, 2);

            if ($adjType === 'DEBIT') {
                if ($prevBalUSD < $amountInUSD) {
                    if ($inLocalTxn) $db->rollBack();
                    $dispPrev = ($currency === 'INR') ? ('₹' . number_format($prevBalUSD * $rate, 2)) : ('$' . number_format($prevBalUSD, 2));
                    return [
                        'status'  => 'error',
                        'message' => "Insufficient balance in {$walletDisplayName}. Current: {$dispPrev}, Requested Debit: {$dispAmount}. Negative balance is blocked."
                    ];
                }

                $newBalUSD = round(max(0, $prevBalUSD - $amountInUSD), 2);

                // Update user.total_withdrawal
                $updUser = $db->prepare("UPDATE user SET total_withdrawal = :new_bal WHERE userid = :userid");
                $updUser->execute([
                    ':new_bal' => $newBalUSD,
                    ':userid'  => $target_user_id
                ]);

                // Clear/reduce the underlying withdrawal transactions in tbl_transaction so dynamic recalculation matches $newBalUSD
                $remToDebit = $amountInUSD;
                $stmtTxns = $db->prepare("
                    SELECT id, amount 
                    FROM tbl_transaction 
                    WHERE (user_id = :uid OR user_id = :clean OR user_id = :an) 
                      AND (subject LIKE '%Withdraw%' OR type = 'Withdrawal Request')
                      AND subject NOT LIKE 'Admin Adjustment%'
                      AND status != 2
                    ORDER BY id DESC 
                    FOR UPDATE
                ");
                $stmtTxns->execute([':uid' => $target_user_id, ':clean' => $cleanUid, ':an' => $prefixedUid]);
                $wdRows = $stmtTxns->fetchAll(PDO::FETCH_ASSOC);

                foreach ($wdRows as $wRow) {
                    if ($remToDebit <= 0) break;
                    $wId  = (int)$wRow['id'];
                    $wAmt = (float)$wRow['amount'];
                    if ($wAmt <= ($remToDebit + 0.0001)) {
                        $db->prepare("UPDATE tbl_transaction SET status = 2, a_status = 2 WHERE id = :id")->execute([':id' => $wId]);
                        $remToDebit = round(max(0, $remToDebit - $wAmt), 2);
                    } else {
                        $newWAmt = round($wAmt - $remToDebit, 2);
                        $db->prepare("UPDATE tbl_transaction SET amount = :amt WHERE id = :id")->execute([':amt' => $newWAmt, ':id' => $wId]);
                        $remToDebit = 0.00;
                    }
                }

            } else {
                // CREDIT
                $newBalUSD = round($prevBalUSD + $amountInUSD, 2);
                $updUser = $db->prepare("UPDATE user SET total_withdrawal = :new_bal WHERE userid = :userid");
                $updUser->execute([
                    ':new_bal' => $newBalUSD,
                    ':userid'  => $target_user_id
                ]);
            }

        } else {
            $stmtUser = $db->prepare("SELECT userid, `{$wallet_column}` FROM user WHERE userid = :userid OR userid = :clean OR userid = :prefixed FOR UPDATE");
            $stmtUser->execute([
                ':userid'   => $target_user_id,
                ':clean'    => $cleanUid,
                ':prefixed' => $prefixedUid
            ]);
            $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
            }

            $target_user_id = $userRow['userid'];
            $prevBalUSD = (float)($userRow[$wallet_column] ?? 0.00);

            // Fallback for income wallets if user column is 0.00 but history has accumulated payouts
            if ($prevBalUSD <= 0.00) {
                if ($wallet_column === 'profit_income_wallet') {
                    $stmtPI = $db->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_roiinc WHERE user_id = :uid");
                    $stmtPI->execute([':uid' => $target_user_id]);
                    $prevBalUSD = (float)$stmtPI->fetchColumn();
                } elseif ($wallet_column === 'profit_sharing_wallet') {
                    $stmtPS = $db->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_daily_levelinc WHERE user_id = :uid");
                    $stmtPS->execute([':uid' => $target_user_id]);
                    $prevBalUSD = (float)$stmtPS->fetchColumn();
                } elseif ($wallet_column === 'direct_bonus_wallet') {
                    $stmtDB = $db->prepare("SELECT COALESCE(SUM(installment_amount), 0) FROM tbl_direct_bonus_schedule WHERE beneficiary_id = :uid AND status = 'CREDITED'");
                    $stmtDB->execute([':uid' => $target_user_id]);
                    $dbVal = (float)$stmtDB->fetchColumn();
                    if ($dbVal <= 0) {
                        $stmtRoi2 = $db->prepare("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_two WHERE user_id = :uid");
                        $stmtRoi2->execute([':uid' => $target_user_id]);
                        $dbVal = (float)$stmtRoi2->fetchColumn();
                    }
                    $prevBalUSD = $dbVal;
                } elseif ($wallet_column === 'mentor_income_wallet') {
                    $stmtMI = $db->prepare("SELECT COALESCE(SUM(payout_amount), 0) FROM tbl_mentor_income_schedule WHERE mentor_id = :uid AND status = 'CREDITED'");
                    $stmtMI->execute([':uid' => $target_user_id]);
                    $prevBalUSD = (float)$stmtMI->fetchColumn();
                } elseif ($wallet_column === 'rank_reward_wallet') {
                    $stmtRR = $db->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_rewardinc WHERE user_id = :uid");
                    $stmtRR->execute([':uid' => $target_user_id]);
                    $prevBalUSD = (float)$stmtRR->fetchColumn();
                } elseif ($wallet_column === 'vip_club_wallet') {
                    $stmtVip1 = $db->prepare("SELECT COALESCE(SUM(reward_amount), 0) FROM tbl_vip_user_qualification WHERE user_id = :uid AND reward_status = 'CREDITED'");
                    $stmtVip1->execute([':uid' => $target_user_id]);
                    $v1 = (float)$stmtVip1->fetchColumn();
                    $stmtVip2 = $db->prepare("SELECT COALESCE(SUM(total_payout), 0) FROM tbl_vip_monthly_schedule WHERE user_id = :uid AND status = 'CREDITED'");
                    $stmtVip2->execute([':uid' => $target_user_id]);
                    $v2 = (float)$stmtVip2->fetchColumn();
                    $prevBalUSD = round($v1 + $v2, 2);
                } elseif ($wallet_column === 'company_turnover_wallet') {
                    $stmtCT = $db->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE user_id = :uid AND (subject LIKE '%Turnover%' OR subject LIKE '%Leadership%')");
                    $stmtCT->execute([':uid' => $target_user_id]);
                    $prevBalUSD = (float)$stmtCT->fetchColumn();
                }
                $prevBalUSD = round($prevBalUSD, 2);
            }

            if ($adjType === 'DEBIT') {
                if ($prevBalUSD < $amountInUSD) {
                    if ($inLocalTxn) $db->rollBack();
                    $dispPrev = ($currency === 'INR') ? ('₹' . number_format($prevBalUSD * $rate, 2)) : ('$' . number_format($prevBalUSD, 2));
                    return [
                        'status'  => 'error',
                        'message' => "Insufficient balance in {$walletDisplayName}. Current: {$dispPrev}, Requested Debit: {$dispAmount}. Negative balance is blocked."
                    ];
                }
                $newBalUSD = round($prevBalUSD - $amountInUSD, 2);
                $updStmt = $db->prepare("UPDATE user SET `{$wallet_column}` = :new_bal WHERE userid = :userid");
                $updStmt->execute([':new_bal' => $newBalUSD, ':userid' => $target_user_id]);
            } else {
                $newBalUSD = round($prevBalUSD + $amountInUSD, 2);
                $updStmt = $db->prepare("UPDATE user SET `{$wallet_column}` = :new_bal WHERE userid = :userid");
                $updStmt->execute([':new_bal' => $newBalUSD, ':userid' => $target_user_id]);
            }
        }

        $txnType = ($adjType === 'CREDIT') ? 'Credit' : 'Debit';
        $subject = "Admin Adjustment ({$adjType}) - Wallet: {$walletDisplayName} ({$dispAmount}) - Reason: {$reason} (Txn: {$txnId})";

        // Create transaction record in tbl_transaction (stores base USD)
        $insTxn = $db->prepare("
            INSERT INTO tbl_transaction
            (user_id, type, subject, amount, created_date, time, status)
            VALUES
            (:user_id, :type, :subject, :amount, CURDATE(), CURTIME(), 1)
        ");
        $insTxn->execute([
            ':user_id' => $target_user_id,
            ':type'    => $txnType,
            ':subject' => $subject,
            ':amount'  => $amountInUSD
        ]);

        // Log Immutable Admin Audit Record in tbl_admin_audit_log
        logAdminAuditAction($admin_id, $adjType, $target_user_id, $amountInUSD, $wallet_column, $prevBalUSD, $newBalUSD, "{$dispAmount} | {$reason}", $txnId, $db);

        // Create User Notification in tbl_system_notifications
        $notifTitle = ($adjType === 'CREDIT') ? "Admin Wallet Credit" : "Admin Wallet Debit";
        $notifMessage = "{$dispAmount} has been " . strtolower($adjType) . "ed " . ($adjType === 'CREDIT' ? 'to' : 'from') . " your " . $walletDisplayName . ".\n\nReason:\n" . $reason . "\n\nTransaction ID:\n" . $txnId;

        $insNotif = $db->prepare("
            INSERT INTO tbl_system_notifications
            (target_type, target_user_id, title, message, created_by, is_read, created_at)
            VALUES
            ('USER', :target_user_id, :title, :message, :created_by, 0, NOW())
        ");
        $insNotif->execute([
            ':target_user_id' => $target_user_id,
            ':title'          => $notifTitle,
            ':message'        => $notifMessage,
            ':created_by'     => $admin_id
        ]);

        if ($inLocalTxn) {
            $db->commit();
        }

        return [
            'status'           => 'success',
            'transaction_id'   => $txnId,
            'message'          => "Successfully processed {$adjType} of {$dispAmount} on {$walletDisplayName} for user {$target_user_id}.",
            'previous_balance' => ($currency === 'INR') ? ($prevBalUSD * $rate) : $prevBalUSD,
            'new_balance'      => ($currency === 'INR') ? ($newBalUSD * $rate) : $newBalUSD
        ];
    } catch (Exception $e) {
        if ($inLocalTxn && $db->inTransaction()) {
            $db->rollBack();
        }
        return ['status' => 'error', 'message' => 'Adjustment error: ' . $e->getMessage()];
    }
}
}

/**
 * Comprehensive real-time Dashboard statistics calculator for Requirement #18.
 */
if (!function_exists('getAdminComprehensiveDashboardStats')) {
function getAdminComprehensiveDashboardStats($pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db) return [];

    $totUsers     = (int)$db->query("SELECT COUNT(*) FROM user")->fetchColumn();
    $activeUsers  = (int)$db->query("SELECT COUNT(*) FROM user WHERE active = '1'")->fetchColumn();
    $inactiveUsers= (int)$db->query("SELECT COUNT(*) FROM user WHERE active = '0'")->fetchColumn();
    $cDate        = date('Y-m-d');
    $todayRegs    = (int)$db->query("SELECT COUNT(*) FROM user WHERE (joining_date = '{$cDate}' OR DATE(joining_date) = '{$cDate}')")->fetchColumn();

    $totUnlockAcc = (int)$db->query("SELECT COUNT(*) FROM user WHERE active = '1'")->fetchColumn();
    $totRevenueUnlock = round($totUnlockAcc * 990.00, 2); // ₹990 per $11 unlock access

    $rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($db) : 90.0;
    if ($rate <= 0) $rate = 90.0;

    $stmtBiz      = $db->query("
        SELECT COALESCE(SUM(
            CASE 
                WHEN real_fund_usd > 0 THEN real_fund_usd * {$rate}
                ELSE package 
            END
        ), 0) 
        FROM tbl_roi_one
    ");
    $totBizInr    = (float)$stmtBiz->fetchColumn();

    $stmtToday    = $db->query("
        SELECT COALESCE(SUM(
            CASE 
                WHEN real_fund_usd > 0 THEN real_fund_usd * {$rate}
                ELSE package 
            END
        ), 0) 
        FROM tbl_roi_one WHERE DATE(date) = CURDATE()
    ");
    $todayBizInr  = (float)$stmtToday->fetchColumn();

    $stmtMonth    = $db->query("
        SELECT COALESCE(SUM(
            CASE 
                WHEN real_fund_usd > 0 THEN real_fund_usd * {$rate}
                ELSE package 
            END
        ), 0) 
        FROM tbl_roi_one WHERE DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
    ");
    $monthBizInr  = (float)$stmtMonth->fetchColumn();

    // Audited Withdrawal Financial Metrics (Net Balance & Real Withdrawal Requests Only)
    $totWdPaid    = (float)$db->query("
        SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) 
        FROM tbl_transaction 
        WHERE (subject LIKE '%Withdrawal%' OR subject LIKE '%Withdraw%') 
          AND subject NOT LIKE 'Admin Adjustment%' 
          AND subject NOT LIKE 'Cancel Withdrawal%'
          AND a_status = '1' 
          AND status != 2 AND a_status != '2'
    ")->fetchColumn();

    $totWdPend    = (float)$db->query("
        SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) 
        FROM tbl_transaction 
        WHERE (subject LIKE '%Withdrawal%' OR subject LIKE '%Withdraw%') 
          AND subject NOT LIKE 'Admin Adjustment%' 
          AND subject NOT LIKE 'Cancel Withdrawal%'
          AND (a_status = '0' OR a_status IS NULL OR a_status = '') 
          AND a_status != '1' 
          AND status != 2 AND a_status != '2'
    ")->fetchColumn();

    // Unique User Counts for Admin Dashboard Withdrawal Cards
    $paidWdUsers  = (int)$db->query("
        SELECT COUNT(DISTINCT user_id) 
        FROM tbl_transaction 
        WHERE (subject LIKE '%Withdrawal%' OR subject LIKE '%Withdraw%') 
          AND subject NOT LIKE 'Admin Adjustment%' 
          AND subject NOT LIKE 'Cancel Withdrawal%'
          AND a_status = '1' 
          AND status != 2 AND a_status != '2'
    ")->fetchColumn();

    $pendWdUsers  = (int)$db->query("
        SELECT COUNT(DISTINCT user_id) 
        FROM tbl_transaction 
        WHERE (subject LIKE '%Withdrawal%' OR subject LIKE '%Withdraw%') 
          AND subject NOT LIKE 'Admin Adjustment%' 
          AND subject NOT LIKE 'Cancel Withdrawal%'
          AND (a_status = '0' OR a_status IS NULL OR a_status = '') 
          AND a_status != '1' 
          AND status != 2 AND a_status != '2'
    ")->fetchColumn();

    $totWdUsers   = (int)$db->query("
        SELECT COUNT(DISTINCT user_id) 
        FROM tbl_transaction 
        WHERE (subject LIKE '%Withdrawal%' OR subject LIKE '%Withdraw%') 
          AND subject NOT LIKE 'Admin Adjustment%' 
          AND subject NOT LIKE 'Cancel Withdrawal%'
          AND (a_status = '1' OR a_status = '0' OR a_status IS NULL OR a_status = '') 
          AND status != 2 AND a_status != '2'
    ")->fetchColumn();

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
        'today_registrations'          => $todayRegs,
        'total_users'                  => $totUsers,
        'active_users'                 => $activeUsers,
        'inactive_users'               => $inactiveUsers,
        'total_unlock_access'          => $totUnlockAcc,
        'unlock_revenue_inr'           => $totRevenueUnlock,
        'total_investment_inr'         => round($totBizInr, 2),
        'total_investment_usd'         => round($totBizInr / $rate, 2),
        'today_business_inr'           => round($todayBizInr, 2),
        'today_business_usd'           => round($todayBizInr / $rate, 2),
        'monthly_business_inr'         => round($monthBizInr, 2),
        'monthly_business_usd'         => round($monthBizInr / $rate, 2),
        'total_withdrawal_paid'        => round($totWdPaid, 2),
        'pending_withdrawal'           => round($totWdPend, 2),
        'withdrawal_paid_user_count'   => $paidWdUsers,
        'pending_withdrawal_user_count'=> $pendWdUsers,
        'total_withdrawal_user_count'  => $totWdUsers,
        'profit_income_paid'           => round($piPaid, 2),
        'profit_sharing_paid'          => round($psPaid, 2),
        'direct_bonus_paid'            => round($dbPaid, 2),
        'mentor_income_paid'           => round($miPaid, 2),
        'vip_club_income_paid'         => round($vipPaid + $vipMonthly, 2),
        'total_income_distributed'     => $totIncomePaid,
        'kyc_pending_count'            => $kycPending,
        'support_tickets_open'         => $openTickets
    ];
}
}

/**
 * Get active/all Ananta Package Configs.
 */
if (!function_exists('getAnantaPackageConfigs')) {
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
}

/**
 * Server-side Package Investment Validation.
 */
if (!function_exists('determinePackageLockingRules')) {
    /**
     * Capital Locking Rules:
     * - Basic Package: 48 months lock -> 15% deduction
     * - Advance Package: 48 months lock -> 15% deduction
     * - Premium Package: 48 months lock -> 15% deduction
     * - 30% Bonus Package: 6 months lock -> 15% deduction
     * - Tour Package: 48 months lock -> 15% deduction
     */
    function determinePackageLockingRules($packageName, $packageCode = null) {
        $str = strtoupper(trim(($packageName ?? '') . ' ' . ($packageCode ?? '')));
        if (strpos($str, 'BONUS') !== false || strpos($str, '30%') !== false) {
            return [
                'lock_period_months' => 6,
                'deduction_percent'  => 15.00
            ];
        }
        return [
            'lock_period_months' => 48,
            'deduction_percent'  => 15.00
        ];
    }
}

if (!function_exists('validatePackageInvestment')) {
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
}

/**
 * Process Ananta Package Investment with Immutable Historical Rules Snapshot.
 */
if (!function_exists('processAnantaPackageInvestment')) {
function processAnantaPackageInvestment($user_id, $package_id, $amount_usd, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;
    if (!$db || !$user_id) {
        return ['status' => 'error', 'message' => 'User authentication required.'];
    }

    // Strict Server-Side Check: User must have completed $11 Account Activation
    $actStatus = function_exists('getUserAccountActivationStatus')
        ? getUserAccountActivationStatus($user_id, $db)
        : null;

    $isActive = ($actStatus && !empty($actStatus['is_active']));
    if (!$actStatus) {
        $stmtActCheck = $db->prepare("SELECT active, activation_expiry_date FROM user WHERE userid = :uid OR userid = :clean LIMIT 1");
        $cleanUid = preg_replace('/^(AN|ANANTA)/i', '', (string)$user_id);
        $stmtActCheck->execute([':uid' => $user_id, ':clean' => $cleanUid]);
        $uAct = $stmtActCheck->fetch(PDO::FETCH_ASSOC);
        if ($uAct && (int)$uAct['active'] === 1) {
            $exp = $uAct['activation_expiry_date'];
            $isActive = empty($exp) || (strtotime($exp) > time());
        }
    }

    if (!$isActive) {
        return [
            'status'  => 'error',
            'message' => 'Please complete your $11 activation before purchasing an investment/package.'
        ];
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
        $stmtUser = $db->prepare("SELECT userid, pin_wallet, deposite_wallet, bonus_30_wallet, total_package FROM user WHERE userid = :uid FOR UPDATE");
        $stmtUser->execute([':uid' => $user_id]);
        $uRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$uRow) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "User {$user_id} not found."];
        }

        $rawBal = (float)($uRow['deposite_wallet'] ?? $uRow['pin_wallet'] ?? 0);
        $pinWalletBalUsd = $rawBal;
        if ($pinWalletBalUsd < $realFundUsd && ($rawBal / 90.0) >= $realFundUsd) {
            $pinWalletBalUsd = round($rawBal / 90.0, 2);
        }
        $pinWalletBalInr = round($pinWalletBalUsd * 90.0, 2);

        if ($pinWalletBalUsd < $realFundUsd) {
            if ($inLocalTxn) $db->rollBack();
            return ['status' => 'error', 'message' => "Insufficient Main Wallet balance. Required: ₹" . number_format($inrAmount, 2) . " ($" . number_format($realFundUsd, 2) . "), Available: ₹" . number_format($pinWalletBalInr, 2) . " ($" . number_format($pinWalletBalUsd, 2) . ")"];
        }

        // 1. Update user pin_wallet, deposite_wallet & total_package
        $updUser = $db->prepare("
            UPDATE user SET
                pin_wallet = GREATEST(0, pin_wallet - :deduct_usd),
                deposite_wallet = GREATEST(0, deposite_wallet - :deduct_usd),
                total_package = total_package + :inr_amt,
                bonus_30_wallet = bonus_30_wallet + :bonus_usd,
                upgrade_date = :cdate,
                atime = :ctime
            WHERE userid = :uid
        ");
        $updUser->execute([
            ':deduct_usd' => $realFundUsd,
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
}

/**
 * Server-side Capital Withdrawal Validation & Processing.
 */
if (!function_exists('processCapitalWithdrawal')) {
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
        $lockMonths   = (int)($inv['lock_period_months'] ?? 0);
        $invDate      = $inv['date'] ?: $cDate;

        // Strict Server-Side Lock Check: Auto-resolve lock months according to package rules if missing
        if ($lockMonths <= 0 || empty($maturityDate)) {
            $rules = determinePackageLockingRules($inv['name'] ?? '', $inv['package_code'] ?? '');
            if ($lockMonths <= 0) {
                $lockMonths = $rules['lock_period_months'];
            }
            if (empty($maturityDate)) {
                $maturityDate = date('Y-m-d', strtotime("+{$lockMonths} months", strtotime($invDate)));
            }
        }

        $computedMaturity = $maturityDate;
        if ($cDate < $computedMaturity) {
            if ($inLocalTxn) $db->rollBack();
            return [
                'status'  => 'error',
                'message' => "Capital is locked for {$lockMonths} months. Maturity date is {$computedMaturity}. Early capital withdrawal is blocked."
            ];
        }

        $realFundUsd  = (float)($inv['real_fund_usd'] > 0 ? $inv['real_fund_usd'] : round(((float)$inv['package']) / 90.0, 2));
        $deductPct    = 15.00; // Strictly 15% deduction on capital withdrawal
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

if (!function_exists('parseInputToUSD')) {
    function parseInputToUSD($inputAmount, $inputCurrency = null, $pdoConnection = null) {
        $curr = $inputCurrency ? strtoupper(trim($inputCurrency)) : getUserCurrency(null, $pdoConnection);
        $amt = (float)$inputAmount;
        if ($curr === 'INR') {
            $rate = getUSDToINRRate($pdoConnection);
            return ($rate > 0) ? round($amt / $rate, 2) : $amt;
        }
        return round($amt, 2);
    }
}

if (!function_exists('getUserActiveInvestmentTotal')) {
    function getUserActiveInvestmentTotal($userid, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || empty($userid)) {
            return ['total_usd' => 0.00, 'total_inr' => 0.00, 'active_count' => 0, 'investments' => []];
        }

        $sql = "
            SELECT id, package_code, real_fund_usd, package, date, time, count, lock_day, lock_period_months, maturity_date, capital_withdrawal_status, status
            FROM tbl_roi_one
            WHERE user_id = :uid 
              AND status = '0' 
              AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
            ORDER BY id DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':uid' => $userid]);
        $activeRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalUsd = 0.00;
        $totalInr = 0.00;

        foreach ($activeRows as $row) {
            $pkgInr = (float)($row['package'] ?? 0);
            $pkgUsd = (float)($row['real_fund_usd'] ?? 0);
            if ($pkgUsd <= 0 && $pkgInr > 0) {
                $pkgUsd = parseInputToUSD($pkgInr, 'INR', $db);
            }
            if ($pkgInr <= 0 && $pkgUsd > 0) {
                $pkgInr = round($pkgUsd * getUSDToINRRate($db), 2);
            }
            $totalUsd += $pkgUsd;
            $totalInr += $pkgInr;
        }

        // Fallback: If no records in tbl_roi_one, check user.active_investment or user.total_package
        if ($totalUsd <= 0 && $totalInr <= 0) {
            $stmtU = $db->prepare("SELECT active_investment, total_package FROM user WHERE userid = :uid LIMIT 1");
            $stmtU->execute([':uid' => $userid]);
            $uRow = $stmtU->fetch(PDO::FETCH_ASSOC);
            if ($uRow) {
                $actInv = (float)($uRow['active_investment'] ?? 0);
                $totPkg = (float)($uRow['total_package'] ?? 0);
                if ($actInv > 0) {
                    $totalUsd = $actInv;
                    $totalInr = round($actInv * getUSDToINRRate($db), 2);
                } elseif ($totPkg > 0) {
                    $totalInr = $totPkg;
                    $totalUsd = parseInputToUSD($totPkg, 'INR', $db);
                }
            }
        }

        return [
            'total_usd'    => round($totalUsd, 2),
            'total_inr'    => round($totalInr, 2),
            'active_count' => count($activeRows),
            'investments'  => $activeRows
        ];
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

if (!function_exists('formatCurrencyFromINR')) {
    function formatCurrencyFromINR($amountInINR, $targetCurrency = null, $includeSymbol = true, $pdoConnection = null) {
        $rate = getUSDToINRRate($pdoConnection);
        $usd = ($rate > 0) ? ((float)$amountInINR / $rate) : (float)$amountInINR;
        return formatCurrency($usd, $targetCurrency, $includeSymbol, $pdoConnection);
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

        if ($activeFlag === 1) {
            if ($expiryDate) {
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
            } else {
                return [
                    'status'         => 'ACTIVE',
                    'active_flag'    => 1,
                    'is_active'      => true,
                    'is_expired'     => false,
                    'start_date'     => $startDate,
                    'expiry_date'    => null,
                    'remaining_days' => 1460,
                    'status_label'   => 'ACTIVE'
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

if (!function_exists('getVIPClubReportStats')) {
    function getVIPClubReportStats($pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        
        $totalQualified = 0;
        $totalPayout = 0.00;
        $activeLevels = 0;

        try {
            $totalQualified = (int)($db->query("SELECT COUNT(*) FROM tbl_vip_user_qualifications WHERE is_qualified = 1")->fetchColumn() ?: 0);
        } catch (\Throwable $e) {
            $totalQualified = 0;
        }

        try {
            $totalPayout = (float)($db->query("SELECT COALESCE(SUM(credited_amount), 0) FROM tbl_vip_closing_schedule WHERE status = 'COMPLETED'")->fetchColumn() ?: 0);
        } catch (\Throwable $e) {
            $totalPayout = 0.00;
        }

        try {
            $activeLevels = (int)($db->query("SELECT COUNT(*) FROM tbl_vip_level_config")->fetchColumn() ?: 0);
        } catch (\Throwable $e) {
            $activeLevels = 0;
        }

        return [
            'total_qualified_users' => $totalQualified,
            'total_payout_distributed' => $totalPayout,
            'active_vip_levels' => $activeLevels
        ];
    }
}

/**
 * Helper to fetch tree details and downline counts for a user.
 */
if (!function_exists('getTreeUserDetails')) {
    function getTreeUserDetails($userId, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || empty($userId)) return null;

        $cleanUid = preg_replace('/^(AN|ANANTA)/i', '', trim($userId));
        $prefixedUid = 'AN' . $cleanUid;
        $anantaUid = 'ANANTA' . $cleanUid;

        $stmtUser = $db->prepare("SELECT * FROM user WHERE userid = :clean OR userid = :prefixed OR userid = :ananta LIMIT 1");
        $stmtUser->execute([':clean' => $cleanUid, ':prefixed' => $prefixedUid, ':ananta' => $anantaUid]);
        $u = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$u) return null;
        $actualUid = $u['userid'];

        $stmtTree = $db->prepare("SELECT * FROM tree WHERE userid = :uid LIMIT 1");
        $stmtTree->execute([':uid' => $actualUid]);
        $t = $stmtTree->fetch(PDO::FETCH_ASSOC);

        // Find current parent
        $stmtParent = $db->prepare("SELECT * FROM tree WHERE left_id = :uid OR right_id = :uid LIMIT 1");
        $stmtParent->execute([':uid' => $actualUid]);
        $parentRow = $stmtParent->fetch(PDO::FETCH_ASSOC);

        $currentParentId = $parentRow ? $parentRow['userid'] : 'ROOT / NONE';
        $currentPosition = 'NONE';
        if ($parentRow) {
            if ($parentRow['left_id'] === $actualUid) {
                $currentPosition = 'LEFT';
            } elseif ($parentRow['right_id'] === $actualUid) {
                $currentPosition = 'RIGHT';
            }
        }

        // Direct children
        $directChildren = [];
        if ($t) {
            if (!empty($t['left_id'])) $directChildren[] = ['userid' => $t['left_id'], 'position' => 'LEFT'];
            if (!empty($t['right_id'])) $directChildren[] = ['userid' => $t['right_id'], 'position' => 'RIGHT'];
        }

        // Calculate complete downline count recursively
        $downlineIds = getSubtreeDescendantIds($actualUid, $db);

        return [
            'userid' => $actualUid,
            'name' => $u['name'],
            'sponserid' => $u['sponserid'] ?? '',
            'sponsername' => $u['sponsername'] ?? '',
            'current_parent' => $currentParentId,
            'current_position' => $currentPosition,
            'left_child' => $t['left_id'] ?? '',
            'right_child' => $t['right_id'] ?? '',
            'left_count' => (int)($t['leftcount'] ?? 0),
            'right_count' => (int)($t['rightcount'] ?? 0),
            'left_total' => (int)($t['lefttotal'] ?? 0),
            'right_total' => (int)($t['righttotal'] ?? 0),
            'direct_children' => $directChildren,
            'downline_count' => count($downlineIds),
            'downline_ids' => $downlineIds
        ];
    }
}

/**
 * Returns all descendant user IDs in the binary tree below $userId.
 */
if (!function_exists('getSubtreeDescendantIds')) {
    function getSubtreeDescendantIds($userId, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || empty($userId)) return [];

        $descendants = [];
        $queue = [$userId];

        while (!empty($queue)) {
            $curr = array_shift($queue);
            $stmt = $db->prepare("SELECT left_id, right_id FROM tree WHERE userid = :uid LIMIT 1");
            $stmt->execute([':uid' => $curr]);
            $t = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($t) {
                if (!empty($t['left_id']) && !in_array($t['left_id'], $descendants)) {
                    $descendants[] = $t['left_id'];
                    $queue[] = $t['left_id'];
                }
                if (!empty($t['right_id']) && !in_array($t['right_id'], $descendants)) {
                    $descendants[] = $t['right_id'];
                    $queue[] = $t['right_id'];
                }
            }
        }

        return $descendants;
    }
}

/**
 * Check new parent availability for LEFT and RIGHT slots.
 */
if (!function_exists('getNewParentAvailability')) {
    function getNewParentAvailability($targetUserId, $newParentId, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || empty($newParentId)) {
            return ['status' => 'error', 'message' => 'New parent ID is required.'];
        }

        $targetUser = getTreeUserDetails($targetUserId, $db);
        if (!$targetUser) {
            return ['status' => 'error', 'message' => 'Selected target user not found.'];
        }

        $parentUser = getTreeUserDetails($newParentId, $db);
        if (!$parentUser) {
            return ['status' => 'error', 'message' => 'Selected new parent user not found.'];
        }

        // Root protection check: Master admin 1290 cannot be moved under someone else as a child
        if ($targetUser['userid'] === 'AN1290' || $targetUser['userid'] === '1290') {
            return ['status' => 'error', 'message' => 'Master Root Admin (AN1290) cannot be moved under another parent.'];
        }

        // Check self-parent
        if ($targetUser['userid'] === $parentUser['userid']) {
            return ['status' => 'error', 'message' => 'Target user and New Parent user cannot be the same.'];
        }

        // Circular Tree Check: New Parent cannot be a descendant of target user
        if (in_array($parentUser['userid'], $targetUser['downline_ids'])) {
            return ['status' => 'error', 'message' => "Invalid parent. You cannot move a user under their own downline."];
        }

        // Check left and right slot occupancy
        $stmtParentTree = $db->prepare("SELECT left_id, right_id FROM tree WHERE userid = :uid LIMIT 1");
        $stmtParentTree->execute([':uid' => $parentUser['userid']]);
        $pTree = $stmtParentTree->fetch(PDO::FETCH_ASSOC);

        $leftOccupied = !empty($pTree['left_id']);
        $leftOccupant = $pTree['left_id'] ?? '';
        $rightOccupied = !empty($pTree['right_id']);
        $rightOccupant = $pTree['right_id'] ?? '';

        // Allow slot if currently occupied by the same target user (same position check)
        $leftValid = !$leftOccupied || ($leftOccupant === $targetUser['userid']);
        $rightValid = !$rightOccupied || ($rightOccupant === $targetUser['userid']);

        return [
            'status' => 'success',
            'target_user' => $targetUser,
            'new_parent' => $parentUser,
            'left' => [
                'occupied' => $leftOccupied,
                'occupant' => $leftOccupant,
                'valid'    => $leftValid
            ],
            'right' => [
                'occupied' => $rightOccupied,
                'occupant' => $rightOccupant,
                'valid'    => $rightValid
            ]
        ];
    }
}

/**
 * Executes Atomic Team Move in Binary Tree with complete validation and audit logging.
 */

if (!function_exists('verifyMovedUserTreeIntegrity')) {
    /**
     * Verifies full tree + user table integrity for a moved user.
     * Returns ['pass' => true/false, 'errors' => [...]]
     *
     * Checks:
     *  A. User exists in user table.
     *  B. Exactly ONE tree parent references this user.
     *  C. That parent === $expectedParentId.
     *  D. That side  === $expectedSide ('left' or 'right').
     *  E. user.underuserid === $expectedParentId.
     *  F. user.join_side   === $expectedSide.
     *  G. user.sponserid   === $originalSponsorId (unchanged).
     *  H. No duplicate tree references (child in 2+ parents).
     *  I. Old parent does NOT reference this user.
     *  J. New parent contains user exactly once (not in both slots).
     */
if (!function_exists('verifyMovedUserTreeIntegrity')) {
    function verifyMovedUserTreeIntegrity($db, $userId, $expectedParentId, $expectedSide, $originalSponsorId, $oldParentId = null) {
        $errors = [];
        $expectedSide = strtolower($expectedSide);

        // A. User exists
        $uRow = $db->prepare("SELECT userid, underuserid, join_side, sponserid FROM user WHERE userid = :uid");
        $uRow->execute([':uid' => $userId]);
        $userRec = $uRow->fetch(PDO::FETCH_ASSOC);
        if (!$userRec) {
            $errors[] = "A: User {$userId} not found in user table.";
            return ['pass' => false, 'errors' => $errors];
        }

        // B. Exactly ONE tree parent references this user
        $pStmt = $db->prepare("SELECT userid, left_id, right_id FROM tree WHERE left_id = :uid OR right_id = :uid");
        $pStmt->execute([':uid' => $userId]);
        $parentRefs = $pStmt->fetchAll(PDO::FETCH_ASSOC);
        $parentCount = count($parentRefs);

        if ($parentCount === 0) {
            $errors[] = "B: User {$userId} has NO parent in tree table (orphan).";
        } elseif ($parentCount > 1) {
            $ids = array_column($parentRefs, 'userid');
            $errors[] = "B/H: User {$userId} has {$parentCount} parents in tree (DUPLICATE): " . implode(', ', $ids);
        }

        if ($parentCount === 1) {
            $actualParentRow  = $parentRefs[0];
            $actualParentId   = (string)$actualParentRow['userid'];
            $actualSide       = ($actualParentRow['left_id'] === $userId) ? 'left' : 'right';

            // C. Correct parent
            if ($actualParentId !== (string)$expectedParentId) {
                $errors[] = "C: Tree parent is {$actualParentId}, expected {$expectedParentId}.";
            }

            // D. Correct side
            if ($actualSide !== $expectedSide) {
                $errors[] = "D: Tree side is {$actualSide}, expected {$expectedSide}.";
            }

            // J. New parent does NOT have user in BOTH slots
            if ($actualParentRow['left_id'] === $userId && $actualParentRow['right_id'] === $userId) {
                $errors[] = "J: User {$userId} exists in BOTH left and right slots of parent {$actualParentId}.";
            }
        }

        // E. user.underuserid correct
        if ((string)($userRec['underuserid'] ?? '') !== (string)$expectedParentId) {
            $errors[] = "E: user.underuserid={$userRec['underuserid']}, expected {$expectedParentId}.";
        }

        // F. user.join_side correct
        if (strtolower((string)($userRec['join_side'] ?? '')) !== $expectedSide) {
            $errors[] = "F: user.join_side={$userRec['join_side']}, expected {$expectedSide}.";
        }

        // G. Sponsorid unchanged
        if (!empty($originalSponsorId) && (string)($userRec['sponserid'] ?? '') !== (string)$originalSponsorId) {
            $errors[] = "G: user.sponserid changed! Was {$originalSponsorId}, now {$userRec['sponserid']}.";
        }

        // I. Old parent no longer references user
        if (!empty($oldParentId) && $oldParentId !== $expectedParentId) {
            $opStmt = $db->prepare("SELECT left_id, right_id FROM tree WHERE userid = :pid");
            $opStmt->execute([':pid' => $oldParentId]);
            $opRow = $opStmt->fetch(PDO::FETCH_ASSOC);
            if ($opRow) {
                if (($opRow['left_id'] ?? '') === $userId || ($opRow['right_id'] ?? '') === $userId) {
                    $errors[] = "I: Old parent {$oldParentId} still references user {$userId}.";
                }
            }
        }

        return ['pass' => empty($errors), 'errors' => $errors];
    }
}
}

if (!function_exists('processAdminMoveTeamInTree')) {
    function processAdminMoveTeamInTree($adminId, $targetUserId, $newParentId, $targetPosition, $reason = '', $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || empty($adminId) || empty($targetUserId) || empty($newParentId) || empty($targetPosition)) {
            return ['status' => 'error', 'message' => 'All parameters (Admin ID, Target User, New Parent, Position) are required.'];
        }
        if (empty(trim($reason))) {
            return ['status' => 'error', 'message' => 'Reason / Remarks is mandatory for moving a user in the tree.'];
        }

        $targetPosition = strtoupper(trim($targetPosition));
        if (!in_array($targetPosition, ['LEFT', 'RIGHT'])) {
            return ['status' => 'error', 'message' => 'Invalid position specified. Must be LEFT or RIGHT.'];
        }

        $inLocalTxn = false;
        if (!$db->inTransaction()) {
            $db->beginTransaction();
            $inLocalTxn = true;
        }

        try {
            // 1. Lock and validate Target User
            $stmtT = $db->prepare("SELECT * FROM user WHERE userid = :uid FOR UPDATE");
            $stmtT->execute([':uid' => $targetUserId]);
            $targetRow = $stmtT->fetch(PDO::FETCH_ASSOC);

            if (!$targetRow) {
                // Fallback clean check
                $cleanT = preg_replace('/^(AN|ANANTA)/i', '', $targetUserId);
                $stmtT->execute([':uid' => $cleanT]);
                $targetRow = $stmtT->fetch(PDO::FETCH_ASSOC);
            }

            if (!$targetRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target user {$targetUserId} not found."];
            }
            $actualTargetId   = $targetRow['userid'];
            $originalSponsorId = (string)($targetRow['sponserid'] ?? ''); // SAVE before any changes

            // Master root admin check
            if ($actualTargetId === '1290' || $actualTargetId === 'AN1290') {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => 'Master Root Admin (AN1290) cannot be moved in the tree.'];
            }

            // 2. Lock and validate New Parent User
            $stmtP = $db->prepare("SELECT * FROM user WHERE userid = :uid FOR UPDATE");
            $stmtP->execute([':uid' => $newParentId]);
            $parentRow = $stmtP->fetch(PDO::FETCH_ASSOC);

            if (!$parentRow) {
                $cleanP = preg_replace('/^(AN|ANANTA)/i', '', $newParentId);
                $stmtP->execute([':uid' => $cleanP]);
                $parentRow = $stmtP->fetch(PDO::FETCH_ASSOC);
            }

            if (!$parentRow) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "New parent user {$newParentId} not found."];
            }
            $actualParentId = $parentRow['userid'];

            // 3. Validation: Self parent check
            if ($actualTargetId === $actualParentId) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => 'Target User and New Parent User cannot be the same.'];
            }

            // 4. Validation: Circular tree check (New Parent is in Target User's downline)
            $downlineIds = getSubtreeDescendantIds($actualTargetId, $db);
            if (in_array($actualParentId, $downlineIds)) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Invalid parent. You cannot move a user under their own downline."];
            }

            // 5. Fetch Target User's current parent in tree table
            $stmtCurrParent = $db->prepare("SELECT * FROM tree WHERE left_id = :uid OR right_id = :uid FOR UPDATE");
            $stmtCurrParent->execute([':uid' => $actualTargetId]);
            $oldParentTree = $stmtCurrParent->fetch(PDO::FETCH_ASSOC);

            $oldParentId = $oldParentTree ? $oldParentTree['userid'] : 'NONE';
            $oldPosition = 'NONE';
            if ($oldParentTree) {
                if ($oldParentTree['left_id'] === $actualTargetId) {
                    $oldPosition = 'LEFT';
                } elseif ($oldParentTree['right_id'] === $actualTargetId) {
                    $oldPosition = 'RIGHT';
                }
            }

            // Check same parent and same position
            if ($oldParentId === $actualParentId && $oldPosition === $targetPosition) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "User {$actualTargetId} is already attached to Parent {$actualParentId} on position {$targetPosition}."];
            }

            // 6. Check target position availability on new parent
            $stmtNewParentTree = $db->prepare("SELECT * FROM tree WHERE userid = :uid FOR UPDATE");
            $stmtNewParentTree->execute([':uid' => $actualParentId]);
            $newParentTree = $stmtNewParentTree->fetch(PDO::FETCH_ASSOC);

            if (!$newParentTree) {
                // Ensure tree record exists for new parent
                $db->prepare("INSERT INTO tree (userid, left_id, right_id, leftcount, rightcount, status, join_side, leftsp, rightsp, lefttotal, righttotal) VALUES (:uid, '', '', 0, 0, 1, '', 0, 0, 0, 0)")
                   ->execute([':uid' => $actualParentId]);
                $stmtNewParentTree->execute([':uid' => $actualParentId]);
                $newParentTree = $stmtNewParentTree->fetch(PDO::FETCH_ASSOC);
            }

            $colToOccupy = ($targetPosition === 'LEFT') ? 'left_id' : 'right_id';
            $currentOccupant = $newParentTree[$colToOccupy] ?? '';

            if (!empty($currentOccupant) && $currentOccupant !== $actualTargetId) {
                if ($inLocalTxn) $db->rollBack();
                return ['status' => 'error', 'message' => "Target position {$targetPosition} under Parent {$actualParentId} is already occupied by User {$currentOccupant}."];
            }

            // 7. Remove Target User from ALL Old Parents globally
            $db->prepare("UPDATE tree SET left_id = IF(left_id = :tid, '', left_id), right_id = IF(right_id = :tid, '', right_id) WHERE left_id = :tid OR right_id = :tid")
               ->execute([':tid' => $actualTargetId]);

            // 8. Attach Target User to New Parent's tree record slot
            if ($targetPosition === 'LEFT') {
                $db->prepare("UPDATE tree SET left_id = :tid WHERE userid = :pid")->execute([':tid' => $actualTargetId, ':pid' => $actualParentId]);
            } else {
                $db->prepare("UPDATE tree SET right_id = :tid WHERE userid = :pid")->execute([':tid' => $actualTargetId, ':pid' => $actualParentId]);
            }

            // Sync user table placement records (underuserid and join_side) -- SPONSERID REMAINS UNTOUCHED
            $db->prepare("UPDATE user SET underuserid = :pid, join_side = :side WHERE userid = :tid")
               ->execute([
                   ':pid'  => $actualParentId,
                   ':side' => strtolower($targetPosition),
                   ':tid'  => $actualTargetId
               ]);

            // Ensure tree record exists for Target User
            $stmtTargetTree = $db->prepare("SELECT * FROM tree WHERE userid = :uid FOR UPDATE");
            $stmtTargetTree->execute([':uid' => $actualTargetId]);
            if (!$stmtTargetTree->fetch()) {
                $db->prepare("INSERT INTO tree (userid, left_id, right_id, leftcount, rightcount, status, join_side, leftsp, rightsp, lefttotal, righttotal) VALUES (:uid, '', '', 0, 0, 1, '', 0, 0, 0, 0)")
                   ->execute([':uid' => $actualTargetId]);
            }

            // Clean up any global tree duplicates
            cleanupGlobalTreeDuplicates($db);

            // 9. Post-move Full Integrity Verification (generic, all users)
            $integrityResult = verifyMovedUserTreeIntegrity(
                $db,
                $actualTargetId,
                $actualParentId,
                strtolower($targetPosition),
                $originalSponsorId,
                ($oldParentId !== 'NONE') ? $oldParentId : null
            );

            if (!$integrityResult['pass']) {
                $errList = implode(' | ', $integrityResult['errors']);
                throw new Exception("Post-move integrity FAILED — rolling back. Errors: {$errList}");
            }

            // 9b. Generic check: no other parent in tree should reference moved user
            $allParentCheck = $db->prepare("SELECT userid FROM tree WHERE (left_id = :tid OR right_id = :tid) AND userid != :pid");
            $allParentCheck->execute([':tid' => $actualTargetId, ':pid' => $actualParentId]);
            $staleParents = $allParentCheck->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($staleParents)) {
                throw new Exception("Post-move stale references found in tree for parents: " . implode(', ', $staleParents) . ". Rolling back.");
            }

            // 10. Audit Log Entry
            $totalTeamMoved = count($downlineIds) + 1;
            $auditRemarks = "Admin {$adminId} moved user {$actualTargetId} ({$targetRow['name']}) and team of {$totalTeamMoved} members from Parent {$oldParentId} ({$oldPosition}) to New Parent {$actualParentId} ({$targetPosition}). Reason: " . ($reason ?: 'Admin Manual Move');

            logAdminAuditAction(
                $adminId,
                'MOVE_TEAM_IN_TREE',
                $actualTargetId,
                0.00,
                'tree',
                1,
                0,
                $auditRemarks,
                null,
                $db
            );

            // Optional User Notification
            try {
                $db->prepare("
                    INSERT INTO tbl_user_notifications (user_id, title, message, type, is_read, created_at)
                    VALUES (:uid, 'Team Tree Location Updated', :msg, 'SYSTEM', 0, NOW())
                ")->execute([
                    ':uid' => $actualTargetId,
                    ':msg' => "Your binary tree placement has been updated by Admin to Parent: {$actualParentId} ({$targetPosition} Position)."
                ]);
            } catch (Exception $ne) {
                // Ignore if notification table column schema varies
            }

            // 11. Automatic Instant Tree & Downline Re-indexing for All Users & Uplines
            rebuildFullTreeAndDownlineIndexes($db);

            if ($inLocalTxn) {
                $db->commit();
            }

            return [
                'status'           => 'success',
                'message'          => "User {$actualTargetId} and complete team of {$totalTeamMoved} members successfully moved under New Parent {$actualParentId} on position {$targetPosition}!",
                'user_id'          => $actualTargetId,
                'user_name'        => $targetRow['name'],
                'old_parent'       => $oldParentId,
                'old_position'     => $oldPosition,
                'new_parent'       => $actualParentId,
                'new_position'     => $targetPosition,
                'downline_moved'   => count($downlineIds),
                'total_team_moved' => $totalTeamMoved
            ];

        } catch (Exception $e) {
            if ($inLocalTxn && $db->inTransaction()) {
                $db->rollBack();
            }
            return [
                'status'  => 'error',
                'message' => 'Move Team transaction failed and rolled back. Error: ' . $e->getMessage()
            ];
        }
    }
}

/**
 * Permanently deletes a user account and all associated user records atomically.
 *
 * @param string $admin_id Admin performing the deletion
 * @param string $target_user_id Target user ID (clean or prefixed)
 * @param PDO|null $pdoConnection
 * @return array Response status and message
 */
if (!function_exists('processPermanentUserAccountDeletion')) {
function processPermanentUserAccountDeletion($admin_id, $target_user_id, $pdoConnection = null) {
    global $pdo;
    $db = $pdoConnection ?: $pdo;

    if (!$db || empty($target_user_id)) {
        return ['status' => 'error', 'message' => 'Target User ID is required.'];
    }

    $cleanUid = preg_replace('/^(AN|ANANTA)/i', '', trim($target_user_id));
    $prefixedUid = 'AN' . $cleanUid;
    $anantaUid = 'ANANTA' . $cleanUid;

    // Prevent Admin deleting master admin account (AN1290 / 1290)
    if ($cleanUid === '1290' || strtolower($target_user_id) === 'an1290' || strtolower($target_user_id) === 'ananta1290') {
        return ['status' => 'error', 'message' => 'Admin master account (AN1290) cannot be deleted.'];
    }

    try {
        $db->beginTransaction();

        // 1. Lock and fetch target user record
        $stmtUser = $db->prepare("SELECT * FROM user WHERE userid = :clean OR userid = :prefixed OR userid = :ananta FOR UPDATE");
        $stmtUser->execute([':clean' => $cleanUid, ':prefixed' => $prefixedUid, ':ananta' => $anantaUid]);
        $userRecord = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$userRecord) {
            $db->rollBack();
            return ['status' => 'error', 'message' => "Target user {$target_user_id} not found."];
        }

        $actualUserId = $userRecord['userid'];

        // 2. Safely reassign downline sponsors so tree structure for other users remains valid
        $sponsorOfDeleted = $userRecord['sponserid'] ?? '';
        $sponsorNameOfDeleted = $userRecord['sponsername'] ?? '';

        $updateDownline = $db->prepare("UPDATE user SET sponserid = :newsponsor, sponsername = :newsponsorname WHERE sponserid = :clean OR sponserid = :prefixed OR sponserid = :actual");
        $updateDownline->execute([
            ':newsponsor'     => $sponsorOfDeleted,
            ':newsponsorname' => $sponsorNameOfDeleted,
            ':clean'          => $cleanUid,
            ':prefixed'       => $prefixedUid,
            ':actual'         => $actualUserId
        ]);

        $uParams = [
            ':clean'    => $cleanUid,
            ':prefixed' => $prefixedUid,
            ':actual'   => $actualUserId
        ];

        // 3. Delete records from all user-related tables with 'userid' column
        $useridTables = [
            'kyc', 'pin_generate_detail', 'pin_list', 'tbl_cart', 'tbl_check_user', 
            'tbl_deposit_withdrawal_history', 'tbl_log', 'tbl_order', 'tbl_otp', 
            'tbl_payment', 'tbl_pool2', 'tbl_pool3', 'tbl_query', 'tbl_royalty_user', 
            'tbl_singleg_user', 'tbl_singleg_user2', 'tbl_singleg_user3', 'tree', 'user1'
        ];
        foreach ($useridTables as $tbl) {
            try {
                $stmt = $db->prepare("DELETE FROM `$tbl` WHERE userid = :clean OR userid = :prefixed OR userid = :actual");
                $stmt->execute($uParams);
            } catch (Exception $e) {
                // Ignore if table/column does not exist in specific environments
            }
        }

        // 4. Delete records from all user-related tables with 'user_id' column
        $userIdTables = [
            'tbl_bonus', 'tbl_capital_withdrawal_request', 'tbl_daily_levelinc', 
            'tbl_direct_bonus_admin_audit', 'tbl_directinc', 'tbl_flush', 'tbl_franchise', 
            'tbl_inr_deposits', 'tbl_levelinc', 'tbl_mentor_income_admin_audit', 
            'tbl_oneroyalty_user', 'tbl_recharge', 'tbl_repurchase_data', 'tbl_rewardinc', 
            'tbl_roi_one', 'tbl_roi_three', 'tbl_roi_two', 'tbl_roiinc', 'tbl_sponserinc', 
            'tbl_support_tickets', 'tbl_temp_data', 'tbl_transaction', 'tbl_user_login_history', 
            'tbl_user_notifications', 'tbl_user_welcome', 'tbl_vip_admin_audit', 
            'tbl_vip_monthly_schedule', 'tbl_vip_user_qualification'
        ];
        foreach ($userIdTables as $tbl) {
            try {
                $stmt = $db->prepare("DELETE FROM `$tbl` WHERE user_id = :clean OR user_id = :prefixed OR user_id = :actual");
                $stmt->execute($uParams);
            } catch (Exception $e) {
                // Ignore if table/column does not exist
            }
        }

        // 5. Special multi-column / specific relationship tables
        $specialQueries = [
            "DELETE FROM pin_transfer WHERE reciever_sponser = :clean OR reciever_sponser = :prefixed OR reciever_sponser = :actual OR sender_sponser = :clean OR sender_sponser = :prefixed OR sender_sponser = :actual",
            "DELETE FROM tbl_account_activation WHERE activator_user_id = :clean OR activator_user_id = :prefixed OR activator_user_id = :actual OR target_user_id = :clean OR target_user_id = :prefixed OR target_user_id = :actual",
            "DELETE FROM tbl_beneficiary_acount WHERE user_id = :clean OR user_id = :prefixed OR user_id = :actual OR sender_id = :clean OR sender_id = :prefixed OR sender_id = :actual",
            "DELETE FROM tbl_direct_bonus_schedule WHERE source_user_id = :clean OR source_user_id = :prefixed OR source_user_id = :actual",
            "DELETE FROM tbl_downline WHERE downline_id = :clean OR downline_id = :prefixed OR downline_id = :actual",
            "DELETE FROM tbl_imps_sender WHERE user_id = :clean OR user_id = :prefixed OR user_id = :actual OR sender_id = :clean OR sender_id = :prefixed OR sender_id = :actual",
            "DELETE FROM tbl_mentor_direct_contribution WHERE direct_user_id = :clean OR direct_user_id = :prefixed OR direct_user_id = :actual",
            "DELETE FROM tbl_mentor_income_schedule WHERE direct_user_id = :clean OR direct_user_id = :prefixed OR direct_user_id = :actual",
            "DELETE FROM tbl_p2p_transfer WHERE sender_id = :clean OR sender_id = :prefixed OR sender_id = :actual OR receiver_id = :clean OR receiver_id = :prefixed OR receiver_id = :actual",
            "DELETE FROM tbl_sponsor WHERE sponsor_id = :clean OR sponsor_id = :prefixed OR sponsor_id = :actual",
            "DELETE FROM tbl_system_notifications WHERE target_user_id = :clean OR target_user_id = :prefixed OR target_user_id = :actual",
            "DELETE FROM tbl_ticket_replies WHERE sender_id = :clean OR sender_id = :prefixed OR sender_id = :actual",
            "DELETE FROM tbl_userlevel WHERE sponser_id = :clean OR sponser_id = :prefixed OR sponser_id = :actual OR downline_id = :clean OR downline_id = :prefixed OR downline_id = :actual",
            "DELETE FROM tbl_userlevel_a WHERE sponser_id = :clean OR sponser_id = :prefixed OR sponser_id = :actual OR downline_id = :clean OR downline_id = :prefixed OR downline_id = :actual",
            "DELETE FROM tbl_userlevel_b WHERE sponser_id = :clean OR sponser_id = :prefixed OR sponser_id = :actual OR downline_id = :clean OR downline_id = :prefixed OR downline_id = :actual"
        ];

        foreach ($specialQueries as $sq) {
            try {
                $db->prepare($sq)->execute($uParams);
            } catch (Exception $e) {
                // Ignore if table does not exist
            }
        }

        // 6. Log Admin Audit Action BEFORE deleting primary user record
        logAdminAuditAction(
            $admin_id, 
            'USER_PERMANENT_DELETE', 
            $actualUserId, 
            0.00, 
            'user', 
            1, 
            0, 
            "Admin {$admin_id} permanently deleted user account {$actualUserId} ({$userRecord['name']}) and all associated records.", 
            null, 
            $db
        );

        // 7. Finally delete the primary user row
        $stmtDeleteUser = $db->prepare("DELETE FROM user WHERE userid = :clean OR userid = :prefixed OR userid = :actual");
        $stmtDeleteUser->execute($uParams);

        // 8. Verify user row is gone
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM user WHERE userid = :clean OR userid = :prefixed OR userid = :actual");
        $stmtCheck->execute($uParams);
        if ((int)$stmtCheck->fetchColumn() > 0) {
            throw new Exception("User record could not be removed from user table.");
        }

        $db->commit();

        return [
            'status'  => 'success',
            'message' => "Account {$actualUserId} permanently deleted successfully."
        ];

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return [
            'status'  => 'error',
            'message' => 'Account deletion failed. No changes were made. Error: ' . $e->getMessage()
        ];
    }
}
}

// Standalone helper — declared outside to prevent fatal "Cannot redeclare" on multiple calls
if (!function_exists('getSubtreeNodesInternalAdmin')) {
    function getSubtreeNodesInternalAdmin($nodeId, &$treeMap) {
        if (empty($nodeId) || !isset($treeMap[$nodeId])) return [];
        $nodes = [];
        $left  = $treeMap[$nodeId]['left'];
        $right = $treeMap[$nodeId]['right'];
        if (!empty($left)) {
            $nodes[] = $left;
            $nodes = array_merge($nodes, getSubtreeNodesInternalAdmin($left, $treeMap));
        }
        if (!empty($right)) {
            $nodes[] = $right;
            $nodes = array_merge($nodes, getSubtreeNodesInternalAdmin($right, $treeMap));
        }
        return array_unique($nodes);
    }
}

if (!function_exists('rebuildFullTreeAndDownlineIndexes')) {
    function rebuildFullTreeAndDownlineIndexes($dbConnection = null) {
        global $pdo;
        $db = $dbConnection ?: $pdo;
        if (!$db) return;

        try {
            // 0. Auto-repair missing tree rows & missing left_id/right_id links from user table
            $allUsers = $db->query("SELECT userid, sponserid, underuserid, join_side FROM user")->fetchAll(PDO::FETCH_ASSOC);
            $existingTreeUsers = $db->query("SELECT userid FROM tree")->fetchAll(PDO::FETCH_COLUMN);
            $existingTreeSet = array_flip($existingTreeUsers);

            $insTree = $db->prepare("INSERT INTO tree (userid, left_id, right_id, status, join_side, leftsp, rightsp, leftpv, rightpv, leftcount, rightcount, lefttotal, righttotal) VALUES (:uid, '', '', 1, 'left', 0, 0, 0, 0, 0, 0, 0, 0)");
            foreach ($allUsers as $u) {
                if (!isset($existingTreeSet[$u['userid']])) {
                    $insTree->execute([':uid' => $u['userid']]);
                    $existingTreeSet[$u['userid']] = true;
                }
            }

            // 1. Clean up global duplicates across all tree rows
            cleanupGlobalTreeDuplicates($db);

            // 2. Fetch current tree placement state
            $treeRows = $db->query("SELECT userid, left_id, right_id FROM tree")->fetchAll(PDO::FETCH_ASSOC);
            $placedChildSet = array();
            $parentTreeState = array();
            foreach ($treeRows as $tr) {
                $p = (string)$tr['userid'];
                $parentTreeState[$p] = $tr;
                if (!empty($tr['left_id'])) $placedChildSet[(string)$tr['left_id']] = true;
                if (!empty($tr['right_id'])) $placedChildSet[(string)$tr['right_id']] = true;
            }

            $updLeft  = $db->prepare("UPDATE tree SET left_id  = :cid WHERE userid = :pid AND (left_id IS NULL OR left_id = '')");
            $updRight = $db->prepare("UPDATE tree SET right_id = :cid WHERE userid = :pid AND (right_id IS NULL OR right_id = '')");

            foreach ($allUsers as $u) {
                $pId  = (string)($u['underuserid'] ?? '');
                $cId  = (string)($u['userid'] ?? '');
                $side = strtolower((string)($u['join_side'] ?? ''));

                if (!empty($pId) && !empty($cId) && $pId !== $cId && isset($existingTreeSet[$pId]) && !isset($placedChildSet[$cId])) {
                    if ($side === 'left' || $side === 'l') {
                        $updLeft->execute([':cid' => $cId, ':pid' => $pId]);
                    } elseif ($side === 'right' || $side === 'r') {
                        $updRight->execute([':cid' => $cId, ':pid' => $pId]);
                    } else {
                        $updRight->execute([':cid' => $cId, ':pid' => $pId]);
                    }
                    $placedChildSet[$cId] = true;
                }
            }

            // 1. Fetch all users from tree table
            $treeUsers = $db->query("SELECT userid, left_id, right_id FROM tree")->fetchAll(PDO::FETCH_ASSOC);
            $treeMap = [];
            foreach ($treeUsers as $u) {
                $treeMap[$u['userid']] = [
                    'left'  => $u['left_id'],
                    'right' => $u['right_id']
                ];
            }

            // 2. Clean mapping tables
            $db->exec("DELETE FROM tbl_userlevel_a");
            $db->exec("DELETE FROM tbl_userlevel_b");
            $db->exec("DELETE FROM tbl_downline");

            $insLvlA = $db->prepare("INSERT INTO tbl_userlevel_a (sponser_id, downline_id, level, date) VALUES (:sp, :dl, :lvl, NOW())");
            $insLvlB = $db->prepare("INSERT INTO tbl_userlevel_b (sponser_id, downline_id, level, date) VALUES (:sp, :dl, :lvl, NOW())");
            $insDown = $db->prepare("INSERT INTO tbl_downline (upline_id, downline_id, date, time) VALUES (:up, :dl, CURDATE(), CURTIME())");
            $updTreeCount = $db->prepare("UPDATE tree SET leftcount = :lc, rightcount = :rc WHERE userid = :uid");

            foreach ($treeMap as $uid => $children) {
                $leftId  = $children['left'];
                $rightId = $children['right'];

                $leftSubtree  = getSubtreeNodesInternalAdmin($leftId, $treeMap);
                if (!empty($leftId)) array_unshift($leftSubtree, $leftId);

                $rightSubtree = getSubtreeNodesInternalAdmin($rightId, $treeMap);
                if (!empty($rightId)) array_unshift($rightSubtree, $rightId);

                $leftCount  = count($leftSubtree);
                $rightCount = count($rightSubtree);

                $updTreeCount->execute([':lc' => $leftCount, ':rc' => $rightCount, ':uid' => $uid]);

                foreach ($leftSubtree as $downlineId) {
                    $insLvlA->execute([':sp' => $uid, ':dl' => $downlineId, ':lvl' => 1]);
                    $insDown->execute([':up' => $uid, ':dl' => $downlineId]);
                }

                foreach ($rightSubtree as $downlineId) {
                    $insLvlB->execute([':sp' => $uid, ':dl' => $downlineId, ':lvl' => 1]);
                    $insDown->execute([':up' => $uid, ':dl' => $downlineId]);
                }
            }

            // 3. Ensure tbl_sponsor sync
            $users = $db->query("SELECT userid, sponserid FROM user WHERE sponserid IS NOT NULL AND sponserid != ''")->fetchAll(PDO::FETCH_ASSOC);

            $chkSpon = $db->prepare("SELECT COUNT(*) FROM tbl_sponsor WHERE sponsor_id = :sp AND referral_id = :ref");
            $insSpon = $db->prepare("INSERT INTO tbl_sponsor (sponsor_id, referral_id, created_date) VALUES (:sp, :ref, CURDATE())");

            foreach ($users as $u) {
                $sp  = $u['sponserid'];
                $ref = $u['userid'];
                if (empty($sp) || empty($ref)) continue;

                $chkSpon->execute([':sp' => $sp, ':ref' => $ref]);
                if ($chkSpon->fetchColumn() == 0) {
                    $insSpon->execute([':sp' => $sp, ':ref' => $ref]);
                }
            }
        } catch (Exception $e) {
            // Log error silently
        }
    }
}

if (!function_exists('getUserWalletBalance')) {
    function getUserWalletBalance($userid, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || !$userid) return 0.0;

        $cleanUid    = preg_replace('/^(AN|ANANTA)/i', '', (string)$userid);
        $prefixedUid = 'AN' . $cleanUid;

        $stmt = $db->prepare("SELECT COALESCE(deposite_wallet, pin_wallet, 0) FROM user WHERE userid = :uid OR userid = :clean OR userid = :prefixed LIMIT 1");
        $stmt->execute([
            ':uid'      => $userid,
            ':clean'    => $cleanUid,
            ':prefixed' => $prefixedUid
        ]);
        return round((float)($stmt->fetchColumn() ?: 0), 2);
    }
}

if (!function_exists('createUserNotification')) {
    function createUserNotification($userId, $type, $title, $message, $refId = null, $pdoConnection = null) {
        global $pdo;
        $db = $pdoConnection ?: $pdo;
        if (!$db || !$userId || !$title || !$message) return false;

        $type = strtoupper(trim($type));
        $validTypes = ['LOGIN', 'DEPOSIT', 'WITHDRAWAL', 'TRANSFER', 'P2P', 'INVESTMENT', 'PACKAGE', 'KYC', 'SECURITY', 'INCOME', 'ADMIN', 'GENERAL', 'SYSTEM'];
        if (!in_array($type, $validTypes, true)) {
            $type = 'GENERAL';
        }
        if ($type === 'PACKAGE') {
            $type = 'INVESTMENT';
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO tbl_user_notifications (user_id, type, title, message, ref_id, is_read, created_at)
                VALUES (:uid, :type, :title, :msg, :ref, 0, NOW())
            ");
            return $stmt->execute([
                ':uid'   => (string)$userId,
                ':type'  => $type,
                ':title' => $title,
                ':msg'   => $message,
                ':ref'   => $refId
            ]);
        } catch (Exception $e) {
            return false;
        }
    }
}
