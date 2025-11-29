/* eslint-env serviceworker */
/* eslint-disable no-restricted-globals */
const CACHE_NAME = 'customer-portal-v1';
const urlsToCache = [
  '/',
  '/static/js/bundle.js',
  '/static/css/main.css',
  '/manifest.json',
  '/favicon.ico',
  '/icon-192x192.png',
  '/icon-512x512.png'
];

// Install event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event
self.addEventListener('fetch', (event) => {
  const req = event.request;

  // Bypass SW for non-GET or cross-origin requests (avoid CORS/login issues)
  const isCrossOrigin = new URL(req.url).origin !== self.location.origin;
  if (req.method !== 'GET' || isCrossOrigin) {
    return; // Let the network handle it
  }

  event.respondWith(
    caches.match(req).then((cached) => cached || fetch(req))
  );
});

// Activate event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
