// BLP 2014-09-11 -- modified for requirejs
// BLP 2014-03-06 -- track user activity

// Use a factory to determin if this is being loaded via AMD or not

(function(factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], function($) {
      var ret = factory($);
      ret();
    });
  } else {
    var ret = factory($);
    ret();
  }

}(function($) {

  return function() {
    // Track the ip/agent/page

    var id, self = window.location.pathname, referrer = document.referrer;

    $.ajax({
      url: '/tracker.php',
      data: {page: 'load', self: self, referrer: referrer },
      type: 'post',
      success: function(data) {
             console.log(data);
             id = data;
           },
           error: function(err) {
             console.log(err);
           }
    });

    $(window).unload(function(e) {
      $.ajax({
        url: '/tracker.php',
        data: {page: 'unload', id: id },
        type: 'post',
        async: false,
        success: function(data) {
               console.log(data);
             },
             error: function(err) {
               console.log(err);
             }
      });
    });
  }
}));