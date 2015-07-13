<?php
// Main page for bartonlp.com
// BLP 2014-12-02 -- remove uptest.php logic as it didn't really work well
// BLP 2014-10-24 -- changed blp flag to 7098
// BLP 2014-09-10 -- This is on the new bartonlp.com at digitalocian.com
// BLP 2014-08-18 -- add click on toweewx to add date so we kill caching. Also
// add blp=8653 as flag for Admin links

require_once("/var/www/includes/siteautoload.class.php");
$S = new Blp; // takes an array if you want to change defaults

/* We can send last-modified if we want. Right now don't
if(!($_GET || $_POST)) {
  header("Last-Modified: ". date("r", getlastmod()));
}
*/

// BLP 2014-09-12 -- This page uses requirejs. If this is NOT present then we get a
// regular script tag with jquery and tracker.
// See includes/head.i.php for more details.

$h->pageScript = "<script data-main='http://bartonlp.com/html/index-app' ".
                 "src='http://bartonlp.com/html/js/require.js'></script>";

// Special Fonts from google.

$h->link = '<link rel="stylesheet" '.
           'href="http://fonts.googleapis.com/css?family=Rancho'.
           '|Lora:400,700,400italic,700italic'.
           '|Jacques+Francois+Shadow&effect=shadow-multiple">';

$h->css = <<<EOF
  <!-- Override some of what is in blp.css to use custome fonts -->
  <style>
body {
  background-color: white;
  font-family: 'Lora', serif;
  font-size: 1.5em;

}
h1 {
  font-family: Rancho, serif;
}
#browser-info {
  border-top: 1px solid gray;
}
#blog {
  width: 50%;
  background-color: #FCF6CF;
  text-align: center;
  padding: 20px;
  margin: auto;
  border: 1px solid #696969;
}
#daycount {
  text-align: center;
  width: 90%;
  margin: auto;
  border: 1px solid black;
  background-color: #ECD087;
  padding-bottom: 20px;
}
#daycount ul {
  width: 80%;
  text-align: left;
  margin: auto;
}
.weather {
  font-family: 'Jacques Francois Shadow', serif;
}
ul {
  font-family: Rancho, serif;
  line-height: 150%;
  font-size: 1.5em;
}
.fontface {
  line-height: 110%;
  font-size: 1.2em;
  margin-top: -20px;
}
.lora {
  font-family: 'Lora', serif;
}
.rancho {
  font-family: Rancho, serif;
}
.jacques {
  font-family: 'Jacques Francois Shadow', serif;
}

#blpimg {
  float: left;
  padding: 5px 10px;
}
@media (max-width: 600px) {
  a {
    font-size: 1.9rem;
    line-height: 2.5rem;
  }
}
  </style>
EOF;

$h->title = "Barton Phillips Experimental Page &#9760;";
$h->banner = "<h1 class='center font-effect-shadow-multiple'>".
             "Barton Phillips Experimental Page</h1>".
             "<h2 class='center weather'>".
             "<a target='_blank' ".
             "href='http://www.bartonphillips.com/toweewx.php'>".
             "My Home Weather Station</a></h2>";

$ref = $_SERVER['HTTP_REFERER'];
if($ref) {
  if(preg_match("~(.*?)\?~", $ref, $m)) $ref = $m[1];
  $ref =<<<EOF
You came to this site from <i>$ref</i>.<br>
EOF;
}

// BLP 2014-10-24 -- changed blp flag to 7098
// BLP 2014-08-18 -- add blp=8653 as flag
// If it's me add in the admin stuff

if($S->isBlp() || ($_GET['blp'] == "7098")) {
  // BLP 2014-12-02 -- as this is only for admin (me) I am using my local net address
  
  $adminStuff = <<<EOF
<h2>Administration Links</h2>
<ul>
<li><a target="_blank" class="uptest" href="http://192.168.0.6/weewx/">WEEWX home</a></li>
<li><a target="_blank" class="uptest" href="http://192.168.0.6/apc.php">APC Status home</a></li>
</ul>
EOF;
}

list($top, $footer) = $S->getPageTopBottom($h, array('msg1'=>"<hr>"));

$ip = $S->ip;
$blpIp = $S->blpIp;

$curdate = date("Y-m-d");

// Get todays count and visitors from daycounts table
$S->query("select sum(count) as count, sum(visits) as visits from daycounts where date='$curdate'");
$row = $S->fetchrow();
$count = number_format($row['count'], 0, "", ",");
$visits = number_format($row['visits'], 0, "", ",");

// Get total number for today.
$S->query("select count(*) from daycounts where date='$curdate'");
list($visitors) = $S->fetchrow();
$visitors = number_format($visitors, 0, "", ",");

$visitors .= ($visitors < 2) ? " visitor" : " visitors";
$date = date("l F j, Y");

// Render the page

echo <<<EOF
$top
<section id='browser-info'>
<p>
   Your browser's User Agent String: <i>$S->agent</i><br>
   Your IP Address: <i>$S->ip</i><br>
   Today is: <span id="datetoday">$date</span><br>
   This site uses three fonts from <i>fonts.googleapis.com</i> just for fun:
   <ul class='fontface'>
   <li class='rancho'>Rancho</li>
   <li class='jacques'>Jacques Francois Shadow</li>
   <li class='lora'>Lora</li>
   </ul>
   </p>
   <hr>
   <p>This page is dynamically generated using PHP on our expermental server at
   <a target="_blank" href="http://www.digitalocean.com/">digitalocean.com</a>.
</p>
</section>

<section id="blog">
<a target="_blank" href="http://myblog.bartonphillips.com">My BLOG with tips and tricks</a>.
</section>

<section id="links">
<h2><a href="http://www.bartonphillips.com">My Home Page (bartonphillips.com)</a></h2>
</ul>
<h2>Interesting Sites</h2>
<ul>
<li><a target="_blank" href="http://www.sitepoint.com">Site Point</a></li>
<li><a target="_blank" href="http://www.html5rocks.com/en/">HTML5 Rocks</a></li>
<li><a target="_blank" href="webstats-new.php">Web Stats</a></li>
</ul>
<h2>About the Internet</h2>
<ul>
<li><a target="_blank" href="http://www.bartonphillips.com/historyofinternet.php">
The History and Timeline of the Internet</a></li>
<li><a target="_blank" href="http://www.bartonphillips.com/howtheinternetworks.php">
How the Internet Works</a></li>
<li><a target="_blank" href="http://www.bartonphillips.com/howtowritehtml.php">
Tutorial: How To Write HTML</a></li>
<li><a target="_blank" href="http://www.bartonphillips.com/buildawebsite.php">
So You Want to Build a Website</a></li>
</ul>
$adminStuff
</section>

<section id="daycount">
<p>There have been $count hits and $visits visits by $visitors today $date</p>
<ul>
<li>Hits are each time this page is accessed. If you do three refreshes in a row you have 3 hits.</li>
<li>Visits are hits that happen 10 minutes appart. Three refresses in a row will not change the number of hits, but if you wait
10 minutes between refresses (or other accesses) to our site that is a visit.</li>
<li>Visitors are seperate accesses by different IP Addresses.</li>
</ul>
</section>

$footer
EOF;
