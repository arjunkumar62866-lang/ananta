<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// session_start();
if (!isset($_SESSION["userid"])) { 
    header("Location:login.php");
    exit();
}

require 'common/connection.php';
// require 'common/printmessage.php';
require 'common/db_method.php';
// require 'common/password.php';
// require 'common/recharge_api.php';

// Set userid from GET or Session
if (isset($_GET['uid'])) {
    $_SESSION['userid'] = $_GET['uid']; 
    $userid = $_SESSION['userid']; 
} else {
    $userid = $_SESSION['userid'];  
}

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
$useramount      = round((double)$useramount, 2);
$usertotal_package=$rowheader['total_package'];
$pin_wallet=$rowheader['pin_wallet'];
$idactive=$rowheader['active'];
$kyc=$rowheader['kyc'];
$pending_geninc = $rowheader['pending_geninc'];



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
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />
</head>

<!--Start sidebar-wrapper-->
<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper">
  <div class="brand-logo">
    <a href="index.php">
      <img src="<?php echo $hmlogo;?>" class="logo-icon" alt="logo icon" height="80px" width="30px" margin-top="5px">
      <!--<h5 class="logo-text"><?php echo $hmtitle; ?></h5>-->
    </a>
  </div>
  <ul class="sidebar-menu do-nicescrol">

    <li>
      <a href="index.php">
        <i class="zmdi zmdi-home"></i> <span>Dashboard</span>
      </a>
    </li>

    <!-- Profile Tools with Submenu -->
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-edit"></i> Profile Tools</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="profile.php"><i class="zmdi zmdi-circle-o"></i> Update Profile</a></li>
        <li><a href="kyc.php"><i class="zmdi zmdi-circle-o"></i> Update KYC</a></li>
        <li><a href="update-password.php"><i class="zmdi zmdi-circle-o"></i> Change Password</a></li>
        <!--<li><a href="#"><i class="zmdi zmdi-circle-o"></i> Wallet Address</a></li>-->
        <li><a href="profile.php"><i class="zmdi zmdi-circle-o"></i> View Profile</a></li>
        <!--<li><a href="#"><i class="zmdi zmdi-circle-o"></i> Welcome Letter</a></li>-->
      </ul>
    </li>
    
    <li>
      <a href="without_guarantee.php">
        <i class="zmdi zmdi-alert-circle-o"></i> <span>Activate Without Guarantee Program</span>
      </a>
    </li>
      <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi zmdi-accounts"></i> My Team</span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="left_team.php"><i class="zmdi zmdi-circle-o"></i> Left Team </a></li>
        <li><a href="right_team.php"><i class="zmdi zmdi-circle-o"></i> Right Team </a></li>
        <!--<li><a href="tree.php"><i class="zmdi zmdi-circle-o"></i> Tree View </a></li>-->
      </ul>
    </li>
    
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-trending-up"></i><span> Incomes</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="generation_income.php"><i class="zmdi zmdi-circle-o"></i> Generation Income</a></li>
        <li><a href="direct_income.php"><i class="zmdi zmdi-circle-o"></i> Direct Income</a></li>
        <!--<li><a href="kyc.php"><i class="zmdi zmdi-circle-o"></i> Daily Profit Sharing Income</a></li>-->
        <li><a href="direct_bonus.php"><i class="zmdi zmdi-circle-o"></i> Direct Bonus 10M</a></li>
        <li><a href="profit_sharing_income.php"><i class="zmdi zmdi-circle-o"></i> Profit Sharing Income</a></li>
        <li><a href="ranking_income.php"><i class="zmdi zmdi-circle-o"></i> Ranking Income</a></li>
        <li><a href="leadership_income.php"><i class="zmdi zmdi-circle-o"></i> Leadership Bonus</a></li>
        <li><a href="reward_income.php"><i class="zmdi zmdi-circle-o"></i> Rank and Rewards</a></li>
        <!--<li><a href="#"><i class="zmdi zmdi-circle-o"></i> Reward Income</a></li>-->
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
        <span><i class="zmdi zmdi-balance-wallet"></i><span> Fund Deposit</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="fund-request.php"><i class="zmdi zmdi-circle-o"></i> Request Fund</a></li>
        <li><a href="request-history.php"><i class="zmdi zmdi-circle-o"></i> Request Summary</a></li>
      </ul>
    </li>
    
    <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-money"></i><span> Withdrawal</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="withdraw.php"><i class="zmdi zmdi-circle-o"></i> Wallet Withdrawal</a></li>
        <li><a href="nonworking1-withdraw1.php"><i class="zmdi zmdi-circle-o"></i> Investment Withdrawal</a></li>
        <li><a href="withdraw-history.php"><i class="zmdi zmdi-circle-o"></i> Withdraw History</a></li>
      </ul>
    </li>
    <li>
      <a href="user-activatebyfund.php">
        <i class="zmdi zmdi-account-add"></i> <span>Activate Other User ID</span>
      </a>
    </li>

   <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-account-box"></i><span> My Account</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <!--<li><a href="user-activatebyfund.php"><i class="zmdi zmdi-circle-o"></i> Activate Other User ID</a></li>-->
        <li><a href="package_buy.php"><i class="zmdi zmdi-circle-o"></i> Investment</a></li>
        <li><a href="my_investments.php"><i class="zmdi zmdi-circle-o"></i> My Investments</a></li>
      </ul>
    </li>
    
    
   <li class="has-sub">
      <a href="javascript:void(0)" class="menu-toggle">
        <span><i class="zmdi zmdi-help"></i><span> Help & Support</span></span>
        <i class="zmdi zmdi-chevron-down arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a href="enquery.php"><i class="zmdi zmdi-circle-o"></i> Send Enquery</a></li>
        <li><a href="enquery-history.php"><i class="zmdi zmdi-circle-o"></i> Enquery History</a></li>
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
.arrow-icon {
    transition: transform 0.3s ease;
    transform: rotate(90deg);
}
.has-sub.active .arrow-icon {
    transform: rotate(0deg);
}
.has-sub.active > .submenu {
    display: block;
}

/* ======================================================
   FIXED — Sidebar Responsive + Scroll for PC & Mobile
   ====================================================== */
#sidebar-wrapper {
    width: 250px;
    position: fixed;
    top: 60px;
    left: 0;
    height: calc(100vh - 60px);
    /*background: bg-theme1;*/
    background: black;
    z-index: 9999;
    overflow-y: auto;          /* MAIN FIX */
    overflow-x: hidden;
    transition: margin-left 0.3s ease;
}

/* Desktop: Sidebar visible */
@media (min-width: 768px) {
    #sidebar-wrapper {
        margin-left: 0;
    }
    #sidebar-wrapper.toggled {
        margin-left: -250px;
    }
}

/* Mobile: Sidebar hidden by default */
@media (max-width: 767px) {
    #sidebar-wrapper {
        margin-left: -250px;
    }
    #sidebar-wrapper.toggled {
        margin-left: 0;
    }
}

/* Scrollbar styling (optional) */
#sidebar-wrapper::-webkit-scrollbar {
    width: 6px;
}
#sidebar-wrapper::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}
#sidebar-wrapper::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Ensure navbar stays above sidebar */
.topbar-nav {
    position: relative;
    z-index: 10000;
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

<!--End sidebar-wrapper-->

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
      <li class="nav-item dropdown-lg ">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
          <span class="user-profile">
            <?php echo $hmpre.$userid;?>
          </span>
        </a>
      </li>
      <li class="nav-item dropdown-lg d-block">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
          <span class="user-profile">
            <?php echo $username;?>
          </span>
        </a>
      </li>
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
            <img src="images/<?php echo $userimage; ?>" class="img-circle" alt="user avatar">
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="dropdown-item user-details">
            <a href="javaScript:void();">
              <div class="media">
                <div class="avatar">
                  <img class="align-self-start mr-3" src="images/<?php echo $userimage; ?>" alt="user avatar">
                </div>
                <div class="media-body">
                  <h6 class="mt-2 user-title"><?php echo $username; ?></h6>
                  <p class="user-subtitle"><?php echo $useremail; ?></p>
                </div>
              </div>
            </a>
          </li>
          <!--<li class="dropdown-divider"></li>-->
          <!--<li class="dropdown-item"><a href="inbox.php"><i class="icon-envelope mr-2"></i> Inbox</li>-->
          <li class="dropdown-divider"></li>
          <li class="dropdown-item"><a href="profile.php"><i class="icon-wallet mr-2"></i> Account</li>
          <!--<li class="dropdown-divider"></li>-->
          <!--<li class="dropdown-item"><i class="icon-settings mr-2"></i> Setting</li>-->
          <li class="dropdown-divider"></li>
          <li class="dropdown-item"><a href="logout.php"><i class="icon-power mr-2"></i> Logout</a></li>
        </ul>
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
