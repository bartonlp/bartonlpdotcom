<?php
// This is used by bartonphillips.net/js/yimage.js to get the images from my RPI

$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setDevelopment(true);
//$S = new $_site->className($_site);

//     data: {path: path, recursive: recursive, size: size, mode: mode},

if($_GET['path']) {
  $path = $_GET['path'];
  $recursize = $_GET['recursive'];
  $size = $_GET['size'];
  $mode = $_GET['mode'];

  $p = file_get_contents("https://www.bartonlp.com/glob.proxy.php".
                         "?path=$path&recursive=$recursive&size=$size&mode=$mode");

  echo $p;
  exit();
}
echo "Go Away";
