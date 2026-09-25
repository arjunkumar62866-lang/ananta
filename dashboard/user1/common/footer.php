<!--Start footer-->
<footer class="footer border-top py-3" style="position: relative !important; width: 100% !important; margin-top: auto !important; flex-shrink: 0 !important; background: #ffffff !important; color: #64748b !important; border-top: 1px solid #e2e8f0 !important; font-size: 13.5px; font-weight: 500; clear: both !important; left: 0 !important; bottom: 0 !important; z-index: 100 !important;">
  <div class="container-fluid">
    <div class="text-center">
      Copyright © <?php echo date('Y'); ?> <strong style="color: #0284c7;">Ananta Multi Trade Private Limited</strong>. All Rights Reserved.
    </div>
  </div>
<!-- APPLE GLASSMORPHIC FLOATING MOBILE NAVBAR WITH SLIDING INDICATOR -->
<style>
.apple-glass-bottom-nav {
  position: fixed;
  bottom: 12px;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 24px);
  max-width: 400px;
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(25px) saturate(190%) !important;
  -webkit-backdrop-filter: blur(25px) saturate(190%) !important;
  border: 1px solid rgba(226, 232, 240, 0.8) !important;
  border-radius: 100px !important;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12), 0 2px 6px rgba(0, 0, 0, 0.04) !important;
  padding: 3px 4px;
  z-index: 99999 !important;
}

.apple-slide-track {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.apple-slide-pill {
  position: absolute;
  top: 0;
  bottom: 0;
  height: 100%;
  background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
  border-radius: 100px;
  box-shadow: 0 3px 10px rgba(2, 132, 199, 0.35);
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1;
}

.apple-nav-link {
  position: relative;
  z-index: 2;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6px 0;
  color: #334155 !important;
  text-decoration: none !important;
  font-size: 10.5px;
  font-weight: 700;
  transition: color 0.25s ease;
  border-radius: 100px;
  cursor: pointer;
}

.apple-nav-link i {
  font-size: 16px;
  margin-bottom: 2px;
  color: #334155;
  transition: transform 0.25s ease, color 0.25s ease;
}

.apple-nav-link.active {
  color: #ffffff !important;
}

.apple-nav-link.active i {
  color: #ffffff !important;
  transform: scale(1.1);
}

/* Persistent display on mobile screens up to 991px */
@media (min-width: 992px) {
  .apple-glass-bottom-nav {
    display: none !important;
  }
}
</style>

<div class="apple-glass-bottom-nav">
  <div class="apple-slide-track">
    <div class="apple-slide-pill"></div>

    <?php
    $currentUserPage = basename($_SERVER['PHP_SELF']);
    ?>
    <a href="index.php" class="apple-nav-link <?php echo ($currentUserPage === 'index.php') ? 'active' : ''; ?>">
      <i class="fa fa-home"></i>
      <span>Home</span>
    </a>
    <a href="my_team.php" class="apple-nav-link <?php echo (in_array($currentUserPage, ['my_team.php', 'my_direct.php', 'left_team.php', 'right_team.php', 'tree.php'])) ? 'active' : ''; ?>">
      <i class="fa fa-users"></i>
      <span>My Team</span>
    </a>
    <a href="p2p.php" class="apple-nav-link <?php echo ($currentUserPage === 'p2p.php') ? 'active' : ''; ?>">
      <i class="fa fa-exchange"></i>
      <span>P2P</span>
    </a>
    <a href="income_wallet.php" class="apple-nav-link <?php echo (in_array($currentUserPage, ['income_wallet.php', 'main_wallet.php', 'activate.php', 'withdraw.php'])) ? 'active' : ''; ?>">
      <i class="fa fa-credit-card"></i>
      <span>Wallet</span>
    </a>
    <a href="javascript:void(0);" class="apple-nav-link toggle-menu-btn" id="btnUserMoreMenu">
      <i class="fa fa-bars"></i>
      <span>More</span>
    </a>
  </div>
</div>

<script>
function initUserAppleSlidePill() {
  var activeLink = document.querySelector('.apple-nav-link.active');
  var slidePill = document.querySelector('.apple-slide-pill');
  var track = document.querySelector('.apple-slide-track');

  if (activeLink && slidePill && track) {
    var trackRect = track.getBoundingClientRect();
    var linkRect = activeLink.getBoundingClientRect();

    var leftOffset = linkRect.left - trackRect.left;
    var width = linkRect.width;

    slidePill.style.width = width + 'px';
    slidePill.style.transform = 'translateX(' + leftOffset + 'px)';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  initUserAppleSlidePill();
  window.addEventListener('resize', initUserAppleSlidePill);

  var moreBtn = document.getElementById('btnUserMoreMenu');
  if (moreBtn) {
    moreBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var wrapper = document.getElementById('wrapper');
      var sidebar = document.getElementById('sidebar-wrapper');
      if (wrapper) wrapper.classList.toggle('toggled');
      if (sidebar) sidebar.classList.toggle('toggled');
    });
  }

  var genericToggleBtns = document.querySelectorAll('.toggle-menu');
  genericToggleBtns.forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var wrapper = document.getElementById('wrapper');
      var sidebar = document.getElementById('sidebar-wrapper');
      if (wrapper) wrapper.classList.toggle('toggled');
      if (sidebar) sidebar.classList.toggle('toggled');
    });
  });
});
</script>
</footer>
<!--End footer-->

<!--start color switcher-->
<div class="right-sidebar">
  <div class="switcher-icon">
    <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
  </div>
  <div class="right-sidebar-content">
    <p class="mb-0">Gaussion Texture</p>
    <hr>
    <ul class="switcher">
      <li id="theme1"></li>
      <li id="theme2"></li>
      <li id="theme3"></li>
      <li id="theme4"></li>
      <li id="theme5"></li>
      <li id="theme6"></li>
    </ul>
    <p class="mb-0">Gradient Background</p>
    <hr>
    <ul class="switcher">
      <li id="theme7"></li>
      <li id="theme8"></li>
      <li id="theme9"></li>
      <li id="theme10"></li>
      <li id="theme11"></li>
      <li id="theme12"></li>
      <li id="theme13"></li>
      <li id="theme14"></li>
      <li id="theme15"></li>
    </ul>
  </div>
</div>
<!--end color switcher-->

<!-- Bootstrap core JavaScript-->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<!-- simplebar js -->
<script src="assets/plugins/simplebar/js/simplebar.js"></script>
<!-- sidebar-menu js -->
<script src="assets/js/sidebar-menu.js"></script>
<!-- Custom scripts -->
<script src="assets/js/app-script.js"></script>
<!-- Chart js -->
<script src="assets/plugins/Chart.js/Chart.min.js"></script>

<!-- Index js -->
<script src="assets/js/index.js"></script>
<!-- PWA Handler -->
<script src="/assets/js/app-pwa.js"></script>

</div><!-- End #wrapper -->
</body>
</html>
