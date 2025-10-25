// ==========================================
// SERVICE WORKER - MAIN LAUNDRY KURIR
// ==========================================
// PWA Service Worker untuk offline functionality dan caching

const CACHE_NAME = 'main-laundry-kurir-v1';
const STATIC_CACHE = 'main-laundry-static-v1';
const DYNAMIC_CACHE = 'main-laundry-dynamic-v1';

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

// Push notification event
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');

    if (!event.data) {
        console.log('[SW] No data in push event');
        return;
    }

    try {
        const data = event.data.json();
        console.log('[SW] Push data:', data);

        const title = data.title || 'Pesanan Baru';
        const options = {
            body: data.body || 'Ada pesanan baru masuk',
            icon: data.icon || '/image/app.png',
            badge: data.badge || '/image/app.png',
            vibrate: data.vibrate || [200, 100, 200, 100, 200],
            tag: data.tag || 'transaction-notification',
            requireInteraction: true, // Notification tidak auto-close
            actions: data.actions || [
                {
                    action: 'view',
                    title: 'Lihat Detail'
                },
                {
                    action: 'close',
                    title: 'Tutup'
                }
            ],
            data: {
                transaction_id: data.transaction_id,
                url: data.url || '/kurir/pesanan',
                ...data
            }
        };

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (error) {
        console.error('[SW] Error parsing push data:', error);

        // Fallback notification jika parsing gagal
        const fallbackOptions = {
            body: 'Ada pesanan baru masuk',
            icon: '/image/app.png',
            badge: '/image/app.png',
            vibrate: [200, 100, 200],
            data: {
                url: '/kurir/pesanan'
            }
        };

        event.waitUntil(
            self.registration.showNotification('Pesanan Baru', fallbackOptions)
        );
    }
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked, action:', event.action);

    event.notification.close();

    // Handle notification actions
    if (event.action === 'close') {
        console.log('[SW] Close action - notification closed');
        return;
    }

    // Default action atau 'view' action - buka URL
    const urlToOpen = event.notification.data?.url || '/kurir/pesanan';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Cari window yang sudah terbuka ke base URL (kurir app)
                const baseUrl = new URL(urlToOpen, self.location.origin);

                for (const client of clientList) {
                    const clientUrl = new URL(client.url);

                    // Jika ada window yang terbuka ke kurir app, focus dan navigate
                    if (clientUrl.origin === baseUrl.origin &&
                        clientUrl.pathname.startsWith('/kurir')) {
                        console.log('[SW] Focusing existing window and navigating to:', baseUrl.href);
                        return client.focus().then(() => {
                            // Navigate ke URL target
                            return client.navigate(baseUrl.href);
                        });
                    }
                }

                // Jika tidak ada window yang terbuka, buka window baru
                console.log('[SW] Opening new window:', baseUrl.href);
                if (clients.openWindow) {
                    return clients.openWindow(baseUrl.href);
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
