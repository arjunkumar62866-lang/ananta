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
}

/* Optional: mobile specific control */
@media (max-width: 768px) {
    .responsive-img {
        width: 100%;
        max-width: 1000px; /* jitna chaho utna */
        object-fit: cover;
    }
}



</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


        <!-- Mobile Menu  -->
        <div class="mobile-menu">
            <div class="menu-backdrop"></div>
            <div class="close-btn"><i class="fas fa-times"></i></div>
            
            <nav class="menu-box">
                <div class="nav-logo"><a href="index.php"><img src="<?php echo $hmlogo; ?>" style="height:100px; width:100px;" alt="" title=""></a></div>
                <div class="menu-outer"><!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header--></div>
                <div class="contact-info">
                    <h4>Contact Info</h4>
                    <ul>
                        <li>XYZ</li>
                        <li><a href="tel:+8801682648101">+91-9999999999</a></li>
                        <li><a href="mailto:<?php echo $hmemail ?>"><?php echo $hmemail ?></a></li>
                    </ul>
                </div>
                <div class="social-links">
                    <ul class="clearfix">
                        <li><a href="https://www.facebook.com/p/Ananta-Melody-Verse-61585786533006"><i class="flaticon-facebook"></i></a></li>
                                        <li><a href="https://www.instagram.com/anantamelodyverses?igsh=NmQ1NGItY3VqZGhw&utm_source=qr"><i class="flaticon-instagram-logo"></i></a></li>
                                        <li><a href="https://t.me/anantamultitreadpvt"><i class="flaticon-telegram-logo"></i></a></li>
                                        <li><a href="https://www.youtube.com/@anantamelodyverse?si=qIDQyBt9kS0s4A0F"><i class="flaticon-youtube"></i></a></li>
                    </ul>
                </div>
            </nav>
        </div><!-- End Mobile Menu -->
<div>
    <div id="particles-js"></div>
        <!-- banner-section -->
        <section class="banner-section">
            <div class="banner-carousel owl-theme owl-carousel nav-style-one">
                <div class="slide-item">
                    <div class="bg-layer" style="background-image: url(assets/images/banner/1.png);"></div>
                    <div class="auto-container">
                        <div class="content-box">
                            <h3>Grow Your Wealth</h3>
                            <h2>Stock Market</h2>
                            <h4>with Smart Stock Market Strategies.</h4>
                            <p>Start building a strong financial future with expert-guided stock investments.</p>
                            <div class="btn-box">
                                <a href="index.php" class="theme-btn btn-two"><span>Get Plan Now</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slide-item">
                    <div class="bg-layer" style="background-image: url(assets/images/banner/2.png);"></div>
                    <div class="auto-container">
                        <div class="content-box">
                            <h3>Earn Passive Income</h3>
                            <h2>Crypto Mining</h2>
                            <h4>through Powerful Crypto Mining Solutions.</h4>
                            <p>Secure, profitable, and energy-efficient mining designed for long-term benefits.</p>
                            <div class="btn-box">
                                <a href="index.php" class="theme-btn btn-two"><span>Get Plan Now</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slide-item">
                    <div class="bg-layer" style="background-image: url(assets/images/banner/3.png);"></div>
                    <div class="auto-container">
                        <div class="content-box">
                            <h3>Build Real Assets</h3>
                            <h2>Real Estate Investments</h2>
                            <h4>with High-Value Real Estate Opportunities.</h4>
                            <p>Invest in properties that deliver stability, appreciation, and recurring income.</p>
                            <div class="btn-box">
                                <a href="index.php" class="theme-btn btn-two"><span>Get Plan Now</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="social-box">
                <span class="text">Social Connect</span>
                <ul class="social-links clearfix">
                    <li><a href="index.php"><i class="flaticon-facebook"></i></a></li>
                    <li><a href="index.php"><i class="flaticon-twitter"></i></a></li>
                    <li><a href="index.php"><i class="flaticon-instagram-logo"></i></a></li>
                    <li><a href="index.php"><i class="flaticon-youtube"></i></a></li>
                </ul>
            </div>
        </section>
        <!-- banner-section end -->


        <!-- about-section -->
        <section class="about-section">
            <div class="auto-container">
                <div class="inner-container">
                    <div class="row clearfix">
                        <div class="col-lg-6 col-md-12 col-sm-12 left-column">
                            <div class="left-content">
                                <div class="title-box">
                                    <h4>Since 2025</h4>
                                    <h2>Ananta</h2>
                                </div>
                                <div class="content_block_one">
                                    <div class="content-box">
                                        <div class="sec-title">
                                            <h6>About Us</h6>
                                            <h2>We offer a 100% guarantee on our investment
program</h2>
                                        </div>
                                        <div class="text-box">
                                            <p>Explore the future of wealth with crypto mining, stock market investments, and real estate. Each offers unique opportunities for growth, passive income, and long-term financial security. Smart choices today can build a stronger, diversified tomorrow.</p>
                                        </div>
                                        <div class="inner-box image-only">
    <img src="assets/images/service/stump.png" alt="Background Image" class="responsive-img">
</div>


                                        <!--<div class="inner-box">-->
                                            <!--<div class="row clearfix">-->
                                            <!--    <div class="col-lg-6 col-md-6 col-sm-12 fact-column">-->
                                            <!--        <div class="fact-box">-->
                                            <!--            <div class="shape" style="background-image: url(assets/images/shape/shape-1.png);"></div>-->
                                            <!--            <div class="icon-box"><img src="assets/images/icons/icon-8.png" alt=""></div>-->
                                            <!--            <p>No. of Business -->
                                            <!--                Executed Last Year.</p>-->
                                            <!--            <h2>$1.4 <span>Billion</span></h2>-->
                                            <!--        </div>-->
                                            <!--    </div>-->
                                            <!--    <div class="col-lg-6 col-md-6 col-sm-12 highlights-column">-->
                                            <!--        <div class="highlights-box">-->
                                            <!--            <ul class="list-item clearfix">-->
                                            <!--                <li>Beginner’s Guide to Crypto Mining</li>-->
                                            <!--                <li>Stock Market Investment Basics</li>-->
                                            <!--            </ul>-->
                                            <!--            <div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Read More</span></a></div>-->
                                            <!--        </div>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 right-column">
                            <div class="right-content">
                                <div class="awards-box">
                                    <div class="awards-carousel owl-carousel ow-theme owl-nav-none dots-style-one">
                                        <div class="awards-block-one">
                                            <div class="image-box"><img src="assets/images/icons/awards-1.png" alt=""></div>
                                            <div class="inner">
                                                <h6>Stock Market Excellence</h6>
                                                <h4><a href="index.php">Best Stock Market Research Platform of the Year <br />the Year</a></h4>
                                                <span>2021-2024</span>
                                            </div>
                                        </div>
                                        <div class="awards-block-one">
                                            <div class="image-box"><img src="assets/images/icons/awards-1.png" alt=""></div>
                                            <div class="inner">
                                                <h6>Crypto Mining Innovation</h6>
                                                <h4><a href="index.php">Most Trusted Crypto Mining Solution Provider <br />the Year</a></h4>
                                                <span>2020-2024</span>
                                            </div>
                                        </div>
                                        <div class="awards-block-one">
                                            <div class="image-box"><img src="assets/images/icons/awards-1.png" alt=""></div>
                                            <div class="inner">
                                                <h6>Real Estate Investment Excellence</h6>
                                                <h4><a href="index.php">Top Real Estate Advisory & Investment Firm <br />the Year</a></h4>
                                                <span>2019-2024</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="featured-box">
                                    <div class="single-item">
                                        <div class="inner-box">
                                            <div class="link-box"><a href="index.html"><i class="flaticon-right-arrow"></i></a></div>
                                            <h3><a href="index.php">Real Estate Passive Income Model</a></h3>
                                            <p>Understand how rental income, REITs, and property flips can boost your financial stability.</p>
                                        </div>
                                        <div class="icon-box"><img src="assets/images/icons/icon-10.png" alt=""></div>
                                    </div>
                                    <div class="single-item">
                                        <div class="inner-box">
                                            <div class="link-box"><a href="index.html"><i class="flaticon-right-arrow"></i></a></div>
                                            <h3><a href="index.php">Stock Market Growth Strategies</a></h3>
                                            <p>Discover proven techniques for long-term wealth building and smart trading decisions.</p>
                                        </div>
                                        <div class="icon-box"><img src="assets/images/icons/icon-11.png" alt=""></div>
                                    </div>
                                    <div class="single-item">
                                        <div class="inner-box">
                                            <div class="link-box"><a href="index.html"><i class="flaticon-right-arrow"></i></a></div>
                                            <h3><a href="index.php">Beginner’s Guide to Crypto Mining.</a></h3>
                                            <p>Learn how mining works, what equipment you need, and how to earn passive income safely.</p>
                                        </div>
                                        <div class="icon-box"><img src="assets/images/icons/icon-12.png" alt=""></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 text-center">
        
        <h2 class="mb-4">Information</h2>

        <div class="ratio ratio-16x9">
          <video autoplay controls muted>
            <source src="assets/images/video/video2.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        </div>

      </div>
    </div>
  </div>
</section>

        

<section class="chooseus-section" style="background-color: #001321;">
            <div class="auto-container">
                <div class="sec-title centred">
                    <h6>Services</h6>
                    <h2 style="color: white;">Services That Provides By Ananta Multi Trade Private Limited</h2>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-4 col-md-6 col-sm-12 left-column">
                        <div class="left-content">
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-2.png);"></div>
                                <span class="count-box">01</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-21.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-22.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Gold Service</h3>
                                <p>We offer secure and trusted gold investment options that help clients build long-term financial stability. Our gold services include safe purchasing, reliable storage guidance, and investment-focused gold solutions designed to preserve wealth and deliver steady returns.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-2.png);"></div>
                                <span class="count-box">02</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-25.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-26.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Real Estate</h3>
                                <p>Our real estate services provide clients with premium residential and commercial investment opportunities. From property selection to documentation and long-term planning, we guide you through every step to ensure growth, stability, and consistent rental or resale value appreciation.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-2.png);"></div>
                                <span class="count-box">03</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-29.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-30.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Stock Market</h3>
                                <p>We help individuals and investors navigate the stock market with expert analysis, smart strategies, and risk-managed trading approaches. Our stock market services focus on long-term wealth creation, portfolio development, and informed decision-making for stable financial results.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 image-column">
                        <figure class="image-box"><img src="assets/images/resource/service.jpg" alt=""></figure>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 left-column">
                        <div class="right-content align-3">
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-3.png);"></div>
                                <span class="count-box">04</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-23.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-24.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Crypto Mining Plant Comming Soon</h3>
                                <p>Our crypto mining services offer a modern and profitable way to generate passive income through advanced mining technology. We provide mining setup, performance optimization, and complete monitoring to ensure safe, efficient, and high-yield digital asset production.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-3.png);"></div>
                                <span class="count-box">05</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-27.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-28.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Electronic Items</h3>
                                <p>We supply high-quality electronics such as LED TVs, AC, and electric scooters. Our products are energy-efficient, durable, and designed to meet the needs of modern households. We ensure affordability, reliability, and long-lasting performance in every product we deliver.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" ></div>
                                <span class="count-box">06</span>
                                <div class="icon-box">
                                    <div class="icon1"><img src="assets/images/icons/Ananta.png" alt="" height="120px" width="120px"></div>
                                    <!--<div class="overlay-icon"><img src="assets/images/icons/icon-32.png" alt=""></div>-->
                                </div>
                                <h3 style="color:white;">Music Production House</h3>
                                <p>We offer complete music and film production services including song writers, singers, actors, choreography, studios, and short film creation. Our production house supports artists, performers, and creators by providing professional studios, creative guidance, and high-quality production facilities.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <section class="service-section" style="background-color: #001321;">
    <div class="auto-container">
                <div class="sec-title centred">
                    <h6>Packages</h6>
                    <h2 style="color:white;">Prakash Package Overview</h2>
                    <p>The Prakash Package offers a
                            unique investment opportunity
                            with LED/Smart LED TVs, ranging
                            from ₹12,000 to ₹55,000.
                            Investors can expect a monthly
                            generation income of 3-4% ,
                            ensuring regular returns and
                            product-backed security for peace
                            of mind.</p>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">1.Prakash Package</a></h3>
                                <span>#01</span>
                                <div class="image-box">
                                    <img src="assets/images/service/24.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-13.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-14.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>12,000/- LED 24 Inches - 20,000/- LED 32 Inches</p>
                                <!--<div class="link-box"><a href="service-details.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">2.Prakash Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/32.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>32,000/- Smart LED 42 Inches - 55,000/- Smart LED 55 Inches</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-lg-3 col-md-6 col-sm-12 service-block">-->
                    <!--    <div class="service-block-one">-->
                    <!--        <div class="inner-box">-->
                    <!--            <h3><a href="#">3.Prakash Package</a></h3>-->
                    <!--            <span>#03</span>-->
                    <!--            <div class="image-box">-->
                    <!--                <img src="assets/images/service/42.jpg" alt="">-->
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-17.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-18.png" alt=""></div>-->
                                    <!--</div>-->
                    <!--            </div>-->
                    <!--            <p>32,000/- Smart LED 42 Inches</p>-->
                                <!--<div class="link-box"><a href="service-details-3.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="col-lg-3 col-md-6 col-sm-12 service-block">-->
                    <!--    <div class="service-block-one">-->
                    <!--        <div class="inner-box">-->
                    <!--            <h3><a href="#">4.Prakash Package</a></h3>-->
                    <!--            <span>#04</span>-->
                    <!--            <div class="image-box">-->
                    <!--                <img src="assets/images/service/55.jpg" alt="">-->
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-19.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-20.png" alt=""></div>-->
                                    <!--</div>-->
                    <!--            </div>-->
                    <!--            <p>55,000/- Smart LED 55 Inches</p>-->
                                <!--<div class="link-box"><a href="service-details-4.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
                <hr>
                <div class="sec-title centred">
                    <h2 style="color:white;">Sampada Package
Overview
</h2>
                    <p>The Sampada Package
presents an exciting investment
opportunity with Electric Scooty
options. Ranging from ₹80,000
to ₹1,00,000, investors can
expect a steady monthly income
of 3.0% to 4.0% over a duration
of 48 months, ensuring secure
returns.</p>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">1.Sampada Package</a></h3>
                                <span>#01</span>
                                <div class="image-box">
                                    <img src="assets/images/service/50-60.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-13.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-14.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>80,000/- Electric Scooty - Range(50-60 KM Full Charge).</p>
                                <!--<div class="link-box"><a href="service-details.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">2.Sampada Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/70-80.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>1,05,000/- Electric Scooty - Range(70-80 KM Full Charge).</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                </div>
                                <hr>
                 <div class="sec-title text-center">
                    <h2 style="color:white;">Samridhi Premium Package
Overview
</h2>
                    <p>The Samridhi Premium Package
presents an exciting investment
opportunity
options. Ranging from ₹2,50,000
to ₹3,50,000, investors can
expect a steady monthly income
of 3.0% to 4.0% over a duration
of 48 months, ensuring secure
returns.</p>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">1.Samridhi Package</a></h3>
                                <span>#01</span>
                                <div class="image-box">
                                    <img src="assets/images/service/sam3.PNG" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>1,85,000</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">2.Samridhi Premium Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/sam4.PNG" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-13.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-14.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>2,85,000</p>
                                <!--<div class="link-box"><a href="service-details.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">3.Samridhi Royal Package</a></h3>
                                <span>#03</span>
                                <div class="image-box">
                                    <img src="assets/images/service/sam5.PNG" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>3,85,000</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    
                </div>
                <hr>
                <div class="sec-title centred">
                    <h2 style="color:white;">Tejas Package
Overview
</h2>
                    <p>The Tejas Package offers
secured investment ranging
from ₹1,50,000 to ₹5,00,000+,
backed by 40% gold. This unique
security provides peace of mind,
ensuring steady monthly income
generation of 3.0% to 4.0%, while
maximizing potential returns for
investors.</p>
                </div>
                <div class="row clearfix">
                    <!--<div class="col-lg-3 col-md-6 col-sm-12 service-block">-->
                    <!--    <div class="service-block-one">-->
                    <!--        <div class="inner-box">-->
                    <!--            <h3><a href="#">1.Tejas Package</a></h3>-->
                    <!--            <span>#01</span>-->
                    <!--            <div class="image-box">-->
                    <!--                <img src="assets/images/service/gold1.jpg" alt="">-->
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-13.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-14.png" alt=""></div>-->
                                    <!--</div>-->
                    <!--            </div>-->
                    <!--            <p>1,10,000</p>-->
                                <!--<div class="link-box"><a href="service-details.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">2.Tejas Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/gold2.jpg" alt="" style="height:160px;">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>1,50,000 - 5,00,000</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">3.Tejas Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/gold3.jpg" alt="" style="height:160px;">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>5,00,000 And Above</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-lg-3 col-md-6 col-sm-12 service-block">-->
                    <!--    <div class="service-block-one">-->
                    <!--        <div class="inner-box">-->
                    <!--            <h3><a href="#">4.Tejas Package</a></h3>-->
                    <!--            <span>#02</span>-->
                    <!--            <div class="image-box">-->
                    <!--                <img src="assets/images/service/gold4.jpg" alt="" style="height:160px;">-->
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                    <!--            </div>-->
                    <!--            <p>10,00,000+ And Soon</p>-->
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                        <hr>
                    <div class="sec-title centred">
                    <h2 style="color:white;">Dhanya Package
Overview
</h2>
                    <p>The Dhanya Package offers an
attractive investment opportunity
ranging from ₹10,00,000 to
₹12,50,000, featuring a New 2nd
Hand Car or Plot Agreement. This
package supports a monthly
income generation of 3.0% to
4.0%, ensuring secure returns for
investors.</p>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">1.Dhanya Package</a></h3>
                                <span>#01</span>
                                <div class="image-box">
                                    <img src="assets/images/service/car1.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-13.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-14.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>10,00,000 (Second Hand Car - (As Per The Invester Choice) - 70-100 Gaj Plot Agreement)</p>
                                <!--<div class="link-box"><a href="service-details.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">2.Dhanya Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/car2.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>12,50,000 (Second Hand Car - (As Per The Invester Choice) - 70-100 Gaj Plot Agreement)</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <hr>
                <div class="sec-title centred">
                    <h2 style="color:white;">Anant Package
Overview
</h2>
                    <p>The Anant Package offers an
impressive investment opportunity
with 25-30% backed by gold
combined with 70% plot security.
This dual security not only provides
a robust asset foundation but also
ensures consistent monthly
income generation at a rate of
3.0% to 4.0%.</p>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">1.Anant Package</a></h3>
                                <span>#01</span>
                                <div class="image-box">
                                    <img src="assets/images/service/plot1.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-13.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-14.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>10,50,000 (Gold+Plot For Security Reasons)</p>
                                <!--<div class="link-box"><a href="service-details.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">2.Anant Package</a></h3>
                                <span>#02</span>
                                <div class="image-box">
                                    <img src="assets/images/service/plot2.jpg" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>21,00,000 (Gold+Plot For Security Reasons)</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 service-block">
                        <div class="service-block-one">
                            <div class="inner-box">
                                <h3><a href="#">3.Anant Package</a></h3>
                                <span>#03</span>
                                <div class="image-box">
                                    <img src="assets/images/service/
goldplot.png
" alt="">
                                    <!--<div class="icon-box">-->
                                    <!--    <div class="icon"><img src="assets/images/icons/icon-15.png" alt=""></div>-->
                                    <!--    <div class="overlay-icon"><img src="assets/images/icons/icon-16.png" alt=""></div>-->
                                    <!--</div>-->
                                </div>
                                <p>51,00,000 (Gold+Plot For Security Reasons) And Soon.</p>
                                <!--<div class="link-box"><a href="service-details-2.html">Read More <i class="flaticon-upper-right-arrow"></i></a></div>-->
                            </div>
                        </div>
                    </div>
                </div>
                </div>

            </div>
        </section>

        <!-- service-section -->
        
        

        <!-- service-section end -->


        <!-- trading-section -->
        <!--<section class="trading-section bg-color-1" style="background-color: #001321;">-->
        <!--    <div class="auto-container">-->
        <!--        <div class="sec-title">-->
        <!--            <h6>Popular Pairs</h6>-->
        <!--            <h2 style="color: white;">Trending Stock Market & Crypto Mining Pairs in the Market.</h2>-->
                    <!--<a href="index.html">View All Pairs<i class="flaticon-upper-right-arrow"></i></a>-->
        <!--        </div>-->
        <!--        <div class="row clearfix">-->
        <!--            <div class="col-lg-6 col-md-12 col-sm-12 trading-block">-->
        <!--                <div class="trading-block-one">-->
        <!--                    <div class="inner-box">-->
        <!--                        <div class="row clearfix">-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
                                        <!--<img src="assets/images/service/plot1.jpg" alt="">-->
                                        <!--<div class="single-item pr_15">-->
                                        <!--    <ul class="list-item">-->
                                        <!--        <li>-->
                                        <!--            <div class="text-box">-->
                                        <!--                <h6>usd</h6>-->
                                        <!--                <p>American Dollar</p>-->
                                        <!--            </div>-->
                                        <!--            <figure class="image-box"><img src="assets/images/icons/flag-1.png" alt=""></figure>-->
                                        <!--        </li>-->
                                        <!--        <li>-->
                                        <!--            <span>Sell</span>-->
                                        <!--            <h5>154.719</h5>-->
                                        <!--        </li>-->
                                        <!--    </ul>-->
                                        <!--</div>-->
        <!--                            </div>-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pl_30">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>jpy</h6>-->
        <!--                                                <p>Japanese Yen</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-2.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Buy</span>-->
        <!--                                            <h5>154.839</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="lower-box">-->
        <!--                        <div class="link-box"><a href="index.html">Let’s Trade Now<i class="flaticon-upper-right-arrow"></i></a></div>-->
        <!--                        <p><i class="flaticon-down"></i> Change <span>-0.14%</span></p>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-lg-6 col-md-12 col-sm-12 trading-block">-->
        <!--                <div class="trading-block-one">-->
        <!--                    <div class="inner-box">-->
        <!--                        <div class="row clearfix">-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pr_15">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>usd</h6>-->
        <!--                                                <p>American Dollar</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-1.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Sell</span>-->
        <!--                                            <h5>0.64589</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pl_30">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>Aud</h6>-->
        <!--                                                <p>Australian Dolar</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-3.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Buy</span>-->
        <!--                                            <h5>0.64612</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="lower-box">-->
        <!--                        <div class="link-box"><a href="index.html">Let’s Trade Now<i class="flaticon-upper-right-arrow"></i></a></div>-->
        <!--                        <p class="upper"><i class="flaticon-down"></i> Change <span>+0.05%</span></p>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-lg-6 col-md-12 col-sm-12 trading-block">-->
        <!--                <div class="trading-block-one">-->
        <!--                    <div class="inner-box">-->
        <!--                        <div class="row clearfix">-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pr_15">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>usd</h6>-->
        <!--                                                <p>American Dollar</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-1.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Sell</span>-->
        <!--                                            <h5>1.42523</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pl_30">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>brl</h6>-->
        <!--                                                <p>Brazilian Real</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-4.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Buy</span>-->
        <!--                                            <h5>1.42540</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="lower-box">-->
        <!--                        <div class="link-box"><a href="index.html">Let’s Trade Now<i class="flaticon-upper-right-arrow"></i></a></div>-->
        <!--                        <p class="upper"><i class="flaticon-down"></i> Change <span>+0.25%</span></p>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-lg-6 col-md-12 col-sm-12 trading-block">-->
        <!--                <div class="trading-block-one">-->
        <!--                    <div class="inner-box">-->
        <!--                        <div class="row clearfix">-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pr_15">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>usd</h6>-->
        <!--                                                <p>American Dollar</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-1.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Sell</span>-->
        <!--                                            <h5>1.3785</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                            <div class="col-lg-6 col-md-6 col-sm-12 single-column">-->
        <!--                                <div class="single-item pl_30">-->
        <!--                                    <ul class="list-item">-->
        <!--                                        <li>-->
        <!--                                            <div class="text-box">-->
        <!--                                                <h6>gbp</h6>-->
        <!--                                                <p>Great Britain Pound</p>-->
        <!--                                            </div>-->
        <!--                                            <figure class="image-box"><img src="assets/images/icons/flag-5.png" alt=""></figure>-->
        <!--                                        </li>-->
        <!--                                        <li>-->
        <!--                                            <span>Buy</span>-->
        <!--                                            <h5>1.37846</h5>-->
        <!--                                        </li>-->
        <!--                                    </ul>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="lower-box">-->
        <!--                        <div class="link-box"><a href="index.html">Let’s Trade Now<i class="flaticon-upper-right-arrow"></i></a></div>-->
        <!--                        <p><i class="flaticon-down"></i> Change <span>-0.41%</span></p>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!-- trading-section end -->
        
        
        
        <section class="no-guarantee-cta" style="background-color:#000d1a; padding:50px 0; margin:40px 0 40px;">
            <div class="auto-container text-center">
                
                <!-- Catchy Heading -->
                <h2 style="color:white; font-size:32px; font-weight:700; margin-bottom:15px;">
                Joining Our Guarantee Program? No Worries!
                </h2>
                <p style="color:#b5c7d3; font-size:18px; margin-bottom:30px;">
                    You can still be a part of our community and access expert guidance through our 
                    <strong>Non-Guarantee Program</strong>. Connect with our admin team for more details.
                </p>
        
                <!-- Redirect Button -->
                <a href="/gurantee_free.php" 
                   style="
                        display:inline-block;
                        padding:15px 35px;
                        background:linear-gradient(45deg,#007bff,#00c6ff);
                        color:white;
                        font-size:18px;
                        border-radius:50px;
                        text-decoration:none;
                        font-weight:600;
                        transition:0.3s;
                   "
                   onmouseover="this.style.opacity='0.85'" 
                   onmouseout="this.style.opacity='1'">
                    Join Without Guarantee
                </a>
            </div>
        </section>
        
        <!-- chooseus-section -->
        <section class="chooseus-section" style="background-color: #001321;">
            <div class="auto-container">
                <div class="sec-title centred">
                    <h6>Key Highlights</h6>
                    <h2 style="color: white;">Empowering Your Financial Journey</h2>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-4 col-md-6 col-sm-12 left-column">
                        <div class="left-content">
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-2.png);"></div>
                                <span class="count-box">01</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-21.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-22.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Expert Market Analysis</h3>
                                <p>Out team provides in-depth insights and reliable stock market analysis to help you make informed investment decisions.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-2.png);"></div>
                                <span class="count-box">02</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-25.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-26.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Transparent Real Estate Deals</h3>
                                <p>We ensure every property transaction is clear, fair, and secure to perfect your valuable investments.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-2.png);"></div>
                                <span class="count-box">03</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-29.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-30.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Advances Crypto Mining Solutions</h3>
                                <p>Utilize cutting-edge technology for efficient and profitable cryptocurrency mining with minimal downtime.</p>
                            </div>
                        </div>
                    </div>
                    
  <!--                  <section class="py-5 bg-light">-->
  <!--<div class="container">-->
  <!--  <div class="row justify-content-center">-->
      <div class="col-lg-4 text-center">
        
        <h2 class="mb-4">Our Promo Video</h2>

        <div class="ratio ratio-16x9">
          <video autoplay controls muted>
            <source src="assets/images/video/video1.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        </div>

      </div>
<!--    </div>-->
<!--  </div>-->
<!--</section>-->

                    <!--<div class="col-lg-4 col-md-6 col-sm-12 image-column">-->
                    <!--    <figure class="image-box"><img src="assets/images/resource/chooseus-1.png" alt=""></figure>-->
                    <!--</div>-->
                    <div class="col-lg-4 col-md-6 col-sm-12 left-column">
                        <div class="right-content align-3">
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-3.png);"></div>
                                <span class="count-box">04</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-23.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-24.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Trusted By Thousands</h3>
                                <p>We have a proven track record of satisfied investor in stock, real estate and crypto mining sectors.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-3.png);"></div>
                                <span class="count-box">05</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-27.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-28.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Comprehensive Supports & Guidence</h3>
                                <p>Our expert guide you throughout your investment journey, ensuring steady growth and risk management.</p>
                            </div>
                            <div class="single-item">
                                <div class="shape" style="background-image: url(assets/images/shape/shape-3.png);"></div>
                                <span class="count-box">06</span>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-31.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-32.png" alt=""></div>
                                </div>
                                <h3 style="color:white;">Cutting-edge & Technology Tools</h3>
                                <p>We provide you with the latest stok market analysis, real estate valuable software, and advanced crypto mining hardware to keep you always one step ahead.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- chooseus-section end -->


        <!-- challenge-section -->
        <!--<section class="challenge-section bg-color-2">-->
        <!--    <div class="auto-container">-->
        <!--        <div class="sec-title light">-->
        <!--            <h6>Join Fxzone</h6>-->
        <!--            <h2>Enter a Crypto Mining Challenge</h2>-->
        <!--        </div>-->
        <!--        <div class="tabs-box">-->
        <!--            <div class="upper-box">-->
        <!--                <div class="tab-btn-box">-->
        <!--                    <h5>Select account size</h5>-->
        <!--                    <ul class="tab-btns tab-buttons">-->
        <!--                        <li class="tab-btn active-btn" data-tab="#tab-1">-->
        <!--                            <div class="shape" style="background-image: url(assets/images/shape/shape-4.png);"></div>-->
        <!--                            <span>$10k</span>-->
        <!--                        </li>-->
        <!--                        <li class="tab-btn" data-tab="#tab-2">-->
        <!--                            <div class="shape" style="background-image: url(assets/images/shape/shape-4.png);"></div>-->
        <!--                            <span>$20k</span>-->
        <!--                        </li>-->
        <!--                        <li class="tab-btn" data-tab="#tab-3">-->
        <!--                            <div class="shape" style="background-image: url(assets/images/shape/shape-4.png);"></div>-->
        <!--                            <span>$30k</span>-->
        <!--                        </li>-->
        <!--                        <li class="tab-btn" data-tab="#tab-4">-->
        <!--                            <div class="shape" style="background-image: url(assets/images/shape/shape-4.png);"></div>-->
        <!--                            <span>$50k</span>-->
        <!--                        </li>-->
        <!--                        <li class="tab-btn" data-tab="#tab-5">-->
        <!--                            <div class="shape" style="background-image: url(assets/images/shape/shape-4.png);"></div>-->
        <!--                            <span>$1l</span>-->
        <!--                        </li>-->
        <!--                    </ul>-->
        <!--                </div>-->
        <!--                <div class="select-box">-->
        <!--                    <h5>Select preference</h5>-->
        <!--                    <select class="wide">-->
        <!--                        <option data-display="2-Step">2-Step</option>-->
        <!--                        <option value="1">1-Step</option>-->
        <!--                        <option value="2">2-Step</option>-->
        <!--                        <option value="3">3-Step</option>-->
        <!--                        <option value="4">4-Step</option>-->
        <!--                     </select>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="tabs-content">-->
        <!--                <div class="tab active-tab" id="tab-1">-->
        <!--                    <div class="table-outer">-->
        <!--                        <table>-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-33.png" alt=""><span>Lite</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-34.png" alt=""><span>Pro</span></div></th>-->
        <!--                                    <th class="big-data"><div class="title-box"><span>Features</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-35.png" alt=""><span>Infinity</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-36.png" alt=""><span>Ultimate</span></div></th>-->
        <!--                                </tr>    -->
        <!--                            </thead>-->
        <!--                            <tbody>-->
        <!--                                <tr>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Target</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Minimum Trading Days</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Daily Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Maximum Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Trading Period</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="icon-box close-icon"><i class="flaticon-close"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Refundable Fee</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Leverage</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td class="big-data"><button><i class="flaticon-down"></i><span>Click to Explore More</span></button></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                </tr>-->
        <!--                            </tbody>    -->
        <!--                        </table>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="tab" id="tab-2">-->
        <!--                    <div class="table-outer">-->
        <!--                        <table>-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-33.png" alt=""><span>Lite</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-34.png" alt=""><span>Pro</span></div></th>-->
        <!--                                    <th><div class="title-box"><span>Features</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-35.png" alt=""><span>Infinity</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-36.png" alt=""><span>Ultimate</span></div></th>-->
        <!--                                </tr>    -->
        <!--                            </thead>-->
        <!--                            <tbody>-->
        <!--                                <tr>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Target</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Minimum Trading Days</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Daily Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Maximum Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Trading Period</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="icon-box close-icon"><i class="flaticon-close"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Refundable Fee</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Leverage</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td class="big-data"><button><i class="flaticon-down"></i><span>Click to Explore More</span></button></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                </tr>-->
        <!--                            </tbody>    -->
        <!--                        </table>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="tab" id="tab-3">-->
        <!--                    <div class="table-outer">-->
        <!--                        <table>-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-33.png" alt=""><span>Lite</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-34.png" alt=""><span>Pro</span></div></th>-->
        <!--                                    <th><div class="title-box"><span>Features</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-35.png" alt=""><span>Infinity</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-36.png" alt=""><span>Ultimate</span></div></th>-->
        <!--                                </tr>    -->
        <!--                            </thead>-->
        <!--                            <tbody>-->
        <!--                                <tr>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Target</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Minimum Trading Days</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Daily Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Maximum Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Trading Period</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="icon-box close-icon"><i class="flaticon-close"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Refundable Fee</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Leverage</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td class="big-data"><button><i class="flaticon-down"></i><span>Click to Explore More</span></button></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                </tr>-->
        <!--                            </tbody>    -->
        <!--                        </table>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="tab" id="tab-4">-->
        <!--                    <div class="table-outer">-->
        <!--                        <table>-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-33.png" alt=""><span>Lite</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-34.png" alt=""><span>Pro</span></div></th>-->
        <!--                                    <th><div class="title-box"><span>Features</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-35.png" alt=""><span>Infinity</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-36.png" alt=""><span>Ultimate</span></div></th>-->
        <!--                                </tr>    -->
        <!--                            </thead>-->
        <!--                            <tbody>-->
        <!--                                <tr>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Target</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Minimum Trading Days</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Daily Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Maximum Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Trading Period</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="icon-box close-icon"><i class="flaticon-close"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Refundable Fee</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Leverage</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td class="big-data"><button><i class="flaticon-down"></i><span>Click to Explore More</span></button></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                </tr>-->
        <!--                            </tbody>    -->
        <!--                        </table>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="tab" id="tab-5">-->
        <!--                    <div class="table-outer">-->
        <!--                        <table>-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-33.png" alt=""><span>Lite</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-34.png" alt=""><span>Pro</span></div></th>-->
        <!--                                    <th><div class="title-box"><span>Features</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-35.png" alt=""><span>Infinity</span></div></th>-->
        <!--                                    <th><div class="title-box"><img src="assets/images/icons/icon-36.png" alt=""><span>Ultimate</span></div></th>-->
        <!--                                </tr>    -->
        <!--                            </thead>-->
        <!--                            <tbody>-->
        <!--                                <tr>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Target</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                    <td>₹800</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Minimum Trading Days</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                    <td>5 Days</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Daily Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>5%</td>-->
        <!--                                    <td>5%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Maximum Drawdown</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>10%</td>-->
        <!--                                    <td>10%</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Trading Period</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                    <td>No Limit</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="icon-box close-icon"><i class="flaticon-close"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Refundable Fee</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                    <td><div class="icon-box check-icon"><i class="flaticon-check"></i></div></td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td class="big-data"><i class="icon-one flaticon-fast-forward-double-right-arrows"></i><span>Leverage</span><i class="icon-two flaticon-fast-forward-double-right-arrows"></i></td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                    <td>1:100</td>-->
        <!--                                </tr>-->
        <!--                                <tr>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td class="big-data"><button><i class="flaticon-down"></i><span>Click to Explore More</span></button></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                    <td><div class="btn-box"><a href="index.html" class="theme-btn btn-three"><span>Get Plan Now</span></a></div></td>-->
        <!--                                </tr>-->
        <!--                            </tbody>    -->
        <!--                        </table>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="more-text light centred">-->
        <!--            <p>Find the Perfect Fit for You... <a href="service.html">Compare Challenges<i class="flaticon-upper-right-arrow"></i></a></p>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!-- challenge-section end -->


        <!-- working-section -->
        <section class="working-section centred" style="background-color: #001321;">
            <div class="auto-container">
                <div class="sec-title">
                    <h6>How it’s work</h6>
                    <h2 style="color: white;">Step-by-Step Market Analysis</h2>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-3 col-md-6 col-sm-12 working-block">
                        <div class="working-block-one">
                            <div class="inner-box">
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-37.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-38.png" alt=""></div>
                                </div>
                                <h6>Identify Market Trend</h6>
                                <h3>Trend Analysis</h3>
                                <h5>01</h5>
                                <p>Understand whether the market is bullish, bearish, or sideways using chart patterns & indicators.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 working-block">
                        <div class="working-block-one">
                            <div class="inner-box">
                                <p>Spot critical price zones where buyers or sellers strongly react in stocks or crypto.</p>
                                <h5>02</h5>
                                <h6>Supports And Resistence</h6>
                                <h3>Mark Key Levels</h3>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-39.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-40.png" alt=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 working-block">
                        <div class="working-block-one">
                            <div class="inner-box">
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-41.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-42.png" alt=""></div>
                                </div>
                                <h6>Analyze Volumn & Momentum</h6>
                                <h3>Valumn & Momentum Study</h3>
                                <h5>03</h5>
                                <p>Evaluate the strength of price movements using RSI, MACD, and volume spikes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 working-block">
                        <div class="working-block-one">
                            <div class="inner-box">
                                <p>Set perfect entry, stop-loss, and target levels to make disciplined market decisions.</p>
                                <h5>04</h5>
                                <h6>Plan Entry & Exit</h6>
                                <h3>Trade Execution</h3>
                                <div class="icon-box">
                                    <div class="icon"><img src="assets/images/icons/icon-43.png" alt=""></div>
                                    <div class="overlay-icon"><img src="assets/images/icons/icon-44.png" alt=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="more-text centred">-->
                <!--    <p>Join the FXzone Revolution ... <a href="service.html">Take the First Step!<i class="flaticon-upper-right-arrow"></i></a></p>-->
                <!--</div>-->
            </div>
        </section>
        <!-- working-section end -->


        <!-- platform-section -->
        <!--<section class="platform-section bg-color-1">-->
        <!--    <div class="pattern-layer" style="background-image: url(assets/images/shape/shape-6.png);"></div>-->
        <!--    <div class="auto-container">-->
        <!--        <div class="row align-items-center">-->
        <!--            <div class="col-lg-6 col-md-12 col-sm-12 content-column">-->
        <!--                <div class="content_block_two">-->
        <!--                    <div class="content-box">-->
        <!--                        <div class="sec-title">-->
        <!--                            <h6>Platform</h6>-->
        <!--                            <h2>Perfect Platform for Every Trader</h2>-->
        <!--                        </div>-->
        <!--                        <div class="text-box">-->
        <!--                            <p>Denouncing pleasure and praising pain was born and  will give  -->
        <!--                                complete account of the system and expound.</p>-->
        <!--                        </div>-->
        <!--                        <div class="tabs-box">-->
        <!--                            <ul class="tab-btns tab-buttons">-->
        <!--                                <li class="tab-btn active-btn" data-tab="#tab-6"><h5>Meta <br />Trader 4</h5> <i class="flaticon-down"></i></li>-->
        <!--                                <li class="tab-btn" data-tab="#tab-7"><h5>Meta <br />Trader 5</h5> <i class="flaticon-down"></i></li>-->
        <!--                            </ul>-->
        <!--                            <div class="tabs-content">-->
        <!--                                <div class="tab active-tab" id="tab-6">-->
        <!--                                    <div class="inner-box">-->
        <!--                                        <ul class="list-style-one clearfix">-->
        <!--                                            <li><i class="flaticon-double-arrow"></i><span>Perfect for both beginners & advanced traders.</span></li>-->
        <!--                                            <li><i class="flaticon-double-arrow"></i><span>Access live price movements.</span></li>-->
        <!--                                            <li><i class="flaticon-double-arrow"></i><span>Charts with 30+ built-in technical indicators.</span></li>-->
        <!--                                        </ul>-->
        <!--                                        <a href="index.html" class="theme-btn btn-three"><span>Explore MT4</span></a>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                                <div class="tab" id="tab-7">-->
        <!--                                    <div class="inner-box">-->
        <!--                                        <ul class="list-style-one clearfix">-->
        <!--                                            <li><i class="flaticon-double-arrow"></i><span>Perfect for both beginners & advanced traders.</span></li>-->
        <!--                                            <li><i class="flaticon-double-arrow"></i><span>Access live price movements.</span></li>-->
        <!--                                            <li><i class="flaticon-double-arrow"></i><span>Charts with 30+ built-in technical indicators.</span></li>-->
        <!--                                        </ul>-->
        <!--                                        <a href="index.html" class="theme-btn btn-three"><span>Explore MT5</span></a>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-lg-6 col-md-12 col-sm-12 image-column">-->
        <!--                <div class="image-box">-->
        <!--                    <span class="big-text">Platform</span>-->
        <!--                    <figure class="image clearfix"><img src="assets/images/resource/platform-1.png" alt=""></figure>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!-- platform-section end -->


        <!-- news-section -->
        <section class="news-section sec-pad">
            <div class="auto-container">
                <div class="sec-title centred">
                    <h6>News & Updates</h6>
                    <h2>Latest Updates & Headlines</h2>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-6 col-sm-12 news-block">
                        <div class="news-block-one wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <div class="inner-box">
                                <h6>Ananta Production</h6>
                                <div class="image-box">
                                    <div id="newsSlider1" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="assets/images/news/1.PNG" class="d-block w-100" alt="">
                                            </div>
                                        
                                            <div class="carousel-item">
                                                <img src="assets/images/news/2.PNG" class="d-block w-100" alt="">
                                            </div>
                                        
                                            <div class="carousel-item">
                                                <img src="assets/images/news/3.PNG" class="d-block w-100" alt="" style="height:370px">
                                            </div>
                                        
                                        </div>
                                        
                                                <!-- Navigation arrows -->
                                        <!--<button class="carousel-control-prev" type="button" data-bs-target="#newsSlider1" data-bs-slide="prev">-->
                                        <!--    <span class="carousel-control-prev-icon"></span>-->
                                        <!--</button>-->
                                        
                                        <!--<button class="carousel-control-next" type="button" data-bs-target="#newsSlider1" data-bs-slide="next">-->
                                        <!--    <span class="carousel-control-next-icon"></span>-->
                                        <!--</button>-->
                                    </div>
                                        
                                    <div class="image-btn">
                                        <a href="#"><i class="flaticon-right-arrow"></i></a>
                                    </div>
                                </div>

                                <div class="lower-content">
                                    <h3>How to Start Your Stock Market Journey</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-4 col-md-6 col-sm-12 news-block">
                        <div class="news-block-one wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <div class="inner-box">
                                <h6>Current Rate Of Gold</h6>
                                <div class="image-box">
                                    <div id="newsSlider1" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="assets/images/news/g1.PNG" class="d-block w-100" alt="">
                                            </div>
                                        
                                            <div class="carousel-item">
                                                <img src="assets/images/news/g2.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                            <div class="carousel-item">
                                                <img src="assets/images/news/g3.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                            <div class="carousel-item">
                                                <img src="assets/images/news/g4.JPG" class="d-block w-100" alt="">
                                            </div>
                                            
                                            <div class="carousel-item">
                                                <img src="assets/images/news/g5.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                            <div class="carousel-item">
                                                <img src="assets/images/news/g6.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g7.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g8.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g9.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g10.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g11.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g12.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g13.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g14.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g15.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g16.PNG" class="d-block w-100" alt="">
                                            </div>
                                            
                                             <div class="carousel-item">
                                                <img src="assets/images/news/g17.PNG" class="d-block w-100" alt="">
                                            </div>
                                        
                                        </div>
                                        
                                                <!-- Navigation arrows -->
                                        <!--<button class="carousel-control-prev" type="button" data-bs-target="#newsSlider1" data-bs-slide="prev">-->
                                        <!--    <span class="carousel-control-prev-icon"></span>-->
                                        <!--</button>-->
                                        
                                        <!--<button class="carousel-control-next" type="button" data-bs-target="#newsSlider1" data-bs-slide="next">-->
                                        <!--    <span class="carousel-control-next-icon"></span>-->
                                        <!--</button>-->
                                    </div>
                                        
                                    <div class="image-btn">
                                        <a href="#"><i class="flaticon-right-arrow"></i></a>
                                    </div>
                                </div>

                                <div class="lower-content">
                                    <h3>The Impact of Global Events on Crypto Mining</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 news-block">
                        <div class="news-block-one wow fadeInUp animated" data-wow-delay="600ms" data-wow-duration="1500ms">
                            <div class="inner-box">
                                <h6>Company News</h6>
                                <div class="image-box">
                                    <figure class="image"><img src="assets/images/news/2.NG" alt="" style="height:370px"></figure>
                                    <div class="image-btn"><a href="#"><i class="flaticon-right-arrow"></i></a></div>
                                </div>
                                <div class="lower-content">
                                    <h3>Exciting New Features on Our RealEstate Platform</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- news-section end -->


        <!-- testimonial-section -->
        <section class="testimonial-section bg-color-1">
            <div class="auto-container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-12 col-sm-12 content-column">
                        <div class="content_block_three">
                            <div class="content-box">
                                <div class="sec-title">
                                    <h6>Testimonials</h6>
                                    <h2>Stories that <br />Inspire Confidence</h2>
                                </div>
                                <div class="text-box">
                                    <p>Real traders sharing their success stories with us.</p>
                                </div>
                                <div class="inner-box">
                                    <div class="curve-text">
                                        <h3>4.9</h3>
                                        <span class="curved-circle">Trader Feedback and Ratings &nbsp;&nbsp;.&nbsp;&nbsp;</span>
                                    </div>
                                    <div class="rating-box">
                                        <ul class="rating">
                                            <li><i class="flaticon-rate-star-button"></i></li>
                                            <li><i class="flaticon-rate-star-button"></i></li>
                                            <li><i class="flaticon-rate-star-button"></i></li>
                                            <li><i class="flaticon-rate-star-button"></i></li>
                                            <li><i class="flaticon-rate-star-button"></i></li>
                                        </ul>
                                        <p>From 2k Members, Reviewed <br />by <a href="index.html">Google</a>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 inner-column">
                        <div class="inner-content">
                            <div class="testimonial-block-one">
                                <div class="rating-box"><h6>5.0</h6><i class="flaticon-rate-star-button"></i></div>
                                <figure class="image-box"><img src="assets/images/resource/testimonial-1.png" alt=""></figure>
                                <div class="inner-box">
                                    <h3>XTB</h3>
                                    <p>XTBzone stands out with its excellent support team and fast payouts. The trading conditions are fair.</p>
                                    <h6><i class="flaticon-funds"></i><span>Profit Split: 85%</span></h6>
                                </div>
                            </div>
                            <div class="testimonial-block-one">
                                <div class="rating-box"><h6>4.9</h6><i class="flaticon-rate-star-button"></i></div>
                                <figure class="image-box"><img src="assets/images/resource/testimonial-2.png" alt=""></figure>
                                <div class="inner-box">
                                    <!--<h3>Maria L., <span>Germany</span></h3>-->
                                    <p>Thanks to XTBzone’s, Their advanced platform tools and educational resources made all.</p>
                                    <h6><i class="flaticon-funds"></i><span>Profit Split: 70%</span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- testimonial-section end -->


        <!-- brands-section -->
        <!--<section class="brands-section centred">-->
        <!--    <div class="auto-container">-->
        <!--        <div class="sec-title">-->
        <!--            <h6>Partners</h6>-->
        <!--            <h2>Our Trusted Global Partners</h2>-->
        <!--        </div>-->
        <!--        <div class="inner-container">-->
        <!--            <div class="inner-box">-->
        <!--                <ul class="brands-list clearfix">-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-1.png" alt=""></a></li>-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-2.png" alt=""></a></li>-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-3.png" alt=""></a></li>-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-4.png" alt=""></a></li>-->
        <!--                </ul>-->
        <!--                <ul class="brands-list clearfix">-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-5.png" alt=""></a></li>-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-6.png" alt=""></a></li>-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-7.png" alt=""></a></li>-->
        <!--                    <li><a href="index.html"><img src="assets/images/clients/clients-8.png" alt=""></a></li>-->
        <!--                </ul>-->
        <!--            </div>-->
        <!--            <div class="more-text centred">-->
        <!--                <h5>Collaborating with 100+ Global Partners Worldwide. <a href="service.html">All Partners<i class="flaticon-upper-right-arrow"></i></a></h5>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!-- brands-section end -->
</div>

        <!-- main-footer -->
    <script src="particles.js"></script>
    <script src="app.js"></script>
  
  <style>
#particles-js {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
} 
#particles-js,
canvas {
    pointer-events: none;
}

</style>

</body><!-- End of .page_wrapper -->

<!-- Mirrored from azim.hostlin.com/Fxzone/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 03 Dec 2025 11:21:14 GMT -->
</html>
<?php include "common/footer.php"; ?>


























