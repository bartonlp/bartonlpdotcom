<?php
// BLP 2016-04-11 -- This uses make-webstats.txt and then does an eval.
// It is a bit conveluted but it works both here and on conejoskiclub.org.

$__p = file_get_contents("http://bartonlp.com/html/make-webstats.eval");
return eval("?>" . $__p);
