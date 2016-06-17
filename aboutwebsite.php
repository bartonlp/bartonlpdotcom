<?php
// All sites that have an "About Web Site" link should have a symlink
// to /var/www/html/aboutwebsite.php and should have a modified MySitemap.php that has
// these items in the $_site array: 1) 'copyright', 2 'className' 3 'siteName' etc.
// The className is the name of the class for the site. For example: /var/www/endpolio uses
// the file /var/www/endpolio/includes/Endpolio.clas.php which has Endpolio as the class.
// Therefore 'className' should be 'Endpolio' which is then instantiated by
// $S = new $_site['className']; which becomes '$S = new Endpolio;
//$AutoLoadDEBUG = true;
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");

$S = new $_site['className']($_site);

// check for subdomain. This doesn't need to be rigorous as we will Never have a multiple
// subdomain like en.test.domain.com. At most we might have www. or mpc.

$webdomain = $S->siteDomain;

if(($n = count(explode(".", $webdomain))) == 2) {
  $webdomain = "www." . $webdomain;
}

$copyright = isset($S->copyright) ? $S->copyright : (isset($S->siteName) ? date("Y") . " " . $S->siteName :
             date("Y") . " " . get_class($S));

$h->title = "About This Web Site and Server";
$h->banner = "<h2>About This Web Site and Server</h2>";
$h->css = <<<EOF
  <style>
img { border: 0; }
/* About this web site (aboutwebsite.php)  */
header {
        text-align: center;
}
#aboutWebSite {
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 2em;
        display: block;
        width: 100%;
        text-align: center;
}
#runWith {
        background-color: white;
        border: groove blue 10px;
        margin: 2em;
}
img[alt="jQuery logo"] {
        background-color: black;
        width: 215px;
}
img[alt="100% Microsoft Free"] {
        width: 100px;
}
img[alt="Powered By ...?"] {
        width: 90px;
        height: 53px;
}
img[alt="DigitalOcean"] {
        width: 200px;
        height: 60px;
        vertical-align: middle;
}
img[alt="Apache"] {
        width: 400px;
        height: 148px;
}
img[alt="PHP Powered"], img[alt="Powered by MySql"] {
        width: 150px;
        heitht: 50px;
}
img[alt="Best viewed with Mozilla or any other browser"] {
        width: 321px;
}
@media (max-width: 800px) {
        #runWith {
          width: 94%;
          margin: 0px;
        }
}
  </style>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<div id="aboutWebSite">
<div id="runWith">
  <p>This site's designer is Barton L. Phillips<br/>
     at <a href="http://www.bartonphillips.com">www.bartonphillips.com</a><br>
     Copyright &copy; $copyright
  </p>
  
	<p>This site is hosted by
    <a href="http://www.digitalocean.com">
		  <img	src="http://bartonlp.com/html/images/aboutsite/digitalocean.jpg"
		    alt="DigitalOcean">
		</a>
  </p>
  <p>This site is run with Linux, Apache, MySql, and PHP<br>
    <img src="http://bartonlp.com/html/images/aboutsite/linux-powered.gif"
      alt="Linux Powered">
  </p>
	<p>
    <a href="http://www.apache.org/">
    <img src="http://bartonlp.com/html/images/aboutsite/apache_logo.gif"
      alt="Apache">
    </a>
  </p>
	<p>
    <a href="http://www.mysql.com">
      <img src="http://bartonlp.com/html/images/aboutsite/powered_by_mysql.gif"
        alt="Powered by MySql">
    </a>
  </p>
	<p>
    <a href="http://www.php.net">
      <img src="http://bartonlp.com/html/images/aboutsite/php-small-white.png"
        alt="PHP Powered">
    </a>
  </p>
  <p>
    <a href="http://jquery.com/">
      <img src="http://bartonlp.com/html/images/aboutsite/logo_jquery_215x53.gif"
        alt="jQuery logo">
    </a>
  </p>
<!--	<p>
    <a href="http://www.mozilla.org">
      <img src="http://bartonlp.com/html/images/aboutsite/bestviewedwithmozillabig.gif"
        alt="Best viewed with Mozilla or any other browser">
    </a>
  </p> -->
	<p>
    <img src="http://bartonlp.com/html/images/aboutsite/msfree.png"
      alt="100% Microsoft Free">
  </p>
	<p>
    <a href="http://www.netcraft.com/whats?url=$webdomain">
	    <img src="http://bartonlp.com/html/images/aboutsite/powered.gif"
        alt="Powered By ...?">
    </a>
	</p>
</div>
</div>
$footer
EOF;
