<?php
$_site = require_once(getenv("SITELOADNAME"));
//$S = new $_site->className($_site);

$list = glob("Pictures/passo2011/*");
//vardump($list);
echo implode("\n", $list);

