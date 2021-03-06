#! /usr/bin/php
<?php
// BLP 2021-02-26 -- The pricedata table is not used by the stock programs it is purely archival
// infomation. Also there are no more $odd items.
// BLP 2020-05-20 -- DJI has no value  
// BLP 2018-02-07 -- Added 'volume' to table.  
// do an update via CRON of the pricedata table.
$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
ErrorClass::setDevelopment(true);
$S = new Database($_site);

$alphakey = "FLT73FUPI9QZ512V";

date_default_timezone_set("America/New_York");

$prefix = "https://cloud.iexapis.com/stable";
$token = "token=pk_feb2cd9902f24ed692db213b2b413272";

$sql = "select stock, price, qty, status from stocks.stocks";
$S->query($sql);

while(list($stock, $price, $qty, $status) = $S->fetchrow('num')) {
  // NOTE Alpha needs RDS-A while iex wants RDS.A
  $stock = ($stock == "RDS-A") ? "RDS.A" : $stock;
  $stocks[$status][$stock] = [$price, $qty];
}

$mutual = $stocks['mutual'];
$active = $stocks['active'];
$watch = $stocks['watch'];
$sold = $stocks['sold']; // We will track sold now


$active += $watch + $sold; // add it to $active, $watch and $sold

// Get information from IEX
$str = "$prefix/stock/market/batch?symbols=" . implode(',', array_keys($active)) . "&types=quote&" . $token . "&filter=symbol,latestPrice,latestUpdate,latestVolume";

$h = curl_init();
curl_setopt($h, CURLOPT_URL, $str);
curl_setopt($h, CURLOPT_HEADER, 0);
curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

// We get the stocks from the array above  
$ret = curl_exec($h);
$ar = json_decode($ret, true); // Make it an array for starts

// $ar is IEX data

// BLP 2021-02-26 -- There are no more ODD items.
// odd has the DJI and ZENO and could have others that are in $active but not on eix.
//$odd = array_diff(array_keys($active), array_keys($ar));

$aa = array_keys($mutual); // Get the mutual funds because they are not in iex

// Now add the things from $odd that we didn't find in the results from iex

// BLP 2021-02-26 -- no more odd items
//$aa = array_merge($odd, $aa); // mutual + odd

$ar = json_decode($ret); // Now get $ar as an object

// loop thought the $aa array of mutual funds and do Alpha

foreach($aa as $k) {
  $alp = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$k&apikey=$alphakey";

  curl_setopt($h, CURLOPT_URL, $alp);
  $alpha = curl_exec($h);

  $alpha = json_decode($alpha, true); // decode as an array

  //$name = $alpha["Meta Data"]["2. Symbol"];

  foreach($alpha["Time Series (Daily)"] as $date=>$v) {
    $close = $v["4. close"]; // The 'close' price is also the 'last' price during the day.
    $volume = $v["5. volume"]; 
    break;
  }

  $price = round($close, 2);

  $S->query("insert into stocks.pricedata (date, stock, price, volume) ".
            "values('$date', '$k', '$price', '$volume') ".
            "on duplicate key update price='$price'");
}

// Now loop through the object from iex

$quotes = '';
$total = '';

foreach($ar as $v) {
  $qt = $v->quote;
  $st = $qt->symbol;

  if($st == "RDS.A") $st = "RDS-A";
  
  $date = date("Y-m-d H:i:s", $qt->latestUpdate / 1000);
  
  $price = $qt->latestPrice; // raw price
  $volume = ($qt->latestVolume == null ? 0 : $qt->latestVolume); // volume

  $S->query("insert into stocks.pricedata (date, stock, price, volume) ".
            "values('$date', '$st', '$price', '$volume') ".
            "on duplicate key update price='$price'");
}
