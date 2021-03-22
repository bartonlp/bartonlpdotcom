#!/usr/bin/php
<?php
// File and new size
chdir('rotary');
echo getcwd() . "<br>";

$files = glob("*");

$imgwidth = 600;

foreach($files as $filename) {
  $newwidth = $imgwidth;

  list($width, $height) = getimagesize($filename);

  $newwidth = $imgwidth;
  $newheight = $height * $imgwidth/$width;

  echo "$filename: $width, $height, NEW: $newwidth, $newheight\n";
}
exit();
// Load
$thumb = imagecreatetruecolor($newwidth, $newheight);
$ext = pathinfo($filename)['extension'];
switch($ext) {
  case 'png':
    $source = imagecreatefrompng($filename);
    $mime = 'image/png';
    $func = 'png';
    break;
  case 'jpg':
    $source = imagecreatefromjpeg($filename);
    $mime = 'image/jpg';
    $func = 'jpeg';
    break;
  case 'gif':
    $source = imagecreatefromgif($filename);
    $mime = 'image/gif';
    $func = 'gif';
    break;
  default:
    throw(new Exception("Not an image file"));
}

// Resize
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

header("Content-type: $mime");

// Output
$func = "image$func";
$func($thumb);
