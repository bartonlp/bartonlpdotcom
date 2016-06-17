<?php
// BLP 2016-02-17 -- count bad bots
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");
$S = new Database($_site['dbinfo']);

$ip = $_SERVER['REMOTE_ADDR'];
$agent = $S->escape($_SERVER['HTTP_USER_AGENT']);

//error_log("badbot.php {$_site['siteName']}: $ip, $agent");

$S->query("insert into {$_site['masterdb']}.badbots (ip, site, agent, created, count) ".
          "values('$ip', '{$_site['siteName']}', '$agent', now(), 1) ".
          "on duplicate key update count=count+1");

header('HTTP/1.1 403 Forbidden');
