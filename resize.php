<?php
// Use imagick to resize files.  

$images = glob('rotary/*.jpg');

foreach($images as $image_file) {
  // Providing 0 forces thumbnailImage to maintain aspect ratio
  $image = new Imagick($image_file);
  $image->thumbnailImage(600,0);
  $image->writeImages("$image_file.thumb.png", false);
}  
  
echo "Done<br>";
