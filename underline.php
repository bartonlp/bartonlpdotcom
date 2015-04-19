<?php
require_once("/var/www/includes/siteautoload.class.php");
$S = new Blp; // takes an array if you want to change defaults

$h->extra = <<<EOF
<style>
[data-smart-underline-container-id="2"] a[data-smart-underline-link-color="rgb(0, 0, 238)"], [data-smart-underline-container-id="2"] a[data-smart-underline-link-color="rgb(0, 0, 238)"]:visited {
  color: rgb(0, 0, 238);
  text-decoration: none !important;
  text-shadow: 0.03em 0 rgb(255, 255, 255), -0.03em 0 rgb(255, 255, 255), 0 0.03em rgb(255, 255, 255), 0 -0.03em rgb(255, 255, 255), 0.06em 0 rgb(255, 255, 255), -0.06em 0 rgb(255, 255, 255), 0.09em 0 rgb(255, 255, 255), -0.09em 0 rgb(255, 255, 255), 0.12em 0 rgb(255, 255, 255), -0.12em 0 rgb(255, 255, 255), 0.15em 0 rgb(255, 255, 255), -0.15em 0 rgb(255, 255, 255);
  background-color: transparent;
  background-image: -webkit-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -webkit-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -webkit-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
  background-image: -moz-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -moz-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -moz-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
  background-image: -o-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -o-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -o-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
  background-image: -ms-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -ms-linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), -ms-linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
  background-image: linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), linear-gradient(rgb(255, 255, 255), rgb(255, 255, 255)), linear-gradient(rgb(0, 0, 238), rgb(0, 0, 238));
  -webkit-background-size: 0.05em 1px, 0.05em 1px, 1px 1px;
  -moz-background-size: 0.05em 1px, 0.05em 1px, 1px 1px;
  background-size: 0.05em 1px, 0.05em 1px, 1px 1px;
  background-repeat: no-repeat, no-repeat, repeat-x;
  background-position: 0% 89%, 100% 89%, 0% 89%;
}

[data-smart-underline-container-id="2"] a[data-smart-underline-link-color="rgb(0, 0, 238)"][data-smart-underline-link-small] {
  background-position: 0% 96%, 100% 96%, 0% 96%;
}

[data-smart-underline-container-id="2"] a[data-smart-underline-link-color="rgb(0, 0, 238)"][data-smart-underline-link-large] {
  background-position: 0% 86%, 100% 86%, 0% 86%;
}

[data-smart-underline-container-id="2"] a[data-smart-underline-link-color="rgb(0, 0, 238)"]::selection {
  text-shadow: 0.03em 0 #b4d5fe, -0.03em 0 #b4d5fe, 0 0.03em #b4d5fe, 0 -0.03em #b4d5fe, 0.06em 0 #b4d5fe, -0.06em 0 #b4d5fe, 0.09em 0 #b4d5fe, -0.09em 0 #b4d5fe, 0.12em 0 #b4d5fe, -0.12em 0 #b4d5fe, 0.15em 0 #b4d5fe, -0.15em 0 #b4d5fe;
  background: #b4d5fe;
}

[data-smart-underline-container-id="2"] a[data-smart-underline-link-color="rgb(0, 0, 238)"]::-moz-selection {
  text-shadow: 0.03em 0 #b4d5fe, -0.03em 0 #b4d5fe, 0 0.03em #b4d5fe, 0 -0.03em #b4d5fe, 0.06em 0 #b4d5fe, -0.06em 0 #b4d5fe, 0.09em 0 #b4d5fe, -0.09em 0 #b4d5fe, 0.12em 0 #b4d5fe, -0.12em 0 #b4d5fe, 0.15em 0 #b4d5fe, -0.15em 0 #b4d5fe;
  background: #b4d5fe;
}
</style>
<script>
jQuery(document).ready(function($) {
  $("body").attr("data-smart-underline-container-id", "2");
  $("a").attr({'data-smart-underline-link-color': "rgb(0, 0, 238)",
               'data-smart-underline-link-large': ""});
});
</script>
EOF;
$h->banner = "<h1>Underline Test</h1>";
//$h->bodytag = '<body data-smart-underline-container-id="2">';
list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<p>Test of underlines</p>
<a href="underline.php">My goodness just (put questions)</a>
<br>
<a href="underline.php">My goodness just (put questions)</a>  
$footer
EOF;
