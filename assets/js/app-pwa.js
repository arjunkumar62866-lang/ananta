/**
 * ANANTA Progressive Web App (PWA) Handler
 * Handles Service Worker Registration, Splash Screen, Standalone Detection, and Custom Install Prompt
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

  // 2. Standalone Mode Detection & White Splash Screen Implementation
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                     window.navigator.standalone === true ||
                     document.referrer.includes('android-app://');

  if (isStandalone) {
    document.documentElement.classList.add('pwa-standalone');
    
    // Create & Inject White Splash Screen on App Launch
    var createSplash = function () {
      if (document.getElementById('ananta-pwa-splash')) return;
      var splash = document.createElement('div');
      splash.id = 'ananta-pwa-splash';
      splash.innerHTML = '<img src="/assets/images/pwa-icon.png" class="splash-logo" alt="ANANTA">';
      if (document.body) {
        document.body.classList.add('pwa-splash-active');
        document.body.appendChild(splash);
      } else {
        document.addEventListener('DOMContentLoaded', function () {
          document.body.classList.add('pwa-splash-active');
          document.body.appendChild(splash);
        });
      }
    };

    createSplash();

    // Smoothly fade out splash screen when page load completes
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

    // End execution for Standalone PWA mode (Install prompt should NOT show inside installed PWA)
    return;
  }

  // 3. Install Prompt Logic for Browsers
  var deferredPrompt = null;

  // Listen for native PWA installation event
  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;

    // Check installation or dismissal preferences
    if (localStorage.getItem('ananta_pwa_installed') === 'true') {
      return;
    }

    var dismissedUntil = localStorage.getItem('ananta_pwa_dismissed_until');
    if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
      return;
    }

    // Show Custom Install Prompt UI
    showInstallPrompt();
  });

  // Handle successful app installation event
  window.addEventListener('appinstalled', function () {
    localStorage.setItem('ananta_pwa_installed', 'true');
    hideInstallPrompt();
    console.log('ANANTA PWA installed successfully');
  });

  // Render Custom Install Prompt UI
  function showInstallPrompt() {
    if (document.getElementById('ananta-pwa-prompt')) return;

    var container = document.createElement('div');
    container.id = 'ananta-pwa-prompt';
    container.className = 'ananta-pwa-prompt-container';

    container.innerHTML =
      '<div class="ananta-pwa-card">' +
        '<div class="ananta-pwa-header">' +
          '<img src="/assets/images/pwa-icon.png" class="ananta-pwa-logo" alt="ANANTA Logo">' +
          '<div class="ananta-pwa-info">' +
            '<h6 class="ananta-pwa-title">Install ANANTA App</h6>' +
            '<p class="ananta-pwa-desc">Install the ANANTA app for a faster and better experience.</p>' +
          '</div>' +
        '</div>' +
        '<div class="ananta-pwa-actions">' +
          '<button id="btn-ananta-pwa-dismiss" class="btn-pwa-dismiss">Not Now</button>' +
          '<button id="btn-ananta-pwa-install" class="btn-pwa-install">Install App</button>' +
        '</div>' +
      '</div>';

    document.body.appendChild(container);

    // "Install App" button click handler
    document.getElementById('btn-ananta-pwa-install').addEventListener('click', function () {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function (choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            localStorage.setItem('ananta_pwa_installed', 'true');
            hideInstallPrompt();
          }
          deferredPrompt = null;
        });
      }
    });

    // "Not Now" secondary button click handler
    document.getElementById('btn-ananta-pwa-dismiss').addEventListener('click', function () {
      // Dismiss for 7 days
      var sevenDaysMs = 7 * 24 * 60 * 60 * 1000;
      localStorage.setItem('ananta_pwa_dismissed_until', (Date.now() + sevenDaysMs).toString());
      hideInstallPrompt();
    });
  }

  function hideInstallPrompt() {
    var promptEl = document.getElementById('ananta-pwa-prompt');
    if (promptEl) {
      promptEl.remove();
    }
  }

  // 4. iOS Safari Add to Home Screen Instructions (If iOS & not standalone)
  var isIOS = /iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream;
  if (isIOS && !isStandalone) {
    if (
      localStorage.getItem('ananta_pwa_installed') !== 'true' &&
      (!localStorage.getItem('ananta_pwa_dismissed_until') || Date.now() >= parseInt(localStorage.getItem('ananta_pwa_dismissed_until'), 10))
    ) {
      window.addEventListener('load', function () {
        setTimeout(showIOSPrompt, 1500);
      });
    }
  }

  function showIOSPrompt() {
    if (document.getElementById('ananta-pwa-prompt')) return;

    var container = document.createElement('div');
    container.id = 'ananta-pwa-prompt';
    container.className = 'ananta-pwa-prompt-container';

    container.innerHTML =
      '<div class="ananta-pwa-card">' +
        '<div class="ananta-pwa-header">' +
          '<img src="/assets/images/pwa-icon.png" class="ananta-pwa-logo" alt="ANANTA Logo">' +
          '<div class="ananta-pwa-info">' +
            '<h6 class="ananta-pwa-title">Install ANANTA App</h6>' +
            '<p class="ananta-pwa-desc">Tap <span style="font-weight:700;">Share</span> and select <span style="font-weight:700;">"Add to Home Screen"</span> to install.</p>' +
          '</div>' +
        '</div>' +
        '<div class="ananta-pwa-actions">' +
          '<button id="btn-ananta-pwa-dismiss" class="btn-pwa-dismiss">Not Now</button>' +
          '<button id="btn-ananta-pwa-install" class="btn-pwa-install">Got It</button>' +
        '</div>' +
      '</div>';

    document.body.appendChild(container);

    document.getElementById('btn-ananta-pwa-install').addEventListener('click', function () {
      var sevenDaysMs = 7 * 24 * 60 * 60 * 1000;
      localStorage.setItem('ananta_pwa_dismissed_until', (Date.now() + sevenDaysMs).toString());
      hideInstallPrompt();
    });

    document.getElementById('btn-ananta-pwa-dismiss').addEventListener('click', function () {
      var sevenDaysMs = 7 * 24 * 60 * 60 * 1000;
      localStorage.setItem('ananta_pwa_dismissed_until', (Date.now() + sevenDaysMs).toString());
      hideInstallPrompt();
    });
  }
})();
