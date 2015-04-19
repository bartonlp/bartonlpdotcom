<?php
  define('TOPFILE', $_SERVER['DOCUMENT_ROOT'] . "/siteautoload.php");
if(file_exists(TOPFILE)) {
  include(TOPFILE);
} else throw new Exception(TOPFILE . " not found");
  vardump($_SERVER);

  echo "<p>This is a test </p>";
?>