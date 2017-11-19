<?php
// BLP 2014-04-29 -- Do various git functions

if($cmd = $_POST['page']) {
  $site = $_POST['site'];
  chdir($site);
  $out = '';
  error_log("cmd: $cmd, site: $site");
  exec("git " . $cmd, $out);
  $out = implode("\n", $out);
  $out = preg_replace(array("/</", "/>/"), array("&lt;","&gt;"), $out);
  echo "<pre>$out</pre>";
  exit();
}

$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

$h->script =<<<EOF
  <script>
jQuery(document).ready(function($) {
  $(".git").click(function() {
    var page = $(this).attr('data-page');
    var site = $(this).attr('data-site');
    var self = $(this);
    console.log("page %s, site %s", page, site);
    $.ajax({
      url: "gitinfo.php",
      data: {page: page, site: site},
      type: 'post',
      success: function(data) {
             console.log(data);
             self.parent().find('.results').html(data);
           },
           error: function(err) {
             console.log(err);
           }
    });             
  });
});
  </script>
EOF;
$h->css =<<<EOF
  <style>
.git {
  border-radius: .5rem;
  font-size: 1rem;
  margin-bottom: .5rem;
}
.results {
  width: 100%;
  height: 10rem;
  overflow: auto;
  border: 1px solid black;
}
  </style>
EOF;

$h->title = "GIT Info";
$h->banner = "<h1>Show GIT Info</h1>";
list($top, $footer) = $S->getPageTopBottom($h);

$prefix = "/var/www";

$sites = ['/applitec', '/bartonlp', '/bartonphillips.com', '/bartonphillips.org', '/bartonphillipsnet', '/granbyrotary.org', '/messiah'];

$data = '';

foreach($sites as $v) {
  $site = $v;
  $v = $prefix.$v;
  $data .= <<<EOF
<div>
<h2>For $site</h2>
<button class='git' data-page="status" data-site='$v'>Status</button>
<button class='git' data-page='log --abbrev-commit' data-site='$v'>Log</button>
<button class='git' data-page='diff -w' data-site='$v'>Diff</button>
<button class='git' data-page='diff -w HEAD^' data-site='$v'>Diff -w HEAD^</button>
<div class='results'>
</div>
<hr>
</div>
EOF;
}

echo <<<EOF
$top
$data
$footer
EOF;
