<?php
//$AutoLoadDEBUG = 1;
$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

if($_POST) {
  vardump("\$_POST", $_POST);
  if($data = $_POST['data']) {
    $result = base64_decode($data);
    echo "$result<br>";
  } elseif($file = $_POST['file']) {
    $data = file_get_contents($file);
    $result = base64_decode($data);
    $result = escapeltgt($result);
    echo "$result<br>\n";
  } elseif($image = $_POST['image']) {
    echo "<img src='$image'>";
    exit();
  } else {
    echo "Nothing to do<br>\n";
  }
  exit();
}

$h->css =<<<EOF
<style>
input[type='text'] {
  width: 100%;
}
code {
  font-size: .8rem;
  background-color: lightgray;
  padding: 0 4px;
}
</style>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<h3>Base 64 Data or File</h3>
<form method='post'>
<input type='text' name='data' autofocus placeholder='Enter base 64 string'>
<br>
<input type='text' name='file' placeholder='Or enter a file with base 64 data'>
<br>
<input type='submit' value="Submit">
</form>
<h3>Base 64 Image URL</h3>
<p>This should have <code>data:image/{image type};base64,{base 64 data}</code><br>
<code>{image type}</code> should be replaced with <code><i>jpg, gif, png</i></code>.<br>
and <code>{base 64 data}</code> should be replaced with the actual base 64 data.</p>
<form method='post'>
<input type='text' name='image' placeholder='Enter base 64 image url'>
<br>
<input type='submit' value="Submit">
</form>
$footer
EOF;
