#!/usr/bin/php
<?php
// This is used for Rotary. When I take photos on my iPhone they are way to big.
// This will take the big images and create 600px wide images.
// We use the directory 'rotary' for the big images and the resized images. The big images are all
// jpeg images and the new resized images are all png.

// Get the autoloader

require_once("/var/www/vendor/autoload.php");

// Change to the 'rotary' directory.

chdir('/var/www/bartonlp/rotary');

// Grab all the jpeg files.

$files = glob("*.jpg");

// New width is 600px

$imgwidth = 600;

// Loop through all of the jpegs

foreach($files as $filename) {
  // get the width and height of the big images
  
  [$width, $height] = getimagesize($filename);

  // create a newheight based on the 600px width
  
  $newheight = ($imgwidth * $width)/$height;

  //echo "$filename: $width, $height, NEW: $imgwidth, $newheight\n";

  // create a blank resourse of the right size
  
  $thumb = imagecreatetruecolor($imgwidth, $newheight);

  // Get the file extension of the big file
  $x = pathinfo($filename);
  $ext = $x['extension'];
  $thumbfile =  pathinfo($filename)['filename'] . '.png';

  // Get the original jpeg as a resource
  
  $source = imagecreatefromjpeg($filename);
  
  // Rotate the original image. NOT sure why I have to do this?

  $source = imagerotate($source, 270, 0);

  // NOW use the height and width to create a new resourse with newwidth and newheight. NOTE the
  // swap! Again not sure why.
  
  imagecopyresized($thumb, $source, 0, 0, 0, 0, $imgwidth, $newheight, $height, $width);

  // Output the resource as a png

  imagepng($thumb, "$thumbfile");
}

echo "Done\n";
