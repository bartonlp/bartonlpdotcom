#!/usr/bin/php
<?php
echo "checktracker.php\n";

$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
$S = new Database($_site);

$db = $S->masterdb;
$myIp = gethostbyname($S->myUri);

// Get the current days records
// the key is id so there is one per session.
// This is actually yesterday as the CRON job is run at the beginning of the day.

$sql = "select ip, agent, site " .
       "from $db.tracker ".
       "where ip != '$myIp' and isJavaScript != 0 and (isJavaScript & ~0x2000) = 0 ".
       "and lasttime >= current_date() - interval 1 day order by ip";

// Are there any records that are zero after the robot tag of 0x2000 is removed?

if($S->query($sql)) {
  // Collect them in $todays
  
  while($row = $S->fetchrow('assoc')) {
    $todays[] = $row;
  }
  
  // Get tracker records before today
  // This is actually the day before yesterday as the CRON job is run first thing in the morning.
  
  $sql = "select ip, agent, site, isJavaScript ".
         "from $db.tracker ".
         "where ip != '$myIp' and isJavaScript != 0 and lasttime < current_date() - interval 1 day order by ip";

  $S->query($sql);

  $r = $S->getResult(); // save the result handle as we will do queries inside the loop
  
  while($row = $S->fetchrow($r, 'assoc')) {
    $ip = $row['ip'];
    $agent = $S->escape($row['agent']);
    $site = $row['site'];

    // remove the robot tag and start, load, normal, script and noscript (0x201f) is there still something?
    
    if($row['isJavaScript'] & ~0x201f) {
      // If yes get the ip/agent from bots.
      
      $sql = "select robots, who from $db.bots where ip='$ip' and agent='$agent'";

      if($S->query($sql)) {
        // Get the tag from bots
        
        list($bot, $who) = $S->fetchrow('num');

        // If $who has a ',' in it that means that two or more sites found this. It is a robot.

        if(strpos($who, ',') === false) {
          // Is there something other than 64 or 128 (0x40 or 0x80) that is the tags for CRON insert
          // or update.
          // If yes that means that the robot was found by some other test, like robots.txt etc.
        
          if(!($bot & ~(0x40 | 0x80))) {
            // Only 64 or 128 so this may not be a robot
          
            echo "$site, isJavaScript: " . dechex($row['isJavaScript']) . ", bot: ". dechex($bot) ."\n";
            $sql = "delete from $db.bots where ip='$ip' and agent='$agent'";
            echo "$sql\n\n";
            $S->query($sql);
            continue;
          }
        } 
      }
    }
    
    $rows[$ip][$agent][$site]++;
  }

  // back to the current date for tracker
  
  foreach($todays as $today) {
    $ip = $today['ip'];
    $agent = $S->escape($today['agent']);
    $site = $today['site'];

    // Now look in the bots table to see if there is a record.

    $n = $S->query("select robots, who from $db.bots where ip='$ip' and agent='$agent'");

    // are there any record?

    if($n) {
      // Yes there is a bots record (should be only one).

      $row = $S->fetchrow('assoc');

      // If this record is for a robot just skip it.
      // 0x40 | 0x80 is the signiture from this file ie. only found by tracker. If anything else then
      // this was found by robots.txt or in SiteClass and really IS a robot.

      if($row['robots'] & ~(0x40 | 0x80)) {
        continue;
      }

      $or = ($row['robots'] & 0x40) ? 0x80 : 0x40;

      // is the site already in the 'who'. If it isn't then add it to the who.

      if(strpos($row['who'], $site) === false) {
        $row['who'] .= ", $site";
      }

      $sql = "update $db.bots set who='{$row['who']}', count=count+1, robots=robots|$or where ip='$ip' and agent='$agent'";

      echo "$sql\n\n";
      $S->query($sql);
    } else {
      // NO Records found in bots
      // Look through the tracker records before todays for our current key

      $agentValue = [];
      $siteValue = [];

      // Is this ip in bots with other than 0x40 or 0x80?
      
      $sql = "select robots from $db.bots where ip='$ip'";
      $S->query($sql);

      $ok = false;

      // Does $bot have a value after 0x40 | 0x80 has been removed?
      
      while(list($bot) = $S->fetchrow('num')) {
        if($bot & ~(0x40 | 0x80)) {
          $ok = true;
          break;
        }
      }

      // If ok is true don't bother with the tracker array because this is realy a bot.
      
      if($ok === false) {
        // From tracker. Is there more than one row for his ip. If so then that means there are
        // multiple agents
        
        if(count($rows[$ip]) < 2) {
          continue;
        }

        $siteValue = [];

        // Loop through the tracker ips.
        
        foreach($rows[$ip] as $k=>$v) {
          // $k is the agent and $v is the site/count
          foreach($v as $kk=>$vv) {
            // $kk is the site and $vv is the count
            // so $agentValue key is agent and $kk is the site
            // $kk is the site and $vv is the count
            $siteValue[$kk] += $vv; // $siteValue[site] += count
          }
        }
        if(count($siteValue) < 2) {
          echo "Only one site: $ip, " .implode(', ', array_keys($siteValue)) . "\n";
          continue;
        }
      }

      // If we get here it is because:
      // 1) there was a bots record that has something other than 0x40 | 0x80 or
      // 2) in tracker the number of sites was more than one.
      
      $sql = "insert into $db.bots (ip, agent, count, robots, who, creation_time ) ".
             "values('$ip', '$agent', 1, 0x40, '$site', now())";

      echo "$sql\n\n";
      $S->query($sql);
    }
  }
} else {
  echo "NO RECORDS FOUND\n";
}
