<?php
// BLP 2016-04-11 -- This uses webstats-new.txt and then does an eval.
// This works on bartonlp.com and conejoskiclub.org. This way both sites are using the same file
// back on bartonlp.com.
$__p = file_get_contents("http://bartonlp.com/html/webstats-new.eval");
return eval("?>" . $__p);
