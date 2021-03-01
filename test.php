<?php
echo "From test.php<br>";  
$_site = require_once(getenv("SITELOADNAME"));
ErrorClass::setNoEmailErrs(true);
ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);
echo "This is a test<br>";
$str = "This is an SqlException";
error_log("This is a test from test.php");

throw(new SqlException($str, $S));

