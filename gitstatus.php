<?php
// BLP 2014-04-29 -- Do various git functions

if($cmd = $_POST['page']) {
  $ret = '';
  
  foreach(['/vendor/bartonlp/site-class', '/applitec', '/bartonlp', '/bartonphillips', '/bartonphillipsnet', '/granbyrotary', '/messiah'] as $site) {
    chdir("/var/www/$site");
    //error_log("cmd: $cmd, site: $site");
    exec("git $cmd", $out);
    $out = implode("\n", $out);
    $ret .= <<<EOF
<hr>
<pre><b>$site</b>
$out
</pre>
EOF;
  }

  echo $ret;
  exit();
}

$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new $_site->className($_site);

$h->script =<<<EOF
  <script>
jQuery(document).ready(function($) {
  $("#git").click(function() {
    $("#results").html('');

    $.ajax({
      url: "gitstatus.php",
      data: {page: 'status'},
      type: 'post',
      success: function(data) {
             //console.log(data);
             $('#results').append(data);
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
#git {
  border-radius: .5rem;
  font-size: 1rem;
  margin-bottom: .5rem;
}
#results {
  width: 100%;
/*  height: 20rem; */
  overflow: auto;
/*  border: 1px solid black; */
}
  </style>
EOF;

$h->title = "GIT Status All";
$h->banner = "<h1>Show GIT Status All</h1>";
list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top

<div>
<button id='git'>Status</button>
<div id='results'>
</div>
<hr>
</div>

$footer
EOF;