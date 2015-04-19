<?php
// BLP 2015-02-22 -- make this global for all members pages in all sites.
// BLP 2015-02-17 -- changed mail sending and removed attachments. Now using plain mail();

$FILE = basename(__FILE__);

require_once("/var/www/includes/siteautoload.class.php");;

$S = new $siteinfo['className'];
//vardump($_REQUEST);
$referer = $_SERVER['HTTP_REFERER'];

// Don't let people come here from anywhere else than the members
// page! We can change this later to make it only our sites

if(!preg_match("~{$S->s['siteDomain']}~i", $referer) && ($_REQUEST['mail'] != 1)) {
  if($referer) echo "referer=$referer<br/>";
  
  echo <<<EOL
<h1>This page can only be accessed from our domain</h1>
<p>
Please return to our <a href='index.php'>home page</a> and follow the <b>Members</b> link.
</p>
EOL;
  exit();
}

// Two different email types:
// 1) single which is a GET
// 2) multiple which is a POST with a checkbox

switch(strtoupper($_SERVER['REQUEST_METHOD'])) {
  case 'POST':
    switch($_POST['page']) {
      case 'sendMail':
        sendMail($S);
        break;
      default:
        $S->idTo = $_POST['id'];
        $S->page = "multi";
        start($S);
        break;
    }
    break;
  case 'GET':
    switch($_GET['page']) {
      default:
        $S->idTo = $_GET['id'];
        $S->page = "single";
        start($S);
        break;
    }
    break;
  default:
    throw(new Exception("Not GET or POST: {$_SERVER['REQUEST_METHOD']}"));
    break;
}

exit();

// Start: This handles both Single and Multiple recipients.
// $S->page is either 'single' or 'multi'
// $S->id; TO id or ids a string if only one or an array if several

function start($S) {
  $memberTable = $S->s['memberTable'];
  $idTo = $S->idTo; // either a single id or an array of ids
  $fromId = $S->id;
  $page = $S->page; // 'single' or 'multi'
  
  // $id may be a single id or an array of ids. Make a string 'x,y,z' for the ids.

  $ids = implode(',', (array)$idTo);

  // Some member tables have Email while others have email etc. MySql doesn't care but if we
  // do a fetchrow('assoc') then the case does matter!
  
  $S->query("select email, concat(fname, ' ', lname) from $memberTable where id='$fromId'");
  list($fromEmail, $fromName) = $S->fetchrow('num');

  // $ids may be a single id or multiple like (x,y,z)
  
  $S->query("select concat(fname, ' ', lname) from $memberTable where id in ($ids)");

  $h->title = "Send Email To Member";

  $h->banner = "<h2>Send a message to ";
  
  if($page == 'single') {
    // Single recipient.
    
    list($name) = $S->fetchrow('num');
    $h->banner .= "<i>$name</i></h2>";
  } else {
    // Multiple recipients.
    
    while(list($name) = $S->fetchrow('num')) {
      $h->banner .= "<br>  $name";
    }
    $h->banner .= "</h2>";
  }

  $h->css = <<<EOF
  <!-- Local CSS -->
  <style>
#mailform * {
  font-size: 1.05em;
  background-color: white;
  padding: 1em;
}
#mailform table * {
  border: 1px solid black;
  cellpadding: 1px;
  cellspacing: 0px;
}
#mailform table input {
        width: 96%;
}
#mailform textarea {
        width: 96%;
        height: 10em;
}
#mailform table {
        width: 100%;
}
#mailform .left {
        text-align: left;
        width: 10px;
}
#mailform input[type='submit'] {
  border-radius: 1em;
  padding: .5em;
  margin-top: .5em;
}
  </style>
EOF;

list($top, $footer) = $S->getPageTopBottom($h);

echo <<<EOF
$top
<form id='mailform' method='post' action="$S->self">
  <table>
    <tr>
      <td class='left'>From (email&nbsp;address)</td>
      <td><input required type='text' name='from' value='$fromName <$fromEmail>'></td>
    </tr>
    <tr>
      <td class='left'>Subject</td>
      <td><input autofocus required  type='text' name='subject' value="$subject"></td>
    </tr>
    <tr>
      <td class='left'>Message</td>
      <td>
        <textarea required name="message"></textarea>
      </td>
    </tr>
  </table>
  <input type="hidden" name="id" value="$ids">
  <input type="hidden" name="page" value="sendMail">
  <input type="submit" value="Send Mail">
</form>
<hr/>
$footer
EOF;
}

// Send Mail
// $_POST["from"] From full name and email
// $_POST["subject"] 
// $_POST["message"]
// $_POST["id"] ids of recipients as a string like 'x,y,z'
// $_POST["page"] page to go to

function sendMail($S) {
  // Submited message from this page
  $memberTable = $S->s['memberTable'];
  $courtesy = isset($S->s['siteName']) ? "\n\nThanks from ".$S->s['siteName'] : '';
  $siteEmail = explode(".", $S->s['siteDomain']);

  // $siteEmail might look like 'www.abc.def.com', we only want 'def.com' part.

  while(count($siteEmail) > 2) {
    array_shift($siteEmail);
  }

  $siteEmail = implode(".", $siteEmail);

  $id = $_POST['id'];
  $from = $_POST["from"]; // From full name and email
  $subject = $_POST["subject"];
  $message = $_POST["message"];

  $h->banner = "<h2>Mail Sent</h2>";
  list($top, $footer) =$S->getPageTopBottom($h);

  $S->query("select id, email, concat(fname, ' ', lname) from $memberTable where id in ($id)");

  $names = array();
  $sendIds = array();
  $cc = "";
  $cnt = 0;

  while(list($id, $email, $name) = $S->fetchrow('num')) {
    ++$cnt;
    $cc .= "  $name\n";
    $names["$name"] = $email;
    $sendIds["$name"] = $id;
  }

  $cc = $cnt > 1 ? "\ncc:\n$cc" : '';

  while(list($name, $email) = each($names)) {
    $msg = "Messsage from $from\n\n$message\n\n--$cc$courtesy";  

    /*
    echo "TO: $name &lt;$email&gt;, subject: $subject, message: $msg<br>";
    echo "From: Member Mail ($from) <$siteEmail><br>".
         "-fbartonphillips@gmail.com\r\n<br>";
    */

    mail("$name <$email>", $subject, $msg,
         "From: Member Mail ($from) <info@$siteEmail>",
         "-fbartonphillips@gmail.com\r\n"
        );
  }

  // If this site has an 'emails' table then post the info

  $database = $S->getDbName();

  if($S->query("select * from information_schema.tables ".
               "where table_schema='$database' ".
               "and table_name = 'emails'")) {

    // Log this email in the emails table
    // The table looks like this
    // id_fk: the members id
    // application: this is __FILE__ which will either be email.php or multmail.php
    // subject: the subject line
    // fromaddress: the from line
    // message: the email message
    // toaddress: $name <$email>. For multmail this is a comma seperated list of id's
    // sendtime: timestamp

    $mid = $S->id;
    $from =  $S->escape($S->email);
    $message = $S->escape($message);
    $subject = $S->escape($subject);
  
    $query = "insert into emails (id_fk,application,subject,fromaddress,message,toaddress) ".
             "value ('$mid', '$FILE', '$subject', '$from', '$message', '$ids')";

    $S->query($query);
  }

  echo "$top$footer";
}