<?php
return <<<EOF
<header>
<img id="logo" src="http://bartonlp.com/html/images/blank.png">
<img src="/heidi/tracker.php?page=normal&id=$this->LAST_ID">
$mainTitle
<noscript>
<p style='color: red; background-color: #FFE4E1; padding: 10px'>
<img src="/heidi/tracker.php?page=noscript&id=$this->LAST_ID">
Your browser either does not support <b>JavaScripts</b> or you have JavaScripts disabled, in either case your browsing
experience will be significantly impaired. If your browser supports JavaScripts but you have it disabled consider enabaling
JavaScripts conditionally if your browser supports that. Sorry for the inconvienence.</p>
</noscript>
</header>
EOF;
