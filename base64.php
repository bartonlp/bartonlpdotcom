<?php
//$AutoLoadDEBUG = 1;
$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new $_site->className($_site);

if($_POST) {
  echo "POST<br>";
  vardump($_POST);
  if($data = $_POST['data']) {
    $result = base64_decode($data);
    echo "$result";
  } elseif($file = $_POST['file']) {
    $data = file_get_contents($file);
    $result = base64_decode($date);
    echo "$result";
  } else {
    echo "Nothing to do";
  }
  exit();
}

list($top, $footer) = $S->getPageTopBottom();

echo <<<EOF
$top
<form method='post'>
<input type='text' name='data' autofocus placeholder='Enter base 64 string'>
<br>
<input type='text' name='file' placeholder='Or enter a file with base 64 data'>
<br>
<input type='submit' value="Submit">
</form>
$footer
EOF;
