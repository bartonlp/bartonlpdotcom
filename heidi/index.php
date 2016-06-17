<?php
//$AutoLoadDEBUG = true;  
$_site = require(getenv("HOME") ."/includes/siteautoload.class.php");
$S = new $_site['className']($_site);
ErrorClass::setDevelopment(true);
$h->title = "Heidi's Site";
$h->banner = "<h1>Heidi's Home Page</h1>";
list($top, $footer) = $S->getPageTopBottom($h);
//$S->query("select * from test");
echo <<<EOF
$top
<p><a href="webstats-new.php">Webstats</a></p>
<hr>
$footer
EOF;
