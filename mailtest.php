<?php

$ret = mail("bartonphillips@gmail.com", "Test from PHP to Gmail", "This is a test",
            "From: barton@bartonlp.com\r\n");

echo "Mail to Gmail: $ret<br>";

$ret = mail("barton@bartonphillips.com", "Test from PHP to bartonphillips", "This is a test",
            "From: barton@bartonlp.com\r\n");

echo "Mail to bartonphillips: $ret<br>";
