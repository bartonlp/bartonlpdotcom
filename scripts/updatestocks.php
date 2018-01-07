#! /usr/bin/php
<?php
$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
ErrorClass::setDevelopment(true);
$S = new Database($_site);

$alphakey = "FLT73FUPI9QZ512V";

date_default_timezone_set("America/New_York");

$prefix = "https://api.iextrading.com/1.0";

$sql = "select stock, price, qty, status from stocks.stocks where status != 'sold'";
$S->query($sql);

while(list($stock, $price, $qty, $status) = $S->fetchrow('num')) {
  $stocks[$status][$stock] = [$price, $qty];
}

$mutual = $stocks['mutual'];
$active = $stocks['active'];
$watch = $stocks['watch'];

$active += $watch;

$str = "$prefix/stock/market/batch?symbols=" . implode(',', array_keys($active)) . "&types=quote";

$h = curl_init();
curl_setopt($h, CURLOPT_URL, $str);
curl_setopt($h, CURLOPT_HEADER, 0);
curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

// We get the stocks from the array above  
$ret = curl_exec($h);
$ar = json_decode($ret);

$aa = array_keys($mutual);
$aa[] = 'DJI';

foreach($aa as $k) {
  $k = preg_replace("/-BLP/", '', $k);
  $alp = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$k&apikey=$alphakey";

  curl_setopt($h, CURLOPT_URL, $alp);
  $alpha = curl_exec($h);

  $alpha = json_decode($alpha, true); // decode as an array

  //$name = $alpha["Meta Data"]["2. Symbol"];
  
  foreach($alpha["Time Series (Daily)"] as $date=>$v) {
    $close = $v["4. close"]; // The 'close' price is also the 'last' price during the day.
    break;
  }

  $price = round($close, 2);

  $S->query("insert into stocks.pricedata (date, stock, price) values('$date', '$k', '$price') ".
            "on duplicate key update price='$price'");
}

$quotes = '';

foreach($ar as $k=>$v) {
  $qt = $v->quote;
  $st = $qt->symbol;
  if($st == "RDS.A") $st = "RDS-A";
  
  $date = date("Y-m-d H:i:s", $qt->latestUpdate / 1000);
  $price = $qt->latestPrice; // raw price
  
  $S->query("insert into stocks.pricedata (date, stock, price) values('$date', '$st', '$price') ".
            "on duplicate key update price='$price'");
}

echo "updatestocks.php DONE\n";

// Table format:
/*
 CREATE TABLE `pricedata` (
  `date` date NOT NULL,
  `stock` varchar(10) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`date`,`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

CREATE TABLE `stocks` (
  `stock` varchar(10) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','watch','sold') DEFAULT NULL,
  PRIMARY KEY (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
*/

