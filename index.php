<?php
$refCode = '';
if (!empty($_GET['refferalId'])) {
    $refCode = trim($_GET['refferalId']);
} elseif (!empty($_GET['ref'])) {
    $refCode = trim($_GET['ref']);
} elseif (!empty($_GET['referral'])) {
    $refCode = trim($_GET['referral']);
} elseif (!empty($_GET['sponsor'])) {
    $refCode = trim($_GET['sponsor']);
} elseif (!empty($_GET['sponsorid'])) {
    $refCode = trim($_GET['sponsorid']);
} elseif (!empty($_GET['sponsor_id'])) {
    $refCode = trim($_GET['sponsor_id']);
} elseif (!empty($_GET['referral_code'])) {
    $refCode = trim($_GET['referral_code']);
} elseif (!empty($_GET['uid'])) {
    $refCode = trim($_GET['uid']);
}

if (!empty($refCode)) {
    $getParams = $_GET;
    $getParams['refferalId'] = $refCode;
    $queryString = '?' . http_build_query($getParams);
    header("Location: dashboard/user1/register.php" . $queryString);
    exit();
}

include "common/header.php"; 
?>
<style>
    #about .mb-3.d-flex > div {
        width: 44px !important;
        height: 44px !important;
        font-size: 24px !important;
    }
    #about h2.display-5 {
        font-size: 20px !important;
    }
    #about p.fw-semibold {
        font-size: 11px !important;
        line-height: 1.2 !important;
    }
}

/* Strict Contrast Overrides for Accordion & Form Controls */
.accordion-button, 
.accordion-button:not(.collapsed), 
.accordion-button:focus, 
.accordion-button:hover,
.accordion-button:active {
    background: #F8FAFC !important;
    color: #0B2545 !important;
    box-shadow: none !important;
}

.accordion-button::after {
    filter: none;
}

.accordion-body {
    background: #FFFFFF !important;
    color: #334155 !important;
}

.contact-form .form-control,
.contact-form .form-control:focus,
.contact-form .form-control:active {
    background: #F8FAFC !important;
    color: #0F172A !important;
    border: 1px solid #CBD5E1 !important;
}

.contact-form .form-control::placeholder {
    color: #64748B !important;
    opacity: 1;
}



</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


        <!-- Mobile Menu  -->
        <div class="mobile-menu">
            <div class="menu-backdrop"></div>
            <div class="close-btn"><i class="fas fa-times"></i></div>
            
            <nav class="menu-box">
                <div class="nav-logo text-center py-3 border-bottom border-secondary border-opacity-25">
                    <a href="index.php">
                        <img src="<?php echo $hmlogo; ?>" style="height:60px; width:auto;" alt="Ananta Logo" title="Ananta Multi Trade">
                    </a>
                </div>
                
                <!-- Explicit Mobile Menu Navigation Links -->
                <div class="menu-outer my-4">
                    <ul class="navigation clearfix" style="list-style: none; padding: 0; margin: 0;">
                        <li class="py-2 border-bottom border-secondary border-opacity-10"><a href="index.php#home" class="text-white fw-bold text-decoration-none fs-5 d-block px-3"><i class="fa fa-home me-2 text-info"></i> Home</a></li>
                        <li class="py-2 border-bottom border-secondary border-opacity-10"><a href="index.php#about" class="text-white fw-bold text-decoration-none fs-5 d-block px-3"><i class="fa fa-info-circle me-2 text-info"></i> About Us</a></li>
                        <li class="py-2 border-bottom border-secondary border-opacity-10"><a href="index.php#businesses" class="text-white fw-bold text-decoration-none fs-5 d-block px-3"><i class="fa fa-briefcase me-2 text-info"></i> Businesses</a></li>
                        <li class="py-2 border-bottom border-secondary border-opacity-10"><a href="index.php#partners" class="text-white fw-bold text-decoration-none fs-5 d-block px-3"><i class="fa fa-question-circle me-2 text-info"></i> Why Us?</a></li>
                        <li class="py-2 border-bottom border-secondary border-opacity-10"><a href="index.php#contact" class="text-white fw-bold text-decoration-none fs-5 d-block px-3"><i class="fa fa-envelope me-2 text-info"></i> Contact</a></li>
                        <li class="py-2"><a href="<?php echo $hmlogin; ?>" class="text-success fw-bold text-decoration-none fs-5 d-block px-3"><i class="fa fa-user-circle me-2 text-success"></i> Account Login</a></li>
                    </ul>
                </div>

                <div class="social-links mt-4 text-center">
                    <ul class="clearfix d-inline-flex gap-3 list-unstyled">
                        <li><a href="https://www.facebook.com/p/Ananta-Melody-Verse-61585786533006" class="text-white fs-4"><i class="flaticon-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/anantamelodyverses?igsh=NmQ1NGItY3VqZGhw&utm_source=qr" class="text-white fs-4"><i class="flaticon-instagram-logo"></i></a></li>
                        <li><a href="https://t.me/anantamultitreadpvt" class="text-white fs-4"><i class="flaticon-telegram-logo"></i></a></li>
                        <li><a href="https://www.youtube.com/@anantamelodyverse?si=qIDQyBt9kS0s4A0F" class="text-white fs-4"><i class="flaticon-youtube"></i></a></li>
                    </ul>
                </div>
            </nav>
        </div><!-- End Mobile Menu -->
        <!-- Corporate Hero Section with Office Building Background -->
        <section id="home" class="ananta-hero-section position-relative overflow-hidden">
            <div class="ananta-hero-bg-holder"></div>
            <div class="container-fluid px-lg-5 px-md-4 px-3 position-relative" style="z-index: 5;">
                <div class="row align-items-center">
                    
                    <!-- Left Hero Content (Far Left Aligned) -->
                    <div class="col-xl-5 col-lg-5 col-md-10 col-12 py-4 ps-lg-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span style="width: 26px; height: 3px; background: #967431; display: inline-block;"></span>
                            <span class="text-uppercase fw-bold" style="font-size: 13px; letter-spacing: 1.2px; color: #1E293B;">ANANTA MULTI TRADE PRIVATE LIMITED</span>
                        </div>

                        <h1 class="fw-black mb-4" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(36px, 4.2vw, 52px); font-weight: 800; color: #0F172A; line-height: 1.08; letter-spacing: -1.2px;">
                            ONE VISION.<br>
                            MULTIPLE<br>
                            OPPORTUNITIES.
                        </h1>

                        <p class="fw-bold mb-3" style="font-size: 16px; color: #334155; line-height: 1.55; max-width: 480px;">
                            Building a diversified business ecosystem across markets, infrastructure, real estate and emerging industries.
                        </p>

                        <p class="mb-4 pb-2" style="font-size: 14px; color: #64748B; line-height: 1.65; max-width: 480px;">
                            Ananta Multi Trade Private Limited is creating a growing network of businesses, partners and opportunities with a focus on innovation, professional operations and long-term growth.
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <a href="#about" class="btn px-4 py-3 fw-bold text-uppercase d-inline-flex align-items-center gap-2" style="background: #967431; color: #FFFFFF; border-radius: 8px; font-size: 13px; letter-spacing: 0.8px; border: none; box-shadow: 0 8px 20px rgba(150, 116, 49, 0.25); transition: all 0.3s ease;">
                                EXPLORE ANANTA <i class="fa fa-arrow-right"></i>
                            </a>
                            <a href="<?php echo $hmregister; ?>" class="btn px-4 py-3 fw-bold text-uppercase d-inline-flex align-items-center gap-2" style="background: rgba(255, 255, 255, 0.85); color: #0F172A; border-radius: 8px; font-size: 13px; letter-spacing: 0.8px; border: 1.5px solid #0F172A; transition: all 0.3s ease;">
                                GET STARTED
                            </a>
                        </div>
                    </div>

                    <!-- Right Space for Unobstructed View of the Building & Logo -->
                    <div class="col-xl-7 col-lg-7 d-none d-lg-block" style="min-height: 480px;"></div>

                </div>
            </div>
        </section>
        <!-- ==================================================
             SECTION 02 — ABOUT US & OUR GROWTH
             ID: #about
             ================================================== -->
        <section id="about" class="py-4 position-relative overflow-hidden w-100" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-top: 1px solid rgba(197, 160, 89, 0.3); border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="container-fluid p-0 position-relative" style="z-index: 5;">
                
                <div class="row g-0 align-items-center">
                    
                    <!-- OUR GROWTH STATS INFOGRAPHIC (FULL WIDTH ON MAIN SITE BACKGROUND) -->
                    <div class="col-12 framer-reveal framer-delay-1">
                        
                        <!-- OUR GROWTH INFOGRAPHIC OVER IMAGE (DESKTOP ONLY - LAPTOP VIEW) -->
                        <div class="growth-image-infographic-wrapper position-relative w-100 mx-auto d-none d-lg-block">
                            
                            <!-- Base Image with Framer Reveal -->
                            <img src="assets/images/about/circle.png" alt="Our Growth Infographic" class="w-100 d-block framer-reveal" style="border-radius: 20px; box-shadow: 0 15px 45px rgba(0, 0, 0, 0.08);">

                            <!-- OVERLAY HEADER TITLE -->
                            <div class="position-absolute text-center" style="top: 3.5%; left: 50%; transform: translateX(-50%); width: 100%; z-index: 10;">
                                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 800; letter-spacing: 3px; color: #0F5132; text-transform: uppercase;">OUR</span>
                                <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 38px; font-weight: 900; letter-spacing: 2px; background: linear-gradient(135deg, #0B2545 0%, #1D4ED8 50%, #0284C7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin-bottom: 2px; line-height: 1;">GROWTH</h2>
                                <div class="d-flex align-items-center justify-content-center gap-3 mt-1">
                                    <span style="font-size: 11.5px; font-weight: 800; letter-spacing: 2px; color: #1E293B; text-transform: uppercase;">STRONGER NETWORK</span>
                                    <span style="color: #94A3B8; font-weight: 400; font-size: 13px;">|</span>
                                    <span style="font-size: 11.5px; font-weight: 800; letter-spacing: 2px; color: #1E293B; text-transform: uppercase;">BIGGER TOMORROW</span>
                                </div>
                            </div>

                            <!-- NODE 01: COUNTRIES -->
                            <div class="img-callout-box framer-reveal framer-delay-1" style="top: 29%; left: 3.8%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #1D4ED8;">01</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">COUNTRIES</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 135px;">Expanding our presence across global markets.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-1" style="top: 50.8%; left: 8.8%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-globe-americas" style="font-size: 22px; color: #1D4ED8; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">7+</div>
                                <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">COUNTRIES</div>
                            </div>

                            <!-- NODE 02: MEMBERS -->
                            <div class="img-callout-box framer-reveal framer-delay-2" style="top: 21%; left: 19%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #0284C7;">02</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">MEMBERS</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 140px;">A growing community of active members.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-2" style="top: 41.5%; left: 24.2%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-users" style="font-size: 22px; color: #0284C7; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">10K+</div>
                                <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">MEMBERS</div>
                            </div>

                            <!-- NODE 03: PARTNERS -->
                            <div class="img-callout-box framer-reveal framer-delay-3" style="top: 74.5%; left: 27.5%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #0D9488;">03</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">PARTNERS</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 140px;">Building valuable partnerships worldwide.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-3" style="top: 56.5%; left: 34.3%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-handshake" style="font-size: 22px; color: #0D9488; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">100+</div>
                                <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">PARTNERS</div>
                            </div>

                            <!-- NODE 04: GLOBAL VOLUME -->
                            <div class="img-callout-box framer-reveal framer-delay-4" style="top: 17%; left: 39%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #16A34A;">04</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">GLOBAL VOLUME</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 145px;">Creating stronger business opportunities.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-4" style="top: 41.5%; left: 45.8%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-coins" style="font-size: 22px; color: #16A34A; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">$2M+</div>
                                <div style="font-size: 8px; font-weight: 800; color: #475569; text-transform: uppercase;">GLOBAL VOLUME</div>
                            </div>

                            <!-- NODE 05: SUCCESS RATE -->
                            <div class="img-callout-box framer-reveal framer-delay-5" style="top: 74.5%; left: 51.5%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #65A30D;">05</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">SUCCESS RATE</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 140px;">Turning opportunities into results.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-5" style="top: 56.5%; left: 57.3%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-chart-line" style="font-size: 22px; color: #65A30D; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">98%</div>
                                <div style="font-size: 8px; font-weight: 800; color: #475569; text-transform: uppercase;">SUCCESS RATE</div>
                            </div>

                            <!-- NODE 06: MONTHLY GROWTH -->
                            <div class="img-callout-box framer-reveal framer-delay-6" style="top: 19%; left: 61%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #84CC16;">06</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">MONTHLY GROWTH</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 145px;">Consistent progress, bigger milestones.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-6" style="top: 42.5%; left: 67.2%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-calendar-alt" style="font-size: 22px; color: #84CC16; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">9.8%</div>
                                <div style="font-size: 8px; font-weight: 800; color: #475569; text-transform: uppercase;">MONTHLY GROWTH</div>
                            </div>

                            <!-- Node 07: ACTIVE MEMBERS -->
                            <div class="img-callout-box framer-reveal framer-delay-5" style="top: 21%; left: 78.5%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #EAB308;">07</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">ACTIVE MEMBERS</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 140px;">More people, more possibilities.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-5" style="top: 41.5%; left: 84.7%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-user-check" style="font-size: 22px; color: #EAB308; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">8.5K+</div>
                                <div style="font-size: 8px; font-weight: 800; color: #475569; text-transform: uppercase;">ACTIVE MEMBERS</div>
                            </div>

                            <!-- NODE 08: ACTIVE COMMUNITY -->
                            <div class="img-callout-box framer-reveal framer-delay-6" style="top: 76%; left: 84%; text-align: left;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size: 14px; font-weight: 900; color: #F97316;">08</span>
                                    <span style="font-size: 12px; font-weight: 800; color: #0F172A; letter-spacing: 0.5px;">ACTIVE COMMUNITY</span>
                                </div>
                                <p class="m-0" style="font-size: 10.5px; color: #475569; line-height: 1.25; max-width: 145px;">Engaged community, stronger growth.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-6" style="top: 57.5%; left: 91.2%; transform: translate(-50%, -50%); text-align: center;">
                                <i class="fas fa-bullhorn" style="font-size: 22px; color: #F97316; margin-bottom: 2px; display: block;"></i>
                                <div style="font-size: 18px; font-weight: 900; color: #0F172A; line-height: 1;">85%+</div>
                                <div style="font-size: 8px; font-weight: 800; color: #475569; text-transform: uppercase;">ACTIVE COMMUNITY</div>
                            </div>

                        </div>

                        <!-- OUR GROWTH INFOGRAPHIC OVER PORTRAIT IMAGE (MOBILE ONLY VIEW - pcircle.png) -->
                        <div class="growth-mobile-infographic-wrapper position-relative w-100 mx-auto d-block d-lg-none" style="max-width: 440px;">
                            
                            <!-- Base Portrait Image -->
                            <img src="assets/images/about/pcircle.png" alt="Our Growth Mobile Infographic" class="w-100 d-block framer-reveal" style="border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);">

                            <!-- OVERLAY HEADER TITLE (MOBILE) -->
                            <div class="position-absolute text-center" style="top: 2.2%; left: 50%; transform: translateX(-50%); width: 100%; z-index: 10;">
                                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 10px; font-weight: 800; letter-spacing: 2px; color: #0F5132; text-transform: uppercase;">OUR</span>
                                <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; font-weight: 900; letter-spacing: 1.5px; background: linear-gradient(135deg, #0B2545 0%, #1D4ED8 50%, #0284C7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin-bottom: 2px; line-height: 1;">GROWTH</h2>
                                <div class="d-flex align-items-center justify-content-center gap-1 mt-1">
                                    <span style="font-size: 7.5px; font-weight: 800; letter-spacing: 0.8px; color: #1E293B; text-transform: uppercase;">STRONGER NETWORK</span>
                                    <span style="color: #94A3B8; font-weight: 400; font-size: 8px;">|</span>
                                    <span style="font-size: 7.5px; font-weight: 800; letter-spacing: 0.8px; color: #1E293B; text-transform: uppercase;">BIGGER TOMORROW</span>
                                </div>
                            </div>

                            <!-- MOBILE NODE 01: COUNTRIES (Left) -->
                            <div class="img-callout-box framer-reveal framer-delay-1" style="top: 12.2%; left: 1.5%; text-align: left; width: 92px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #1D4ED8;">01</span>
                                    <span style="font-size: 8px; font-weight: 800; color: #0F172A; letter-spacing: -0.2px;">COUNTRIES</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 90px;">Expanding presence across global markets.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-1" style="top: 13.8%; left: 33.2%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-globe-americas" style="font-size: 12px; color: #1D4ED8; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">7+</div>
                                <div style="font-size: 5px; font-weight: 800; color: #475569; line-height: 1;">COUNTRIES</div>
                            </div>

                            <!-- MOBILE NODE 02: MEMBERS (Right) -->
                            <div class="img-callout-box framer-reveal framer-delay-2" style="top: 22.2%; left: 74.5%; text-align: left; width: 95px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #0284C7;">02</span>
                                    <span style="font-size: 8px; font-weight: 800; color: #0F172A; letter-spacing: -0.2px;">MEMBERS</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 92px;">Active community members.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-2" style="top: 23.8%; left: 54.8%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-users" style="font-size: 12px; color: #0284C7; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">10K+</div>
                                <div style="font-size: 5px; font-weight: 800; color: #475569; line-height: 1;">MEMBERS</div>
                            </div>

                            <!-- MOBILE NODE 03: PARTNERS (Left) -->
                            <div class="img-callout-box framer-reveal framer-delay-3" style="top: 31.8%; left: 1.5%; text-align: left; width: 92px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #0D9488;">03</span>
                                    <span style="font-size: 8px; font-weight: 800; color: #0F172A; letter-spacing: -0.2px;">PARTNERS</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 90px;">Valuable partnerships worldwide.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-3" style="top: 33.3%; left: 37.9%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-handshake" style="font-size: 12px; color: #0D9488; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">100+</div>
                                <div style="font-size: 5px; font-weight: 800; color: #475569; line-height: 1;">PARTNERS</div>
                            </div>

                            <!-- MOBILE NODE 04: GLOBAL VOLUME (Right) -->
                            <div class="img-callout-box framer-reveal framer-delay-4" style="top: 41.5%; left: 77.5%; text-align: left; width: 95px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #16A34A;">04</span>
                                    <span style="font-size: 8px; font-weight: 800; color: #0F172A; letter-spacing: -0.2px;">VOLUME</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 92px;">Stronger opportunities.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-4" style="top: 43.1%; left: 57.6%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-coins" style="font-size: 12px; color: #16A34A; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">$2M+</div>
                                <div style="font-size: 4.8px; font-weight: 800; color: #475569; line-height: 1;">GLOBAL VOLUME</div>
                            </div>

                            <!-- MOBILE NODE 05: SUCCESS RATE (Left) -->
                            <div class="img-callout-box framer-reveal framer-delay-5" style="top: 51.0%; left: 1.5%; text-align: left; width: 92px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #65A30D;">05</span>
                                    <span style="font-size: 7.5px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">SUCCESS RATE</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 90px;">Turning opportunities into results.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-5" style="top: 52.8%; left: 38.5%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-chart-line" style="font-size: 12px; color: #65A30D; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">98%</div>
                                <div style="font-size: 4.8px; font-weight: 800; color: #475569; line-height: 1;">SUCCESS RATE</div>
                            </div>

                            <!-- MOBILE NODE 06: MONTHLY GROWTH (Right) -->
                            <div class="img-callout-box framer-reveal framer-delay-6" style="top: 60.8%; left: 78.5%; text-align: left; width: 92px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #84CC16;">06</span>
                                    <span style="font-size: 8px; font-weight: 800; color: #0F172A; letter-spacing: -0.2px;">GROWTH</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 90px;">Bigger milestones.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-6" style="top: 62.4%; left: 60.1%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-calendar-alt" style="font-size: 12px; color: #84CC16; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">9.8%</div>
                                <div style="font-size: 4.8px; font-weight: 800; color: #475569; line-height: 1;">MONTHLY GROWTH</div>
                            </div>

                            <!-- MOBILE NODE 07: ACTIVE MEMBERS (Left) -->
                            <div class="img-callout-box framer-reveal framer-delay-5" style="top: 70.2%; left: 1.5%; text-align: left; width: 92px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #EAB308;">07</span>
                                    <span style="font-size: 7.5px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">ACTIVE MEMBERS</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 90px;">More people, more possibilities.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-5" style="top: 72.1%; left: 39.4%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-user-check" style="font-size: 12px; color: #EAB308; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">8.5K+</div>
                                <div style="font-size: 4.8px; font-weight: 800; color: #475569; line-height: 1;">ACTIVE MEMBERS</div>
                            </div>

                            <!-- MOBILE NODE 08: ACTIVE COMMUNITY (Right) -->
                            <div class="img-callout-box framer-reveal framer-delay-6" style="top: 79.8%; left: 77.5%; text-align: left; width: 95px;">
                                <div class="d-flex align-items-center gap-1 mb-0" style="line-height: 1.1;">
                                    <span style="font-size: 10px; font-weight: 900; color: #F97316;">08</span>
                                    <span style="font-size: 7.5px; font-weight: 800; color: #0F172A; letter-spacing: -0.2px;">COMMUNITY</span>
                                </div>
                                <p class="m-0" style="font-size: 6.8px; color: #475569; line-height: 1.15; max-width: 92px;">Stronger growth.</p>
                            </div>
                            <div class="img-circle-content framer-reveal framer-delay-6" style="top: 81.6%; left: 59.8%; transform: translate(-50%, -50%); text-align: center; width: 62px;">
                                <i class="fas fa-bullhorn" style="font-size: 12px; color: #F97316; margin-bottom: 1px; display: block;"></i>
                                <div style="font-size: 11px; font-weight: 900; color: #0F172A; line-height: 1;">85%+</div>
                                <div style="font-size: 4.8px; font-weight: 800; color: #475569; line-height: 1;">COMMUNITY</div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </section>

        <!-- ==================================================
             SECTION 03 — WHAT WE DO
             ID: #businesses
             ================================================== -->
        <section id="businesses" class="py-5 position-relative overflow-hidden" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="auto-container position-relative" style="z-index: 5;">
                
                <!-- Section Header (Exact Match to Screenshot) -->
                <div class="row align-items-end mb-4 pb-2">
                    <div class="col-lg-7 col-md-7 col-12">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span style="width: 24px; height: 3px; background: #967431; display: inline-block;"></span>
                            <span class="text-uppercase fw-bold" style="font-size: 13px; letter-spacing: 1.2px; color: #1E293B;">WHAT WE DO</span>
                        </div>
                        <h2 class="fw-black mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(26px, 3.2vw, 40px); font-weight: 800; color: #0F172A; line-height: 1.12; letter-spacing: -0.8px;">
                            DIVERSIFIED BUSINESS.<br>ONE ECOSYSTEM.
                        </h2>
                    </div>
                    <div class="col-lg-5 col-md-5 col-12 mt-3 mt-md-0">
                        <p class="mb-0" style="font-size: 14.5px; color: #475569; line-height: 1.6; max-width: 440px;">
                            We operate across multiple high-growth sectors, creating value for our partners, members and communities worldwide.
                        </p>
                    </div>
                </div>

                <!-- 6 Business Cards Grid (Exact Design to Screenshot with Framer Motion) -->
                <div class="row g-4">
                    
                    <!-- Vertical 1: FOREX TRADING -->
                    <div class="col-lg-4 col-md-6 col-sm-12 framer-reveal framer-delay-1">
                        <div class="h-100 transition-all overflow-hidden ananta-motion-card" style="background: rgba(253, 250, 244, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.28); border-radius: 16px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);">
                            <img src="assets/images/about/forex.png" alt="Forex Trading" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                            <div style="margin-top: -22px; margin-left: 20px; margin-bottom: 10px; position: relative; z-index: 2;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: #FFFDF8; border: 1.5px solid #E2D3B4;">
                                    <i class="fa fa-chart-line fs-5" style="color: #967431;"></i>
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Roboto', sans-serif; font-size: 16px; letter-spacing: 0.3px;">FOREX TRADING</h5>
                                <p class="mb-0" style="color: #64748B; font-family: 'Roboto', sans-serif; font-size: 13.5px; line-height: 1.5;">Market-focused trading and research.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vertical 2: REAL ESTATE -->
                    <div class="col-lg-4 col-md-6 col-sm-12 framer-reveal framer-delay-2">
                        <div class="h-100 transition-all overflow-hidden ananta-motion-card" style="background: rgba(253, 250, 244, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.28); border-radius: 16px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);">
                            <img src="assets/images/about/real.png" alt="Real Estate" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                            <div style="margin-top: -22px; margin-left: 20px; margin-bottom: 10px; position: relative; z-index: 2;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: #FFFDF8; border: 1.5px solid #E2D3B4;">
                                    <i class="fa fa-home fs-5" style="color: #967431;"></i>
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Roboto', sans-serif; font-size: 16px; letter-spacing: 0.3px;">REAL ESTATE</h5>
                                <p class="mb-0" style="color: #64748B; font-family: 'Roboto', sans-serif; font-size: 13.5px; line-height: 1.5;">Property and real-asset opportunities.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vertical 3: IPO & INVESTMENT -->
                    <div class="col-lg-4 col-md-6 col-sm-12 framer-reveal framer-delay-3">
                        <div class="h-100 transition-all overflow-hidden ananta-motion-card" style="background: rgba(253, 250, 244, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.28); border-radius: 16px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);">
                            <img src="assets/images/about/ipo.jpg" alt="IPO & Investment" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                            <div style="margin-top: -22px; margin-left: 20px; margin-bottom: 10px; position: relative; z-index: 2;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: #FFFDF8; border: 1.5px solid #E2D3B4;">
                                    <i class="fa fa-line-chart fs-5" style="color: #967431;"></i>
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Roboto', sans-serif; font-size: 16px; letter-spacing: 0.3px;">IPO & INVESTMENT</h5>
                                <p class="mb-0" style="color: #64748B; font-family: 'Roboto', sans-serif; font-size: 13.5px; line-height: 1.5;">Opportunities across selected market segments.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vertical 4: EV CHARGING POINT -->
                    <div class="col-lg-4 col-md-6 col-sm-12 framer-reveal framer-delay-4">
                        <div class="h-100 transition-all overflow-hidden ananta-motion-card" style="background: rgba(253, 250, 244, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.28); border-radius: 16px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);">
                            <img src="assets/images/about/ev.png" alt="EV Charging Point" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                            <div style="margin-top: -22px; margin-left: 20px; margin-bottom: 10px; position: relative; z-index: 2;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: #FFFDF8; border: 1.5px solid #E2D3B4;">
                                    <i class="fa fa-bolt fs-5" style="color: #967431;"></i>
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Roboto', sans-serif; font-size: 16px; letter-spacing: 0.3px;">EV CHARGING POINT</h5>
                                <p class="mb-0" style="color: #64748B; font-family: 'Roboto', sans-serif; font-size: 13.5px; line-height: 1.5;">Building the next generation of EV infrastructure.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vertical 5: TRAVEL & TOURISM -->
                    <div class="col-lg-4 col-md-6 col-sm-12 framer-reveal framer-delay-5">
                        <div class="h-100 transition-all overflow-hidden ananta-motion-card" style="background: rgba(253, 250, 244, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.28); border-radius: 16px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);">
                            <img src="assets/images/about/TRAVEL.png" alt="Travel & Tourism" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                            <div style="margin-top: -22px; margin-left: 20px; margin-bottom: 10px; position: relative; z-index: 2;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: #FFFDF8; border: 1.5px solid #E2D3B4;">
                                    <i class="fa fa-plane-departure fs-5" style="color: #967431;"></i>
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Roboto', sans-serif; font-size: 16px; letter-spacing: 0.3px;">TRAVEL & TOURISM</h5>
                                <p class="mb-1" style="color: #64748B; font-family: 'Roboto', sans-serif; font-size: 13.5px; line-height: 1.5;">Domestic and international travel solutions.</p>
                                <span class="d-block fw-bold" style="font-size: 12px; color: #1E293B; font-family: 'Roboto', sans-serif;">Agency - Saqlaini Travels</span>
                            </div>
                        </div>
                    </div>

                    <!-- Vertical 6: GOLD -->
                    <div class="col-lg-4 col-md-6 col-sm-12 framer-reveal framer-delay-6">
                        <div class="h-100 transition-all overflow-hidden ananta-motion-card" style="background: rgba(253, 250, 244, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.28); border-radius: 16px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);">
                            <img src="assets/images/about/gold.jpg" alt="Gold" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                            <div style="margin-top: -22px; margin-left: 20px; margin-bottom: 10px; position: relative; z-index: 2;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: #FFFDF8; border: 1.5px solid #E2D3B4;">
                                    <i class="fa fa-cubes fs-5" style="color: #967431;"></i>
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Roboto', sans-serif; font-size: 16px; letter-spacing: 0.3px;">GOLD</h5>
                                <p class="mb-0" style="color: #64748B; font-family: 'Roboto', sans-serif; font-size: 13.5px; line-height: 1.5;">Gold-focused business opportunities.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ==================================================
             SECTION 03.5 — INTERACTIVE 5-IMAGE CENTER ZOOM SHOWCASE
             ================================================== -->
        <section class="py-5 position-relative overflow-hidden ananta-slider-section" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-top: 1px solid rgba(197, 160, 89, 0.3); border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="container-fluid px-2 px-md-4 position-relative framer-reveal framer-delay-1" style="z-index: 5;">
                <div class="sec-title text-center mb-4">
                    <div class="ananta-badge mb-2" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(11, 37, 69, 0.25); border: 1px solid rgba(197, 160, 89, 0.4);">
                        <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> BUSINESS SHOWCASE
                    </div>
                    <h3 class="fw-extrabold text-uppercase mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0B2545 !important; font-size: 28px; letter-spacing: -0.5px;">
                        EXPLORE OUR <span style="color: #967431;">HIGH-GROWTH SECTORS</span>
                    </h3>
                </div>

                <!-- Swiper 5-Image Continuous Loop Slider -->
                <div class="swiper ananta-showcase-swiper">
                    <div class="swiper-wrapper align-items-center">
                        
                        <!-- Slide 1: Forex Trading -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/forex.png" alt="Forex Trading">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 01</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">FOREX TRADING</h5>
                                        <p class="showcase-detail-text mb-0">Institutional liquidity, currency market research & automated risk management strategies.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Real Estate -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/real.png" alt="Real Estate">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 02</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">REAL ESTATE</h5>
                                        <p class="showcase-detail-text mb-0">Premium commercial & residential property acquisitions and real-asset development.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: IPO & Investment -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/ipo.jpg" alt="IPO & Investment">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 03</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">IPO & INVESTMENT</h5>
                                        <p class="showcase-detail-text mb-0">Pre-IPO capital structuring, market entry support & high-growth equity opportunities.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 4: EV Charging Point -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/ev.png" alt="EV Charging Point">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 04</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">EV CHARGING POINT</h5>
                                        <p class="showcase-detail-text mb-0">Pioneering green energy electric vehicle charging network & smart power stations.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 5: Travel & Tourism -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/TRAVEL.png" alt="Travel & Tourism">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 05</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">TRAVEL & TOURISM</h5>
                                        <p class="showcase-detail-text mb-0">Saqlaini Travels agency offering luxury global tours, flight ticketing & travel packages.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 6: Gold Opportunities -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/gold.jpg" alt="Gold Opportunities">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 06</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">GOLD OPPORTUNITIES</h5>
                                        <p class="showcase-detail-text mb-0">Physical gold trade, bullion asset security & long-term commodity value growth.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 7: Forex Trading (Duplicate for Infinite Loop) -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/forex.png" alt="Forex Trading">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 01</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">FOREX TRADING</h5>
                                        <p class="showcase-detail-text mb-0">Institutional liquidity, currency market research & automated risk management strategies.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 8: Real Estate (Duplicate for Infinite Loop) -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/real.png" alt="Real Estate">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 02</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">REAL ESTATE</h5>
                                        <p class="showcase-detail-text mb-0">Premium commercial & residential property acquisitions and real-asset development.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 9: IPO & Investment (Duplicate for Infinite Loop) -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/ipo.jpg" alt="IPO & Investment">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 03</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">IPO & INVESTMENT</h5>
                                        <p class="showcase-detail-text mb-0">Pre-IPO capital structuring, market entry support & high-growth equity opportunities.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 10: EV Charging Point (Duplicate for Infinite Loop) -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/ev.png" alt="EV Charging Point">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 04</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">EV CHARGING POINT</h5>
                                        <p class="showcase-detail-text mb-0">Pioneering green energy electric vehicle charging network & smart power stations.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 11: Travel & Tourism (Duplicate for Infinite Loop) -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/TRAVEL.png" alt="Travel & Tourism">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 05</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">TRAVEL & TOURISM</h5>
                                        <p class="showcase-detail-text mb-0">Saqlaini Travels agency offering luxury global tours, flight ticketing & travel packages.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 12: Gold Opportunities (Duplicate for Infinite Loop) -->
                        <div class="swiper-slide">
                            <div class="showcase-card">
                                <div class="showcase-img-holder">
                                    <img src="assets/images/about/gold.jpg" alt="Gold Opportunities">
                                    <div class="showcase-overlay">
                                        <span class="badge text-dark fw-bold px-2 py-1 text-uppercase mb-1" style="background-color: #C5A059; font-size: 10px;">SECTOR 06</span>
                                        <h5 class="fw-bold text-white mb-1" style="font-size: 14px;">GOLD OPPORTUNITIES</h5>
                                        <p class="showcase-detail-text mb-0">Physical gold trade, bullion asset security & long-term commodity value growth.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Swiper Pagination Dots -->
                    <div class="swiper-pagination showcase-pagination mt-4"></div>
                </div>
            </div>
        </section>




        <!-- ==================================================
             SECTION 04 — WHY ANANTA
             ID: #partners
             ================================================== -->
        <section id="partners" class="py-5 position-relative overflow-hidden" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="auto-container position-relative" style="z-index: 5;">
                <div class="sec-title text-center mb-5 framer-reveal">
                    <div class="ananta-badge mb-3" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(11, 37, 69, 0.25); border: 1px solid rgba(197, 160, 89, 0.4);">
                        <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> WHY ANANTA
                    </div>
                    <h2 class="fw-extrabold display-4 mb-2" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; letter-spacing: 0.5px; font-weight: 800; text-transform: uppercase;">
                        WHY <span style="color: #0F5132;">CHOOSE</span> ANANTA
                    </h2>
                    <div class="d-flex align-items-center justify-content-center gap-3 my-3">
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, transparent, #0F5132);"></span>
                        <span style="display: inline-block; width: 7px; height: 7px; background: #C5A059; transform: rotate(45deg);"></span>
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, #0F5132, transparent);"></span>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Feature 01 -->
                    <div class="col-lg-6 col-md-6 col-sm-12 framer-reveal framer-delay-1">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start ananta-feature-card" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div class="display-4 fw-extrabold flex-shrink-0" style="font-family: var(--ananta-font-heading); color: #0B2545; line-height: 1;">
                                01
                            </div>
                            <div>
                                <h4 class="fw-bold mb-2" style="font-family: var(--ananta-font-heading);">
                                    <span style="color: #0B2545;">01 — </span><span style="color: #0F5132;">DIVERSIFIED</span>
                                </h4>
                                <p class="mb-0" style="color: #475569; font-size: 15.5px; line-height: 1.6;">
                                    Multiple business verticals under one growing ecosystem.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 02 -->
                    <div class="col-lg-6 col-md-6 col-sm-12 framer-reveal framer-delay-2">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start ananta-feature-card" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div class="display-4 fw-extrabold flex-shrink-0" style="font-family: var(--ananta-font-heading); color: #0F5132; line-height: 1;">
                                02
                            </div>
                            <div>
                                <h4 class="fw-bold mb-2" style="font-family: var(--ananta-font-heading);">
                                    <span style="color: #0F5132;">02 — </span><span style="color: #0B2545;">PROFESSIONAL</span>
                                </h4>
                                <p class="mb-0" style="color: #475569; font-size: 15.5px; line-height: 1.6;">
                                    Structured operations supported by dedicated teams.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 03 -->
                    <div class="col-lg-6 col-md-6 col-sm-12 framer-reveal framer-delay-3">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start ananta-feature-card" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div class="display-4 fw-extrabold flex-shrink-0" style="font-family: var(--ananta-font-heading); color: #0B2545; line-height: 1;">
                                03
                            </div>
                            <div>
                                <h4 class="fw-bold mb-2" style="font-family: var(--ananta-font-heading);">
                                    <span style="color: #0B2545;">03 — </span><span style="color: #0F5132;">INNOVATIVE</span>
                                </h4>
                                <p class="mb-0" style="color: #475569; font-size: 15.5px; line-height: 1.6;">
                                    Focused on emerging markets, technology and new opportunities.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 04 -->
                    <div class="col-lg-6 col-md-6 col-sm-12 framer-reveal framer-delay-4">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start ananta-feature-card" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div class="display-4 fw-extrabold flex-shrink-0" style="font-family: var(--ananta-font-heading); color: #0F5132; line-height: 1;">
                                04
                            </div>
                            <div>
                                <h4 class="fw-bold mb-2" style="font-family: var(--ananta-font-heading);">
                                    <span style="color: #0F5132;">04 — </span><span style="color: #0B2545;">CONNECTED</span>
                                </h4>
                                <p class="mb-0" style="color: #475569; font-size: 15.5px; line-height: 1.6;">
                                    A growing network of members, partners and businesses.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================
             SECTION 05 — OUR VISION
             ID: #vision
             ================================================== -->
        <section id="vision" class="py-5 position-relative overflow-hidden" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="auto-container position-relative py-3 framer-reveal" style="z-index: 5;">
                <div class="p-5 rounded-5 text-center position-relative overflow-hidden shadow-lg ananta-vision-box" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 30px !important;">
                    
                    <div class="ananta-badge mb-3" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> OUR VISION
                    </div>

                    <h1 class="fw-extrabold display-4 mb-4 text-white" style="font-family: var(--ananta-font-heading); letter-spacing: 0.5px;">
                        <span style="color: #FFFFFF;">BUILD.</span> 
                        <span style="color: #C5A059;">CONNECT.</span> 
                        <span style="color: #FFFFFF;">INNOVATE.</span> 
                        <span style="color: #C5A059;">GROW.</span>
                    </h1>
                    <p class="fs-4 mx-auto mb-0" style="color: #E2E8F0; max-width: 820px; line-height: 1.7; font-weight: 400;">
                        Our vision is to build a modern and diversified business ecosystem that connects people, businesses, technology and emerging opportunities across markets.
                    </p>
                </div>
            </div>
        <!-- ==================================================
             SECTION 05.5 — WHAT OUR USERS SAY (CUSTOMER FEEDBACK)
             ID: #testimonials
             ================================================== -->
        <section id="testimonials" class="py-5 position-relative overflow-hidden ananta-testimonials-section" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="container-fluid position-relative py-3 framer-reveal" style="z-index: 5;">
                
                <!-- Section Header -->
                <div class="sec-title text-center mb-5">
                    <div class="ananta-badge mb-3" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(11, 37, 69, 0.25); border: 1px solid rgba(197, 160, 89, 0.4);">
                        <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> CUSTOMER FEEDBACK
                    </div>
                    <h2 class="fw-extrabold display-4 mb-2" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; letter-spacing: 0.5px; font-weight: 800; text-transform: uppercase;">
                        WHAT OUR <span style="color: #0F5132;">USERS SAY</span>
                    </h2>
                    <p class="fs-5 mx-auto" style="color: #475569; max-width: 650px; font-weight: 400; line-height: 1.5;">Real experiences and reviews from our valued partners, members, and investors across the world.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3 my-3">
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, transparent, #0F5132);"></span>
                        <span style="display: inline-block; width: 7px; height: 7px; background: #C5A059; transform: rotate(45deg);"></span>
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, #0F5132, transparent);"></span>
                    </div>
                </div>

                <!-- Marquee Wrapper with Soft Edge Fade Mask -->
                <div class="testimonial-marquee-wrapper">
                    
                    <!-- ROW 1: Right-to-Left Infinite Scroll -->
                    <div class="testimonial-marquee-container mb-4">
                        <div class="marquee-track marquee-track-left">
                            
                            <!-- Card 1 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80" alt="Sophia Patel">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Sophia Patel</h6>
                                        <span class="text-muted" style="font-size: 12px;">Equity Investor • Dubai</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Ananta's diversified business ecosystem provided me with unprecedented transparency and steady capital growth!"</p>
                            </div>

                            <!-- Card 2 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80" alt="Rajesh Kumar">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Rajesh Kumar</h6>
                                        <span class="text-muted" style="font-size: 12px;">Forex Partner • Mumbai</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"The institutional research and trading strategies are top-notch. Truly professional team and execution."</p>
                            </div>

                            <!-- Card 3 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80" alt="Ananya Roy">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Ananya Roy</h6>
                                        <span class="text-muted" style="font-size: 12px;">EV Network Partner • Delhi</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Extremely thrilled with the EV charging setup. Smooth deployment and great support from Ananta team."</p>
                            </div>

                            <!-- Card 4 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80" alt="Vikram Sharma">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Vikram Sharma</h6>
                                        <span class="text-muted" style="font-size: 12px;">Real Estate Client • Bangalore</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Secured premium commercial land through Ananta. Honest advice and completely transparent deal."</p>
                            </div>

                            <!-- Card 5 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80" alt="Elena Rostova">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Elena Rostova</h6>
                                        <span class="text-muted" style="font-size: 12px;">Traveler • London</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Saqlaini Travels booked our luxury tour seamlessly. Best itinerary management and support throughout!"</p>
                            </div>

                            <!-- Duplicate Set for Seamless Continuous Loop -->
                            <!-- Card 1 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80" alt="Sophia Patel">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Sophia Patel</h6>
                                        <span class="text-muted" style="font-size: 12px;">Equity Investor • Dubai</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Ananta's diversified business ecosystem provided me with unprecedented transparency and steady capital growth!"</p>
                            </div>

                            <!-- Card 2 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80" alt="Rajesh Kumar">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Rajesh Kumar</h6>
                                        <span class="text-muted" style="font-size: 12px;">Forex Partner • Mumbai</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"The institutional research and trading strategies are top-notch. Truly professional team and execution."</p>
                            </div>

                            <!-- Card 3 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80" alt="Ananya Roy">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Ananya Roy</h6>
                                        <span class="text-muted" style="font-size: 12px;">EV Network Partner • Delhi</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Extremely thrilled with the EV charging setup. Smooth deployment and great support from Ananta team."</p>
                            </div>

                            <!-- Card 4 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80" alt="Vikram Sharma">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Vikram Sharma</h6>
                                        <span class="text-muted" style="font-size: 12px;">Real Estate Client • Bangalore</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Secured premium commercial land through Ananta. Honest advice and completely transparent deal."</p>
                            </div>

                            <!-- Card 5 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80" alt="Elena Rostova">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Elena Rostova</h6>
                                        <span class="text-muted" style="font-size: 12px;">Traveler • London</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Saqlaini Travels booked our luxury tour seamlessly. Best itinerary management and support throughout!"</p>
                            </div>

                        </div>
                    </div>

                    <!-- ROW 2: Left-to-Right Infinite Scroll -->
                    <div class="testimonial-marquee-container">
                        <div class="marquee-track marquee-track-right">
                            
                            <!-- Card 6 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80" alt="Marcus Vance">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Marcus Vance</h6>
                                        <span class="text-muted" style="font-size: 12px;">IPO Investor • Singapore</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Participated in Pre-IPO capital structuring through Ananta. High professionalism and timely updates."</p>
                            </div>

                            <!-- Card 7 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=120&auto=format&fit=crop&q=80" alt="Neha Gupta">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Neha Gupta</h6>
                                        <span class="text-muted" style="font-size: 12px;">Gold Bullion Client • Pune</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Physical gold asset management with 100% security and verified purity. Highly recommended!"</p>
                            </div>

                            <!-- Card 8 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=120&auto=format&fit=crop&q=80" alt="David Miller">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">David Miller</h6>
                                        <span class="text-muted" style="font-size: 12px;">Corporate Member • Sydney</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"The ecosystem model connects multiple revenue streams seamlessly. Super impressed by the vision."</p>
                            </div>

                            <!-- Card 9 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=120&auto=format&fit=crop&q=80" alt="Priya Singh">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Priya Singh</h6>
                                        <span class="text-muted" style="font-size: 12px;">Franchise Partner • Kolkata</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Excellent onboarding and dedicated relationship management. Glad to be a part of Ananta."</p>
                            </div>

                            <!-- Card 10 -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=120&auto=format&fit=crop&q=80" alt="Arjun Mehta">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Arjun Mehta</h6>
                                        <span class="text-muted" style="font-size: 12px;">Fintech Investor • Ahmedabad</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Innovating every single quarter. Ananta Multi Trade is setting new standards in diversified trade."</p>
                            </div>

                            <!-- Duplicate Set for Seamless Continuous Loop -->
                            <!-- Card 6 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80" alt="Marcus Vance">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Marcus Vance</h6>
                                        <span class="text-muted" style="font-size: 12px;">IPO Investor • Singapore</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Participated in Pre-IPO capital structuring through Ananta. High professionalism and timely updates."</p>
                            </div>

                            <!-- Card 7 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=120&auto=format&fit=crop&q=80" alt="Neha Gupta">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Neha Gupta</h6>
                                        <span class="text-muted" style="font-size: 12px;">Gold Bullion Client • Pune</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Physical gold asset management with 100% security and verified purity. Highly recommended!"</p>
                            </div>

                            <!-- Card 8 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=120&auto=format&fit=crop&q=80" alt="David Miller">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">David Miller</h6>
                                        <span class="text-muted" style="font-size: 12px;">Corporate Member • Sydney</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"The ecosystem model connects multiple revenue streams seamlessly. Super impressed by the vision."</p>
                            </div>

                            <!-- Card 9 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=120&auto=format&fit=crop&q=80" alt="Priya Singh">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Priya Singh</h6>
                                        <span class="text-muted" style="font-size: 12px;">Franchise Partner • Kolkata</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Excellent onboarding and dedicated relationship management. Glad to be a part of Ananta."</p>
                            </div>

                            <!-- Card 10 Duplicate -->
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-holder">
                                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=120&auto=format&fit=crop&q=80" alt="Arjun Mehta">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: #0B2545;">Arjun Mehta</h6>
                                        <span class="text-muted" style="font-size: 12px;">Fintech Investor • Ahmedabad</span>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                                <p class="feedback-text mb-0">"Innovating every single quarter. Ananta Multi Trade is setting new standards in diversified trade."</p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ==================================================
             SECTION 06 — FAQ & CONTACT US
             ID: #contact
             ================================================== -->

        <section id="contact" class="py-5 position-relative" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; color: #0F172A; border-top: 1px solid rgba(197, 160, 89, 0.3);">
            
            <div class="container-fluid container-xl position-relative py-3">
                
                <!-- Section Header -->
                <div class="sec-title text-center mb-5 framer-reveal">
                    <div class="ananta-badge mb-3" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(11, 37, 69, 0.25); border: 1px solid rgba(197, 160, 89, 0.4);">
                        <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> GET IN TOUCH & FAQ
                    </div>
                    <h2 class="fw-extrabold display-5 mb-2" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; letter-spacing: -0.5px;">
                        FREQUENTLY ASKED <span style="color: #0F5132;">QUESTIONS</span> & CONTACT
                    </h2>
                    <p class="fs-5 mx-auto" style="color: #475569; max-width: 680px; font-weight: 400; line-height: 1.5;">Find instant answers to common questions on the left, or send us a direct message using the contact form on the right.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3 my-3">
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, transparent, #0F5132);"></span>
                        <span style="display: inline-block; width: 7px; height: 7px; background: #C5A059; transform: rotate(45deg);"></span>
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, #0F5132, transparent);"></span>
                    </div>
                </div>

                <!-- 2-Column Layout: Left FAQ, Right Contact Form -->
                <div class="row g-4 align-items-stretch">
                    
                    <!-- LEFT SIDE: FAQ ACCORDION -->
                    <div class="col-lg-6 col-md-12 framer-reveal framer-delay-1">
                        <div class="p-4 p-md-4 rounded-4 h-100" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); box-shadow: 0 10px 30px rgba(11, 37, 69, 0.05); border-radius: 24px !important;">
                            <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 46px; height: 46px; background: rgba(11, 37, 69, 0.08); border: 1px solid rgba(11, 37, 69, 0.15);">
                                    <i class="fa fa-question-circle fs-4" style="color: #0B2545;"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-0" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 20px;">Frequently Asked Questions</h3>
                                    <span style="color: #64748B; font-size: 13.5px; font-weight: 500;">Fast Solutions & Guidance</span>
                                </div>
                            </div>

                            <!-- Accordion List -->
                            <div class="accordion" id="anantaContactFaq">
                                
                                <!-- FAQ 1 (OPEN STATE) -->
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); border-radius: 14px !important;">
                                    <h2 class="accordion-header" id="faqHeadOne">
                                        <button class="accordion-button fw-bold py-3 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne" style="background: #F8FAFC; color: #0B2545 !important; font-size: 15px; box-shadow: none;">
                                            <i class="fa fa-question-circle me-2" style="color: #0B2545;"></i> What is Ananta Multi Trade Private Limited?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadOne" data-bs-parent="#anantaContactFaq">
                                        <div class="accordion-body px-4 py-3" style="color: #334155; background: #FFFFFF; font-size: 14px; line-height: 1.65; border-top: 1px solid #E2E8F0;">
                                            Ananta Multi Trade Private Limited is a modern corporate fintech enterprise committed to providing structured financial strategies, multi-trade services, institutional solutions, and wealth management opportunities.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); border-radius: 14px !important;">
                                    <h2 class="accordion-header" id="faqHeadTwo">
                                        <button class="accordion-button collapsed fw-bold py-3 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo" style="background: #F8FAFC; color: #0B2545 !important; font-size: 15px; box-shadow: none;">
                                            <i class="fa fa-shield-halved me-2" style="color: #0F5132;"></i> How secure and reliable are Ananta's services?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadTwo" data-bs-parent="#anantaContactFaq">
                                        <div class="accordion-body px-4 py-3" style="color: #334155; background: #FFFFFF; font-size: 14px; line-height: 1.65; border-top: 1px solid #E2E8F0;">
                                            We operate under strict compliance standards with robust risk management frameworks, automated monitoring systems, and dedicated multi-tier support to ensure maximal transparency and reliability.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); border-radius: 14px !important;">
                                    <h2 class="accordion-header" id="faqHeadThree">
                                        <button class="accordion-button collapsed fw-bold py-3 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree" style="background: #F8FAFC; color: #0B2545 !important; font-size: 15px; box-shadow: none;">
                                            <i class="fa fa-chart-line me-2" style="color: #0B2545;"></i> What market verticals do you operate in?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadThree" data-bs-parent="#anantaContactFaq">
                                        <div class="accordion-body px-4 py-3" style="color: #334155; background: #FFFFFF; font-size: 14px; line-height: 1.65; border-top: 1px solid #E2E8F0;">
                                            Our strategic domain spans across equities, forex, commodities, structured corporate trading, automated fintech analytics, and strategic partnership ecosystems.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); border-radius: 14px !important;">
                                    <h2 class="accordion-header" id="faqHeadFour">
                                        <button class="accordion-button collapsed fw-bold py-3 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour" style="background: #F8FAFC; color: #0B2545 !important; font-size: 15px; box-shadow: none;">
                                            <i class="fa fa-clock me-2" style="color: #0F5132;"></i> What are your operational hours & support availability?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadFour" data-bs-parent="#anantaContactFaq">
                                        <div class="accordion-body px-4 py-3" style="color: #334155; background: #FFFFFF; font-size: 14px; line-height: 1.65; border-top: 1px solid #E2E8F0;">
                                            Our market operations run Monday 9:00 AM to Saturday 11:59 PM (GMT). Client support and direct query resolution is available via our online message desk.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="accordion-item rounded-3 overflow-hidden" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); border-radius: 14px !important;">
                                    <h2 class="accordion-header" id="faqHeadFive">
                                        <button class="accordion-button collapsed fw-bold py-3 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFive" aria-expanded="false" aria-controls="faqCollapseFive" style="background: #F8FAFC; color: #0B2545 !important; font-size: 15px; box-shadow: none;">
                                            <i class="fa fa-paper-plane me-2" style="color: #0B2545;"></i> How can I send an inquiry or become a partner?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseFive" class="accordion-collapse collapse" aria-labelledby="faqHeadFive" data-bs-parent="#anantaContactFaq">
                                        <div class="accordion-body px-4 py-3" style="color: #334155; background: #FFFFFF; font-size: 14px; line-height: 1.65; border-top: 1px solid #E2E8F0;">
                                            Simply fill out the contact form on the right with your full name, email, phone number, and query details. Our executive team will get back to you promptly.
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: CONTACT FORM -->
                    <div class="col-lg-6 col-md-12 framer-reveal framer-delay-2">
                        <div class="p-4 p-md-4 rounded-4 h-100" style="background: rgba(255, 255, 255, 0.55); backdrop-filter: blur(8px); border: 1px solid rgba(197, 160, 89, 0.3); box-shadow: 0 10px 30px rgba(11, 37, 69, 0.05); border-radius: 24px !important;">
                            
                            <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 46px; height: 46px; background: rgba(15, 81, 50, 0.08); border: 1px solid rgba(15, 81, 50, 0.15);">
                                    <i class="fa fa-envelope-open-text fs-4" style="color: #0F5132;"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-0" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 20px;">Send Us A Message</h3>
                                    <span style="color: #64748B; font-size: 13.5px; font-weight: 500;">We'd Love To Hear From You</span>
                                </div>
                            </div>

                            <form method="post" action="contact.php" class="contact-form">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="fw-bold mb-2" style="font-size: 13.5px; color: #0F172A;">Your Full Name <span style="color: #DC2626;">*</span></label>
                                            <input type="text" name="username" class="form-control rounded-3 py-3 px-3 fw-medium" placeholder="Your Full Name" required style="background: #F8FAFC !important; border: 1px solid #CBD5E1 !important; color: #0F172A !important; border-radius: 12px !important;">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="fw-bold mb-2" style="font-size: 13.5px; color: #0F172A;">Your Email <span style="color: #DC2626;">*</span></label>
                                            <input type="email" name="email" class="form-control rounded-3 py-3 px-3 fw-medium" placeholder="Your Email *" required style="background: #F8FAFC !important; border: 1px solid #CBD5E1 !important; color: #0F172A !important; border-radius: 12px !important;">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="fw-bold mb-2" style="font-size: 13.5px; color: #0F172A;">Your Phone <span style="color: #DC2626;">*</span></label>
                                            <input type="text" name="phone" class="form-control rounded-3 py-3 px-3 fw-medium" placeholder="Your Phone" required style="background: #F8FAFC !important; border: 1px solid #CBD5E1 !important; color: #0F172A !important; border-radius: 12px !important;">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="fw-bold mb-2" style="font-size: 13.5px; color: #0F172A;">Address / Message Details <span style="color: #DC2626;">*</span></label>
                                            <textarea name="address" rows="3" class="form-control rounded-3 py-3 px-3 fw-medium" placeholder="Address / Message Details *" required style="background: #F8FAFC !important; border: 1px solid #CBD5E1 !important; color: #0F172A !important; border-radius: 12px !important;"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4 text-center">
                                        <button type="submit" class="btn w-100 py-3 rounded-3 text-white fw-bold fs-6 shadow-sm d-flex align-items-center justify-content-center gap-2" name="submit-form" style="background: linear-gradient(90deg, #0B2545 0%, #0F5132 100%); border: none; border-radius: 14px !important; letter-spacing: 0.3px;">
                                            Send Message <i class="fa fa-paper-plane ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>

        </section>
    </div>

    <!-- Scroll Reveal & Swiper Slider Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll Reveal Observer
        const revealElements = document.querySelectorAll('.framer-reveal');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        revealElements.forEach(el => observer.observe(el));

        // Swiper 5-Image Endless Circulation Slider (Always 5 slides on desktop, 2s interval)
        if (document.querySelector('.ananta-showcase-swiper')) {
            const showcaseSwiper = new Swiper('.ananta-showcase-swiper', {
                slidesPerView: 1.5,
                spaceBetween: 14,
                centeredSlides: true,
                loop: true,
                loopAdditionalSlides: 5,
                speed: 800,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                breakpoints: {
                    576: {
                        slidesPerView: 2.5,
                        spaceBetween: 16
                    },
                    768: {
                        slidesPerView: 3.5,
                        spaceBetween: 18
                    },
                    1200: {
                        slidesPerView: 5,
                        spaceBetween: 20
                    }
                },
                pagination: {
                    el: '.showcase-pagination',
                    clickable: true
                }
            });
        }
    });
    </script>




    <?php include "common/footer.php"; ?>
</body>
</html>


