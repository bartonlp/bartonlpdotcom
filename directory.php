<?php
// This file will not be in any links or in the robots.txt file so no one should
// be able to find it.
// Anyone that does open this file will be tracked.
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");
$S = new $_site['className']($_site);

$h->title = "Directory";
$h->banner = "<h1>Directory</h1>";
list($top, $footer) = $S->getPageTopBottom($h, "<hr>");

// Send me an email
$ip = $S->ip;
$agent = $S->agent;

$info = <<<EOF
Someone has accessed Directory.php. Info:
ip=$ip
agent=$agent
EOF;
  
mail("bartonphillips@gmail.com", "Directory.php Accessed?",
     $info,
     "From: Directory.php");

echo <<<EOF
$top
<p>Not what you expected.</p>
$footer
EOF;
