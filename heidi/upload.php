<?php
// Upload files to Heidi
$_site = require_once(getenv("SITELOAD"). "/siteload.php");
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

if(isset($_POST['submit'])) {
  $file = $_FILES['userfile']['name'][0];
  $uploadfile = "/var/www/bartonlp/heidi/uploads/$file";

  if(move_uploaded_file($_FILES['userfile']['tmp_name'][0], $uploadfile)) {
    $tmp = "File is valid, and was successfully uploaded ($file)";
  } else {
    $tmp = "Error<br>";
  }

  $h->banner = "<h1>Upload</h1>";
  list($top, $footer) = $S->getPageTopBottom();

  echo <<<EOF
$top
<p>$tmp</p>
$footer
EOF;

  exit();
}

$h->title = "Heidi's Upload Page";
$h->banner = "<h1>Heidi's Upload Page</h1>";

list($top, $footer) = $S->getPageTopBottom();

echo <<<EOF
$top
<p>To upload a file simply select the file you want and click submit.</p>
<p>The file will be uploaded into the 'http://www.bartonlp.com/heidi/uploads/' directory.
You can execute the file by entering 'http://www.bartonlp.com/heidi/uploads/<filename>'. Replace
<filename> with the actual filename.</p>

<form enctype="multipart/form-data" method="POST">
  Upload this file: <input name="userfile[]" type="file" multiple />
  <br>
  <input type="submit" name='submit' value="Send File" />
</form>
<hr>
$footer
EOF;
