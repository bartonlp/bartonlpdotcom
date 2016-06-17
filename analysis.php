<?php
if($_GET['site']) {
  $__site = "?site={$_GET['site']}";
}
return eval("?>" . file_get_contents("http://bartonlp.com/html/analysis.eval$__site"));


