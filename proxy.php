<?php
// This is a proxy for the gitHub and others. If takes the query string and logs both counter2 and
// tracker info and then redirects to the query string.

$_site = require_once(getenv("SITELOAD")."/siteload.php");

$_site->count = false; // Don't count
$_site->countMe = false; // Don't countMe

$S = new $_site->className($_site);

$query = $_SERVER['QUERY_STRING'];

// Put info into counter2
if($S->isBot) {
  $bot = 1;
  $count = 0;
} else {
  $bot = 0;
  $count = 1;
}
if($S->id) {
  $member = 1;
} else {
  $member = 0;
}

$S->query("insert into barton.counter2 (site, date, filename, count, bots, members, lasttime) ".
          "values('$S->siteName', now(), '$query', $count, $bot, $member, now()) ".
          "on duplicate key update count=count+$count, bots=bots+$bot, members=members+$member, lasttime=now()");

//$agent = $S->escape($S->agent);
//$ip = $S->ip;
//$refid = $S->refid;

// Put info into tracker.

//$S->query("insert into barton.tracker (site, page, ip, agent, refid, isJavaScript, starttime, lasttime) ".
//          "values('$S->siteName', '$query', '$ip', '$agent', '$refid', $trackerBot, now(), now())");

$S->query("update barton.tracker set page='$query', lasttime=now() where id=$S->LAST_ID");

//error_log("Query: $query");

header("Location: $query");
