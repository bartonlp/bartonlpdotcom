#!/usr/bin/php
<?php
// Use imagick to resize files.  

$images = glob('rotary/*.jpg');

foreach($images as $image_file) {
  // Providing 0 forces thumbnailImage to maintain aspect ratio
  $image = new Imagick($image_file);
  $image->thumbnailImage(600,0);
  $thumb = preg_replace('~(.*)jpg~', '$1', $image_file) . "thumb.png";
  echo "$thumb\n";
  $image->writeImages("$thumb", false);
  chmod($thumb, 0660);
  exit();
}  
  
echo "Done<br>";
