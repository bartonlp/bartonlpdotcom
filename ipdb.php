<?php

/*
CREATE TABLE `country_blocks` (
  `ip_start_str` varchar(20) DEFAULT NULL,
  `ip_end_str` varchar(20) DEFAULT NULL,
  `ip_start` bigint(20) NOT NULL DEFAULT '0',
  `ip_end` bigint(20) DEFAULT NULL,
  `country_code` varchar(4) DEFAULT NULL,
  `country_name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`ip_start`),
  KEY `cc_idx` (`country_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `city_blocks` (
  `ip_start` bigint(20) NOT NULL DEFAULT '0',
  `ip_end` bigint(20) DEFAULT NULL,
  `loc_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ip_start`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `city_location` (
  `loc_id` int(11) NOT NULL DEFAULT '0',
  `country_code` varchar(4) DEFAULT NULL,
  `region_code` varchar(4) DEFAULT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `metro_code` varchar(10) DEFAULT NULL,
  `area_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `region_names` (
  `country_code` varchar(4) DEFAULT NULL,
  `region_code` varchar(4) DEFAULT NULL,
  `region_name` varchar(100) DEFAULT NULL,
  UNIQUE KEY `country_code` (`country_code`,`region_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/

require_once("/var/www/includes/siteautoload.class.php");
if($ipstr = $_GET['ip']) {
  $S = new Database($dbinfo);

  $country_blocks = "ip str: $ipstr<br>";
  $iplong = Dot2LongIP($ipstr);
  $country_blocks .= "ip long: $iplong<br>";

  $sql = "select * from country_blocks where '$iplong' <= ip_end order by ip_start limit 1";
  $S->query($sql);
  $row =  $S->fetchrow('assoc');
  $country_blocks .= print_r($row, true);

  $sql = "select * from city_blocks where $iplong <= ip_end order by ip_start limit 1";
  $S->query($sql);
  $row =  $S->fetchrow('assoc');
  $city_blocks = print_r($row, true);

  $sql = "select * from city_location where loc_id = {$row['loc_id']}";
  $S->query($sql);
  $row =  $S->fetchrow('assoc');
  $city_location = print_r($row, true);

  $sql = "select * from region_names where country_code = '{$row['country_code']}' ".
         "and region_code = '{$row['region_code']}'";
  $S->query($sql);
  $row =  $S->fetchrow('assoc');
  $region_names = print_r($row, true);

  echo "$country_blocks\n$city_blocks\n$city_location\n$region_names";
  exit();
}

$S = new Blp; // takes an array if you want to change defaults

$h->banner = "<h1>IPDB test</h1>";

$h->extra = <<<EOF
<script>
jQuery(document).ready(function($) {
  $("form").click(function(e) {
    var ip = $("input", this).val();
    console.log("ip: %s", ip);
    $.get("http://bartonlp.com/html/webstats-new.php?ip="+ip, function(dd) {
      $("#info").text(dd);
    });
    $.get("ipdb.php?ip="+ip, function(data) {
      $("#biginfo").html(data);
    });
    return false;
  });
});
</script>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);
echo <<<EOF
$top
<form>
<p>Enter Ip Address to check</p>
<input type="text" id="ipaddress" autofocus/>
<button>Submit</button>
</form>
<p id="info"></p>
<pre>
<div id="biginfo"></div>
</pre>
$footer
EOF;

function Dot2LongIP($IPaddr) {
  if($IPaddr == "") {
    return 0;
 } else {
   $ips = explode(".", "$IPaddr");
   return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
 }
}

