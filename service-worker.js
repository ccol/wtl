var dataCacheName = 'weatherData-v1';
var cacheName = 'weatherPWA-step-6-2';
//var filesToCache = [];
var filesToCache = ['/pwa/',
  '/pwa/index.html',
  '/pwa/scripts/app.js',
  '/pwa/styles/inline.css',
  '/pwa/images/clear.png',
  '/pwa/images/cloudy-scattered-showers.png',
  '/pwa/images/cloudy.png',
  '/pwa/images/fog.png',
  '/pwa/images/ic_add_white_24px.svg',
  '/pwa/images/ic_refresh_white_24px.svg',
  '/pwa/images/partly-cloudy.png',
  '/pwa/images/rain.png',
  '/pwa/images/scattered-showers.png',
  '/pwa/images/sleet.png',
  '/pwa/images/snow.png',
  '/pwa/images/thunderstorm.png',
  '/pwa/images/wind.png']; //this is the list of resources that are to be cached, so the page works offline (when these are loaded)

self.addEventListener('install', function(e) {
  console.log('[ServiceWorker] Install');
  e.waitUntil(
    caches.open(cacheName).then(function(cache) { //open the cache and provide a cache name - this allows us to version files, or separate data from the app shell so that we can easily update one but not affect the other.
      console.log('[ServiceWorker] Caching app shell');
      //WARNING! YOU'LL GET A STUPID ERROR IN THE CHROME DEVTOOLS THAT JUST SAYS ERROR LINE 1, IF THE LIST OF URLS ARE INVALID...WHICH THEY WERE COZ THEY WERE ALL STARTING WITH FORWARD SLASH, AS THEY ASSUME ROOT LEVEL
      return cache.addAll(filesToCache); //this takes a list of URLs (i.e. resources the page will need to cache, such as images, js files, css files etc. then fetches them from the server, and adds the response to the cache.
    })
  );
});

self.addEventListener('activate', function(e) {
  console.log('[ServiceWorker] Activate');
  e.waitUntil(
    caches.keys().then(function(keyList) { //this ensures that the service worker updates its cache whenever any of the app shell files change...for this to work, you increment the cacheName var at the top of this file, and this triggers the mismatch if statement, and deletes the cache.
      return Promise.all(keyList.map(function(key) {
        if (key !== cacheName && key !== dataCacheName) {
          console.log('[ServiceWorker] Removing old cache', key);
          return caches.delete(key);
        }
      }));
    })
  );
  return self.clients.claim();
});

//service workers provide the ability to intercept requests made from our PWA and handle them within the service worker. That menas we can determine how we want to handle the request and potentially serve our own cached response, rather than go out to the network.
self.addEventListener('fetch', function(e) {
  console.log('[Service Worker] Fetch', e.request.url);
  var dataUrl = 'https://query.yahooapis.com/v1/public/yql';
  if (e.request.url.indexOf(dataUrl) > -1) {
    /*
     * When the request URL contains dataUrl, the app is asking for fresh
     * weather data. In this case, the service worker always goes to the
     * network and then caches the response. This is called the "Cache then
     * network" strategy:
     * https://jakearchibald.com/2014/offline-cookbook/#cache-then-network
     */
    e.respondWith(
      caches.open(dataCacheName).then(function(cache) {
        return fetch(e.request).then(function(response){
          cache.put(e.request.url, response.clone());
          return response;
        });
      })
    );
  } else {
    /*
     * The app is asking for app shell files. In this scenario the app uses the
     * "Cache, falling back to the network" offline strategy:
     * https://jakearchibald.com/2014/offline-cookbook/#cache-falling-back-to-network
     */
    e.respondWith(
      caches.match(e.request).then(function(response) {
        return response || fetch(e.request);
      })
    );
  }
});
