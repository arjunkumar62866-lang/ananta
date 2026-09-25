const CACHE_NAME = 'ananta-pwa-v3';
const STATIC_ASSETS = [
  '/',
  '/index.php',
  '/assets/images/pwa-icon.png',
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

// Fetch event - Network-only for PHP / API dynamic requests to prevent stale session/user data
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // Skip caching non-GET requests, PHP files, dashboard URLs, or API requests
  if (
    request.method !== 'GET' ||
    url.pathname.endsWith('.php') ||
    url.pathname.includes('/dashboard/') ||
    url.pathname.includes('/api/')
  ) {
    return; // Pass through to network directly without SW caching
  }

  // Stale-while-revalidate for static assets (CSS, JS, Images, Fonts, Manifest)
  event.respondWith(
    caches.match(request).then((cachedResponse) => {
      const fetchPromise = fetch(request)
        .then((networkResponse) => {
          if (
            networkResponse &&
            networkResponse.status === 200 &&
            (url.pathname.startsWith('/assets/') || url.pathname.endsWith('.json'))
          ) {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseToCache);
            });
          }
          return networkResponse;
        })
        .catch(() => cachedResponse);

      return cachedResponse || fetchPromise;
    })
  );
});
