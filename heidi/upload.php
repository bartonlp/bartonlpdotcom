<?php
// Upload files
$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

$siteName = strtolower($S->siteName);

if($_GET['page'] == 'delete') {
  $file = $_GET['file'];
  unlink($file);
  echo "DELETE: $file";
  exit();
}

if(isset($_POST['submit'])) {
  $userfile = $_FILES['userfile'];
  
  for($i=0; $i < count($userfile['name']); ++$i) {
    $file = $userfile['name'][$i];
    $uploadfile = "/var/www/bartonlp/$siteName/uploads/$file";

    if(move_uploaded_file($userfile['tmp_name'][$i], $uploadfile)) {
      $tmp .= "File is valid, and was successfully uploaded ($file)<br>";
    } else {
      $tmp .= "Error<br>";
    }
  }
  $uploaded = "<p id='uploadmsg'>$tmp</p>";
}

$glob = glob("uploads/*");
$files = '<p>Uploaded Files</p><ul>';

foreach($glob as $v) {
  $files .= "<li><a class='upload-file' data-file='$v' href='$v' alt='$v'>$v</a></li>";
}
$files .= "</ul></p>";
          
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
#delete {
  border-radius: .5rem;
  padding: .3rem;
  background-color: red;
  color: white;
  margin-bottom: 1rem;
}
#BigPhoto {
  display: none;
}
#image {
  max-width: 100%;
}
  </style>
EOF;

$h->link = '<link rel="stylesheet" href="http://bartonphillips.net/css/blp.css" title="blp default">';

$h->script = <<<EOF
  <script>
jQuery(document).ready(function($) {
  var filename;
  var li;

  $(".upload-file").click(function() {
    console.log(this.className);
    li = $(this).parent();
    $("#files").hide();
    filename = $(this).attr('data-file');
    console.log("filename", filename);

    $("#BigPhoto").html("<button id='delete'>Delete Image</button><br><img id='image' src='" + filename +"'>").show();

    $("#image").click(function() {
      $("#BigPhoto").hide();
      $("#files").show();
    });

    $("#BigPhoto").on("click", "#delete", function() {
      $.ajax({
        url: 'upload.test.php',
        type: 'get',
        data: {page: 'delete', file: filename},
        success: function(data) {
          console.log(data);
          location = 'upload.test.php';
        },
        error: function(err) {
          console.log(err);
          return false;
        }
      });
      $("#BigPhoto").hide();
      $("#files").show();
      li.remove();
    });
    return false;
  });
});
  </script>
EOF;

$h->title = "$S->siteNme's Upload Page";
$h->banner = "<h1>$S->siteName's Upload Page</h1>";

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
$uploaded
<p>To upload a file simply select the file you want and click submit.</p>
<p>The file will be uploaded into the 'http://www.bartonlp.com/$siteName/uploads/' directory.
You can execute the file by entering 'http://www.bartonlp.com/$siteName/uploads/&lt;filename&gt;'. Replace
<filename> with the actual filename.</p>

<form enctype="multipart/form-data" method="POST">
  Upload this file: <input name="userfile[]" type="file" multiple />
  <br>
  <input type="submit" name='submit' value="Send File" />
</form>
<div id="container">
To view the image click on the filename. The image is displayed with a <b>Delete Image</b> button above the image.
To delete the image click <b>Delete Image</b>. To dismiss the image click on the <b>Image</b>.
<div id="files">
$files
</div>
<div id="BigPhoto"></div>
</div>
<br>
<a href="index.php">Return to $S->siteName's Home page</a>
<hr>
$footer
EOF;
