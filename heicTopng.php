<?php
// This will move and resize images in HEIC format and make the PNG. It also changes the size from
// huge to 600px wide and keeps the aspect ratio.
// This uses Cloudmersive at 'https://account.cloudmersive.com/keys'
// Account: bartonphillips@gmail.com
// Password: 709Blp8653
// My key is: 'ee045117-9a3b-431c-91a9-e2ff078b058d'  
// I have a FREE subscription which gives me 800 shots.

ini_set("max-execution-time", "300");
  
//$_site = require_once(getenv("SITELOADNAME")); // get autoload.php from vendor/
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Apikey
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Apikey', 'ee045117-9a3b-431c-91a9-e2ff078b058d');

$apiInstance = new Swagger\Client\Api\ConvertImageApi(
  new GuzzleHttp\Client(),
  $config
);

// I could do this in a loop using glob() but for now just one file.

$input_file = "rotary/*.heic"; // Change file name
$format1 = 'HEIC';
$format2 = 'PNG';

$files = glob("rotary/IMG_03*.heic");
foreach($files as $input_file) {
  try {
    $result = $apiInstance->convertImageImageFormatConvert($format1, $format2, $input_file);
  } catch (Exception $e) {
    echo 'Exception when calling ConvertImageApi->convertImageMultipageImageFormatConvert: ', $e->getMessage(), PHP_EOL;
  }

  [$width, $height] = getimagesizefromstring($result); // get the width and height from the real image.

  // $result is a full fledged png and we need a resource.

  $source = imagecreatefromstring($result); // this creates a resource from our image.

  $newwidth = 600;
  $newheight = $height * 600/$width;

  error_log("width: $width, height: $height, neww: $newwidth, newh: $newheight");

  $thumb = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

  // NOTE: the file to write to must have group 'www-data' and group write permision.

  imagepng($thumb, "$input_file.png");
}
echo "DONE<br>";



