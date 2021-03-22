<?php
// NOTE THIS USES PUG
  
$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

use Pug\Pug;

$pug = new Pug();

if($_POST || ($get = $_GET['filename'])) {
  if($get) {
    $file = $get;
    $type = "GitHub";
  } else {
    $file = $_POST['filename'];
    $type = $_POST['type'];
  }

  switch($type) {
    case "GitHub":
      $parser = new \cebe\markdown\GithubMarkdown();
      $github =<<<EOF
  <link rel="stylesheet" href="http://bartonphillips.net/css/theme.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
  <script src="http://bartonphillips.net/js/syntaxhighlighter.js"></script>
  <script>
jQuery(document).ready(function($) {
  var code = $("code[class|='language'");
  $(code).each(function(i, e) {
    var cl = $(e).attr('class');
    $(e).parent().addClass(cl.replace(/language-(.*)/, 'brush: $1'));
    $(e).parent().html($(e).html());
    $(e).remove();
  });
});
  </script>
EOF;
      break;
    case "Traditional":
      $parser = new \cebe\markdown\Markdown();
      break;
    case "Extended":
      $parser = new \cebe\markdown\MarkdownExtra();
      break;
    case "RAW":
      $parser = "RAW";
      break;
    default:
      echo "ERROR $type<br>";
      exit();
  }

  $header =<<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Markdown Converter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset='utf-8'>
  <meta name="copyright" content="2017 Barton L. Phillips">
  <meta name="Author"
    content="Barton L. Phillips, mailto:bartonphillips@gmail.com">
  $github
</head>
<body>
EOF;

  //echo "$file<br>";
  $output = file_get_contents($file);
  //echo "$output";

  if(empty($output)) {
    echo "<h1 style='text-align: center; font-size: 2rem;'>File Not Found<br>$file</h1>";
    exit();
  }

  if($parser != "RAW") {
    $output = $header . $parser->parse($output) . "</body>\n</html>";
  }
} else {
  // Render the start page.
  $args->id = $S->LAST_ID;
  $args->footer = $S->getHitCount();
  $args->mtime = "2016-10-23 11:32 PDT";
  $args->copyright = $S->copyright;
  $args->author = $S->author;
  $args->title = "Show Markdown";
  $args->desc = "Expermental Page";

  $output = $pug->render('./pug/showmarkdown.pug', ['args'=>(array)$args]);
}

echo $output;
