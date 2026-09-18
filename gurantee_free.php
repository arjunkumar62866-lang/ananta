<?php include "common/header.php"; 
include "common/connection.php";

if (isset($_POST['submit'])) {
    $date = date('Y-m-d'); 
    $time = date('H:i:s'); 
    $name = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    
    $sql = $pdo->prepare("Insert into tbl_guarantee(name,email,phone,message,date,time) values (?,?,?,?,?,?)");
    $sql->execute([$name, $email, $phone, $message, $date, $time]);
    
    echo "<script>alert('Your Query is noted down. We will contact you soon...')</script>";
}

?>
        <!-- Mobile Menu  -->
        <div class="mobile-menu">
            <div class="menu-backdrop"></div>
            <div class="close-btn"><i class="fas fa-times"></i></div>
            
            <nav class="menu-box">
                <div class="nav-logo"><a href="index.html"><img src="assets/images/logo-2.png" alt="" title=""></a></div>
                <div class="menu-outer"><!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header--></div>
                <div class="contact-info">
                    <h4>Contact Info</h4>
                    <ul>
                        <li>Chicago 12, Melborne City, USA</li>
                        <li><a href="tel:+8801682648101">+88 01682648101</a></li>
                        <li><a href="mailto:info@example.com">info@example.com</a></li>
                    </ul>
                </div>
                <div class="social-links">
                    <ul class="clearfix">
                        <li><a href="index.html"><span class="fab fa-twitter"></span></a></li>
                        <li><a href="index.html"><span class="fab fa-facebook-square"></span></a></li>
                        <li><a href="index.html"><span class="fab fa-pinterest-p"></span></a></li>
                        <li><a href="index.html"><span class="fab fa-instagram"></span></a></li>
                        <li><a href="index.html"><span class="fab fa-youtube"></span></a></li>
                    </ul>
                </div>
            </nav>
        </div><!-- End Mobile Menu -->

<div id="particles-js"></div>
        <!-- page-title -->
        <section class="page-title">
            <div class="bg-layer" style="background-image: url(assets/images/background/page-title.jpg);"></div>
            <div class="auto-container">
                <div class="content-box">
                    <h1>Without Guarantee Program</h1>
                    <ul class="bread-crumb">
                        <li><a href="index.html">Home</a></li>
                        <li><span>Without Guarantee Program</span></li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- page-title end -->


        <!-- contact-form-section -->
        <section class="contact-form-section">
            <div class="pattern-layer" style="background-image: url(assets/images/shape/shape-64.png);"></div>
            <div class="auto-container">
                <div class="sec-title centred">
                    <h6>Send Message</h6>
                    <h2>We’d Love to Hear From You</h2>
                </div>
                <div class="tabs-box">
                    <ul class="tab-btns tab-buttons clearfix">
                        <!--<li class="tab-btn active-btn" data-tab="#tab-1">Existing Trader</li>-->
                        <!--<li class="tab-btn" data-tab="#tab-2">New Trader</li>-->
                    </ul>
                    <div class="tabs-content">
                        <div class="tab active-tab" id="tab-1">
                            <form method="post" action="" class="contact-form">
                                <div class="row clearfix">
                                    <div class="col-lg-12 col-md-12 col-sm-12 left-column">
                                        <div class="left-content">
                                            
                                            <div class="form-group">
                                                <label>Your Full Name</label>
                                                <input type="text" name="username" placeholder="Enter Full Name" required>
                                            </div>
                            
                                            <div class="form-group">
                                                <label>Your Email</label>
                                                <input type="email" name="email" placeholder="Enter Email Address" required>
                                            </div>
                            
                                            <div class="form-group">
                                                <label>Your Phone</label>
                                                <input type="text" name="phone" placeholder="Enter Phone number" required>
                                            </div>
                            
                                            <div class="form-group">
                                                <label>Message</label>
                                                <input type="text" name="message" placeholder="Enter Message" required>
                                            </div>
                            
                                            <!-- Submit Button -->
                                            <div class="form-group message-btn">
                                                <button type="submit" class="theme-btn btn-five" name="submit">
                                                    <span>Send Message</span>
                                                </button>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- contact-form-section end -->



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

<!-- Mirrored from azim.hostlin.com/Fxzone/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 03 Dec 2025 11:24:18 GMT -->
</html>
<?php include "common/footer.php"; ?>
