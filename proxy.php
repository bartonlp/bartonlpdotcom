<?php
// BLP 2021-03-10 -- Proxy by passes all of the tracker.php and tracker.js logic. It writes a
// special string into the 'site' fields ($S->siteName . "Proxy") to identify this behavior.
// This is a proxy for the gitHub and others. It takes the query string and logs both counter2 and
// tracker info and then redirects to the query string.
  
$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setNoEmailErrs(true);
ErrorClass::setDevelopment(true);
$_site->count = false; // Don't count
$_site->countMe = false; // Don't countMe

$S = new $_site->className($_site);

function checkUser($S) {
  //error_log("PROXY-referer: " . $_SERVER['HTTP_REFERER'] . ", siteName: $S->siteName");
  if($S->siteName != "Bartonphillips.com" && preg_match("~.*barton~", $_SERVER['HTTP_REFERER']) === 0) {
    echo "<h1>Go Away</h1>";
    //error_log("Proxy Go Away: ".$_SERVER['REQUEST_URI']);
    exit();
  }
};

checkUser($S);

$query = $_SERVER['QUERY_STRING'];

$trackersite = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Put info into counter2
if($S->isBot) {
  $bot = 1;
  $count = 0;
} else {
  $bot = 0;
  $count = 1;
}

// BLP 2021-03-10 -- no more member info. I have removed trackmember() from SiteClass.
// I will remove the field from counter2 someday.
/*if($S->id) {
  $member = 1;
} else {
  $member = 0;
}
*/

$query = substr($query, 0, 254);

// siteName plus "Proxy"
$site = $S->siteName . "Proxy";

// So the site in counter2 will have Proxy added to the site name.

$S->query("insert into $S->masterdb.counter2 (site, date, filename, count, bots, members, lasttime) ".
          "values('$site', now(), '$query', $count, $bot, 0, now()) ".
          "on duplicate key update count=count+$count, bots=bots+$bot, lasttime=now()");

$agent = $S->escape($S->agent);
$ip = $S->ip;
$refid = $S->refid;

// Put info into tracker.
// BLP 2021-03-10 -- removed $trackBot and added zero. Not sure where $trackBot came from?
$trackersite = substr($trackersite, 0, 250); // make sure it is not too long.

$S->query("insert into $S->masterdb.tracker (site, page, ip, agent, refid, isJavaScript, starttime, lasttime) ".
          "values('$site', '$trackersite', '$ip', '$agent', '$refid', 0, now(), now())");

//$S->query("update $S->masterdb.tracker set page='$trackersite', lasttime=now() where id=$S->LAST_ID");

//error_log("Query: $query");

header("Location: $query");
