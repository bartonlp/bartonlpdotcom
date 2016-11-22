<?php
// This is symlinked in all of the sites.

if(is_null($S)) {
  $_site = require_once(getenv("SITELOAD")."/siteload.php");
  $S = new $_site->className($_site);

  $h->title = "make-webstats";
  
  $page = getwebstats($S);

  list($top, $footer) = $S->getPageTopBottom($h);

  echo <<<EOF
$top
<h1>Standalone</h1>
$page
$footer
EOF;
  exit();
}

$visitors = [];
$jsEnabled = [];
$blpips = [];

return getwebstats($S);

function getwebstats($S) {
  global $visitors, $jsEnabled, $blpips;
  
  $T = new dbTables($S);

  $query = "select myip as 'BLP IP', createtime as Since from $S->masterdb.myip order by createtime desc";

  list($tbl) = $T->maketable($query, array('callback'=>'blpipmake', 'attr'=>array('id'=>'blpid','border'=>"1")));

  if(!$tbl) {
    $tbl = "<h3 class='noNewData'>No New Data Today</h2>";
  }

  $creationDate = date("Y-m-d H:i:s T");

  $page = <<<EOF
<hr/>
</script>

<p id="hourly" class="h-update">The first six tables are created within an hour of this access.<br>
 This was created $creationDate. Hourly update.</p>

<h2>From table <i>blpip</i></h2>
<p>These are the IP Addresses used by the Webmaster. When these addresses appear in the other tables they are in
<span style="color: red">RED</span>.</p>
$tbl
EOF;

  $n = $S->query("select id from $S->masterdb.logagent where site='$S->siteName' and id!=0 and lasttime >= current_date() limit 1");

  $idfield = $n ? ", id as ID" : '';
  
  $query = "select ip as IP, agent as Agent$idfield, count as Count, lasttime as LastTime " .
  "from $S->masterdb.logagent where site='$S->siteName' and lasttime >= current_date() order by lasttime desc";

  list($tbl) = $T->maketable($query,
                             array('callback'=>'blpip',
                                   'attr'=>array('id'=>"logagent", 'border'=>"1")));
  if(!$tbl) {
    $tbl = "<h3 class='noNewData'>No New Data Today</h2>";
  }

  $page .= <<<EOF
<h2 id="table3">From table <i>logagent</i> for today</h2>
<a href="#table4">Next</a>
$tbl
EOF;

  // Here 'count' is total number of hits so count-realcnt is the number of Bots.
  
  $query = "select filename as Page, realcnt as 'Real', (count-realcnt) as 'Bots', lasttime as LastTime from $S->masterdb.counter ".
  "where site='$S->siteName' and lasttime >= current_date() order by lasttime desc";

  list($tbl) = $T->maketable($query, array('attr'=>array('border'=>'1', 'id'=>'counter')));

  if(!$tbl) {
    $tbl = "<h3 class='noNewData'>No New Data Today</h2>";
  }

  $page .= <<<EOF
<h2 id="table4">From table <i>counter</i> for today</h2>
<a href="#table5">Next</a>
<p>Shows the total number of hits for a page.<br>
'Real' is the total number of non-robots hits. 'Bots' is the number of robots hits.</p>
$tbl
EOF;

  $today = date("Y-m-d");

  // are there any members during this day
  
  $query = "select members from $S->masterdb.counter2 where site='$S->siteName' and members!=0 and lasttime >= current_date() limit 1";
  $memberquery = $S->query($query) ? "members as Members," : '';

  // 'count' is actually the number of 'Real' vs 'Bots'. A true 'count' would be Real + Bots.
  
  $query = "select filename as Page, count as 'Real',$memberquery bots as Bots, lasttime as LastTime ".
           "from $S->masterdb.counter2 ".
           "where site='$S->siteName' and lasttime >= current_date() order by lasttime desc";
  
  list($tbl) = $T->maketable($query, array('attr'=>array('border'=>'1', 'id'=>'counter2')));

  if(!$tbl) {
    $tbl = "<h3 class='noNewData'>No New Data Today</h2>";
  }

  $page .= <<<EOF
<h2 id="table5">From table <i>counter2</i> for today</h2>
<a href="#table6">Next</a>
<p>Shows the number of hits today for each page.<br>
$tbl
EOF;

  // Get the footer line
  
  $query = "select sum(`real`+bots) as Count, sum(`real`) as 'Real', sum(bots) as 'Bots', ".
           "sum(members) as 'Members', sum(visits) as Visits " .
           "from $S->masterdb.daycounts ".
           "where site='$S->siteName' and lasttime >= current_date() - interval 6 day";

  $S->query($query);
  list($Count, $Real, $Bots, $Members, $Visits) = $S->fetchrow('num');

  // Use 'tracker' to get the number of Visitors ie unique ip accesses.
  
  $S->query("select ip, date(lasttime) ".
            "from $S->masterdb.tracker where lasttime>=current_date() - interval 6 day and site='$S->siteName' ".
            "order by date(lasttime)");

  $Visitors = 0;

  // There should be ONE UNIQUE ip in the rows. So count them into the date.

  $tmp = '';
  
  while(list($ip, $date) = $S->fetchrow('num')) {
    $tmp[$date][$ip] = '';
  }

  foreach($tmp as $d=>$v) { 
    $visitors[$d] = $n = count($v);
    $Visitors += $n;
  }
  
  if($Members) {
    $memberfooter = "<th>$Members</th>";
    $memberquery = ", members";
  } else {
    $memberfooter = '';
    $memberquery = '';
  }

  // I mask 0x201c out which means 1) no robots, 2) noscript, 3)no normal, 4) no script. We use
  // timer, all of tracker and beacon, and start and load.

  $ips = implode(',', array_keys($blpips));
  
  $sql = "select count(*), date(starttime) from $S->masterdb.tracker ".
         "where date(starttime)>=current_date() - interval 6 day and site='$S->siteName' and ".
         "isJavaScript & ~(0x201c) and ip not in('$ips') group by date(starttime)  order by date(starttime)";
  
  $S->query($sql);

  $jsenabled = 0;
  
  while(list($cnt, $date) = $S->fetchrow('num')) {
    //echo "$cnt, $date<br>";
    $jsEnabled[$date] += $cnt;
    $jsenabled += $cnt;
  }

  $ftr = "<tr><th>Totals</th><th>$Visitors</th><th>$Count</th><th>$Real</th>".
         "<th>$jsenabled</th><th>$Bots</th>$memberfooter<th>$Visits</th></tr>";

  // Get the table lines
  
  $query = "select date as Date, 'visitors' as Visitors, `real`+bots as Count, `real` as 'Real', 'AJAX', ".
           "bots as 'Bots'$memberquery, visits as Visits ".
           "from $S->masterdb.daycounts where site='$S->siteName' and ".
           "lasttime >= current_date() - interval 6 day order by date desc";

  function visit(&$row, &$rowdesc) {
    global $visitors, $jsEnabled;

    $row['Visitors'] = $visitors[$row['Date']];
    $row['AJAX'] = $jsEnabled[$row['Date']];
    return false;
  }
  
  list($tbl) = $T->maketable($query, array('callback'=>'visit', 'footer'=>$ftr, 'attr'=>array('border'=>"1", 'id'=>"daycount")));

  if(!$tbl) {
    $tbl = "<h3 class='noNewData'>No New Data Today</h2>";
  }

  if(is_array($S->daycountwhat)) {
    $counting = implode(", ", $S->daycountwhat);
  } else {
    $counting = $S->daycountwhat ? $S->daycountwhat : 'All files';
  }
  if(strtolower($counting) == 'all') {
    $counting = "All files";
  }

  $next = $S->memberTable ? "#table7a" : "#table7";
    
  $page .= <<<EOF
<h2 id="table6">From table <i>daycount</i> for seven days</h2>
<p>'Visitors' is the number of distinct IP addresses (via 'tracker' table).<br>
'Count' is the sum of 'Real' and 'Bots', the total number of HITS.<br>
'Real' is the number of non-robots.<br>
'AJAX' is the number of non-robots with AJAX functioning (via 'tracker' table).<br>
'Bots' is the number of robots.<br>
'Visits' are hits outside of a 10 minutes interval.<br>
So if you come to the site from two different IP addresses you would be two 'Visitors'.<br>
If you hit our site 10 times the sum of 'Real' and 'Bots' would be 10.<br>
If you hit our site 5 time within 10 minutes you will have only one 'Visits'.<br>
If you hit our site again after 10 minutes you would have two 'Visits'.</p>
<a href="$next">Next</a>
<p>Counting $counting.</p>
$tbl
EOF;

  if($S->memberTable) {
    $query = "select * from memberpagecnt where lasttime >= current_date() - interval 7 day";
    list($tbl) = $T->maketable($query, array('attr'=>array('border'=>"1", 'id'=>"memberpagecnt")));

    $page .= <<<EOF
<h2 id="table7a">Table <i>memberpagecnt</i> for seven days</h2>
<a href="#table7">Next</a>
$tbl
EOF;
  }

  $page .= "<p class='h-update'>End Hourly update.</p>";

  // Write the file out

  file_put_contents("webstats.i.txt", $page);

  return $page;
}

// Call back functions

// fill the $blpips array with the ip numbers

function blpipmake(&$row, &$rowdesc) {
  global $blpips;
  $blpips[$row['BLP IP']] = 1;

  return false;
}

// If the ip address is in the $blpips array make the ip row say BARTON in red.

function blpip(&$row, &$rowdesc) {
  global $blpips;
  
  if($blpips[$row['IP']]) {
    $row['IP'] = "<span class='blp-row'>{$row['IP']}</span>";
  }

  $row['Agent'] = escapeltgt($row['Agent']);

  return false;
}

