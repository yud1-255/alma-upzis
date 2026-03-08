const CACHE_NAME = 'alma-cache-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    OFFLINE_URL,
];

// Install: pre-cache critical assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate: clean up old cache versions
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// Fetch: route requests to appropriate strategy
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Pass through non-GET requests (POST, PUT, etc.)
    if (request.method !== 'GET') {
        return;
    }

    // Pass through requests with credentials/cookies
    if (request.credentials === 'include' && isNavigationOrApi(request)) {
        // Allow static assets with credentials to still be cached
    }

    const url = new URL(request.url);

    // Static assets: cache-first
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Navigation and API requests: network-first with offline fallback
    event.respondWith(networkFirst(request));
});

function isStaticAsset(url) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/.test(url.pathname)
        || url.pathname.startsWith('/icons/')
        || url.pathname.startsWith('/images/');
}

function isNavigationOrApi(request) {
    return request.mode === 'navigate'
        || request.headers.get('X-Inertia')
        || request.url.includes('/api/');
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }

    try {
        const response = await fetch(request);
        if (response.ok) {
            await addToCache(request, response.clone());
        }
        return response;
    } catch (error) {
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        return response;
    } catch (error) {
        // For navigation requests, show offline fallback
        if (request.mode === 'navigate') {
            const cached = await caches.match(OFFLINE_URL);
            if (cached) {
                return cached;
            }
        }

        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

async function addToCache(request, response) {
    try {
        const cache = await caches.open(CACHE_NAME);
        await cache.put(request, response);
    } catch (error) {
        if (error.name === 'QuotaExceededError') {
            // Clear old cache and retry
            await caches.delete(CACHE_NAME);
            const cache = await caches.open(CACHE_NAME);
            await cache.addAll(PRECACHE_ASSETS);
            await cache.put(request, response);
        }
    }
}
