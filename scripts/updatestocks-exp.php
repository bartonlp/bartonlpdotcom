#! /usr/bin/php
<?php
// BLP 2018-03-22 -- This DOES NOT WORK YET!
// Expermental ONLY.
// See value.php which gets the value of each of my stocks and saves it in stocks.values

$_site = require_once("/var/www/vendor/bartonlp/site-class/includes/siteload.php");
ErrorClass::setDevelopment(true);
ErrorClass::setNoEmailErrs(true);
ErrorClass::setNoHtml(true);
$S = new Database($_site);

$alphakey = "FLT73FUPI9QZ512V"; // www.alphavantage.co key.
$prefix = "https://api.iextrading.com/1.0"; // iex prefix.

date_default_timezone_set("America/New_York");

$sql = "select stock, qty, status from stocks.stocks";
$S->query($sql);

while(list($stock, $qty, $status) = $S->fetchrow('num')) {
  if($status == 'active') {
    $spq[$stock] = $qty;
  }

  // NOTE Alpha needs RDS-A while iex wants RDS.A
  $stock = ($stock == "RDS-A") ? "RDS.A" : $stock;
  $stock = preg_replace("/-BLP/", '', $stock);
  $stocks[$stock] = $status;
}

// Get sub arrays given the status.

function getit($st) {
  global $stocks; // need the global $stocks

  // We can use 'use' to pass extra elements to the anonomous function.
  
  return array_filter(array_keys($stocks), function($k) use($st, $stocks) {
    if($stocks[$k] == $st) return $stocks[$k];
    else return false;
  });
};

$mutual = getit('mutual');
$active = getit('active');
$watch = getit('watch');
$sold = getit('sold'); // We will track sold now

$active += $watch + $sold; // add it to $active, $watch and $sold

$str = "$prefix/stock/market/batch?symbols=" . implode(',', $active) . "&types=quote";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $str);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// We get the stocks from the array above  
$ret = curl_exec($ch);
$ar = json_decode($ret, true); // Make it an array for starts

// odd has the DJI and ZENO and could have others that are in $active but not on eix.

$odd = array_diff($active, array_keys($ar));

$aa = $mutual; // Get the mutual funds because they are not in iex

// Now add the things from $odd that we didn't find in the results from iex

$aa = array_merge($odd, $aa); // mutual + odd

$ar = json_decode($ret); // Now get $ar as an object

// loop thought the $aa array of mutual funds and $odd and do Alpha

if(false) { // don't do this right now
foreach($aa as $k) {
  $alp = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$k&apikey=$alphakey";

  curl_setopt($ch, CURLOPT_URL, $alp);
  $alpha = curl_exec($ch);

  $alpha = json_decode($alpha, true); // decode as an array

  // The $alpha array looks like:
  // ["Meta Data"]
  //   [1. Information] => Daily Prices (open, high, low, close) and Volumes
  //   [2. Symbol] => Dow Jones Industrial Average Index
  //   [3. Last Refreshed] => 2018-03-21
  //   [4. Output Size] => Compact
  //   [5. Time Zone] => US/Eastern
  // ["Time Series (Daily)"]
  //   [<Yesterday's Date>]
  //     [1. open] => 24723.4902
  //     [2. high] => 24758.2402
  //     [3. low] => 24655.4004
  //     [4. close] => 24713.7207
  //     [5. volume] => 66950386
  //   [<Yesterday -1 Date>]
  //     ...

  // This gets the First date (yesterdays date). The 'Time Series (Daily)' has keys that are the
  // date and because I don't know the date I can't index into the array. So I do a foreach and
  // then break on the first item which is the last date (current).
  
  foreach($alpha["Time Series (Daily)"] as $date=>$v) {
    $close = $v["4. close"]; // The 'close' price is also the 'last' price during the day.
    $volume = $v["5. volume"]; 
    break;
  }

  $price = round($close, 2);

  /*
  $S->query("insert into stocks.pricedata (date, stock, price, volume) ".
            "values('$date', '$k', '$price', '$volume') ".
            "on duplicate key update price='$price'");
  */            
}
} // else if false

// Now loop through the object from iex

foreach($ar as $v) {
  $qt = $v->quote;
  $st = $qt->symbol;

  // Unfortunatly iex has RDS.A and Alpha has RDS.A, as does Wells Fargo. It seems everyone has
  // their own way of specifying this stock.
  
  if($st == "RDS.A") $st = "RDS-A"; // RDS-A is what is in the $spq array
  
  $date = date("Y-m-d H:i:s", $qt->latestUpdate / 1000);
  $price = $qt->latestPrice; // raw price
  $volume = $qt->latestVolume; // volume

  $total = 0;
  
  if($spq[$st] || $spq["$st-BLP"]) {
    //echo $price * $spq[$st] .", $st\n";
    $total = $price * $spq[$st];
    if($spq["$st-BLP"]) {
      //echo $price * $spq["$st-BLP"] . ", $st-BlP\n";
      $total += $price * $spq["$st-BLP"];
    }
  }

  echo "$st, value: $total\n";
  
  /*
  $S->query("insert into stocks.pricedata (date, stock, price, volume) ".
            "values('$date', '$st', '$price', '$volume') ".
            "on duplicate key update price='$price'");
  */
}

echo "updatestocks.php DONE\n";


