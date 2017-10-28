<?php
// Example goes with test-php-input.php. Shows getting a input via php://input and via $_POST.
// Note that the php://input has a raw input string so we must take it apart with parse_str().
// The $_POST has already had the raw string converted.
// The test-php-input.php file sends two values: 'test' and 'siteName'.

$data = file_get_contents("php://input");

if($data) {
  error_log("data: " .print_r($data, true));
  parse_str($data, $ar);
  error_log("data2: ".print_r($ar, true));
  if($ar['page'] == 'beacon') {
    echo <<<EOF
<p>This is beacon: </p>
{$ar['test']}<br>
EOF;
    exit();
  }

  echo <<<EOF
<p>This is from the 'php://input':</p>
Raw: $data<br>
Parsed:<br>
{$ar['test']}<br>
{$ar['siteName']}<br>
EOF;
}

if($_POST) {
  $_site = $_POST;
}
error_log("POST: " .print_r($_site, true));

$j = json_encode($_site);
echo <<<EOF
<p>This is a test of POST:</p>
{$_site['test']}<br>
{$_site['siteName']}<br>
json encoded Data -> $j
EOF;



