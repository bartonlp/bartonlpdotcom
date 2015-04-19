<?php
// BLP 2015-02-27 -- This is a universal webstat

require_once("/var/www/includes/siteautoload.class.php");
$memberTable = $siteinfo['memberTable'];

$referer = $_SERVER['HTTP_REFERER'];
//echo $siteinfo['siteDomain'] . " ref: $referer<br>";
if(0) {
  if(!preg_match($siteinfo['siteDomain'], $referer)) {
    echo <<<EOF
<h1>Access Forbiden</h1>
<p>You must get here via the <a href="/">Home Page</a></p>
EOF;
    exit();
  }
}

Error::setNoEmailErrs(true); // For debugging
Error::setDevelopment(true); // during development

$myIp = gethostbyname($siteinfo['myUri']); // get my home ip address

// *************************************
// Ajax functions
// *************************************

// Ajax updatebots

if($_GET['page'] == 'updatebots') {
  $S = new Database($dbinfo);
  // BLP 2015-02-12 --
  $sql = "insert ignore into barton.bots2 (agent) value('{$_GET['agent']}')";
  $S->query($sql);
  
  $sql = "insert ignore into barton.bots (ip) values('{$_GET['ip']}')";
  $n = $S->query($sql);
  
  echo json_encode(array('n'=>$n, 'sql'=>$sql));
  exit();
}

// This section is the Ajax back end for this page. This is called via $.get()

$tableNames = array();

if($_GET["table"]) {
  $S = new Database($dbinfo);
  $t = new dbTables($S);

  switch($_GET["table"]) {
    case "daycounts":
      // Add daycount
      // Callback to get the number of IPs that were members.
      $memtotal = 0;

      $query = "select count(*) as Visitors, sum(count) as Count, ".
               "sum(visits) as Visits from daycounts ".
               "where lasttime > date_sub(now(), interval 7 day) order by date";

      $S->query($query);

      list($Visitors, $Count, $Visits) = $S->fetchrow('num');

      $S->query("select date from daycounts order by date limit 1");
      list($start) = $S->fetchrow();

      $ftr = "<tr><th>Totals</th><th>$Visitors</th><th>$Count</th>".
             "<th class='memtotal'>&nbsp;</th><th>$Visits</th></tr>";

      $query = "select date as Date, count(*) as Visitors, sum(count) as Count, ".
               "'0' as Members, sum(visits) as Visits ".
               "from daycounts where date > date_sub(now(), interval 7 day) ".
               "group by date order by date desc";

      list($tbl) = $t->maketable($query, array('footer'=>$ftr,
                                               'attr'=>array('border'=>"1", 'id'=>"daycount")
                                              )
                                );


      $tbl = preg_replace("~(<tfoot>.*?<th class='memtotal'>)&nbsp;~sm", "\${1}$memtotal", $tbl);
      echo $tbl;
      break;
    case "counter":
      $sql = "select * from counter where lasttime > date_sub(now(), interval 7 day)";
      list($table) = $t->maketable($sql, array('attr'=>array('id'=>"counter", 'border'=>"1")));
      echo $table;
      break;
    case "counter2":
      $sql = "select * from counter2 where lasttime > date_sub(now(), interval 7 day) ".
             "order by lasttime desc";
      list($table) = $t->maketable($sql, array('attr'=>array('id'=>"counter2", 'border'=>"1")));
      echo $table;
      break;
    case "memberpagecnt":
      $sql = "select * from memberpagecnt where lasttime > date_sub(now(), interval 7 day) " .
             "order by lasttime desc";
      list($table) = $t->maketable($sql, array('attr'=>array('id'=>"memberpagecnt", 'border'=>"1")));
      echo $table;
      break;
    case "ipAgentHits":
      function ipagentcallback(&$row, &$desc) {
        global $S;
        
        $ip = $S->escape($row['IP']);

        $tr = "<tr";

        // escape markup in agent
        $row['Agent'] = escapeltgt($row['Agent']);

        if($row['id']) {
          if($row['id'] == '25') {
            $tr .= " class='blp'";
          } 
        } else {
          $tr .= " class='noId'";
          $n = $S->query("select ip from barton.bots where ip='$ip'");
          if($n) {
            $tr .= " style='color: red'";
          } else {
            // BLP 2014-11-16 -- Look for 'http://' in agent and if found add it to the
            // bots table.
            if(preg_match('~http://~', $row['Agent'])) {
              $sql = "insert ignore into barton.bots value('$ip')";
              $S->query($sql);
              $tr .= " style='color: red'";
            }
          }

        }
        $tr .= ">";

        $desc = preg_replace("/<tr>/", $tr, $desc);
        return false;
      }

      if(!empty($memberTable)) {
        $query = "select l.ip as IP, l.agent as Agent, l.id, ".
                 "concat(r.FName, ' ', r.LName) as Name, " .
                 "l.lasttime as LastTime from logagent as l ".
                 "left join $memberTable as r on l.id=r.id" .
                 " where l.lasttime > date_sub(now(), interval 7 day) ".
                 "order by l.lasttime desc";
      } else {
        $query = "select ip as IP, agent as Agent, id, ".
                 "lasttime as LastTime from logagent ".
                 "where lasttime > date_sub(now(), interval 7 day) ".
                 "order by lasttime desc";
      }

      list($table) = $t->maketable($query, array('callback'=>'ipagentcallback',
        'attr'=>array('id'=>"ipAgentHits", 'border'=>'1')));

      echo $table;
      break;
  }
  exit();
}

// ********************************************************************************
// End of Ajax
// ********************************************************************************

// Start of Main Page logic

$S = new $siteinfo['className'];
$t = new dbTables($S);

$h->extra = <<<EOF
  <link rel="stylesheet" href="css/tablesorter.css">
  <link rel="stylesheet" href="css/hits.css">
  <script src="js/tablesorter/jquery.metadata.js"></script>
  <script src="js/tablesorter/jquery.tablesorter.js"></script>

  <script>
jQuery(document).ready(function($) {
  var tablename="{$_GET['table']}";
  var idName="$jmembers";
  var flags = {webmaster: false, robots: false, ip: false , page: false};

  $("#tracker").tablesorter().addClass('tablesorter');

  // Don't show webmaster

  var myIp = "$myIp";

  // Check Flags look at other flags

  function checkFlags(flag) {
    var msg;

    if(flag) { // Flag is true.
      switch(flag) {
        case 'webmaster': // default is don't show
          $(".webmaster").parent().hide();
          msg = "Show ";
          flags.webmaster = false;
          break;
        case 'robots': // true means we are showing robots
          $('.robots').parent().hide();
          msg = "Show ";
          flags.robots = false;
          break;
        case 'ip': // true means only this ip is showing so we want to make all ips show
          $(".ip").removeClass('ip');
          $("#tracker tr").show();

          if(flags.page) {
            $("#tracker td:first-child:not('.page')").parent().hide();
          }
             
          if(!flags.webmaster) {
            $(".webmaster").parent().hide();
          }
          if(!flags.robots) {
            $(".robots").parent().hide();
          }
          msg = "Only ";
          flags.ip = false;
             
          break;
        case 'page': // true means we are only showing this page
          $(".page").removeClass('page');
          $("#tracker tr").show();
                          
          if(flags.ip) {
            $("#tracker td:nth-child(2):not('.ip')").parent().hide();
          }

          if(!flags.webmaster) {
            $(".webmaster").parent().hide();
          }
          if(!flags.robots) {
            $(".robots").parent().hide();
          }
          msg = "Only ";
          flags.page = false;
          break;
      }
      $("#"+ flag).text(msg + flag);
      calcAv();
      return;
    }   

    for(var f in flags) {
      if(flags[f]) { // if true
        switch(f) {
          case 'webmaster':
            flags.webmaster = false;
            if(true in flags) {
              $(".webmaster").parent().not(":hidden").show();
            } else {
              $(".webmaster").parent().show();
            }
            flags.webmaster = true;
            msg = "Hide ";
            break;
          case 'robots':
            flags.robots = false;
            if(true in flags) {
              $('.robots').parent().not(":hidden").show();
            } else {
              $(".robots").parent().show();
            }
            flags.robots = true;
            msg = "Hide ";
            break;
          case 'ip': 
            $("#tracker tr td:nth-child(2):not('.ip')").parent().hide();
            msg = "All ";
            break;
          case 'page':
            $("#tracker tr td:first-child:not('.page')").parent().hide();
            msg = "All ";
            break;
        }
        $("#"+ f).text(msg + f);
      }   
    }
    calcAv();
  }

  function calcAv() {
    // Calculate the average time spend using the NOT hidden elements
    var av = 0, cnt = 0;
    $("#tracker tbody :not(:hidden) td:nth-child(7)").each(function(i, v) {
      var t = $(this).text();
      if(!t) return true; // Continue

      var ar = t.match(/^(\d{2}):(\d{2}):(\d{2})$/);
      t = parseInt(ar[1], 10) * 3600 + parseInt(ar[2],10) * 60 + parseInt(ar[3],10);
      if(t > 7200) return true; // Continue if over two hours 
 
      //console.log("t: %d", t);
      av += t;
      ++cnt;      
    });

    av = av/cnt; // Average
   
    var hours = Math.floor(av / (3600)); 
   
    var divisor_for_minutes = av % (3600);
    var minutes = Math.floor(divisor_for_minutes / 60);
 
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);

    var tm = hours.pad()+":"+minutes.pad()+":"+seconds.pad();

    console.log("av time: ", tm);
    $("#average").html(tm);
  }

  Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
  }

  // To start Webmaster is hidden

  $("#tracker td:nth-child(2) span.co-ip").each(function(i, v) {
    if($(v).text() == myIp) {
      $(v).parent().addClass("webmaster").css("color", "green").parent().hide();
    }
  });

  calcAv();

  // To start Robots are hidden

  $(".bot td:nth-child(4)").addClass('robots').css("color", "red").parent().hide();
  
  // Put a couple of buttons before the table

  $("#tracker").before("<div id='trackerselectdiv'>"+
                       "<button id='webmaster'>Show webmaster</button>"+
                       "<button id='robots'>Show robots</button>"+
                       "<button id='page'>Only page</button>"+
                       "<button id='ip'>Only ip</button>"+
                       "</div>");

  // ShwoHide Webmaster clicked

  $("#webmaster").click(function(e) {
    if(flags.webmaster) {
      checkFlags('webmaster');
    } else {
      // Show
      flags.webmaster = true;
      // Now show only my IP
      checkFlags();
    }
    //flags.webmaster = !flags.webmaster;
  });

  // Page clicked

  $("#tracker td:first-child").click(function(e) {
    if(flags.page) {
      checkFlags('page');
    } else {
      // show only this page
      flags.page = true;
      var page = $(this).text();
      $("#tracker tr td:first-child").each(function(i, v) {
        if($(v).text() == page) {
          $(v).addClass('page');
        }
      });
      checkFlags();
    }
  });

  // IP address clicked

  $("#tracker td:nth-child(2)").click(function(e) {
    if(flags.ip) {
      checkFlags('ip');
    } else {
      // show only IP
      flags.ip = true;
      var ip = $(this).text();
      $("#tracker tr td:nth-child(2)").each(function(i, v) {
        if($(v).text() == ip) {
          $(v).addClass('ip');
        }
      });
      checkFlags();
    }
  });

  // ShowHideBots clicked

  $("#robots").click(function() {
    if(flags.robots) {
      // hide
      checkFlags('robots');
    } else {
      // show
      flags.robots = true;
      checkFlags();
    }
  });

  $("#ip").click(function() {
    if(flags.ip) {
      // hide
      checkFlags('ip');
    } else {
      // show
      alert("click on the IP address you want to show");
    }
  });

  $("#page").click(function() {
    if(flags.page) {
      // hide
      checkFlags('page');
    } else {
      // show
      alert("click on the page you want to show");
    }
  });

  //************************************************
  // create a div for name popup

  $("body").append("<div id='popup' style='position: absolute; display: none; border: 2px solid black; background-color: #8dbdd8; padding: 5px;'></div>");

  $(".wrapper").on("click", "#memberpagecnt td:nth-child(2)", function(e) {
    var id = $(this).text();
    var pos = $(this).offset();
    var name = idName[id];
    $("#popup").text(name).css({display: 'block', top: pos.top, left: pos.left+50});
  });

  $("h2.table").each(function() {
    $(this).append(" <span class='showhide' style='color: red'>Show Table</span>");
  });

  // attach class tablesorter to all except our counter and nav-bar

  $("table").not($("#hitCountertbl, #nav-bar table")).addClass('tablesorter');

  $("#counter, #counter2, #memberHits, #otherMemberHits, #memberNot, #memberpagecnt").tablesorter();
  $("#lamphost table").tablesorter({ sortList: [[2,1]], headers: { 1: {sorter: "currency"} } } );
  $("#OScnt table").tablesorter({ sortList:[[1,1]] , headers: { 1: {sorter: "currency"}, 2: {sorter: "currency"}}});
  $("#browserCnt table").tablesorter({ sortList:[[1,1]], headers: { 1: {sorter: "currency"}, 2: {sorter: "currency"}} });

  $("div.table").hide();

  if(tablename != "") {
    $("div[name='"+tablename+"']").show();
    $("div[name='"+tablename+"']").prev().children().first().text("Hide Table");
  }

  $(".showhide").css("cursor", "pointer");

  // when the Show/Hide button for the table is pressed we do an Ajax call to get the data and
  // append it to the div the first time. After that we only show/hide the table.

  $(".showhide").click(function() {
    // tgl is not set initially so false.

    $("#popup").hide();

    if(!this.tgl) {
      // Show

      tablename = $(this).parent().next().attr("name"); // global
      var tbl = $("#"+tablename); // The <table>
      var t = $(this); // The span
      var s = t.parent().next(); // <div class="table"

      s.show();
      t.text("Hide Table");

      // if the table has already been instantiated just show it above
      // if not then create the table

      if(!tbl.length) {
        // Make the table from the database via Ajax

        $("body").css("cursor", "wait");
        t.css("cursor", "wait");

        $.get("$S->self", { table: tablename }, function(data) {
          $("div[name='"+tablename+"']").append(data);
          $("table").not("#hitCountertbl").addClass('tablesorter'); // attach class tablesorter to all except our counter

          // Switch for the specific table

          switch(tablename) {
            case "counter":
            case "counter2":
            case "memberpagecnt":
              $("#"+tablename).tablesorter(); 
              break;
            case "pageHits":
              $("#pageHits").tablesorter(); //{ sortList:[[1,1]] }
              break;
            case "ipAgentHits":
              $("#ipAgentHits").tablesorter(); // { sortList:[[4,1]] }

              // Set up the Ip Agent Hits table
              var memberTable = "$memberTable";
              var text = '';

              if(memberTable) {
                 text = "You can toggle the display of only members or all visitors"+
                              "<input type='submit' value='Show/Hide NonMembers' "+
                              "id='hideShowNonMembers' /><br/>"+
                              "You can toggle the display of the ID "+
                              "<input type='submit' value='Show/Hide ID' "+
                              "id='hideShowIdField' /><br/>";
                // Ip Agent Hits.
                // Hide all ids of zero
              
                $(".noId, .botmsg").hide();
              }

              $("#ipAgentHits").before("<p>"+text+
                "Your can toggle the display of the Webmaster "+
                "<input type='submit' value='Show/Hide Webmaster' id='hideShowWebmaster' /> "+
                "<p class='botmsg'><span style='color: red;'>"+
                "Bots from bots table are red</span><br>"+
                "You can toggle the dispaly of Bots "+
                "<input type='submit' value='Show/Hide Bots' id='hidebots' /></p>");

              // Button to toggle bots show/hide

              $("#hidebots").click(function() {
                if(!this.flag) {
                  // show
                  $("#ipAgentHits tr[style^='color: red']").show();
                } else {
                  // hide
                  $("#ipAgentHits tr[style^='color: red']").hide();
                }
                this.flag = !this.flag;
              });

              // Button to toggle between all and members only in Ip Agent Hits
              // table

              $("#hideShowNonMembers").click(function() {
                if(!this.flag) {
                  $(".noId").not("[name='blp']").show();
                  $(".botmsg").show();
                  $("#hidebots").prop("flag", true);
                } else {
                  $(".noId").hide();
                  $(".botmsg").hide();
                }
                this.flag = !this.flag;
              });

              // Hide the ID field in Ip Agent Hits table
              $("#ipAgentHits td:nth-child(3)").hide();
              $("#ipAgentHits thead th:nth-child(3)").hide();

              // Button to toggle between no ID and showing ID in Ip Agent Hits
              // table

              $("#hideShowIdField").click(function() {
                if(!this.flag) {
                  $("#ipAgentHits td:nth-child(3)").show();
                  $("#ipAgentHits thead th:nth-child(3)").show();
                } else {
                  $("#ipAgentHits td:nth-child(3)").hide();
                  $("#ipAgentHits thead th:nth-child(3)").hide();
                }
                this.flag = !this.flag;
              });

              // Hide the webmaster (me) in the Ip Agent Hits table
              $(".blp").hide();

              // Button to toggle between hide and show of Webmaster in Ip Agents
              // Hits Table

              $("#hideShowWebmaster").click(function() {
                if(!this.flag) {
                  $(".blp").show();
                } else {
                  $(".blp").hide();
                }
                this.flag = !this.flag
              });
              break;
          } // end of switch

          $("body").css("cursor", "default");
          t.css("cursor", "pointer");
        });
      }
    } else {
      $(this).parent().next().hide();
      $(this).text("Show Table");
    }
    this.tgl = !this.tgl;
  });

  // ipAgents click on agent to add to bots

  $(".wrapper").on("click", "#ipAgentHits td:nth-child(2)" , function(e) {
    var self = $(this);
    var id = $("td:nth-child(3)", self.parent()).text();
    if(id != '0')
      return;

    var agent = self.text();
    var ip = $("td:nth-child(1)" , self.parent()).text();
    console.log("ip: %s, agent: %s", ip, agent);
    $.ajax({url: "webstats.php",
            data: {page: 'updatebots', ip: ip, agent: agent},
            type: "get",
            dataType: "json",
            success: function(data) {
              console.log("OK", data);
              if(data.n) {
                self.html("<span style='color: white; background: red'>"+agent+"</span>");
              }
            },
            error: function(err) {
              console.log("ERR", err);
            }
    });
  });
});
  </script>

  <style>
button {
  -webkit-border-radius: 7px;
  -moz-border-radius: 7px;
  border-radius: 7px;
  font-size: 1.2em;
  margin-bottom: 10px;
}
.country {
  border: 1px solid black;
  padding: 3px;
  background-color: #8dbdd8;
}
#tracker {
  width: 100%;
}
/* page */
#tracker td:first-child:hover {
  cursor: pointer;
}
/* ip */
#tracker td:nth-child(2):hover {
  cursor: pointer;
}
/* Member */
#tracker td:nth-child(3) {
  width: 100px;
}
/* agent, starttime, endtime */
#tracker td:nth-child(5), #tracker td:nth-child(6) {
  width: 5em;
}
/* time */
#tracker td:nth-child(7) {
  width: 3em;
}
/* referrer */
#tracker td:last-child {
  word-break: break-all;
  word-break: break-word; /* for chrome */
  width: 100px;
}
#daycount tbody tr td { /*visitors*/
  text-align: right;
}
#daycount tfoot tr th {
  text-align: right;
}
#daycount tfoot tr th:first-child {
  text-align: center;
}
@media (max-width: 660px) {
  .right {
    float: none;
    width: 306px;
  }
  .left {
    float: none;
    width: 306px;
  }
  .left td:first-child {
    word-wrap: break-word;
    word-break: break-all;
    word-break: break-word;
  }
}
  </style>
EOF;

// cache the table names once

$items = array(
  'counter'=>"<h2 class='table'>Page Count</h2>".
"<div class='table' name='counter'>".
"<p>From the <i>counter</i> table for last 7 days.</p>".
"</div>",
  'counter2'=>"<h2 class='table'>Page Count2</h2>".
"<div class='table' name='counter2'>".
"<p>From the <i>counter2</i> table for last 7 days.</p>".
"</div>",
  'tracker'=>"<h2 class='table'>Page Tracker</h2>".
"<div class='table' name='tracker'>".
"<p>From the <i>tracker</i> table for last 3 days.</p>".
"<p>Average stay time: <span id='average'></span> (times over an hour are discarded.)</p>".
"</div>",
  'memberpagecnt'=>"<h2 class='table'>Member Page Count</h2>".
"<div class='table' name='memberpagecnt'>".
"<p>From the <i>memberpagecnt</i> table for last 7 days.</p>".
"</div>",
  'logagent'=>"<h2 class='table'>IP-AGENT Hits</h2>".
"<div class='table' name='ipAgentHits'>".
"<p>From the <i>logagent</i> table for last 7 days.</p>".
"</div>",
  'daycounts'=>"<h2 class='table'>Day Counts</h2>".
"<div class='table' name='daycounts'>".
"<p>Day Counts do NOT include webmaster visits. ".
"The counts do however include ROBOTS who seem to".
"be much more interested in our web page than humans.<br>".
"Only showing 7 days.</p>".
"<p>Visitors are unique IP Addresses, Count is the total number of accesses to all pages,".
"Visits are accesses seperated by 10 minutes.</p>".
"</div>");

// Create the page

$S->query("select table_name from information_schema.tables ".
          "where table_schema='{$dbinfo['database']}' ".
          "and table_name in('counter','counter2','memberpagecnt','logagent',".
          "'logip','tracker','daycounts')");

$page = '';

while(list($name) = $S->fetchrow('num')) {
  //echo "$name<br>";
  $page .= $items[$name];
}

$h->banner = "<h2>Web Site Statistics</h2><p>All times are Mountain time.</p>";
$h->title = "Web Site Statistics";

list($top, $footer) = $S->getPageTopBottom($h);

$sql = "select ip from tracker where starttime > date_sub(now(), interval 3 day)";

$S->query($sql);
$tkipar = array();

while(list($tkip) = $S->fetchrow('num')) {
  $tkipar[] = $tkip;
}
$list = json_encode($tkipar);
$ipcountry = file_get_contents("http://www.bartonlp.com/webstats-new.php?list=$list");
$ipcountry = (array)json_decode($ipcountry);

function tcallback(&$row, &$desc) {
  global $memberbyip, $members, $S, $ipcountry;

  $ip = $S->escape($row['ip']);

  $co = $ipcountry[$ip];

  $row['ip'] = "<span class='co-ip'>$ip</span><br><div class='country'>$co</div>";
  
  $ref = urldecode($row['referrer']);
  // if google then remove the rest because google doesn't have an info in q= any more.
  if(strpos($ref, 'google') !== false) {
    $ref = preg_replace("~\?.*$~", '', $ref);
  }
  $row['referrer'] = $ref;

  if($memberbyip["$ip"]) {
    $row['Member'] = $members[$memberbyip["$ip"]];
  } else {
    $row['Member'] = '';
    
    if($S->query("select ip from barton.bots where ip='$ip'")) {
      $desc = preg_replace("~<tr>~", "<tr class='bot'>", $desc);
    }
  }
}

$sql = "select page, ip, 'Member', agent, starttime, endtime, difftime as time, referrer ".
       "from tracker where starttime > date_sub(now(), interval 3 day) order by starttime desc";

list($trackertable) = $t->maketable($sql, array('callback'=>tcallback,
                                                'attr'=>array('id'=>"tracker", 'border'=>'1')));
if(empty($trackertable)) {
  $trackertable = "No Data Found";
}

// Render Page
//**********************************************************
// Start of Show/Hide Tables
//**********************************************************

echo <<<EOF
$top
<main>
<!-- pages goes here -->
$page
</main>
$footer
EOF;
