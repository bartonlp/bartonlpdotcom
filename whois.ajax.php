<?php
// special whois for granbyrotary.org admin
// We are passed the email address
// We do a whois on the domain part of the address
// and a dig mx on the ip too

$ret = array();

$ip = $_GET['ip'];
$domain = $_GET['domain'];

//Header("Content-type: text/plain");
$errorhdr = <<<EOF
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta name="robots" content="noindex">
</head>
<body>
EOF;
$footer = "</body></html>";

if($ip == 'yes') {
  $ip = gethostbyname($domain);

  echo <<<EOF
<h1>Info for: $domain, IP: $ip</h1>
EOF;

  $command = "whois $ip";
  exec($command, $ret);
  $v = implode("\n", $ret);

  if(preg_match("/country:\s+(.*)/i", $v, $m)) {
    echo $m[1];
  }
} else {
  // If given an IP address (or a url) instead of an email address.
  
  $command = "whois $domain";
  array_push($ret, "<h2>Whois Records</h2>*******************************<pre>\n$command\n");

  exec($command, $ret);
  $ret[] = "</pre>";
  echo implode("\n", $ret);
}
