// Service Worker for Benka PWA
const CACHE_NAME = 'benka-v6';
const urlsToCache = [
  '/dashboard',
  '/attendance',
  '/employees',
  '/job-roles',
  '/statistics'
];

// Pages to use cache-first strategy for instant loading
const CACHE_FIRST_ROUTES = ['/dashboard', '/attendance', '/employees', '/job-roles', '/statistics'];

// Install event - cache essential resources
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker v6...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[SW] Cache opened, pre-caching pages...');
        // Pre-cache all pages so they're instant from the start
        return Promise.all(
          urlsToCache.map(url => {
            return fetch(url, {credentials: 'same-origin'})
              .then(response => {
                console.log('[SW] Cached:', url);
                return cache.put(url, response);
              })
              .catch(err => console.log('[SW] Failed to cache:', url, err));
          })
        );
      })
      .catch((error) => {
        console.log('[SW] Cache failed:', error);
      })
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - network first, fallback to cache
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Don't cache authentication routes to avoid CSRF token issues
  // Also don't cache build assets to always get fresh CSS/JS
  const url = new URL(event.request.url);
  if (url.pathname.includes('/login') ||
      url.pathname.includes('/register') ||
      url.pathname.includes('/logout') ||
      url.pathname.includes('/auth/') ||
      url.pathname.includes('/build/')) {
    event.respondWith(fetch(event.request));
    return;
  }

  // Use cache-first strategy for main app pages (instant loading)
  const shouldUseCacheFirst = CACHE_FIRST_ROUTES.some(route => url.pathname === route);

  if (shouldUseCacheFirst) {
    // Cache-first: Check cache FIRST, show immediately
    event.respondWith(
      caches.open(CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((cachedResponse) => {
          // Fetch fresh version in background
          const fetchPromise = fetch(event.request).then((networkResponse) => {
            cache.put(event.request, networkResponse.clone());
            return networkResponse;
          });

          // Return cached version IMMEDIATELY if available, otherwise wait for network
          return cachedResponse || fetchPromise;
        });
      })
    );
  } else {
    // Network-first strategy for other pages
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          // Clone the response before caching
          const responseToCache = response.clone();

          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });

          return response;
        })
        .catch(() => {
          // Network failed, try cache
          return caches.match(event.request).then((response) => {
            return response || new Response('Offline - Please check your connection', {
              status: 503,
              statusText: 'Service Unavailable',
              headers: new Headers({
                'Content-Type': 'text/plain'
              })
            });
          });
        })
    );
  }
});
