// ==========================================
// SERVICE WORKER - MAIN LAUNDRY KURIR
// ==========================================
// PWA Service Worker untuk offline functionality dan caching

const CACHE_NAME = 'main-laundry-kurir-v4';
const STATIC_CACHE = 'main-laundry-static-v4';
const DYNAMIC_CACHE = 'main-laundry-dynamic-v4';

// Assets yang akan di-cache saat install (static assets)
// JANGAN cache halaman HTML! Hanya cache assets statis
const STATIC_ASSETS = [
    '/image/app.png',
    '/manifest.json',
];

// Offline page URL (untuk fallback saat network failed)
const OFFLINE_URL = '/kurir/offline';

// Install event - cache static assets & offline page
self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...');

    event.waitUntil(
        Promise.all([
            // Cache static assets
            caches.open(STATIC_CACHE)
                .then((cache) => {
                    console.log('[SW] Caching static assets');
                    return cache.addAll(STATIC_ASSETS);
                })
                .catch((error) => {
                    console.error('[SW] Failed to cache static assets:', error);
                }),
            // Cache offline page
            caches.open(CACHE_NAME)
                .then((cache) => {
                    console.log('[SW] Caching offline page');
                    return cache.add(new Request(OFFLINE_URL, { cache: 'reload' }));
                })
                .catch((error) => {
                    console.error('[SW] Failed to cache offline page:', error);
                })
        ])
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

    // Skip untuk Livewire navigation requests (wire:navigate)
    // Livewire SPA menggunakan header X-Livewire untuk fetch halaman
    const isLivewireRequest = request.headers.get('X-Livewire') !== null ||
                             request.headers.get('X-Livewire-Navigate') !== null;

    if (isLivewireRequest) {
        console.log('[SW] Skipping Livewire navigation request (fresh data):', request.url);
        return;
    }

    // Cek apakah request untuk halaman HTML (navigation)
    const isHTMLPage = request.destination === 'document' ||
                      request.headers.get('accept')?.includes('text/html');

    // Untuk halaman HTML: Network Only (SELALU dari network, JANGAN cache!)
    // Untuk assets (CSS, JS, images): Cache First (performa lebih cepat)
    if (isHTMLPage) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    console.log('[SW] Serving HTML from network (fresh):', request.url);
                    return networkResponse;
                })
                .catch((error) => {
                    // Kalau offline, serve offline page dari cache
                    console.log('[SW] Network failed, serving offline page:', request.url);

                    return caches.open(CACHE_NAME)
                        .then((cache) => {
                            return cache.match(OFFLINE_URL);
                        })
                        .then((response) => {
                            if (response) {
                                return response;
                            }

                            // Jika offline page tidak ada di cache, return basic HTML
                            return new Response(
                                '<html><body><h1>Offline</h1><p>Tidak ada koneksi internet. Silakan coba lagi.</p></body></html>',
                                { headers: { 'Content-Type': 'text/html' } }
                            );
                        });
                })
        );
    } else {
        // Untuk assets: Cache First (performa lebih cepat)
        event.respondWith(
            caches.match(request)
                .then((cachedResponse) => {
                    if (cachedResponse) {
                        console.log('[SW] Serving asset from cache:', request.url);
                        return cachedResponse;
                    }

                    // Jika tidak ada di cache, fetch dari network
                    return fetch(request)
                        .then((networkResponse) => {
                            const responseClone = networkResponse.clone();

                            caches.open(DYNAMIC_CACHE)
                                .then((cache) => {
                                    if (networkResponse.status === 200) {
                                        cache.put(request, responseClone).catch((err) => {
                                            console.log('[SW] Cache put failed (ignored):', err.message);
                                        });
                                    }
                                });

                            return networkResponse;
                        })
                        .catch((error) => {
                            console.error('[SW] Fetch failed:', error);
                        });
                })
        );
    }
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
