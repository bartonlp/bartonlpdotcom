<p id="str"></p>
<p id="results1"><p>
<pre id="results2"></pre>
<pre id="results3"></pre>

<script>
  
function uintToString(uintArray) {
  var encodedString = String.fromCharCode.apply(null, uintArray),
  decodedString = decodeURIComponent(escape(encodedString));
  return decodedString;
}

if('TextEncoder' in window) {
//  var str = "This is a test Ņ &copy; &#8364";
  var str = "This is a test &#8364";
  document.querySelector('#str').innerHTML = str;
  
  var encoder = new TextEncoder('utf-8');
  var str0 = encoder.encode(str);
  console.log('utf-8: %O', str0);
  encoder = new TextEncoder('utf-16le');
  var str1 = encoder.encode(str);
  console.log('utf-16le: %O', str1);
  var encoder = new TextEncoder('utf-16be');
  var str2 = encoder.encode(str);
  console.log('utf-16be: %O',str2);
  
  var decoder = new TextDecoder('utf-16be');
  var sstr = JSON.stringify(str2);
  console.log("sstr: %o", sstr);
  
  var uint16ar = new Uint16Array(str2.buffer);
  var ssstr = JSON.stringify(uint16ar);
  console.log("[0]: %d", uint16ar[0], uint16ar.length);
  console.log("ssstr: %o", ssstr, uint16ar);
  
  document.querySelector('#results1').innerHTML = decoder.decode(str2);
  document.querySelector('#results2').innerHTML = decoder.decode(str2);
  console.log("[20]: %d", uint16ar[20]);
  document.querySelector('#results3').innerHTML = uint16ar[20];
} else {
  document.querySelector('#results').textContent = 'Your browser does not support the Encoding API.';
}
</script>
