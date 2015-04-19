requirejs.config({
  // Use the cookieless url to load all of the js files.
  baseUrl: 'http://bartonlp.com/html/js',
/*
 paths: {
             phpdate: '<path if not baseUrl>',
             tracker: '<path if not baseUrl>',
        }
*/           
});

// Start loading the main app file. Put all of
// your application logic in there.
// Get the main from the cookieless url.
requirejs(['http://bartonlp.com/html/index-app/main.js']);
