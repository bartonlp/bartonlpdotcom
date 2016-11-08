<?php
// BLP 2016-06-22 -- NOTE: this uses http://bartonphillips.net/js/webstats-new.js. This file is also
// used by http://www.bartonphillips.com/webstats.php which uses the webstats-new.js which uses
// this file for ALL of its AJAX calls!!!
// BLP 2016-05-06 -- add get site to analysis
// BLP 2016-01-15 -- put this in http://bartonphillips.net/ and put simlinks in the other
// directories.  
// BLP 2016-01-06 -- add 'Show showall' to tracker
// BLP 2014-11-02 -- make tracker average stay reflect the current state of the table.
// BLP 2014-08-30 -- change $av to only look at last day and to allow only times less the 2hr.

// Check to see if there is a vendor in this directory. Granbyranch has one!
if(file_exists("vendor/autoload.php")) {
  require_once("vendor/autoload.php");
}
$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new $_site->className($_site);

// Turn an ip address into a long. This is for the country lookup

function Dot2LongIP($IPaddr) {
  if($IPaddr == "") {
    return 0;
  } else {
    $ips = explode(".", "$IPaddr");
    return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
  }
}

// via file_get_contents('webstats-new.php?list=<iplist>
// Given a list of ip addresses get a list of countries as $ar[$ip] = $name of country.

if($list = $_POST['list']) {
  $list = json_decode($list);
  $ar = array();

  foreach($list as $ip) {
    $iplong = Dot2LongIP($ip);

    $sql = "select countryLONG from $S->masterdb.ipcountry ".
            "where '$iplong' between ipFROM and ipTO";

    $S->query($sql);
    
    list($name) = $S->fetchrow('num');

    $ar[$ip] = $name;
  }
  echo json_encode($ar);
  exit();
}

// via ajax proxy for curl http://ipinfo.io/<ip>

if($_POST['page'] == 'curl') {
  $ip = $_POST['ip'];

  $cmd = "http://ipinfo.io/$ip";
  $ch = curl_init($cmd);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $loc = json_decode(curl_exec($ch));
  $locstr = "Hostname: $loc->hostname<br>$loc->city, $loc->region $loc->postal<br>Location: $loc->loc<br>ISP: $loc->org<br>";

  echo $locstr;
  exit();
}

// via ajax findbot. Search the bots table looking for all the records with ip

if($_POST['page'] == 'findbot') {
  $ip = $_POST['ip'];

  $human = [3=>"Robots", 0xc=>"SiteClass", 0x30=>"Sitemap", 0xc0=>"cron"];
  
  $S->query("select agent, who, robots from barton.bots where ip='$ip'");

  $ret = '';

  while(list($agent, $who, $robots) = $S->fetchrow('num')) {
    $h = '';
    
    foreach($human as $k=>$v) {
      $h .= $robots & $k ? "$v " : '';
    }

    $bot = sprintf("%X", $robots);
    $ret .= "<tr><td>$who</td><td>$agent</td><td>$bot</td><td>$h</td></tr>";
  }

  if(empty($ret)) {
    $ret = "<div style='background-color: pink; padding: 10px'>$ip Not In Bots</div>";
  } else {
    $ret = <<<EOF
<style>
#FindBot table {
  width: 100%;
}
#FindBot table td:first-child {
  width: 20%;
}
#FindBot table td:nth-child(2) {
  word-break: break-all;
  width: 70%;
}
#FindBot table td:nth-child(3) {
  width: 10%;
}
#FindBot table * {
  border: 1px solid black;
}
</style>
<table>
<thead>
  <tr><th>$ip</th><th>Agent</th><th>Bots</th><th>Human</th></tr>
</thead>
<tbody>
$ret
</tbody>
</table>
EOF;
  }
  echo $ret; 
  exit();
}

// AJAX from webstats-new.js
// Get the info form the tracker table again.
// NOTE this is called from js/webstats-new.js which always uses this file for its AJAX calls!!

if($_POST['page'] == 'gettracker') {
  // Callback function for maketable()

  function callback1(&$row, &$desc) {
    global $S, $ipcountry;

    $ip = $S->escape($row['ip']);

    $co = $ipcountry[$ip];

    $row['ip'] = "<span class='co-ip'>$ip</span><br><div class='country'>$co</div>";

    console.log("js: " + $row['js']);
    
    if(($row['js'] & 0x2000) === 0x2000) {
      $desc = preg_replace("~<tr>~", "<tr class='bots'>", $desc);
    }
    $row['js'] = dechex($row['js']);
  } // End callback

  $site = $_POST['site'];
  
  $ipcountry = json_decode($_POST['ipcountry'], true);

  $T = new dbTables($S);

  $sql = "select ip, page, agent, starttime, endtime, difftime, isJavaScript as js ".
         "from $S->masterdb.tracker " .
         "where site='$site' and starttime >= current_date() - interval 24 hour ". 
         "order by starttime desc";

  list($tracker) = $T->maketable($sql, array('callback'=>callback1,
                                             'attr'=>array('id'=>'tracker', 'border'=>'1')));

  echo $tracker;
  exit();
}

// Ajax 'getnewhourly' update

if($_POST['page'] == 'getnewhourly') {
  // 'getnewhourly' needs a fully instantiated $S
  
  $S->siteName = $S->siteDomain = $_POST['site'];
  
  echo require_once($S->path . '/make-webstats.php');
  exit();
}

// Normal Page

$T = new dbTables($S);

// webstats.i.txt is created by 'scripts/make-webstats.php'
// Get it if it is orver an hour old.

if(time() > (filemtime($S->path . "/webstats.i.txt") + (60*60))) {
  //echo "require<br>";
  $page = require_once($S->path . '/make-webstats.php');
} else {
  //echo "get<br>";
  $page = file_get_contents($S->path ."/webstats.i.txt");
}

// The analysis files are updated once a day by a cron job.

$analysis = file_get_contents("http://bartonphillips.net/analysis/ALL-analysis.i.txt");

// Make tracker right now.
// Only need one ip out of posibly many

$sql = "select distinct ip from $S->masterdb.tracker where site='$S->siteName' and starttime >= current_date() - interval 24 hour";

$S->query($sql);
$tkipar = array(); // tracker ip array

while(list($tkip) = $S->fetchrow('num')) {
  $tkipar[] = $tkip;
}
$tkipar = array_keys(array_flip($tkipar));

$list = json_encode($tkipar);

// Now we want to do a POST so set up the context first.

$options = array('http' => array(
                                 'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                 'method'  => 'POST',
                                 'content' => http_build_query(array('list'=>$list))
                                )
                );

$context  = stream_context_create($options);

// Now this is going to do a POST!

$ipc = file_get_contents("http://www.bartonlp.com/webstats-new.php", false, $context);

foreach(json_decode($ipc) as $k=>$v) {
  $ipcountry[$k] = $v;
}

function callback(&$row, &$desc) {
  global $S, $ipcountry;

  $ip = $S->escape($row['ip']);

  $co = $ipcountry[$ip];

  $row['ip'] = "<span class='co-ip'>$ip</span><br><div class='country'>$co</div>";

  if(($row['js'] & 0x2000) === 0x2000) {
    $desc = preg_replace("~<tr>~", "<tr class='bots'>", $desc);
  }
  $row['js'] = dechex($row['js']);
}

$sql = "select ip, page, agent, starttime, endtime, difftime, isJavaScript as js ".
       "from $S->masterdb.tracker where site='$S->siteName' and starttime >= current_date() - interval 24 hour ". 
       "order by starttime desc";

list($tracker) = $T->maketable($sql, array('callback'=>callback,
                                            'attr'=>array('id'=>'tracker',
                                            'border'=>'1')));

$sql = "select ip, agent, count, hex(robots) as bots, who, creation_time as 'created', lasttime ".
       "from $S->masterdb.bots ".
       "where lasttime >= current_date() and count !=0 order by lasttime desc";

list($bots) = $T->maketable($sql, array('attr'=>array('id'=>'robots', 'border'=>'1')));

$sql = "select ip, agent, site, which, count from $S->masterdb.bots2 ".
       "where date >= current_date() order by lasttime desc";

list($bots2) = $T->maketable($sql, array('attr'=>array('id'=>'robots2', 'border'=>'1')));

// figure out the timezone of the server by doing 'date' which returns
// something like: Sun Dec 28 12:14:44 MST 2014
// Get the first letter of the time zone, like M for MST etc.

$date = date("Y-m-d H:i:s T");

$h->css = '';

if($S->siteName == "Applitec") {
  $h->css =<<<EOF
  <style>
main {
  margin-left: 5rem;
  margin-top:  0;
  width: 95%;
}
footer {
  margin-left: 5rem;
  width: 95%;
}
#maintitle {
  margin-left: 5rem;
}
  </style>
EOF;
}

$h->link = <<<EOF
  <!-- local css links -->
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<!--[if lte IE 8]>
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
<!--<![endif]-->
  <link rel="stylesheet" href="http://bartonphillips.net/css/tablesorter.css">
  <link rel="stylesheet" href="http://bartonphillips.net/css/webstats-new.css">
EOF;

$h->css .= <<<EOF
  <style>
* {
  box-sizing: border-box !important;
}
  </style>
EOF;

// BLP 2016-05-06 -- $jsonIpcountry etc. must happen after $ipcountry is filled!

$jsonIpcountry = json_encode($ipcountry);

$h->extra = <<<EOF
  <script>
var ipcountry = JSON.stringify($jsonIpcountry);
var thesite = "$S->siteName";
var myIp = "$S->myIp";
  </script>
  <script src="http://bartonphillips.net/js/tablesorter/jquery.tablesorter.js"></script>
  <script src="http://bartonphillips.net/js/webstats-new.js"></script>
EOF;

$h->title = "Web Statistics";
$sitename = strtolower($S->siteDomain);
$h->banner = "<h1 id='maintitle'>Web Stats For <b>$sitename</b></h1>";

list($top, $footer) = $S->getPageTopBottom($h);

// Render the page

$members = $S->memberTable ? "\n<li><a href='#table7a'>Goto Table: memberTable</a></li>" : '';

echo <<<EOF
$top
<main>
<p>$date</p>
<ul>
   <li><a href="#table3">Goto Table: logagent</a></li>
   <li><a href="#table4">Goto Table: counter</a></li>
   <li><a href="#table5">Goto Table: counter2</a></li>
   <li><a href="#table6">Goto Table: daycounts</a></li>$members
   <li><a href="#table7">Goto Table: tracker</a></li>
   <li><a href="#table8">Goto Table: bots</a></li>
   <li><a href="#table9">Goto Table: bots2</a></li>
   <li><a href="#analysis-info">Goto Analysis Info</a></li>
</ul>

<div id="hourly-update">
$page
</div>

<h2 id="table7">Table Seven from table <i>tracker</i> (real time) for last 24 hours</h2>
<a href="#table8">Next</a>
<p>'js' is hex. 1, 2, 32(x20), 64(x40), 128(x80, 256(x100), 512(x200) and 4096(x1000) are done via 'ajax'.<br>
4, 8 and 16(x10) via an &lt;img&gt;<br>
1=start, 2=load, 4=script, 8=normal, 16(x10)=noscript,<br>
32(x20)=beacon/pagehide, 64(x40)=beacon/unload, 128(x80)=beacon/beforeunload,<br>
256(x100)=tracker/beforeunload, 512(x200)=tracker/unload, 1024(x400)=tracker/pagehide,<br>
4096(x1000)=tracker/timer: hits once every 5 seconds via ajax.</br>
8192(x2000)=SiteClass (PHP) determined this is a robot via analysis of user agent or scan of 'bots'.<br>
The 'starttime' is done by SiteClass (PHP) when the file is loaded.</p>
$tracker
<h2 id="table8">Table Eight from table <i>bots</i> (real time) for Today</h2>
<a href="#table9">Next</a>
<p>The 'bots' field is hex.<br>
The 'count' field is the total count since 'created'.<br>
From 'rotots.txt': Initial Insert=1, Update= OR 2.<br>
From app scan: Initial Insert=4, Update= OR 8.<br>
From 'Sitemap.xml': Initial Insert=16(x10), Update= OR 32(x20).<br>
From 'tracker' cron: Inital Insert=64(x40), Update= OR 128(x80).<br>
So if you have a 1 you can't have a 4 and visa versa.</p>
$bots
<h2 id="table9">Table Nine from table <i>bots2</i> (real time) for Today</h2>
<a href="#analysis-info">Next</a>
<p>'which' is 1 for 'robots.txt', 2 for the application, 4 for 'Sitemap.xml'.<br>
The 'count' field is the number of hits today.</p>
$bots2
<div id="analysis">
$analysis
</div>
<hr>
</main>
$footer
EOF;
