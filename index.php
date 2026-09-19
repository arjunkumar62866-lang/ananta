<?php include "common/header.php"; ?>
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
        <section id="about" class="py-5 position-relative overflow-hidden" style="background-image: url('assets/images/background/bg.png') !important; background-repeat: repeat !important; border-top: 1px solid rgba(197, 160, 89, 0.3); border-bottom: 1px solid rgba(197, 160, 89, 0.3);">
            <div class="auto-container position-relative" style="z-index: 5;">
                
                <div class="row g-5 align-items-center">
                    
                    <!-- LEFT COLUMN: ABOUT US CONTENT -->
                    <div class="col-lg-5 col-md-12 framer-reveal framer-delay-1">
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
                    <div class="col-lg-7 col-md-12 framer-reveal framer-delay-2">
                        
                        <div class="sec-title text-center text-lg-start mb-4">
                            <span class="fw-bold text-uppercase d-block mb-1" style="color: #C5A059; letter-spacing: 1px; font-size: 13px;">Performance & Expansion</span>
                            <h3 class="fw-extrabold text-uppercase mb-0" style="font-family: var(--ananta-font-heading); color: #0B2545 !important; font-size: 26px;">
                                OUR GROWTH
                            </h3>
                        </div>

                        <div class="row g-3 justify-content-center py-2">
                            <!-- Stat 1: 7+ Countries -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            🌍
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">7+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Countries</p>
                                </div>
                            </div>

                            <!-- Stat 2: 10K+ Members -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            👥
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">10K+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Members</p>
                                </div>
                            </div>

                            <!-- Stat 3: $2M+ Global Volume -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            💰
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">$2M+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Global Volume</p>
                                </div>
                            </div>

                            <!-- Stat 4: 100+ Partners -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            🤝
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">100+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Partners</p>
                                </div>
                            </div>

                            <!-- Stat 5: 98% Success Rate -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            📈
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">98%</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Success Rate</p>
                                </div>
                            </div>

                            <!-- Stat 6: 9.8% Monthly Growth -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            📊
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">9.8%</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Monthly Growth</p>
                                </div>
                            </div>

                            <!-- Stat 7: 8.5K+ Active Members -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(15,81,50,0.1) 0%, rgba(11,37,69,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            👥
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0F5132; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">8.5K+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Active Members</p>
                                </div>
                            </div>

                            <!-- Stat 8: 85%+ Active Community -->
                            <div class="col-4 col-md-3">
                                <div class="p-3 rounded-4 text-center h-100 ananta-stat-box" style="background: rgba(248, 250, 252, 0.85) !important; border: 1px solid #CBD5E1 !important;">
                                    <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 48px;">
                                        <div class="stat-emoji-wrapper" style="width: 44px; height: 44px; background: radial-gradient(circle, rgba(11,37,69,0.1) 0%, rgba(15,81,50,0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            ⚡
                                        </div>
                                    </div>
                                    <h2 class="fw-extrabold mb-1 display-5" style="color: #0B2545; font-family: var(--ananta-font-heading); font-size: 24px; font-weight: 800;">85%+</h2>
                                    <p class="mb-0 fw-semibold" style="color: #334155; font-size: 12.5px;">Active Community</p>
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
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; letter-spacing: 0.3px;">FOREX TRADING</h5>
                                <p class="mb-0" style="color: #64748B; font-size: 13.5px; line-height: 1.5;">Market-focused trading and research.</p>
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
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; letter-spacing: 0.3px;">REAL ESTATE</h5>
                                <p class="mb-0" style="color: #64748B; font-size: 13.5px; line-height: 1.5;">Property and real-asset opportunities.</p>
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
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; letter-spacing: 0.3px;">IPO & INVESTMENT</h5>
                                <p class="mb-0" style="color: #64748B; font-size: 13.5px; line-height: 1.5;">Opportunities across selected market segments.</p>
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
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; letter-spacing: 0.3px;">EV CHARGING POINT</h5>
                                <p class="mb-0" style="color: #64748B; font-size: 13.5px; line-height: 1.5;">Building the next generation of EV infrastructure.</p>
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
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; letter-spacing: 0.3px;">TRAVEL & TOURISM</h5>
                                <p class="mb-1" style="color: #64748B; font-size: 13.5px; line-height: 1.5;">Domestic and international travel solutions.</p>
                                <span class="d-block fw-bold" style="font-size: 12px; color: #1E293B;">Agency - Saqlaini Travels</span>
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
                                <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; letter-spacing: 0.3px;">GOLD</h5>
                                <p class="mb-0" style="color: #64748B; font-size: 13.5px; line-height: 1.5;">Gold-focused business opportunities.</p>
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

        // Swiper 5-Image Center Zoom Loop Slider (Always 5 slides on desktop, 2s interval)
        if (document.querySelector('.ananta-showcase-swiper')) {
            const showcaseSwiper = new Swiper('.ananta-showcase-swiper', {
                slidesPerView: 1.5,
                spaceBetween: 14,
                centeredSlides: true,
                loop: true,
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


