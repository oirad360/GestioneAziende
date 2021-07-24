const cacheName='gestioneaziende_cache_v1'
const filesToCache=[
  "paginaOffline.html",
  "paginaOffline.css",
  "sfondoHeader.jpg"
]
self.addEventListener('install', event => {
	console.log('PWA Service Worker installing.');
    event.waitUntil(
    caches.open('gestioneaziende_service_worker').then(cache => {
      return cache.addAll(filesToCache)
      .then(() => self.skipWaiting());
    })
  )
});

self.addEventListener('activate',  event => {
  console.log('PWA Service Worker activating.');  
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
	
  );
});
/*
"public/assets/1.png",
  "public/assets/2.png",
  "public/assets/3.png",
  "public/assets/4.png",
  "public/assets/5.png",
  "public/assets/cuore.png",
  "public/assets/cuorevuoto.png",
  "public/assets/cuoretagliato.png",
  "public/assets/defaultAvatar.jpg",
  "public/assets/likeblu.png",
  "public/assets/likevuoto.png",
  "public/assets/logoUNICT.jpg",
  "public/assets/search.png",
  "public/assets/sfondoHeader.jpg",
  "public/assets/triangolo.png",
  "public/scripts/carrello.js",
  "public/scripts/env.js",
  "public/scripts/home.js",
  "public/scripts/info.js",
  "public/scripts/login.js",
  "public/scripts/modal.js",
  "public/scripts/prodotti.js",
  "public/scripts/profilo.js",
  "public/scripts/profiloEsterno.js",
  "public/scripts/recensioni.js",
  "public/scripts/sideNav.js",
  "public/scripts/signup.js",
  "public/scripts/suggerimentiProdotti.js",
  "public/style/containerProdotti.css",
  "public/style/footerHeader.css",
  "public/style/form.css",
  "public/style/modal.css",
  "public/style/recensioni.css",
  "public/style/sectionCarrello.css",
  "public/style/sectionRecensioni.css",
  "public/style/sectionHome.css",
  "public/style/sectionInfo.css",
  "public/style/sectionProfilo.css",
  "public/style/sectionProdotti.css",
  "public/index.php",
  "routes/web.php",
  "public/logo48.png",
  "public/logo144.png",
  */