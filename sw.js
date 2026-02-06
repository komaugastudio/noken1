const CACHE_NAME = "nokenmart-v1";
const urlsToCache = [
    "index.php",
    "offline.php",
    "manifest.json",
    "https://cdn.tailwindcss.com",
    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
    "https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap"
];

// Install Service Worker & Cache File Penting
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log("Membuka cache");
            return cache.addAll(urlsToCache);
        })
    );
});

// Aktivasi & Bersihkan Cache Lama
self.addEventListener("activate", (event) => {
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
});

// Fetch Data (Cek Cache dulu, kalau gagal ambil Network, kalau offline tampilkan offline.php)
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request).catch(() => {
                // Jika offline dan halaman HTML, tampilkan offline.php
                if (event.request.headers.get("accept").includes("text/html")) {
                    return caches.match("offline.php");
                }
            });
        })
    );
});