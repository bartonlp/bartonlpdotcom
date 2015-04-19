<?php
echo <<<EOF
<h1>Cidr Range</h1>
<form action="http://www.ipaddressguide.com/cidr#range" method="post">
<p>
<label>IP Range</label>
<input name="ipStart" type="text" size="30" value="" onclick="this.value='';" />
-
<input name="ipEnd" type="text" size="30" value="" onclick="this.value='';" />
</p>
<p>
<input class="button" type="submit" value="Calculate" /> <a href="/home">Cancel</a>
</p>
</form>

This site or product includes IP2Location LITE data available from
<a href="http://www.ip2location.com">http://www.ip2location.com</a>.  
EOF;
?>