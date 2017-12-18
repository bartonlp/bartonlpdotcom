<?php
//
$txt = file_get_contents("main.txt");
var_dump($txt);

echo <<<EOF
<body>
TEXT: $txt
</body>
EOF;
