<?php
// BLP 2016-01-09 -- check to see if this may be a robot

if(!$this->isBot) {
  //echo "$S->agent<br>";
  if(preg_match("~^.*(?:(msie\s*\d*)|(trident\/*\s*\d*)).*$~i", $this->agent, $m)) {
    $which = $m[1] ? $m[1] : $m[2];
    echo <<<EOF
<!DOCTYPE html>
<html>
<head>
  <title>NO GOOD MSIE</title>
</head>
<div style="background-color: red; color: white; padding: 10px;">
Your browser's <b>Agent String</b> says it is:<br>
$m[0]<br>
Sorry you are using Microsoft's Broken Internet Explorer ($which).
</div>
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

return <<<EOF
<head>
  <title>{$arg['title']}</title>
  <!-- METAs -->
  <meta charset='utf-8'/>
  <meta name="copyright" content="$this->copyright">
  <meta name="Author" content="$this->author"/>
  <meta name="description" content="{$arg['desc']}"/>
  <meta name="keywords"
    content="Barton Phillips, Applitec Inc., Rotary, Programming, Poker, Tips and tricks, blog"/>
  <meta name=viewport content="width=device-width, initial-scale=1">
  <link rel="canonical" href="http://www.bartonphillips.com">
  <!-- CSS -->
  <link rel="stylesheet" href="https://bartonphillips.net/css/blp.css">
  <!-- css is not css but a link to tracker via .htaccess RewriteRule. -->
  <link rel="stylesheet" href="/csstest-{$this->LAST_ID}.css" title="blp test">
  {$arg['link']}
  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
  <script>
var lastId = $this->LAST_ID;
  </script>
  <script src="https://bartonphillips.net/js/tracker.js"></script>
  <!-- Custom Scripts -->
{$arg['extra']}
{$arg['script']}
{$arg['css']}
<!--
  <script  type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "name": "Barton Phillips Expermental",
  "alternateName": "Barton Phillips Home",
  "url": "http://www.bartonlp.com"
}
  </script>
  <script  type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "http://www.bartonlp.com",
  "logo": "https://bartonphillips.net/images/blp-image.png"
}
</script>
-->
</head>
EOF;
