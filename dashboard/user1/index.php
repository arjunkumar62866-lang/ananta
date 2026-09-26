<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">
    <style>
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

.wallet-view {
    font-size: 12px;
    color: #0284c7;
    text-decoration: none;
    font-weight: 600;
}

@keyframes pulseGreenGlow {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.8);
        opacity: 0.8;
    }
    70% {
        transform: scale(1.2);
        box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
        opacity: 1;
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        opacity: 0.8;
    }
}

.blinking-dot {
    width: 9px;
    height: 9px;
    background-color: #22c55e;
    border-radius: 50%;
    display: inline-block;
    animation: pulseGreenGlow 1.4s infinite ease-in-out;
}

.inactive-dot {
    width: 9px;
    height: 9px;
    background-color: #93c5fd;
    border-radius: 50%;
    display: inline-block;
    opacity: 0.85;
}

.quick-wallet-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    padding: 12px 10px !important;
    transition: transform 0.2s;
}
.quick-wallet-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 12px !important;
    flex-shrink: 0;
    margin-right: 8px !important;
}
.quick-wallet-icon i {
    font-size: 18px !important;
}
.quick-wallet-title {
    font-size: 11px !important;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.quick-wallet-amount {
    font-size: 15px !important;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
@media (min-width: 576px) {
    .quick-wallet-card {
        padding: 14px 16px !important;
        border-radius: 18px !important;
    }
    .quick-wallet-icon {
        width: 44px !important;
        height: 44px !important;
        margin-right: 12px !important;
    }
    .quick-wallet-icon i {
        font-size: 20px !important;
    }
    .quick-wallet-title {
        font-size: 13px !important;
    }
    .quick-wallet-amount {
        font-size: 18px !important;
    }
}
</style>
<?php 
include 'common/header.php'; 

$isAccountActive = (isset($idactive) && ((string)$idactive === '1' || (int)$idactive === 1)) || (isset($status) && strtolower((string)$status) === 'active');


// get level Business
$directbusinesstotal=gettotallevelbusiness($userid);
$directbusinesstotalleft=gettotallevelbusinessleft($userid);
$directbusinesstotalright=gettotallevelbusinessright($userid);

// $my_right_active_directs = getmydirectidright($userid,"right");
// $my_left_active_directs = getmydirectidleft($userid,"right");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Reward achiever check 
rank_reward($userid);

// withdraw ammount
$table="tbl_transaction";
$withdrawaltotal = incometotalnew($pdo, $table,$userid,'Withdrawal Request');
$withdrawaltotal= round((float)($withdrawaltotal ?? 0), 2);

// generation income
$table="tbl_transaction";
$generation_income = incometotalnew_exact_subject($pdo, $table,$userid,'Generation Income Payout');
$generation_income= round((float)($generation_income ?? 0), 2);

// Direct Bonus

$stmt = $pdo->prepare("SELECT SUM(package) AS total_package FROM tbl_roi_two WHERE user_id = :userid");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$direct_bonus = $result['total_package'] ?? 0;
$direct_bonus= round((float)($direct_bonus ?? 0), 2);


// Ranking Income
$table="tbl_transaction";
$ranking_income = incometotalnew($pdo, $table,$userid,'Ranking Income Payout');
$ranking_income= round((float)($ranking_income ?? 0), 2);

// Profit Sharing Income
$table="tbl_daily_levelinc";
$profit_sharing_income = incometotalnew($pdo, $table,$userid,'Profit Sharing Income');
$profit_sharing_income= round((float)($profit_sharing_income ?? 0), 2);

// Reward Income
$table="tbl_transaction";
$reward_income = incometotalnew($pdo, $table,$userid,'Reward Income');
$reward_income= round((float)($reward_income ?? 0), 2);

// Total Income (Profit Income Wallet + Profit Sharing Wallet)
$total_income = round((float)$profit_income_wallet + (float)$profit_sharing_wallet, 2);

// Leadership Income
$table="tbl_transaction";
$leadership_income_income = incometotalnew($pdo, $table,$userid,'Leadership Income');
$leadership_income_income= round((float)($leadership_income_income ?? 0), 2);

// User Growth Combined Total (7 Incomes: Profit Income, Profit Sharing, Direct Bonus, Mentor/Generation Income, VIP Club/Ranking Income, Company Turnover/Leadership, Rank Reward)
$user_growth_total = round(
    (float)$profit_income_wallet +
    (float)$profit_sharing_income +
    (float)$direct_bonus +
    (float)$generation_income +
    (float)$ranking_income +
    (float)($leadership_income_income ?? 0) +
    (float)$reward_income,
    2
);

// direct income
$table="tbl_levelinc";
$directincome = incometotalnew($pdo, $table,$userid,'Direct Income');
$directincome= round((float)($directincome ?? 0), 2);

// Daily Profit Sharing Incomes
$table="tbl_roiinc";
$roiincome = incometotalnew($pdo,$table,$userid,'Daily Profit Sharing Income');
$roiincome= round((float)($roiincome ?? 0), 2);

// Today Daily Profit Sharing Income
$table="tbl_roiinc";
$dailyroiincome = incometotalnewdate($date,$table,$userid,'Daily Profit Sharing Income');

//Level Income
$table="tbl_daily_levelinc";
$dailylevelincome = incometotalnew($pdo,$table,$userid,'Daily Level Income');
$dailylevelincome= round((float)($dailylevelincome ?? 0), 2);

//Reward income

$table="tbl_rewardinc";
$rewardincome = incometotalnew($pdo,$table,$userid,'Reward Income');
$rewardincome= round((float)($rewardincome ?? 0), 2);



//TotalBusiness

$totallevelbusiness=gettotallevelbusiness($userid);
$totallevelbusiness1=$totallevelbusiness;

$roidata=getroionedatanew($userid);

$roipackage= is_array($roidata) && isset($roidata['package']) ? $roidata['package'] : 0;

// >=$useramount

$totalbusiee=(int)3*$roipackage;



// Fetch tree data
$stmtTree = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid");
$stmtTree->execute([':userid' => $userid]);
$rowTree = $stmtTree->fetch(PDO::FETCH_ASSOC);

$left_team = $rowTree['leftcount'] ?? 0;
$right_team = $rowTree['rightcount'] ?? 0;
$left_total_team = $rowTree['lefttotal'] ?? 0;
$right_total_team = $rowTree['righttotal'] ?? 0;
    

// get my direct actives
$my_left_active_directs = getmydirectactiveleft($userid);
$my_right_active_directs = getmydirectactiveright($userid);

// total team
$total_team=getActiveDownlineCount($userid);

// my direct count
$my_directs=getmydirect($userid);

// get $inactive_team
$inactive_team = ($left_total_team + $right_total_team)-($left_team + $right_team);

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
// --- Direct Referral Slots Backend Logic (6 Slots) ---
$direct_slots = array_fill(0, 6, null);
try {
    $stmtDirects = $pdo->prepare("SELECT id, userid, name, active, total_package FROM user WHERE sponserid = :userid ORDER BY id ASC LIMIT 6");
    $stmtDirects->execute([':userid' => $userid]);
    $fetched_directs = $stmtDirects->fetchAll(PDO::FETCH_ASSOC);

    for ($i = 0; $i < count($fetched_directs); $i++) {
        $d = $fetched_directs[$i];
        $d_userid = $d['userid'];
        $d_active = (int)($d['active'] ?? 0);
        $d_total_package = (float)($d['total_package'] ?? 0);

        // Check investment completed ($145 / active investment record in tbl_roi_one or package >= 145 or total_package > 0)
        $stmtRoiCheck = $pdo->prepare("SELECT COUNT(*) FROM tbl_roi_one WHERE user_id = :uid");
        $stmtRoiCheck->execute([':uid' => $d_userid]);
        $has_investment = ($stmtRoiCheck->fetchColumn() > 0) || ($d_total_package > 0);

        if ($d_active == 1 && $has_investment) {
            $slot_status = 'ACTIVE'; // Active / Green
        } else {
            $slot_status = 'REGISTRATION_ONLY'; // Registered but missing active account or investment
        }

        $direct_slots[$i] = [
            'userid' => $d_userid,
            'name' => $d['name'],
            'status' => $slot_status
        ];
    }
} catch (PDOException $e) {
    // Fallback gracefully on exception
}

?>
<style>
/* Slider container */
.slider {
  position: relative;
  width: 100%;
  height: 100vh;        /* FULL PHONE SCREEN HEIGHT */
  overflow: hidden;
  border-radius: 0;     /* optional: remove radius for real fullscreen */
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

/* Slide Images */
.slide {
  width: 100%;
  height: 100%;
  object-fit: contain;     /* Fill screen without stretching */
  display: none;
  background-color: #000; /* optional, black bars if aspect ratio differs */
}

.slide.active {
  display: block;
}

/* Arrow Buttons */
.arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  font-size: 24px;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 50%;
  z-index: 10;
}

.arrow.left { left: 10px; }
.arrow.right { right: 10px; }

/* REMOVE old height rules */
@media (max-width: 600px) {
  .slider {
    height: 100vh;   /* still full screen */
  }
}

@media (max-width: 400px) {
  .slider {
    height: 100vh;   /* still full screen */
  }
}
</style>


<style>
body.ananta-user-dashboard {
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
#sidebar-wrapper {
  background: #ffffff !important;
  border-right: 1px solid #e2e8f0 !important;
  box-shadow: 4px 0 25px rgba(15, 23, 42, 0.04) !important;
}
#sidebar-wrapper .sidebar-menu > li > a {
  color: #334155 !important;
  font-weight: 600;
  border-left: 3px solid transparent;
}
#sidebar-wrapper .sidebar-menu > li > a:hover,
#sidebar-wrapper .sidebar-menu > li.active > a {
  color: #0284c7 !important;
  background: rgba(2, 132, 199, 0.08) !important;
  border-left-color: #0284c7 !important;
}
#sidebar-wrapper .submenu li a {
  color: #64748b !important;
}
#sidebar-wrapper .submenu li a:hover {
  color: #0284c7 !important;
}
.brand-logo {
  background: #ffffff !important;
  border-bottom: 1px solid #e2e8f0 !important;
}
</style>

<body class="ananta-user-dashboard">

  <!-- Start wrapper-->
  <div id="wrapper">

    <!--Start sidebar-wrapper-->
    <!--End sidebar-wrapper-->

    <!--Start topbar header-->

    <!--End topbar header-->

    <div class="clearfix"></div>

        <div class="content-wrapper py-3 px-2 px-md-4" style="background-color: #f8fafc !important;">
            <!-- Full-Screen Promo Ad Banner Modal Popup (Only once per Login Session) -->
            <?php
            if (isset($_SESSION['show_banner']) && $_SESSION['show_banner'] === true):
                unset($_SESSION['show_banner']); // Display only once after login
                
                // Fetch active promo banners
                $stmtBanner = $pdo->prepare("SELECT img FROM tbl_banner WHERE status = 1 ORDER BY id DESC");
                $stmtBanner->execute();
                $banners = $stmtBanner->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($banners)):
            ?>
            <div id="bannerModal" class="banner-modal-overlay" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 999999; display: flex; align-items: center; justify-content: center; padding: 15px; transition: opacity 0.3s ease;">
                <div class="banner-modal-content" style="background: #ffffff; border-radius: 24px; max-width: 600px; width: 100%; max-height: 90vh; overflow: hidden; position: relative; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.2);">
                    
                    <!-- Close (Cut) Button -->
                    <button type="button" class="btn-close-ad" onclick="closeBannerModal()" style="position: absolute; top: 12px; right: 12px; width: 36px; height: 36px; border-radius: 50%; background: #ffffff; color: #0f172a; border: none; font-size: 22px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 20; box-shadow: 0 4px 15px rgba(0,0,0,0.3); outline: none;">
                        &times;
                    </button>

                    <!-- Ad Banner Header Badge -->
                    <div style="background: linear-gradient(135deg, #0284c7 0%, #00b4d8 100%); padding: 12px 20px; color: #ffffff; font-weight: 700; font-size: 13.5px; display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="zmdi zmdi-notifications-active mr-2"></i> Announcement / Promo Ad</span>
                        <span class="badge badge-light text-dark font-weight-bold" style="font-size: 10.5px;">ANANTA SPECIAL</span>
                    </div>

                    <!-- Carousel Body -->
                    <div class="banner-modal-body p-0" style="max-height: 65vh; overflow-y: auto; text-align: center; background: #0f172a;">
                        <div id="bannerModalCarousel" class="carousel slide" data-ride="carousel" data-interval="3500">
                            <div class="carousel-inner">
                                <?php foreach ($banners as $idx => $b): ?>
                                <div class="carousel-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars($b['img']); ?>" class="d-block w-100" style="max-height: 60vh; object-fit: contain; background: #0f172a;" alt="Promo Banner">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($banners) > 1): ?>
                            <a class="carousel-control-prev" href="#bannerModalCarousel" role="button" data-slide="prev" style="width: 12%;">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.6); padding: 16px; border-radius: 50%;"></span>
                            </a>
                            <a class="carousel-control-next" href="#bannerModalCarousel" role="button" data-slide="next" style="width: 12%;">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.6); padding: 16px; border-radius: 50%;"></span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="p-3 bg-light d-flex justify-content-end align-items-center" style="border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn font-weight-bold text-white px-4 py-2" onclick="closeBannerModal()" style="background: linear-gradient(135deg, #0284c7 0%, #00b4d8 100%); border-radius: 12px; font-size: 13.5px; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3); border: none;">
                            Continue to Dashboard &nbsp;&rarr;
                        </button>
                    </div>
                </div>
            </div>

            <script>
            function closeBannerModal() {
                var modal = document.getElementById('bannerModal');
                if (modal) {
                    modal.style.opacity = '0';
                    setTimeout(function() { modal.style.display = 'none'; }, 250);
                }
            }
            </script>
            <?php
                endif; // end if (!empty($banners))
            endif; // end if ($_SESSION['show_banner'])
            ?>

<!-- Combined Welcome + Joined On + Package -->
<div class="welcome-info-scroll mb-4">
    <div class="welcome-info-row">

        <!-- Welcome Box -->
        <div class="welcome-main-card">
            <div class="welcome-accent"></div>

            <div class="welcome-content">
                <span class="welcome-label">Welcome Back,</span>

                <h1>
                    <?php echo htmlspecialchars($username); ?>
                    <span class="text-primary">
                        <?php echo "$hmpre$userid"; ?>
                    </span>
                </h1>

                <p>
                    Grow Together &nbsp;•&nbsp; Build Bigger
                </p>
            </div>
        </div>


        <!-- Joined On Box -->
        <div class="welcome-small-card">
            <div class="welcome-icon joined-icon">
                <i class="zmdi zmdi-calendar"></i>
            </div>

            <div class="welcome-small-content">
                <span>Joined On</span>
                <h6>
                    <?php echo date('d M Y', strtotime($dateofjoining ?? $date)); ?>
                </h6>
            </div>
        </div>


        <!-- Package Box -->
        <a href="package_buy.php" class="welcome-small-card package-card">

            <div class="welcome-icon package-icon">
                <i class="zmdi zmdi-trending-up"></i>
            </div>

            <div class="welcome-small-content">
                <span>Package</span>
                <h6>
                    <?php
                    echo (!empty($roipackage) && $roipackage > 0)
                        ? formatCurrency($roipackage, $selectedCurrency)
                        : 'Advance';
                    ?>
                </h6>
            </div>

            <i class="zmdi zmdi-chevron-right package-arrow"></i>
        </a>

        <!-- Notification Box -->
        <a href="notifications.php" class="welcome-small-card notification-card">
            <div class="welcome-icon" style="background: #fef2f2; color: #ef4444;">
                <i class="zmdi zmdi-notifications"></i>
            </div>
            <div class="welcome-small-content">
                <span>Notification</span>
                <h6>
                    <?php if (!empty($unreadNotificationCount) && $unreadNotificationCount > 0): ?>
                        <span class="badge badge-pill badge-danger font-weight-bold px-2 py-1" style="background: #ef4444; color: #ffffff; font-size: 11px;"><?php echo $unreadNotificationCount; ?> New</span>
                    <?php else: ?>
                        View All
                    <?php endif; ?>
                </h6>
            </div>
            <i class="zmdi zmdi-chevron-right package-arrow"></i>
        </a>

    </div>
</div>


<style>
/* Main horizontal wrapper */
.welcome-info-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.welcome-info-scroll::-webkit-scrollbar {
    display: none;
}


/* One single row */
.welcome-info-row {
    display: flex;
    align-items: stretch;
    gap: 12px;
    width: max-content;
    min-width: 100%;
}


/* Welcome card */
.welcome-main-card {
    width: 520px;
    min-width: 520px;
    min-height: 110px;

    display: flex;
    align-items: center;

    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 20px;

    padding: 22px 28px;

    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);

    box-sizing: border-box;
}


/* Blue vertical line */
.welcome-accent {
    width: 6px;
    height: 60px;
    flex-shrink: 0;

    background: linear-gradient(
        180deg,
        #0284c7 0%,
        #00b4d8 100%
    );

    border-radius: 4px;
    margin-right: 20px;
}


/* Welcome content */
.welcome-content {
    min-width: 0;
}

.welcome-label {
    display: block;
    color: #64748b;

    font-size: 13px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.welcome-content h1 {
    margin: 2px 0 2px;

    color: #0f172a;

    font-family: 'Plus Jakarta Sans', sans-serif;

    font-size: 28px;
    line-height: 1.2;

    font-weight: 700;
    letter-spacing: -0.5px;

    white-space: nowrap;
}

.welcome-content p {
    margin: 0;

    color: #64748b;

    font-size: 13px;
    font-weight: 600;
}


/* Joined + Package cards */
.welcome-small-card {
    width: 240px;
    min-width: 240px;
    min-height: 110px;

    display: flex;
    align-items: center;

    position: relative;

    background: #ffffff;

    border: 1px solid #e2e8f0;
    border-radius: 16px;

    padding: 18px;

    box-sizing: border-box;

    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);

    text-decoration: none;
}


/* Icon */
.welcome-icon {
    width: 44px;
    height: 44px;

    min-width: 44px;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-right: 14px;
}

.welcome-icon i {
    font-size: 20px;
}


/* Joined icon */
.joined-icon {
    background: #eff6ff;
    color: #0284c7;
}


/* Package icon */
.package-icon {
    background: #f0fdf4;
    color: #16a34a;
}


/* Small card text */
.welcome-small-content {
    min-width: 0;
}

.welcome-small-content span {
    display: block;

    color: #64748b;

    font-size: 11px;
    font-weight: 700;

    text-transform: uppercase;

    margin-bottom: 3px;
}

.welcome-small-content h6 {
    margin: 0;

    color: #0f172a;

    font-size: 14px;
    font-weight: 700;

    white-space: nowrap;
}


/* Package arrow */
.package-arrow {
    margin-left: auto;

    color: #94a3b8;

    font-size: 18px;
}


/* Tablet */
@media (max-width: 991px) {

    .welcome-main-card {
        width: 430px;
        min-width: 430px;
    }

    .welcome-small-card {
        width: 220px;
        min-width: 220px;
    }
}


/* Mobile */
@media (max-width: 575px) {

    .welcome-info-row {
        gap: 10px;
    }

    .welcome-main-card {
        width: 300px;
        min-width: 300px;

        min-height: 100px;

        padding: 18px 20px;
    }

    .welcome-accent {
        width: 5px;
        height: 52px;

        margin-right: 14px;
    }

    .welcome-content h1 {
        font-size: 21px;
    }

    .welcome-label {
        font-size: 11px;
    }

    .welcome-content p {
        font-size: 11px;
    }

    .welcome-small-card {
        width: 210px;
        min-width: 210px;

        min-height: 100px;

        padding: 15px;
    }

    .welcome-icon {
        width: 40px;
        height: 40px;

        min-width: 40px;

        margin-right: 11px;
    }

    .welcome-icon i {
        font-size: 18px;
    }
}
</style>


            <!-- 2. Main "User Growth" Glowing Gradient Hero Banner Card -->
            <div class="card user-growth-hero-card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #00b4d8 50%, #009688 100%) !important; border-radius: 24px; color: #ffffff; position: relative; overflow: hidden;">
                
                <!-- Decorative Bar Chart Graphic in Background -->
                <div style="position: absolute; right: 0; bottom: 0; opacity: 0.18; pointer-events: none; padding-right: 15px;">
                    <svg width="240" height="140" viewBox="0 0 240 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="20" y="90" width="24" height="50" rx="6" fill="white"/>
                        <rect x="56" y="70" width="24" height="70" rx="6" fill="white"/>
                        <rect x="92" y="50" width="24" height="90" rx="6" fill="white"/>
                        <rect x="128" y="30" width="24" height="110" rx="6" fill="white"/>
                        <rect x="164" y="10" width="24" height="130" rx="6" fill="white"/>
                        <path d="M10 80 Q 90 40 210 10" stroke="white" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </div>

                <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
                    <!-- Card Header Icon + Title & Active/Inactive Account Status Badge -->
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); width: 50px; height: 50px; flex-shrink: 0;">
                                <i class="zmdi zmdi-accounts-alt zmdi-hc-2x text-white"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 font-weight-bold text-white" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px;">User Growth</h4>
                                <span class="small" style="color: rgba(255, 255, 255, 0.85); font-weight: 500;">Total Earnings (All 7 Types)</span>
                            </div>
                        </div>

                        <!-- Active / Inactive Status Badge -->
                        <?php if (!empty($isAccountActive)): ?>
                            <div class="account-status-badge d-flex align-items-center" style="background: rgba(34, 197, 94, 0.25); border: 1px solid rgba(34, 197, 94, 0.6); backdrop-filter: blur(8px); border-radius: 100px; padding: 6px 14px; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);">
                                <span class="blinking-dot mr-2"></span>
                                <span style="color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.6px; font-family: 'Plus Jakarta Sans', sans-serif;">ACTIVE</span>
                            </div>
                        <?php else: ?>
                            <div class="account-status-badge d-flex align-items-center" style="background: rgba(37, 99, 235, 0.35); border: 1px solid rgba(147, 197, 253, 0.6); backdrop-filter: blur(8px); border-radius: 100px; padding: 6px 14px; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);">
                                <span class="inactive-dot mr-2"></span>
                                <span style="color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.6px; font-family: 'Plus Jakarta Sans', sans-serif;">INACTIVE</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Big Amount Display with Eye Hide/Show Toggle -->
                    <div class="d-flex align-items-center my-3">
                        <h1 class="mb-0 font-weight-bold text-white mr-3" id="userGrowthAmountText" style="font-size: 38px; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.5px;">
                            <?php echo formatCurrency($user_growth_total, $selectedCurrency); ?>
                        </h1>
                        <button type="button" class="btn p-0 text-white opacity-80" id="toggleGrowthEyeBtn" onclick="toggleUserGrowthVisibility()" title="Toggle Balance Visibility" style="background: transparent; border: none; outline: none;">
                            <i class="zmdi zmdi-eye zmdi-hc-lg" id="growthEyeIcon" style="font-size: 22px;"></i>
                        </button>
                    </div>

                    <!-- Bottom Row: Percentage Pill & View Details Action -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4">
                        <div class="d-flex align-items-center">
                            <span class="badge mr-2" style="background: #22c55e; color: #ffffff; font-size: 13px; font-weight: 700; border-radius: 100px; padding: 6px 14px; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);">
                                <i class="zmdi zmdi-trending-up mr-1"></i> +12.5%
                            </span>
                            <span class="small" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">vs last month</span>
                        </div>

                        <a href="user_growth.php" class="btn text-white font-weight-bold d-inline-flex align-items-center" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 100px; padding: 8px 22px; font-size: 13.5px; transition: all 0.2s; text-decoration: none;">
                            View Details <i class="zmdi zmdi-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <script>
            let isGrowthVisible = true;
            function toggleUserGrowthVisibility() {
                const textEl = document.getElementById('userGrowthAmountText');
                const iconEl = document.getElementById('growthEyeIcon');
                const fullAmount = "<?php echo formatCurrency($user_growth_total, $selectedCurrency); ?>";
                if (isGrowthVisible) {
                    textEl.textContent = "<?php echo getCurrencySymbol($selectedCurrency); ?> ••••••";
                    iconEl.className = "zmdi zmdi-eye-off zmdi-hc-lg";
                    isGrowthVisible = false;
                } else {
                    textEl.textContent = fullAmount;
                    iconEl.className = "zmdi zmdi-eye zmdi-hc-lg";
                    isGrowthVisible = true;
                }
            }
            </script>

            <!-- 3. Direct Slots Section (Always ABOVE Wallet Cards) -->
            <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 20px; overflow: hidden; position: relative;">
                <div class="card-body p-3 p-md-4">
                    <!-- Section Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2" style="border-bottom: 1px dashed #f1f5f9;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #0284c7; width: 44px; height: 44px; flex-shrink: 0; border-radius: 14px;">
                                <i class="zmdi zmdi-accounts-alt" style="font-size: 22px;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">Directs</h4>
                                <span class="text-muted small font-weight-semibold" style="font-size: 13px;">Your 6 Direct Positions</span>
                            </div>
                        </div>
                        <div class="px-3 py-1 text-right" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px;">
                            <span class="d-block text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Directs</span>
                            <span class="font-weight-bold" style="color: #0f172a; font-size: 16px;">6</span>
                        </div>
                    </div>

                    <!-- 6 Direct Positions Container (1 horizontal row on desktop/laptop) -->
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-6 g-2 flex-nowrap overflow-auto py-1" style="scrollbar-width: thin; min-height: 200px;">
                        <?php for ($idx = 0; $idx < 6; $idx++): 
                            $slotNum = $idx + 1;
                            $slotData = $direct_slots[$idx] ?? null;
                        ?>
                            <div class="col" style="min-width: 145px; flex: 1;">
                                <div class="card border-0 h-100 text-center p-2 p-sm-3" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 16px; transition: transform 0.2s, border-color 0.2s;">
                                    <span class="font-weight-bold mb-3 d-block" style="color: #0f172a; font-size: 14px;">Direct <?php echo $slotNum; ?></span>
                                    
                                    <?php if ($slotData && $slotData['status'] === 'ACTIVE'): ?>
                                        <!-- Active / Green Circular Status -->
                                        <div class="position-relative mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 76px; height: 76px;">
                                            <svg width="76" height="76" viewBox="0 0 76 76" style="transform: rotate(-90deg);">
                                                <circle cx="38" cy="38" r="32" stroke="#e2e8f0" stroke-width="6" fill="none"/>
                                                <circle cx="38" cy="38" r="32" stroke="#22c55e" stroke-width="6" fill="none" stroke-dasharray="201" stroke-dashoffset="0" stroke-linecap="round"/>
                                            </svg>
                                            <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #22c55e; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.35);">
                                                <i class="zmdi zmdi-check" style="font-size: 18px; font-weight: bold;"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge w-100 py-2 px-1 font-weight-bold d-inline-flex align-items-center justify-content-center text-nowrap" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; border-radius: 100px; font-size: 11.5px; white-space: nowrap;">
                                                <i class="zmdi zmdi-check-circle mr-1"></i> Active
                                            </span>
                                        </div>

                                    <?php elseif ($slotData && $slotData['status'] === 'REGISTRATION_ONLY'): ?>
                                        <!-- Registration Only / Red Circular Status -->
                                        <div class="position-relative mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 76px; height: 76px;">
                                            <svg width="76" height="76" viewBox="0 0 76 76" style="transform: rotate(-90deg);">
                                                <circle cx="38" cy="38" r="32" stroke="#e2e8f0" stroke-width="6" fill="none"/>
                                                <circle cx="38" cy="38" r="32" stroke="#ef4444" stroke-width="6" fill="none" stroke-dasharray="201" stroke-dashoffset="65" stroke-linecap="round"/>
                                            </svg>
                                            <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #ef4444; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.35);">
                                                <span style="font-weight: 900; font-size: 16px; line-height: 1;">!</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge w-100 py-2 px-1 font-weight-bold d-inline-flex align-items-center justify-content-center text-nowrap" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 100px; font-size: 10.5px; white-space: nowrap;">
                                                <i class="zmdi zmdi-alert-circle mr-1"></i> Registration Only
                                            </span>
                                        </div>

                                    <?php else: ?>
                                        <!-- Empty / Not Registered Neutral Grey Circular Status -->
                                        <div class="position-relative mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 76px; height: 76px;">
                                            <svg width="76" height="76" viewBox="0 0 76 76">
                                                <circle cx="38" cy="38" r="32" stroke="#e2e8f0" stroke-width="6" fill="none"/>
                                            </svg>
                                            <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px; background: #f1f5f9;">
                                                <i class="zmdi zmdi-account" style="font-size: 18px; color: #94a3b8;"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge w-100 py-2 px-1 font-weight-bold d-inline-flex align-items-center justify-content-center text-nowrap" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; border-radius: 100px; font-size: 10.5px; white-space: nowrap;">
                                                <i class="zmdi zmdi-minus-circle-outline mr-1"></i> Not Registered
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- 4. Quick Wallet Access Cards (4 Cards directly BELOW Direct Slots) -->
            <div class="row g-2 mb-4">
                <!-- Card 1: Main Wallet -->
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <a href="main_wallet.php" class="card border-0 shadow-sm quick-wallet-card h-100 text-decoration-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center min-w-0" style="flex: 1;">
                                <div class="rounded-xl quick-wallet-icon p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #0284c7;">
                                    <i class="zmdi zmdi-balance-wallet zmdi-hc-lg"></i>
                                </div>
                                <div class="min-w-0" style="flex: 1;">
                                    <span class="d-block text-muted font-weight-bold quick-wallet-title">Main Wallet</span>
                                    <h4 class="mb-0 font-weight-bold quick-wallet-amount" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?php echo formatCurrency($deposite_wallet, $selectedCurrency); ?>
                                    </h4>
                                </div>
                            </div>
                            <i class="zmdi zmdi-chevron-right text-muted flex-shrink-0 ml-1 d-none d-sm-block" style="font-size: 18px;"></i>
                        </div>
                    </a>
                </div>

                <!-- Card 2: Active Investment -->
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <a href="package_buy.php" class="card border-0 shadow-sm quick-wallet-card h-100 text-decoration-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center min-w-0" style="flex: 1;">
                                <div class="rounded-xl quick-wallet-icon p-2 d-flex align-items-center justify-content-center" style="background: #f0fdf4; color: #16a34a;">
                                    <i class="zmdi zmdi-layers zmdi-hc-lg"></i>
                                </div>
                                <div class="min-w-0" style="flex: 1;">
                                    <span class="d-block text-muted font-weight-bold quick-wallet-title">Active Investment</span>
                                    <h4 class="mb-0 font-weight-bold quick-wallet-amount" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?php echo formatCurrency($roipackage, $selectedCurrency); ?>
                                    </h4>
                                </div>
                            </div>
                            <i class="zmdi zmdi-chevron-right text-muted flex-shrink-0 ml-1 d-none d-sm-block" style="font-size: 18px;"></i>
                        </div>
                    </a>
                </div>

                <!-- Card 3: Net Balance -->
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <a href="income_wallet.php" class="card border-0 shadow-sm quick-wallet-card h-100 text-decoration-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center min-w-0" style="flex: 1;">
                                <div class="rounded-xl quick-wallet-icon p-2 d-flex align-items-center justify-content-center" style="background: #fefce8; color: #ca8a04;">
                                    <i class="zmdi zmdi-money-box zmdi-hc-lg"></i>
                                </div>
                                <div class="min-w-0" style="flex: 1;">
                                    <span class="d-block text-muted font-weight-bold quick-wallet-title">Net Balance</span>
                                    <h4 class="mb-0 font-weight-bold quick-wallet-amount" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?php echo formatCurrency($useramount, $selectedCurrency); ?>
                                    </h4>
                                </div>
                            </div>
                            <i class="zmdi zmdi-chevron-right text-muted flex-shrink-0 ml-1 d-none d-sm-block" style="font-size: 18px;"></i>
                        </div>
                    </a>
                </div>

                <!-- Card 4: Total Withdrawal -->
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <a href="withdraw-history.php" class="card border-0 shadow-sm quick-wallet-card h-100 text-decoration-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center min-w-0" style="flex: 1;">
                                <div class="rounded-xl quick-wallet-icon p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #0284c7;">
                                    <i class="zmdi zmdi-swap-vertical zmdi-hc-lg"></i>
                                </div>
                                <div class="min-w-0" style="flex: 1;">
                                    <span class="d-block text-muted font-weight-bold quick-wallet-title">Total Withdrawal</span>
                                    <h4 class="mb-0 font-weight-bold quick-wallet-amount" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?php echo formatCurrency($withdrawaltotal, $selectedCurrency); ?>
                                    </h4>
                                </div>
                            </div>
                            <i class="zmdi zmdi-chevron-right text-muted flex-shrink-0 ml-1 d-none d-sm-block" style="font-size: 18px;"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- 4. "Income" Section Header -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0 font-weight-bold d-flex align-items-center" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="zmdi zmdi-chart mr-2 text-primary"></i> Income
                </h4>
                <div class="dropdown">
                    <button class="btn btn-sm bg-white border text-muted font-weight-bold rounded-pill px-3 py-1 shadow-sm dropdown-toggle" type="button">
                        <i class="zmdi zmdi-calendar mr-1 text-primary"></i> This Month
                    </button>
                </div>
            </div>

            <!-- 5. Grid of 7 Income Stream Cards -->
            <div class="row mb-4">
                <!-- 1. Profit Income -->
                <div class="col-6 col-md-4 mb-3">
                    <a href="profit_income.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="background: #22c55e; color: #ffffff; width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="zmdi zmdi-trending-up" style="font-size: 18px;"></i>
                            </div>
                            <span class="font-weight-bold text-truncate" style="color: #334155; font-size: 14px;">Profit Income</span>
                        </div>
                        <h3 class="font-weight-bold mb-1 text-truncate" style="color: #0f172a; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <?php echo formatCurrency($profit_income_wallet, $selectedCurrency); ?>
                        </h3>
                        <!-- <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="small font-weight-bold" style="color: #16a34a; font-size: 12.5px;">+8.2%</span>
                            <i class="zmdi zmdi-chevron-right text-muted" style="font-size: 16px;"></i>
                        </div> -->
                    </a>
                </div>

                <!-- 2. Profit Sharing -->
                <div class="col-6 col-md-4 mb-3">
                    <a href="profit_sharing_income.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="background: #0284c7; color: #ffffff; width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="zmdi zmdi-accounts-alt" style="font-size: 18px;"></i>
                            </div>
                            <span class="font-weight-bold text-truncate" style="color: #334155; font-size: 14px;">Profit Sharing</span>
                        </div>
                        <h3 class="font-weight-bold mb-1 text-truncate" style="color: #0f172a; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <?php echo formatCurrency($profit_sharing_income, $selectedCurrency); ?>
                        </h3>
                        <!-- <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="small font-weight-bold" style="color: #16a34a; font-size: 12.5px;">+6.7%</span>
                            <i class="zmdi zmdi-chevron-right text-muted" style="font-size: 16px;"></i>
                        </div> -->
                    </a>
                </div>

                <!-- 3. Direct Bonus -->
                <div class="col-6 col-md-4 mb-3">
                    <a href="direct_bonus.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="background: #9333ea; color: #ffffff; width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="zmdi zmdi-account-add" style="font-size: 18px;"></i>
                            </div>
                            <span class="font-weight-bold text-truncate" style="color: #334155; font-size: 14px;">Direct Bonus</span>
                        </div>
                        <h3 class="font-weight-bold mb-1 text-truncate" style="color: #0f172a; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <?php echo formatCurrency($direct_bonus, $selectedCurrency); ?>
                        </h3>
                        <!-- <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="small font-weight-bold" style="color: #16a34a; font-size: 12.5px;">+5.4%</span>
                            <i class="zmdi zmdi-chevron-right text-muted" style="font-size: 16px;"></i>
                        </div> -->
                    </a>
                </div>

                <!-- 4. Mentor Income -->
                <div class="col-6 col-md-4 mb-3">
                    <a href="mentor_income.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="background: #f59e0b; color: #ffffff; width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="zmdi zmdi-group" style="font-size: 18px;"></i>
                            </div>
                            <span class="font-weight-bold text-truncate" style="color: #334155; font-size: 14px;">Mentor Income</span>
                        </div>
                        <h3 class="font-weight-bold mb-1 text-truncate" style="color: #0f172a; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <?php echo formatCurrency($generation_income, $selectedCurrency); ?>
                        </h3>
                        <!-- <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="small font-weight-bold" style="color: #16a34a; font-size: 12.5px;">+4.9%</span>
                            <i class="zmdi zmdi-chevron-right text-muted" style="font-size: 16px;"></i>
                        </div> -->
                    </a>
                </div>

                <!-- 5. Rank Reward -->
                <div class="col-6 col-md-4 mb-3">
                    <a href="reward_income.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="background: #e11d48; color: #ffffff; width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="zmdi zmdi-card-giftcard" style="font-size: 18px;"></i>
                            </div>
                            <span class="font-weight-bold text-truncate" style="color: #334155; font-size: 14px;">Rank Reward</span>
                        </div>
                        <h3 class="font-weight-bold mb-1 text-truncate" style="color: #0f172a; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <?php echo formatCurrency($reward_income, $selectedCurrency); ?>
                        </h3>
                        <!-- <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="small font-weight-bold" style="color: #16a34a; font-size: 12.5px;">+3.8%</span>
                            <i class="zmdi zmdi-chevron-right text-muted" style="font-size: 16px;"></i>
                        </div> -->
                    </a>
                </div>

                <!-- 6. VIP CLUB Income -->
                <div class="col-6 col-md-4 mb-3">
                    <a href="vip-club.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="background: #6366f1; color: #ffffff; width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="zmdi zmdi-star" style="font-size: 18px;"></i>
                            </div>
                            <span class="font-weight-bold text-truncate" style="color: #334155; font-size: 14px;">VIP CLUB Income</span>
                        </div>
                        <h3 class="font-weight-bold mb-1 text-truncate" style="color: #0f172a; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <?php echo formatCurrency($ranking_income, $selectedCurrency); ?>
                        </h3>
                        <!-- <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="small font-weight-bold" style="color: #16a34a; font-size: 12.5px;">+2.6%</span>
                            <i class="zmdi zmdi-chevron-right text-muted" style="font-size: 16px;"></i>
                        </div> -->
                    </a>
                </div>

                <!-- 7. Company Turnover Income (Full width card) -->
                <div class="col-12 mb-3">
                    <a href="company_turnover_income.php" class="card border-0 shadow-sm p-3 h-100 text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center min-w-0">
                                <div class="rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="background: #0d9488; color: #ffffff; width: 46px; height: 46px; flex-shrink: 0;">
                                    <i class="zmdi zmdi-balance" style="font-size: 22px;"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-weight-bold d-block text-truncate" style="color: #334155; font-size: 14.5px;">Company Turnover Income</span>
                                    <h3 class="font-weight-bold mb-0 text-truncate" style="color: #0f172a; font-size: 22px; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?php echo formatCurrency(($leadership_income_income ?? 0), $selectedCurrency); ?>
                                        <!-- <span class="small font-weight-bold ml-2" style="color: #16a34a; font-size: 13px;">+1.9%</span> -->
                                    </h3>
                                </div>
                            </div>
                            <i class="zmdi zmdi-chevron-right text-muted flex-shrink-0" style="font-size: 20px;"></i>
                        </div>
                    </a>
                </div>
            </div>

        <!-- News Ticker Card -->
        
        


        <!-- Business & Personal Details Grid -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="ananta-fintech-card p-4 h-100">
                    <h5 class="font-weight-bold mb-3 pb-2 border-bottom" style="color: #0f172a;">Personal Profile Details</h5>
                    <div class="d-flex flex-column gap-3" style="font-size: 14px; color: #475569;">
                        <!-- <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Account ID</span>
                            <span class="font-weight-bold text-primary"><?php echo "$hmpre$userid";?></span>
                        </div> -->
                        <!-- <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Full Name</span>
                            <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($username);?></span>
                        </div> -->
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Sponsor ID</span>
                            <span class="font-weight-bold text-dark"><?php echo "$hmpre$usersponser";?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Mobile Number</span>
                            <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($usermobile);?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Email Address</span>
                            <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($useremail);?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Designation Rank</span>
                            <span class="font-weight-bold text-success"><?php echo htmlspecialchars($rank); ?></span>
                        </div>
                        <!-- <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Joining Date</span>
                            <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($dateofjoining);?></span>
                        </div> -->
                    </div>
                </div>
            </div>



            <!-- Business Overview Column -->
            <div class="col-md-6 mb-4">
                <div class="ananta-fintech-card p-4 h-100">
                    <h5 class="font-weight-bold mb-3 pb-2 border-bottom" style="color: #0f172a;">Business Overview</h5>
                    <div class="d-flex flex-column gap-3" style="font-size: 14px; color: #475569;">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">My Directs</span>
                            <span class="font-weight-bold text-dark"><?php echo $my_directs;?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Inactive Team</span>
                            <span class="font-weight-bold text-danger"><?php echo $total_team - ($my_left_active_directs + $my_right_active_directs);?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Personal Package Investment</span>
                            <span class="font-weight-bold text-success"><?php echo formatCurrency(parseInputToUSD($usertotal_package, 'INR'), $selectedCurrency); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Total Team Business</span>
                            <span class="font-weight-bold text-primary"><?php echo formatCurrency(parseInputToUSD($directbusinesstotal, 'INR'), $selectedCurrency); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Team Business (Left / Right)</span>
                            <span class="font-weight-bold text-dark"><?php echo formatCurrency(parseInputToUSD($directbusinesstotalleft, 'INR'), $selectedCurrency) . " / " . formatCurrency(parseInputToUSD($directbusinesstotalright, 'INR'), $selectedCurrency); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Active Team (Left / Right)</span>
                            <span class="font-weight-bold text-dark"><?php echo $my_left_active_directs . " / " . $my_right_active_directs; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Total Downline Members</span>
                            <span class="font-weight-bold text-dark"><?php echo $total_team; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Profile Details Column -->
            
        </div>

        <!-- Level-wise Profit Sharing Income Section -->
        <?php
            // Fetch aggregated actual Level 1 to 15 Profit Sharing totals for the logged-in user
            $levelSharingSummary = array_fill(1, 15, 0.00);
            try {
                $stmtLvlSum = $pdo->prepare("
                    SELECT level_num, SUM(CAST(amount AS DECIMAL(15,2))) AS total_level_amount
                    FROM tbl_daily_levelinc
                    WHERE user_id = :userid AND level_num IS NOT NULL AND level_num >= 1 AND level_num <= 15
                    GROUP BY level_num
                ");
                $stmtLvlSum->execute([':userid' => $userid]);
                $lvlRows = $stmtLvlSum->fetchAll(PDO::FETCH_ASSOC);
                foreach ($lvlRows as $lrow) {
                    $ln = (int)$lrow['level_num'];
                    if ($ln >= 1 && $ln <= 15) {
                        $levelSharingSummary[$ln] = (float)$lrow['total_level_amount'];
                    }
                }
            } catch (Exception $e) {
                // Ignore error if column not queried
            }
        ?>
        <!-- <div class="row mb-4">
            <div class="col-12">
                <div class="ananta-fintech-card p-4 p-md-5">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <h5 class="font-weight-bold mb-0" style="color: #0f172a;">Level-Wise Profit Sharing Income Summary</h5>
                        <a href="profit_sharing_income.php" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 600;">View Transaction History</a>
                    </div>
                    <p class="text-muted small mb-4">Aggregated earnings per level from downline profit sharing activity.</p>

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        <?php for ($l = 1; $l <= 15; $l++): ?>
                        <div class="col mb-3">
                            <div class="p-3 bg-white rounded-lg border text-center shadow-sm" style="border-radius: 14px !important; background: #f8fafc !important;">
                                <span class="badge mb-2" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; font-weight: 700; font-size: 11px;">LEVEL <?php echo $l; ?></span>
                                <h6 class="font-weight-bold mb-0" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                    <?php echo $hmcurrency . ' ' . number_format((float)($levelSharingSummary[$l] ?? 0.00), 2); ?>
                                </h6>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Sponsor Level Team Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="ananta-fintech-card p-4 p-md-5">
                    <h5 class="font-weight-bold mb-3 pb-2 border-bottom" style="color: #0f172a;">Sponsor Level Team Lookup</h5>
                    <form method="POST" id="levelForm" class="row align-items-center mb-4">
                        <div class="col-auto">
                            <label class="font-weight-bold small text-uppercase mb-0" style="color: #475569;">Select Downline Level:</label>
                        </div>
                        <div class="col-auto">
                            <select name="level" id="levelSelect" onchange="this.form.submit()" class="form-control font-weight-bold" style="border-radius: 12px; border-color: #cbd5e1; height: 44px; min-width: 180px; background: #ffffff !important; color: #0f172a !important;">
                                <option value="" style="color: #0f172a !important; background: #ffffff !important;">-- Choose Level --</option>
                                <?php 
                                    for($i=1; $i<=20; $i++){
                                        $selected = (isset($_POST['level']) && $_POST['level'] == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected style='color: #0f172a !important; background: #ffffff !important;'>Level $i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </form>

                    <?php
                        if (isset($_POST['level']) && $_POST['level'] != "") {
                        
                            $userid = $_SESSION['userid']; 
                            $selectedLevel = $_POST['level'];
                        
                            try {
                                $stmt = $pdo->prepare("
                                    SELECT downline_id 
                                    FROM tbl_userlevel_a
                                    WHERE sponser_id = :sid AND level = :lvl
                                
                                    UNION ALL
                                
                                    SELECT downline_id 
                                    FROM tbl_userlevel_b
                                    WHERE sponser_id = :sid AND level = :lvl
                                ");
                        
                                $stmt->execute([
                                    ':sid' => $userid,
                                    ':lvl' => $selectedLevel
                                ]);
                        
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                                echo "<div class='p-3' style='background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;'>";
                                echo "<h6 class='font-weight-bold text-dark mb-3'>Downline Members for Level $selectedLevel:</h6>";
                        
                                if (count($results) > 0) {
                                    echo "<div class='row g-2'>";
                                    foreach ($results as $row) {
                                        $id_name = getuserdatabysponserid($row['downline_id']);
                                        echo "<div class='col-md-6 mb-2'>";
                                        echo "<div class='p-3 bg-white border rounded-lg shadow-sm d-flex justify-content-between align-items-center' style='border-radius: 12px !important;'>";
                                        echo "<div>";
                                        echo "<span class='font-weight-bold text-primary d-block'>" . $hmpre . htmlspecialchars($row['downline_id']) . "</span>";
                                        echo "<span class='small text-muted'>" . htmlspecialchars($id_name['name']) . "</span>";
                                        echo "</div>";
                                        echo "<div>";
                                        echo "<span class='badge px-2 py-1' style='background: rgba(22, 163, 74, 0.1); color: #16a34a; font-weight: 700; border-radius: 6px;'>" . htmlspecialchars($id_name['plan']) . " (" . formatCurrency(parseInputToUSD($id_name['total_package'], 'INR'), $selectedCurrency) . ")</span>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                } else {
                                    echo "<span class='text-danger font-weight-bold small'><i class='fa fa-info-circle me-1'></i> No downline members found for Level $selectedLevel.</span>";
                                }
                        
                                echo "</div>";
                        
                            } catch (PDOException $e) {
                                echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'common/footer.php'; ?>

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

      </div>
      <!-- End container-fluid-->

    </div><!--End content-wrapper-->
    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

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

<script>

    let currentIndex = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlide(index) {
      slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (i === index) {
          slide.classList.add('active');
        }
      });
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % slides.length;
      showSlide(currentIndex);
    }

    function prevSlide() {
      currentIndex = (currentIndex - 1 + slides.length) % slides.length;
      showSlide(currentIndex);
    }
    setInterval(nextSlide, 3000);
  showSlide(currentIndex);
</script>
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 05:58:57 GMT -->

</html>