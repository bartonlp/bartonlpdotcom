<?php
$_site = require_once(getenv("HOME") . "/includes/siteautoload.class.php");
$_site['headFile'] = $_site['footerFile'] = $_site['bannerFile'] = null;

$S = new $_site['className']($_site);

if($_POST) {
  $id = $_POST['id'];
  $page = $_POST['page'];
  error_log("$page: $id");
  exit();
}

if($data = file_get_contents('php://input')) {
  $data = json_decode($data, true);
  $id = $data['id'];
  switch($data['which']) {
    case 1:
      $name = 'pagehide';
      break;
    case 2:
      $name = 'unload';
      break;
    case 3:
      $name = 'beforeunload';
      break;
  }
  error_log("beacon/$name: $id"); 
  exit();
}

$data = json_decode($data, true);

$h->script = <<<EOF
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
  <script>
var lastId = $S->LAST_ID;

(function($) {
  var trackerUrl = "testandroid.php";

  // start is done weather or not 'load' happens.

  $.ajax({
    url: trackerUrl,
    data: {page: 'start', id: lastId },
    type: 'post',
    success: function(data) {
           console.log("Start done: ",data);
         },
         error: function(err) {
           console.log(err);
         }
  });
  
  $(window).on("load", function(e) {
    $.ajax({
      url: trackerUrl,
      data: {page: 'load', 'id': lastId},
      type: 'post',
      success: function(data) {
             console.log("Load: " + data);
           },
           error: function(err) {
             console.log(err);
           }
    });
  });

  $(window).on('pagehide', function() {
    $.ajax({
      url: trackerUrl,
      data: {page: 'pagehide', id: lastId },
      type: 'post',
//      async: false, 
      success: function(data) {
             console.log("Beforeunload done: ",data);
           },
           error: function(err) {
             console.log(err);
           }
    });
  });

  $(window).on('beforeunload', function() {
    $.ajax({
      url: trackerUrl,
      data: {page: 'beforeunload', id: lastId },
      type: 'post',
//      async: false,
      success: function(data) {
             console.log("Beforeunload done: ",data);
           },
           error: function(err) {
             console.log(err);
           }
    });
  });

  $(window).on("unload", function(e) {
    $.ajax({
      url: trackerUrl,
      data: {page: 'unload', id: lastId },
      type: 'post',
//      async: false,
      success: function(data) {
             console.log("Unload done: ",data);
           },
           error: function(err) {
             console.log(err);
           }
    });
  });

  // We will use beacon also
  
  if(navigator.sendBeacon) {
    var d = new Date;

    $(window).on("pagehide", function() {
      navigator.sendBeacon('/testandroid.php', JSON.stringify({'id':lastId, 'which': 1}));
    });

    $(window).on("unload", function() {
      navigator.sendBeacon('/testandroid.php', JSON.stringify({'id':lastId, 'which': 2}));
    });

    $(window).on('beforeunload ',function() {
      navigator.sendBeacon('/testandroid.php', JSON.stringify({'id':lastId, 'which': 3}));    
    });
  } else {
    console.log("Beacon NOT SUPPORTED. On Chrome 37 enable 'expermental-web-platform-features'");
  }

  // Now lets try a timer to update the endtime

  setInterval(function() {
    //console.log("lastId: " +lastId);
    $.ajax({
      url: trackerUrl,
      data: {page: 'timer', id: lastId },
      type: 'post',
      success: function(data) {
             console.log(data);
           },
           error: function(err) {
             console.log(err);
           }
    });
  }, 5000);
})(jQuery);
  </script>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<h1>Android Test</h1>
$footer
EOF;

