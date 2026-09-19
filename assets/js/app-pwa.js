/**
 * Ananta PWA Service Worker Registration & Installation Prompt Handler
 */
(function () {
  'use strict';

  // 1. Register Service Worker
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker
        .register('/sw.js')
        .then(function (reg) {
          console.log('PWA Service Worker registered successfully:', reg.scope);
        })
        .catch(function (err) {
          console.warn('PWA Service Worker registration failed:', err);
        });
    });
  }

  // 2. Check standalone / installed mode
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  if (isStandalone) {
    document.body.classList.add('pwa-standalone');
    return; // Do not show install prompt inside standalone PWA
  }

  // 3. Handle Install Prompt
  var deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;

    if (localStorage.getItem('ananta_pwa_prompt_dismissed') === 'true') {
      return; // Respect user dismissal preference
    }

    showInstallBanner();
  });

  function showInstallBanner() {
    if (document.getElementById('ananta-pwa-banner')) return;

    var banner = document.createElement('div');
    banner.id = 'ananta-pwa-banner';
    banner.className = 'ananta-pwa-banner';
    banner.innerHTML =
      '<div class="pwa-info">' +
        '<img src="/assets/images/logo-stacked.png" class="pwa-icon" alt="Ananta" style="height: 48px; width: auto; object-fit: contain;">' +
        '<div class="pwa-text">' +
          '<h6>Ananta App</h6>' +
          '<p>Install for quick access & seamless trading</p>' +
        '</div>' +
      '</div>' +
      '<div class="pwa-actions">' +
        '<button id="btn-pwa-install-action" class="btn-pwa-install">Install</button>' +
        '<button id="btn-pwa-close-action" class="btn-pwa-close">&times;</button>' +
      '</div>';

    document.body.appendChild(banner);

    document.getElementById('btn-pwa-install-action').addEventListener('click', function () {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function (choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            console.log('User accepted PWA installation');
          }
          deferredPrompt = null;
          banner.remove();
        });
      }
    });

    document.getElementById('btn-pwa-close-action').addEventListener('click', function () {
      localStorage.setItem('ananta_pwa_prompt_dismissed', 'true');
      banner.remove();
    });
  }
})();
