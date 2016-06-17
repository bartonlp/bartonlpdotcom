<?php
// Test if a css property exists.
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");
$S = new $_site['className']($_site);
$h->script = <<<EOF
  <script>
function isPropertySupported(property) {
	return property in document.body.style;
}
jQuery(document).ready(function($) {
  $("main input[type='submit']").click(function() {
    var check = $("main input").val();
    if(isPropertySupported(check)) {
      $("main div").html("Property '"+check+"' Supported");
    } else {
      $("main div").html("'"+check+"' is not Supported");
    }
    $("main input[type='text']").val('').focus();
    return false;
  });
});
  </script>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<main>
<div></div>
<form>
<input type='text' name='check' autofocus>
<input type='submit' value="Submit">
</form>
</main>
$footer
EOF;

