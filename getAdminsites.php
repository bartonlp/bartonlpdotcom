<?php
// This is used by remote sites (like HP-Envy) that want to get the adminsites.php information.
// Those sites will need to do:
// $adminstuff = file_get_contents("https://www.bartonlp.com/getAdminsites.php");

$adminstuff = require("/var/www/bartonlp/adminsites.php");
echo $adminstuff;
