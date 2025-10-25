// ==========================================
// SERVICE WORKER - MAIN LAUNDRY KURIR
// ==========================================
// PWA Service Worker untuk offline functionality dan caching

const CACHE_NAME = 'main-laundry-kurir-v1';
const STATIC_CACHE = 'main-laundry-static-v1';
const DYNAMIC_CACHE = 'main-laundry-dynamic-v1';

// Assets yang akan di-cache saat install (static assets)
const STATIC_ASSETS = [
    '/kurir/',
    '/image/app.png',
    '/manifest.json',
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...');

    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .catch((error) => {
                console.error('[SW] Failed to cache static assets:', error);
            })
    );

    // Force service worker untuk langsung aktif tanpa menunggu
    self.skipWaiting();
});

// Activate event - cleanup old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating service worker...');

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    // Hapus cache lama yang bukan dari versi ini
                    if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE && cacheName !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );

    // Claim semua clients yang aktif
    return self.clients.claim();
});

// Fetch event - serve dari cache dengan fallback ke network
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip untuk request non-GET
    if (request.method !== 'GET') {
        return;
    }

    // Skip untuk request dengan scheme yang tidak didukung (chrome-extension, etc)
    const url = new URL(request.url);
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Skip untuk request ke backend API yang perlu real-time data
    if (request.url.includes('/livewire/') ||
        request.url.includes('/api/') ||
        request.url.includes('/broadcasting/')) {
        return;
    }

    event.respondWith(
        caches.match(request)
            .then((cachedResponse) => {
                // Jika ada di cache, return cached response
                if (cachedResponse) {
                    console.log('[SW] Serving from cache:', request.url);
                    return cachedResponse;
                }

                // Jika tidak ada di cache, fetch dari network
                return fetch(request)
                    .then((networkResponse) => {
                        // Clone response karena response hanya bisa digunakan sekali
                        const responseClone = networkResponse.clone();

                        // Simpan ke dynamic cache untuk request selanjutnya
                        caches.open(DYNAMIC_CACHE)
                            .then((cache) => {
                                // Hanya cache response yang success dan dari http/https
                                if (networkResponse.status === 200) {
                                    cache.put(request, responseClone).catch((err) => {
                                        // Silently ignore cache errors (chrome-extension, etc)
                                        console.log('[SW] Cache put failed (ignored):', err.message);
                                    });
                                }
                            });

                        return networkResponse;
                    })
                    .catch((error) => {
                        console.error('[SW] Fetch failed:', error);

                        // Bisa return offline page di sini jika diperlukan
                        // return caches.match('/offline.html');
                    });
            })
    );
});

// Push notification event (untuk future implementation)
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');

    if (event.data) {
        const data = event.data.json();

        const options = {
            body: data.body,
            icon: '/image/app.png',
            badge: '/image/app.png',
            vibrate: [200, 100, 200],
            data: {
                url: data.url || '/',
            },
        };

        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');

    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Cek apakah ada window yang sudah terbuka
                for (const client of clientList) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }

                // Jika tidak ada, buka window baru
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Sync event (untuk background sync - future implementation)
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);

    if (event.tag === 'sync-orders') {
        event.waitUntil(
            // Sync logic di sini
            Promise.resolve()
        );
    }
});
