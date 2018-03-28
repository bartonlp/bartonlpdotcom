#! /usr/bin/php
<?php
// BLP 2018-03-22 -- Get the value of each of my stocks and save it in the stocks.values table.
// This is run as a CRON job mon-fri.

$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
ErrorClass::setDevelopment(true);
ErrorClass::setNoEmailErrs(true);
ErrorClass::setNoHtml(true);
$S = new Database($_site);

$prefix = "https://api.iextrading.com/1.0"; // iex prefix.

date_default_timezone_set("America/New_York");

$sql = "select stock, qty from stocks.stocks where status='active'";
$S->query($sql);

while(list($stock, $qty) = $S->fetchrow('num')) {
  // NOTE Alpha needs RDS-A while iex wants RDS.A
  $stock = $stock == 'RDS-A' ? "RDS.A" : $stock;
  $spq[$stock] += $qty; // add qty because we may have BA and BA-BLP
}

$str = "$prefix/stock/market/batch?symbols=" . implode(',', array_keys($spq)) . "&types=quote";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $str);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// We get the stocks from the array above  
$ret = curl_exec($ch);
$ar = json_decode($ret);

$total = 0.0;

foreach($ar as $v) {
  $qt = $v->quote;

  $st = $qt->symbol;
  $date = date("Y-m-d H:i:s", $qt->latestUpdate / 1000);
  $price = $qt->latestPrice; // raw price
  $value = $price * $spq[$st];
  $total += $value;
  //echo "$st, value: $value\n";
  $S->query("insert into stocks.`values` (date, stock, value) values('$date', '$st', '$value') ".
            "on duplicate key update value='$value'");
}
echo "Total: $total\n";
echo "updatestocks.php DONE\n";

/*
CREATE TABLE `values` (
  `date` date NOT NULL,
  `stock` varchar(10) NOT NULL,
  `value` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`date`,`stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
