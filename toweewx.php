<?php
// Weather Station weewx
// Send to either normal page of smartphone page
/*
This is in the bartonphillips.com database not in barton.
CREATE TABLE `detect` (
  `ip` varchar(20) NOT NULL default '',
  `agent` varchar(255) NOT NULL default '',
  `type` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ip`,`agent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

$_site = require_once(getenv("SITELOAD")."/siteload.php");
$S = new $_site->className($_site);

if($S->isBot) {
  header("location: http://www.bartonphillips.com/weewx/index.php");
}

require_once("includes/Mobile_Detect.php");

class myDetect extends Mobile_Detect {
  protected $otherOss = array(/*'Linux X11 64' => "Linux x86_64",
                              'Linux X11' => "Linux x86",
                              'Windows XP' => "Windows NT 5\.1",
                              'Windows Vista' => "Windows NT 6\.0",
                              'Windows 7' => "Windows NT 6\.1",
                              'Windows 8' => "Windows NT 6\.2",*/
                              'Windows' => "Windows",
                              'Macintosh' => "Macintosh",
                              'UNIX/Linux/BSD' => 'X11'
                             );

  protected $allOs = array();
  
  public function getOs($userAgent = null) {
    $userAgent = $userAgent ? $userAgent : $this->userAgent;
    $this->allOs = array_merge($this->otherOss, self::$operatingSystems);

    foreach($this->allOs as $k => $v) {
      if(empty($v)){ continue; }
      
      if($this->match($v, $userAgent)) {
        //echo "k: $k<br>";
        return $k;
      }
    }
    return "OS NOT FOUND";
  }
}

// </endclass

$ip = $_SERVER['REMOTE_ADDR'];
$agent = $S->escape($_SERVER['HTTP_USER_AGENT']);

$query = "";

$detect = new myDetect;
//$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
//echo "Device Type: $deviceType<br>";

$os = $detect->getOs();
$type = '';

if($detect->isMobile()) {
  $type .= "Mobile";
  if($detect->isTablet()) {
    $type .= ",Tablet";
  }
} else {
  $desktop = true;
  $type .= "Desktop";
}
$type = "$os>>$type";
//echo "TYPE: $type<br>";
//echo "Agent: $agent<br>";

$query = "select * from bartonphillipsdotcom.detect where ip='$ip' and agent='$agent'";

$n = $S->query($query);

if($n) {
  $row = $S->fetchrow('assoc');

  $rtype = $row['type'];
  $extra = "";
  //echo "rtype: $rtype<br>";
  
  if(preg_match("~(^.*?)( ::.*)*$~i", $rtype, $m)) {
    $rtype = $m[1];
    $extra = $m[2];
    //echo "rtype: $rtype, extra: $extra<br>";
  } 

  //echo "rtype: $rtype==type: $type<br>";

  if(($rtype == $type)) {
    $query = "update bartonphillipsdotcom.detect set timestamp=now() where ip='$ip' && agent='$agent'";
  } else {
    $type .= " :: older({$rtype}{$extra})";
    $query = "update bartonphillipsdotcom.detect set type='$type', timestamp=now() where ip='$ip' && agent='$agent'";
  }
  //echo "query: $query<br>";

  $S->query($query);
} else {
  $query = "insert into bartonphillipsdotcom.detect (ip, agent, type) values('$ip', '$agent', '$type')";

  //echo "query: $query<br>";

  $S->query($query);
}

// Is it Desktop or Mobile?

if($desktop) {
  header("location: http://www.bartonphillips.com/weewx/index.php");
} else {
  header("location: http://www.bartonphillips.com/weewx/smartphone/index.html");
}
