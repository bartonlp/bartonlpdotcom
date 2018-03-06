# www.bartonlp.com

*www.bartonlp.com* is canonical for *www.bartonphillips.com*. This site has:

* tracker.php
* beacon.php,
* analysis.php
* adminsites.php
* proxy.php
* robots.php
* sitemap.php 
* gitinfo.php
* gitstatus.php
* register.php
* and '.eval' version of the former.

These file are symlinked to the other sites at 45.55.27.116 on DigitalOcean.com VPS
(virtual private server).

All of the sites use the SiteClass framework which is at /var/www/vendor/bartonlp/site-class,
and documentation can be found at https://github.com/bartonlp/site-class.

The framework is also used by my home computer at http://www.bartonphillips.dyndns.org:4080, 
my RPI at http://www.bartonphillips.dyndns.org:8080, and my other server at DigitalOcean
(162.243.156.130 www.bartonlp.org).

The http://go.myphotochannel.com (74.208.215.54) site at *1and1.com* has the Slideshow and
Cpanel apps.

All of the sites are maintained as git repositories and are hosted at http:github.com/bartonlp .

These file are used by https://www.bartonphillips.com and should be in that directory:

* base64.php
* getAdminsites.php
* getIP.php
* getcountryfromip.php
* showmarkdown.php

**toweewx.php** decides if the request for https://www.bartonphillips.com/weewx
is from a desktop or a mobile.
It is used by both https://www.bartonphillips.com and https://www.bartonlp.com.

## Contact me: [bartonphillips@gmail.com](mailto:bartonphillips@gmail.com)

