/**
 * BCM.net - Service Worker
 * Handles PWA caching and offline support
 */

const CACHE_NAME = 'bcm-net-v1';
const OFFLINE_URL = '/offline';

// Assets to pre-cache (static shell)
const PRECACHE_ASSETS = [
    '/',
    '/dashboard',
    '/manifest.json',
    '/favicon.ico',
    '/icon-192x192.png',
    '/icon-512x512.png',
];

// ── Install ────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch(() => {
                // Silently ignore missing assets during install
            });
        })
    );
    self.skipWaiting();
});

// ── Activate ───────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            )
        )
    );
    self.clients.claim();
});

// ── Fetch ──────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin GET requests
    if (request.method !== 'GET' || url.origin !== location.origin) {
        return;
    }

    // Skip API / admin / auth routes — always go network-first
    const skipPatterns = ['/api/', '/login', '/logout', '/sanctum'];
    if (skipPatterns.some((p) => url.pathname.startsWith(p))) {
        return;
    }

    // Network-first strategy for HTML pages
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    return response;
                })
                .catch(() =>
                    caches.match(request).then(
                        (cached) => cached || caches.match(OFFLINE_URL)
                    )
                )
        );
        return;
    }

    // Cache-first strategy for static assets (JS, CSS, images, fonts)
    const staticExtensions = ['.js', '.css', '.png', '.jpg', '.jpeg', '.svg', '.ico', '.woff', '.woff2'];
    const isStatic = staticExtensions.some((ext) => url.pathname.endsWith(ext));

    if (isStatic) {
        event.respondWith(
            caches.match(request).then(
                (cached) =>
                    cached ||
                    fetch(request).then((response) => {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                        return response;
                    })
            )
        );
    }
});
