<?php
$hmlogo="assets/images/logo.png";
$hmfavicon="assets/images/logo.png";
$hmtitle="Ananta";
$hmlogin="/dashboard/user1/login.php";
$hmregister="/dashboard/user1/new_binary_registration_form.php";
$hmmobile="";
$hmemail="anantamultitread@gmail.com";
$hmaddress="";
$hmabout="about.php";
$hmcontact="contact.php";
$hmhome="index.php";
$hmservice="service-details.php";
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from azim.hostlin.com/Fxzone/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 03 Dec 2025 11:20:44 GMT -->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

<title><?php echo $hmtitle; ?></title>

<!-- Fav Icon -->
<link rel="icon" href="<?php echo $hmfavicon; ?>" type="image/x-icon">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Funnel+Display:wght@300..800&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&amp;display=swap" rel="stylesheet">

<!-- Stylesheets -->
<link href="assets/css/font-awesome-all.css" rel="stylesheet">
<link href="assets/css/flaticon.css" rel="stylesheet">
<link href="assets/css/owl.css" rel="stylesheet">
<link href="assets/css/bootstrap.css" rel="stylesheet">
<link href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
<link href="assets/css/animate.css" rel="stylesheet">
<link href="assets/css/nice-select.css" rel="stylesheet">
<link href="assets/css/color.css" rel="stylesheet">
<link href="assets/css/elpath.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">

</head>


<!-- page wrapper -->
<body>

    <div class="boxed_wrapper">


        <!-- preloader -->
        <!--<div class="loader-wrap">-->
        <!--    <div class="preloader">-->
        <!--        <div id="handle-preloader" class="handle-preloader">-->
        <!--            <div class="animation-preloader">-->
        <!--                <div class="spinner"></div>-->
        <!--                <div class="txt-loading">-->
        <!--                    <span data-text-preloader="f" class="letters-loading">-->
        <!--                        f-->
        <!--                    </span>-->
        <!--                    <span data-text-preloader="x" class="letters-loading">-->
        <!--                        x-->
        <!--                    </span>-->
        <!--                    <span data-text-preloader="z" class="letters-loading">-->
        <!--                        z-->
        <!--                    </span>-->
        <!--                    <span data-text-preloader="o" class="letters-loading">-->
        <!--                        o-->
        <!--                    </span>-->
        <!--                    <span data-text-preloader="n" class="letters-loading">-->
        <!--                        n-->
        <!--                    </span>-->
        <!--                    <span data-text-preloader="e" class="letters-loading">-->
        <!--                        e-->
        <!--                    </span>-->
        <!--                </div>-->
        <!--            </div>  -->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <!-- preloader end -->


        <!--Search Popup-->
        <div id="search-popup" class="search-popup">
            <div class="popup-inner">
                <div class="upper-box">
                    <div class="close-search"><i class="flaticon-close"></i></div>
                </div>
                <div class="overlay-layer"></div>
                <div class="auto-container">
                    <div class="search-form">
                        <form method="post" action="https://azim.hostlin.com/Fxzone/index-5.html">
                            <div class="form-group">
                                <input type="search" name="search-field" placeholder="Type your keyword and hit" required >
                                <button type="submit"><i class="flaticon-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="chat-icon"><button type="button" class="chat-toggler"><i class="flaticon-chat"></i></button></div>

        <!--chat popup-->
        <div id="chat-popup" class="chat-popup">
            <div class="popup-inner">
                <div class="close-chat"><i class="flaticon-close"></i></div>
                <div class="chat-form">
                    <p>Please fill out the form below and we will get back to you as soon as possible.</p>
                    <form method="post" action="https://azim.hostlin.com/Fxzone/index.html">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Your Text"></textarea>
                        </div>
                        <div class="form-group message-btn">
                            <button type="submit" class="theme-btn btn-one">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- main header -->
        <header class="main-header">
            <!-- header-top -->
             <div class="header-top">
                <div class="top-inner">
                    <ul class="info-list">
                        <li><i class="flaticon-telegram-logo"></i><a href="#">Telegram Chat</a></li>
                        <!--<li><i class="flaticon-mail"></i><span>Let’s connect...<a href="mailto:mail@example.com">mail@example.com</a></span></li>-->
                    </ul>
                    <ul class="links-list">
                        <li><i class="flaticon-link"></i><a href="index.php">How it’s work</a></li>
                        <li><a href="index.php">Office Timeing Morning 10AM to Evening 06PM.</a></li>
                    </ul>
                </div>
             </div>
            <!-- header-lower -->
            <div class="header-lower">
                <div class="outer-container">
                    <div class="outer-box">
                        <div class="left-column">
                            <div class="logo-box">
                                <figure class="logo"><a href="index.php"><img src="<?php echo $hmlogo; ?>" alt="" style="height:100px; width:100px;"></a></figure>
                            </div>
                            <!--<div class="account-box">-->
                            <!--    <div class="icon-box"><img src="assets/images/icons/icon-1.png" alt=""></div>-->
                            <!--    <div class="select-box">-->
                            <!--        <select class="wide">-->
                            <!--           <option data-display="Account">Account</option>-->
                            <!--           <option value="1">Login</option>-->
                            <!--           <option value="2">Signup</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                        <div class="menu-area clearfix">
                            <!--Mobile Navigation Toggler-->
                            <div class="mobile-nav-toggler">
                                <i class="icon-bar"></i>
                                <i class="icon-bar"></i>
                                <i class="icon-bar"></i>
                            </div>
                            <nav class="main-menu navbar-expand-md navbar-light">
                                <div class="collapse navbar-collapse show clearfix" id="navbarSupportedContent">
                                    <ul class="navigation clearfix">
                                        <li class="current dropdown"><a href="<?php echo $hmhome; ?>">Home</a>
                                            <!--<ul>-->
                                            <!--    <li><a href="index.html">Home Page 01</a></li>-->
                                            <!--    <li><a href="index-2.html">Home Page 02</a></li>-->
                                            <!--    <li><a href="index-3.html">Home Page 03</a></li>-->
                                            <!--    <li><a href="index-4.html">Home Page 04</a></li>-->
                                            <!--</ul>-->
                                        </li>  
                                        <li class="dropdown"><a href="<?php echo $hmabout; ?>">About</a>
                                            <!--<ul>-->
                                            <!--    <li><a href="about.html">About Us</a></li>-->
                                            <!--    <li><a href="history.html">Our History</a></li>-->
                                            <!--    <li><a href="team.html">Team Members</a></li>-->
                                            <!--    <li><a href="faq.html">Faq’s</a></li>-->
                                            <!--    <li><a href="error.html">404</a></li>-->
                                            <!--</ul>-->
                                        </li>
                                        <li class="dropdown"><a href="<?php echo $hmservice; ?>">Services</a>
                                            <!--<ul>-->
                                            <!--    <li class="dropdown"><a href="#"></a>-->
                                                    <!--<ul>-->
                                                    <!--    <li><a href="account-1.html">All Accounts</a></li>-->
                                                    <!--    <li><a href="account-2.html">Individual Account</a></li>-->
                                                    <!--    <li><a href="account-3.html">Professional Account</a></li>-->
                                                    <!--    <li><a href="account-4.html">Demo Account</a></li>-->
                                                    <!--    <li><a href="account-5.html">Pro Account</a></li>-->
                                                    <!--    <li><a href="account-6.html">VIP Account</a></li>-->
                                                    <!--    <li><a href="account-7.html">ECN Account</a></li>-->
                                                    <!--</ul>-->
                                            <!--    </li>-->
                                                <!--<li class="dropdown"><a href="#">Education</a>-->
                                                    <!--<ul>-->
                                                    <!--    <li><a href="education.html">Education</a></li>-->
                                                    <!--    <li><a href="education-details.html">Education Details</a></li>-->
                                                    <!--</ul>-->
                                                <!--</li>-->
                                                <!--<li class="dropdown"><a href="#">Platform</a>-->
                                                    <!--<ul>-->
                                                    <!--    <li><a href="trader-1.html">MetaTrader 4</a></li>-->
                                                    <!--    <li><a href="trader-2.html">MetaTrader 5</a></li>-->
                                                    <!--</ul>-->
                                                <!--</li>-->
                                            <!--</ul>-->
                                        </li> 
                                        <li class="dropdown"><a href="team.php">Team</a>
                                            <!--<ul>-->
                                            <!--    <li><a href="service.html">All Services</a></li>-->
                                            <!--    <li><a href="service-details.html">Currency Pairs</a></li>-->
                                            <!--    <li><a href="service-details-2.html">Trading Accounts</a></li>-->
                                            <!--    <li><a href="service-details-3.html">Platform & Tools</a></li>-->
                                            <!--    <li><a href="service-details-4.html">Monitoring & Support</a></li>-->
                                            <!--    <li><a href="service-details-5.html">Education & Training</a></li>-->
                                            <!--    <li><a href="service-details-6.html">Research & News</a></li>-->
                                            <!--</ul>-->
                                        </li> 
                                        <!--<li class="dropdown"><a href="#">Blog</a>-->
                                            <!--<ul>-->
                                            <!--    <li><a href="blog.html">Grid View</a></li>-->
                                            <!--    <li><a href="blog-2.html">List View 01</a></li>-->
                                            <!--    <li><a href="blog-3.html">List View 02</a></li>-->
                                            <!--    <li><a href="blog-details.html">Single Post</a></li>-->
                                            <!--</ul>-->
                                        <!--</li>  -->
                                        <li><a href="<?php echo $hmcontact; ?>">Contact</a></li>
                                        <!--<div class="account-box">-->
                                        <!--    <div class="icon-box"><img src="assets/images/icons/icon-1.png" alt=""></div>-->
                                        <!--        <div class="select-box">-->
                                        <!--            <select class="wide">-->
                                        <!--                <option data-display="Account">Account</option>-->
                                        <!--                <option value="1">Login</option>-->
                                        <!--                <option value="2">Signup</option>-->
                                        <!--            </select>-->
                                        <!--        </div>-->
                                        <!--</div>-->
                                        <!--<div class="icon-box"><img src="assets/images/icons/icon-1.png" alt=""></div>-->
                                        <li class="dropdown"><a href="#">Account</a>
                                            <ul>
                                                <li><a href="<?php echo $hmlogin; ?>">Login</a></li>
                                                <li><a href="<?php echo $hmregister; ?>">Register</a></li>
                                            </ul>
                                        </li>  
                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <!--<div class="menu-right-content">-->
                        <!--    <ul class="option-list">-->
                        <!--        <li><div class="search-box-outer search-toggler"><i class="flaticon-search"></i></div></li>-->
                        <!--        <li class="support-box">-->
                        <!--            <div class="icon-box">-->
                        <!--                <div class="icon-one"><img src="assets/images/icons/icon-7.png" alt=""></div>-->
                        <!--                <div class="icon-two"><img src="assets/images/icons/icon-2.png" alt=""></div>-->
                        <!--            </div>-->
                        <!--            <div class="support-content">-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-3.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <a href="index.html"></a>-->
                        <!--                        <h5>Faq’s</h5>-->
                        <!--                        <p>Get 100+ answers.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-4.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <button type="button" class="chat-toggler"></button>-->
                        <!--                        <h5>Live Chat</h5>-->
                        <!--                        <p>Anytime, Anywhere.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-5.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <a href="index.html"></a>-->
                        <!--                        <h5>Community Forums</h5>-->
                        <!--                        <p>Unite & Discuss.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-6.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <a href="index.html"></a>-->
                        <!--                        <h5>Tutorials</h5>-->
                        <!--                        <p>Skill Up Now.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--    <div class="btn-box"><a href="index.html" class="theme-btn btn-one"><span>Get Free Trial</span></a></div>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>

            <!--sticky Header-->
            <div class="sticky-header">
                <div class="outer-container">
                    <div class="outer-box">
                        <div class="left-column">
                            <div class="logo-box">
                                <figure class="logo"><a href="index.php"><img src="<?php echo $hmlogo; ?>" alt="" style="height:100px; width:100px;"></a></figure>
                            </div>
                            <!--<div class="account-box">-->
                            <!--    <div class="icon-box"><img src="assets/images/icons/icon-1.png" alt=""></div>-->
                            <!--    <div class="select-box">-->
                            <!--        <select class="wide">-->
                            <!--           <option data-display="Account">Account</option>-->
                            <!--           <option value="1">Login</option>-->
                            <!--           <option value="2">Signup</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                        <div class="menu-area clearfix">
                            <nav class="main-menu clearfix">
                                <!--Keep This Empty / Menu will come through Javascript-->
                            </nav>
                        </div>
                        <!--<div class="menu-right-content">-->
                        <!--    <ul class="option-list">-->
                        <!--        <li><div class="search-box-outer search-toggler"><i class="flaticon-search"></i></div></li>-->
                        <!--        <li class="support-box">-->
                        <!--            <div class="icon-box">-->
                        <!--                <div class="icon-one"><img src="assets/images/icons/icon-7.png" alt=""></div>-->
                        <!--                <div class="icon-two"><img src="assets/images/icons/icon-2.png" alt=""></div>-->
                        <!--            </div>-->
                        <!--            <div class="support-content">-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-3.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <a href="index.html"></a>-->
                        <!--                        <h5>Faq’s</h5>-->
                        <!--                        <p>Get 100+ answers.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-4.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <button type="button" class="chat-toggler"></button>-->
                        <!--                        <h5>Live Chat</h5>-->
                        <!--                        <p>Anytime, Anywhere.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-5.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <a href="index.html"></a>-->
                        <!--                        <h5>Community Forums</h5>-->
                        <!--                        <p>Unite & Discuss.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--                <div class="single-item">-->
                        <!--                    <div class="icon-box"><img src="assets/images/icons/icon-6.png" alt=""></div>-->
                        <!--                    <div class="inner">-->
                        <!--                        <a href="index.html"></a>-->
                        <!--                        <h5>Tutorials</h5>-->
                        <!--                        <p>Skill Up Now.</p>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--    <div class="btn-box"><a href="index.html" class="theme-btn btn-one"><span>Get Free Trial</span></a></div>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
        </header>
    </body>
</html>