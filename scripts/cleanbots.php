#!/usr/bin/php
<?php
// BLP 2021-03-13 -- Not used in all-cron.sh
// This program looks in the 'bots' table and if 'robots' has xc0 and the site has does not have a comma in it.
// 0x40 is tracker-cron and 0x80 is tracker-cron update.
// If it also has site with a comma indicating more than one site has found the 0xc0,  
// we delete the record for bots.
// Now 0x20, 0x40 and 0x80 are set by beacon.php

echo "Start\n";
  
$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
$S = new Database($_site);

$db = $S->masterdb;
$myIp = gethostbyname($S->myUri);

$sql = "select ip, site from $db.bots where (robots & ~(0x40|0x80)) = 0 and not locate(',', site) order by ip";

$count = "No entries like this is bots.\n";

if($S->query($sql)) {
  $rows = [];
  
  while(list($ip, $who) = $S->fetchrow('num')) {
    // Is $rows set and is it NOT true?
    
    if(isset($rows[$ip])) {
      if($rows[$ip] === true) {
        // Already set true so just get next ip.
        continue;
      }

      // Is $rows[$ip] different from $who?
      
      if($rows[$ip] != $who) {
        // If so set $rows[$ip] to true. These are the ones we will NOT delete.
        $rows[$ip] = true;
      }
    } else {
      // This is the first time and maybe the only time so $rows[$ip] is not true so we may well
      // delete it.
      $rows[$ip] = $who;
    }
  }

  // Now go through the list and find all the $ip entries that are NOT true.

  echo "count1: $count\n";
  foreach($rows as $k=>$v) {
    if($v !== true) {
      // These should be deleted.
/*      
      $sql = "select ip, agent, site from $db.bots where ip='$k'";
      $S->query($sql);
      while(list($ip, $agent, $who) = $S->fetchrow('num')) {
        echo "$ip, $agent, $who\n";
      }
      echo "\n";
*/      
//      $sql = "delete from $db.bots where ip='$k'";
//      echo "$sql\n";
//      $S->query($sql);
      $count++;
    }
  }
}
echo "count: $count\n";
