self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    return self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // We intentionally do NOT call event.respondWith() here.
    // This allows the browser to handle the network request naturally.
    // This listener is required for the browser to recognize this as an installable PWA.
});
