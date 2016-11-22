<?php
// BLP 2016-09-03 -- change ftp password to 7098653?
// BLP 2016-05-06 -- use logagent and logagent2 instead of analysis and analysis2. Restrict the
// number of entries to 30,000 of the latest entries.  
// All sites have the same analysis.php as simlinks
// BLP 2016-04-02 -- we should really have two tables. One that accumulates everything and one that
// accumulates 60 days and after 60 days deletes the oldest. As it is now when I add an entry if
// the ip agent is old the old entry is updated. With two tables I would only update entries within
// 60 days and drop old entries.

$_site = require_once(getenv("SITELOAD")."/siteload.php");

// Ajax from CRON job /var/www/bartonlp/scrits/update-analysis.sh

if($thisSite = $_GET['siteupdate']) {
  $S = new $_site->className($_site);
  getAnalysis($S, $thisSite);
  exit();
}

// Ajax from webstats-new.js

if($thisSite = $_GET['site']) {
  $analysis = file_get_contents("http://bartonphillips.net/analysis/$thisSite-analysis.i.txt");

  echo $analysis;
  exit();
}

// POST from this file when standalone.

if(isset($_POST['submit']) || !$S) {
  $S = new $_site->className($_site);

  $h->title = "Analysis";
  
  $h->link = <<<EOF
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<!--[if lte IE 8]>
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
<!--<![endif]-->
  <link rel="stylesheet" href="http://bartonphillips.net/css/tablesorter.css">
EOF;

  $h->extra = <<<EOF
  <script src="http://bartonphillips.net/js/tablesorter/jquery.tablesorter.js"></script>
  <script>
jQuery(document).ready(function($) {
  $.tablesorter.addParser({
    id: 'strnum',
    is: function(s) {
          return false;
    },
    format: function(s) {
          s = s.replace(/,/g, "");
          return parseInt(s, 10);
    },
    type: 'numeric'
  });

  $("#os1, #os2, #browser1, #browser2")
    .tablesorter({ headers: { 1: {sorter: 'strnum'}, 2: {sorter: false}, 3: {sorter: false}}, sortList: [[1,1]]})
    .addClass('tablesorter');
});
  </script>
EOF;

  $h->css = <<<EOF
  <style>
body {
  margin: 1rem;
}
button {
  font-size: 1rem;
  border-radius: .5rem;
}
  </style>
EOF;

  list($top, $footer) = $S->getPageTopBottom($h);

  $site = empty($_POST['site']) ? 'ALL' : $_POST['site'];

  $analysis = file_get_contents("http://bartonphillipsnet/analysis/$site-analysis.i.txt");

  echo <<<EOF
$top
$analysis
<hr>
$footer
EOF;
  exit();
}

return getAnalysis($S);

// Helper function to make the tables

function maketable($sql, $S) {
  $total = array();
  $counts = array();

  $n = $S->query($sql);

  $pat1 = "~https?://|python|java|wget|nutch|perl|libwww|lwp-trivial|curl|php/|urllib|".
         "gt::www|snoopy|mfc_tear_sample|http::lite|phpcrawl|uri::fetch|zend_http_client|".
         "http client|pecl::http|blackberry|windows|android|ipad|iphone|darwin|macintosh|x11|linux|bsd|cros|msie~i";

  while(list($agent, $count) = $S->fetchrow('num')) {
    if(preg_match_all($pat1, $agent, $m)) {
      $mm = array_map('strtolower', $m[0]);
      $val = '';
      
      if(array_intersect(array("http://","https://","python","java","wget","nutch","perl","libwww",
                               "lwp-trivial","curl","php/","urllib","gt::www","snoopy","mfc_tear_sample",
                               "http::lite","phpcrawl","uri::fetch","zend_http_client",
                               "http client","pecl::http"),
                         $mm))
      {
        $val = 'ROBOT';
        $total['os'][1] += $count;
      } elseif(array_intersect(array('blackberry'), $mm)) {
        $val = 'BlackBerry';
      } elseif(array_intersect(array('darwin'), $mm)) {
        $val = 'Darwin';
      } elseif(array_intersect(array('android'), $mm)) {
        $val = 'Android';
      } elseif(array_intersect(array('windows','msie'), $mm)) {
        $val = 'Windows';
      } elseif(array_intersect(array('ipad'), $mm)) {
        $val = 'iPad';
      } elseif(array_intersect(array('iphone'), $mm)) {
        $val = 'iPhone';
      } elseif(array_intersect(array('macintosh'), $mm)) {
        $val = 'Macintosh';
      } elseif(array_intersect(array('cros'), $mm)) {
        $val = 'CrOS';
      } elseif(array_intersect(['x11','linux','bsd'], $mm)) {
        $val = 'Unix/Linux/BSD';
      }
      $counts['os'][$val] += $count;
    } else {
      //echo "Other, $count: $agent<br>";
      $counts['os']['Other'] += $count;
    }
    $total['os'][0] += $count;

    // Now browsers

    $pat2 = "~https?://|python|java|wget|nutch|perl|libwww|lwp-trivial|curl|php/|urllib|".
           "gt::www|snoopy|mfc_tear_sample|http::lite|phpcrawl|uri::fetch|zend_http_client|".
           "http client|pecl::http|".
           "firefox|chrome|safari|trident|msie| edge/|opera|konqueror~i";

    if(preg_match_all($pat2, $agent, $m)) {
      $mm = array_map('strtolower', $m[0]);

      if(array_intersect(array("http://","https://","python","java","wget","nutch","perl","libwww",
                               "lwp-trivial","curl","php/","urllib","gt::www","snoopy","mfc_tear_sample",
                               "http::lite","phpcrawl","uri::fetch","zend_http_client",
                               "http client","pecl::http"),
                         $mm))
      {
        $counts['browser']['ROBOT'] += $count;
        $total['browser'][0] += $count;
        $total['browser'][1] += $count;
        continue;
      }

      // NOTE the order of these tests. Check for 'opera' first then the "MsIe" variants then
      // 'chrome' and then the rest.
      
      if(array_intersect(['opera'], $mm)) {
        $name = 'Opera';
      } elseif(array_intersect([' edge/'], $mm)) {
        $name = 'MS-Edge';
      } elseif(array_intersect(['trident','msie'], $mm)) {
        $name = 'MsIe';
      } elseif(array_intersect(['chrome'], $mm)) {
        $name = 'Chrome';
      } elseif(array_intersect(['safari'], $mm)) {
        $name = 'Safari';
      } elseif(array_intersect(['firefox'], $mm)) {
        $name = 'Firefox';
      } elseif(array_intersect(['konqueror'], $mm)) {
        $name = 'Konqueror';
      } 
      $counts['browser'][$name] += $count;
    } else {
      $counts['browser']['Other'] += $count;
    }
    $total['browser'][0] += $count;
  }

  return array($total, $counts, $n);
}

// Main function to get analysis

function getAnalysis($S, $site='ALL') {
  $rows = [];
  $cnt = 0;
  $cnt2 = 0;

  $S->query("select myip from $S->masterdb.myip");

  $ips = '';
  
  while(list($blpip) = $S->fetchrow('num')) {
    $ips .= "$blpip,";
  }
  $ips = rtrim($ips, ',');
      
  $where1 = $for = '';

  if($site && $site != 'ALL') {
    $where1 = " and site='$site'";
    $for = " for $site";
  }

  $S->query("select created from $S->masterdb.logagent where ip not in ('$ips')$where1 order by created limit 1");

  list($startDate) = $S->fetchrow('num');

  $sql = "select agent, count from $S->masterdb.logagent where ip not in('$ips')$where1";
  
  list($totals, $counts, $n[0]) = maketable($sql, $S);
  
  $days = 60;

  $S->query("select created from $S->masterdb.logagent2 ".
            "where created >= current_date() - interval $days day and ip not in ('$ips')$where1 order by created limit 1");
  
  list($sinceDate) = $S->fetchrow('num');

  $sql = "select agent, count from $S->masterdb.logagent2 ".
         "where created >= current_date() - interval $days day and ip not in ('$ips')$where1";

  list($totals2, $counts2, $n[1]) = maketable($sql, $S);
  
  $os = [];
  $browser = [];

  for($i=1; $i<3; ++$i) {
    foreach(array('os','browser') as $v) {
      $V = ucwords($v);
      ${$v}[$i-1] = <<<EOF
<table id='$v$i' class='pure-table pure-table-bordered pure-table-striped'>
<thead>
<tr><th>$V</th><th>Count</th><th>%</th><th>% less Bots</th></tr>  
</thead>  
<tbody>  
EOF;
    }
  }

  foreach($counts as $k=>$v) {
    foreach($v as $kk=>$vv) {
      $percent = number_format($vv/$totals[$k][0]*100, 2);
      $percent2 = number_format($vv/($totals[$k][0] - $totals[$k][1])*100, 2);
      $vv = number_format($vv, 0);
      if($kk == "ROBOT") {
        $percent2 = '';
      }
      ${$k}[0] .= "<tr><td>{$kk}</td><td>{$vv}</td><td>$percent</td><td>$percent2</td></tr>";
    }
  }

  foreach($counts2 as $k=>$v) {
    foreach($v as $kk=>$vv) {
      $percent = number_format($vv/$totals2[$k][0]*100, 2);
      $percent2 = number_format($vv/($totals2[$k][0] - $totals2[$k][1])*100, 2);
      $vv = number_format($vv, 0);
      if($kk == "ROBOT") {
        $percent2 = '';
      }
      ${$k}[1] .= "<tr><td>{$kk}</td><td>{$vv}</td><td>$percent</td><td>$percent2</td></tr>";
    }
  }

  $os[0] .= "</tbody></table>";
  $os[1] .= "</tbody></table>";
  $browser[0] .= "</tbody></table>";
  $browser[1] .= "</tbody></table>"; 

  $creationDate = date("Y-m-d H:i:s T");

  // Make this function into a string so we can use it in the echo within {}
  $number_format = 'number_format';

  $analysis = <<<EOF
<style>
.pure-table-striped tr:nth-child(2n-1) td {
  background-color: inherit;
}
.pure-table-striped tbody {
  background-color: #e0e0e0;
}
.pure-table-striped tbody tr:nth-child(2n+2) td {
  background-color: pink;
}
.pure-table thead {
  background-color: yellow;
}
#os1, #os2, #browser1, #browser2 {
  font-size: 1rem;
}
#os1 td:nth-child(2), #browser1 td:nth-child(2),
#os2 td:nth-child(2), #browser2 td:nth-child(2),
#os1 td:nth-child(3), #browser1 td:nth-child(3),
#os2 td:nth-child(3), #browser2 td:nth-child(3),
#os1 td:nth-child(4), #browser1 td:nth-child(4),
#os2 td:nth-child(4), #browser2 td:nth-child(4) {
  text-align: right;
}
.AlignTop {
  vertical-align: top;
}
.pure-table thead {
  vertical-align: middle;
}
.HeaderRow th {
  padding: 20px 10px;
}
table.tablesorter thead tr .header {
 background-size: 1.5rem;
}
</style>
<h2 id="analysis-info">Analysis Information$for</h2>
<p class="h-update">Last updated $creationDate.</p>

<p>The following websites are used to accumulate data</p>
<ul>
<li>www.bartonphillips.com</li>
<li>www.bartonlp.com</li>
<li>www.granbyrotary.org</li>
<li>www.allnaturalcleaningcompany.com</li>
<li>www.mountainmessiah.com</li>
<li>www.applitec.com</li>
</ul>

<div id="siteanalysis">
  <form method="post" action="analysis.php">
    <p>Showing $site</p>
    Get Site: 
    <select name='site'>
      <option>Applitec</option>
      <option>Allnatural</option>
      <option>Bartonlp</option>
      <option>BartonlpOrg</option>
      <option>Bartonphillips</option>
      <option>GranbyRotary</option>
      <option>Messiah</option>
      <option>Puppiesnmore</option>
      <option>Weewx</option>
      <option>ALL</option>
    </select>

    <button id="mysite" type="submit">Submit</button>
  </form>
</div>

<p>These tables show the number and percentage of Operating Systems and Browsers.<br>
The Totals show the number of Records and Counts for the entire table and the last N days.<br>
The OS and Browser totals should be the same. <br>
The two sets of tables give you an idea
of how the market is trending.</p>

<table id="CompareTbl">
<thead>
  <tr>
    <th>
      Total Records: {$number_format($n[0])}<br>
      From: $startDate<br>
      Total Count: {$number_format($totals['os'][0])}
    </th>
    <th>
      Total Records: {$number_format($n[1])}<br>
      First Record: $sinceDate<br>
      Total Count: {$number_format($totals2['browser'][0])}
    </th>
  </tr>
</thead>
<tbody>
  <!-- OS rows -->
  <tr class="HeaderRow"><th>OS All</th><th>OS Last $days Days</th></tr>
  <tr>
    <td class="AlignTop">
      <div class="pure-g">
        <div class="pure-u-1 pure-u-md-1-2">
$os[0]
        </div>
      </div>
    </td>

    <td class="AlignTop">
      <div class="pure-g">
        <div class="pure-u-1 pure-u-md-1-2">
$os[1]
        </div>
      </div>
    </td>
  </tr>
  <!-- Browser rows -->
  <tr class="HeaderRow"><th>Browser All</th><th>Browser Last $days Days</th></tr>
  <tr>
    <td class="AlignTop">
      <div class="pure-g">
        <div class="pure-u-1 pure-u-md-1-2">
$browser[0]
        </div>
      </div>
    </td>

    <td class="AlignTop">
      <div class="pure-g">
        <div class="pure-u-1 pure-u-md-1-2">
$browser[1]
        </div>
      </div>
    </td>
  </tr>
</tbody>
</table>
EOF;

  if(ftp_file_put_contents("/var/www/bartonphillipsnet/analysis/$site-analysis.i.txt", $analysis) === false) {
    error_log("analysis: ftp_file_put_contents FAILED on $site-analysis.i.txt");
  }
  return $analysis;
}

// do file_put_contents() via ftp

function ftp_file_put_contents($remote_file, $file_string) {
// FTP login
  $ftp_server="bartonphillips.net"; 
  $ftp_user_name="barton"; 
  $ftp_user_pass="7098653?";

  // Create temporary file
  $local_file = fopen('php://temp', 'r+');
  fwrite($local_file, $file_string);
  rewind($local_file);       

  // Create FTP connection
  $ftp_conn=ftp_connect($ftp_server); 

  // FTP login
  @$login_result = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass); 

  // FTP upload
  if($login_result) $upload_result=ftp_fput($ftp_conn, $remote_file, $local_file, FTP_ASCII);

  // Error handling
  if(!$login_result || !$upload_result) {
    echo('FTP error: The file could not be written on the remote server.');
  }

  // Close FTP connection
  ftp_close($ftp_conn);

  // Close file handle
  fclose($local_file);
}
