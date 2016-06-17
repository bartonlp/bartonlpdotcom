<?php
// Example of how to do a POST instead of a GET.

$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");
$S = new $_site['className']($_site);

$j = json_encode($_site);

// http_build_query($query_data, ...) takes an object or array. $_site is an array.

$options = array('http' => array(
                                 'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                 'method'  => 'POST',
                                 'content' => http_build_query($_site)
                                )
                );

//vardump("data: ", http_build_query($_site));

$context  = stream_context_create($options);

$siteName = file_get_contents("http://bartonlp.com/html/example1.php", false, $context);

echo <<<EOF
$top
<h1>Test</h1>
$siteName
$footer
EOF;
