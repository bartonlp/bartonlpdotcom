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
  <meta name="keywords"
    content="Barton Phillips, Applitec Inc., Rotary, Programming, Poker, Tips and tricks, blog"/>
  <meta name=viewport content="width=device-width, initial-scale=1">
  <link rel="canonical" href="http://www.bartonphillips.com">
  <!-- CSS -->
  <link rel="stylesheet" href="http://bartonphillips.net/css/blp.css">
  {$arg['link']}
  <!-- jQuery -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
  <script>
var lastId = $this->LAST_ID;
  </script>
  <script src="http://bartonphillips.net/js/tracker.js"></script>
  <!-- Custom Scripts -->
{$arg['extra']}
{$arg['script']}
{$arg['css']}
<script  type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "name": "Barton Phillips Expermental",
  "alternateName": "Barton Phillips Home",
  "url": "http://www.bartonlp.com"
}
</script>
<script  type="application/ld+json">{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "http://www.bartonlp.com",
  "logo": "http://www.bartonlp.com/images/images/blp-image.png"
}
</script>
</head>
EOF;
