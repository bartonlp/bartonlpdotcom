<?php
// Main page for bartonlp.com
// BLP 2016-11-12 -- site now uses SITELOAD

$_site = require_once(getenv("SITELOAD")."/siteload.php");
//ErrorClass::setNoEmailErrs(true);
//ErrorClass::setDevelopment(true);
$S = new $_site->className($_site);

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
  font-size: 1.5rem;

}
h1 {
  font-family: Rancho, serif;
}
h2 {
  font-family: 'Jacques Francois Shadow', serif;
  font-size: 1.6rem;
}
h3 {
  font-family: 'Rancho', serif;
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
.hereMsg {
  font-size: 1.2rem;
  font-weight: bold;
  padding-top: 1rem;
}
.weather {
  font-family: 'Jacques Francois Shadow', serif;
  margin-top: -2rem;
}
.mylinks, #adminstuff ul {
  font-size: 1rem;
  column-count: 4;
  column-gap: 2rem;
  column-rule: 3px solid black;
  line-height: 1.5rem;
}
#adminstuff ul {
  font-family: 'Lora', serif;
  list-style: none;
  padding-left: 0;
}
#adminstuff h2 {
  font-family: 'Rancho', serif;
  margin-bottom: 3rem;
}
.mylinks p {
  margin: 0;
}
#links a:hover {
  background-color: #FCF6CF;;
}
#links a {
  padding: .3rem;
}
.admin {
  font-size: 1rem;
  column-count: 4;
  column-gap: 2rem;
  column-rule: 3px solid black;
  line-height: 1.5rem;
}
.admin p {
  margin: 0;
}
ul {
  font-family: Rancho, serif;
  font-size: 1.2rem;
  margin-top: -2rem;
}
.fontface {
  line-height: 110%;
  font-size: 1.2rem;
  margin-top: -1.5rem;
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
.center {
  text-align: center;
}

#logo {
  padding: 5px 10px;
}
@media (max-width: 600px) {
  body {
    font-size: 16px;
  }
  .admin, .mylinks {
    width: 97%;
    font-size: 1rem;
    line-height: 1rem;
    column-count: 2;
    column-gap: 1rem;
  }
}
  </style>
EOF;

$h->title = "Bartonlp.com";

$h->banner = "<h1 class='center font-effect-shadow-multiple'>".
             "bartonlp.com</h1>".
             "<h2 class='center weather'>".
             "<a target='_blank' ".
             "href='http://www.bartonlp.com/toweewx.php'>".
             "My Home Weather Station</a></h2>";

$ref = $_SERVER['HTTP_REFERER'];
if($ref) {
  if(preg_match("~(.*?)\?~", $ref, $m)) $ref = $m[1];
  $ref =<<<EOF
You came to this site from <i>$ref</i>.<br>
EOF;
}

// BLP 2014-10-24 -- changed blp flag to 7098
// If it's me add in the admin stuff

if($S->isMe() || ($_GET['blp'] == "7098")) {
  $adminStuff = file_get_contents("/var/www/bartonlp/adminsites.txt");
}

// Do we have a cookie? If not offer to register

if(!($hereId = $_COOKIE['SiteId'])) {
  $S->query("select count, date(created) from $S->masterdb.logagent ".
            "where ip='$S->ip' and agent='$S->agent' and site='$S->siteName'");

  list($hereCount, $created) = $S->fetchrow('num');
  if($hereCount > 1) {
    $hereMsg =<<<EOF
<div class="hereMsg">You have been to our site $hereCount since $created<br>
Why not <a href="register.php">register</a>
</div>
EOF;
  }
} else {
  $sql = "select name from members where id=$hereId";
  if($n = $S->query($sql)) {
    list($memberName) = $S->fetchrow('num');
    $hereMsg =<<<EOF
<div class="hereMsg">Welcome $memberName</div>
EOF;
  } else {
    error_log("$S->siteName: members id ($hereId) not found at line ".__LINE__);
  }
}

list($top, $footer) = $S->getPageTopBottom($h, array('msg1'=>"<hr>"));

// Get todays count and visitors from daycounts table
$S->query("select sum(`real`+bots) as count, sum(visits) as visits ".
          "from $S->masterdb.daycounts ".
          "where date=current_date() and site='$S->siteName'");

$row = $S->fetchrow();
$count = number_format($row['count'], 0, "", ",");
$visits = number_format($row['visits'], 0, "", ",");

$date = date("l F j, Y");

// Render the page

echo <<<EOF
$top
<section id='browser-info'>
$hereMsg
<p>
  Your browser's User Agent String: <i>$S->agent</i><br>
  Your IP Address: <i>$S->ip</i><br>
  Today is: <span id="datetoday">$date</span><br>
  This site uses three fonts from <i>fonts.googleapis.com</i> just for fun:
</p>
<ul class='fontface'>
  <li class='rancho'>Rancho</li>
  <li class='jacques'>Jacques Francois Shadow</li>
  <li class='lora'>Lora</li>
</ul>
<hr>
<p>This page is dynamically generated using PHP on our expermental server at
  <a target="_blank" href="http://www.digitalocean.com/">digitalocean.com</a>.
</p>
</section>

<section id="blog">
<a target="_blank" href="proxy.php?http://myblog.bartonphillips.com">My BLOG with tips and tricks</a>.
</section>

<section id="links">
<h2><a href="http://www.bartonphillips.com">My Home Page (bartonphillips.com)</a></h2>

<h3>GitHub Projects</h3>
<div class='mylinks'>
  <p><a target="_blank" href="proxy.php?https://bartonlp.github.io/bartonphillips">Barton Phillips GitHub site</a></p>
  <p><a target="_blank" href="proxy.php?https://bartonlp.github.io/site-class/">SiteClass on GitHub</a></p>
  <p><a target="_blank" href="proxy.php?https://bartonlp.github.io/updatesite/">UpdateSite Class on GitHub</a></p>
  <p><a target="_blank" href="proxy.php?https://bartonlp.github.io/rssfeed/">RssFeed Class on GitHub</a></p>
</div>
<h3>About the Internet</h3>
<div class='mylinks'>
  <p><a target="_blank" href="proxy.php?http://www.bartonphillips.com/historyofinternet.php">The History and Timeline of the Internet</a></p>
  <p><a target="_blank" href="proxy.php?http://www.bartonphillips.com/howtheinternetworks.php">How the Internet Works</a></p>
  <p><a target="_blank" href="proxy.php?http://www.bartonphillips.com/howtowritehtml.php">Tutorial: How To Write HTML</a></p>
  <p><a target="_blank" href="proxy.php?http://www.bartonphillips.com/buildawebsite.php">So You Want to Build a Website</a></p>
</div>
$adminStuff
</section>
<hr>
<div class='center'>
<span itemscope itemtype="http://schema.org/Organization">
  <link itemprop="url" href="http://www.bartonlp.com">
  <a itemprop="sameAs" href="http://www.facebook.com/bartonlp"><img width="200" src="images/facebook.png" alt="follow us on facebook"></a>
  <a itemprop="sameAs" href="http://www.twitter.com/bartonlp"><img width="200" src="images/twitter3.png" alt="follow us on twitter"></a>
</span>
</div>
$footer
EOF;
