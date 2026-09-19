<?php
$hmlogo="assets/images/logo.png";
$hmfavicon="assets/images/logo.png";
$hmtitle="Ananta";
$hmlogin="dashboard/user1/login.php";
$hmregister="dashboard/user1/new_binary_registration_form.php";

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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Roboto:wght@300;400;500;700;900&family=Funnel+Display:wght@300..800&display=swap" rel="stylesheet">

<!-- PWA Meta Tags & Manifest -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0a2540">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="<?php echo $hmtitle; ?>">

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
<link rel="stylesheet" href="assets/css/app-pwa.css">
<link rel="stylesheet" href="assets/css/app-modern.css">
<link rel="stylesheet" href="assets/css/header-glass.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


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


        <!-- Floating Help & Support Contact Trigger Button -->
        <div class="chat-icon">
            <button type="button" class="chat-toggler ananta-glass-chat-btn">
                <i class="fa fa-headset"></i>
                <span class="chat-btn-pulse"></span>
            </button>
        </div>

        <!-- Premium Glass Chat & Help Support Popup Modal -->
        <div id="chat-popup" class="chat-popup ananta-glass-chat-popup">
            <div class="popup-inner ananta-chat-modal-inner">
                <div class="close-chat"><i class="flaticon-close"></i></div>
                
                <!-- Green Premium Brand Header with Logo -->
                <div class="chat-modal-header text-center mb-3">
                    <div class="modal-logo-wrapper mb-2">
                        <img src="<?php echo $hmlogo; ?>" alt="Ananta Multi Trade" style="height: 44px; width: auto;">
                    </div>
                    <div class="chat-header-badge">
                        <i class="fa fa-shield-alt me-1"></i> 24/7 Premium Support
                    </div>
                    <h4 class="chat-title">Get in Touch With Us</h4>
                    <p class="chat-subtitle">Please fill out the form below and our advisors will respond immediately.</p>
                </div>

                <div class="chat-form">
                    <form method="post" action="contact.php">
                        <div class="form-group position-relative mb-3">
                            <i class="fa fa-user input-icon-left"></i>
                            <input type="text" name="name" class="ananta-glass-input" placeholder="Your Name" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <i class="fa fa-envelope input-icon-left"></i>
                            <input type="email" name="email" class="ananta-glass-input" placeholder="Your Email Address" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <i class="fa fa-comment-alt input-icon-left" style="top: 18px;"></i>
                            <textarea name="message" class="ananta-glass-input" rows="2" placeholder="How can we help you today?" required></textarea>
                        </div>
                        <div class="form-group message-btn text-center mb-0">
                            <button type="submit" class="chat-submit-btn">
                                Send Message <i class="fa fa-paper-plane ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Single White Transparent Rounded Glass Header (Inspired by Reference UI) -->
        <header class="ananta-glass-header-wrapper">
            <div class="ananta-glass-header-inner">
                
                <!-- Left / Center Mobile: Rounded Brand Logo -->
                <div class="glass-logo-card">
                    <a href="index.php">
                        <img src="assets/images/logo.png" alt="Ananta Multi Trade">
                    </a>
                </div>

                <!-- Navigation Links with Sliding Glass Effect (Desktop) -->
                <ul class="glass-nav-links d-none d-lg-flex">
                    <!-- Dynamic Sliding Pill Highlighter -->
                    <div class="nav-slide-pill" id="navSlidePill"></div>

                    <li class="nav-item active"><a href="index.php#home">Home</a></li>
                    <li class="nav-item"><a href="index.php#about">About Us</a></li>
                    <li class="nav-item"><a href="index.php#businesses">Businesses</a></li>
                    <li class="nav-item"><a href="index.php#partners">Why Us?</a></li>
                    <li class="nav-item"><a href="index.php#contact">Contact</a></li>
                </ul>

                <!-- Right Action Buttons (Desktop: Login & Register Modal Triggers) -->
                <div class="glass-actions d-none d-lg-flex">
                    <button type="button" class="btn-glass-login" data-ananta-modal="loginModal"><i class="fa fa-user me-1"></i> Login</button>
                    <button type="button" class="btn-glass-register" data-ananta-modal="registerModal"><i class="fa fa-user-plus me-1"></i> Register</button>
                </div>

                <!-- Mobile Header Button (Account / Login Icon Only) -->
                <div class="mobile-header-login d-lg-none">
                    <button type="button" class="mobile-login-icon-btn" data-ananta-modal="loginModal" title="Account Login">
                        <i class="fa fa-user-circle"></i>
                    </button>
                </div>

            </div>
        </header>

        <!-- Dynamic Auth Modal Backdrop & Popups for Landing Page -->
        <div id="anantaAuthOverlay" class="ananta-auth-modal-overlay">
            
            <!-- 1. LOGIN POPUP MODAL -->
            <div id="loginModal" class="ananta-auth-modal-card">
                <button type="button" class="ananta-modal-close">&times;</button>
                <div class="auth-logo text-center mb-3">
                    <img src="assets/images/logo-stacked.png" alt="Ananta Logo" style="max-height: 95px; width: auto;">
                </div>
                <div class="auth-header text-center mb-4">
                    <h3 style="font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Login</h3>
                    <p style="font-size: 10.5px; letter-spacing: 0.8px; text-transform: uppercase; font-weight: 600; color: #64748b; margin: 0;">PLEASE LOGIN TO YOUR ACCOUNT TO CONTINUE</p>
                </div>
                <form action="dashboard/user1/login.php" method="POST">
                    <div class="form-group mb-3 position-relative">
                        <input type="text" name="userid" class="form-control ananta-modal-input" placeholder="Email or User ID" required style="border-radius: 12px; height: 46px; padding-right: 40px;">
                        <i class="fa fa-user" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="form-group mb-3 position-relative">
                        <input type="password" name="password" class="form-control ananta-modal-input" placeholder="Password" required style="border-radius: 12px; height: 46px; padding-right: 40px;">
                        <i class="fa fa-lock" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4" style="font-size: 13px;">
                        <label class="mb-0 d-flex align-items-center gap-1" style="color: #64748b; cursor: pointer;">
                            <input type="checkbox" checked style="accent-color: #10b981;"> Remember me
                        </label>
                        <a href="dashboard/user1/reset-password.php" style="color: #00b4d8; font-weight: 600; text-decoration: none;">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #00b4d8 0%, #10b981 100%); color: #fff; font-weight: 700; border-radius: 12px; height: 46px; letter-spacing: 0.5px;">LOGIN</button>
                    <div class="text-center mt-3" style="font-size: 13px; color: #64748b;">
                        Don't have an account? <a href="#" data-switch-modal="registerModal" style="color: #10b981; font-weight: 700; text-decoration: none;">Signup</a>
                    </div>
                </form>
            </div>

            <!-- 2. REGISTER POPUP MODAL -->
            <div id="registerModal" class="ananta-auth-modal-card">
                <button type="button" class="ananta-modal-close">&times;</button>
                <div class="auth-logo text-center mb-3">
                    <img src="assets/images/logo-stacked.png" alt="Ananta Logo" style="max-height: 85px; width: auto;">
                </div>
                <div class="auth-header text-center mb-3">
                    <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Create Account</h3>
                    <p style="font-size: 10px; letter-spacing: 0.8px; text-transform: uppercase; font-weight: 600; color: #64748b; margin: 0;">JOIN ANANTA TO START YOUR JOURNEY</p>
                </div>
                <form action="dashboard/user1/new_binary_registration_form.php" method="POST" id="modal_registration_form">
                    <div class="form-group mb-2 position-relative">
                        <input type="text" name="refferalId" id="modalReferrerId" class="form-control ananta-modal-input" placeholder="Referrer ID" required style="border-radius: 10px; height: 42px; padding-right: 36px; font-size: 13.5px;">
                        <i class="fa fa-link" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="form-group mb-2" id="modal_sponsor_name" style="display: none;">
                        <input type="text" id="modal_response2" class="form-control" style="background-color: #f1f5f9; font-size: 13px; font-weight: 600; border-radius: 10px; height: 38px;" readonly>
                    </div>
                    <div class="form-group mb-2 position-relative">
                        <input type="text" name="userName" class="form-control ananta-modal-input" placeholder="Full Name" required style="border-radius: 10px; height: 42px; padding-right: 36px; font-size: 13.5px;">
                        <i class="fa fa-user" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="form-group mb-2 position-relative">
                        <input type="email" name="email" class="form-control ananta-modal-input" placeholder="Email Address" required style="border-radius: 10px; height: 42px; padding-right: 36px; font-size: 13.5px;">
                        <i class="fa fa-envelope" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="form-group mb-2 d-flex gap-2">
                        <select class="form-control" name="mobilecode" style="flex: 0 0 42%; min-width: 0; border-radius: 10px; height: 42px; font-size: 12.5px; padding-left: 8px; padding-right: 8px;">
                            <option value="+91" selected>India (+91)</option>
                            <option value="+1">US (+1)</option>
                            <option value="+44">UK (+44)</option>
                            <option value="+971">UAE (+971)</option>
                        </select>
                        <div class="position-relative flex-grow-1" style="flex: 1 1 58%; min-width: 0;">
                            <input type="text" name="mobile" class="form-control ananta-modal-input" placeholder="Mobile Number" required style="border-radius: 10px; height: 42px; padding-right: 36px; font-size: 13.5px;">
                            <i class="fa fa-phone" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        </div>
                    </div>
                    <div class="form-group mb-2 position-relative">
                        <input type="password" name="pass1" id="modalPass1" class="form-control ananta-modal-input" placeholder="Password" required style="border-radius: 10px; height: 42px; padding-right: 36px; font-size: 13.5px;">
                        <i class="fa fa-lock" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="form-group mb-2 position-relative">
                        <input type="password" name="pass2" id="modalPass2" class="form-control ananta-modal-input" placeholder="Confirm Password" required style="border-radius: 10px; height: 42px; padding-right: 36px; font-size: 13.5px;">
                        <i class="fa fa-lock" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 px-2 py-1" style="background: #f8fafc; border-radius: 8px; font-size: 12.5px;">
                        <span style="font-weight: 600; color: #475569;">Position:</span>
                        <div class="d-flex gap-3">
                            <label class="mb-0" style="cursor: pointer;"><input type="radio" name="position" value="left" checked> Left</label>
                            <label class="mb-0" style="cursor: pointer;"><input type="radio" name="position" value="right"> Right</label>
                        </div>
                    </div>
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #00b4d8 0%, #10b981 100%); color: #fff; font-weight: 700; border-radius: 10px; height: 42px; letter-spacing: 0.5px; margin-top: 6px;">REGISTER</button>
                    <div class="text-center mt-2" style="font-size: 12.5px; color: #64748b;">
                        Already have an account? <a href="#" data-switch-modal="loginModal" style="color: #10b981; font-weight: 700; text-decoration: none;">Sign In</a>
                    </div>
                </form>
            </div>

        </div>


        <!-- Fixed Bottom Mobile Navigation Bar (Pure Transparent Glass Blur & Equal Small Buttons with Sliding Glass Pill) -->
        <nav class="ananta-mobile-bottom-nav d-lg-none">
            <div class="ananta-mobile-bottom-nav-inner">
                <!-- Dynamic Sliding Glass Pill Indicator for Mobile -->
                <div class="mobile-slide-pill" id="mobileSlidePill"></div>

                <a href="index.php#home" class="mobile-bottom-item active">
                    <i class="fa fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="index.php#about" class="mobile-bottom-item">
                    <i class="fa fa-info-circle"></i>
                    <span>About Us</span>
                </a>
                <a href="index.php#businesses" class="mobile-bottom-item">
                    <i class="fa fa-briefcase"></i>
                    <span>Businesses</span>
                </a>
                <a href="index.php#partners" class="mobile-bottom-item">
                    <i class="fa fa-question-circle"></i>
                    <span>Why Us?</span>
                </a>
                <a href="index.php#contact" class="mobile-bottom-item">
                    <i class="fa fa-envelope"></i>
                    <span>Contact</span>
                </a>
            </div>
        </nav>

        <!-- Script for Header Sliding Apple-style Pill & Active Page Matching -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.glass-nav-links .nav-item');
            const pill = document.getElementById('navSlidePill');
            const navContainer = document.querySelector('.glass-nav-links');

            function movePill(targetLi) {
                if (!targetLi || !pill || !navContainer) return;
                const containerRect = navContainer.getBoundingClientRect();
                const liRect = targetLi.getBoundingClientRect();

                pill.style.width = liRect.width + 'px';
                pill.style.height = liRect.height + 'px';
                pill.style.left = (liRect.left - containerRect.left) + 'px';
                pill.style.top = (liRect.top - containerRect.top) + 'px';
                pill.style.opacity = '1';
            }

            // Detect current page and mark active
            const currentPath = window.location.pathname.split('/').pop() || 'index.php';
            navLinks.forEach(li => {
                const a = li.querySelector('a');
                if (a) {
                    const href = a.getAttribute('href');
                    if (href && (href.startsWith(currentPath) || (currentPath === 'index.php' && href.includes('index.php')))) {
                        navLinks.forEach(item => item.classList.remove('active'));
                        li.classList.add('active');
                    }
                }
            });

            // Position pill on initial page load
            const activeLi = document.querySelector('.glass-nav-links .nav-item.active') || navLinks[0];
            if (activeLi) {
                setTimeout(() => movePill(activeLi), 100);
            }

            navLinks.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Lock active class on clicked option
                    navLinks.forEach(li => li.classList.remove('active'));
                    this.classList.add('active');
                    movePill(this);

                    const anchor = this.querySelector('a');
                    if (anchor) {
                        const href = anchor.getAttribute('href');
                        if (href && href.includes('#')) {
                            const hash = href.substring(href.indexOf('#'));
                            const targetSec = document.querySelector(hash);
                            if (targetSec) {
                                e.preventDefault();
                                targetSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }
                    }
                });
            });

            // Reset back to locked active item when mouse leaves nav container
            if (navContainer) {
                navContainer.addEventListener('mouseleave', function() {
                    const currentActive = document.querySelector('.glass-nav-links .nav-item.active') || navLinks[0];
                    if (currentActive) movePill(currentActive);
                });
            }

            // --- Mobile Bottom Navigation Sliding Glass Pill logic ---
            const mobileItems = document.querySelectorAll('.ananta-mobile-bottom-nav .mobile-bottom-item');
            const mobilePill = document.getElementById('mobileSlidePill');
            const mobileContainer = document.querySelector('.ananta-mobile-bottom-nav-inner');

            function moveMobilePill(targetItem) {
                if (!targetItem || !mobilePill || !mobileContainer) return;
                const containerRect = mobileContainer.getBoundingClientRect();
                const itemRect = targetItem.getBoundingClientRect();

                mobilePill.style.width = itemRect.width + 'px';
                mobilePill.style.height = itemRect.height + 'px';
                mobilePill.style.left = (itemRect.left - containerRect.left) + 'px';
                mobilePill.style.top = (itemRect.top - containerRect.top) + 'px';
                mobilePill.style.opacity = '1';
            }

            // Detect current page for mobile nav active state
            mobileItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && (href.startsWith(currentPath) || (currentPath === 'index.php' && href.includes('index.php')))) {
                    mobileItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                }
            });

            const activeMobileItem = document.querySelector('.ananta-mobile-bottom-nav .mobile-bottom-item.active') || mobileItems[0];
            if (activeMobileItem) {
                setTimeout(() => moveMobilePill(activeMobileItem), 120);
            }

            mobileItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    mobileItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    moveMobilePill(this);

                    const href = this.getAttribute('href');
                    if (href && href.includes('#')) {
                        const hash = href.substring(href.indexOf('#'));
                        const targetSec = document.querySelector(hash);
                        if (targetSec) {
                            e.preventDefault();
                            targetSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                });
            });

            window.addEventListener('resize', function() {
                const currentActive = document.querySelector('.glass-nav-links .nav-item.active') || navLinks[0];
                if (currentActive) movePill(currentActive);

                const currentMobileActive = document.querySelector('.ananta-mobile-bottom-nav .mobile-bottom-item.active') || mobileItems[0];
                if (currentMobileActive) moveMobilePill(currentMobileActive);
            });

            // --- ScrollSpy: Auto-update Desktop & Mobile Nav active pill on Scroll ---
            const sections = [
                document.getElementById('home'),
                document.getElementById('about'),
                document.getElementById('businesses'),
                document.getElementById('partners'),
                document.getElementById('contact')
            ].filter(Boolean);

            if (sections.length > 0) {
                const observerOptions = {
                    root: null,
                    rootMargin: '-20% 0px -60% 0px',
                    threshold: 0
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const sectionId = entry.target.getAttribute('id');
                            if (!sectionId) return;

                            // Update Desktop Nav
                            navLinks.forEach(li => {
                                const a = li.querySelector('a');
                                if (a && a.getAttribute('href') && a.getAttribute('href').endsWith('#' + sectionId)) {
                                    navLinks.forEach(item => item.classList.remove('active'));
                                    li.classList.add('active');
                                    movePill(li);
                                }
                            });

                            // Update Mobile Nav
                            mobileItems.forEach(item => {
                                const href = item.getAttribute('href');
                                if (href && href.endsWith('#' + sectionId)) {
                                    mobileItems.forEach(i => i.classList.remove('active'));
                                    item.classList.add('active');
                                    moveMobilePill(item);
                                }
                            });
                        }
                    });
                }, observerOptions);

                sections.forEach(sec => observer.observe(sec));
            }
            // --- Auth Modals JavaScript Logic ---
            const overlay = document.getElementById('anantaAuthOverlay');
            const modalCards = document.querySelectorAll('.ananta-auth-modal-card');
            const openModalBtns = document.querySelectorAll('[data-ananta-modal]');
            const closeModalBtns = document.querySelectorAll('.ananta-modal-close');
            const switchModalBtns = document.querySelectorAll('[data-switch-modal]');

            function openModal(modalId) {
                if (!overlay) return;
                modalCards.forEach(c => c.classList.remove('active'));
                const targetCard = document.getElementById(modalId);
                if (targetCard) {
                    overlay.classList.add('active');
                    targetCard.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeModal() {
                if (!overlay) return;
                overlay.classList.remove('active');
                modalCards.forEach(c => c.classList.remove('active'));
                document.body.style.overflow = '';
            }

            openModalBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetModal = this.getAttribute('data-ananta-modal');
                    openModal(targetModal);
                });
            });

            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) closeModal();
                });
            }

            switchModalBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetModal = this.getAttribute('data-switch-modal');
                    openModal(targetModal);
                });
            });

            // Modal Referrer ID AJAX lookup
            const modalRefInput = document.getElementById('modalReferrerId');
            if (modalRefInput) {
                modalRefInput.addEventListener('blur', function() {
                    const refId = this.value;
                    if (refId.length > 2) {
                        $.ajax({
                            type: 'POST',
                            url: 'dashboard/user1/checkName.php',
                            data: { data: refId },
                            success: function(response) {
                                if (response != 0) {
                                    $('#modal_response2').val(response);
                                    $('#modal_sponsor_name').slideDown();
                                } else {
                                    $('#modal_response2').val("Wrong Sponsor ID");
                                    $('#modal_sponsor_name').slideDown();
                                }
                            }
                        });
                    }
                });
            }
        });
        </script>

    </body>
</html>