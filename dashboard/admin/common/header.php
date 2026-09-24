<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strictly allow Admin access ONLY to user AN1290 / 1290
$current_session_user = $_SESSION['userid'] ?? '';
if ($current_session_user === '1290' || $current_session_user === 'AN1290') {
    $_SESSION['auserid'] = 'admin';
} else {
    unset($_SESSION['auserid']);
    header("Location: /dashboard/user1/index.php");
    exit();
}


require_once __DIR__ . '/connection.php';
// require 'common/printmessage.php';
require_once __DIR__ . '/db_method.php';
// require 'common/password.php';
// require 'common/recharge_api.php';

// Set userid from GET or Session
if (isset($_GET['uid'])) {
    $_SESSION['auserid'] = $_GET['uid']; 
    $userid = $_SESSION['auserid']; 
} else {
    $userid = $_SESSION['auserid'];  
}

// error_reporting(E_ALL);
// ini_set('display_errors', 1);



// Fetch admin or user data
$stmt = $pdo->prepare("SELECT * FROM admin WHERE auserid = :userid");
$stmt->execute(['userid' => $userid]);
$rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rowheader) {
    // Fallback to user table for user AN1290 / 1290
    $cleanUid = preg_replace('/^(AN|ANANTA)/i', '', $userid);
    $stmtUser = $pdo->prepare("SELECT * FROM user WHERE userid = :uid OR userid = :clean");
    $stmtUser->execute([':uid' => $userid, ':clean' => $cleanUid]);
    $rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($rowUser) {
        $userid     = $rowUser['userid'];
        $username   = $rowUser['name'];
        $usermobile = $rowUser['mobile'];
    } else {
        $userid     = 'AN1290';
        $username   = 'Ananta Admin';
        $usermobile = 'Admin Account';
    }
} else {
    $userid     = $rowheader['auserid'] ?? 'AN1290';
    $username   = $rowheader['name'] ?? 'Ananta Admin';
    $usermobile = $rowheader['mobile'] ?? 'Admin Account';
}
// $usersponser     = $rowheader['sponserid'];
// $usersponsername = $rowheader['sponsername'];
// $dateofjoining   = $rowheader['joining_date'];
// $status          = $rowheader['status'];
// $useramount      = $rowheader['amount'];
// $useramount      = round((double)$useramount, 2);
// $usertotal_package=$rowheader['total_package'];
// $pin_wallet=$rowheader['pin_wallet'];
// $idactive=$rowheader['active'];
// $kyc=$rowheader['kyc'];

// Fetch home settings
$homeset = getHomeSettings($pdo);
$hmmobile    = $homeset['mobile'];
$hmemail     = $homeset['email'];
$hmaddress   = $homeset['address'];
$hmtitle     = $homeset['title'];
$hmurl       = $homeset['url'];
$hmpackage   = $homeset['package'];
$hmpre       = $homeset['pre'];
$hmbitly     = $homeset['bitly'];
$hmemailfrom = $homeset['emailfrom'];
$hmbg        = $homeset['background'];
$hmlogo      = $homeset['logo'];
$hmfavicon   = $homeset['favicon'];
$hmcolor     = $homeset['color'];
$hmcurrency =  $homeset['currency'];

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$date=date('Y-m-d');
  //Time Formate AM/PM 
$time=date('h:i a');
$day=date("l");

$webtistime=webtistime();
$webtisdate=webtisdate();
$newsdata= getnews();
$news = $newsdata['news'];
?>

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
  <link rel="icon" href="<?php echo $hmfavicon;?>" type="image/x-icon">
  <!-- Vector CSS -->
  <link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.html" rel="stylesheet" />
  <!-- simplebar CSS-->
  <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Sidebar CSS-->
  <link href="assets/css/sidebar-menu.css" rel="stylesheet" />
  <!-- PWA Meta Tags & Manifest -->
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#0a2540">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="<?php echo $hmtitle;?>">
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />
  <link href="/assets/css/app-pwa.css" rel="stylesheet" />
  <link href="/assets/css/app-modern.css" rel="stylesheet" />

  <!-- Font Awesome-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


</head>

<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper">
  <div class="brand-logo" style="padding: 15px; text-align: center;">
    <a href="index.php">
      <img src="/assets/images/logo.png" class="logo-icon" alt="Ananta Logo" style="max-height: 50px; width: auto; object-fit: contain;">
    </a>
  </div>
  <ul class="sidebar-menu do-nicescrol">

    <!-- 1. Dashboard -->
    <li>
      <a href="index.php">
        <i class="zmdi zmdi-home"></i> <span>1. Dashboard</span>
      </a>
    </li>

    <!-- Profile Submenu -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-account"></i> Admin Profile</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="user_profile.php?uid=1290"><i class="zmdi zmdi-circle-o"></i> Admin Details</a></li>
        <li><a href="password.php"><i class="zmdi zmdi-circle-o"></i> Update Password</a></li>
      </ul>
    </li>

    <!-- 2. User Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-accounts-list"></i> 2. User Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="all_user.php"><i class="zmdi zmdi-circle-o"></i> All Users & Profiles</a></li>
        <li><a href="active_all_user.php"><i class="zmdi zmdi-circle-o"></i> Active Users</a></li>
        <li><a href="inactive_all_user.php"><i class="zmdi zmdi-circle-o"></i> Inactive Users</a></li>
        <li><a href="deactive_user.php"><i class="zmdi zmdi-circle-o"></i> Blocked / Suspended</a></li>
        <li><a href="activation_history.php"><i class="zmdi zmdi-circle-o"></i> Activation History ($11)</a></li>
        <li><a href="user_timeline.php"><i class="zmdi zmdi-circle-o"></i> Complete User Timeline</a></li>
        <li><a href="pin_wallet_amount.php"><i class="zmdi zmdi-circle-o"></i> Admin Wallet Control</a></li>
      </ul>
    </li>

    <!-- 3. Wallet Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-balance-wallet"></i> 3. Wallet Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="growth_wallet.php"><i class="zmdi zmdi-circle-o"></i> Growth Wallet & Income</a></li>
        <li><a href="main_wallet.php"><i class="zmdi zmdi-circle-o"></i> Main Wallet & Transfers</a></li>
        <li><a href="income_wallets.php"><i class="zmdi zmdi-circle-o"></i> All Income Wallets</a></li>
        <li><a href="pin_wallet_amount.php"><i class="zmdi zmdi-circle-o"></i> Wallet Credit / Debit</a></li>
        <li><a href="pin_wallet_amount_history.php"><i class="zmdi zmdi-circle-o"></i> Permanent Wallet History</a></li>
      </ul>
    </li>

    <!-- 4. Deposit Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-money-box"></i> 4. Deposit Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="pending-fund-request.php"><i class="zmdi zmdi-circle-o"></i> Pending Fund Requests</a></li>
        <li><a href="fund-request.php?status=1"><i class="zmdi zmdi-circle-o"></i> Approved / Success Deposits</a></li>
        <li><a href="fund-request.php?status=2"><i class="zmdi zmdi-circle-o"></i> Rejected Deposits</a></li>
        <li><a href="fund-request.php"><i class="zmdi zmdi-circle-o"></i> Full Deposit History</a></li>
      </ul>
    </li>

    <!-- 5. P2P Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-swap"></i> 5. P2P Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="p2p_management.php"><i class="zmdi zmdi-circle-o"></i> P2P Control & Investments</a></li>
        <li><a href="p2p_management.php?tab=history"><i class="zmdi zmdi-circle-o"></i> P2P Transaction History</a></li>
      </ul>
    </li>

    <!-- 6. Withdrawal Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-card-off"></i> 6. Withdrawal Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="withdraw-history.php?type=0"><i class="zmdi zmdi-circle-o"></i> Pending Withdrawals</a></li>
        <li><a href="withdraw-history.php?type=hold"><i class="zmdi zmdi-circle-o"></i> Hold Withdrawals</a></li>
        <li><a href="withdraw-history.php?type=1"><i class="zmdi zmdi-circle-o"></i> Approved & Paid</a></li>
        <li><a href="withdraw-history.php?type=2"><i class="zmdi zmdi-circle-o"></i> Rejected Withdrawals</a></li>
        <li><a href="investment-withdraw-history.php"><i class="zmdi zmdi-circle-o"></i> Investment Withdrawals</a></li>
        <li><a href="withdraw-history.php"><i class="zmdi zmdi-circle-o"></i> Full Withdrawal History</a></li>
      </ul>
    </li>

    <!-- 7. Income Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-trending-up"></i> 7. Income Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="income_management.php"><i class="zmdi zmdi-circle-o"></i> Income Dashboard & Overview</a></li>
        <li><a href="monthly-profit-closing.php"><i class="zmdi zmdi-circle-o"></i> Profit Income Closing</a></li>
        <li><a href="daily-level-income.php"><i class="zmdi zmdi-circle-o"></i> Profit Sharing (L1-15)</a></li>
        <li><a href="direct-bonus.php"><i class="zmdi zmdi-circle-o"></i> Direct Bonus 6% (10M)</a></li>
        <li><a href="mentor-income.php"><i class="zmdi zmdi-circle-o"></i> Mentor Income (2%)</a></li>
        <li><a href="vip-club.php"><i class="zmdi zmdi-circle-o"></i> Rank Rewards & VIP Club</a></li>
        <li><a href="generation-income.php"><i class="zmdi zmdi-circle-o"></i> Generation Income</a></li>
        <li><a href="level-income.php"><i class="zmdi zmdi-circle-o"></i> Direct Income</a></li>
      </ul>
    </li>

    <!-- 8. Rank & VIP Club -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-star"></i> 8. Rank & VIP Club</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="rank_settings.php"><i class="zmdi zmdi-circle-o"></i> Rank Settings & Matrix</a></li>
        <li><a href="vip-club.php"><i class="zmdi zmdi-circle-o"></i> VIP Qualifications & Income</a></li>
        <li><a href="vip-club.php?tab=history"><i class="zmdi zmdi-circle-o"></i> VIP History & Closing</a></li>
      </ul>
    </li>

    <!-- 9. Referral / Team Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-sitemap"></i> 9. Team Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="team_management.php"><i class="zmdi zmdi-circle-o"></i> Direct & Binary Team Tree</a></li>
        <li><a href="team_management.php?tab=business"><i class="zmdi zmdi-circle-o"></i> Team Business & History</a></li>
      </ul>
    </li>

    <!-- 10. KYC Management -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-card"></i> 10. KYC Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="pending_kyc.php"><i class="zmdi zmdi-circle-o"></i> Pending KYC Applications</a></li>
        <li><a href="completed_kyc.php"><i class="zmdi zmdi-circle-o"></i> Approved KYC Records</a></li>
        <li><a href="kyc.php"><i class="zmdi zmdi-circle-o"></i> Full KYC History & Review</a></li>
      </ul>
    </li>

    <!-- 11. Ticket Support -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-help-outline"></i> 11. Ticket Support</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="support_tickets.php?status=OPEN"><i class="zmdi zmdi-circle-o"></i> Open Support Tickets</a></li>
        <li><a href="support_tickets.php?status=PENDING"><i class="zmdi zmdi-circle-o"></i> Pending Support Tickets</a></li>
        <li><a href="support_tickets.php?status=RESOLVED"><i class="zmdi zmdi-circle-o"></i> Resolved Tickets</a></li>
        <li><a href="support_tickets.php"><i class="zmdi zmdi-circle-o"></i> Ticket History & Replies</a></li>
        <li><a href="user_enquiry.php"><i class="zmdi zmdi-circle-o"></i> Website Enquiries</a></li>
      </ul>
    </li>

    <!-- 12. Notification Centre -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-notifications"></i> 12. Notification Centre</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="notification_centre.php"><i class="zmdi zmdi-circle-o"></i> Global Broadcast Notification</a></li>
        <li><a href="notification_centre.php?tab=user"><i class="zmdi zmdi-circle-o"></i> User-wise Targeted Notice</a></li>
      </ul>
    </li>

    <!-- 13. Offer / Popup / Banner -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-image"></i> 13. Offer / Popup / Banner</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="offer_update.php"><i class="zmdi zmdi-circle-o"></i> Offer Popup & ON/OFF</a></li>
        <li><a href="manage_banner.php"><i class="zmdi zmdi-circle-o"></i> Website Banners</a></li>
      </ul>
    </li>

    <!-- 14. Reports -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-chart"></i> 14. Financial Reports</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="reports.php?type=daily"><i class="zmdi zmdi-circle-o"></i> Daily Financial Summary</a></li>
        <li><a href="reports.php?type=monthly"><i class="zmdi zmdi-circle-o"></i> Monthly Financial Summary</a></li>
        <li><a href="reports.php?type=yearly"><i class="zmdi zmdi-circle-o"></i> Yearly Financial Summary</a></li>
        <li><a href="reports.php?type=user"><i class="zmdi zmdi-circle-o"></i> User-wise Reports</a></li>
        <li><a href="reports.php?type=investment"><i class="zmdi zmdi-circle-o"></i> Investment Reports</a></li>
        <li><a href="reports.php?type=withdrawal"><i class="zmdi zmdi-circle-o"></i> Withdrawal Reports</a></li>
        <li><a href="reports.php?type=income"><i class="zmdi zmdi-circle-o"></i> Income Reports</a></li>
        <li><a href="reports.php?type=business"><i class="zmdi zmdi-circle-o"></i> Business Reports</a></li>
        <li><a href="reports.php?type=company"><i class="zmdi zmdi-circle-o"></i> Company Revenue Reports</a></li>
      </ul>
    </li>

    <!-- 15. Admin Audit & Website Controls -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-settings"></i> 15. Audit & Controls</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="admin_audit_controls.php"><i class="zmdi zmdi-circle-o"></i> Admin Audit Log</a></li>
        <li><a href="admin_audit_controls.php?tab=controls"><i class="zmdi zmdi-circle-o"></i> Website Controls (ON/OFF)</a></li>
        <li><a href="roi-update.php"><i class="zmdi zmdi-circle-o"></i> Set Profit Percentage</a></li>
      </ul>
    </li>

    <li>
      <a href="logout.php">
        <i class="zmdi zmdi-power"></i> <span>Logout</span>
      </a>
    </li>
  </ul>
</div>

<!-- Dropdown Script & Styles -->
<style>
/* SUBMENU STYLING */
.submenu {
    display: none;
    list-style: none;
    padding-left: 20px;
}
.submenu li a {
    display: block;
    padding: 6px 0;
    font-size: 14px;
    color: #ccc;
    text-decoration: none;
}
.submenu li a:hover {
    color: #fff;
}

.has-sub > .menu-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
/* SUBMENU & SIDEBAR MENU REDESIGN STYLING (ADMIN) */
.sidebar-menu {
    padding: 15px 12px !important;
}
.sidebar-menu > li {
    margin-bottom: 4px;
}
.sidebar-menu > li > a {
    display: flex !important;
    align-items: center !important;
    gap: 12px;
    padding: 10px 14px !important;
    border-radius: 12px !important;
    color: #334155 !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    text-decoration: none !important;
    transition: all 0.25s ease !important;
    border-left: none !important;
}
.sidebar-menu > li > a i {
    font-size: 17px !important;
    color: #64748b;
    transition: color 0.25s ease;
}
.sidebar-menu > li:hover > a,
.sidebar-menu > li.active > a,
.sidebar-menu > li.has-sub.active > a {
    background: rgba(22, 163, 74, 0.1) !important;
    color: #16a34a !important;
    border-left: 3px solid #16a34a !important;
}
.sidebar-menu > li:hover > a i,
.sidebar-menu > li.active > a i,
.sidebar-menu > li.has-sub.active > a i,
.sidebar-menu > li.has-sub.active > a .arrow-icon {
    color: #16a34a !important;
}
.submenu {
    display: none;
    list-style: none;
    padding-left: 28px !important;
    margin-top: 2px;
    margin-bottom: 6px;
}
.submenu li a {
    display: flex !important;
    align-items: center !important;
    gap: 8px;
    padding: 7px 12px !important;
    font-size: 13px !important;
    color: #64748b !important;
    font-weight: 500;
    border-radius: 8px !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
}
.submenu li a:hover,
.submenu li.active a {
    color: #16a34a !important;
    background: rgba(22, 163, 74, 0.1) !important;
    font-weight: 700 !important;
}
.has-sub > .menu-toggle {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
}
.arrow-icon {
    transition: transform 0.3s ease !important;
    transform: rotate(0deg) !important;
    color: #94a3b8;
    font-size: 14px !important;
}
.has-sub.active > .menu-toggle .arrow-icon {
    transform: rotate(180deg) !important;
    color: #16a34a;
}
.has-sub.active > .submenu {
    display: block;
}

/* ======================================================
   FIXED — Admin Sidebar Responsive + Offset
   ====================================================== */
#sidebar-wrapper {
    width: 260px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    background: #ffffff !important;
    z-index: 9999;
    overflow-y: auto;
    overflow-x: hidden;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 4px 0 25px rgba(15, 23, 42, 0.05);
    border-right: 1px solid #e2e8f0;
}

/* Desktop View (>= 992px): Fixed left sidebar & offset content */
@media (min-width: 992px) {
    #sidebar-wrapper {
        margin-left: 0 !important;
    }
    .content-wrapper {
        margin-left: 260px !important;
        padding-top: 100px !important; /* Guaranteed zero topbar overlap */
        padding-left: 28px !important;
        padding-right: 28px !important;
        transition: margin-left 0.3s ease;
    }
    .topbar-nav {
        left: 260px !important;
        width: calc(100% - 260px) !important;
        position: fixed !important;
        top: 0 !important;
        z-index: 9998 !important;
    }
    .topbar-nav .navbar {
        left: 260px !important;
        width: calc(100% - 260px) !important;
    }
    .toggle-menu {
        display: none !important; /* Hide hamburger toggle on laptop/desktop */
    }
}

/* Mobile & Tablet View (< 992px): Drawer sidebar with toggle */
@media (max-width: 991px) {
    #sidebar-wrapper {
        margin-left: -260px;
    }
    #sidebar-wrapper.toggled {
        margin-left: 0 !important;
    }
    .content-wrapper {
        margin-left: 0 !important;
        padding-top: 100px !important; /* Guaranteed zero topbar overlap */
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    .topbar-nav {
        left: 0 !important;
        width: 100% !important;
        position: fixed !important;
        top: 0 !important;
        z-index: 9998 !important;
    }
    .topbar-nav .navbar {
        left: 0 !important;
        width: 100% !important;
    }
    .toggle-menu {
        display: block !important;
    }
}

/* Scrollbar styling (optional) */
#sidebar-wrapper::-webkit-scrollbar {
    width: 6px;
}
#sidebar-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
#sidebar-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Ensure navbar stays above sidebar */
.topbar-nav {
    position: relative;
    z-index: 9998;
}
.toggle-menu {
    z-index: 10001;
    position: relative;
}

</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Submenu toggle listener
    document.querySelectorAll('.has-sub > .menu-toggle').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
    });

    // 2. Auto-detect active page URL and highlight menu item in green
    var currentPath = window.location.pathname.split('/').pop() || 'index.php';
    var currentSearch = window.location.search;
    var fullUrl = currentPath + currentSearch;

    document.querySelectorAll('#sidebar-wrapper a').forEach(function(link) {
        var href = link.getAttribute('href');
        if (!href || href === 'javascript:void(0)' || href === 'javascript:void();') return;

        if (href === fullUrl || href === currentPath) {
            var li = link.closest('li');
            if (li) {
                li.classList.add('active');
            }
            var parentSub = link.closest('.has-sub');
            if (parentSub) {
                parentSub.classList.add('active');
            }
        }
    });
});
</script>

<!--Start topbar header-->
<header class="topbar-nav">
  <nav class="navbar navbar-expand fixed-top">
    <ul class="navbar-nav mr-auto align-items-center">
      <li class="nav-item">
        <a class="nav-link toggle-menu" href="javascript:void();">
          <i class="icon-menu menu-icon"></i>
        </a>
      </li>
      <!--<li class="nav-item">-->
      <!--  <form class="search-bar">-->
      <!--    <input type="text" class="form-control" placeholder="Enter keywords">-->
      <!--    <a href="javascript:void();"><i class="icon-magnifier"></i></a>-->
      <!--  </form>-->
      <!--</li>-->
    </ul>

    <ul class="navbar-nav align-items-center right-nav-link">
      <!--<li class="nav-item dropdown-lg">-->
      <!--  <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown"-->
      <!--    href="javascript:void();">-->
      <!--    <i class="fa fa-envelope-open-o"></i></a>-->
      <!--</li>-->
      <!--<li class="nav-item dropdown-lg">-->
      <!--  <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown"-->
      <!--    href="javascript:void();">-->
      <!--    <i class="fa fa-bell-o"></i></a>-->
      <!--</li>-->
      <!--<li class="nav-item language">-->
      <!--  <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown"-->
      <!--    href="javascript:void();"><i class="fa fa-flag"></i></a>-->
      <!--  <ul class="dropdown-menu dropdown-menu-right">-->
      <!--    <li class="dropdown-item"> <i class="flag-icon flag-icon-gb mr-2"></i> English</li>-->
      <!--    <li class="dropdown-item"> <i class="flag-icon flag-icon-fr mr-2"></i> French</li>-->
      <!--    <li class="dropdown-item"> <i class="flag-icon flag-icon-cn mr-2"></i> Chinese</li>-->
      <!--    <li class="dropdown-item"> <i class="flag-icon flag-icon-de mr-2"></i> German</li>-->
      <!--  </ul>-->
      <!--</li>-->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="true" aria-expanded="false">
          <span class="user-profile d-flex align-items-center justify-content-center">
            <img src="/assets/images/usera.png" class="img-circle" alt="Admin Profile" style="width:40px; height:40px; object-fit:cover; border:2px solid #0284c7; border-radius:50%; box-shadow:0 3px 10px rgba(2,132,199,0.25);">
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right shadow-lg border-0" style="border-radius:16px; padding:14px; margin-top:10px; background:#ffffff; min-width:230px;">
          <li class="dropdown-item user-details" style="border-bottom:1px solid #f1f5f9; padding-bottom:10px; margin-bottom:8px;">
            <a href="user_profile.php?uid=1290" style="text-decoration:none;">
              <div class="media align-items-center">
                <div class="avatar mr-2">
                  <img class="align-self-start img-circle" src="/assets/images/usera.png" alt="Admin Profile" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                </div>
                <div class="media-body">
                  <h6 class="mt-0 mb-0 user-title font-weight-bold" style="color:#0f172a; font-size:14px;"><?php echo htmlspecialchars($username ?? 'Ananta Admin'); ?></h6>
                  <p class="user-subtitle mb-0 text-muted small" style="font-size:12px;"><?php echo htmlspecialchars($usermobile ?? 'Admin Account'); ?></p>
                </div>
              </div>
            </a>
          </li>
          <li class="dropdown-item" style="padding: 8px 12px; border-radius:8px;">
            <a href="user_profile.php?uid=1290" class="d-flex align-items-center gap-2 text-dark font-weight-bold small" style="color:#0f172a !important; text-decoration:none;">
              <i class="fa fa-user-circle text-primary mr-2"></i> Admin Profile
            </a>
          </li>
          <li class="dropdown-item" style="padding: 8px 12px; border-radius:8px;">
            <a href="password.php" class="d-flex align-items-center gap-2 text-dark font-weight-bold small" style="color:#0f172a !important; text-decoration:none;">
              <i class="fa fa-key text-primary mr-2"></i> Update Password
            </a>
          </li>
          <li class="dropdown-divider" style="margin: 6px 0;"></li>
          <li class="dropdown-item" style="padding: 8px 12px; border-radius:8px;">
            <a href="logout.php" class="d-flex align-items-center gap-2 text-danger font-weight-bold small" style="color:#ef4444 !important; text-decoration:none;">
              <i class="icon-power mr-2"></i> Logout
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</header>

<style>
.dropdown-menu.show,
.dropdown.show > .dropdown-menu {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: absolute !important;
    right: 0 !important;
    top: 100% !important;
    z-index: 999999 !important;
}
</style>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".toggle-menu");
    const sidebar = document.getElementById("sidebar-wrapper");

    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener("click", function (e) {
        e.preventDefault();
        sidebar.classList.toggle("toggled");
      });
    }

    // Toggle Topbar Dropdowns
    document.querySelectorAll('[data-toggle="dropdown"]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var parent = this.closest('.dropdown');
            if (parent) {
                var isShow = parent.classList.contains('show');
                document.querySelectorAll('.dropdown.show').forEach(function(d) {
                    d.classList.remove('show');
                    var m = d.querySelector('.dropdown-menu');
                    if (m) m.classList.remove('show');
                });
                if (!isShow) {
                    parent.classList.add('show');
                    var menu = parent.querySelector('.dropdown-menu');
                    if (menu) menu.classList.add('show');
                }
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.show').forEach(function(d) {
                d.classList.remove('show');
                var m = d.querySelector('.dropdown-menu');
                if (m) m.classList.remove('show');
            });
        }
    });
  });
</script>
<!--End topbar header-->

<style>
/* Global High-Contrast Form Controls & Export Buttons Fix for Admin Panel */
.form-control, 
select.form-control, 
textarea.form-control, 
input[type="text"].form-control, 
input[type="number"].form-control, 
input[type="email"].form-control, 
input[type="password"].form-control, 
input[type="date"].form-control,
.search-bar input.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    opacity: 1 !important;
    box-shadow: none !important;
}

.form-control:focus, 
select.form-control:focus, 
textarea.form-control:focus, 
input[type="text"].form-control:focus, 
input[type="number"].form-control:focus, 
input[type="date"].form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
}

.form-control[readonly], 
.form-control:disabled, 
select.form-control:disabled {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border-color: #cbd5e1 !important;
}

select.form-control option,
select option {
    background-color: #ffffff !important;
    color: #0f172a !important;
    padding: 8px !important;
}

.form-control::placeholder, 
textarea.form-control::placeholder,
input::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}

label {
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 6px !important;
}

/* High-Contrast Export & Header Buttons */
.btn-export-excel,
a[href*="export.php?"][href*="format=csv"],
a[href*="format=excel"],
.btn-export-csv {
    background-color: #ffffff !important;
    color: #15803d !important;
    border: 1.5px solid #16a34a !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
    border-radius: 10px !important;
}

.btn-export-excel:hover,
a[href*="export.php?"][href*="format=csv"]:hover {
    background-color: #f0fdf4 !important;
    color: #166534 !important;
    border-color: #15803d !important;
}

.btn-export-pdf,
a[href*="export.php?"][href*="format=pdf"] {
    background-color: #ffffff !important;
    color: #b91c1c !important;
    border: 1.5px solid #dc2626 !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
    border-radius: 10px !important;
}

.btn-export-pdf:hover,
a[href*="export.php?"][href*="format=pdf"]:hover {
    background-color: #fef2f2 !important;
    color: #991b1b !important;
    border-color: #b91c1c !important;
}
</style>

