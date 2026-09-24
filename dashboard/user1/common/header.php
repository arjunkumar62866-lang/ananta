<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// session_start();
if (!isset($_SESSION["userid"])) { 
    header("Location:login.php");
    exit();
}

require_once 'common/connection.php';
// require 'common/printmessage.php';
require_once 'common/db_method.php';
require_once 'common/login_reg_control_helper.php';

// --- Login & Registration Access Control Check for Active User Session ---
$lrcState = getLoginRegControlState($pdo);
if ($lrcState['status'] === 'OFF') {
    if ($lrcState['message_type'] === 'ERROR') {
        // Immediately invalidate/logout logged-in user session
        unset($_SESSION['userid']);
        session_destroy();
        header("Location: login.php");
        exit();
    } elseif ($lrcState['message_type'] === 'WARNING') {
        // Set warning message to display on page load for authenticated users
        $_SESSION['lrc_warning_message'] = $lrcState['message_text'];
    }
}

// Set userid from GET or Session
if (isset($_GET['uid'])) {
    $_SESSION['userid'] = $_GET['uid']; 
    $userid = $_SESSION['userid']; 
} else {
    $userid = $_SESSION['userid'];  
}

$selectedCurrency = getUserCurrency();
$_SESSION['currency'] = $selectedCurrency;
$_SESSION['selected_currency'] = $selectedCurrency;

// error_reporting(E_ALL);
// ini_set('display_errors', 1);



// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

$userimage       = $rowheader['user_image'];
$userid          = $rowheader['userid'];
$username        = $rowheader['name'];
$useremail       = $rowheader['email'];
$usermobile      = $rowheader['mobile'];
$usersponser     = $rowheader['sponserid'];
$usersponsername = $rowheader['sponsername'];
$dateofjoining   = $rowheader['joining_date'];
$status          = $rowheader['status'];
$useramount      = $rowheader['amount'];
$user_income     = $rowheader['total_inc'];
$rank            = $rowheader['rank'];
$side            =$rowheader['join_side'];
$useramount      = round((float)$useramount, 2);
$usertotal_package=$rowheader['total_package'];
$pin_wallet=$rowheader['pin_wallet'];
$idactive=$rowheader['active'];
$kyc=$rowheader['kyc'];
$pending_geninc = $rowheader['pending_geninc'];
$profit_income_wallet = $rowheader['profit_income_wallet'] ?? 0;
$profit_sharing_wallet = $rowheader['profit_sharing_wallet'] ?? 0;
$direct_bonus_wallet = $rowheader['direct_bonus_wallet'] ?? 0;



$treedata=getusertreedata($userid);
$leftid = $treedata['left'];
$rightid = $treedata['right'];
$left_team = $treedata['leftcount'];
$right_team = $treedata['rightcount'];
$total_team=$left_team+$right_team;
$left_total = $treedata['lefttotal'];
$right_total = $treedata['righttotal'];
$all_total=$left_total+$right_total;

$left_active=$left_team;
$right_active=$right_team;

$left_pv = $treedata['leftpv'];
$right_pv = $treedata['rightpv'];

$left_sp = $treedata['leftsp'];
$right_sp = $treedata['rightsp'];




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
$hmmatching_amount = $homeset['matching_amount'];

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
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="<?php echo $hmtitle;?>">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Funnel+Display:wght@300..800&display=swap" rel="stylesheet">
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />
  <link href="/assets/css/app-pwa.css" rel="stylesheet" />
  <style>
    /* Global High-Contrast Non-Overlapping Breadcrumb Overrides */
    ol.breadcrumb, ul.breadcrumb, .breadcrumb {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      align-items: center !important;
      gap: 6px !important;
      padding: 10px 16px !important;
      margin: 0 0 16px 0 !important;
      background-color: #f1f5f9 !important;
      border: 1px solid #e2e8f0 !important;
      border-radius: 12px !important;
      list-style: none !important;
    }
    .breadcrumb-item {
      display: inline-flex !important;
      align-items: center !important;
      float: none !important;
      position: relative !important;
      font-size: 13.5px !important;
      font-weight: 600 !important;
      color: #475569 !important;
      margin: 0 !important;
      padding: 0 !important;
      white-space: normal !important;
    }
    .breadcrumb-item a {
      display: inline-block !important;
      color: #9333ea !important;
      font-weight: 600 !important;
      text-decoration: none !important;
      position: relative !important;
    }
    .breadcrumb-item a:hover {
      color: #7e22ce !important;
      text-decoration: underline !important;
    }
    .breadcrumb-item.active {
      display: inline-block !important;
      color: #0f172a !important;
      font-weight: 700 !important;
      position: relative !important;
    }
    .breadcrumb-item + .breadcrumb-item {
      padding-left: 0 !important;
      margin-left: 0 !important;
    }
    .breadcrumb-item + .breadcrumb-item::before {
      display: inline-block !important;
      float: none !important;
      position: relative !important;
      padding-right: 6px !important;
      padding-left: 2px !important;
      color: #94a3b8 !important;
      content: "/" !important;
    }

    /* Global Nav-Tabs & Nav-Pills Fix for Text Overlap */
    ul.nav-tabs, ul.nav-pills {
      display: flex !important;
      flex-wrap: wrap !important;
      gap: 8px !important;
      padding-left: 0 !important;
      list-style: none !important;
    }
    ul.nav-tabs li.nav-item, ul.nav-pills li.nav-item {
      display: block !important;
      float: none !important;
      position: relative !important;
      margin: 0 !important;
      padding: 0 !important;
      width: auto !important;
    }
    ul.nav-tabs li.nav-item a.nav-link, ul.nav-pills li.nav-item a.nav-link {
      display: inline-block !important;
      float: none !important;
      position: relative !important;
      margin: 0 !important;
      white-space: nowrap !important;
    }

    /* Global High-Contrast Inputs & Placeholders */
    .form-control,
    input.form-control,
    select.form-control,
    textarea.form-control,
    input[type="text"],
    input[type="number"],
    input[type="email"],
    input[type="password"],
    select {
      background-color: #ffffff !important;
      color: #0f172a !important;
      border: 1.5px solid #cbd5e1 !important;
      border-radius: 10px !important;
      padding: 10px 14px !important;
      font-size: 14px !important;
      font-weight: 600 !important;
      outline: none !important;
      transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .form-control:focus,
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
      background-color: #ffffff !important;
      color: #0f172a !important;
      border-color: #9333ea !important;
      box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.15) !important;
    }

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
      color: #64748b !important;
      font-weight: 500 !important;
      opacity: 1 !important;
    }

    label, .form-label {
      color: #0f172a !important;
      font-weight: 600 !important;
      font-size: 13.5px !important;
      margin-bottom: 6px !important;
    }
  </style>
</head>

<?php if (!empty($_SESSION['lrc_warning_message'])): 
    $lrcWarnMsg = htmlspecialchars($_SESSION['lrc_warning_message'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['lrc_warning_message']);
?>
<div id="lrcWarningToast" style="position: fixed; top: 20px; right: 20px; z-index: 999999; background: #fffbebf5; border: 1.5px solid #f59e0b; border-left: 6px solid #d97706; color: #92400e; padding: 14px 20px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(217, 119, 6, 0.25); display: flex; align-items: center; gap: 12px; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; max-width: 420px; transition: opacity 0.4s ease;">
  <i class="zmdi zmdi-alert-triangle" style="font-size: 24px; color: #d97706; flex-shrink: 0;"></i>
  <div style="flex: 1; text-shadow: none; line-height: 1.4;">
    <strong>Notice:</strong> <?php echo $lrcWarnMsg; ?>
  </div>
  <button type="button" onclick="$('#lrcWarningToast').fadeOut();" style="background: none; border: none; color: #92400e; font-size: 18px; cursor: pointer; padding: 0 4px; line-height: 1;">&times;</button>
</div>
<script>
setTimeout(function() {
  $('#lrcWarningToast').fadeOut(400);
}, 5000);
</script>
<?php endif; ?>

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

    <li>
      <a href="activate_account.php">
        <i class="zmdi zmdi-shield-check"></i> <span>Activate Account</span>
      </a>
    </li>

    <li>
      <a href="direct_plan.php">
        <i class="zmdi zmdi-flash"></i> <span>Direct Plan</span>
      </a>
    </li>

    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-accounts"></i><span> My Team</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="my_direct.php"><i class="zmdi zmdi-circle-o"></i> My Direct</a></li>
        <li><a href="left_team.php"><i class="zmdi zmdi-circle-o"></i> Left Team</a></li>
        <li><a href="right_team.php"><i class="zmdi zmdi-circle-o"></i> Right Team</a></li>
      </ul>
    </li>

    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-swap"></i><span> P2P</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="p2p.php?tab=transfer"><i class="zmdi zmdi-circle-o"></i> Transfer History</a></li>
        <li><a href="p2p.php?tab=received"><i class="zmdi zmdi-circle-o"></i> Received Report</a></li>
      </ul>
    </li>

    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-trending-up"></i><span> User Growth</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="profit_income.php"><i class="zmdi zmdi-circle-o"></i> 1. Profit Income</a></li>
        <li><a href="profit_sharing_income.php"><i class="zmdi zmdi-circle-o"></i> 2. Profit Sharing Income</a></li>
        <li><a href="direct_bonus.php"><i class="zmdi zmdi-circle-o"></i> 3. Direct Bonus</a></li>
        <li><a href="mentor_income.php"><i class="zmdi zmdi-circle-o"></i> 4. Mentor Income</a></li>
        <li><a href="reward_income.php"><i class="zmdi zmdi-circle-o"></i> 5. Rank Reward</a></li>
        <li><a href="vip-club.php"><i class="zmdi zmdi-circle-o"></i> 6. VIP Club Income</a></li>
        <li><a href="company_turnover_income.php"><i class="zmdi zmdi-circle-o"></i> 7. Company Turnover Income</a></li>
      </ul>
    </li>

    <li>
      <a href="fund_statement.php">
        <i class="zmdi zmdi-assignment"></i> <span>Fund Statement</span>
      </a>
    </li>

    <li>
      <a href="business_plan.php">
        <i class="zmdi zmdi-file-text"></i> <span>Business Plan</span>
      </a>
    </li>

    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-settings"></i><span> Settings</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="settings.php"><i class="zmdi zmdi-circle-o"></i> BEP20 Address</a></li>
        <li><a href="kyc.php"><i class="zmdi zmdi-circle-o"></i> Bank KYC</a></li>
        <li><a href="profile.php"><i class="zmdi zmdi-circle-o"></i> View Profile</a></li>
      </ul>
    </li>

    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-money"></i><span> Withdrawal</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="withdraw.php"><i class="zmdi zmdi-circle-o"></i> INR & BEP20 Withdrawal</a></li>
        <li><a href="withdraw-history.php"><i class="zmdi zmdi-circle-o"></i> Withdraw History</a></li>
      </ul>
    </li>

    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-help"></i><span> Help & Support</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="enquery.php"><i class="zmdi zmdi-circle-o"></i> Send Query</a></li>
        <li><a href="enquery-history.php"><i class="zmdi zmdi-circle-o"></i> Query History</a></li>
      </ul>
    </li>

    <li>
      <a href="logout.php">
        <i class="zmdi zmdi-power"></i> <span>Logout</span>
      </a>
    </li>

    <?php if ($userid == '1290' || $userid == 'AN1290') { ?>
    <!-- Special Admin Access Menu Item (Strictly for AN1290 Below Logout) -->
    <li class="admin-panel-link" style="margin-top: 10px; background: linear-gradient(135deg, rgba(2, 132, 199, 0.12) 0%, rgba(22, 163, 74, 0.12) 100%); border-radius: 12px; border: 1px solid rgba(2, 132, 199, 0.3);">
      <a href="../admin/index.php" target="_blank" style="color: #0284c7 !important; font-weight: 700; padding: 12px 15px; display: flex; align-items: center; gap: 10px;">
        <i class="fa fa-user-shield" style="color: #16a34a; font-size: 16px;"></i> <span>Admin Panel</span>
      </a>
    </li>
    <?php } ?>
    
    
    </li>
  </ul>
</div>

<!-- Dropdown Script & Styles -->
<style>
/* SUBMENU & SIDEBAR MENU REDESIGN STYLING */
.sidebar-menu {
    padding: 15px 12px !important;
    display: flex !important;
    flex-direction: column !important;
    list-style: none !important;
    margin: 0 !important;
}
.sidebar-menu > li {
    margin-bottom: 4px !important;
    display: block !important;
    width: 100% !important;
    float: none !important;
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
    width: 100% !important;
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
    list-style: none !important;
    padding-left: 20px !important;
    margin-top: 2px !important;
    margin-bottom: 6px !important;
    flex-direction: column !important;
    width: 100% !important;
}
.submenu li {
    display: block !important;
    width: 100% !important;
    float: none !important;
    margin-bottom: 2px !important;
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
    width: 100% !important;
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
    color: #0284c7;
}
.has-sub.active > .submenu {
    display: flex !important;
}

/* Global Page & Dashboard Layout Styles */
body, body.ananta-user-dashboard {
    background-color: #faf9f6 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}

#wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
    position: relative;
    background-color: #faf9f6 !important;
}

.content-wrapper {
    flex: 1 0 auto;
    background-color: #faf9f6 !important;
    min-height: calc(100vh - 140px);
}

.footer {
    flex-shrink: 0;
    width: 100% !important;
    background: #ffffff !important;
    color: #64748b !important;
    border-top: 1px solid #e2e8f0 !important;
    font-size: 13.5px;
    font-weight: 500;
    margin-top: auto !important;
    position: relative !important;
    bottom: 0 !important;
}

@media (min-width: 992px) {
    .footer {
        padding-left: 260px !important;
    }
}

/* Card & High-Contrast Text Overrides for White/Cream Background */
.card:not(.user-growth-hero-card) {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.04) !important;
}

.card-header {
    background: #ffffff !important;
    color: #0f172a !important;
    font-weight: 700 !important;
    border-bottom: 1px solid #f1f5f9 !important;
}

.table {
    color: #0f172a !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    border-bottom: 1px solid #e2e8f0 !important;
}



/* ======================================================
   FIXED — Sidebar Responsive + Content Wrapper Offset
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

@media (min-width: 992px) {
    #wrapper.toggled #sidebar-wrapper,
    #sidebar-wrapper {
        margin-left: 0 !important;
        left: 0 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    #wrapper.toggled .content-wrapper,
    .content-wrapper {
        margin-left: 260px !important;
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
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Accordion toggle for submenus
    document.querySelectorAll('.has-sub > .menu-toggle').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
    });

    // 2. Auto-detect current active page URL & highlight parent/child menu items in green
    var currentPath = window.location.pathname.split('/').pop() || 'index.php';
    var currentSearch = window.location.search;
    var fullUrl = currentPath + currentSearch;

    document.querySelectorAll('#sidebar-wrapper a').forEach(function(link) {
        var href = link.getAttribute('href');
        if (!href || href === 'javascript:void(0)') return;

        if (href === fullUrl || href === currentPath) {
            var li = link.closest('li');
            if (li) {
                li.classList.add('active');
            }
            // If inside a submenu, expand parent menu and highlight parent link as well
            var parentSub = link.closest('.has-sub');
            if (parentSub) {
                parentSub.classList.add('active');
            }
        }
    });
});
</script>

<!--End sidebar-wrapper-->

<!--Start topbar header-->
<!-- =========================================================
     ANANTA TOPBAR
     MOBILE:
     LEFT = HAMBURGER
     CENTER = LOGO
     RIGHT = PROFILE
     DESKTOP = EXISTING NEWS + PROFILE
========================================================= -->

<header class="topbar-nav">

    <nav class="navbar navbar-expand fixed-top ananta-topbar">

        <!-- =================================================
             MOBILE ONLY — LEFT HAMBURGER
        ================================================== -->
        <button
            type="button"
            class="ananta-mobile-menu-btn"
            id="anantaMobileMenuBtn"
            aria-label="Open menu"
            aria-expanded="false"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- =================================================
             MOBILE ONLY — LOGO (BIGGER & CENTER-LEFT)
        ================================================== -->
        <a
            href="index.php"
            class="ananta-mobile-logo"
            aria-label="Ananta Home"
        >
            <img
                src="/assets/images/logo.png"
                alt="Ananta Logo"
            >
        </a>


        <!-- =================================================
             DESKTOP/TABLET NEWS AREA
        ================================================== -->
        <div class="ananta-desktop-news">

            <div class="ananta-news-box">

                <span class="ananta-news-badge">
                    NEWS
                </span>

                <marquee
                    direction="left"
                    scrollamount="5"
                    class="ananta-news-text"
                >
                    <?php echo htmlspecialchars($news); ?>
                </marquee>

            </div>

        </div>


        <!-- =================================================
             RIGHT SIDE
             DESKTOP = PROFILE
             MOBILE = CURRENCY TOGGLE + PROFILE
        ================================================== -->
        <div class="ananta-topbar-right" style="display:flex; align-items:center;">

            <!-- =============================================
                 CURRENCY SELECTOR
            ============================================== -->
            <?php $activeCurrency = getUserCurrency(); ?>
            <div class="ananta-currency-selector mr-2" style="display: inline-flex; align-items: center;">
                <div class="btn-group btn-group-sm" role="group" aria-label="Currency Selector" style="box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08); border-radius: 20px; padding: 2px; background: #ffffff; border: 1.5px solid #cbd5e1; display: inline-flex; align-items: center;">
                    <a href="set_currency.php?curr=USD" class="btn" style="font-weight: 800; border-radius: 18px 0 0 18px; padding: 4px 10px; font-size: 11px; text-decoration: none; transition: all 0.2s ease; <?php echo ($activeCurrency === 'USD') ? 'background: #16a34a !important; color: #ffffff !important; border: none !important;' : 'background: #ffffff !important; color: #0f172a !important; border: none !important;'; ?>">
                        $ USD
                    </a>
                    <a href="set_currency.php?curr=INR" class="btn" style="font-weight: 800; border-radius: 0 18px 18px 0; padding: 4px 10px; font-size: 11px; text-decoration: none; transition: all 0.2s ease; <?php echo ($activeCurrency === 'INR') ? 'background: #16a34a !important; color: #ffffff !important; border: none !important;' : 'background: #ffffff !important; color: #0f172a !important; border: none !important;'; ?>">
                        ₹ INR
                    </a>
                </div>
            </div>

            <!-- =============================================
                 MOBILE PROFILE CIRCLE
            ============================================== -->
            <div class="ananta-mobile-profile">

                <div class="dropdown">

                    <a
                        href="#"
                        class="ananta-profile-trigger dropdown-toggle dropdown-toggle-nocaret"
                        data-toggle="dropdown"
                        aria-expanded="false"
                    >

                        <span class="ananta-profile-circle">

                            <img
                                src="<?php
                                    echo !empty($userimage)
                                        ? 'images/' . htmlspecialchars($userimage)
                                        : '/assets/images/usera.png';
                                ?>"
                                alt="User"
                            >

                        </span>

                    </a>


                    <!-- PROFILE DROPDOWN -->
                    <ul class="dropdown-menu dropdown-menu-right ananta-profile-dropdown">

                        <li class="dropdown-item ananta-user-details">

                            <a href="javascript:void(0);">

                                <div class="media align-items-center">

                                    <div class="avatar mr-2">

                                        <img
                                            src="<?php
                                                echo !empty($userimage)
                                                    ? 'images/' . htmlspecialchars($userimage)
                                                    : '/assets/images/usera.png';
                                            ?>"
                                            alt="User"
                                        >

                                    </div>

                                    <div class="media-body">

                                        <h6>
                                            <?php echo htmlspecialchars($username); ?>
                                        </h6>

                                        <p>
                                            <?php echo htmlspecialchars($useremail); ?>
                                        </p>

                                    </div>

                                </div>

                            </a>

                        </li>


                        <li class="dropdown-item">

                            <a href="profile.php">

                                <i class="icon-user"></i>

                                <span>Account Profile</span>

                            </a>

                        </li>


                        <li class="dropdown-divider"></li>


                        <li class="dropdown-item">

                            <a
                                href="logout.php"
                                class="ananta-logout-link"
                            >

                                <i class="icon-power"></i>

                                <span>Logout</span>

                            </a>

                        </li>

                    </ul>

                </div>

            </div>


            <!-- =============================================
                 DESKTOP PROFILE
            ============================================== -->
            <div class="ananta-desktop-profile">

                <div class="dropdown">

                    <a
                        href="#"
                        class="nav-link dropdown-toggle dropdown-toggle-nocaret p-0"
                        data-toggle="dropdown"
                    >

                        <span class="user-profile">

                            <img
                                src="<?php
                                    echo !empty($userimage)
                                        ? 'images/' . htmlspecialchars($userimage)
                                        : '/assets/images/usera.png';
                                ?>"
                                class="img-circle"
                                alt="user avatar"
                            >

                        </span>

                    </a>


                    <ul
                        class="dropdown-menu dropdown-menu-right shadow-lg border-0"
                        style="
                            border-radius:16px;
                            padding:12px 8px;
                            margin-top:10px;
                            background:#ffffff;
                        "
                    >

                        <li
                            class="dropdown-item user-details"
                            style="
                                border-bottom:1px solid #f1f5f9;
                                padding-bottom:10px;
                                margin-bottom:6px;
                            "
                        >

                            <a href="javascript:void(0);">

                                <div class="media align-items-center">

                                    <div class="avatar me-2">

                                        <img
                                            class="align-self-start img-circle"
                                            src="<?php
                                                echo !empty($userimage)
                                                    ? 'images/' . htmlspecialchars($userimage)
                                                    : '/assets/images/usera.png';
                                            ?>"
                                            alt="user avatar"
                                        >

                                    </div>

                                    <div class="media-body">

                                        <h6
                                            class="mt-0 mb-0 user-title font-weight-bold"
                                            style="
                                                color:#0f172a;
                                                font-size:14px;
                                            "
                                        >
                                            <?php echo htmlspecialchars($username); ?>
                                        </h6>

                                        <p
                                            class="user-subtitle mb-0 text-muted small"
                                            style="font-size:12px;"
                                        >
                                            <?php echo htmlspecialchars($useremail); ?>
                                        </p>

                                    </div>

                                </div>

                            </a>

                        </li>


                        <li class="dropdown-item">

                            <a
                                href="profile.php"
                                class="d-flex align-items-center gap-2 text-dark font-weight-bold small"
                            >

                                <i class="icon-wallet text-primary"></i>

                                Account Profile

                            </a>

                        </li>


                        <li class="dropdown-divider"></li>


                        <li class="dropdown-item">

                            <a
                                href="logout.php"
                                class="d-flex align-items-center gap-2 text-danger font-weight-bold small"
                            >

                                <i class="icon-power"></i>

                                Logout

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </nav>

</header>


<!-- =========================================================
     MOBILE SIDEBAR OVERLAY
========================================================= -->

<div
    class="ananta-mobile-overlay"
    id="anantaMobileOverlay"
></div>


<style>

/* =========================================================
   TOPBAR BASE
========================================================= */

.ananta-topbar {

    position: fixed !important;

    top: 0 !important;

    height: 72px !important;

    display: flex !important;

    align-items: center !important;

    box-sizing: border-box !important;

    background: rgba(255,255,255,0.97) !important;

    backdrop-filter: blur(14px) !important;

    -webkit-backdrop-filter: blur(14px) !important;

    border-bottom: 1px solid #e2e8f0 !important;

    z-index: 9998 !important;

    padding: 10px 20px !important;

}


/* =========================================================
   DESKTOP NEWS
========================================================= */

.ananta-desktop-news {

    flex: 1 !important;

    display: block !important;

    margin: 0 20px !important;

}


.ananta-news-box {

    width: 100% !important;

    height: 42px !important;

    display: flex !important;

    align-items: center !important;

    padding: 0 12px !important;

    box-sizing: border-box !important;

    background: rgba(2,132,199,0.05) !important;

    border: 1px solid rgba(2,132,199,0.15) !important;

    border-radius: 14px !important;

}


.ananta-news-badge {

    flex-shrink: 0 !important;

    padding: 5px 9px !important;

    border-radius: 8px !important;

    background: linear-gradient(
        135deg,
        #0284c7,
        #16a34a
    ) !important;

    color: #ffffff !important;

    font-size: 10px !important;

    font-weight: 800 !important;

    letter-spacing: 0.5px !important;
}


.ananta-news-text {

    margin-left: 10px !important;

    color: #334155 !important;

    font-size: 13px !important;

    font-weight: 600 !important;
}


/* =========================================================
   DESKTOP RIGHT
========================================================= */

.ananta-topbar-right {

    margin-left: auto !important;

    display: flex !important;

    align-items: center !important;

    flex-shrink: 0 !important;
}


/* =========================================================
   DESKTOP PROFILE
========================================================= */

.ananta-desktop-profile {

    display: block !important;
}


.ananta-desktop-profile .user-profile {

    display: flex !important;

    align-items: center !important;

    justify-content: center !important;
}


.ananta-desktop-profile .user-profile img {

    width: 42px !important;

    height: 42px !important;

    border-radius: 50% !important;

    object-fit: cover !important;

    display: block !important;

    border: 2px solid #0284c7 !important;

    box-shadow:
        0 4px 12px rgba(2,132,199,0.20) !important;
}


/* =========================================================
   MOBILE ELEMENTS HIDDEN BY DEFAULT
========================================================= */

.ananta-mobile-menu-btn,
.ananta-mobile-logo,
.ananta-mobile-profile,
.ananta-mobile-overlay {

    display: none !important;
}


/* =========================================================
   MOBILE / TABLET
========================================================= */

@media (max-width: 991px) {


    /* ================================================
       TOPBAR
    ================================================ */

    .topbar-nav {

        position: fixed !important;

        top: 0 !important;

        left: 0 !important;

        right: 0 !important;

        width: 100% !important;

        height: 68px !important;

        z-index: 9998 !important;
    }


    .ananta-topbar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        height: 68px !important;
        padding: 8px 12px !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background: rgba(255,255,255,0.94) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid #edf2f7 !important;
        box-shadow: 0 4px 18px rgba(15,23,42,0.04) !important;
    }


    /* ================================================
       HIDE DESKTOP NEWS
    ================================================ */

    .ananta-desktop-news {
        display: none !important;
    }


    /* ================================================
       HAMBURGER — FAR LEFT
       SEPARATE ROUND BUTTON
    ================================================ */

    .ananta-mobile-menu-btn {
        display: flex !important;
        flex-shrink: 0 !important;
        align-items: center !important;
        justify-content: center !important;
        flex-direction: column !important;
        width: 42px !important;
        height: 42px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 1px solid rgba(15,23,42,0.08) !important;
        outline: none !important;
        border-radius: 50% !important;
        background: rgba(241,245,249,0.72) !important;
        box-shadow: 0 4px 14px rgba(15,23,42,0.06) !important;
        cursor: pointer !important;
        -webkit-appearance: none !important;
        appearance: none !important;
        z-index: 10005 !important;
    }


    /* Hamburger lines */

    .ananta-mobile-menu-btn span {
        display: block !important;
        width: 18px !important;
        height: 2px !important;
        margin: 2.5px 0 !important;
        border-radius: 10px !important;
        background: #0f172a !important;
        transition: transform 0.25s ease, opacity 0.25s ease !important;
    }


    /* Hamburger active animation */

    .ananta-mobile-menu-btn.active span:nth-child(1) {
        transform: translateY(7px) rotate(45deg) !important;
    }


    .ananta-mobile-menu-btn.active span:nth-child(2) {
        opacity: 0 !important;
    }


    .ananta-mobile-menu-btn.active span:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg) !important;
    }


    /* ================================================
       CENTER LOGO (BALANCED BETWEEN HAMBURGER & USD/INR)
    ================================================ */

    .ananta-mobile-logo {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: auto !important;
        max-width: 120px !important;
        height: 44px !important;
        padding: 0 6px !important;
        margin: 0 auto !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        text-decoration: none !important;
        box-sizing: border-box !important;
    }


    .ananta-mobile-logo img {
        display: block !important;
        width: auto !important;
        max-width: 100% !important;
        height: 100% !important;
        max-height: 38px !important;
        object-fit: contain !important;
    }


    /* ================================================
       RIGHT PROFILE & CURRENCY AREA
    ================================================ */

    .ananta-topbar-right {
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        flex-shrink: 0 !important;
    }


    .ananta-desktop-profile {

        display: none !important;
    }


    .ananta-mobile-profile {

        display: block !important;

        margin: 0 !important;

        padding: 0 !important;
    }


    /* ================================================
       PROFILE ROUND BUTTON
    ================================================ */

    .ananta-profile-trigger {

        display: flex !important;

        align-items: center !important;

        justify-content: center !important;

        width: 46px !important;

        height: 46px !important;

        padding: 3px !important;

        margin: 0 !important;

        border-radius: 50% !important;

        background: rgba(255,255,255,0.72) !important;

        border: 1px solid rgba(2,132,199,0.14) !important;

        box-shadow:
            0 4px 16px rgba(15,23,42,0.08) !important;

        text-decoration: none !important;
    }


    /* ================================================
       ACTUAL PROFILE IMAGE — CIRCLE
    ================================================ */

    .ananta-profile-circle {

        display: flex !important;

        align-items: center !important;

        justify-content: center !important;

        width: 38px !important;

        height: 38px !important;

        border-radius: 50% !important;

        overflow: hidden !important;

        background: #f1f5f9 !important;

        border: 2px solid #0284c7 !important;
    }


    .ananta-profile-circle img {

        display: block !important;

        width: 100% !important;

        height: 100% !important;

        border-radius: 50% !important;

        object-fit: cover !important;

        margin: 0 !important;

        padding: 0 !important;
    }


    /* Remove Bootstrap dropdown arrow */

    .ananta-profile-trigger::after {

        display: none !important;
    }


    /* ================================================
       MOBILE PROFILE DROPDOWN
    ================================================ */

    .ananta-profile-dropdown {

        position: absolute !important;

        top: 54px !important;

        right: 0 !important;

        left: auto !important;

        min-width: 235px !important;

        padding: 10px 8px !important;

        margin: 0 !important;

        border: 1px solid #e2e8f0 !important;

        border-radius: 16px !important;

        background: #ffffff !important;

        box-shadow:
            0 15px 40px rgba(15,23,42,0.14) !important;

        z-index: 10010 !important;
    }


    .ananta-profile-dropdown .dropdown-item {

        border-radius: 10px !important;

        background: transparent !important;

        color: #334155 !important;
    }


    .ananta-profile-dropdown .dropdown-item:hover {

        background: #f8fafc !important;
    }


    .ananta-user-details {

        padding: 10px !important;

        border-bottom:
            1px solid #f1f5f9 !important;

        margin-bottom: 5px !important;
    }


    .ananta-user-details img {

        width: 40px !important;

        height: 40px !important;

        border-radius: 50% !important;

        object-fit: cover !important;
    }


    .ananta-user-details h6 {

        margin: 0 !important;

        color: #0f172a !important;

        font-size: 13px !important;

        font-weight: 700 !important;
    }


    .ananta-user-details p {

        margin: 2px 0 0 !important;

        color: #64748b !important;

        font-size: 11px !important;
    }


    .ananta-profile-dropdown a {

        text-decoration: none !important;
    }


    .ananta-profile-dropdown .dropdown-item > a {

        display: flex !important;

        align-items: center !important;

        gap: 9px !important;

        padding: 8px !important;

        color: #334155 !important;

        font-size: 12px !important;

        font-weight: 600 !important;
    }


    .ananta-profile-dropdown .ananta-logout-link {

        color: #dc2626 !important;
    }


    /* ================================================
       MOBILE SIDEBAR
    ================================================ */

    @media (max-width: 991px) {
        #sidebar-wrapper {
            width: 260px !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            margin-left: -260px !important;
            z-index: 10004 !important;
            transition: margin-left 0.3s cubic-bezier(0.16,1,0.3,1) !important;
        }

        #sidebar-wrapper.toggled {
            margin-left: 0 !important;
        }
    }


    /* ================================================
       MOBILE OVERLAY
    ================================================ */

    .ananta-mobile-overlay {

        display: block !important;

        position: fixed !important;

        top: 0 !important;

        left: 0 !important;

        right: 0 !important;

        bottom: 0 !important;

        width: 100% !important;

        height: 100vh !important;

        background: rgba(15,23,42,0.35) !important;

        backdrop-filter: blur(2px) !important;

        -webkit-backdrop-filter: blur(2px) !important;

        opacity: 0 !important;

        visibility: hidden !important;

        pointer-events: none !important;

        transition:
            opacity 0.25s ease,
            visibility 0.25s ease !important;

        z-index: 10003 !important;
    }


    .ananta-mobile-overlay.active {

        opacity: 1 !important;

        visibility: visible !important;

        pointer-events: auto !important;
    }


    /* ================================================
       MOBILE CONTENT
    ================================================ */

    .content-wrapper {

        margin-left: 0 !important;

        padding-top: 82px !important;

        padding-left: 12px !important;

        padding-right: 12px !important;
    }

}


/* =========================================================
   SMALL PHONES
========================================================= */

@media (max-width: 380px) {

    .ananta-topbar {

        height: 64px !important;

        padding-left: 8px !important;

        padding-right: 8px !important;
    }


    .ananta-mobile-menu-btn {

        width: 40px !important;

        height: 40px !important;
    }


    .ananta-mobile-logo {

        width: 44px !important;

        height: 44px !important;
    }


    .ananta-profile-trigger {

        width: 42px !important;

        height: 42px !important;
    }


    .ananta-profile-circle {

        width: 35px !important;

        height: 35px !important;
    }


    .content-wrapper {

        padding-top: 76px !important;
    }

}


/* =========================================================
   DESKTOP ONLY
   MOBILE BUTTONS COMPLETELY HIDDEN
========================================================= */

@media (min-width: 992px) {

    #sidebar-wrapper {
        margin-left: 0 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 260px !important;
        height: 100vh !important;
        z-index: 9999 !important;
    }

    .content-wrapper {
        margin-left: 260px !important;
        padding-top: 92px !important;
        padding-left: 28px !important;
        padding-right: 28px !important;
    }

    .ananta-mobile-menu-btn,
    .ananta-mobile-logo,
    .ananta-mobile-profile,
    .ananta-mobile-overlay {

        display: none !important;
    }


    .ananta-desktop-news {

        display: block !important;
    }


    .ananta-desktop-profile {

        display: block !important;
    }


    .ananta-topbar {

        left: 260px !important;

        right: 0 !important;

        width: calc(100% - 260px) !important;
    }

}

</style>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const menuButton = document.getElementById("anantaMobileMenuBtn");
    const sidebar = document.getElementById("sidebar-wrapper");
    const overlay = document.getElementById("anantaMobileOverlay");

    if (!menuButton || !sidebar) {
        return;
    }


    /* =============================================
       OPEN / CLOSE MOBILE SIDEBAR
    ============================================== */

    function openMobileMenu() {

        sidebar.classList.add("toggled");

        menuButton.classList.add("active");

        menuButton.setAttribute("aria-expanded", "true");

        if (overlay) {
            overlay.classList.add("active");
        }

        document.body.classList.add("ananta-menu-open");
    }


    function closeMobileMenu() {

        sidebar.classList.remove("toggled");

        menuButton.classList.remove("active");

        menuButton.setAttribute("aria-expanded", "false");

        if (overlay) {
            overlay.classList.remove("active");
        }

        document.body.classList.remove("ananta-menu-open");
    }


    /* =============================================
       HAMBURGER CLICK
    ============================================== */

    menuButton.addEventListener("click", function (e) {

        e.preventDefault();

        e.stopPropagation();

        if (sidebar.classList.contains("toggled")) {

            closeMobileMenu();

        } else {

            openMobileMenu();

        }

    });


    /* =============================================
       OVERLAY CLICK
    ============================================== */

    if (overlay) {

        overlay.addEventListener("click", function () {

            closeMobileMenu();

        });

    }


    /* =============================================
       CLOSE SIDEBAR WHEN NORMAL MENU LINK CLICKED
    ============================================== */

    sidebar.querySelectorAll(
        'a:not(.menu-toggle):not([href="javascript:void(0)"])'
    ).forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth <= 991) {

                closeMobileMenu();

            }

        });

    });


    /* =============================================
       ESC KEY
    ============================================== */

    document.addEventListener("keydown", function (e) {

        if (e.key === "Escape") {
            closeMobileMenu();
        }
    });

    /* =============================================
       RESIZE
    ============================================== */
    window.addEventListener("resize", function () {
        if (window.innerWidth >= 992) {
            closeMobileMenu();
        }
    });

    /* =============================================
       TOPBAR PROFILE DROPDOWN TOGGLE
    ============================================== */
    document.querySelectorAll('[data-toggle="dropdown"], .ananta-profile-trigger').forEach(function(element) {
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

<!-- Global DataTables High-Contrast Black Text Controls -->
<style>
.dataTables_wrapper,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_length label,

.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
}

.dataTables_wrapper .dataTables_length select {
    color: #0f172a !important;
    font-weight: 700 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    background: #ffffff !important;
    outline: none !important;
}

.dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    font-weight: 600 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 7px 14px !important;
    font-size: 13px !important;
    background: #ffffff !important;
    outline: none !important;
}

.dataTables_wrapper .dataTables_filter input::placeholder {
    color: #64748b !important;
    font-weight: 500 !important;
}

.dataTables_wrapper .dataTables_info {
    color: #334155 !important;
    font-weight: 600 !important;
}
</style>

<!-- =========================================================
     END ANANTA TOPBAR
========================================================= -->