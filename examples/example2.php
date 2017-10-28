<?php
// Example of how to do a POST instead of a GET.

$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new $_site->className($_site);

// http_build_query($query_data, ...) takes an object or array. $_site is an array.

$t->test = "test from example2";
$t->siteName = "bartonlp.com";

$options = array('http' => array(
                                 'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                 'method'  => 'POST',
                                 'content' => http_build_query($t)
                                )
                );

//vardump("data: ", http_build_query($_site));

$context  = stream_context_create($options);

$siteName = file_get_contents("http://www.bartonlp.com/examples/example1.php", false, $context);

echo <<<EOF
$top
<h1>Test</h1>
$siteName
$footer
EOF;
