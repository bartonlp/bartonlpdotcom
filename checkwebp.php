<?php
// check/detect webp ability
require_once("/var/www/includes/siteautoload.class.php");
$S = new Blp; // takes an array if you want to change defaults

$h->extra = <<<EOF
<script>
// check_webp_feature:
//   'feature' can be one of 'lossy', 'lossless', 'alpha' or 'animation'.
//   'callback(feature, result)' will be passed back the detection result (in an asynchronous way!)

function check_webp_feature(feature, callback) {
  var kTestImages = {
      lossy: "UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AAAAAA",
      lossless: "UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==",
      alpha: "UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAABQBAJ0BKgEAAQAAAP4AAA3AAP7mtQAAAA==",
      animation: "UklGRlIAAABXRUJQVlA4WAoAAAASAAAAAAAAAAAAQU5JTQYAAAD/////AABBTk1GJgAAAAAAAAAAAAAAAAAAAGQAAABWUDhMDQAAAC8AAAAQBxAREYiI/gcA"
  };
  var img = new Image();
  img.onload = function() {
    var result = (img.width > 0) && (img.height > 0);
    callback(feature, result);
  };
  img.onerror = function() {
    callback(feature, false);
  };
  img.src = "data:image/webp;base64," + kTestImages[feature];
}

jQuery(document).ready(function($) {
  var features = ["lossy", "lossless", "anamation"];
  features.forEach(function(feature) {
    check_webp_feature(feature, function(f, r) {
      $("#content").append(f + ": " + r + "<br>");
    });
  });

  // ***********************
  // Webp example
/* 
  var WebPImage = { width:{value:0},height:{value:0} }
  var decoder = new WebPDecoder();
  var bitmap = decoder.WebPDecodeRGB(data, data.length, WebPImage.width,
                                     WebPImage.height);
  //or
  var bitmap = decoder.WebPDecodeRGBA(data, data.length, WebPImage.width,
                                      WebPImage.height);

  //read width/height
  var width = WebPImage.width.value;
  var height = WebPImage.height.value;
  console.log("w: %d, h: %d", width, height);

  // Encode
  var out={output:''}; //rgba data
  var encoder = new WebPEncoder();

  //config, you can set all arguments or what you need
  var config = new Object()
  config.target_size = 0;			// if non-zero, set the desired target size in bytes. Takes precedence over the 'compression' parameter.
  config.target_PSNR = 0.;		// if non-zero, specifies the minimal distortion to	try to achieve. Takes precedence over target_size.
  config.method = method;			// quality/speed trade-off (0=fast, 6=slower-better)
  config.sns_strength = 50;		// Spatial Noise Shaping. 0=off, 100=maximum.
  config.filter_strength = 20;	// range: [0 = off .. 100 = strongest]
  config.filter_sharpness = 0;	// range: [0 = off .. 7 = least sharp]
  config.filter_type = 1;			// filtering type: 0 = simple, 1 = strong (only used if filter_strength > 0 or autofilter > 0)
  config.partitions = 0;			// log2(number of token partitions) in [0..3] Default is set to 0 for easier progressive decoding.
  config.segments = 4;			// maximum number of segments to use, in [1..4]
  config.pass = 1;				// number of entropy-analysis passes (in [1..10]).
  config.show_compressed = 0;		// if true, export the compressed picture back. In-loop filtering is not applied.
  config.preprocessing = 0;		// preprocessing filter (0=none, 1=segment-smooth)
  config.autofilter = 0;			// Auto adjust filter's strength [0 = off, 1 = on]
                  //   --- description from libwebp-C-Source Code --- 
  config.extra_info_type = 0;		// print extra_info
  config.preset = 0 				//0: default, 1: picture, 2: photo, 3: drawing, 4: icon, 5: text

  //set config; default config -> WebPEncodeConfig( null ) 
  encoder.WebPEncodeConfig(config); //when you set the config you must it do for every WebPEncode... new

  //start encoding
  var size = encoder.WebPEncodeRGB(inputData, w, h, w*3, qualityVal, out); //w*4 desc: w = width, 3:RGB/BGR, 4:RGBA/BGRA 
  // or
  var size = encoder.WebPEncodeRGBA(inputData, w, h, w*4, qualityVal, out); //w*4 desc: w = width, 3:RGB/BGR, 4:RGBA/BGRA 

  //after encoding, you can get the enc-details:
  str = encoder.ReturnExtraInfo();

  //output (array of bytes)
  var output = out.output;
*/

});

</script>
EOF;

$h->banner = "<h1>Webp Check</h1>";

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<div id='content'></div>
<picture>
  <source
    media="(max-width: 600px)"
    sizes="80vw"
    srcset="test.webp">
  <img
    sizes="80vw"
    src="CIMG0275.JPG"
    width='700'
    alt="test image">

</picture>
$footer
EOF;
