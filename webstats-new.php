<?php
// BLP 2014-11-02 -- make tracker average stay reflect the current state of the table.
// BLP 2014-08-30 -- change $av to only look at last 3 days and to allow only times less the 2hr.

//ini_set('error_log', '/tmp/debugblp.txt');

require_once("/var/www/includes/siteautoload.class.php");
function Dot2LongIP($IPaddr) {
  if($IPaddr == "") {
    return 0;
 } else {
   $ips = explode(".", "$IPaddr");
   return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
 }
}

if($list = $_GET['list']) {
  $S = new Database($dbinfo);
  $list = json_decode($list);
  $ar = array();
  
  foreach($list as $ip) {
    $iplong = Dot2LongIP($ip);

    $sql = "select country_name from country_blocks where '$iplong' ".
           "between ip_start and ip_end";
         
    $S->query($sql);
    
    list($name) = $S->fetchrow('num');
    
    $ar[$ip] = $name;
  }
  echo json_encode($ar);
    
  exit();
}

if($ip = $_GET['ip']) {
  header("Access-Control-Allow-Origin: *");
  
  $S = new Database($dbinfo);

  $iplong = Dot2LongIP($ip);
  
  $sql = "select country_name from country_blocks where '$iplong' ".
         "between ip_start and ip_end";
         
  $S->query($sql);

  list($name) = $S->fetchrow('num');

  echo "$name";

  exit();
}

$referer = $_SERVER['HTTP_REFERER'];

if(!preg_match("/bartonlp\.com/", $referer)) {
  echo <<<EOL
<h1>Access Forbiden</h1>
<p>Please go away.</p>

EOL;
  exit();
}

$S = new Blp;
$T = new dbTables($S);

$extra = <<<EOF
<link rel="stylesheet"  href="http://bartonlp.com/html/css/tablesorter.css" type="text/css">
<script src="http://bartonlp.com/html/js/tablesorter/jquery.tablesorter.js"></script>
<script src="http://bartonlp.com/html/js/tablesorter/jquery.metadata.js"></script>
<script src="http://bartonlp.com/html/js/phpdate.js"></script>
<script>
jQuery(document).ready(function($) {
  var flags = {webmaster: false, robots: false, ip: false , page: false};

  $("#logagent, #counter, #tracker").tablesorter()
    .addClass('tablesorter'); // attach class tablesorter to all except our counter

  // Don't show webmaster

  var myIp = "$S->myIp";

  // Check Flags look at other flags

  function checkFlags(flag) {
    var msg;

    if(flag) { // Flag is true.
      switch(flag) {
        case 'webmaster': // default is don't show
          $(".webmaster").parent().hide();
          msg = "Show ";
          flags.webmaster = false;
          break;
        case 'robots': // true means we are showing robots
          $('.robots').parent().hide();
          msg = "Show ";
          flags.robots = false;
          break;
        case 'ip': // true means only this ip is showing so we want to make all ips show
          $(".ip").removeClass('ip');
          $("#tracker tr").show();

          if(flags.page) {
            $("#tracker td:first-child:not('.page')").parent().hide();
          }
             
          if(!flags.webmaster) {
            $(".webmaster").parent().hide();
          }
          if(!flags.robots) {
            $(".robots").parent().hide();
          }
          msg = "Only ";
          flags.ip = false;
             
          break;
        case 'page': // true means we are only showing this page
          $(".page").removeClass('page');
          $("#tracker tr").show();
                          
          if(flags.ip) {
            $("#tracker td:nth-child(2):not('.ip')").parent().hide();
          }

          if(!flags.webmaster) {
            $(".webmaster").parent().hide();
          }
          if(!flags.robots) {
            $(".robots").parent().hide();
          }
          msg = "Only ";
          flags.page = false;
          break;
      }
      $("#"+ flag).text(msg + flag);
      calcAv();
      return;
    }   

    for(var f in flags) {
      if(flags[f]) { // if true
        switch(f) {
          case 'webmaster':
            flags.webmaster = false;
            if(true in flags) {
              $(".webmaster").parent().not(":hidden").show();
            } else {
              $(".webmaster").parent().show();
            }
            flags.webmaster = true;
            msg = "Hide ";
            break;
          case 'robots':
            flags.robots = false;
            if(true in flags) {
              $('.robots').parent().not(":hidden").show();
            } else {
              $(".robots").parent().show();
            }
            flags.robots = true;
            msg = "Hide ";
            break;
          case 'ip': 
            $("#tracker tr td:nth-child(2):not('.ip')").parent().hide();
            msg = "All ";
            break;
          case 'page':
            $("#tracker tr td:first-child:not('.page')").parent().hide();
            msg = "All ";
            break;
        }
        $("#"+ f).text(msg + f);
      }   
    }
    calcAv();
  }

  function calcAv() {
    // Always hide any popups first
    $("#popup").hide();
    // Calculate the average time spend using the NOT hidden elements
    var av = 0, cnt = 0;
    $("#tracker tbody :not(:hidden) td:nth-child(6)").each(function(i, v) {
      var t = $(this).text();
      if(!t) return true; // Continue

      var ar = t.match(/^(\d{2}):(\d{2}):(\d{2})$/);
      t = parseInt(ar[1], 10) * 3600 + parseInt(ar[2],10) * 60 + parseInt(ar[3],10);
      if(t > 7200) return true; // Continue if over two hours 

      console.log("t: %d", t);
      av += t;
      ++cnt;      
    });

    av = av/cnt; // Average
   
    var hours = Math.floor(av / (3600)); 
   
    var divisor_for_minutes = av % (3600);
    var minutes = Math.floor(divisor_for_minutes / 60);
 
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);

    var tm = hours.pad()+":"+minutes.pad()+":"+seconds.pad();

    console.log("av time: ", tm);
    $("#average").html(tm);
  }

  Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
  }

  // To start Webmaster is hidden

  $("#tracker td:nth-child(2) span.co-ip").each(function(i, v) {
    if($(v).text() == myIp) {
      $(v).parent().addClass("webmaster").css("color", "green").parent().hide();
    }
  });

  // To start Robots are hidden

  $(".bot td:nth-child(3)").addClass('robots').css("color", "red").parent().hide();
  
  // Put a couple of buttons before the table

  $("#tracker").before("<div id='trackerselectdiv'>"+
                       "<button id='webmaster'>Show webmaster</button>"+
                       "<button id='robots'>Show robots</button>"+
                       "<button id='page'>Only page</button>"+
                       "<button id='ip'>Only ip</button>"+
                       "</div>");

  calcAv(); // Calculate the starting average

  // ShwoHide Webmaster clicked

  $("#webmaster").click(function(e) {
    if(flags.webmaster) {
      checkFlags('webmaster');
    } else {
      // Show
      flags.webmaster = true;
      // Now show only my IP
      checkFlags();
    }
    //flags.webmaster = !flags.webmaster;
  });

  // Page clicked

  $("#tracker td:first-child").click(function(e) {
    if(flags.page) {
      checkFlags('page');
    } else {
      // show only this page
      flags.page = true;
      var page = $(this).text();
      $("#tracker tr td:first-child").each(function(i, v) {
        if($(v).text() == page) {
          $(v).addClass('page');
        }
      });
      checkFlags();
    }
  });

  // IP address clicked

  $("#tracker td:nth-child(2)").click(function(e) {
    if(flags.ip) {
      checkFlags('ip');
    } else {
      // show only IP
      flags.ip = true;
      var ip = $(this).text();
      $("#tracker tr td:nth-child(2)").each(function(i, v) {
        if($(v).text() == ip) {
          $(v).addClass('ip');
        }
      });
      checkFlags();
    }
  });

  // ShowHideBots clicked

  $("#robots").click(function() {
    if(flags.robots) {
      // hide
      checkFlags('robots');
    } else {
      // show
      flags.robots = true;
      checkFlags();
    }
  });

  $("#ip").click(function() {
    if(flags.ip) {
      // hide
      checkFlags('ip');
    } else {
      // show
      alert("click on the IP address you want to show");
    }
  });

  $("#page").click(function() {
    if(flags.page) {
      // hide
      checkFlags('page');
    } else {
      // show
      alert("click on the page you want to show");
    }
  });
});
  </script>

  <style>
button {
  -webkit-border-radius: 7px;
  -moz-border-radius: 7px;
  border-radius: 7px;
  font-size: 1.2em;
  margin-bottom: 10px;
}
.country {
  border: 1px solid black;
  padding: 3px;
  background-color: #8dbdd8;
}
th, td {
  padding: 5px;
}
#tracker {
  width: 100%;
}
#tracker td:nth-child(4), #tracker td:nth-child(5) {
  width: 5em;
}
#tracker td:last-child {
  word-break: break-all;
  word-break: break-word; /* for chrome */
}
#tracker td:nth-child(2):hover {
  cursor: pointer;
}
#tracker td:first-child:hover {
  cursor: pointer;
}
div {
  padding: 10px 0;
}
</style>

EOF;

$h = array('title'=>"Web Statistics", 'extra'=>$extra,
           'banner'=>"<h1>Web Stats For <b>bartonlp.com</b></h1>");

$b = array('msg1'=>"<p>Return to <a href='index.php'>Home Page</a></p>\n<hr/>");

list($top, $footer) = $S->getPageTopBottom($h, $b);

$page = file_get_contents("webstats.i.txt");

function callback(&$row, &$desc) {
  global $S;

  $ip = $S->escape($row['ip']);
  //$country = file_get_contents("http://bartonlp.com/html/webstats-new.php?ip=$ip");
  $iplong = Dot2LongIP($ip);
  //error_log("country: $country");

  $sql = "select country_name from country_blocks where '$iplong' ".
         "between ip_start and ip_end";
         
  $S->query($sql);

  list($country) = $S->fetchrow('num');

  $row['ip'] = "<span class='co-ip'>$ip</span><br><div class='country'>$country</div>";
  
  if($S->query("select ip from bots where ip='$ip'")) {
    $desc = preg_replace("~<tr>~", "<tr class='bot'>", $desc);
  }

  
  $ref = urldecode($row['referrer']);
  
  // if google then remove the rest because google doesn't have an info in q= any more.
  if(strpos($ref, 'google') !== false) {
    $ref = preg_replace("~\?.*$~", '', $ref);
  }
  $row['referrer'] = $ref;
}

$sql = "select page, ip, agent, starttime, endtime, difftime, referrer ".
       "from tracker where starttime > date_sub(now(), interval 3 day) order by starttime desc";

list($tracker) = $T->maketable($sql, array('callback'=>callback,
                                           'attr'=>array('id'=>'tracker', 'border'=>'1')));

$ddate = preg_replace("/^.*?:\d\d (.).*/", '$1', exec("date"));

$zones = array("E"=>"America/New_York",
               "C"=>"America/Chicago",
               "M"=>"America/Denver",
               "P"=>"America/Los_Angeles"
              );

date_default_timezone_set($zones[$ddate]);
$date = date("Y-m-d H:i:s T");

//$S->query("select timediff(now(),convert_tz(now(),@@session.time_zone,'+00:00'))");
//list($mysqldate) = $S->fetchrow('num');

echo <<<EOF
$top
$date
<p>This report is gethered once a day (except Tracker). Results are limited to 20 records.</p>
<ul>
   <li><a href="#table2">Goto Table Two: logip</a></li>
   <li><a href="#table3">Goto Table Three: logagent</a></li>
   <li><a href="#table4">Goto Table Four: counter</a></li>
   <li><a href="#table5">Goto Table Five: counter2</a></li>
   <li><a href="#table6">Goto Table Six: daycounts</a></li>
   <li><a href="#table7">Goto Table Seven: tracker</a></li>
</ul>   

$page
<div id="table7">
<h2>Table Seven from <i>tracker</i> (real time)</h2>
<p>Tracker information is accumulated via a <b>JavaScrip</b> AJAX function. Most robots can not
run <b>JavaScript</b> (Google being one of the exceptions, being able to run the 'starttime' portion).
When the page is loaded by the browser the 'starttime' information is posted to the datebase. When
the browser <i>unloads</i> the page the browser runs the 'onunload' listener and logs the 'endtime'
etc.
</p>
<p>Click on IP to show only that IP.<br>
Click on Page to show only that page.<br>
Average stay time: <span id='average'></span> (times over two hours are discarded.)<br>
Showing only last 3 days.</p>
$tracker
$footer
EOF;
