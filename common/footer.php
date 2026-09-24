<style>
    .main-footer {
    position: relative;
    z-index: 9999;
}

.main-footer a {
    pointer-events: auto !important;
}

.scroll-top,
.scroll-to-target,
.overlay,
.bg-overlay {
    pointer-events: none !important;
}

</style>
<style>
<style>
html, body {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.boxed_wrapper {
    flex: 1 0 auto;
    width: 100%;
    margin: 0 auto;
}

.main-footer {
    position: relative;
    z-index: 9999;
}
.main-footer a {
    pointer-events: auto !important;
}
.scroll-top, .scroll-to-target, .overlay, .bg-overlay {
    pointer-events: none !important;
}

/* Outer Full-Bleed Glass Footer (100% Viewport Width, 0px Side & Bottom Gaps) */
.ananta-glass-footer-wrapper {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 0 100px 0 !important; /* Bottom padding to ensure content is fully above mobile sticky nav */
    flex-shrink: 0;
    background: linear-gradient(135deg, rgba(235, 245, 252, 0.96) 0%, rgba(220, 244, 238, 0.94) 100%) !important;
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-top: 1.5px solid rgba(255, 255, 255, 0.95);
    border-left: none !important;
    border-right: none !important;
    border-bottom: none !important;
    border-top-left-radius: 40px !important;
    border-top-right-radius: 40px !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    box-shadow: 0 -15px 40px rgba(0, 0, 0, 0.05), inset 0 2px 4px rgba(255, 255, 255, 0.9);
    position: relative;
    overflow: hidden;
    color: #0F172A;
}

@media (min-width: 769px) {
    .ananta-glass-footer-wrapper {
        padding-bottom: 0 !important;
    }
}

/* Liquid Fluid Top Wave Overlay */
.ananta-wave-header-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 95px;
    pointer-events: none;
    z-index: 1;
}

/* Navigation Items */
.ananta-nav-link {
    color: #1E293B !important;
    font-weight: 600;
    font-size: 15px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.ananta-nav-link:hover {
    color: #0B5ED7 !important;
    padding-left: 6px;
}

/* Vertical Column Divider */
@media (min-width: 992px) {
    .ananta-col-divider {
        border-left: 1px solid rgba(0, 0, 0, 0.12);
        padding-left: 2.5rem !important;
    }
}

/* Email Pill Box Widget */
.ananta-email-pill {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.95);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05), inset 0 1px 2px #FFFFFF;
    border-radius: 22px;
    padding: 14px 20px;
    backdrop-filter: blur(12px);
}

/* Social Icon Buttons */
.ananta-social-btn {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FFFFFF 0%, #E2F1F8 100%);
    border: 1px solid rgba(255, 255, 255, 0.95);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08), inset 0 1px 2px #FFFFFF;
    color: #0F172A !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 17px;
    text-decoration: none;
    transition: all 0.25s ease;
}
.ananta-social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(11, 94, 215, 0.22);
    color: #0B5ED7 !important;
}
</style>

<footer class="main-footer ananta-glass-footer-wrapper">
    <!-- Liquid Fluid Top Wave Graphic -->
    <div class="ananta-wave-header-bg">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 100%; height: 100%;">
            <path d="M0,0 C150,80 350,-30 500,50 C650,130 900,-10 1200,30 L1200,0 L0,0 Z" fill="url(#liquidGradient)" opacity="0.55"></path>
            <defs>
                <linearGradient id="liquidGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#38BDF8" stop-opacity="0.9"/>
                    <stop offset="50%" stop-color="#34D399" stop-opacity="0.9"/>
                    <stop offset="100%" stop-color="#38BDF8" stop-opacity="0.9"/>
                </linearGradient>
            </defs>
        </svg>
    </div>

    <!-- Inner Content Container -->
    <div class="container-fluid container-xl px-3 px-md-5 py-4 py-md-5 position-relative" style="z-index: 5;">
        <!-- Main Footer Content -->
        <div class="row g-4 pt-3 position-relative" style="z-index: 5;">
            
            <!-- COLUMN 1: LOGO & BRAND DETAILS -->
            <div class="col-lg-5 col-md-12">
                <div class="pe-lg-4">
                    <div class="mb-3">
                        <img src="assets/images/logo.png" alt="Ananta Multi Trade" style="max-width: 220px; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.12));">
                    </div>
                    <h4 class="fw-bold mb-2" style="color: #0F172A; font-family: var(--ananta-font-heading); font-size: 19px; letter-spacing: 0.2px;">
                        ANANTA MULTI TRADE PRIVATE LIMITED
                    </h4>
                    <p class="fw-bold mb-3" style="color: #0F172A; font-size: 15px; line-height: 1.4;">
                        Building Businesses. Creating Opportunities. Growing Together.
                    </p>
                    <p style="color: #334155; font-size: 14px; line-height: 1.6; margin-bottom: 0; max-width: 480px;">
                        Ananta Multi Trade Private Limited is creating a growing network of businesses, partners and opportunities with a focus on innovation, professional operations and long-term growth.
                    </p>
                </div>
            </div>

            <!-- COLUMN 2: NAVIGATION LINKS -->
            <div class="col-lg-3 col-md-6">
                <div>
                    <h5 class="fw-bold mb-3" style="color: #0F172A; font-family: var(--ananta-font-heading); font-size: 18px;">
                        Navigation
                    </h5>
                    <div class="d-flex flex-column">
                        <a href="index.php#home" class="ananta-nav-link">
                            <span style="color: #475569; font-weight: 700;">&rsaquo;</span> Home
                        </a>
                        <a href="index.php#about" class="ananta-nav-link">
                            <span style="color: #475569; font-weight: 700;">&rsaquo;</span> About Us
                        </a>
                        <a href="index.php#businesses" class="ananta-nav-link">
                            <span style="color: #475569; font-weight: 700;">&rsaquo;</span> Businesses
                        </a>
                        <a href="index.php#partners" class="ananta-nav-link">
                            <span style="color: #475569; font-weight: 700;">&rsaquo;</span> Why Us?
                        </a>
                        <a href="index.php#contact" class="ananta-nav-link" style="border-bottom: none;">
                            <span style="color: #475569; font-weight: 700;">&rsaquo;</span> Contact
                        </a>
                    </div>
                </div>
            </div>

            <!-- COLUMN 3: CONNECT & REACH OUT (SEPARATED BY VERTICAL DIVIDER) -->
            <div class="col-lg-4 col-md-6 ananta-col-divider">
                <div>
                    <h5 class="fw-bold mb-3" style="color: #0F172A; font-family: var(--ananta-font-heading); font-size: 18px;">
                        Connect & Reach Out
                    </h5>
                    
                    <!-- Official Support Email Glass Pill Box -->
                    <div class="ananta-email-pill mb-4 d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%); border: 1px solid rgba(255,255,255,0.95); box-shadow: 0 4px 10px rgba(11, 94, 215, 0.15);">
                            <i class="fa fa-envelope fs-5" style="color: #0284C7;"></i>
                        </div>
                        <div>
                            <small class="text-uppercase d-block fw-bold" style="font-size: 10px; color: #475569; letter-spacing: 0.8px;">OFFICIAL SUPPORT EMAIL</small>
                            <a href="mailto:<?php echo $hmemail; ?>" class="fw-bold text-decoration-none" style="color: #0F172A; font-size: 14.5px; word-break: break-all;"><?php echo $hmemail; ?></a>
                        </div>
                    </div>
                    

                    <!-- Glossy Round Social Icon Buttons -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://www.facebook.com/p/Ananta-Melody-Verse-61585786533006" target="_blank" class="ananta-social-btn" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/anantamelodyverses?igsh=NmQ1NGItY3VqZGhw&utm_source=qr" target="_blank" class="ananta-social-btn" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://t.me/anantamultitreadpvt" target="_blank" class="ananta-social-btn" title="Telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <a href="https://www.youtube.com/@anantamelodyverse?si=qIDQyBt9kS0s4A0F" target="_blank" class="ananta-social-btn" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                   <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap w-100">

    <a href="privacy-policy.php"
       class="footer-pill">
        Privacy Policy
    </a>

    <a href="terms-condition.php"
       class="footer-pill">
        Terms & Conditions
    </a>

    <a href="disclaimer.php"
       class="footer-pill">
        Disclaimer
    </a>

</div>

<style>
.footer-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.65);
    border: 1px solid rgba(11, 94, 215, 0.15);
    color: #334155;
    font-size: 12px;
    font-weight: 500;
    line-height: 1;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.25s ease;
}

.footer-pill:hover {
    color: #0B5ED7;
    background: rgba(11, 94, 215, 0.08);
    border-color: rgba(11, 94, 215, 0.3);
    transform: translateY(-1px);
}

@media (max-width: 576px) {
    .footer-pill {
        padding: 6px 10px;
        font-size: 11px;
    }
}
</style>
                </div>
            </div>

        </div>

        <!-- Bottom Horizontal Footer Bar -->
        <div class="mt-4 pt-3 border-top border-dark border-opacity-10 d-flex flex-wrap justify-content-between align-items-center gap-3 text-center text-md-start" style="font-size: 14px; color: #334155; position: relative; z-index: 5;">
            <div class="w-100 w-md-auto mb-2 mb-md-0">
                <button type="button" onclick="openLrcModal();" class="d-none d-lg-inline-block" title="Access Control" style="background: transparent; border: none; color: #0F172A; font-size: 14px; font-weight: 700; cursor: pointer; padding: 0 2px; outline: none; vertical-align: baseline;">&copy;</button><span class="d-lg-none">&copy;</span> <?php echo date('Y'); ?> <span class="fw-bold" style="color: #0F172A;"><?php echo $hmtitle; ?> Multi Trade Private Limited</span>. All Rights Reserved.
            </div>
            
        </div>
    </div>
</footer>
<?php include_once __DIR__ . '/../dashboard/user1/common/login_reg_control_modal.php'; ?>
        <!-- main-footer end -->


        <!-- scroll to top -->
        <!--<button class="scroll-top scroll-to-target" data-target="html">-->
        <!--    <i class="flaticon-upper-right-arrow"></i>-->
        <!--</button>-->



    <!-- jequery plugins -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/wow.js"></script>
    <script src="assets/js/validation.js"></script>
    <script src="assets/js/jquery.fancybox.js"></script>
    <script src="assets/js/appear.js"></script>
    <script src="assets/js/scrollbar.js"></script>
    <script src="assets/js/isotope.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/jquery.lettering.min.js"></script>
    <script src="assets/js/jquery.circleType.js"></script>
    <script src="assets/js/bxslider.js"></script>

    <!-- main-js -->
    <script src="assets/js/script.js"></script>
    <!-- PWA Service Worker & Install Handler -->
    <script src="assets/js/app-pwa.js"></script>
