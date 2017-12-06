// worker.worker.js This is javascript
// This is the worker side of worker.main.php and it calls
// worker.ajax.php for the info from tables.

// Add an event listener for 'message'. The data is in evt.data and we
// make it into a string and then pass the string to sendText()

addEventListener("message", function(evt) {
  var string = new TextDecoder("utf-8").decode(evt.data);
  console.log("Worker string: ", string);
  sendText(string);
});

// SendText() does the usual XMLHttpRequest() stuff to post to
// worker.ajax.php.
function sendText(txt) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'worker.ajax.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  // Send the text to the worker.ajax.php

  xhr.send("sql="+txt);

  // Get the information from our xhr.send().
  
  xhr.onload = function(e) {
    if(this.status == 200) {
      // We can get two non json items back ERROR or DONE
      
      if(this.responseText.match(/ERROR|DONE/)) {
        var str = this.responseText;

        // make an ArrayBuffer for the string.
        var buf = new ArrayBuffer(str.length);
        // now make a Uint8Array from buf
        var bufView = new Uint8Array(buf);
        // Fill the values from the string into the Uint8Array
        for(var i=0, strLen=str.length; i < strLen; i++) {
          bufView[i] = str.charCodeAt(i);
        }
        console.log("Error Worker bufView: ", bufView);
        // Post the Transfer buffer
        postMessage(bufView, [bufView.buffer]);
        return;
      }
      // If it isn't the two possible ascii text values then this is a
      // JSON packet fo decode it.
      
      console.log("Worker response", this.responseText);
      var newtxt = JSON.parse(this.responseText, true);

      // Take the items out of newtxt which is an array.
      
      var rows = '';
      
      for(item of newtxt) {
        // Now the stuff in the array is an object so get the key and
        // value and put them into the rows variable.
        
        for([key, value] of Object.entries(item)) {
          rows += key + ": " + value + "\n";
        }
        rows += "\n";
      }
      // Now we do the same thing we did above to make the Transfer
      // buffer
      var buf = new ArrayBuffer(rows.length);
      var bufView = new Uint8Array(buf);
      for(var i=0, strLen=rows.length; i < strLen; i++) {
        bufView[i] = rows.charCodeAt(i);
      }
      console.log("Worker bufView: ", bufView);
      postMessage(bufView, [bufView.buffer]);
      //postMessage(rows);
    }
  };
}
