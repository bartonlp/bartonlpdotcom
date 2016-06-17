<?php
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");
$S = new Database($_site['dbinfo']);

$n = $S->query("select ip, agent, site from logagent where created>'2010-00-00 00:00:00'");
$r = $S->getResult();
if(!$n) {
  echo "What?";
  exit();
}

while(list($ip, $agent, $site) = $S->fetchrow($r, 'num')) {
  
  if($S->query("select starttime from tracker where ip='$ip' and agent='$agent' and site='$site'")) {
    list($created) = $S->fetchrow('num');
    try {
      $S->query("update logagent set created='$created' where ip='$ip' and agent='$agent' and site='$site'");
      echo "created: $created, $ip, agent: $agent<br>";
    } catch(Exception $e) {
      echo $e->getCode() . "<br>$ip, $agent<br>";
    }
  } else {
    echo "$ip not found<br>";
  }
}
echo "Done";
