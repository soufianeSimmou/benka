// Service Worker for Benka PWA
const CACHE_NAME = 'benka-v4';
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
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Cache opened');
        return cache.addAll(urlsToCache.map(url => new Request(url, {credentials: 'same-origin'})));
      })
      .catch((error) => {
        console.log('Cache failed:', error);
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
    // Cache-first: Check cache, then network, update cache in background
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        // Return cached version immediately if available
        if (cachedResponse) {
          // Update cache in background
          fetch(event.request).then((response) => {
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, response);
            });
          }).catch(() => {});

          return cachedResponse;
        }

        // No cache, fetch from network
        return fetch(event.request).then((response) => {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
          return response;
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
