<?php
// Example goes with testit.php. Shows getting a input via php://input and via $_POST.
// Note that the php://input has a raw input string so we must take it apart with parse_str().
// The $_POST has already had the raw string converted.

$data = file_get_contents("php://input");
if($data) {
  error_log("data: " .print_r($data, true));
  parse_str($data, $ar);
  error_log("data2: ".print_r($ar, true));
}
if($_POST) {
  $_site = $_POST;
}
error_log(print_r($_site, true));

$j = json_encode($_site);
echo <<<EOF
<p>This is a test</p>
{$_site['siteName']}<br>
json Data -> $j
EOF;



