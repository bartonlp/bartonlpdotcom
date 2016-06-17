<?php
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");
$S = new $_site['className']($_site);

$sql = "select * from bots order by ip";
$S->query($sql);

$r = $S->getResult();

$lastip = false;
$cnt = 0;
$ar = [];
$robotval = false;
$good = [];
$bad = [];

while($row = $S->fetchrow($r, 'assoc')) {
  extract($row);

  if($lastip === false) {
    $lastip = $ip;
  }

  if($lastip != $ip) {
    $lastip = $ip;

    $txt = "<label>Count: $cnt</label><ul>";
    foreach($ar as $v) {
      $txt .= "<li>$v</li>";
    }
      
    $txt .= "</ul>";

    if($cnt > 1) {
      if($robotval) {
        $good[] =  $txt;
      } else {
        $bad[] = $txt;
      }
    } elseif($robotval) {
        $good[] = $txt;
    }

    $robotval = false;
    $cnt = 0;
    $ar = [];
  }
  
  $robotval |= $robots & 1;
  $bot = sprintf("%02s", dechex($robots));
  $ar[] = "$bot : $ip, $count, $creation_time, $lasttime<br>$agent";
  $cnt++;
  
  if($lastip == $ip) {
    continue;
  }
}

echo "<h2>Good Good bot</h2>";
foreach($good as $v) {
  echo "$v<br>";
}
echo "<h2>Bad Bad bot</h2>";
foreach($bad as $v) {
  echo "$v<br>";
}