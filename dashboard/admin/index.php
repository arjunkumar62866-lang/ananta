<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">
    <style>
body.ananta-admin-dashboard {
  background-color: #f4f6f8 !important;
  color: #334155 !important;
  font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}
.content-wrapper {
  background-color: #f4f6f8 !important;
  padding-top: 85px !important;
}
.topbar-nav .navbar {
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(20px) !important;
  -webkit-backdrop-filter: blur(20px) !important;
  border-bottom: 1px solid #e2e8f0 !important;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
}
.topbar-nav .nav-link {
  color: #0f172a !important;
}
.wallet-box {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    height: 96px;               
    color: #0f172a !important;
    margin-top: 15px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;
}

.wallet-box:hover {
    transform: translateY(-3px);
    border-color: #0284c7 !important;
    box-shadow: 0 15px 35px rgba(2, 132, 199, 0.12);
}

.wallet-icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%);
    border: 1px solid rgba(2, 132, 199, 0.2);
    border-radius: 16px;
}

.wallet-title {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
}

.wallet-amount {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
</style>
<?php 
include 'common/header.php'; 


// withdraw ammount
$table="tbl_transaction";
$withdrawaltotal = incometotalnew($pdo, $table,'Withdrawal Request');
$withdrawaltotal= round((float)($withdrawaltotal ?? 0), 2);

// direct income
$table="tbl_levelinc";
$directincome = incometotalnew($pdo, $table,'Direct Income');
$directincome= round((float)($directincome ?? 0), 2);

// generation income
$table="tbl_transaction";
$generation_income = incometotalnew($pdo, $table,'Generation Income');
$generation_income= round((float)($generation_income ?? 0), 2);

// Ranking Income
$table="tbl_transaction";
$ranking_income = incometotalnew($pdo, $table,'Ranking Income Payout');
$ranking_income= round((float)($ranking_income ?? 0), 2);

// Profit Sharing Income
$table="tbl_daily_levelinc";
$profit_sharing_income = incometotalnew($pdo, $table,'Profit Sharing Income');
$profit_sharing_income= round((float)($profit_sharing_income ?? 0), 2);

// Reward Income
$table="tbl_transaction";
$reward_income = incometotalnew($pdo, $table,'Reward Income');
$reward_income= round((float)($reward_income ?? 0), 2);

// Leadership Income
$table="tbl_transaction";
$leadership_income_income = incometotalnew($pdo, $table,'Leadership Income');
$leadership_income_income= round((float)($leadership_income_income ?? 0), 2);

// Direct Bonus
$stmt = $pdo->prepare("SELECT SUM(package) AS total_package FROM tbl_roi_two");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$direct_bonus = $result['total_package'] ?? 0;
$direct_bonus= round((float)($direct_bonus ?? 0), 2);

// Daily Profit Sharing Incomes
$table="tbl_roiinc";
$roiincome = incometotalnew($pdo,$table,'Daily Profit Sharing Income');
$roiincome= round((float)($roiincome ?? 0), 2);

// Today Daily Profit Sharing Income
$table="tbl_roiinc";
$dailyroiincome = incometotalnewdate($date,$table,'Daily Profit Sharing Income');

//Level Income
$table="tbl_daily_levelinc";
$dailylevelincome = incometotalnew($pdo,$table,'Daily Level Income');
$dailylevelincome= round((float)($dailylevelincome ?? 0), 2);

//Reward income
$table="tbl_rewardinc";
$rewardincome = incometotalnew($pdo,$table,'Reward Income');
$rewardincome= round((float)($rewardincome ?? 0), 2);

// Active & Inactive Users
$stmt = $pdo->query("SELECT COUNT(*) AS active FROM user WHERE status='1' AND active='1'");
$total_active = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

$stmt = $pdo->query("SELECT COUNT(*) AS active FROM user WHERE status='1' AND active='0'");
$total_pending = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
?>

<body class="ananta-admin-dashboard">

  <!-- Start wrapper-->
  <div id="wrapper">

    <!--Start sidebar-wrapper-->
    <!--End sidebar-wrapper-->

    <!--Start topbar header-->

    <!--End topbar header-->

    <div class="clearfix"></div>

    <div class="content-wrapper">
      
      <!-- Premium Glass Admin Welcome Banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-0" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.12) 0%, rgba(22, 163, 74, 0.12) 100%), #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border: 1px solid rgba(2, 132, 199, 0.2) !important;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="welcome-avatar-glow" style="width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);">
                  <i class="fa fa-user-shield"></i>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">ADMIN PANEL</span>
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">System Overview</span>
                  </div>
                  <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Welcome to Ananta Admin Console! 🛡️
                  </h4>
                  <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                    Monitor platform analytics, manage user activities, and oversee financial payouts seamlessly.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-3" style="background-color: transparent; border: none; box-shadow: none;">
          <div class="card-content">
            <div class="row row-group m-0">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(2, 132, 199, 0.1); border-color: rgba(2, 132, 199, 0.25);">
                        <i class="fa fa-users" style="font-size:22px; color:#0284c7;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Active Users</h6>
                        <h5 class="wallet-amount mb-1"><?php echo $total_active;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.25);">
                        <i class="fa fa-user-times" style="font-size:22px; color:#ef4444;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Inactive User</h6>
                        <h5 class="wallet-amount mb-1"><?php echo $total_pending;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(22, 163, 74, 0.1); border-color: rgba(22, 163, 74, 0.25);">
                        <i class="fa fa-briefcase" style="font-size:22px; color:#16a34a;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Total Business</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".getalluserpackage($pdo);?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.25);">
                        <i class="fa fa-sitemap" style="font-size:22px; color:#8b5cf6;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Generation Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$generation_income;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(6, 182, 212, 0.1); border-color: rgba(6, 182, 212, 0.25);">
                        <i class="fa fa-user-plus" style="font-size:22px; color:#06b6d4;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Direct Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$directincome;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.25);">
                        <i class="fa fa-gift" style="font-size:22px; color:#f59e0b;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Direct Bonus 10M</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$direct_bonus;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(236, 72, 153, 0.1); border-color: rgba(236, 72, 153, 0.25);">
                        <i class="fa fa-pie-chart" style="font-size:22px; color:#ec4899;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Profit Sharing Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$profit_sharing_income;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.25);">
                        <i class="fa fa-trophy" style="font-size:22px; color:#eab308;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Ranking Income</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$ranking_income;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.25);">
                        <i class="fa fa-star" style="font-size:22px; color:#3b82f6;"></i>
                    </div>
                    <div class="ml-3">
                        <h6 class="wallet-title mb-1">Leadership Bonus</h6>
                        <h5 class="wallet-amount mb-1"><?php echo "$hmcurrency ".$leadership_income_income;?></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="wallet-box d-flex align-items-center px-3 py-3">
                    <div class="wallet-icon d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.25);">
                        <i class="fa fa-shield" style="font-size:22px; color:#10b981;"></i>
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