<?php
echo $_SERVER['HTTP_HOST']."<br>";
echo $_SERVER['SERVER_NAME']."<br>";
$ipaddress = $_SERVER['REMOTE_ADDR'];
echo "$ipaddress<br>";
$hostname = gethostbyaddr($ipaddress);
echo "$hostname<br>";

$cmd = "http://ipinfo.io/$S->ip";
$ch = curl_init($cmd);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$loc = json_decode(curl_exec($ch));
  
$locstr = <<<EOF
Hostname: <i class='green'>$loc->hostname</i><br>
Location: <i class='green'>$loc->city, $loc->region $loc->postal</i><br>
GPS Loc: <i class='green'>$loc->loc</i><br>
ISP: <i class='green'>$loc->org</i><br>
EOF;

echo $locstr;
