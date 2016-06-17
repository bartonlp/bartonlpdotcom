<?php
// Get remote filemtime

if(!function_exists('filemtime_remote')) {
  function filemtime_remote($uri) {
    $uri = parse_url($uri);
    $handle = @fsockopen($uri['host'],80);

    if(!$handle)
      return 0;

    fputs($handle,"GET $uri[path] HTTP/1.1\r\nHost: $uri[host]\r\n\r\n");
    $result = 0;

    while(!feof($handle)) {
      $line = fgets($handle, 1024);

      if(!trim($line)) {
        break;
      }

      $col = strpos($line,':');
      if($col !== false) {
        $header = trim(substr($line,0,$col));
        $value = trim(substr($line,$col+1));
        if(strtolower($header) == 'last-modified') {
          $result = strtotime($value);
          break;
        }
      }
    }
    fclose($handle);
    return $result;
  }
}
