/*
*
*  Push Notifications codelab
*  Copyright 2015 Google Inc. All rights reserved.
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      https://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License
*
*/

// Version 0.1

'use strict';

// TODO
console.log('Started', self);

self.addEventListener('install', function(event) {
  self.skipWaiting();
  console.log('Installed', event);
});

self.addEventListener('activate', function(event) {
  console.log('Activated', event);
});

self.addEventListener('push', function(event) {
  console.log('Push message', event);
  

  /*
  //generic stuff from google dev website
  let promiseChain;
  if (event.data) {
    // We have data - lets use it
    console.log('event.data:' + event.data);
    console.log('event.data.json():' + event.data.json());
    promiseChain = Promise.resolve(event.data.json());
  } else {
    console.log('no event data');
    promiseChain = fetch('do_send.php')
      .then(response => response.json());
  }
  */

  //when ready for firefox v chrome testing, use below from OL sw
  /*
  if (event.data && event.data.json && event.data.json()) {
    // Firefox
    var d = event.data.json();
    event.waitUntil(showPushMessage(d));
  } else {

  }*/
  
  
  var title = 'Push message';

  //1. this is the simple show notification function from the google devs tutorial
  event.waitUntil(
    self.registration.showNotification(title, {
     body: 'The Message',
     icon: 'images/icon.png',
     tag: 'my-tag'
   }));

  //2. This is the more complex example that borrows from our ol-service-worker.js, whereby you need to do a request to some file before showing the notification. 
  //This is typically so you can either a) fetch a payload (i.e. content of the push such as title, body, image) BUT the payload has to have been encrypted when sent (and this is beyond our scope)
  //Or b) pass the subscription.endpoint (i.e. something unique to who the user is) to some URL to fetch something. This might be able to be used if i make a database that stores the endpoint's messages or preferences or something like that, and then have the notification's body/title be relevant to that...
/*

	//var url = 'do_send.php?subscriptionUrl=' + subscription.endpoint;
	var url = 'do_send.php';
	
  event.waitUntil(self.registration.pushManager.getSubscription()
    .then(function(subscription) {
console.log('1');
      //NO, this isn't supposed to be the actual send - this is just the receiving of it
      //This needs to be some URL that takes the subscription.endpoint as a parameter
      //And then fetches messages yet to be delivered, that belong to that endpoint...
      //That's quite a bit of work, and just to support browsers that don't support payloads (chrome now supports payloads)
      //BUT in order to do payloads, you gotta encrypt it! and that's where things get tough
      var url = 'do_send.php';

console.log('2, url: ' + url);
console.log('Subscription: ' + subscription);
console.log('Subscription.endpoint: ' + subscription.endpoint);

      return fetch(url).then(function(response) {
        response.json().then(function(data){
          console.log(data);

          title = data.title;
        self.registration.showNotification(title, {
           body: data.body || 'The Message',
           icon: 'images/icon.png',
           tag: 'my-tag'
         });

        });

      });
    }, function(e) {
      console.log('Error', e.message);
      return e;
    }));

*/

});

self.addEventListener('notificationclick', function(event) {
  console.log('Notification click: tag', event.notification.tag);
  // Android doesn't close the notification when you click it
  // See http://crbug.com/463146
  event.notification.close();
  var url = 'https://www.otherlevels.com';
  // Check if there's already a tab open with this URL.
  // If yes: focus on the tab.
  // If no: open a tab with the URL.
  event.waitUntil(
    clients.matchAll({
      type: 'window'
    })
    .then(function(windowClients) {
      console.log('WindowClients', windowClients);
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
        console.log('WindowClient', client);
        if (client.url === url && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});