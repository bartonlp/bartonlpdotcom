<?php
// Upload files to Heather
$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

if(isset($_POST['submit'])) {
  $userfile = $_FILES['userfile'];
  
  for($i=0; $i < count($userfile['name']); ++$i) {
    $file = $userfile['name'][$i];
    $uploadfile = "/var/www/bartonlp/heather/uploads/$file";

    if(move_uploaded_file($userfile['tmp_name'][$i], $uploadfile)) {
      $tmp .= "File is valid, and was successfully uploaded ($file)<br>";
    } else {
      $tmp .= "Error<br>";
    }
  }
  $uploaded = "<p id='uploadmsg'>$tmp</p>";
}

$glob = glob("uploads/*");
$files = '<p>Uploaded Files</p>';

foreach($glob as $v) {
  $files .= "$v<br>";
}
$h->css =<<<EOF
  <style>
#files {
  display: table-cell;
  color: lightpink;
  padding: .5rem;
  border: 1px solid black;
}
#files p {
  margin: 0;
  display: block;
  color: black;
}

#container {
  margin-top: 1rem;
}
#uploadmsg {
  display: table-cell;
  padding: .5rem;
  background-color: lightgreen;
  border: 1px solid black;
}
form input[type='submit'] {
  border-radius: .5rem;
  background-color: lightgreen;
}
  </style>
EOF;

$h->title = "Heather's Upload Page";
$h->banner = "<h1>Heather's Upload Page</h1>";

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
$uploaded
<p>To upload a file simply select the file you want and click submit.</p>
<p>The file will be uploaded into the 'http://www.bartonlp.com/heather/uploads/' directory.
You can execute the file by entering 'http://www.bartonlp.com/heather/uploads/&lt;filename&gt;'. Replace
<filename> with the actual filename.</p>

<form enctype="multipart/form-data" method="POST">
  Upload this file: <input name="userfile[]" type="file" multiple />
  <br>
  <input type="submit" name='submit' value="Send File" />
</form>
<div id="container">
<div id="files">
$files
</div>
</div>
<hr>
$footer
EOF;
