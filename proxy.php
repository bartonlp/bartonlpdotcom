<?php
// This is a proxy for the gitHub and others. If takes the query string and logs both counter2 and
// tracker info and then redirects to the query string.

$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new Database($_site);

$query = $_SERVER['QUERY_STRING'];

// Put info into counter2

$S->query("insert into barton.counter2 (site, date, filename, count, bots, members, lasttime) ".
          "values('$S->siteName', now(), '$query', 1, -1, -1, now()) ".
          "on duplicate key update count=count+1, lasttime=now()");

$agent = $S->escape($_SERVER['HTTP_USER_AGENT']);
$ip = $_SERVER['REMOTE_ADDR'];
$refid = $_SERVER['HTTP_REFERER'];

// Put info into tracker.

$S->query("insert into barton.tracker (site, page, ip, agent, refid, lasttime) ".
          "values('$S->siteName', '$query', '$ip', '$agent', '$refid', now())");

//error_log("Query: $query");

header("Location: $query");

  