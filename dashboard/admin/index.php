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


// error_reporting(E_ALL);
// ini_set('display_errors', 1);



// withdraw ammount
$table="tbl_transaction";
$withdrawaltotal = incometotalnew($pdo, $table,'Withdrawal Request');
$withdrawaltotal= round($withdrawaltotal, 2);

// direct income
$table="tbl_levelinc";
$directincome = incometotalnew($pdo, $table,'Direct Income');
$directincome= round($directincome, 2);

// generation income
$table="tbl_transaction";
$generation_income = incometotalnew($pdo, $table,'Generation Income');
$generation_income= round($generation_income, 2);

// Direct Bonus
$table="tbl_transaction";
$direct_bonus = incometotalnew($pdo, $table,'Direct Bonus');
$direct_bonus= round($direct_bonus, 2);

// Ranking Income
$table="tbl_transaction";
$ranking_income = incometotalnew($pdo, $table,'Ranking Income');
$ranking_income= round($ranking_income, 2);

// Reward Income
$table="tbl_transaction";
$reward_income = incometotalnew($pdo, $table,'Reward Income');
$reward_income= round($reward_income, 2);

// Leadership Income
$table="tbl_transaction";
$leadership_income_income = incometotalnew($pdo, $table,'Leadership Income');
$leadership_income_income= round($leadership_income_income, 2);

// Daily Profit Sharing Incomes
$table="tbl_roiinc";
$roiincome = incometotalnew($pdo,$table,'Daily Profit Sharing Income');
$roiincome= round($roiincome, 2);

// Today Daily Profit Sharing Income
$table="tbl_roiinc";
$dailyroiincome = incometotalnewdate($date,$table,$userid,'Daily Profit Sharing Income');

//Level Income
$table="tbl_daily_levelinc";
$profit_sharing_income = incometotalnew($pdo,$table,'Profit Sharing Income');
$profit_sharing_income= round($profit_sharing_income, 2);

//Reward income

$table="tbl_rewardinc";
$rewardincome = incometotalnew($pdo,$table,'Reward Income');
$rewardincome= round($rewardincome, 2);

// direct business

//TotalBusiness

// $totallevelbusiness=gettotallevelbusiness($userid);
// $totallevelbusiness1=$totallevelbusiness;

// =========================
// Active Users
// =========================
$stmt = $pdo->query("SELECT COUNT(*) AS active FROM user WHERE status='1' AND active='1'");
$total_active = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

// =========================
// Inactive Users
// =========================
$stmt = $pdo->query("SELECT COUNT(*) AS active FROM user WHERE status='1' AND active='0'");
$total_pending = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

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
                 
      

        <!--Start Dashboard Content-->

        <div class="card mt-3">
          <div class="card-content">
            <div class="row row-group m-0">
                <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/active-user.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Active Users</h6>
                        <h5 class="wallet-amount mb-1"><?php echo $total_active;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/inactive-user.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Inactive User</h6>
                        <h5 class="wallet-amount mb-1"><?php echo $total_pending;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/withdrawal.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Total Business</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".getalluserpackage($pdo);?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/withdrawal.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Generation Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$generation_income;?></h5>
                    </div>

                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center">
                        <img src="images/withdrawal.png" width="30px" height="30px">
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
                        <img src="images/withdrawal.png" width="30px" height="30px">
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
                        <img src="images/withdrawal.png" width="30px" height="30px">
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
                        <img src="images/withdrawal.png" width="30px" height="30px">
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
                        <img src="images/withdrawal.png" width="30px" height="30px">
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
                        <img src="images/withdrawal.png" width="30px" height="30px">
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Rank and Rewards</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$reward_income;?></h5>
                    </div>

                </div>
            </div>
            
            
            
            
            
              
      <!--        <div class="col-12 col-lg-6 col-xl-3 border-light">-->
      <!--          <div class="card-body">-->
      <!--            <h5 class="text-white mb-0"><php echo "$hmcurrency ".$usertotal_package;?> <span class="float-right"><i class="fa fa-envira"></i></span></h5>-->
      <!--            <div class="progress my-3" style="height:3px;">-->
      <!--              <div class="progress-bar" style="width:55%"></div>-->
      <!--            </div>-->
      <!--            <p class="mb-0 text-white small-font">My Business <span class="float-right">+2.2% <i-->
      <!--                  class="zmdi zmdi-long-arrow-up"></i></span></p>-->
      <!--          </div>-->
      <!--        </div>-->
              
      <!--        <div class="col-12 col-lg-6 col-xl-3 border-light">-->
      <!--          <div class="card-body">-->
      <!--            <h5 class="text-white mb-0"><php-->
        <!--$directbusinesstotal = getlevelDirectbusiness($userid, 1); -->
        <!--echo "$hmcurrency ".$directbusinesstotal; -->
      <!--?> <span class="float-right"><i class="fa fa-envira"></i></span></h5>-->
      <!--            <div class="progress my-3" style="height:3px;">-->
      <!--              <div class="progress-bar" style="width:55%"></div>-->
      <!--            </div>-->
      <!--            <p class="mb-0 text-white small-font">Direct Business <span class="float-right">+2.2% <i-->
      <!--                  class="zmdi zmdi-long-arrow-up"></i></span></p>-->
      <!--          </div>-->
      <!--        </div>-->
              
      <!--        <div class="col-12 col-lg-6 col-xl-3 border-light">-->
      <!--          <div class="card-body">-->
      <!--            <h5 class="text-white mb-0"><php echo "$hmcurrency ".$totallevelbusiness1;?> <span class="float-right"><i class="fa fa-envira"></i></span></h5>-->
      <!--            <div class="progress my-3" style="height:3px;">-->
      <!--              <div class="progress-bar" style="width:55%"></div>-->
      <!--            </div>-->
      <!--            <p class="mb-0 text-white small-font">Team Business<span class="float-right">+2.2% <i-->
      <!--                  class="zmdi zmdi-long-arrow-up"></i></span></p>-->
      <!--          </div>-->
      <!--        </div>-->
              
      <!--        <div class="col-12 col-lg-6 col-xl-3 border-light">-->
      <!--          <div class="card-body">-->
      <!--            <h5 class="text-white mb-0"><php -->
        <!--$totalallbusiness = (int)$totallevelbusiness1 + (int)$directbusinesstotal; -->
        <!--echo "$hmcurrency ".$totalallbusiness; -->
      <!--?> <span class="float-right"><i class="fa fa-envira"></i></span></h5>-->
      <!--            <div class="progress my-3" style="height:3px;">-->
      <!--              <div class="progress-bar" style="width:55%"></div>-->
      <!--            </div>-->
      <!--            <p class="mb-0 text-white small-font">Total Business <span class="float-right">+2.2% <i-->
      <!--                  class="zmdi zmdi-long-arrow-up"></i></span></p>-->
      <!--          </div>-->
      <!--        </div>-->

            </div>
          </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card mb-3 text-white colorwhite p-3">
        
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="manual-roi-one-pay.php" class="btn btn-primary mr-2 mb-2">Pay Generation Income</a>
                            <a href="roi-two-pay.php" class="btn btn-success mr-2 mb-2">Pay Direct Bonus</a>
                            <a href="roi-three-pay.php" class="btn btn-primary mr-2 mb-2">Pay Ranking Income</a>
                            <a href="royalty-user-pay.php" class="btn btn-success mr-2 mb-2">Pay Leadership Bonus</a>
                        </div>
        
                    </div>
                </div>
            </div>
        </div>



        
<!--<div class="row">-->
<!--          <div class="col-12 col-lg-12">-->
<!--            <div class="card">-->
<!--              <div class="card mb-3 text-white colorwhite">-->
<!--            <div class="card-body" style=" padding: 25px; margin-top: 20px; color: white; box-shadow: 0 2px 15px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1);">-->
                <!-- Title -->
<!--                <div class="title" style="margin-bottom: 20px;">-->
<!--                    <h5 style="font-size: 13px; color: #a0a8b9; margin: 0; letter-spacing: 1px;">Referral Link</h5>-->
<!--                </div>-->

                <!-- Referral Input and Copy Button -->
<!--                <div class="input-group mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">-->
<!--                    <input -->
<!--                        type="text" -->
<!--                        class="form-control" -->
<!--                        style=" border: 1px solid rgba(255,255,255,0.1); color: #000; border-radius: 10px; padding: 12px; flex: 1; font-size: 14px;" -->
<!--                        title="Copy to Clipboard" -->
<!--                        value="<php echo $hmurl; ?>user/register?uid=<php echo $hmpre .-->
                        <!--$userid; >" -->
<!--                        readonly-->
<!--                    >-->

<!--                    <a-->
<!--                        class="copy_text" -->
<!--                        data-toggle="tooltip" -->
<!--                        title="Copy to Clipboard" -->
<!--                        href="<php echo $hmurl; ?>user/register?uid=<php echo $hmpre .-->
                        <!--$userid; >"-->
<!--                    >-->
<!--                        <button -->
<!--                            type="button" -->
<!--                            class="btn" -->
<!--                            style=" border: 1px solid rgba(255,255,255,0.1); color: white; padding: 12px 18px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer;"-->
<!--                        >-->
<!--                            Copy-->
<!--                        </button>-->
<!--                    </a>-->
<!--                </div>-->

                <!-- WhatsApp Button -->
<!--                <div class="input-group mt-3" style="display: flex;">-->
<!--                    <a -->
<!--                        href="https://wa.me/?text=https://metafxworld.com/dashboard/user/register?uid=<php echo $hmpre .-->
<!--                        $userid; ?>" -->
<!--                        target="_blank"-->
<!--                        class="form-control btn btn-primary"-->
<!--                        style="background-color:#25D366; color: white; font-weight: 600; padding: 12px 18px; border-radius: 10px; text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px;"-->
<!--                    >-->
<!--                        WhatsApp <i class="fa fa-whatsapp" style="font-size: 18px; margin-left: 8px;"></i>-->
<!--                    </a>-->
<!--                </div>-->

                <!-- Telegram Button (if active) -->
<!--                <php if ($idactive == 1) { ?>-->
<!--                    <div class="input-group mt-3" style="display: flex;">-->
<!--                        <a -->
<!--                            href="https://t.me/share/url?url=https://metafxworld.com/dashboard/user/register?uid=<php echo $hmpre .-->
<!--                            $userid; ?>&text=Join%20MetaFX%20World%20Now!" -->
<!--                            target="_blank"-->
<!--                            class="form-control btn btn-primary"-->
<!--                            style="background-color:#0088cc; color: white; font-weight: 600; padding: 12px 18px; border-radius: 10px; text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px;"-->
<!--                        >-->
<!--                            Join Telegram <i class="fa fa-telegram" style="font-size: 18px; margin-left: 8px;"></i>-->
<!--                        </a>-->
<!--                    </div>-->
<!--                <php } ?>-->
<!--            </div>-->
              
<!--            </div>-->
<!--          </div>-->
        <!--</div><!--End Row-->

        <!--End Dashboard Content-->



        <!--start overlay-->
<!--        <div class="overlay toggle-menu"></div>-->
        <!--end overlay-->

<!--      </div>-->
      <!-- End container-fluid-->

    </div><!--End content-wrapper-->
    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

    <?php include 'common/footer.php'?>

  </div>
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 05:58:57 GMT -->

</html>