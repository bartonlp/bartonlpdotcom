<?php
// Test the CIDR.php class
$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);
ErrorClass::setDevelopment(true);

$cidr = require_once("CIDR.php");

$what = CIDR::match("75.1.73.143", "75.1.73.0/32");
vardump("what", $what);
