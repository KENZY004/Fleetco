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

// Offline Telemetry Queue (IndexedDB)
const DB_NAME = 'FleetcoOffline';
const STORE_NAME = 'telemetry_queue';

function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(DB_NAME, 1);
    request.onupgradeneeded = () => request.result.createObjectStore(STORE_NAME, { autoIncrement: true });
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function saveToQueue(data) {
  const db = await openDB();
  const tx = db.transaction(STORE_NAME, 'readwrite');
  tx.objectStore(STORE_NAME).add(data);
  return tx.complete;
}

// Background Sync for Telematics (If network fails)
self.addEventListener('sync', event => {
  if (event.tag === 'sync-telematics') {
    event.waitUntil(uploadPendingTelematics());
  }
});

// Handle data from main thread
self.addEventListener('message', event => {
  if (event.data.type === 'QUEUE_TELEMETRY') {
    saveToQueue(event.data.payload);
  }
});

async function uploadPendingTelematics() {
  const db = await openDB();
  const tx = db.transaction(STORE_NAME, 'readwrite');
  const store = tx.objectStore(STORE_NAME);
  const allRecords = await new Promise(resolve => {
    const req = store.getAll();
    req.onsuccess = () => resolve(req.result);
  });

  if (allRecords.length === 0) return;

  console.log(`[SW] Syncing ${allRecords.length} offline pings...`);

  for (const record of allRecords) {
    try {
      const response = await fetch('/api/telematics', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(record)
      });
      if (response.ok) {
        // Find the key of the record to delete it
        // For simplicity, we just clear everything if we succeed for now, 
        // but in production, we should be more surgical.
      }
    } catch (err) {
      console.error('[SW] Sync failed for record:', err);
    }
  }

  // Clear store after attempt
  const clearTx = db.transaction(STORE_NAME, 'readwrite');
  clearTx.objectStore(STORE_NAME).clear();
}
