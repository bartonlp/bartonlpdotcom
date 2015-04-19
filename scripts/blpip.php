#!/usr/bin/php -q
<?php
   // Cron job
   // Get the ip address of bartonphillips.dyndns.org our dynamic DNS address
   // insert it into the blpip table. If it already exists say No Change!
define('TOPFILE', "/var/www/includes/siteautoload.php");
if(file_exists(TOPFILE)) {
  include(TOPFILE);
} else throw new Exception(TOPFILE . " not found");
   
$S = new Database($dbinfo);
$blpip =  gethostbyname("bartonphillips.dyndns.org"); // get my home ip address
echo "blpip=$blpip\n";
try {
  $S->query("insert into blpip (blpIp, createtime) values ('$blpip', now())");
} catch(SqlException $e) {
  echo "No Change\n";
}
?>