<?php 
//This gets today's date 

$ar = [4=>['color'=>'#00FF7F', 'text'=>"night backet ball"],
       6=>['text'=>"hili"],
       10=>['color'=>'#FCF6CF', 'text'=>"<ul><li>around the world with my friends</li><li>More Fun</li></ul>"]];

// $date =time(); 
$date = strtotime("2016-06-01");

//This puts the day, month, and year in seperate variables 

$day = date('d', $date) ; 

$month = date('m', $date) ; 

$year = date('Y', $date) ;

//Here we generate the first day of the month 

$first_day = mktime(0,0,0,$month, 1, $year) ; 

//This gets us the month name 

$title = date('F', $first_day) ;

//Here we find out what day of the week the first day of the month falls on 

$day_of_week = date('D', $first_day) ; 

//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero

switch($day_of_week){ 
  case "Sun": $blank = 0; break; 
  case "Mon": $blank = 1; break; 
  case "Tue": $blank = 2; break; 
  case "Wed": $blank = 3; break; 
  case "Thu": $blank = 4; break; 
  case "Fri": $blank = 5; break; 
  case "Sat": $blank = 6; break; 
}

//We then determine how many days are in the current month

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year) ; 

switch($days_in_month) {
  case 28:
    $n = $blank ? 5 : 4;
    $h = 100 / $n;
    break;
  case 29:
    $n = 5;
    $h = 100 / $n;
    break;
  case 30:
    $n = $blank > 5 ? 6 : 5;
    $h = 100 / $n;
    break;
  case 31:
    $n = $blank > 4 ? 6 : 5;
    $h = 100 / $n;
    break;
}

//Here we start building the table heads 

echo <<<EOF
<!DOCTYPE html>
<html>
<head>
<style>
html {
  height: 100%;

}
table {
  width: 100%;
  height: 100%;
  border: 4px solid black;
  box-sizing: border-box;
}
tr {
  height: $h%;
  border: 2px solid red;
}
tbody td {
  padding-left: .5rem;
  padding-right: .5rem;
  font-size: 1.5rem;
}
th, td {
  width: 14.285714286%;
  border: 1px solid green;
}
td {
  vertical-align: 1cm;
}
thead th {
  font-size: 3rem;
}
body {
  height: calc(100% - (16px));
}
.text {
  font-size: .8rem;
  line-height: .8rem;
}
.before {
  background-color: pink;
}
.after {
  background-color: yellow;
}
ul {
  padding-left: .5rem;
  margin: 0;
}
</style>
</head>
<body>
<table>
<thead>
<tr><th colspan=7> $title $year </th></tr>
<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thr</th><th>Fri</th><th>Sat</th></tr>
</thead>
<tbody>
EOF;

//This counts the days in the week, up to 7

 $day_count = 1;

 echo "<tr>";

 //first we take care of those blank days

 while ( $blank > 0 ) { 
   echo "<td class='before'></td>"; 
   $blank = $blank-1; 
   $day_count++;
 } 

 //sets the first day of the month to 1 

 $day_num = 1;

 //count up the days, untill we've done all of them in the month

 while($day_num <= $days_in_month) {
   $text = "<div class='text'>".$ar[$day_num]['text']."</div>";
   $x = " style='background-color: ". $ar[$day_num]['color'] . ";'";
   echo "<td$x>$day_num<br>$text</td>"; 
   $day_num++; 
   $day_count++;

   //Make sure we start a new row every week

   if($day_count > 7) {
     echo "</tr><tr>";
     $day_count = 1;
   }
 } 

 //Finaly we finish out the table with some blank details if needed

 while($day_count >1 && $day_count <=7) { 
   echo "<td class='after'> </td>"; 
   $day_count++; 
 } 

 echo <<<EOF
</tr>
</tbody>
</table>
</body>
</html>
EOF;
