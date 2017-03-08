<?php
return <<<EOF
<head>
  <title>{$arg['title']}</title>
  <!-- METAs -->
  <meta charset='utf-8'/>
  <meta name="copyright" content="$this->copyright">
  <meta name="Author" content="$this->author"/>
  <meta name="description" content="{$arg['desc']}"/>
  <meta name="keywords"
    content="Heidi stuff"/>
  <meta name=viewport content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link rel="stylesheet" href="http://bartonphillips.net/css/blp.css">
  {$arg['link']}
  <!-- jQuery -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
  <script>var lastId = $this->LAST_ID;</script>
  <script src="http://bartonphillips.net/js/tracker.js"></script>
  <!-- Custom Scripts -->
{$arg['extra']}
{$arg['script']}
{$arg['css']}
</head>
EOF;
