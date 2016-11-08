<?php
return <<<EOF
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
     content="Heidi Kemmer"/>
  <meta name=viewport content="width=device-width, initial-scale=1">
  <!-- ICONS, RSS -->
  <link rel="shortcut icon" href="http://www.bartonphillips.org/favicon.ico" />
  <!-- CSS -->
  <link rel="stylesheet" href="css/heidi.css"/>
  {$arg['link']}
  <!-- jQuery -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <script>
var lastId = $this->LAST_ID;
jQuery(document).ready(function($) {
  $("#logo").attr('src', "/heidi/tracker.php?page=script&id="+lastId);
});
  </script>
  <script async src="http://bartonphillips.net/js/tracker.js"></script>
  <!-- Custom Scripts -->
{$arg['extra']}
{$arg['script']}
{$arg['css']}
</head>
EOF;
