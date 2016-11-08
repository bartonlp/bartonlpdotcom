<?php
  
define('TOP', '/var/www'); // for bartonlp.com which is like our Dell-530 home system.
define('INCLUDES', TOP."/includes"); // /var/www/includes
define('DATABASE_ENGINES', INCLUDES."/database-engines");
define('SITE_INCLUDES', SITE_ROOT."/includes"); // SITE_ROOT is defined in siteautoload.php!

// Email info

define('EMAILADDRESS', "bartonphillips@gmail.com");
define('EMAILRETURN', "bartonphillips@gmail.com");
define('EMAILFROM', "webmaster@bartonlp.com");

// Database connection information
// 'engine' is the type of database engine to use. Options are 'mysqli', 'sqlite'.
// Others may be added later
           
$dbinfo = array('host' => 'localhost',
                'user' => 'barton',
                'password' => '7098653',
                'database' => 'heidi',
                'engine' => 'mysqli'
               );

// SiteClass information
// This site has no members so no membertable.
// See the SiteClass constructor for other possible values like 'count',
// 'emailDomain' etc.

$siteinfo = array('siteDomain' => "www.bartonlp.com/heidi",
                  'emailDomain' => "bartonlp.com",
                  'siteName' => "Heidi",
                  'copyright' => "2016 Barton L. Phillips and Heidi Kemmer",
                  'className' => "SiteClass",
                  'memberTable' => "heidi", 
                  'path' => "/var/www/bartonlp/heidi",
                  'masterdb' => "barton",
                  'dbinfo' => $dbinfo,
                  'headFile' => SITE_INCLUDES."/head.i.php",
                  'bannerFile' => SITE_INCLUDES."/banner.i.php",
                  'footerFile' => SITE_INCLUDES."/footer.i.php",
                  'count' => true,
                  'countMe' => true, // Count BLP
                  'analysis' => true,
                  'trackerImg1' => "/images/blank.png", // script
                  'trackerImg2' => "/images/blank.png", // normal
                  'myUri' => "bartonphillips.dyndns.org", // If we are local (at home) then 'localhost'
                  'EMAILADDRESS' => EMAILADDRESS,
                  'EMAILRETURN' => EMAILRETURN,
                  'EMAILFROM' => EMAILFROM,
                 );

