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
<hr>
$footer
EOF;
