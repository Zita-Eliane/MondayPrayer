// ─────────────────────────────────────────────────────────────────
// Koinonia — Service Worker (PWA)
// Fichier à placer dans : public/sw.js
// ─────────────────────────────────────────────────────────────────

const CACHE_NAME = 'koinonia-v1';

// Ressources à mettre en cache pour un accès hors-ligne
const STATIC_ASSETS = [
    '/',
    '/dashboard',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// ── Installation ──────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ── Activation (nettoyage des anciens caches) ─────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Stratégie : Network First, fallback cache ─────────────────────
self.addEventListener('fetch', (event) => {
    // Ignore les requêtes non-GET et les appels API POST
    if (event.request.method !== 'GET') return;

    // Ignore les requêtes vers d'autres origines
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Mise en cache de la réponse fraîche
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => {
                // Hors-ligne : on sert depuis le cache
                return caches.match(event.request)
                    .then(cached => cached || caches.match('/dashboard'));
            })
    );
});

// ── Notifications push ────────────────────────────────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();

    event.waitUntil(
        self.registration.showNotification(data.title || 'Koinonia', {
            body:    data.message || 'Nouveau message',
            icon:    '/icons/icon-192.png',
            badge:   '/icons/icon-96.png',
            tag:     data.type || 'koinonia',
            data:    { url: data.url || '/dashboard' },
            actions: [
                { action: 'open',    title: '📖 Ouvrir' },
                { action: 'dismiss', title: '✕ Ignorer' },
            ]
        })
    );
});

// ── Clic sur une notification ─────────────────────────────────────
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const url = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Si l'app est déjà ouverte, on focus
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.navigate(url);
                        return client.focus();
                    }
                }
                // Sinon on ouvre un nouvel onglet
                if (clients.openWindow) return clients.openWindow(url);
            })
    );
});
