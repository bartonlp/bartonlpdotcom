<?php
$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new $_site->className($_site);

function Dot2LongIP($IPaddr) {
  if($IPaddr == "") {
    return 0;
  } else {
    $ips = explode(".", "$IPaddr");
    return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
  }
}

// via file_get_contents('webstats-new.php?list=<iplist>
// Given a list of ip addresses get a list of countries as $ar[$ip] = $name of country.

if($list = $_GET['list']) {
  $list = json_decode($list);
  $ar = array();

  foreach($list as $ip) {
    $iplong = Dot2LongIP($ip);

    $sql = "select countryLONG from $S->masterdb.ipcountry ".
            "where '$iplong' between ipFROM and ipTO";

    $S->query($sql);
    
    list($name) = $S->fetchrow('num');
    
    $ar[$ip] = $name;
  }
  echo json_encode($ar);
  exit();
}

/*
$sql = "select distinct ip from $S->masterdb.tracker where starttime >= current_date()"; 

$S->query($sql);
$tkipar = array(); // tracker ip array

while(list($tkip) = $S->fetchrow('num')) {
  $tkipar[] = $tkip;
}
$tkipar = array_keys(array_flip($tkipar));

$list = json_encode($tkipar);

$options = array('http' => array(
                                 'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                 'method'  => 'POST',
                                 'content' => http_build_query(array('list'=>$list))
                                )
                );

$context  = stream_context_create($options);

$ipc = file_get_contents("http://www.bartonlp.com/getcountryfromip.php", false, $context);
foreach(json_decode($ipc) as $k=>$v) {
  $ipcountry[$k] = $v;
}
*/

$h->title = "get country from ip";
$h->banner = "<h1>Get Country From IP</h1>";
$h->css = <<<EOF
  <style>
input {
  font-size: 1rem;
  padding: .2rem;
}
button {
  font-size: 1rem;
  padding: .2rem;
  border-radius: .5rem;
}
span {
  color: red;
  font-style: italic;
  font-family: "Times New Roman", Times, serif;
}
  </style>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

if($ip = $_POST['ip']) {
  $request = '["'. $ip . '"]';
  $ar = file_get_contents("http://www.bartonlp.com/getcountryfromip.php?list=$request");
  $list = json_decode($ar);
  $list = $list->$ip;
}

echo <<<EOF
$top
<form action='' method='post'>
Enter IP: <input autofocus type='text' name='ip' value='$ip'><br>
<button type='submit'>Submit</button>
</form>
<h2>Country is: <span>$list</span></h2>
<hr>
$footer
EOF;
