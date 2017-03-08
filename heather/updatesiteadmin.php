<?php
$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

$errorhdr = <<<EOF
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta name="robots" content="noindex">
</head>
EOF;

$h->title = "Update Site Admin for Heather";
$h->banner = "<h1>Update Site Admin For Heather</h1>";
$s->site = "bartonlp.com/heather";

if(!$_GET && !$_POST) {
  $_GET['page'] = "admin"; // Force us to the admin page if not get or post
}

$updatepage = UpdateSite::secondHalf($S, $h, $s);

echo <<<EOF
$updatepage
EOF;
