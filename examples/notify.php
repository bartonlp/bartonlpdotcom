<?php
echo <<<EOF
<script>
function notifyMe() {
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    console.log("This browser does not support desktop notification");
  }

  // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification("Hi there!", {body: "This is the body",
          icon: "favicon.ico",
          image: "https://bartonphillips.net/images/Octocat.png"});

    console.log("hi 1:", notification);
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== "denied") {
    console.log("hi 2");
    Notification.requestPermission(function (permission) {
      // If the user accepts, let's create a notification
      console.log("hi 3");
      if (permission === "granted") {
        var notification = new Notification("Hi there!");
        console.log("hi 4:", notification);
      }
    });
  }
  console.log("hi 5");

  // At last, if the user has denied notifications, and you 
  // want to be respectful there is no need to bother them any more.
}

notifyMe();
</script>
EOF;
