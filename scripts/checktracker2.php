#!/usr/bin/php
<?php
// BLP 2021-02-26 -- currently not being used in all-cron.sh. Commented out.
// This script looks at the 'tracker' table and adds any isJavaScript==0 entries to 'bots' and
// 'bots2'. It looks for the max value of lasttime and then colects 'tracker' entries gt that max
// value.

$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
$S = new Database($_site); // NOTE isSiteClass is NOT defined in Database so this will give a E_NOTICE
//$S = new $_site->className($_site);
$db = $S->masterdb;

// NOTE now myUri can be an array of URI's or a single URI.
// This needs to be fixed!
// At the present time I have removed this from the crontab!!!!

if(strpos($S->myUri, 'http') == 0) {
  // Here $this->myUri probably looks like 'https://bartonphillips.net/myUri.json'
  // When we get that it is an array [myUri] so we want the elements of the array 0..n
  // Now $this->myUri looks like it would be from mysitemap.json if the array was in that
  // file.

  $S->myUri = json_decode(file_get_contents($S->myUri), true)['myUri'];

  if(is_array($S->myUri)) {
    foreach($S->myUri as $v) {
      $ips[] = gethostbyname($v);
    }
  } else {
    // This is not an array but rather a single ip address
    $ips[] = gethostbyname($S->myUri); // get my home ip address
  }
} else {
  // This is not a url but rather a string with a single ip address
  $ips[] = gethostbyname($S->myUri); // get my home ip address
}

$myIps = '"' . implode('","', $ips). '"';
//echo "myIps: $myIps\n";

// Get the current days records
// the key is id so there is one per session.
// This is actually yesterday as the CRON job is run at the beginning of the day.

$sql = "select max(lasttime) from $db.bots ".
       "where lasttime >= current_date() - interval 1 day and robots&0x100=0x100";

$S->query($sql);
list($last) = $S->fetchrow('num');
//echo "Last: $last\n";

if(empty($last)) {
  exit();
}

$sql = "select ip, agent, site, lasttime " .
       "from $db.tracker ".
       "where ip not in ($myIps) and isJavaScript = 0 ".
       "and lasttime >= '$last' order by ip";

//echo "sql: $sql\n";

if($S->query($sql) == 0) {
  //echo "NO RECORDS FOUND\n";
  //echo "DONE\n";
  exit();
}

// Loop through all if toady's tracker records.

$r = $S->getResult();

while(list($ip, $agent, $site, $lasttime) = $S->fetchrow($r, 'num')) {
  $agent = $S->escape($agent);

  // Now look in the bots table to see if there is a record.

  $sql = "select ip, robots, who from $db.bots where ip='$ip' and agent='$agent'";

  // Is there a record?

  if($S->query($sql)) {
    // Yes there is a bots record (should be only one).

    list($ip, $robots, $who) = $S->fetchrow('num');

    $orig = $who;
    
    $who = strpos($who, $site) === false ? "$site, $who" : $who;

    $sql = "update $db.bots set who='$who', count=count+1, robots=robots|0x100, lasttime=now() ".
           "where ip='$ip' and agent='$agent'";

    echo "Update, ip: $ip, robots: ".dechex($robots).", who: $who, orig: $orig\n";
    //echo "$sql\n";
    $S->query($sql);

    $sql = "insert into $db.bots2 (ip, agent, date, site, which, count, lasttime) ".
           "values('$ip', '$agent', now(), '$site', 0, 1, now()) ".
           "on duplicate key update count=count+1, lasttime=now()";

    $S->query($sql);
  } else {
    // There is NO bots entrie
    
    $sql = "insert into $db.bots (ip, agent, count, robots, who, creation_time, lasttime) ".
           "values('$ip', '$agent', 1, 0x100, '$site', now(), now())";

    echo "Insert, ip: $ip, who: $site\n";
    //echo "$sql\n";
    $S->query($sql);

    $sql = "insert into $db.bots2 (ip, agent, date, site, which, count, lasttime) ".
           "values('$ip', '$agent', now(), '$site', 0, 1, now())";

    $S->query($sql);
  }
}
$date = date("Y-m-d H:i:s");
echo "DONE, L: $last, C: $date\n";
