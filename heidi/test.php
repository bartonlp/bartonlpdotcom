<?php
$_site = require_once("/var/www/includes/siteautoload.class.php");

$self = $_SERVER['PHP_SELF'];
$realpath = DOC_ROOT . $self;
echo "$realpath<br>";