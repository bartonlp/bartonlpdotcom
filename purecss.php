<?php
require_once("/var/www/includes/siteautoload.class.php");
$S = new Blp; // takes an array if you want to change defaults
$h->title = "Pure CSS Test";
$h->banner = "<h1>Pure CSS Test</h1>";
$h->link = <<<EOF
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
<!--<![endif]-->
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<div class="pure-g">
    <div class="pure-u-1-3"><p>Thirds</p></div>
    <div class="pure-u-1-3"><p>Thirds</p></div>
    <div class="pure-u-1-3"><p>Thirds</p></div>
</div>
$footer
EOF;
