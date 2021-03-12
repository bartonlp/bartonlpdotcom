<?php
// BLP 2021-03-10 -- I don't think this is used anywhere!
// This is used by bartonphillips.net/js/yimage.js to get the images from my
// www.bartonlp.com/Pictures

$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setDevelopment(true);

//     data: {path: path, recursive: recursive, size: size, mode: mode},

if($_GET['path']) {
  $path = $_GET['path'];
  $recursize = $_GET['recursive'];
  $size = $_GET['size'];
  $mode = $_GET['mode'];

  $list = glob("$path");
  if($mode == 'random') {
    shuffel($list);
  }
  echo implode("\n", $list);
  exit();
}
echo "Go Away";
