<?php
$_site = require_once(getenv("SITELOAD"). "/siteload.php");
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

$h->title = "Heidi's Site";
$h->banner = "<h1>Heidi's Home Page</h1>";
list($top, $footer) = $S->getPageTopBottom($h);
//$S->query("select * from test");
echo <<<EOF
$top
<h1>$S->siteName</h1>
<p><a href="webstats-new.php">Webstats</a></p>
<hr>
$footer
EOF;
