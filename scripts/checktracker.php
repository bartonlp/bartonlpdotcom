#!/usr/bin/php
<?php
echo "checktracker.php\n";
$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");

$S = new Database($_site);

$db = $S->masterdb;

foreach($S->myUri as $v) {
  $ips[] = "'" . gethostbyname($v) . "'";
}
$myIps = implode(',', $ips);
echo "myIps: $myIps\n";

// Get the current days records
// the key is id so there is one per session.
// This is actually yesterday as the CRON job is run at the beginning of the day.

$sql = "select ip, agent, site " .
       "from $db.tracker ".
       "where ip not in ($myIps) and (isJavaScript & ~0x2000) = 0 ".
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
         "where ip not in ($myIps) and lasttime < current_date() - interval 1 day order by ip";

  $S->query($sql);

  $r = $S->getResult(); // save the result handle as we will do queries inside the loop
  
  while($row = $S->fetchrow($r, 'assoc')) {
    $ip = $row['ip'];
    $agent = $S->escape($row['agent']);
    $site = $row['site'];
    $isJavaScript = $row['isJavaScript'];
    
    // remove the robot tag and start, load, normal, script and noscript (0x201f)
    // is there still something?
    
    if($isJavaScript & ~0x201f) {
      // If yes get the ip/agent from bots.
      
      $sql = "select robots, who from $db.bots where ip='$ip' and agent='$agent'";

      if($S->query($sql)) {
        // Get the tag from bots
        
        list($bot, $who) = $S->fetchrow('num');
        
        // If $who has a ',' in it that means that two or more sites found this. It is a robot.

        if(strpos($who, ',') === false) {
          echo "First bots: ".dechex($bot)." who: $who, ip: $ip\n";
          
          // Is there something other than 64 or 128 (0x40 or 0x80)
          // that is the tags for CRON insert or update.
          // If yes that means that the robot was found by some other test,
          // like robots.txt (1, 2), SiteClass (4, 8) or Sitemap.xml (0x10, 0x20).
        
          if(!($bot & ~(0x40 | 0x80))) {
            // Only 64 or 128 so this may not be a robot
          
            echo "Delete who: $who, isJavaScript: " . dechex($isJavaScript) . ", bot: ". dechex($bot) ."\n";
            $sql = "delete from $db.bots where ip='$ip' and agent='$agent'";
            echo "$sql\n\n";
            $S->query($sql);
            continue;
          }
        } else {
          $or = (($bot & 0x40) == 0x40) ? 0x80 : (($bot & 0x80) == 0x80) ? '' : 0x80;
          $or |= $isJavaScript == 0 ? 0x100 : '0';
          $who = strpos($who, $site) !== false ? $who : "$who, $site";

          //echo "isJavaScript: ".dechex($isJavaScript). ", or: ".dechex($or).", who: $who, ip: $ip\n";
          
          $sql = "update $db.bots set count=count+1, who='$who', robots=robots | $or, lasttime=now() ".
                 "where ip='$ip' and agent='$agent'";
          
          $S->query($sql);
          echo "First Update: $who, isJavaScript: " . dechex($isJavaScript) . "\n";
          continue;

        }
      }
    }
    
    $rows[$ip][$agent][$site]++;
  }

  //vardumpNoEscape("rows", $rows);
  //exit();
  
  // Back to the current date from tracker
  // $todays is an array of row data
  
  foreach($todays as $today) {
    $ip = $today['ip'];
    $agent = $S->escape($today['agent']);
    $site = $today['site'];
    $isJava = $today['isJavaScript'];
 
    // Now look in the bots table to see if there is a record.

    $n = $S->query("select robots, who from $db.bots where ip='$ip' and agent='$agent'");

    // are there any record?

    if($n) {
      // Yes there is a bots record (should be only one).

      list($robots, $who) = $S->fetchrow('num');

      // If this record is for a robot just skip it.
      // 0x40 | 0x80 is the signiture from this file ie. only found by tracker. If anything else then
      // this was found by robots.txt or in SiteClass or via Sitemap.xml and really IS a robot.

      if(($robots & ~(0x40 | 0x80)) || strpos($who, $site) !== false) {
        continue;
      }

      $or = ($robots & 0x40) ? 0x80 : 0x40;

      // Is the record from tracker's isJavaScript == 0?
      if($isJava == 0) {
        $or |= '0x100';
      }

      // is the site already in the 'who'. If it isn't then add it to the who.

      if(strpos($who, $site) === false) {
        $who .= ", $site";
      }

      $sql = "update $db.bots set who='$who', count=count+1, robots=robots|$or, lasttime=now() ".
             "where ip='$ip' and agent='$agent'";
      
      echo "Second Update, who: $who, isJavaScript: " . dechex($isJava) . "\n\n";

      $S->query($sql);
    } else {
      // NO Records found in bots for ip and agent
      // so look just for ip
      
      $sql = "select robots from $db.bots where ip='$ip'";

      $n = $S->query($sql);

      // Did we find any records for this ip?
      
      if($n) {
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
          // From tracker. Is there more than one row for his ip.
          // If so then that means there are multiple agents.
          // $rows[$ip][$agent][$site]++; was done at the very top

          $siteValue = [];

          // Loop through the tracker ips.

          foreach($rows[$ip] as $k=>$v) {
            // $k is the agent and $v is [site=>count]
            foreach($v as $kk=>$vv) {
              // $k is agent and $kk is the site
              // and $vv is the count
              $siteValue[$kk] += $vv; // $siteValue[site] += count
            }
          }

          if((count($rows[$ip]) < 2) && (count($siteValue) < 2)) {
            echo "isJavaScript: $isJava.\nOnly one site/agent: $ip, agents: " .
                implode(', ', array_keys($row[$ip])) .", sites: " .
                implode(', ', array_keys($siteValue)) . "\n";
            continue;
          }
        }
      }
      
      // If we get here it is because:
      // 1) There are no bots records for this ip/agent or this ip BUT
      //    we really have a bots record to add.
      // 2) there was a bots record that has something other than 0x40 | 0x80 or
      // 3) in tracker the number of agents was more than one or
      // 4) in tracker the number of sites was more than one.

      $or = $isJava == 0 ? 0x140 : 0x40;

      $sql = "insert into $db.bots (ip, agent, count, robots, who, creation_time, lasttime) ".
             "values('$ip', '$agent', 1, $or, '$site', now(), now())";

      echo "Insert, who: $site, robots: "; dechex($or). ", isJavaScript: " . dechex($isJava) . "\n\n";
      $S->query($sql);
    }
  }
} else {
  echo "NO RECORDS FOUND\n";
}

echo "\nDONE\n";
