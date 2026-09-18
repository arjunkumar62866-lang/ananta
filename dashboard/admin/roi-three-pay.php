<?php
session_start();
if (!isset($_SESSION["auserid"])) {
    header("Location:index");
    exit();
}

include("common/connection.php");
include("common/db_method.php");

date_default_timezone_set("Asia/Kolkata");
$today = date("Y-m-d");

/****************************************************
 * FETCH ALL USERS ELIGIBLE FOR RANKING INCOME
 ****************************************************/
 
// 25 lakh business requirement
// $requiredPV = 2500000;
$today = date('Y-m-d');

$users = $pdo->query("SELECT userid, rank, ranking_percentage FROM user")->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $u) {

    $user_id = $u['userid'];
    $user_rank = $u['rank'];
    $ranking_percentage = $u['ranking_percentage'];
    
    $query = $pdo->prepare("SELECT monthly_business from tbl_rewardlevel WHERE rank = :rank");
    $query->execute(['rank'=>$user_rank]);
    $requiredPV1 = $query->fetch(PDO::FETCH_ASSOC);
    $requiredPV = $requiredPV1['monthly_business'];
    // echo($requiredPV);

    // Calculate LEFT & RIGHT business dynamically
    $leftpv  = gettotallevelbusiness_left($user_id);
    $rightpv = gettotallevelbusiness_right($user_id);

    if ($leftpv < $requiredPV || $rightpv < $requiredPV || getmydirectactive($user_id)<10 || getmydirectactiveleft($user_id)<25 || getmydirectactiveright($user_id)<25) {
        continue;
    }

    /****************************************************
     * CHECK PREVIOUS RANKING
     ****************************************************/
    $chk = $pdo->prepare("
        SELECT * FROM tbl_roi_three 
        WHERE user_id = :user_id 
        ORDER BY id DESC LIMIT 1
    ");
    $chk->execute([':user_id' => $user_id]);
    $prev = $chk->fetch(PDO::FETCH_ASSOC);

    $eligible = false;

    // First payout
    if (!$prev) {
        $eligible = true;
    } 
    // Repeat payout
    else {
        $prev_left  = $prev['left_pv'];
        $prev_right = $prev['right_pv'];
        $last_date  = $prev['created_date'];

        if (
            ($leftpv - $prev_left) >= $requiredPV &&
            ($rightpv - $prev_right) >= $requiredPV 
        ) {     
            // ---------------------------------------------------------- aaj ki condition----------------------
                $next_month = date("Y-m-d", strtotime("+1 month", strtotime($last_date)));
                if ($today >= $next_month) {
                    $eligible = true;
                }
                
            }
    }

    if (!$eligible) {
        continue;
    }

    /****************************************************
     * CALCULATE INCOME
     ****************************************************/
    $business = min($leftpv, $rightpv);
    $income   = ($business * $ranking_percentage) / 100; // 0.5%

    /****************************************************
     * INSERT ROI
     ****************************************************/
    $insert = $pdo->prepare("
        INSERT INTO tbl_roi_three
        (user_id, left_pv, right_pv, amount, created_date, status)
        VALUES 
        (:user_id, :leftpv, :rightpv, :amount, :date, '1')
    ");

    $insert->execute([
        ':user_id' => $user_id,
        ':leftpv'  => $leftpv,
        ':rightpv' => $rightpv,
        ':amount'  => $income,
        ':date'    => $today
    ]);

    /****************************************************
     * WALLET UPDATE
     ****************************************************/
    updatenonworkwallet($user_id, $income, $pdo);

    /****************************************************
     * TRANSACTION ENTRY
     ****************************************************/
    $txn = $pdo->prepare("
        INSERT INTO tbl_transaction 
        (user_id, type, subject, amount, created_date, status)
        VALUES 
        (:user_id, 'Ranking Income', 'Ranking Income Payout', :amount, :date, '1')
    ");

    $txn->execute([
        ':user_id' => $user_id,
        ':amount'  => $income,
        ':date'    => $today
    ]);
}

?>
<meta http-equiv="refresh" content="0; url=index.php" />
