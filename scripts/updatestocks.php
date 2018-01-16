#! /usr/bin/php
<?php
// do an update via CRON of the pricedata table.
$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
ErrorClass::setDevelopment(true);
$S = new Database($_site);

$alphakey = "FLT73FUPI9QZ512V";

date_default_timezone_set("America/New_York");

$prefix = "https://api.iextrading.com/1.0";

$sql = "select stock, price, qty, status from stocks.stocks";
$S->query($sql);

while(list($stock, $price, $qty, $status) = $S->fetchrow('num')) {
  // NOTE Alpha needs RDS-A while iex wants RDS.A
  $stock = ($stock == "RDS-A") ? "RDS.A" : $stock;
  $stock = preg_replace("/-BLP/", '', $stock);
  $stocks[$status][$stock] = [$price, $qty];
}

$mutual = $stocks['mutual'];
$active = $stocks['active'];
$watch = $stocks['watch'];
$sold = $stocks['sold']; // We will track sold now

$active += $watch + $sold; // add it to $active, $watch and $sold

$str = "$prefix/stock/market/batch?symbols=" . implode(',', array_keys($active)) . "&types=quote";

$h = curl_init();
curl_setopt($h, CURLOPT_URL, $str);
curl_setopt($h, CURLOPT_HEADER, 0);
curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

// We get the stocks from the array above  
$ret = curl_exec($h);
$ar = json_decode($ret, true); // Make it an array for starts

$odd = array_diff(array_keys($active), array_keys($ar));

$aa = array_keys($mutual); // Get the mutual funds because they are not in iex
// Now add the things from $odd that we didn't find in the results from iex

$aa = array_merge($odd, $aa);

$ar = json_decode($ret); // Now get $ar as an object

// loop thought the $aa array of mutual funds and $odd and do Alpha

foreach($aa as $k) {
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

// Now loop through the object from iex

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
