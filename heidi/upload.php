<?php
// Upload files to Heidi
//$AutoLoadDEBUG = true;
$_site = require("/var/www/includes/siteautoload.class.php");
$S = new SiteClass($_site);
//vardump($S);

if(isset($_POST['submit'])) {
  $uploaddir = '/var/www/bartonlp/heidi/uploads/';
  $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

  $h->banner = "<h1>Upload</h1>";
  list($top, $footer) = $S->getPageTopBottom();
  echo $top;
  if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.<br>";
  } else {
    echo "Error<br>";
  }
  echo $footer;
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
  Upload this file: <input name="userfile" type="file" />
  <br>
  <input type="submit" name='submit' value="Send File" />
</form>
<hr>
$footer
EOF;
