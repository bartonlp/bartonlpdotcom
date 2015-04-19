<?php
// BLP 2014-09-15 -- utility to assist downloading files

require_once("/var/www/includes/siteautoload.class.php");
$S = new Blp; // count page

$file = $_GET['file'];

// Note the path should be relative to the directory where download.php lives OR be absolute!
// OR no path if the file to download lives in the same directory as download.php.

$path = rtrim($_GET['path'], "/");

header('Content-Type: text/html');

$errorhdr = <<<EOF
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta name="robots" content="noindex">
</head>
EOF;

if(empty($_SERVER["HTTP_REFERER"])) {
  echo "You got here by accident! <a href=''> Return to welcome page</a><br>";
  exit();
}

if(empty($file)) {
  mail("barton@bartonlp.com",
       "$S->self, NO FILE GIVEN",
       "NO File given: Referrer={$_SERVER['HTTP_REFERER']}, IP={$_SERVER['REMOTE_ADDR']}, " .
       "AGENT={$_SERVER['HTTP_USER_AGENT']}",
       "From: download.php", "-f barton@bartonlp.com");

  echo <<<EOF
$errorhdr
<body>
An Error Has Occured. The Webmaster has been notified. Sorry
</body>
</html>
EOF;
  exit();
}

if($path) $path .= "/";

$fp = @fopen("${path}${file}",'r');

if($fp === false) {
  mail("barton@bartonlp.com", "$S->self,  File Open Error",
       "Errno=$ERRNO, $ERRSTR,\n" .
       "file info: ${path}${file},\n" .
       "Referrer={$_SERVER['HTTP_REFERER']},\nIP={$_SERVER['REMOTE_ADDR']}," .
       "AGENT={$_SERVER['HTTP_USER_AGENT']}",
       "From: download.php", "-f bartonp@bartonlp.com");

  echo <<<EOF
$errorhdr
<body>
An Error Has Occured. The Webmaster has been notified. Sorry
</body>
</html>
EOF;
  exit();
}

header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment;filename=$file");

fpassthru($fp);
fclose($fp);
