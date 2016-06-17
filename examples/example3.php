<?php
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");

if(date("U") > (filemtime("http://bartonlp.com/html/webstats.i.txt") + (60*60))) {
  require_once("http://bartonlp.com/html/make-webstats.php");
}
$page = file_get_contents("http://bartonlp.com/html/webstats.i.txt");

echo "$page";

