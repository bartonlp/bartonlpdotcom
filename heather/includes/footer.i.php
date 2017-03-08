<?php
// Footer file

$lastmod = date("M j, Y H:i", getlastmod());

return <<<EOF
<footer>
<h2><a target="_blank" href='aboutwebsite.php'>About This
   Site</a></h2>

<div id="address">
<address>
  Copyright &copy; $this->copyright
</address>
</div>
{$arg['msg']}
{$arg['msg1']} 
$counterWigget
{$arg['msg2']}
<p>Last Modified: $lastmod</p>
</footer>
</body>
</html>
EOF;
