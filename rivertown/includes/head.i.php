<?php
// BLP 2018-05-11 -- ADD 'NO GOOD MSIE' message

if(!$this->isBot) {
  //echo "$this->agent<br>";
  if(preg_match("~^.*(?:(msie\s*\d*)|(trident\/*\s*\d*)).*$~i", $this->agent, $m)) {
    $which = $m[1] ? $m[1] : $m[2];
    echo <<<EOF
<!DOCTYPE html>
<html>
<head>
  <title>NO GOOD MSIE</title>
</head>
<body>
<div style="background-color: red; color: white; padding: 10px;">
Your browser's <b>User Agent String</b> says it is:<br>
$m[0]<br>
Sorry you are using Microsoft's Broken Internet Explorer ($which).</div>
<div>
<p>You should upgrade to Windows 10 and Edge if you must use MS-Windows.</p>
<p>Better yet get <a href="https://www.google.com/chrome/"><b>Google Chrome</b></a>
or <a href="https://www.mozilla.org/en-US/firefox/"><b>Mozilla Firefox</b>.</p></a>
These two browsers will work with almost all previous
versions of Windows and are very up to date.</p>
<b>Better yet remove MS-Windows from your
system and install Linux instead.
Sorry but I just can not continue to support ancient versions of browsers.</b></p>
</div>
</body>
</html>
EOF;
    exit();
  }
}

if(!$arg['keywords']) {
  $arg['keywords'] = "River Towne Rentals, Property Management, Rent Property in New Bern";
}

return <<<EOF
<head>
  <title>{$arg['title']}</title>
  <!-- METAs -->
  <meta name=viewport content="width=device-width, initial-scale=1">
  <meta charset='utf-8'>
  <meta name="copyright" content="$this->copyright">
  <meta name="Author"
    content="$this->author">
  <meta name="description"
    content="{$arg['desc']}">
  <meta name="keywords"
    content="{$arg['keywords']}">

  <!-- ICONS, RSS -->
  <link rel="shortcut icon" href="https://bartonphillips.net/images/favicon.ico">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="https://bartonphillips.net/css/blp.css" title="blp default">
{$arg['link']}
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
{$arg['extra']}
{$arg['script']}
{$arg['css']}
</head>
EOF;
