
var cacheName = 'weatherPWA-step-6-1';
var filesToCache = [
                    '/weatherpwa/weatherpwa.html',
                    '/weatherpwa/js/app.js',
                    '/weatherpwa/styles/inline.css',
                    '/weatherpwa/images/clear.png',
                    '/weatherpwa/images/cloudy-scattered-showers.png',
                    '/weatherpwa/images/cloudy.png',
                    '/weatherpwa/images/fog.png',
                    '/weatherpwa/images/ic_add_white_24px.svg',
                    '/weatherpwa/images/ic_refresh_white_24px.svg',
                    '/weatherpwa/images/partly-cloudy.png',
                    '/weatherpwa/images/rain.png',
                    '/weatherpwa/images/scattered-showers.png',
                    '/weatherpwa/images/sleet.png',
                    '/weatherpwa/images/snow.png',
                    '/weatherpwa/images/thunderstorm.png',
                    '/weatherpwa/images/wind.png'
                   ];

self.addEventListener('install', function(e) {
  console.log('[ServiceWorker] Install');
  e.waitUntil(
              caches.open(cacheName).then(function(cache) {
    console.log('[ServiceWorker] Caching app shell');
    return cache.addAll(filesToCache);
  })
             );
});

self.addEventListener('activate', function(e) {
  console.log('[ServiceWorker] Activate');
  e.waitUntil(
              caches.keys().then(function(keyList) {
    return Promise.all(keyList.map(function(key) {
      if (key !== cacheName) {
        console.log('[ServiceWorker] Removing old cache', key);
        return caches.delete(key);
      }
    }));
  })
             );
    return self.clients.claim();
});

self.addEventListener('fetch', function(e) {
  console.log('[ServiceWorker] Fetch', e.request.url);
  e.respondWith(
                caches.match(e.request).then(function(response) {
    return response || fetch(e.request);
  })
               );
});
