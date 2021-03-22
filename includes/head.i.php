<?php
// BLP 2016-01-09 -- check to see if this may be a robot

return <<<EOF
<head>
  <title>{$arg['title']}</title>
  <!-- METAs -->
  <meta charset='utf-8'/>
  <meta name="copyright" content="$this->copyright">
  <meta name="Author" content="$this->author"/>
  <meta name="description" content="{$arg['desc']}"/>
  <meta name="keywords" content="Barton Phillips"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link rel="stylesheet" href="https://bartonphillips.net/css/blp.css">
  <!-- css is not css but a link to tracker via .htaccess RewriteRule. -->
  <link rel="stylesheet" href="/csstest-{$this->LAST_ID}.css" title="blp test">
  {$arg['link']}
  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
  <script>
var lastId = $this->LAST_ID;
  </script>
  <script src="https://bartonphillips.net/js/tracker.js"></script>
  <!-- Custom Scripts -->
{$arg['extra']}
{$arg['script']}
{$arg['css']}
</head>
EOF;
