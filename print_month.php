<?php
/*
 * Created by PhpStorm.
 * User: juanito
 * Date: 8/16/16
 * Time: 3:21 PM
 */
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking
session_start();

$file = fopen("../../db.txt", "r") or die("Error opening file.");
$dbinfo = [];
while (!feof($file)) {
    $info = trim(fgets($file));
    array_push($dbinfo, $info);
}
$connect = mysqli_connect($dbinfo[0], $dbinfo[1], $dbinfo[2], $dbinfo[3], $dbinfo[4]);
if (empty($connect)) {
    die ("mysqli_connect failed " . mysqli_connect_error());
}

function month_gen_simple($cur_mon, $days, $weekdays, $year, $doc1_list, $doc2_list, $docs) {
    $week = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];
    $mon_list = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    $str = '
  <div class="row"> <!-- row with month name -->
    <div class="col-xs-5"></div>
    <div class="col-xs-2">
      <h1>' . $mon_list[$cur_mon] .
        '</h1>
    </div>
        <div class="col-xs-5"></div>
  </div> <!-- end row with month name -->
  <div class="row">
    <div class="col-xs-1 left-edge-print">
    </div>';

    for ($x = 0; $x < count($week); $x++) {
        $str .= '<div class="col-xs-1 weekbox"><h3>' . $week[$x] . '</h3></div>';
    }
    $str .= '<div class="col-xs-4"></div></div> <!-- end row of week days header -->';
    $head = 0;
    $end = 1;
    for ($i = 0; $i < 6; $i++) {
        $str .= '
  		<div class="row block-' . strval($cur_mon) . '">
  		<div class="col-xs-1 left-edge-print"></div>';
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
                    $str .= '<div class="col-xs-1 col-md-1 before"></div>';
                }
                else {
                    $daynum = $days[$cur_mon][$head];
                    $head ++;
                    $docid = (strval($cur_mon) . '-' . strval($daynum) . '-'. strval($year) . '-'. 'docname');
                    if (isset($doc1_list[$cur_mon][$daynum - 1])) {
                        $dox = '<span class="docname n1" id="' . $docid . '-1">' .  $docs[($doc1_list[$cur_mon][$daynum - 1])][2] . '</span>';
                    }
                    else {
                        $dox = '';
                    }
                    if (isset($doc2_list[$cur_mon][$daynum - 1])) {
                        $dox .=  '<span class="docname n2" id="' . $docid . '-2">' .  $docs[($doc2_list[$cur_mon][$daynum - 1])][2] .  '</span>';
                    }
                    $str .= '<div class="col-xs-1 col-md-1 norm day" id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">' . strval($daynum) .  $dox . '</div>';
                }
            }
            else if (($i > 3) && (count($days[$cur_mon]) <= $head)) {
                $str .= '<div class="col-xs-1 col-md-1 after"></div>';
                $end++;
            }
            else {
                $daynum = $days[$cur_mon][$head];
                $head ++;
                $docid = (strval($cur_mon) . '-' . strval($daynum) . '-'. strval($year) . '-'. 'docname');
                if (isset($doc1_list[$cur_mon][$daynum - 1])) {
                    $dox = '<span class="docname n1" id="' . $docid . '-1">' . $docs[($doc1_list[$cur_mon][$daynum - 1])][2] . '</span>';
                }
                else {
                    $dox = '';
                }
                if (isset($doc2_list[$cur_mon][$daynum - 1])) {
                    $dox .=  '<span class="docname n2" id="' . $docid . '-2">' . $docs[($doc2_list[$cur_mon][$daynum - 1])][2] . '</span>';
                }
                $str .= '<div class="col-xs-1 col-md-1 norm day" id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">' . strval($daynum) . $dox . '</div>';
            }
        }
        $str .= '<div class="col-xs-4"></div></div>';
    }
    return $str;
}
// end month generator function

//$sql = "SELECT * FROM doctor_logger WHERE YEAR = " . $_COOKIE["year"] . " AND MONTH =" . $_POST["mon"] .  " ORDER BY DAY";
$sql = "SELECT * FROM doctor_logger WHERE YEAR=" . $_COOKIE["year"] . " ORDER BY MONTH, DAY";
$result = $connect->query($sql);
$days = [];
$weekdays = [];
$docs1 = [];
$docs2 = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cur_day = $row["DAY"];
        if ($cur_day == "1") {
            if (isset($days_list) && isset($weekdays_list)) {
                array_push($days, $days_list);
                array_push($weekdays, $weekdays_list);
                array_push($docs1, $doc1_list);
                array_push($docs2, $doc2_list);

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
    array_push($docs1, $doc1_list);
    array_push($docs2, $doc2_list);
}

// fetch docs
$sql = "SELECT * FROM names ORDER BY ID ASC;";
$result = $connect->query($sql);
$docs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $basket = [];
        array_push($basket, $row["ID"]);
        array_push($basket, $row["FIRST_NAME"]);
        array_push($basket, $row["LAST_NAME"]);
        array_push($basket, $row["CLASS"]);
        array_push($docs, $basket);
    }
}

$output = month_gen_simple($_POST["mon"], $days, $weekdays, $_COOKIE["year"], $docs1, $docs2, $docs);

// now add our panel with our stats ////////////////////////////////////////////////////////////////////////////////////
$output .= '
<div class="container rpan-print">
  <div class="row">
    <div class="col-xs-5 doc-col"><b>Doctor</b></div>
    <div class="col-xs-3 yr-col"><b>Year</b></div>
    <div class="col-xs-3 mon-col"><b>Month</b></div>

</div> <!-- end row with names -->
    <!-- fetch doctors -->';

for ($h = 0; $h < count($docs); $h++) {
	$doc_id = $docs[$h][0];
	$output .= '<div class="row">
          <div class="col-xs-5 doc-col doc-print" > ' . $docs[$h][2] . '</div>
	      <div class="col-xs-1 tot-col year-print" style="color:green">
	      <!-- fetch how many days for that year -->';
	$total_year = 0;
	for ($a = 0; $a < count($docs1); $a++) {
		for ($b = 0; $b < count($docs1[$a]); $b++) {
			if ($doc_id == $docs1[$a][$b] || $doc_id == $docs2[$a][$b]) {
				$total_year ++;
			}
		}
	}
    // now fetch how many weekends per year
    $total_weekends = 0;
    $week_end = [5, 6, 0];
    $tab = 0;
    for ($a = 0; $a < count($docs1); $a++) {
        for ($b = 0; $b < count($docs1[$a]); $b++) {
            if ($doc_id == $docs1[$a][$b] || $doc_id == $docs2[$a][$b]) {
                if (in_array($weekdays[$a][$b], $week_end)){ // only count if it's three days in a row
                    $tab++;
                } else {
    $tab = 0;
}
                if ($tab > 2) {
                    $total_weekends ++;
                }
            }
        }
    }

    $total_month = 0;
    $m = $_POST["mon"];
    for ($b = 0; $b < count($docs1[$m]); $b++) {
        if ($doc_id == $docs1[$m][$b] || $doc_id == $docs2[$m][$b]) {
            $total_month ++;
        }
    }


    $output .= $total_year . '</div>';
    $output .= '<div class="col-xs-1 tot-col week-print" style="color:orange" >';
    $output .= $total_weekends;

	// now get total for current month
	$output .= '
		  </div> <!-- end total days -->
		  <div class="col-xs-1 mon-col mon-print" style="color:blue">' . $total_month . '</div>
	      </div> <!-- end unique doctor row -->
		  <!--</div> end row -->																	';
}

$output .= '
  </div> <!-- end right-panel-contract -->';

////////////////// END PANEL MAKER //////////////////////////
$_SESSION["print_info"] = $output;



if (isset($_SESSION["print_info"])) {
    echo "print_info is set";
}