<?php
$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

if($_SERVER['HTTPS']) {
  echo $_SERVER['HTTPS'] . "<br>";
}
echo "test";