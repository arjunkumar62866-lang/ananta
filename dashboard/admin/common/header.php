<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-authenticate Admin session if user session is AN1290 / 1290
if (isset($_SESSION['userid']) && ($_SESSION['userid'] == '1290' || $_SESSION['userid'] == 'AN1290')) {
    $_SESSION['auserid'] = 'admin';
}

if (!isset($_SESSION["auserid"])) { 
    header("Location:login.php");
    exit();
}

require_once 'common/connection.php';
// require 'common/printmessage.php';
require_once 'common/db_method.php';
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



// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM admin WHERE auserid = :userid");
$stmt->execute(['userid' => $userid]);
$rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

// $userimage       = $rowheader['image'];
$userid          = $rowheader['auserid'];
$username        = $rowheader['name'];
// $useremail       = $rowheader['email'];
$usermobile      = $rowheader['mobile'];
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
<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper">
  <div class="brand-logo" style="padding: 15px; text-align: center;">
    <a href="index.php">
      <img src="/assets/images/logo.png" class="logo-icon" alt="Ananta Logo" style="max-height: 50px; width: auto; object-fit: contain;">
    </a>
  </div>
  <ul class="sidebar-menu do-nicescrol">

    <li>
      <a href="index.php">
        <i class="zmdi zmdi-home"></i> <span>Dashboard</span>
      </a>
    </li>
    
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi zmdi-account"></i> Profile</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="password.php"><i class="zmdi zmdi-circle-o"></i> Update Password </a></li>
       
      </ul>
    </li>

    <!-- Profile Tools with Submenu -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-edit"></i> User Management</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="all_user.php"><i class="zmdi zmdi-circle-o"></i> All Users</a></li>
        <li><a href="active_all_user.php"><i class="zmdi zmdi-circle-o"></i> Active Users</a></li>
        <li><a href="inactive_all_user.php"><i class="zmdi zmdi-circle-o"></i> Inactive Users</a></li>
        <li><a href="deactive_user.php"><i class="zmdi zmdi-circle-o"></i> Blocked Users</a></li>
        <li><a href="pending_all_user.php"><i class="zmdi zmdi-circle-o"></i> Pending Users</a></li>
        <li><a href="news.php"><i class="zmdi zmdi-circle-o"></i> Update News</a></li>
        <!--<li><a href="#"><i class="zmdi zmdi-circle-o"></i> Welcome Letter</a></li>-->
      </ul>
    </li>
    
      <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi zmdi-accounts"></i> KYC Details</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="pending_kyc.php"><i class="zmdi zmdi-circle-o"></i> Pending KYC </a></li>
        <li><a href="completed_kyc.php"><i class="zmdi zmdi-circle-o"></i> Completed KYC </a></li>
        <!--<li><a href="tree.php"><i class="zmdi zmdi-circle-o"></i> Tree View </a></li>-->
      </ul>
    </li>
    
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-trending-up"></i><span> Incomes</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="generation-income.php"><i class="zmdi zmdi-circle-o"></i> Generation Income</a></li>
        <li><a href="level-income.php"><i class="zmdi zmdi-circle-o"></i> Direct Income</a></li>
        <!--<li><a href="kyc.php"><i class="zmdi zmdi-circle-o"></i> Daily Profit Sharing Income</a></li>-->
        <li><a href="direct-bonus.php"><i class="zmdi zmdi-circle-o"></i> Direct Bonus 10M</a></li>
        <li><a href="daily-level-income.php"><i class="zmdi zmdi-circle-o"></i> Profit sharing Income</a></li>
        <li><a href="ranking-income.php"><i class="zmdi zmdi-circle-o"></i> Ranking Income</a></li>
        <li><a href="leadership-income.php"><i class="zmdi zmdi-circle-o"></i> Leadership Bonus</a></li>
        <li><a href="reward-income.php"><i class="zmdi zmdi-circle-o"></i> Rank and Rewards</a></li>
      </ul>
    </li>
    
    
    
<!--    <li class="has-sub">-->
<!--    <a href="javascript:void(0)" class="menu-toggle">-->
<!--        <span><i class="zmdi zmdi-shopping-cart"></i><span>Our Shopping Portal</span></span>-->
<!--        <i class="zmdi zmdi-chevron-down arrow-icon"></i>-->
<!--    </a>-->
<!--    <ul class="sidebar-submenu">-->
<!--        
        
<!--            $sel = "SELECT * FROM tbl_category WHERE status = :status ORDER BY id ASC";-->
<!--            $stmt = $pdo->prepare($sel);-->
<!--            $stmt->execute([':status' => 1]);-->
<!--            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);-->

<!--            if ($categories) {-->
<!--                foreach ($categories as $rowp) {-->
<!--                    ?>-->
<!--                    <li>-->
<!--                        <a target="_blank" href="all-product.php?slug=<?php echo htmlspecialchars($rowp['slug']); ?>">-->
<!--                            <i class="fa fa-circle-o"></i> <?php echo htmlspecialchars($rowp['name']); ?>-->
<!--                        </a>-->
<!--                    </li>-->
<!--                    
<!--                }-->
<!--            }-->
        
<!--        ?>-->
<!--    </ul>-->
<!--</li>-->

<!--<li class="has-sub">-->
<!--      <a href="javascript:void(0)" class="menu-toggle">-->
<!--        <span><i class="zmdi zmdi-shopping-basket"></i><span> Order form</span></span>-->
<!--        <i class="zmdi zmdi-chevron-down arrow-icon"></i>-->
<!--      </a>-->
<!--      <ul class="submenu">-->
<!--        <li><a href="all-order.php"><i class="zmdi zmdi-circle-o"></i> Orders </a></li>-->
       
<!--      </ul>-->
<!--    </li>-->

    
    
    <!--<li>-->
    <!--  <a href="#">-->
    <!--    <i class="zmdi zmdi-accounts"></i> <span>My Team</span>-->
    <!--  </a>-->
    <!--</li>-->
    
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-balance-wallet"></i><span> Withdrawal</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="withdraw-history.php?type=0"><i class="zmdi zmdi-circle-o"></i> Withdraw Pending</a></li>
        <li><a href="investment-withdraw-history.php?type=3"><i class="zmdi zmdi-circle-o"></i> Investment Withdraw Pending</a></li>
        <li><a href="withdraw-history.php?type=1"><i class="zmdi zmdi-circle-o"></i> Withdraw Approved</a></li>
        <li><a href="withdraw-history.php?type=2"><i class="zmdi zmdi-circle-o"></i> Withdraw Cancel</a></li>
      </ul>
    </li>
    
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-money"></i><span> Manage Generation Income</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="roi-update.php"><i class="zmdi zmdi-circle-o"></i> Set Percentage</a></li>
        <!--<li><a href="withdraw-history.php"><i class="zmdi zmdi-circle-o"></i> Withdraw History</a></li>-->
      </ul>
    </li>
    

   <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-account-box"></i><span> Fund Request</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="pending-fund-request.php"><i class="zmdi zmdi-circle-o"></i> Pending Fund Request</a></li>
        <li><a href="fund-request.php"><i class="zmdi zmdi-circle-o"></i> Fund Request History</a></li>
        <!--<li><a href="my_investments.php"><i class="zmdi zmdi-circle-o"></i> My Investments</a></li>-->
      </ul>
    </li>
    
   <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-account-box"></i><span> Fund</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="pin_wallet_amount.php"><i class="zmdi zmdi-circle-o"></i> Add Fund</a></li>
        <li><a href="pin_wallet_amount_history.php"><i class="zmdi zmdi-circle-o"></i> Manage Fund History</a></li>
        <!--<li><a href="my_investments.php"><i class="zmdi zmdi-circle-o"></i> My Investments</a></li>-->
      </ul>
    </li>
    
    
   <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-help"></i><span> Enquery</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="user_enquiry.php?title=Pending Enquiry"><i class="zmdi zmdi-circle-o"></i> Enquery Pending</a></li>
        <li><a href="user_enquiry.php?title=Viewed Enquiry"><i class="zmdi zmdi-circle-o"></i> Enquery Viewed</a></li>
        <!--<li><a href="enquery-history.php"><i class="zmdi zmdi-circle-o"></i> Enquery History</a></li>-->
      </ul>
    </li>
   <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-settings"></i><span> Settings</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="manage_banner.php"><i class="zmdi zmdi-circle-o"></i> Banner update</a></li>
        <!--<li><a href="user_enquiry.php?title=Viewed Enquiry"><i class="zmdi zmdi-circle-o"></i> Enquery Viewed</a></li>-->
        <!--<li><a href="enquery-history.php"><i class="zmdi zmdi-circle-o"></i> Enquery History</a></li>-->
      </ul>
    </li>

    <li>
      <a href="logout.php">
        <i class="zmdi zmdi-power"></i> <span>Logout</span>
      </a>
    </li>
    
    
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
.sidebar-menu > li.active > a {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.08) 0%, rgba(22, 163, 74, 0.08) 100%) !important;
    color: #0284c7 !important;
}
.sidebar-menu > li:hover > a i,
.sidebar-menu > li.active > a i {
    color: #0284c7 !important;
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
.submenu li a:hover {
    color: #16a34a !important;
    background: rgba(22, 163, 74, 0.06) !important;
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
    color: #0284c7;
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
document.querySelectorAll('.has-sub > .menu-toggle').forEach(item => {
    item.addEventListener('click', function () {
        this.parentElement.classList.toggle('active');
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
      <li class="nav-item">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
          <span class="user-profile">
            <img src="images/logo.png" class="img-circle" alt="admin avatar">
          </span>
        </a>
        <!--<ul class="dropdown-menu dropdown-menu-right">-->
        <!--  <li class="dropdown-item user-details">-->
        <!--    <a href="javaScript:void();">-->
        <!--      <div class="media">-->
        <!--        <div class="avatar">-->
        <!--          <img class="align-self-start mr-3" src="images/<php echo $userimage; ?>" alt="user avatar">-->
        <!--        </div>-->
        <!--        <div class="media-body">-->
        <!--          <h6 class="mt-2 user-title"><php echo $username; ?></h6>-->
        <!--          <p class="user-subtitle"><php echo $useremail; ?></p>-->
        <!--        </div>-->
        <!--      </div>-->
        <!--    </a>-->
        <!--  </li>-->
        <!--  <li class="dropdown-divider"></li>-->
        <!--  <li class="dropdown-item"><i class="icon-envelope mr-2"></i> Inbox</li>-->
        <!--  <li class="dropdown-divider"></li>-->
        <!--  <li class="dropdown-item"><i class="icon-wallet mr-2"></i> Account</li>-->
        <!--  <li class="dropdown-divider"></li>-->
        <!--  <li class="dropdown-item"><i class="icon-settings mr-2"></i> Setting</li>-->
        <!--  <li class="dropdown-divider"></li>-->
        <!--  <li class="dropdown-item"><a href="logout.php"><i class="icon-power mr-2"></i> Logout</a></li>-->
        <!--</ul>-->
      </li>
    </ul>
  </nav>
</header>

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
  });
</script>
<!--End topbar header-->
