<?php
$_site = require_once(getenv("SITELOADNAME"));
//$S = new $_site->className($_site);

$list = glob("Pictures/passo2011/*");
//vardump($list);
foreach($list as $l) {
  echo "https://www.bartonlp.com/$l\n";
}
