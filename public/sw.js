const CACHE_NAME = 'fleetco-v1';
const ASSETS = [
  '/',
  '/manifest.json',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
  'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap'
];

// Install Event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(ASSETS);
    })
  );
});

// Activate Event (Cleanup old caches)
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('[SW] Clearing old cache:', cache);
            return caches.delete(cache);
          }
        })
      );
    })
  );
});

// Fetch Event (Network First for data, Cache First for assets)
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      // Force update if it's the root or dashboard
      if (event.request.url.includes('/dashboard') || event.request.url.endsWith(':8000/') || event.request.url.endsWith(':8000')) {
         return fetch(event.request);
      }
      return response || fetch(event.request);
    })
  );
});

// Background Sync for Telematics (If network fails)
self.addEventListener('sync', event => {
  if (event.tag === 'sync-telematics') {
    event.waitUntil(uploadPendingTelematics());
  }
});

async function uploadPendingTelematics() {
  // Logic to read from IndexedDB and POST to /api/telematics
  console.log('[SW] Uploading pending telematics data...');
}
