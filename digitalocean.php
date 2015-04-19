<?php
// BLP 2014-11-03 -- Example of DigitalOceans API v2
// We ask for a list of DNS zones for domains

require_once("/var/www/includes/siteautoload.class.php");
$S = new Blp; // takes an array if you want to change defaults

$h->extra =<<<'EOF'
<script src="http://bartonlp.com/html/js/jquery.js"></script>
<script>
jQuery(document).ready(function($) {
  $.ajax({
    url: "https://api.digitalocean.com/v2/droplets",
    headers: {Authorization:
           "Bearer 7e963502499f6bc3b4b8248bc785c3b073261be646742780c9606666281a8853"},
    dataType: 'json',
    success: function(data) {
      console.log("droplet data: ", data);
      var list = '';

      for(var i=0; i < data.droplets.length; ++i) {
        for(k in data.droplets[0]) {
          list += k+": "+data.droplets[i][k]+"\n";
        }
        list += "\n";
      }

      $("h1").after("<div id='droplet'><pre>"+list+"</pre></div>");

      $.ajax({
        url: "https://api.digitalocean.com/v2/domains",
        headers: {Authorization:
               "Bearer 7e963502499f6bc3b4b8248bc785c3b073261be646742780c9606666281a8853"},
        dataType: 'json',
        success: function(data) {
          console.log("domain data: ", data);
          var list = '';
          for(var i=0; i < data.domains.length; ++i) {
            list += data.domains[i].zone_file + "\n\n";
          }
          
          $("#droplet").after("<div><pre>"+list+"</pre></div>");
        },
        error: function(err) {
          console.log("error: %o", err);
        }
      });
    },
    error: function(err) {
      console.log("error: %o", err);
    }
  });  
});
</script>
EOF;

$h->banner = "<h1>TEST</h1>";

list($top, $footer) = $S->getPageTopBottom($h);
echo <<<EOF
$top
$footer
EOF;

// List all droplets
//curl -X GET "https://api.digitalocean.com/v2/droplets" \
//	-H "Authorization: Bearer $TOKEN"
// List all domains
//curl -X GET "https://api.digitalocean.com/v2/domains" \
//	-H "Authorization: Bearer $TOKEN"
