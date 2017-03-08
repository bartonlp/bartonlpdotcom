<?php
//   $SQLDEBUG = true;
//   $ERRORDEBUG = true;

// This is the second part of the update site logic.
// The full set of files are in includes/ and are:
// 1) the class file 'updatesite.class.php'
// 2) the preview include 'updatesite-preview.i.php' which can be in the $nextfilename file.
// Item 2 is not manditory and can either be ignored or replaced by your own function updatesite_preview(...)
// I have a simpler version of the preview 'updatesite-simple-preview.i.php' that can be used instead
/*
$_site = require_once("/var/www/includes/siteautoload.class.php");
$S = new $_site['className']($_site);
//require_once("includes/updatesite-preview.i.php");
//require_once("includes/updatesite-preview.new.i.php"); // new with data-uri and no ajax
require_once(INCLUDES . "/updatesite-simple-preview.i.php"); // Simple preview logic!
*/

$_site = require_once(getenv("SITELOADNAME"));
$S = new $_site->className($_site);

$h->title = "Update Site For Granby Rotary";
$h->banner = "<h1>Update Site For Granby Rotary</h1>";
$h->nofooter = true; // don't have UpdateSite.class display the footers as we will do it below.

// This is some enhanced logic that shows the changes at once. You can cut and past this into other site versions of
// updatestie2.php!

$h->extra = <<<EOF
  <script type="text/javascript">
jQuery(document).ready(function() {
  var auto = 1;

  $("#updatesiteform #formtablesubmitth input")
  .after("<input type='button' id='render' style='display: none' value='Quick Preview'/>" +
        "<input type='button' id='autopreview' value='Stop Auto Preview' />");

  $("#updatesiteform").after("<div style='padding: 5px; border: 1px solid black' id='quickpreview'>");
  $("#quickpreview").html("<div style='border: 1px solid red'>TITLE: " + $("#formtitle").val() +
                            "</div>" + $("#formdesc").val());

  $("#autopreview").click(function() {
    if(auto) {
      $(this).val("Start Auto Preview");
      $("#render").show();
      auto = 0;
    } else {
      $(this).val("Stop Auto Preview");
      $("#render").hide();
      $("#render").click();
      auto = 1;
    }
  });

  $("#render").click(function() {
    $("#quickpreview").html("<div style='border: 1px solid red'>TITLE: " + $("#formtitle").val() +
                            "</div>" + $("#formdesc").val());
  });

  $("#formdesc, #formtitle").keyup(function() {
    if(!auto) return false;

    $("#quickpreview").html("<div style='border: 1px solid red'>TITLE: " + $("#formtitle").val() +
                            "</div>" + $("#formdesc").val());
  });
});
  </script>
EOF;

// Load updatesite.class.php

//require_once("includes/updatesite.class.php");

$s->site = "granbyrotary.org";

UpdateSite::secondHalf($S, $h, $s);

// footer created in secondHalf()

echo <<<EOF
<a href='howtowritehtml.php'>How to write HTML</a>
$S->footer
EOF;
