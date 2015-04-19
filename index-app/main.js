// BLP 2014-09-12 -- we give tracker in the define array but not in the
// function because tracker.js executes the function rather than
// returning it.
define(['jquery', 'phpdate',
        'tracker'], function(jQuery, date) {
  // BLP 2014-08-13 -- make this 'windows load' not 'jQuery ready' so that tracker.php gets called by
  // tracker.js before this ajax happens. This ajax can take a long time to end if the /etc/hosts
  // localhost has not been set to www.bartonphillips.dyndns.org. The ajax 'timeout' happens within
  // two seconds but the browser continues to wait and the menu bar icon keeps spinning for many
  // secondes.

  jQuery(window).load(function(e) {
    // Just do this at startup.
    // NOTE: if this is run on my home desktop and the /etc/hosts file has not been set to have
    // localhost also be www/bartonphillips.dyndns.org this will get an 'timeout' error after two
    // seconds BUT the browser will stay busy (the little spinning icon in the menu bar) for many
    // seconds. NO good way around this as far as I can tell.

    // The logic here is: This ajax call is being made by the local client browser NOT by the server.
    // If the local client's /etc/hosts file has not been modified (like on a tablet where one does
    // not have root access to allow that) then this ajax call will timeout in two seconds and change
    // the ip address to the local ip number.

    $.ajax({ url: 'http://www.bartonphillips.dyndns.org/uptest.php',
           data: {uptest: 'yes'},
           type: 'get',
           dataType: 'html',
           timeout: 2000, // two seconds
           success: function(d) {
      console.log("OK", d);
    },
           error: function(e) {
      console.log("ERR",e);
      e.abort();

      $(".uptest").each(function(i, v) {
        var x = $(v).attr("href");
        $(v).attr("href", x.replace("http://bartonphillips.dyndns.org/",
                                    "http://192.168.0.3/"));
      });
    }
    });
  });
  
  jQuery(document).ready(function($) {
    // style is done with a psudo 'heredoc'
    var style = (function () {
    /*
    <style>
        [data-smart-underline-container-id="3"] a[data-smart-underline-link-color="rgb(0, 0, 238)"], [data-smart-underline-container-id="3"] a[data-smart-underline-link-color="rgb(0, 0, 238)"]:visited {
      color: rgb(0, 0, 238);
             text-decoration: none !important;
             text-shadow: 0.03em 0 rgb(255, 255, 255), -0.03em 0 rgb(255, 255, 255), 0 0.03em rgb(255, 255, 255), 0 -0.03em rgb(255, 255, 255), 0.06em 0 rgb(255, 255, 255), -0.06em 0 rgb(255, 255, 255), 0.09em 0 rgb(255, 255, 255), -0.09em 0 rgb(255, 255, 255), 0.12em 0 rgb(255, 255, 255), -0.12em 0 rgb(255, 255, 255), 0.15em 0 rgb(255, 255, 255), -0.15em 0 rgb(255, 255, 255);
             background-color: transparent;
             background-image: -webkit-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -webkit-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -webkit-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
             background-image: -moz-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -moz-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -moz-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
             background-image: -o-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -o-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -o-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
             background-image: -ms-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -ms-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -ms-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
             background-image: linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
             -webkit-background-size: 0.05em 1px, 0.05em 1px, 1px 1px;
             -moz-background-size: 0.05em 1px, 0.05em 1px, 1px 1px;
             background-size: 0.05em 1px, 0.05em 1px, 1px 1px;
             background-repeat: no-repeat, no-repeat, repeat-x;
             background-position: 0% 89%, 100% 89%, 0% 89%;
    }

    [data-smart-underline-container-id="3"] a[data-smart-underline-link-color="rgb(0, 0, 238)"][data-smart-underline-link-small] {
      background-position: 0% 96%, 100% 96%, 0% 96%;
    }

    [data-smart-underline-container-id="3"] a[data-smart-underline-link-color="rgb(0, 0, 238)"][data-smart-underline-link-large] {
      background-position: 0% 86%, 100% 86%, 0% 86%;
    }

    [data-smart-underline-container-id="3"] a[data-smart-underline-link-color="rgb(0, 0, 238)"]::selection {
      text-shadow: 0.03em 0 #b4d5fe, -0.03em 0 #b4d5fe, 0 0.03em #b4d5fe, 0 -0.03em #b4d5fe, 0.06em 0 #b4d5fe, -0.06em 0 #b4d5fe, 0.09em 0 #b4d5fe, -0.09em 0 #b4d5fe, 0.12em 0 #b4d5fe, -0.12em 0 #b4d5fe, 0.15em 0 #b4d5fe, -0.15em 0 #b4d5fe;
      background: #b4d5fe;
    }

    [data-smart-underline-container-id="3"] a[data-smart-underline-link-color="rgb(0, 0, 238)"]::-moz-selection {
      text-shadow: 0.03em 0 #b4d5fe, -0.03em 0 #b4d5fe, 0 0.03em #b4d5fe, 0 -0.03em #b4d5fe, 0.06em 0 #b4d5fe, -0.06em 0 #b4d5fe, 0.09em 0 #b4d5fe, -0.09em 0 #b4d5fe, 0.12em 0 #b4d5fe, -0.12em 0 #b4d5fe, 0.15em 0 #b4d5fe, -0.15em 0 #b4d5fe;
      background: #b4d5fe;
    }
    </style>
    */
    }).toString().match(/\/\*\s*([\s\S]*?)\s*\*\//m)[1];

    $("body").attr("data-smart-underline-container-id", "3");
    $("a").attr({'data-smart-underline-link-color': "rgb(0, 0, 238)",
                 'data-smart-underline-link-large': ""});

    $("body").append(style);

    // Date Today
    function thetime() {
      var d = date("l F j, Y");
      var t = date("H:i:s"); // from phpdate.js
      $("#datetoday").html(d+"<br>The Time is: "+t);
      setInterval(thetime, 1000);
    }

    thetime();

    // BLP 2014-08-18 -- Kill caching on toweewx

    $("a[href='toweewx.php']").click(function() {
    // when toweewx clicked add t= Unix date to the href attribute 
      var d = date("U");
      $(this).attr("href", "toweewx.php?t="+d);
    });

    $("a[href='webstats-new.php']").click(function() {
      var d = date("U");
      $(this).attr("href", "webstats-new.php?t="+d);
    });
  });

/* not working right yet on the server side nodejs  
  var ws = new WebSocket("ws://bartonphillips.dyndns.org:8080?BLP=8653", "weather");

  ws.onopen = function(event) {
    console.log("protocol: ", ws.protocol );
  };

  ws.onmessage = function(event) {
    var ev = JSON.parse(event.data);
    console.log("message: ", ev);
    var div = document.querySelector("#weather");
    if(ev.event == 'weather') {
      div.innerHTML = ev.time+
                      ", Inside Temp: " + ev.inTemp+
                      ", Outside Temp: "+ev.outTemp;
    } 
  };
*/  
});
