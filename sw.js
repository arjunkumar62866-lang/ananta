const CACHE_NAME = 'ananta-pwa-v1';
const STATIC_ASSETS = [
  '/',
  '/index.php',
  '/assets/css/bootstrap.css',
  '/assets/css/style.css',
  '/assets/css/color.css',
  '/assets/images/logo.png',
  '/manifest.json'
];

// Install event - Cache core static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('PWA Cache AddAll Warning:', err);
      });
    }).then(() => self.skipWaiting())
  );
});

// Activate event - Clean old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event - Network first strategy for dynamic/API/PHP requests to prevent stale data
self.addEventListener('fetch', (event) => {
  const requestUrl = new URL(event.request.url);

  // Skip caching non-GET requests or sensitive PHP dashboard/POST operations
  if (event.request.method !== 'GET' || requestUrl.pathname.endsWith('.php')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Cache static CSS, JS, images safely
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          (requestUrl.pathname.startsWith('/assets/') || requestUrl.pathname.endsWith('.json'))
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        // Fallback to cache if network fails (offline static asset)
        return caches.match(event.request);
      })
  );
});
