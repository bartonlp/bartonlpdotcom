<?php
// BLP 2014-09-11 -- modified for requirejs. Add $pageScript as a pass in parameter
// that can override the default jquery.js and tracker.js

$pageScript = <<<EOF
  <script src='http://bartonlp.com/html/js/jquery.js'></script>
  <script src='http://bartonlp.com/html/js/tracker.js'></script>
  <!--<script src="//fast.eager.io/Wlq8pcrZTL.js"></script>-->
EOF;

if($arg['pageScript']) {
  $pageScript = $arg['pageScript'];
}

$pageHeadText = <<<EOF
<head>
  <title>{$arg['title']}</title>
  <!-- METAs -->
  <meta charset='utf-8'/>
  <meta name="copyright" content="$this->copyright">
  <meta name="Author"
     content="Barton L. Phillips, mailto:barton@bartonphillips.org"/>
  <meta name="description"
     content="{$arg['desc']}"/>
  <meta name="keywords"
     content="Barton Phillips, Granby, Applitec Inc., Rotary, Programming,
        RSS Generator, Poker, Tips and tricks, blog"/>
  <meta name=viewport content="width=device-width, initial-scale=1">
  <!-- ICONS, RSS -->
  <link rel="shortcut icon" href="http://www.bartonphillips.org/favicon.ico" />
  <link rel="alternate" type="application/rss+xml" title="RSS" href="/rssfeed.xml" />
  <!-- CSS -->
  <link rel="stylesheet" href="css/blp.css">
  {$arg['link']}
  <!-- pageScript -->
  $pageScript
  <!-- Custom Scripts -->
{$arg['extra']}
{$arg['script']}
{$arg['css']}
</head>
EOF;
?>
