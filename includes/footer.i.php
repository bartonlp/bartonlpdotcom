<?php
// Footer file

$statcounter = <<<EOF
<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
var sc_project=10124882; 
var sc_invisible=1; 
var sc_security="1d3699f6"; 
var scJsHost = (("https:" == document.location.protocol) ?
"https://secure." : "http://www.");
document.write("<sc"+"ript type='text/javascript' src='" +
scJsHost+
"statcounter.com/counter/counter.js'></"+"script>");
</script>
<noscript><div class="statcounter"><a title="web analytics"
href="http://statcounter.com/" target="_blank"><img
class="statcounter"
src="http://c.statcounter.com/10124882/0/1d3699f6/1/"
alt="web analytics"></a></div></noscript>
<!-- End of StatCounter Code for Default Guide -->
<a href="http://statcounter.com/p10124882/?guest=1">View My
Stats</a>
EOF;

// If we set $b['statcounter'] for getFooter($b) to a string
// then we want to use the string.
// If $b['statcounter'] === false then make $statcounter be ''.
// If $b['statcounter'] is NOT set then we want to use the above $statcounter.

if(isset($arg['statcounter'])) {
  if(is_string($arg['statcounter'])) {
    $statcounter = $arg['statcounter'];
  } elseif($arg['statcounter'] === false) {
    $statcounter = '';
  }
}

return <<<EOF
<footer>
<h2><a target="_blank" href='aboutwebsite.php'>About This
   Site</a></h2>

<div id="address">
<address>
  Copyright &copy; $this->copyright</address>
<address>
Barton Phillips<br>
828 Cayo Grande Ct., Newbury Park CA 91320<br>
<a href='mailto:bartonphillips@gmail.com?to=test@bartonlp.com&subject=test'>
  bartonphillips@gmail.com
</a>
</address>
</div>
</div>
{$arg['msg']}
{$arg['msg1']} 
$counterWigget
{$arg['msg2']}
</footer>
$statcounter
</body>
</html>
EOF;
