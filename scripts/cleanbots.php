#!/usr/bin/php
<?php
//$AutoLoadDEBUG = true;  
$_site = require_once("/var/www/includes/siteautoload.class.php");

$db = $_site['masterdb'];
$myIp = gethostbyname($_site['myUri']);

$S = new Database($_site['dbinfo']);

$sql = "select ip, who from $db.bots where (robots & ~(0x40|0x80)) = 0 and not locate(',',who) order by ip";

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
  
  foreach($rows as $k=>$v) {
    if($v !== true) {
      // These should be deleted.
/*      
      $sql = "select ip, agent, who from $db.bots where ip='$k'";
      $S->query($sql);
      while(list($ip, $agent, $who) = $S->fetchrow('num')) {
        echo "$ip, $agent, $who\n";
      }
      echo "\n";
*/      
      $sql = "delete from $db.bots where ip='$k'";
      echo "$sql\n";
      $S->query($sql);
    }
  }
}
