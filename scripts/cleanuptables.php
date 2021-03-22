#!/usr/bin/php
<?php
//echo "cleanuptables.php\n";

// clean up the tables so they only have 90 days of info

$days = 90;

// Use full path to siteload.php because this is a CLI.
// This will use the mysitemap.json in /var/www/bartonlp
  
$_site = require_once('/var/www/vendor/bartonlp/site-class/includes/siteload.php');
$S = new Database($_site);

$db = $S->masterdb;

$ar = ['tracker', 'bots2', 'daycounts', 'counter2'];

foreach($ar as $v) {
  //echo "delete from $v\n";
  $S->query("delete from $db.$v where lasttime < current_date() - interval $days day");
}

// For lagagent only keep 1 years

$years = 1;

$S->query("delete from $db.logagent where lasttime < current_date() - interval $years year");
