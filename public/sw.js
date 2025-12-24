// Service Worker for Benka PWA - Only cache assets, not pages (CSRF issue)
const CACHE_NAME = 'benka-v9';

// Install event - skip waiting to activate immediately
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker v9 (assets only)...');
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

// Fetch event - ONLY cache build assets (CSS/JS), never cache HTML pages
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  const url = new URL(event.request.url);

  // ONLY cache build assets (CSS/JS/fonts) - NEVER cache HTML pages (CSRF tokens)
  if (url.pathname.includes('/build/') ||
      url.pathname.endsWith('.css') ||
      url.pathname.endsWith('.js') ||
      url.pathname.endsWith('.woff') ||
      url.pathname.endsWith('.woff2') ||
      url.pathname.endsWith('.ttf')) {

    event.respondWith(
      caches.open(CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((cachedResponse) => {
          // Return cached version immediately if available
          if (cachedResponse) {
            console.log('[SW] Serving from cache:', url.pathname);
            return cachedResponse;
          }
          // Otherwise fetch and cache
          return fetch(event.request).then((response) => {
            console.log('[SW] Caching asset:', url.pathname);
            cache.put(event.request, response.clone());
            return response;
          });
        });
      })
    );
    return;
  }

  // For everything else (HTML pages, API calls), just fetch normally - NO CACHING
  // This prevents CSRF token issues
});
