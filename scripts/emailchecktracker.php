#!/usr/bin/php
<?php
// This runs as a cron job and emails checktracker.log to me and then removes the content

$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");

$msg = file_get_contents("/var/www/bartonlp/scripts/checktracker.log");

//echo "msg: $msg\n";

mail("bartonphillips@gmail.com", "checktracker.log", $msg, "from: www.bartonlp.com");

file_put_contents("/var/www/bartonlp/scripts/checktracker.log", '');

echo "Eamil of checktracker.log sent. Fill set to empty.\n";
