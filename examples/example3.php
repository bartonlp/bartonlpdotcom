<?php

if(date("U") > (filemtime("http://www.bartonlp.com/webstats.i.txt") + (60*60))) {
  require_once("http://www.bartonlp.com/make-webstats.php");
}
$page = file_get_contents("http://www.bartonlp.com/webstats.i.txt");

echo "$page";

