<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">
    <style>
.wallet-box {
    background: #083A3A;        /* same dark green */
    border-radius: 20px 0 20px 0; /* right-side curve exactly like image */
    height: 90px;               
    color: white;
    margin-top:20px;
}

.wallet-icon {
    width: 55px;
    height: 55px;
    background: #ffffff;
    border-radius: 50%;
}

.wallet-title {
    font-size: 14px;
    opacity: 0.9;
}

.wallet-amount {
    font-size: 16px;
    font-weight: 600;
}

.wallet-view {
    font-size: 12px;
    color: #d8d8d8;
    text-decoration: none;
}


</style>
<?php 
include 'common/header.php'; 

// get level Business
$directbusinesstotal=gettotallevelbusiness($userid);

$my_right_active_directs = getmydirectidright($userid,"right");
$my_left_active_directs = getmydirectidleft($userid,"right");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Reward achiever check 
rank_reward($userid);

// withdraw ammount
$table="tbl_transaction";
$withdrawaltotal = incometotalnew($pdo, $table,$userid,'Withdrawal Request');
$withdrawaltotal= round($withdrawaltotal, 2);

// generation income
$table="tbl_transaction";
$generation_income = incometotalnew($pdo, $table,$userid,'Generation Income Payout');
$generation_income= round($generation_income, 2);

// Direct Bonus

$stmt = $pdo->prepare("SELECT SUM(package) AS total_package FROM tbl_roi_two WHERE user_id = :userid");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$direct_bonus = $result['total_package'] ?? 0;
$direct_bonus= round($direct_bonus, 2);


// Ranking Income
$table="tbl_transaction";
$ranking_income = incometotalnew($pdo, $table,$userid,'Ranking Income Payout');
$ranking_income= round($ranking_income, 2);

// Profit Sharing Income
$table="tbl_daily_levelinc";
$profit_sharing_income = incometotalnew($pdo, $table,$userid,'Profit Sharing Income');
$profit_sharing_income= round($profit_sharing_income, 2);

// Reward Income
$table="tbl_transaction";
$reward_income = incometotalnew($pdo, $table,$userid,'Reward Income');
$reward_income= round($reward_income, 2);

// Leadership Income
$table="tbl_transaction";
$leadership_income_income = incometotalnew($pdo, $table,$userid,'Leadership Income');
$leadership_income_income= round($leadership_income_income, 2);

// direct income
$table="tbl_levelinc";
$directincome = incometotalnew($pdo, $table,$userid,'Direct Income');
$directincome= round($directincome, 2);

// Daily Profit Sharing Incomes
$table="tbl_roiinc";
$roiincome = incometotalnew($pdo,$table,$userid,'Daily Profit Sharing Income');
$roiincome= round($roiincome, 2);

// Today Daily Profit Sharing Income
$table="tbl_roiinc";
$dailyroiincome = incometotalnewdate($date,$table,$userid,'Daily Profit Sharing Income');

//Level Income
$table="tbl_daily_levelinc";
$dailylevelincome = incometotalnew($pdo,$table,$userid,'Daily Level Income');
$dailylevelincome= round($dailylevelincome, 2);

//Reward income

$table="tbl_rewardinc";
$rewardincome = incometotalnew($pdo,$table,$userid,'Reward Income');
$rewardincome= round($rewardincome, 2);



//TotalBusiness

$totallevelbusiness=gettotallevelbusiness($userid);
$totallevelbusiness1=$totallevelbusiness;

$roidata=getroionedatanew($userid);

$roipackage=$roidata['package'];

// >=$useramount

$totalbusiee=(int)3*$roipackage;



// // Direct Business
// $directbusinesstotal = getlevelDirectbusiness($userid, 1);




// ✅ Fetch tree data
$stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid");
$stmt->execute([':userid' => $userid]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$left_team = $row['leftcount'];
$right_team = $row['rightcount'];

$left_total_team = $row['lefttotal'];
$right_total_team = $row['righttotal'];
    

// get my direct actives
$active_directs=getmydirectactive($userid);

// get $inactive_team
$inactive_team = ($left_total_team + $right_total_team)-($left_team + $right_team)

// if ($row) {
//     $left_team = $row['leftcount'];
//     $right_team = $row['rightcount'];
    

//     $total_pv = $left_pv + $right_pv;

//     // ✅ Determine smaller team
//     $my_team = ($left_pv < $right_pv) ? $left_pv : $right_pv;
    

//     // ✅ Calculate single pair income difference
//     if ($left_pv == $right_pv) {
//         $singlepairincome = 0;
//     } elseif ($left_pv > $right_pv) {
//         $singlepairincome = $left_pv - $right_pv;
//     } else {
//         $singlepairincome = $right_pv - $left_pv;
//     }

//     // ✅ Fetch previous pair and team earnings
//     $stmt2 = $pdo->prepare("
//         SELECT 
//             SUM(total_pairs) AS pre_pair,
//             SUM(left_team) AS myleftteam,
//             SUM(right_team) AS myrightteam,
//             SUM(weekly_earning) AS weekly_earning
//         FROM tbl_temp_data
//         WHERE user_id = :userid
//     ");
//     $stmt2->execute([':userid' => $userid]);
//     $r1 = $stmt2->fetch(PDO::FETCH_ASSOC);

//     $pre_pair = $r1['pre_pair'] ?? 0;
//     $myleftteampaid = $r1['myleftteam'] ?? 0;
//     $rightteampaid = $r1['myrightteam'] ?? 0;
//     $totalincomeeared = $r1['weekly_earning'] ?? 0;
   
//     // ✅ Calculate team weekly details
//     $myteamweekly = $my_team - $pre_pair;

//     $updatedleftteam = $left_pv - $myleftteampaid;
//     $updatedrightteam = $right_pv - $rightteampaid;

//     $todaydate = date('Y-m-d');
//     $closingdate = date('Y-m-d');
//     $timestamp = strtotime($closingdate);
//     $new_date = date("Y-m-d", $timestamp);
//     $ClosingDate = date('Y-m-d'); // same-day closing 

//     // ✅ Check if user is active and date matches
//     if ($idactive == '1' && $todaydate == $ClosingDate) {

//         // ✅ (Optional checkweeklyentry can be added back)
//         if (1 == '1') {
//             // Example earning logic
//             $weeklyclosingamountfinal = $myteamweekly * $hmmatching_amount;
//             // echo"wwwwwwwwwwwwwwwwwwwwwwwwwww ".$weeklyclosingamountfinal;
//             $weeklyclosingamountfinal1 = $weeklyclosingamountfinal;

//             // if ($weeklyclosingamountfinal1 > $capping) {
//             //     $weeklyclosingamountfinal1 = $capping;
//             // }
//             for($i=0;$i<50000;$i++){

//             // if ($weeklyclosingamountfinal > 0) {
//             if (1==1) {
//                 $stmt3 = $pdo->prepare("
//                     INSERT INTO tbl_temp_data 
//                         (user_id, total_pairs, flushedpair, left_team, right_team, paid_date, date, closingdate, status, weekly_earning)
//                     VALUES 
//                         (:userid, :total_pairs, :flushedpair, :left_team, :right_team, '', :date, :closingdate, 0, :weekly_earning)
//                 ");

//                 $stmt3->execute([
//                     ':userid'         => $userid,
//                     ':total_pairs'    => $myteamweekly,
//                     ':flushedpair'    => $flusedpair ?? 0,
//                     ':left_team'      => $updatedleftteam,
//                     ':right_team'     => $updatedrightteam,
//                     ':date'           => $todaydate,
//                     ':closingdate'    => $ClosingDate,
//                     ':weekly_earning' => $weeklyclosingamountfinal1
//                 ]);
//             }
//             }
//         }
//     }
// }
/* End Binary Method */






?>
<body class="bg-theme bg-theme1">

  <!-- Start wrapper-->
  <div id="wrapper">

    <!--Start sidebar-wrapper-->
    <!--End sidebar-wrapper-->

    <!--Start topbar header-->

    <!--End topbar header-->

    <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12" >
                    <div class="bs-component">
                        <?php if ($idactive != 1) { ?>
                            <div class="card mb-3 text-white bg-danger">
                                <div class="card-body">
                                    <blockquote class="card-blockquote">
                                        <strong style="color:white;">Welcome in <?php echo $hmtitle; ?>!</strong> 
                                        <a href="activatefund.php" style="color:Yellow;">Hello <?php echo $username; ?>, Your ID Activation is Pending Click here to Activate</a>
                                    </blockquote>
                                </div>
                            </div>
                            <?php if ($useramount >= $totalbusiee) {
                                // $stmt = $pdo->prepare("UPDATE tbl_roi_one SET status = :status WHERE user_id = :userid");
                                // $stmt->execute([
                                //     ':status' => 1,
                                //     ':userid' => $userid
                                //     ]);
                                    echo "<script>alert('You need to re topup your id to get the more profits');</script>";
                                } ?>
                            <?php if ($difftime >= 72) {
                                // $stmt = $pdo->prepare("update user set status= :status  where userid = :userid");
                                // $stmt->execute([
                                //     ':status' => 2,
                                //     ':userid' => $userid
                                //     ]);
                                    echo "<script>alert('Your Activation was Over. Plz Contact Admin');window.location.assign('login');</script>";
                            } ?>
                        <?php } else { ?>
                            <div class="card mb-3 text-white colorwhite">
                                <div class="card-body">
                                    <blockquote class="card-blockquote">
                                        <strong style="color:white;">Welcome in <?php echo $hmtitle; ?>!</strong> 
                                        <a style="color:yellow;">Hello <?php echo $username; ?>, You Are Active </a>
                                    </blockquote>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="col-lg-12" >
                    <div  class="bs-component">
                        <div class="card mb-3 text-white">
                            <div class="card-body">
                          <blockquote class="card-blockquote">
                              <strong > News:</strong>
                              <marquee style="color:white;"  direction="left">
                                  <?php echo $news; ?>
                              </marquee>
                          </blockquote>
                      </div>
                  </div>
              </div>
          </div>
  </div>

        <!--Start Dashboard Content-->  

        <div class="card mt-3">
          <div class="card-content">
            <div class="row row-group m-0">
              
              
             <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/wallet.gif" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Wallet Balance</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$useramount;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/withdrawal.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Total Withdrawal</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$withdrawaltotal;?></h5>
                    </div>

                </div>
            </div>
            
           

              
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/fund-wallet.gif" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Fund Wallet</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$pin_wallet; ?></h5>
                    </div>

                </div>
            </div>

              <!--<div class="col-12 col-lg-6 col-xl-3 border-light">-->
              <!--  <div class="card-body">-->
              <!--    <h5 class="text-white mb-0"><php echo "$hmcurrency ".$roiincome; ?> <span class="float-right"><i class="fa fa-line-chart"></i></span></h5>-->
              <!--    <div class="progress my-3" style="height:3px;">-->
              <!--      <div class="progress-bar" style="width:55%"></div>-->
              <!--    </div>-->
              <!--    <p class="mb-0 text-white small-font">Daily Profit Sharing Incomes <span class="float-right">+2.2% <i-->
              <!--          class="zmdi zmdi-long-arrow-up"></i></span></p>-->
              <!--  </div>-->
              <!--</div>-->

              <!--<div class="col-12 col-lg-6 col-xl-3 border-light">-->
              <!--  <div class="card-body">-->
              <!--    <h5 class="text-white mb-0"><php echo "$hmcurrency ".$dailyroiincome; ?> <span class="float-right"><i class="fa fa-clock-o"></i></span></h5>-->
              <!--    <div class="progress my-3" style="height:3px;">-->
              <!--      <div class="progress-bar" style="width:55%"></div>-->
              <!--    </div>-->
              <!--    <p class="mb-0 text-white small-font">Today Daily Profit Sharing Income <span class="float-right">+2.2% <i-->
              <!--          class="zmdi zmdi-long-arrow-up"></i></span></p>-->
              <!--  </div>-->
              <!--</div>-->
              
              <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/income.png" width="30">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Generation Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$generation_income; ?></h5>
                    </div>

                </div>
            </div>
            
             <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/income.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Direct Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$directincome;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/direct-bonus.gif" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Direct Bonus 10M</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$direct_bonus;?></h5>
                    </div>

                </div>
            </div>
            
            
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/income.png" width="30">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Profit Sharing Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$profit_sharing_income;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/income.png" width="30">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Ranking Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$ranking_income;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/leadership.gif" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Leadership Bonus</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$leadership_income_income;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/ranking.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">My Rank</h6>
                        <h5 class="wallet-amount mb-1"><?php echo $rank;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/reward-income.gif" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Rank and Rewards</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$reward_income;?></h5>
                    </div>

                </div>
            </div>
            
            
            
            <!--<div class="col-12 col-md-6 col-lg-3">-->
            <!--    <div class="wallet-box d-flex align-items-center px-3 py-3">-->
            <!--        <div class="wallet-icon d-flex align-items-center justify-content-center">-->
            <!--            <img src="images/investment.gif" width="30px" height="30px">-->
            <!--        </div>-->
            <!--        <div class="ml-3">-->
            <!--            <h6 class="wallet-title mb-1">My Business</h6>-->
            <!--            <h5 class="wallet-amount mb-1"><php echo "$hmcurrency ".$usertotal_package;?></h5>-->
            <!--        </div>-->

            <!--    </div>-->
            <!--</div>-->
            
            
            <!--<div class="col-12 col-md-6 col-lg-3">-->
            <!--    <div class="wallet-box d-flex align-items-center px-3 py-3">-->
            <!--        <div class="wallet-icon d-flex align-items-center justify-content-center">-->
            <!--            <img src="images/investment.gif" width="30px" height="30px">-->
            <!--        </div>-->
            <!--        <div class="ml-3">-->
            <!--            <h6 class="wallet-title mb-1">Direct Business</h6>-->
            <!--            <h5 class="wallet-amount mb-1"><php-->
         
                    <!--echo "$hmcurrency ".$directbusinesstotal; -->
            <!--        ?></h5>-->
            <!--        </div>-->

            <!--    </div>-->
            <!--</div>-->
              
              
              <!--<div class="col-12 col-lg-6 col-xl-3 border-light">-->
              <!--  <div class="card-body">-->
              <!--    <h5 class="text-white mb-0"><php echo "$hmcurrency ".$totallevelbusiness1;?> <span class="float-right"><i class="fa fa-group"></i></span></h5>-->
              <!--    <div class="progress my-3" style="height:3px;">-->
              <!--      <div class="progress-bar" style="width:55%"></div>-->
              <!--    </div>-->
              <!--    <p class="mb-0 text-white small-font">Team Business<span class="float-right">+2.2% <i-->
              <!--          class="zmdi zmdi-long-arrow-up"></i></span></p>-->
              <!--  </div>-->
              <!--</div>-->
              
              <!--<div class="col-12 col-lg-6 col-xl-3 border-light">-->
              <!--  <div class="card-body">-->
              <!--    <h5 class="text-white mb-0"><php -->
                    <!--$totalallbusiness = (int)$totallevelbusiness1 + (int)$directbusinesstotal; -->
                    <!--echo "$hmcurrency ".$totalallbusiness; -->
              <!--      ?> <span class="float-right"><i class="fa fa-bar-chart"></i></span></h5>-->
              <!--    <div class="progress my-3" style="height:3px;">-->
              <!--      <div class="progress-bar" style="width:55%"></div>-->
              <!--    </div>-->
              <!--    <p class="mb-0 text-white small-font">Total business <span class="float-right">+2.2% <i-->
              <!--          class="zmdi zmdi-long-arrow-up"></i></span></p>-->
              <!--  </div>-->
              <!--</div>-->
              
              
              
            
            <!--<div class="col-12 col-md-6 col-lg-3">-->
            <!--    <div class="wallet-box d-flex align-items-center px-3 py-3">-->
            <!--        <div class="wallet-icon d-flex align-items-center justify-content-center">-->
            <!--            <img src="images/left-user.gif" width="30px" height="30px">-->
            <!--        </div>-->
            <!--        <div class="ml-3">-->
            <!--            <h6 class="wallet-title mb-1">Left Active Directs</h6>-->
            <!--            <h5 class="wallet-amount mb-1"><php echo $left_team;?></h5>-->
            <!--        </div>-->

            <!--    </div>-->
            <!--</div>-->
            
            <!--<div class="col-12 col-md-6 col-lg-3">-->
            <!--    <div class="wallet-box d-flex align-items-center px-3 py-3">-->
            <!--        <div class="wallet-icon d-flex align-items-center justify-content-center">-->
            <!--            <img src="images/left-user.gif" width="30px" height="30px">-->
            <!--        </div>-->
            <!--        <div class="ml-3">-->
            <!--            <h6 class="wallet-title mb-1">Right Active Directs</h6>-->
            <!--            <h5 class="wallet-amount mb-1"><php echo $right_team;?></h5>-->
            <!--        </div>-->

            <!--    </div>-->
            <!--</div>-->
              
              

            </div>
          </div>
        </div>

        
    <div class="row">
          <div class="col-12 col-lg-12">
            <div class="card">
              <div class="card mb-3 text-white colorwhite">
            <div class="card-body" style=" padding: 25px; margin-top: 20px; color: white; box-shadow: 0 2px 15px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1);">
                <!-- Title -->
                <div class="title" style="margin-bottom: 20px;">
                    <h5 style="font-size: 13px; color: #a0a8b9; margin: 0; letter-spacing: 1px;">Referral Link Left</h5>
                </div>

                <!-- Referral Input and Copy Button -->
                <div class="input-group mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input 
                        type="text" 
                        class="form-control" 
                        style=" border: 1px solid rgba(255,255,255,0.1); color: #000; border-radius: 10px; padding: 12px; flex: 1; font-size: 14px;" 
                        title="Copy to Clipboard" 
                        value="<?php echo $hmurl; ?>user/add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $userid; ?>&type=left" 
                        readonly
                    >

                    <a
                        class="copy_text" 
                        data-toggle="tooltip" 
                        title="Copy to Clipboard" 
                        href="<?php echo $hmurl; ?>user/add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $userid; ?>&type=left"
                    >
                        <button 
                            type="button" 
                            class="btn" 
                            style=" border: 1px solid rgba(255,255,255,0.1); color: white; padding: 12px 18px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer;"
                        >
                            Copy
                        </button>
                    </a>
                </div>
                
                
                <!-- Title -->
                <div class="title" style="margin-bottom: 20px;">
                    <h5 style="font-size: 13px; color: #a0a8b9; margin: 0; letter-spacing: 1px;">Referral Link Right</h5>
                </div>

                <!-- Referral Input and Copy Button -->
                <div class="input-group mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input 
                        type="text" 
                        class="form-control" 
                        style=" border: 1px solid rgba(255,255,255,0.1); color: #000; border-radius: 10px; padding: 12px; flex: 1; font-size: 14px;" 
                        title="Copy to Clipboard" 
                        value="<?php echo $hmurl; ?>user/add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $userid; ?>&type=right" 
                        readonly
                    >

                    <a
                        class="copy_text" 
                        data-toggle="tooltip" 
                        title="Copy to Clipboard" 
                        href="<?php echo $hmurl; ?>user/add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $userid; ?>&type=right"
                    >
                        <button 
                            type="button" 
                            class="btn" 
                            style=" border: 1px solid rgba(255,255,255,0.1); color: white; padding: 12px 18px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer;"
                        >
                            Copy
                        </button>
                    </a>
                </div>

                <!-- WhatsApp Button -->
                <div class="input-group mt-3" style="display: flex;">
                    <a 
                        href="https://wa.me/?text=https://anantamtptl.com/dashboard/user/new_binary_registration_form.php?uid=<?php echo $hmpre .
                        $userid; ?>" 
                        target="_blank"
                        class="form-control btn btn-primary"
                        style="background-color:#25D366; color: white; font-weight: 600; padding: 12px 18px; border-radius: 10px; text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px;"
                    >
                        WhatsApp <i class="fa fa-whatsapp" style="font-size: 18px; margin-left: 8px;"></i>
                    </a>
                </div>

                <!-- Telegram Button (if active) -->
                <?php if ($idactive == 1) { ?>
                    <div class="input-group mt-3" style="display: flex;">
                        <a 
                            href="https://t.me/share/url?url=https://anantamtptl.com/dashboard/user/new_binary_registration_form.php?uid=<?php echo $hmpre .
                            $userid; ?>&text=Join%20Ananta%20World%20Now!" 
                            target="_blank"
                            class="form-control btn btn-primary"
                            style="background-color:#0088cc; color: white; font-weight: 600; padding: 12px 18px; border-radius: 10px; text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px;"
                        >
                            Join Telegram <i class="fa fa-telegram" style="font-size: 18px; margin-left: 8px;"></i>
                        </a>
                    </div>
                <?php } ?>
            </div>
              
            </div>
          </div>
        </div><!--End Row-->

        <!--End Dashboard Content-->



        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

      </div>
      
    <div class="row">
          <div class="col-12 col-lg-12">
            <div class="card">
              <div class="card mb-3 text-white colorwhite">
            <div class="card-body" style=" padding: 25px; margin-top: 20px; color: white; box-shadow: 0 2px 15px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1);">
                <!-- Title -->
                <div class="title" style="margin-bottom: 20px;">
                    <strong style="font-size: 15px; color: white; margin: 0; letter-spacing: 1px;">Business Details</strong>
                </div>
                <!-- Business Details Values -->
                <div style="margin-top: 25px; font-size: 13px; color: #d1d5db; line-height: 24px;">
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Direct Active Team</span>-->
                    <!--    <span><php echo $active_directs;?></span>-->
                    <!--</div>-->
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Inactive Team</span>
                        <span><?php echo $inactive_team;?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>My Business</span>
                        <span><?php echo "$hmcurrency ".$usertotal_package;?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span>Sponsor Team Business</span>
                        <span><?php echo $hmcurrency.$directbusinesstotal?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Active Team (Left/Right)</span>
                        <span><?php echo $my_left_active_directs . "/" . $my_right_active_directs; ?></span>
                    </div>
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Strong/Weaker Leg Team</span>-->
                    <!--    <span>0/0</span>-->
                    <!--</div>-->
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Team Carry Forward (Left/Right)</span>-->
                    <!--    <span>0/0</span>-->
                    <!--</div>-->
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Current Mining Business (Left/Right)</span>-->
                    <!--    <span>0/0</span>-->
                    <!--</div>-->
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Total Mining Business (Left/Right)</span>-->
                    <!--    <span>0/0</span>-->
                    <!--</div>-->
                
                </div>
                
            </div>
              
            </div>
          </div>
        </div><!--End Row-->

        <!--End Dashboard Content-->



        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

      </div>
      
      <!--// personal info-->
    <div class="row">
          <div class="col-12 col-lg-12">
            <div class="card">
              <div class="card mb-3 text-white colorwhite">
            <div class="card-body" style=" padding: 25px; margin-top: 20px; color: white; box-shadow: 0 2px 15px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1);">
                <!-- Title -->
                <div class="title" style="margin-bottom: 20px;">
                    <strong style="font-size: 15px; color: white; margin: 0; letter-spacing: 1px;">Personal Details</strong>
                </div>
                <!-- Business Details Values -->
                <div style="margin-top: 25px; font-size: 13px; color: #d1d5db; line-height: 24px;">
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>User Id</span>
                        <span><?php echo "$hmpre$userid";?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Name</span>
                        <span><?php echo $username?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Sponsor Id</span>
                        <span><?php echo "$hmpre$usersponser";?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span>Sponsor Name</span>
                        <span><?php echo $usersponsername;?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span>Mobile</span>
                        <span><?php echo $usermobile;?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span>Email</span>
                        <span><?php echo $useremail;?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Designation</span>
                        <span><?php echo $rank; ?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Registration Date</span>
                        <span><?php echo $dateofjoining;?></span>
                    </div>
                
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Investment</span>
                        <span><?php echo $usertotal_package;?></span>
                    </div>
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Current Mining Business (Left/Right)</span>-->
                    <!--    <span>0/0</span>-->
                    <!--</div>-->
                
                    <!--<div style="display: flex; justify-content: space-between;">-->
                    <!--    <span>Total Mining Business (Left/Right)</span>-->
                    <!--    <span>0/0</span>-->
                    <!--</div>-->
                
                </div>
                
            </div>
              
            </div>
          </div>
        </div><!--End Row-->

        <!--End Dashboard Content-->



        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

      </div>
      
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card mb-3 text-white colorwhite">
                    <div class="card-body" style=" padding: 25px; margin-top: 20px; color: white; box-shadow: 0 2px 15px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1);">
                        <!-- Title -->
                        <div class="title" style="margin-bottom: 20px;">
                            <strong style="font-size: 15px; color: white; margin: 0; letter-spacing: 1px;">Sponsor Level Team</strong>
                        </div>
                        <!-- Business Details Values -->
                        <div style="margin-top: 25px; font-size: 13px; color: #d1d5db; line-height: 24px;">
                            <form method="POST" id="levelForm">
                                <label>Select Level:</label>
                                <select name="level" id="levelSelect" style="padding:5px; border-radius:6px; background:#1f2937; color:white;">
                                    <option value="">-- Select Level --</option>
                                        <?php 
                                            for($i=1; $i<=20; $i++){
                                                echo "<option value='$i'>$i</option>";
                                            }
                                        ?>
                                </select>
                            </form>
                            <?php
                                if (isset($_POST['level']) && $_POST['level'] != "") {
                                
                                    $userid = $_SESSION['userid'];  // already stored in session
                                    $selectedLevel = $_POST['level'];
                                
                                    try {
                                        $stmt = $pdo->prepare("
                                            SELECT downline_id 
                                            FROM tbl_userlevel 
                                            WHERE sponser_id = :sid AND level = :lvl
                                        ");
                                
                                        $stmt->execute([
                                            ':sid' => $userid,
                                            ':lvl' => $selectedLevel
                                        ]);
                                
                                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                        echo "<div style='margin-top:20px; color:#fff;'>";
                                        echo "<strong>Downline IDs for Level $selectedLevel:</strong><br><br>";
                                
                                        if (count($results) > 0) {
                                            foreach ($results as $row) {
                                                $id_name =getuserdatabysponserid($row['downline_id'] );
                                                echo "-> " .$hmpre. $row['downline_id'] ." ( ". $id_name['name']." ) ".$id_name['plan']." (".$hmcurrency.$id_name['total_package'] .")"."<br>";
                                            }
                                        } else {
                                            echo "<span style='color:#f87171;'>No downline found for this level.</span>";
                                        }
                                
                                        echo "</div>";
                                
                                    } catch (PDOException $e) {
                                        echo "Database Error: " . $e->getMessage();
                                    }
                                }
                            ?>
                                
                        </div>
                    </div>
                </div>
            </div>
    </div><!--End Row-->

        <!--End Dashboard Content-->



        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

      </div>
      
     
      <!-- End container-fluid-->

    </div><!--End content-wrapper-->
    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

    <?php include 'common/footer.php'?>

  </div>
  
  <script>
  $('.copy_text').click(function (e) {
      e.preventDefault();
      var copyText = $(this).attr('href');
      document.addEventListener('copy', function(e) {
          e.clipboardData.setData('text/plain', copyText);
          e.preventDefault();
      }, true);
      document.execCommand('copy');
      alert('copied text: ' + copyText);
      
  });
  </script>
  <script>
    document.getElementById('levelSelect').addEventListener('change', function() {
        document.getElementById('levelForm').submit();
    });
</script>
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 05:58:57 GMT -->

</html>