#!/usr/bin/php
<?php
// File and new size
require_once("/var/www/vendor/autoload.php");
  
chdir('rotary');

$files = glob("*");

$imgwidth = 600;

foreach($files as $filename) {
  [$width, $height] = getimagesize($filename);

  $newheight = ($imgwidth * $width)/$height;

  //echo "$filename: $width, $height, NEW: $imgwidth, $newheight\n";
  // Load
  $thumb = imagecreatetruecolor($imgwidth, $newheight);

  //$w = imagesx($thumb);
  //$h = imagesy($thumb);

  //echo "w: $w, h: $h\n";
  
  $x = pathinfo($filename);
  $ext = $x['extension'];
  $thumbfile =  $x['filename'] . '.png';
  //echo "base: $thumbfile, ext: $ext\n";
  
  switch($ext) {
    case 'png':
      $source = imagecreatefrompng($filename);
      //$mime = 'image/png';
      //$func = 'png';
      break;
    case 'jpg':
      $source = imagecreatefromjpeg($filename);
      //$mime = 'image/jpg';
      //$func = 'jpeg';
      break;
    case 'gif':
      $source = imagecreatefromgif($filename);
      //$mime = 'image/gif';
      //$func = 'gif';
      break;
    default:
      throw(new Exception("Not an image file"));
  }

  // Resize

  $source = imagerotate($source, 270, 0);
  imagecopyresized($thumb, $source, 0, 0, 0, 0, $imgwidth, $newheight, $height, $width);
  // Output

  imagepng($thumb, "$thumbfile");
  imagejpeg($source, "test.jpg");
}
echo "Done\n";
