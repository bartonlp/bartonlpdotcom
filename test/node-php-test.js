var express = require('express');
var php = require("node-php");
var url = require("url");
var path = require("path"); 

var app = express();

var server = app.listen(9090, function() {
  var port = server.address().port;
  
  console.log('listening on port %s', port)
});

app.use("/test/test.php", function(req, res) {
  console.log("req: ", req);
  var uri = url.parse(req.url).pathname, filename = uri;

  console.log("filename: %s", filename);
  php.cgi(filename);
});

