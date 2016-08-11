<?php
/*
 * Created by PhpStorm.
 * User: juanito
 * Date: 8/8/16
 * Time: 3:21 PM
 */
session_start();
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking

function month_generator($cur_mon, $days, $weekdays, $year) {
    $week = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];
    $mon_list = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

$str = '
  <div class="row">
    <div class="col-xs-5"></div>
    <div class="col-xs-2" id="month-head-' . strval($cur_mon) . '">
      <h1 id="mon-name-' . strval($cur_mon) . '">' . $mon_list[$cur_mon] .
    '</h1>
    </div>
    <div class="col-xs-5"></div>
  </div> <!-- end row with month name -->
  <div class="row">
    <div class="col-xs-1 left_edge">
    </div>';

    for ($x = 0; $x < count($week); $x++) {
        $str .= '<div class="col-xs-1 weekbox"><h3>' . $week[$x] . '</h3></div>';
    }
    $str .= '<div class="col-xs-3"></div></div> <!-- end row of week days header -->';
    $head = 0;
    $end = 1;
    for ($i = 0; $i < 6; $i++) {
        $str .= '
  		<div class="row">
  		<div class="col-xs-1 left_edge"></div>';
	$colorin = 'document.getElementById(this).style.background-color=\'green\'';

	$colorout = 'document.getElementById(this).style.background-color=\'white\'';
	$attr = 'onmouseover="colorin(this)" onmouseout="colorout(this)"';
        for ($j = 0; $j < 7; $j++) {
            $start = $weekdays[$cur_mon][0];
            if ($i == 0) {
                if ($j < $start) {
                    $tmp = $start - $j;
                    // get days from previous month
                    if ($cur_mon > 0) {
                        $daynum = $days[$cur_mon - 1][count($days[$cur_mon - 1]) - $tmp];
                    }
                    else {
                        $lastdec = ['25', '26', '27', '28', '29', '30', '31'];
                        $daynum = $lastdec[count($lastdec) - $tmp];
                    }
		            $str .= '<div class="col-xs-12 col-md-1 before day" ' . $attr . 'id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">' . strval($daynum) . '</div>';
                }
                else {
                      $daynum = $days[$cur_mon][$head];
                      $head ++;
		              $str .= '<div class="col-xs-12 col-md-1 norm day" ' . $attr . 'id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">'. strval($daynum) . '</div>';
                }
            }
            else if (($i > 3) && (count($days[$cur_mon]) <= $head)) {
		        $str .= '<div class="col-xs-12 col-md-1 after day" ' . $attr . 'id="'. (strval($cur_mon) . "-" . strval($end) . "-" . strval($year) . "-box") .'">' . strval($end) . '</div>';
                $end++;
            }
            else {
                $daynum = $days[$cur_mon][$head];
                $head++;
		        $str .= '<div class="col-xs-12 col-md-1 norm day" ' . $attr . 'id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">' . strval($daynum) . '</div>';
            }
        }
        $str .= '<div class="col-xs-3"></div></div>';
    }
    return $str;
}
// end month generator function

// connect to DB (can you put this at beginning of file?
$file = fopen("./db.txt", "r") or die("Error opening file.");
$dbinfo = [];
while (!feof($file)) {
    $info = trim(fgets($file));
    array_push($dbinfo, $info);
}
$connect = mysqli_connect($dbinfo[0], $dbinfo[1], $dbinfo[2], $dbinfo[3], $dbinfo[4]);
if (empty($connect)) {
    die ("mysqli_connect failed " . mysqli_connect_error());
}
$year_in = $_POST["year"];
$sql = "SELECT * FROM doctor_logger WHERE YEAR = " . $year_in . " ORDER BY MONTH, DAY";
$result = $connect->query($sql);
$months = [];
for ($x = 0; $x < 12; $x++) {
    array_push($months, $x);
}
$days = [];
$weekdays = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cur_day = $row["DAY"];
        if ($cur_day == "1") {
            if (isset($days_list) && isset($weekdays_list)) {
                array_push($days, $days_list);
                array_push($weekdays, $weekdays_list);
                array_push($doc1_list, $days_list);
                array_push($doc2_list, $weekdays_list);

            }
            $days_list = [];
            $weekdays_list = [];
            $doc1_list = [];
            $doc2_list = [];
            array_push($days_list, $cur_day);
            array_push($weekdays_list, $row["WEEKDAY"]);
            array_push($doc1_list, $row["DOC1"]);
            array_push($doc2_list, $row["DOC2"]);
        }
        else {
            array_push($days_list, $cur_day);
            array_push($weekdays_list, $row["WEEKDAY"]);
            array_push($doc1_list, $row["DOC1"]);
            array_push($doc2_list, $row["DOC2"]);
        }
    }
    array_push($days, $days_list);
    array_push($weekdays, $weekdays_list);
    array_push($doc1_list, $days_list);
    array_push($doc2_list, $weekdays_list);
}

$output = "";
$p = 0;
for ($i = 0; $i < 3; $i++) {
    $output .= '<div class="row">';
    for ($j = 0; $j < 4; $j++) {
        $output .= month_generator($p, $days, $weekdays, $year_in);
        $p++;
    }
    $output .= '</div>';
}
//$output .= '<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.0/jquery.scrollTo.min.js"></script>';
//$output .= '<script> $.scrollTo( $("#month-head-8"), 500); </script>';
echo $output;
?>
