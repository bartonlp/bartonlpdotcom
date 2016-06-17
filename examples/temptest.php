<?php
if($_GET['page'] == 'list') {
  function Dot2LongIP($IPaddr) {
    if($IPaddr == "") {
      return 0;
    } else {
      $ips = explode(".", "$IPaddr");
      return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
    }
  }

  $_site = require_once("/var/www/includes/siteautoload.class.php");
    
  $S = new Database($_site['dbinfo']);
  $S->query("select ip from barton.logagent limit 20");
  while(list($ip) = $S->fetchrow('num')) {
    $list[] = $ip;
  }

  $ar = array();

  foreach($list as $ip) {
    $iplong = Dot2LongIP($ip);

    $sql = "select countryLONG from barton.ipcountry ".
            "where '$iplong' between ipFROM and ipTO";

    $S->query($sql);
    
    list($name) = $S->fetchrow('num');
    
    $ar[$ip] = $name;
  }
  
  echo json_encode($ar);
  exit();
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>JSON Transform</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.js"></script>
    <script type="text/javascript" src="js/underscore.js"></script>
    <script type="text/template" id="tpl-html">
        <div class="well">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Country</th>
                </tr>
                </thead>
                <tbody>
                <% _.each( target, function(i, k, l) { %>
                    <tr>
                        <td><%= k %></td>
                        <td><%= i %></td>
                    </tr>
                <% }); %>
                </tbody>
            </table>
        </div>
    </script>
    <script type="text/template" id="tpl-xml">
        <data>
        <% _.each( target, function(i, k) {%>
            <entry>
                <ip><%= k %></ip>
                <country><%= i %></country>
            </entry>
        <% }); %>
        </data>
    </script>

    <script>
      
      function generateHTML() {
        $.get("temptest.php?page=list", function(data) {
          var data = { target: JSON.parse(data) };
          var template = _.template( $("#tpl-html").text() );
          $("#output").html( template(data) );
        });
      }

      function generateXML() {
        $.get("temptest.php?page=list", {page: 'list'}, function(data) {
          var data = { target: JSON.parse(data) };
          var template = _.template( $("#tpl-xml").text() );
          var xml = template(data);
          $("#output").html( "<pre>" + _.escape( xml ) + "</pre>" );
        });
      }
    </script>
</head>
<body style="padding:50px 10px ">

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a href="javascript:generateHTML()" class="btn" id="html">Generate HTML</a>
                <a href="javascript:generateXML()" class="btn" id="xml">Generate XML</a>
            </div>
        </div>
    </div>

    <div id="output">Click a button above to transform the raw JS data.</div>
</body>
</html>