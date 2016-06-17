#!/usr/bin/php -q
<?php
// Cron job
// Get the ip address of bartonphillips.dyndns.org our dynamic DNS address
// insert it into the blpip table. If it already exists say No Change!

$_site = require_once(getenv("HOME")."/www/includes/siteautoload.class.php");
   
$S = new Database($_site['dbinfo']);
$blpip = gethostbyname("bartonphillips.dyndns.org"); // get my home ip address

if($S->query("insert ignore into {$_site['masterdb']}.blpip (blpIp, createtime) values ('$blpip', now())")) {
  echo "blpip=$blpip\n";
  echo "******* IP For bartonphillips.dyndns.org HAS CHANGED ********\n";
}

