<?php
$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

// Start UpdateSite logic
/* CREATE TABLE `site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `bodytext` text,
  `date` datetime DEFAULT NULL,
  `status` enum('active','inactive','delete') DEFAULT 'active',
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=231 DEFAULT CHARSET=utf8;

Any of the above fields can be used in $item.
*/
// For a fair description of how the UpdateSite class works look at the class file.
// I have updated the comments quite a lot.
// Check out the admintext.php file and the updatesite.php and updatesite2.php files.

$s->siteclass = $S;
$s->page = "index.php"; // the name of this page
$s->itemname ="Message1"; // the item we want to get first

$u = new UpdateSite($s); // Should do this outside of the '// START UpdateSite ...' comments

// Now getItem gets the info for the $s->itemname sections
// The special comments around each getItem() are MANDATORY and are used by the UpdateSite class
// to maintain the information in the 'site' table in the bartonphillipsdotorg database at
// bartonphillips.com

// START UpdateSite Message1
$item = $u->getItem();
// END UpdateSite Message1

// If item is false then no item in table

if($item !== false) {
  $msg1 = $item['bodytext'];
}

$s->itemname ="CSS";

// START UpdateSite CSS
$item = $u->getItem($s);
// END UpdateSite CSS

if($item !== false) {
  $CSS = $item['bodytext'];
}

$s->itemname = "BANNER";

// START UpdateSite BANNER
$item = $u->getItem($s);
// END UpdateSite BANNER

if($item !== fasle) {
  $BANNER = $item['bodytext'];
}

// End of UpdateSite logic

$h->banner = $BANNER;
$h->css = $CSS;

if(strpos($msg1, 'id="show"') !== false) {
  $h->extra = <<<EOF
  <script src="https://bartonphillips.net/js/random.js"></script>
  <script src="https://bartonphillips.net/js/ximage.js"></script>
  <script>dobanner("Pictures/Germany2010/*.JPG", "no");</script>
EOF;
  $h->css = <<<EOF
$CSS
  <style>
#show {
   width: 20rem;
   margin: auto;
}
#show img {
  width: 100%;
}
  </style>
EOF;
}

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
$msg1
$footer
EOF;

