<?php
$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setDevelopment(true);
//$S = new $_site->className($_site);

//     data: {path: path, recursive: recursive, size: size, mode: mode},

if($_GET['path']) {
  $path = $_GET['path'];
  $recursize = $_GET['recursive'];
  $size = $_GET['size'];
  $mode = $_GET['mode'];

  $p = file_get_contents("http://www.bartonphillips.dyndns.org:8080/glob.proxy.php".
                         "?path=$path&recursive=$recursive&size=$size&mode=$mode");

  echo $p;
  exit();
}
echo "Go Away";