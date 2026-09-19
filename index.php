<?php include "common/header.php"; ?>
        <!-- main-header end -->
<style>
.inner-box.image-only {
    width: 100%;
    height: 500px;   /* apne hisaab se */
    overflow: hidden;
    border-radius: 10px;
}

.inner-box.image-only img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* 🔥 poora div cover */
    display: block;
}

.inner-box.image-only {
    text-align: center;   /* image center rahe */
}

.responsive-img {
    max-width: 100%;
    height: auto;
}/* Mobile View Background & Section Visibility Guarantees */
@media (max-width: 991px) {
    body, .boxed_wrapper {
        background: transparent !important;
    }

    #about, #businesses, #partners, #vision {
        background: #FFFFFF !important;
        background-color: #FFFFFF !important;
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    #contact {
        background: transparent !important;
        background-color: transparent !important;
        position: relative !important;
        z-index: 10 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        padding-top: 40px !important;
        padding-bottom: 60px !important;
        min-height: auto !important;
    }

    #contact .container-fluid,
    #contact .row,
    #contact .col-lg-6 {
        display: block !important;
        width: 100% !important;
        visibility: visible !important;
    }
}

/* Mobile specific centering & container padding for Section #contact */
@media (max-width: 768px) {
    #contact .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
        box-sizing: border-box !important;
    }
    #contact .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
    }
    #contact .col-lg-6 {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
}

/* Mobile specific styling for Section 02 (#about) 3 stats per line */
@media (max-width: 768px) {
    #about .row > [class*="col-"] {
        padding-left: 3px !important;
        padding-right: 3px !important;
    }
    #about .p-4 {
        padding: 8px 2px !important;
    }
    #about .mb-3.d-flex {
        height: 45px !important;
        margin-bottom: 4px !important;
    }
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
        <!-- Nestify-Inspired Ananta Fintech Hero Section with Responsive Desktop/Mobile Building Background -->
        <section id="home" class="ananta-hero-section position-relative overflow-hidden">
            <div class="ananta-hero-bg-holder"></div>
            <div class="auto-container position-relative" style="z-index: 5;">
                <div class="row align-items-center">
                                       <!-- Left Hero Copy & Actions -->
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-5 mb-lg-0">
                        <div class="ananta-badge mb-3" style="background: rgba(11, 94, 215, 0.12); border: 1px solid rgba(11, 94, 215, 0.35); color: #38BDF8; padding: 6px 16px; border-radius: 100px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px; letter-spacing: 0.5px;">
                            <span class="ananta-badge-dot" style="background: #22A447;"></span> ANANTA MULTI TRADE PRIVATE LIMITED
                        </div>

                        <h1 class="ananta-hero-title text-white fw-extrabold display-4 mb-3" style="font-family: var(--ananta-font-heading); line-height: 1.15;">
                            ONE VISION.<br />
                            <span style="color: #0B5ED7;">MULTIPLE</span> <span style="color: #22A447;">OPPORTUNITIES.</span>
                        </h1>

                        <h5 class="fw-bold mb-3" style="color: #E2E8F0; line-height: 1.5; font-size: 19px;">
                            Building a diversified business ecosystem across markets, infrastructure, real estate and emerging industries.
                        </h5>

                        <p class="ananta-hero-desc mb-4" style="color: #94A3B8; font-size: 15.5px; line-height: 1.7;">
                            Ananta Multi Trade Private Limited is creating a growing network of businesses, partners and opportunities with a focus on innovation, professional operations and long-term growth.
                        </p>

                        <div class="d-flex flex-wrap gap-3 align-items-center mb-4">
                            <a href="#what-we-do" class="ananta-btn-primary" style="background: linear-gradient(135deg, #0B5ED7 0%, #22A447 100%) !important; border: none !important;">
                                EXPLORE ANANTA <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                            <a href="<?php echo $hmregister; ?>" class="ananta-btn-secondary" style="border-color: rgba(34, 164, 71, 0.4) !important;">
                                GET STARTED <i class="fa fa-user-plus ms-1" style="color: #22A447;"></i>
                            </a>
                        </div>

                        <!-- Mini Trust Proof Badges (Hard White Text & Green Shade Accent) -->
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary border-opacity-25 mb-4 flex-wrap gap-2">
                            <div>
                                <h4 class="text-white fw-bold mb-0" style="font-family: var(--ananta-font-heading); color: #FFFFFF !important;">100%</h4>
                                <small style="color: #7ED321; font-weight: 600;">Secured Programs</small>
                            </div>
                            <div class="vr bg-secondary opacity-50" style="height: 28px;"></div>
                            <div>
                                <h4 class="text-white fw-bold mb-0" style="font-family: var(--ananta-font-heading); color: #FFFFFF !important;">3 - 4%</h4>
                                <small style="color: #7ED321; font-weight: 600;">Monthly Yield Generation</small>
                            </div>
                            <div class="vr bg-secondary opacity-50" style="height: 28px;"></div>
                            <div>
                                <h4 class="text-white fw-bold mb-0" style="font-family: var(--ananta-font-heading); color: #FFFFFF !important;">Multi-Asset</h4>
                                <small style="color: #7ED321; font-weight: 600;">Gold, Plots & Scooters</small>
                            </div>
                        </div>

                        <!-- 4 Asset Data Cards (Clean 2x2 Grid Layout, Hard White Text & Green Accents - No Collisions) -->
                        <div class="row g-2 p-3 rounded-4 mb-2 w-100 m-0" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px);">
                            <!-- Card 1: Stock Portfolio -->
                            <div class="col-6 p-2 border-end border-bottom border-white border-opacity-10">
                                <small class="text-uppercase d-block fw-bold" style="font-size: 9.5px; color: #94A3B8; letter-spacing: 0.5px;">Stock Portfolio Strategy</small>
                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                    <span class="fw-bold me-1" id="hero-stat-val-1" style="font-size: 15px; color: #FFFFFF !important; text-shadow: 0 0 10px rgba(255,255,255,0.2);">₹ 10,52,280</span>
                                    <span class="badge fw-bold" id="hero-stat-chg-1" style="background: rgba(255, 95, 86, 0.15); color: #FF5F56; font-size: 10px; border: 1px solid rgba(255, 95, 86, 0.3);">-4.39% Yield <i class="fa fa-caret-down"></i></span>
                                </div>
                            </div>

                            <!-- Card 2: Crypto Mining Asset -->
                            <div class="col-6 p-2 border-bottom border-white border-opacity-10">
                                <small class="text-uppercase d-block fw-bold" style="font-size: 9.5px; color: #94A3B8; letter-spacing: 0.5px;">Crypto Mining Asset</small>
                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                    <span class="fw-bold me-1" id="hero-stat-val-2" style="font-size: 15px; color: #FFFFFF !important; text-shadow: 0 0 10px rgba(255,255,255,0.2);">Active Node</span>
                                    <span class="badge fw-bold" id="hero-stat-chg-2" style="background: rgba(126, 211, 33, 0.15); color: #7ED321; font-size: 10px; border: 1px solid rgba(126, 211, 33, 0.3);"><i class="fa fa-server me-1"></i> High-Efficiency</span>
                                </div>
                            </div>

                            <!-- Card 3: Tejas Gold Package -->
                            <div class="col-6 p-2 border-end border-white border-opacity-10">
                                <small class="text-uppercase d-block fw-bold" style="font-size: 9.5px; color: #94A3B8; letter-spacing: 0.5px;">Tejas Gold Package</small>
                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                    <span class="fw-bold me-1" id="hero-stat-val-3" style="font-size: 15px; color: #FFFFFF !important; text-shadow: 0 0 10px rgba(255,255,255,0.2);">40% Backed</span>
                                    <span class="badge fw-bold" id="hero-stat-chg-3" style="background: rgba(126, 211, 33, 0.15); color: #7ED321; font-size: 10px; border: 1px solid rgba(126, 211, 33, 0.3);"><i class="fa fa-shield-alt me-1"></i> Gold Protected</span>
                                </div>
                            </div>

                            <!-- Card 4: Real Estate Security -->
                            <div class="col-6 p-2">
                                <small class="text-uppercase d-block fw-bold" style="font-size: 9.5px; color: #94A3B8; letter-spacing: 0.5px;">Real Estate Security</small>
                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                    <span class="fw-bold me-1" id="hero-stat-val-4" style="font-size: 15px; color: #FFFFFF !important; text-shadow: 0 0 10px rgba(255,255,255,0.2);">70-100 Gaj</span>
                                    <span class="badge fw-bold" id="hero-stat-chg-4" style="background: rgba(126, 211, 33, 0.15); color: #7ED321; font-size: 10px; border: 1px solid rgba(126, 211, 33, 0.3);"><i class="fa fa-building me-1"></i> Plot Agreement</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Fully Visible Transparent Exchange List (No Scroll) -->
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="p-0" style="background: transparent !important; border: none !important; box-shadow: none !important;">

                            <!-- Transparent Borderless Market Data & Real-time Graph Widget -->
                            <div class="p-3 rounded-4" style="background: transparent !important; border: none !important; box-shadow: none !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-10">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa fa-globe text-info"></i>
                                        <span class="fw-bold" style="font-size: 13px; letter-spacing: 0.8px; color: #FFFFFF !important; text-shadow: 0 0 8px rgba(255,255,255,0.3);">GLOBAL EXCHANGES & LIVE INDIAN MARKET</span>
                                    </div>
                                    <span class="badge text-dark fw-bold" style="font-size: 9px; background: #7ED321;"><i class="fa fa-wifi me-1"></i> LIVE TICKER</span>
                                </div>

                                <!-- Fully Visible List (No Scroll Bar) -->
                                <div class="d-flex flex-column gap-1" id="exchangeMarketList" style="overflow: visible !important;">
                                    
                                    <!-- 1. Nasdaq (US) -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.nasdaq.com/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Nasdaq</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">US • $43.58T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-1" d="M0,18 L20,12 L40,16 L60,8 L80,14 L100,2" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-1" style="font-size: 11.5px; color: #FFFFFF !important;">$43.58T</span>
                                            <small class="fw-bold" id="ex-chg-1" style="color: #7ED321; font-size: 9.5px;">+1.45% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 2. NYSE (US) -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.nyse.com/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 NYSE</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">US • $33.29T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-2" d="M0,15 L20,18 L40,10 L60,14 L80,6 L100,3" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-2" style="font-size: 11.5px; color: #FFFFFF !important;">$33.29T</span>
                                            <small class="fw-bold" id="ex-chg-2" style="color: #7ED321; font-size: 9.5px;">+0.92% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 3. BSE India -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.04); border-left: 2px solid #7ED321;">
                                        <div style="width: 35%;">
                                            <a href="https://www.bseindia.com/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">🇮🇳 BSE India</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">India • $5.14T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-3" d="M0,20 L20,14 L40,17 L60,9 L80,11 L100,1" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-3" style="font-size: 11.5px; color: #FFFFFF !important;">$5.14T</span>
                                            <small class="fw-bold" id="ex-chg-3" style="color: #7ED321; font-size: 9.5px;">+1.88% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 4. NSE India -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.04); border-left: 2px solid #7ED321;">
                                        <div style="width: 35%;">
                                            <a href="https://www.nseindia.com/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">🇮🇳 NSE India</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">India • $5.12T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-4" d="M0,17 L20,10 L40,14 L60,6 L80,12 L100,2" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-4" style="font-size: 11.5px; color: #FFFFFF !important;">$5.12T</span>
                                            <small class="fw-bold" id="ex-chg-4" style="color: #7ED321; font-size: 9.5px;">+1.75% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 5. Shanghai SSE -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://english.sse.com.cn/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Shanghai SSE</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">China • $9.97T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-5" d="M0,12 L20,16 L40,8 L60,14 L80,9 L100,5" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-5" style="font-size: 11.5px; color: #FFFFFF !important;">$9.97T</span>
                                            <small class="fw-bold" id="ex-chg-5" style="color: #7ED321; font-size: 9.5px;">+0.64% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 6. Japan JPX -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.jpx.co.jp/english" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Japan JPX</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">Japan • $8.72T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-6" d="M0,19 L20,11 L40,15 L60,7 L80,11 L100,1" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-6" style="font-size: 11.5px; color: #FFFFFF !important;">$8.72T</span>
                                            <small class="fw-bold" id="ex-chg-6" style="color: #7ED321; font-size: 9.5px;">+1.12% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 7. Euronext -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.euronext.com/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Euronext</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">Europe • $8.33T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-7" d="M0,14 L20,10 L40,16 L60,8 L80,13 L100,2" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-7" style="font-size: 11.5px; color: #FFFFFF !important;">$8.33T</span>
                                            <small class="fw-bold" id="ex-chg-7" style="color: #7ED321; font-size: 9.5px;">+0.78% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 8. Shenzhen SZSE -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.szse.cn/English" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Shenzhen SZSE</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">China • $7.30T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-8" d="M0,16 L20,12 L40,14 L60,8 L80,10 L100,4" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-8" style="font-size: 11.5px; color: #FFFFFF !important;">$7.30T</span>
                                            <small class="fw-bold" id="ex-chg-8" style="color: #7ED321; font-size: 9.5px;">+0.52% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 9. HKEX Hong Kong -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.hkex.com.hk/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 HKEX</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">Hong Kong • $6.20T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-9" d="M0,18 L20,15 L40,17 L60,10 L80,12 L100,5" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-9" style="font-size: 11.5px; color: #FFFFFF !important;">$6.20T</span>
                                            <small class="fw-bold" id="ex-chg-9" style="color: #7ED321; font-size: 9.5px;">+1.05% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 10. KRX Korea -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://global.krx.co.kr/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 KRX Korea</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">South Korea • $4.89T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-10" d="M0,14 L20,10 L40,13 L60,7 L80,9 L100,3" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-10" style="font-size: 11.5px; color: #FFFFFF !important;">$4.89T</span>
                                            <small class="fw-bold" id="ex-chg-10" style="color: #7ED321; font-size: 9.5px;">+0.85% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Real-Time Continuous SVG Sparkline Graph & Live Market Value Refresh Script -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const items = [
                { id: 1, base: 43.58 },
                { id: 2, base: 33.29 },
                { id: 3, base: 5.14 },
                { id: 4, base: 5.12 },
                { id: 5, base: 9.97 },
                { id: 6, base: 8.72 },
                { id: 7, base: 8.33 },
                { id: 8, base: 7.30 },
                { id: 9, base: 6.20 },
                { id: 10, base: 4.89 }
            ];

            // Smooth continuous live ticker update every 800ms
            setInterval(() => {
                items.forEach(item => {
                    const pathEl = document.getElementById(`graph-path-${item.id}`);
                    const valEl = document.getElementById(`ex-val-${item.id}`);
                    const chgEl = document.getElementById(`ex-chg-${item.id}`);

                    if (pathEl && valEl && chgEl) {
                        // Dynamic continuous wave graph path generation
                        const p1 = Math.floor(Math.random() * 10 + 10);
                        const p2 = Math.floor(Math.random() * 12 + 6);
                        const p3 = Math.floor(Math.random() * 10 + 12);
                        const p4 = Math.floor(Math.random() * 8 + 3);
                        const p5 = Math.floor(Math.random() * 10 + 8);
                        const p6 = Math.floor(Math.random() * 6 + 1);

                        const dPath = `M0,${p1} L20,${p2} L40,${p3} L60,${p4} L80,${p5} L100,${p6}`;
                        pathEl.setAttribute('d', dPath);

                        // Dynamic value & pct fluctuation
                        const isUp = Math.random() > 0.42; // slightly biased towards growth
                        const delta = (isUp ? 1 : -1) * (Math.random() * 0.05);
                        item.base = Math.max(1.0, parseFloat((item.base + delta).toFixed(2)));
                        const pct = (Math.random() * 1.8 + 0.1).toFixed(2);

                        valEl.textContent = `$${item.base}T`;

                        if (isUp) {
                            chgEl.style.color = '#7ED321';
                            chgEl.innerHTML = `+${pct}% <i class="fa fa-caret-up"></i>`;
                            pathEl.setAttribute('stroke', '#7ED321');
                        } else {
                            chgEl.style.color = '#FF5F56';
                            chgEl.innerHTML = `-${pct}% <i class="fa fa-caret-down"></i>`;
                            pathEl.setAttribute('stroke', '#FF5F56');
                        }
                    }
                });

                // Live Fluctuation for Left Column Hero Stat Cards
                const heroStatVal1 = document.getElementById('hero-stat-val-1');
                const heroStatChg1 = document.getElementById('hero-stat-chg-1');
                if (heroStatVal1 && heroStatChg1) {
                    const isUp = Math.random() > 0.48;
                    const valDelta = (isUp ? 1 : -1) * Math.floor(Math.random() * 350 + 50);
                    let currentVal = parseInt((heroStatVal1.dataset.val || '1052280'), 10);
                    currentVal = Math.max(1000000, currentVal + valDelta);
                    heroStatVal1.dataset.val = currentVal;
                    heroStatVal1.textContent = `₹ ${currentVal.toLocaleString('en-IN')}`;

                    const yieldPct = (4.2 + (Math.random() - 0.5) * 0.4).toFixed(2);
                    if (isUp) {
                        heroStatChg1.style.background = 'rgba(126, 211, 33, 0.15)';
                        heroStatChg1.style.color = '#7ED321';
                        heroStatChg1.style.border = '1px solid rgba(126, 211, 33, 0.3)';
                        heroStatChg1.innerHTML = `+${yieldPct}% Monthly Yield <i class="fa fa-caret-up"></i>`;
                    } else {
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-4" d="M0,17 L20,10 L40,14 L60,6 L80,12 L100,2" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-4" style="font-size: 11.5px; color: #FFFFFF !important;">$5.12T</span>
                                            <small class="fw-bold" id="ex-chg-4" style="color: #7ED321; font-size: 9.5px;">+1.75% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 5. Shanghai SSE -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://english.sse.com.cn/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Shanghai SSE</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">China • $9.97T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-5" d="M0,12 L20,16 L40,8 L60,14 L80,9 L100,5" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-5" style="font-size: 11.5px; color: #FFFFFF !important;">$9.97T</span>
                                            <small class="fw-bold" id="ex-chg-5" style="color: #7ED321; font-size: 9.5px;">+0.64% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 6. Japan JPX -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.jpx.co.jp/english" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Japan JPX</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">Japan • $8.72T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-6" d="M0,19 L20,11 L40,15 L60,7 L80,11 L100,1" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-6" style="font-size: 11.5px; color: #FFFFFF !important;">$8.72T</span>
                                            <small class="fw-bold" id="ex-chg-6" style="color: #7ED321; font-size: 9.5px;">+1.12% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 7. Euronext -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.euronext.com/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Euronext</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">Europe • $8.33T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-7" d="M0,14 L20,10 L40,16 L60,8 L80,13 L100,2" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-7" style="font-size: 11.5px; color: #FFFFFF !important;">$8.33T</span>
                                            <small class="fw-bold" id="ex-chg-7" style="color: #7ED321; font-size: 9.5px;">+0.78% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 8. Shenzhen SZSE -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.szse.cn/English" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 Shenzhen SZSE</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">China • $7.30T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-8" d="M0,16 L20,12 L40,14 L60,8 L80,10 L100,4" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-8" style="font-size: 11.5px; color: #FFFFFF !important;">$7.30T</span>
                                            <small class="fw-bold" id="ex-chg-8" style="color: #7ED321; font-size: 9.5px;">+0.52% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 9. HKEX Hong Kong -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://www.hkex.com.hk/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 HKEX</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">Hong Kong • $6.20T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-9" d="M0,18 L20,15 L40,17 L60,10 L80,12 L100,5" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-9" style="font-size: 11.5px; color: #FFFFFF !important;">$6.20T</span>
                                            <small class="fw-bold" id="ex-chg-9" style="color: #7ED321; font-size: 9.5px;">+1.05% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                    <!-- 10. KRX Korea -->
                                    <div class="d-flex align-items-center justify-content-between py-1 px-2 rounded-3" style="background: rgba(255, 255, 255, 0.02);">
                                        <div style="width: 35%;">
                                            <a href="https://global.krx.co.kr/" target="_blank" class="fw-bold text-decoration-none d-block" style="font-size: 11.5px; color: #FFFFFF !important;">📈 KRX Korea</a>
                                            <small class="text-muted d-block" style="font-size: 9.5px;">South Korea • $4.89T</small>
                                        </div>
                                        <div style="width: 32%;">
                                            <svg width="100%" height="18" viewBox="0 0 100 25" style="overflow: visible;">
                                                <path id="graph-path-10" d="M0,14 L20,10 L40,13 L60,7 L80,9 L100,3" fill="none" stroke="#7ED321" stroke-width="2.2" stroke-linecap="round" style="transition: all 0.5s ease-in-out;"/>
                                            </svg>
                                        </div>
                                        <div class="text-end" style="width: 30%;">
                                            <span class="fw-bold d-block" id="ex-val-10" style="font-size: 11.5px; color: #FFFFFF !important;">$4.89T</span>
                                            <small class="fw-bold" id="ex-chg-10" style="color: #7ED321; font-size: 9.5px;">+0.85% <i class="fa fa-caret-up"></i></small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Real-Time Continuous SVG Sparkline Graph & Live Market Value Refresh Script -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const items = [
                { id: 1, base: 43.58 },
                { id: 2, base: 33.29 },
                { id: 3, base: 5.14 },
                { id: 4, base: 5.12 },
                { id: 5, base: 9.97 },
                { id: 6, base: 8.72 },
                { id: 7, base: 8.33 },
                { id: 8, base: 7.30 },
                { id: 9, base: 6.20 },
                { id: 10, base: 4.89 }
            ];

            // Smooth continuous live ticker update every 800ms
            setInterval(() => {
                items.forEach(item => {
                    const pathEl = document.getElementById(`graph-path-${item.id}`);
                    const valEl = document.getElementById(`ex-val-${item.id}`);
                    const chgEl = document.getElementById(`ex-chg-${item.id}`);

                    if (pathEl && valEl && chgEl) {
                        // Dynamic continuous wave graph path generation
                        const p1 = Math.floor(Math.random() * 10 + 10);
                        const p2 = Math.floor(Math.random() * 12 + 6);
                        const p3 = Math.floor(Math.random() * 10 + 12);
                        const p4 = Math.floor(Math.random() * 8 + 3);
                        const p5 = Math.floor(Math.random() * 10 + 8);
                        const p6 = Math.floor(Math.random() * 6 + 1);

                        const dPath = `M0,${p1} L20,${p2} L40,${p3} L60,${p4} L80,${p5} L100,${p6}`;
                        pathEl.setAttribute('d', dPath);

                        // Dynamic value & pct fluctuation
                        const isUp = Math.random() > 0.42; // slightly biased towards growth
                        const delta = (isUp ? 1 : -1) * (Math.random() * 0.05);
                        item.base = Math.max(1.0, parseFloat((item.base + delta).toFixed(2)));
                        const pct = (Math.random() * 1.8 + 0.1).toFixed(2);

                        valEl.textContent = `$${item.base}T`;

                        if (isUp) {
                            chgEl.style.color = '#7ED321';
                            chgEl.innerHTML = `+${pct}% <i class="fa fa-caret-up"></i>`;
                            pathEl.setAttribute('stroke', '#7ED321');
                        } else {
                            chgEl.style.color = '#FF5F56';
                            chgEl.innerHTML = `-${pct}% <i class="fa fa-caret-down"></i>`;
                            pathEl.setAttribute('stroke', '#FF5F56');
                        }
                    }
                });

                // Live Fluctuation for Left Column Hero Stat Cards
                const heroStatVal1 = document.getElementById('hero-stat-val-1');
                const heroStatChg1 = document.getElementById('hero-stat-chg-1');
                if (heroStatVal1 && heroStatChg1) {
                    const isUp = Math.random() > 0.48;
                    const valDelta = (isUp ? 1 : -1) * Math.floor(Math.random() * 350 + 50);
                    let currentVal = parseInt((heroStatVal1.dataset.val || '1052280'), 10);
                    currentVal = Math.max(1000000, currentVal + valDelta);
                    heroStatVal1.dataset.val = currentVal;
                    heroStatVal1.textContent = `₹ ${currentVal.toLocaleString('en-IN')}`;

                    const yieldPct = (4.2 + (Math.random() - 0.5) * 0.4).toFixed(2);
                    if (isUp) {
                        heroStatChg1.style.background = 'rgba(126, 211, 33, 0.15)';
                        heroStatChg1.style.color = '#7ED321';
                        heroStatChg1.style.border = '1px solid rgba(126, 211, 33, 0.3)';
                        heroStatChg1.innerHTML = `+${yieldPct}% Monthly Yield <i class="fa fa-caret-up"></i>`;
                    } else {
                        heroStatChg1.style.background = 'rgba(255, 95, 86, 0.15)';
                        heroStatChg1.style.color = '#FF5F56';
                        heroStatChg1.style.border = '1px solid rgba(255, 95, 86, 0.3)';
                        heroStatChg1.innerHTML = `-${yieldPct}% Monthly Yield <i class="fa fa-caret-down"></i>`;
                    }
                }
            }, 800);
        });
        </script>
        <!-- ==================================================
             SECTION 02 — ABOUT US & OUR GROWTH
             ID: #about
             ================================================== -->
        <section id="about" class="py-5 position-relative overflow-hidden" style="background: #FFFFFF !important; border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0;">
            <div class="auto-container position-relative" style="z-index: 5;">
                
                <div class="row g-5 align-items-center">
                    
                    <!-- LEFT COLUMN: ABOUT US CONTENT -->
                    <div class="col-lg-5 col-md-12">
                        <div class="pe-lg-3 text-center text-lg-start">
                            
                            <!-- Pill Badge -->
                            <div class="ananta-badge mb-3" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(11, 37, 69, 0.25); border: 1px solid rgba(197, 160, 89, 0.4);">
                                <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> ABOUT US
                            </div>
                            
                            <!-- Subtitle / Company Title -->
                            <h6 class="fw-extrabold text-uppercase mb-2" style="color: #0F5132; letter-spacing: 1.5px; font-size: 14px;">
                                ANANTA MULTI TRADE PRIVATE LIMITED
                            </h6>

                            <!-- Hero Title -->
                            <h2 class="fw-extrabold display-5 mb-3" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; letter-spacing: -0.5px; font-weight: 800; line-height: 1.2;">
                                ONE VISION.<br />
                                <span style="color: #0F5132;">MULTIPLE OPPORTUNITIES.</span>
                            </h2>

                            <!-- Accent Divider -->
                            <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-3 my-3">
                                <span style="width: 60px; height: 2px; background: linear-gradient(90deg, #0F5132, transparent);"></span>
                                <span style="display: inline-block; width: 6px; height: 6px; background: #C5A059; transform: rotate(45deg);"></span>
                                <span style="width: 60px; height: 2px; background: linear-gradient(90deg, transparent, #0F5132);"></span>
                            </div>

                            <!-- Lead Text -->
                            <h5 class="fw-bold mb-3" style="color: #1E293B; line-height: 1.5; font-size: 18px;">
                                Building a diversified business ecosystem across markets, infrastructure, real estate and emerging industries.
                            </h5>

                            <!-- Body Text -->
                            <p style="color: #475569; font-size: 15px; line-height: 1.7; margin-bottom: 0;">
                                Ananta Multi Trade Private Limited is creating a growing network of businesses, partners and opportunities with a focus on innovation, professional operations and long-term growth.
                            </p>

                        </div>
                    </div>

                    <!-- RIGHT COLUMN: OUR GROWTH STATS GRID -->
                    <div class="col-lg-7 col-md-12">
                        
                        <div class="sec-title text-center text-lg-start mb-4">
                            <span class="fw-bold text-uppercase d-block mb-1" style="color: #C5A059; letter-spacing: 1px; font-size: 13px;">Performance & Expansion</span>
                            <h3 class="fw-extrabold text-uppercase mb-0" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; font-size: 26px;">
                                OUR GROWTH
                            </h3>
                        </div>

                        <div class="row g-3 justify-content-center py-2">
                            <!-- Stat 1: 7+ Countries -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            🌍
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">7+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Countries</p>
                                </div>
                            </div>

                            <!-- Stat 2: 10K+ Members -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            👥
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">10K+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Members</p>
                                </div>
                            </div>

                            <!-- Stat 3: $2M+ Global Volume -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            💰
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">$2M+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Global Volume</p>
                                </div>
                            </div>

                            <!-- Stat 4: 100+ Partners -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            🤝
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">100+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Partners</p>
                                </div>
                            </div>

                            <!-- Stat 5: 98% Success Rate -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            📈
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">98%</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Success Rate</p>
                                </div>
                            </div>

                            <!-- Stat 6: 9.8% Monthly Growth -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            📊
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">9.8%</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Monthly Growth</p>
                                </div>
                            </div>

                            <!-- Stat 7: 8.5K+ Active Members -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            👥
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">8.5K+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Active Members</p>
                                </div>
                            </div>

                            <!-- Stat 8: 85%+ Active Community -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 transition-all border-0 bg-light" style="background: rgba(248, 250, 252, 0.8) !important; border: 1px solid #E2E8F0 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 50px;">
                                        <div style="width: 48px; height: 48px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                                            ⚡
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 26px; font-weight: 800;">85%+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 13px;">Active Community</p>
                                </div>
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
        <section id="businesses" class="py-5 position-relative overflow-hidden" style="background: #FFFFFF !important; border-bottom: 1px solid #E2E8F0;">
            <div class="auto-container position-relative" style="z-index: 5;">
                <div class="sec-title text-center mb-5">
                    <div class="ananta-badge mb-3" style="background: linear-gradient(135deg, #0F5132 0%, #0B2545 100%); color: #FFFFFF; font-weight: 600; padding: 7px 20px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(15, 81, 50, 0.2); border: 1px solid rgba(197, 160, 89, 0.4);">
                        <span style="width: 8px; height: 8px; background: #C5A059; border-radius: 50%; display: inline-block;"></span> WHAT WE DO
                    </div>
                    <h2 class="fw-extrabold display-4 mb-2" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; letter-spacing: 0.5px; font-weight: 800; text-transform: uppercase;">
                        DIVERSIFIED BUSINESS. <span style="color: #0F5132;">ONE ECOSYSTEM.</span>
                    </h2>
                    <div class="d-flex align-items-center justify-content-center gap-3 my-3">
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, transparent, #0F5132);"></span>
                        <span style="display: inline-block; width: 7px; height: 7px; background: #C5A059; transform: rotate(45deg);"></span>
                        <span style="width: 80px; height: 2px; background: linear-gradient(90deg, #0F5132, transparent);"></span>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Vertical 1: FOREX TRADING -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex flex-column justify-content-between" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-uppercase" style="font-size: 13px; color: #0B2545; letter-spacing: 1px;">01</span>
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: rgba(11, 37, 69, 0.08); border: 1px solid rgba(11, 37, 69, 0.15);">
                                        <i class="fa fa-chart-line fs-4" style="color: #0B2545;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #0B2545; font-family: var(--ananta-font-heading);">FOREX TRADING</h4>
                                <p style="color: #475569; font-size: 15px; line-height: 1.6;">Market-focused trading and research.</p>
                            </div>
                            <a href="service-details.php" class="fw-bold text-decoration-none d-inline-flex align-items-center mt-3" style="color: #0B2545; font-size: 14px;">Explore Vertical <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>

                    <!-- Vertical 2: REAL ESTATE -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex flex-column justify-content-between" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-uppercase" style="font-size: 13px; color: #0F5132; letter-spacing: 1px;">02</span>
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: rgba(15, 81, 50, 0.08); border: 1px solid rgba(15, 81, 50, 0.15);">
                                        <i class="fa fa-building fs-4" style="color: #0F5132;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #0B2545; font-family: var(--ananta-font-heading);">REAL ESTATE</h4>
                                <p style="color: #475569; font-size: 15px; line-height: 1.6;">Property and real-asset opportunities.</p>
                            </div>
                            <a href="service-details.php" class="fw-bold text-decoration-none d-inline-flex align-items-center mt-3" style="color: #0F5132; font-size: 14px;">Explore Vertical <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>

                    <!-- Vertical 3: IPO & INVESTMENT -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex flex-column justify-content-between" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-uppercase" style="font-size: 13px; color: #0B2545; letter-spacing: 1px;">03</span>
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: rgba(11, 37, 69, 0.08); border: 1px solid rgba(11, 37, 69, 0.15);">
                                        <i class="fa fa-chart-pie fs-4" style="color: #0B2545;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #0B2545; font-family: var(--ananta-font-heading);">IPO & INVESTMENT</h4>
                                <p style="color: #475569; font-size: 15px; line-height: 1.6;">Opportunities across selected market segments.</p>
                            </div>
                            <a href="service-details.php" class="fw-bold text-decoration-none d-inline-flex align-items-center mt-3" style="color: #0B2545; font-size: 14px;">Explore Vertical <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>

                    <!-- Vertical 4: EV CHARGING POINT -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex flex-column justify-content-between" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-uppercase" style="font-size: 13px; color: #0F5132; letter-spacing: 1px;">04</span>
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: rgba(15, 81, 50, 0.08); border: 1px solid rgba(15, 81, 50, 0.15);">
                                        <i class="fa fa-bolt fs-4" style="color: #0F5132;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #0B2545; font-family: var(--ananta-font-heading);">EV CHARGING POINT</h4>
                                <p style="color: #475569; font-size: 15px; line-height: 1.6;">Building the next generation of EV infrastructure.</p>
                            </div>
                            <a href="service-details.php" class="fw-bold text-decoration-none d-inline-flex align-items-center mt-3" style="color: #0F5132; font-size: 14px;">Explore Vertical <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>

                    <!-- Vertical 5: TRAVEL & TOURISM -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex flex-column justify-content-between" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-uppercase" style="font-size: 13px; color: #0B2545; letter-spacing: 1px;">05</span>
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: rgba(11, 37, 69, 0.08); border: 1px solid rgba(11, 37, 69, 0.15);">
                                        <i class="fa fa-plane-departure fs-4" style="color: #0B2545;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #0B2545; font-family: var(--ananta-font-heading);">TRAVEL & TOURISM</h4>
                                <p style="color: #475569; font-size: 15px; line-height: 1.6;">Domestic and international travel solutions.</p>
                            </div>
                            <a href="service-details.php" class="fw-bold text-decoration-none d-inline-flex align-items-center mt-3" style="color: #0B2545; font-size: 14px;">Explore Vertical <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>

                    <!-- Vertical 6: GOLD -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex flex-column justify-content-between" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-uppercase" style="font-size: 13px; color: #0F5132; letter-spacing: 1px;">06</span>
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: rgba(15, 81, 50, 0.08); border: 1px solid rgba(15, 81, 50, 0.15);">
                                        <i class="fa fa-coins fs-4" style="color: #0F5132;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #0B2545; font-family: var(--ananta-font-heading);">GOLD</h4>
                                <p style="color: #475569; font-size: 15px; line-height: 1.6;">Gold-focused business opportunities.</p>
                            </div>
                            <a href="service-details.php" class="fw-bold text-decoration-none d-inline-flex align-items-center mt-3" style="color: #0F5132; font-size: 14px;">Explore Vertical <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>

                </div>

                <div class="text-center mt-5">
                    <a href="service-details.php" class="btn px-4 py-3 rounded-3 text-white fw-bold shadow-sm text-decoration-none" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); border: none; border-radius: 30px !important; padding: 12px 30px;">
                        EXPLORE ALL VERTICALS <i class="fa fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- ==================================================
             SECTION 04 — WHY ANANTA
             ID: #partners
             ================================================== -->
        <section id="partners" class="py-5 position-relative overflow-hidden" style="background: #FFFFFF !important; border-bottom: 1px solid #E2E8F0;">
            <div class="auto-container position-relative" style="z-index: 5;">
                <div class="sec-title text-center mb-5">
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
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
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
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
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
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
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
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="p-4 rounded-4 h-100 transition-all d-flex gap-4 align-items-start" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 6px 24px rgba(11, 37, 69, 0.05); border-radius: 20px !important;">
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
        <section id="vision" class="py-5 position-relative overflow-hidden" style="background: #FFFFFF !important; border-bottom: 1px solid #E2E8F0;">
            <div class="auto-container position-relative py-3" style="z-index: 5;">
                <div class="p-5 rounded-5 text-center position-relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #0B2545 0%, #0F5132 100%); border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 30px !important;">
                    
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
        </section>

        <!-- ==================================================
             SECTION 06 — FAQ & CONTACT US
             ID: #contact
             ================================================== -->
        <section id="contact" class="py-5 position-relative" style="background: #FFFFFF !important; color: #0F172A; border-top: 1px solid #E2E8F0;">
            
            <div class="container-fluid container-xl position-relative py-3">
                
                <!-- Section Header -->
                <div class="sec-title text-center mb-5">
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
                    <div class="col-lg-6 col-md-12">
                        <div class="p-4 p-md-4 rounded-4 h-100" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 10px 30px rgba(11, 37, 69, 0.05); border-radius: 24px !important;">
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
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px !important;">
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
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px !important;">
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
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px !important;">
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
                                <div class="accordion-item mb-3 rounded-3 overflow-hidden" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px !important;">
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
                                <div class="accordion-item rounded-3 overflow-hidden" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px !important;">
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
                    <div class="col-lg-6 col-md-12">
                        <div class="p-4 p-md-4 rounded-4 h-100" style="background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 10px 30px rgba(11, 37, 69, 0.05); border-radius: 24px !important;">
                            
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

    <?php include "common/footer.php"; ?>
</body>
</html>
