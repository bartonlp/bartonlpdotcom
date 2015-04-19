var http = require("http"),
//php = require("node-php"),
exec = require("child_process").exec,
url = require("url"),
path = require("path"),
fs = require("fs"),
port = process.argv[2] || 8888;

http.createServer(function(request, response) {
  var uri = url.parse(request.url).pathname,
  filename = path.join('/var/www/html', uri);

  console.log("uri: %s, filename: %s", uri, filename);
  
  var contentTypesByExtension = {
    '.html': "text/html",
    '.php':  "text/html",
    '.css':  "text/css",
    '.js':   "text/javascript"
  };

  fs.exists(filename, function(exists) {
    if(!exists) {
      response.writeHead(404, {"Content-Type": "text/plain"});
      response.write("404 Not Found\n");
      response.end();
      return;
    }

    if(fs.statSync(filename).isDirectory()) {
      filename += 'index.html';
    }
    
    console.log("uri: %s, final filename: %s", uri, filename);

    var headers = {};

    var contentType = contentTypesByExtension[path.extname(filename)];

    if(contentType) {
      headers["Content-Type"] = contentType;
    }
    response.writeHead(200, headers);

    if(filename.match(/^.*?\.php/)) {
      console.log("PHP FILE");
      // Fake out to set PHP_SELF via siteautoloader. If PHP_SELF is
      // NOT defined and BLP is then move BLP to PHP_SELF in
      // siteautoloader.php
      var options = {DOCUMENT_ROOT: '/var/www/html', BLP: uri};
      console.log("options: ", options);
      exec("php-cgi -q "+ filename, {env: options},
           function(error, stdout, stderr) {
        if(error) {
          console.log("error: ", error);
        }
        console.log("stdout: %s", stdout);
        if(stderr) {
          console.log("stderr: %s",stderr);
        }

        response.write(stdout, "binary");
        response.end();
      });
    } else {
      fs.readFile(filename, "binary", function(err, file) {
        if(err) {        
          response.writeHead(500, {"Content-Type": "text/plain"});
          response.write(err + "\n");
          response.end();
          return;
        }

        response.write(file, "binary");
        response.end();
      });
    }
  });
}).listen(parseInt(port, 10));

console.log("Static file server running at\n  => http://localhost:" + port + "/\nCTRL + C to shutdown");
