<?php
// Service Worker Demo.
// This program uses s-worker.js as the service worker.

echo <<<EOF
<!DOCTYPE html>
<html>
<head>
<script>
if('serviceWorker' in navigator) {
  var mydiv = document.querySelector('#div');
  navigator.serviceWorker.register('/examples.js/s-worker.js')
  .then((test) => { console.log("ServiceWorker is: %s", test.active.state);
                    showFilesList(test.active.state);
                    return test;
  })
  .then(waitUntilInstalled)
  .catch(function(error) {
    // Something went wrong during registration. The service-worker.js file
    // might be unavailable or contain a syntax error.
    document.querySelector('div').textContent = error;
  });
}
function waitUntilInstalled(registration) {
  return new Promise(function(resolve, reject) {
    if(registration.installing) {
      // If the current registration represents the "installing" service worker, then wait
      // until the installation step (during which the resources are pre-fetched) completes
      // to display the file list.
      registration.installing.addEventListener('statechange', function(e) {
        if(e.target.state == 'installed') {
          resolve('installed');
        } else if(e.target.state == 'redundant') {
          reject('redundant');
        }
      });
    } else {
      // Otherwise, if this isn't the "installing" service worker, then installation must have been
      // completed during a previous visit to this page, and the resources are already pre-fetched.
      // So we can show the list of files right away.
      resolve('already installed');
    }
  }).then(d => console.log("waitUntil:", d)).catch(err => console.log("waitUntil err:", err));
}

function showFilesList(text) {
  console.log("text:", text);
  document.querySelector('#div').innerHTML = text;
}  
</script>
</head>
<body>
<h1>Service Worker Test</h1>
<div id="div"></div>
</body>
</html>
EOF;
