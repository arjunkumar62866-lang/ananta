/**
 * ANANTA Progressive Web App (PWA) Handler
 * Handles Service Worker Registration, Pure White Splash Screen,
 * Standalone Detection, Center Screen Install Modal, and Header Thin Line Banner.
 */
(function () {
  'use strict';

  // 1. Service Worker Registration
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker
        .register('/sw.js')
        .then(function (reg) {
          console.log('ANANTA PWA Service Worker registered successfully:', reg.scope);
        })
        .catch(function (err) {
          console.warn('ANANTA PWA Service Worker registration failed:', err);
        });
    });
  }

  // 2. Standalone Mode Detection & Pure White Splash Screen
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                     window.navigator.standalone === true ||
                     document.referrer.includes('android-app://');

  if (isStandalone) {
    document.documentElement.classList.add('pwa-standalone');

    // Create & Inject Pure Creamy White Splash Screen on App Launch
    var createSplash = function () {
      if (document.getElementById('ananta-pwa-splash')) return;
      var splash = document.createElement('div');
      splash.id = 'ananta-pwa-splash';
      splash.innerHTML = '<img src="/assets/images/pwa-icon.png" class="splash-logo" alt="ANANTA">';
      
      var mountSplash = function () {
        if (document.body) {
          document.body.classList.add('pwa-splash-active');
          document.body.appendChild(splash);
        }
      };

      if (document.body) {
        mountSplash();
      } else {
        document.addEventListener('DOMContentLoaded', mountSplash);
      }
    };

    createSplash();

    // Fade out splash screen when page load completes
    var removeSplash = function () {
      var splash = document.getElementById('ananta-pwa-splash');
      if (splash) {
        splash.classList.add('fade-out');
        if (document.body) document.body.classList.remove('pwa-splash-active');
        setTimeout(function () {
          if (splash && splash.parentNode) {
            splash.parentNode.removeChild(splash);
          }
        }, 400);
      }
    };

    if (document.readyState === 'complete') {
      setTimeout(removeSplash, 300);
    } else {
      window.addEventListener('load', function () {
        setTimeout(removeSplash, 300);
      });
    }

    // End execution for Standalone PWA mode (Do NOT show install prompts inside installed PWA)
    return;
  }

  // 3. Browser Install Logic (Uninstalled PWA Auto-Detect & Prompts)
  var deferredPrompt = null;
  var isIOS = /iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream;

  // Listen for native PWA install prompt event
  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;

    if (localStorage.getItem('ananta_pwa_installed') === 'true') {
      return;
    }

    initPwaPrompts();
  });

  // Track successful app installation
  window.addEventListener('appinstalled', function () {
    localStorage.setItem('ananta_pwa_installed', 'true');
    hideCenterModal();
    hideHeaderBanner();
    console.log('ANANTA PWA installed successfully');
  });

  // Auto-detect on page load if app is not installed
  window.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('ananta_pwa_installed') !== 'true') {
      initPwaPrompts();
    }
  });

  function initPwaPrompts() {
    if (localStorage.getItem('ananta_pwa_installed') === 'true') return;

    var centerDismissed = sessionStorage.getItem('ananta_pwa_center_dismissed') === 'true';
    var headerDismissed = sessionStorage.getItem('ananta_pwa_header_dismissed') === 'true';

    // Step 1: If center modal not yet dismissed in current session, show Center Modal
    if (!centerDismissed) {
      showCenterModal();
    } 
    // Step 2: If center modal was dismissed, show Thin Header Banner automatically
    else if (!headerDismissed) {
      showHeaderBanner();
    }
  }

  // ==================================================
  // A. CENTER SCREEN MODAL PROMPT
  // ==================================================
  function showCenterModal() {
    if (document.getElementById('ananta-pwa-center-modal')) return;

    var overlay = document.createElement('div');
    overlay.id = 'ananta-pwa-center-modal';
    overlay.className = 'ananta-pwa-modal-overlay';

    var modalHtml =
      '<div class="ananta-pwa-center-card">' +
        '<button id="btn-pwa-center-x" class="ananta-pwa-modal-close-btn">&times;</button>' +
        '<img src="/assets/images/pwa-icon.png" class="ananta-pwa-center-logo" alt="ANANTA Logo">' +
        '<h5 class="ananta-pwa-center-title">Install ANANTA App</h5>' +
        '<p class="ananta-pwa-center-desc">' +
          (isIOS ? 'Tap Share and select "Add to Home Screen" to install ANANTA App for faster access.' : 'Install the ANANTA official app on your phone for a smoother and faster experience.') +
        '</p>' +
        '<div class="ananta-pwa-center-actions">' +
          '<button id="btn-pwa-center-install" class="btn-pwa-center-install">' + (isIOS ? 'Got It' : 'Install App') + '</button>' +
          '<button id="btn-pwa-center-cancel" class="btn-pwa-center-cancel">Not Now</button>' +
        '</div>' +
      '</div>';

    overlay.innerHTML = modalHtml;
    document.body.appendChild(overlay);

    // Install Button Handler
    document.getElementById('btn-pwa-center-install').addEventListener('click', function () {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function (choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            localStorage.setItem('ananta_pwa_installed', 'true');
            hideCenterModal();
          } else {
            switchToHeaderBanner();
          }
          deferredPrompt = null;
        });
      } else {
        // Fallback or iOS
        switchToHeaderBanner();
      }
    });

    // Close / Cancel Handlers -> Switch to Thin Header Line Banner
    document.getElementById('btn-pwa-center-x').addEventListener('click', switchToHeaderBanner);
    document.getElementById('btn-pwa-center-cancel').addEventListener('click', switchToHeaderBanner);
  }

  function switchToHeaderBanner() {
    sessionStorage.setItem('ananta_pwa_center_dismissed', 'true');
    hideCenterModal();
    if (sessionStorage.getItem('ananta_pwa_header_dismissed') !== 'true') {
      showHeaderBanner();
    }
  }

  function hideCenterModal() {
    var modal = document.getElementById('ananta-pwa-center-modal');
    if (modal) modal.remove();
  }

  // ==================================================
  // B. THIN HEADER LINE BANNER
  // ==================================================
  function showHeaderBanner() {
    if (document.getElementById('ananta-pwa-header-banner')) return;
    if (sessionStorage.getItem('ananta_pwa_header_dismissed') === 'true') return;

    var banner = document.createElement('div');
    banner.id = 'ananta-pwa-header-banner';
    banner.className = 'ananta-pwa-header-banner';

    banner.innerHTML =
      '<div id="ananta-pwa-banner-action" class="ananta-pwa-banner-left">' +
        '<img src="/assets/images/pwa-icon.png" class="ananta-pwa-banner-icon" alt="ANANTA">' +
        '<span class="ananta-pwa-banner-text">Install ANANTA App</span>' +
      '</div>' +
      '<div class="ananta-pwa-banner-right">' +
        '<button id="btn-pwa-banner-install" class="btn-pwa-banner-install">Install</button>' +
        '<button id="btn-pwa-banner-close" class="btn-pwa-banner-close" title="Close">&times;</button>' +
      '</div>';

    document.body.classList.add('has-pwa-header-banner');
    document.body.insertBefore(banner, document.body.firstChild);

    var triggerInstall = function () {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function (choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            localStorage.setItem('ananta_pwa_installed', 'true');
            hideHeaderBanner();
          }
          deferredPrompt = null;
        });
      } else {
        // Show center modal if prompt not available
        sessionStorage.removeItem('ananta_pwa_center_dismissed');
        hideHeaderBanner();
        showCenterModal();
      }
    };

    document.getElementById('ananta-pwa-banner-action').addEventListener('click', triggerInstall);
    document.getElementById('btn-pwa-banner-install').addEventListener('click', triggerInstall);

    // Cross button close handler
    document.getElementById('btn-pwa-banner-close').addEventListener('click', function (e) {
      e.stopPropagation();
      sessionStorage.setItem('ananta_pwa_header_dismissed', 'true');
      hideHeaderBanner();
    });
  }

  function hideHeaderBanner() {
    var banner = document.getElementById('ananta-pwa-header-banner');
    if (banner) {
      banner.remove();
      document.body.classList.remove('has-pwa-header-banner');
    }
  }

})();
