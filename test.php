<?php
$_site = require_once(getenv("HOME")."/includes/siteautoload.class.php");

  $img1 = "http://bartonlp.com/html/images/blank.png";

  if($_site['trackerImg1']) {
    $img1 = "http://bartonlp.com/html" . $_site['trackerImg1'];
  }

  $imageType = preg_replace("~^.*\.(.*)$~", "$1", $img1);
  $img = file_get_contents("$img1");
  error_log($img);
  header("Content-type: image/$imageType");
  echo $img;
  exit();