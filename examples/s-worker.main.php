<?php
// service worker

echo <<<EOF
<!DOCTYPE html>
<html>
<head>
<script>
if('serviceWorker' in navigator) {
  var mydiv = document.querySelector('#div');
  navigator.serviceWorker.register('/examples/s-worker.js')
  //.then(waitUntilInstalled)
  .then(showFilesList)
  .catch(function(error) {
    // Something went wrong during registration. The service-worker.js file
    // might be unavailable or contain a syntax error.
    document.querySelector('div').textContent = error;
  });
}
function waitUntilInstalled(registration) {
  return new Promise(function(resolve, reject) {
    if (registration.installing) {
      // If the current registration represents the "installing" service worker, then wait
      // until the installation step (during which the resources are pre-fetched) completes
      // to display the file list.
      registration.installing.addEventListener('statechange', function(e) {
        if (e.target.state == 'installed') {
          resolve();
        } else if(e.target.state == 'redundant') {
          reject();
        }
      });
    } else {
      // Otherwise, if this isn't the "installing" service worker, then installation must have been
      // completed during a previous visit to this page, and the resources are already pre-fetched.
      // So we can show the list of files right away.
      resolve();
    }
  });
}

function showFilesList(text) {
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
