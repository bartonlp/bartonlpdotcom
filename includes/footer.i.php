<?php
// Footer file
// BLP 2018-02-24 -- add 'script' just before </body>

$lastmod = date("M j, Y H:i", getlastmod());

return <<<EOF
<footer>
<h2><a target="_blank" href='aboutwebsite.php'>About This
   Site</a></h2>

<div id="address">
<address>
  Copyright &copy; $this->copyright
</address>
<address>
Barton Phillips<br>
$this->address<br>
<a href='mailto:bartonphillips@gmail.com'>
  bartonphillips@gmail.com
</a>
</address>
</div>
{$arg['msg']}
{$arg['msg1']} 
$counterWigget
{$arg['msg2']}
<p>Last Modified: $lastmod</p>
</footer>
{$arg['script']}
</body>
</html>
EOF;
