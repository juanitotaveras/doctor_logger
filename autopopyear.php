<?php
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking
// connect to DB
$file = fopen("../../../db.txt", "r") or die("Error opening file.");
$dbinfo = [];
while (!feof($file)) {
    $info = trim(fgets($file));
    array_push($dbinfo, $info);
}
$connect = mysqli_connect($dbinfo[0], $dbinfo[1], $dbinfo[2], $dbinfo[3], $dbinfo[4]);
if (empty($connect)) {
    die ("mysqli_connect failed " . mysqli_connect_error());
}

/////////////// fetch stats///////////////////////////////////////////////////////////////////
$year = $_POST["year"];
$sql = "SELECT * FROM doctor_logger WHERE YEAR = " . $year. " ORDER BY MONTH, DAY";
$result = $connect->query($sql);
$days = [];
$weekdays = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cur_day = $row["DAY"];
        if ($cur_day == "1") {
            if (isset($days_list) && isset($weekdays_list)) {
                array_push($days, $days_list);
                array_push($weekdays, $weekdays_list);
            }
            $days_list = [];
            $weekdays_list = [];
            array_push($days_list, $cur_day);
            array_push($weekdays_list, $row["WEEKDAY"]);
        }
        else {
            array_push($days_list, $cur_day);
            array_push($weekdays_list, $row["WEEKDAY"]);
        }
    }
    array_push($days, $days_list);
    array_push($weekdays, $weekdays_list);
}


//////////////////////////////////////////////////////////////////////////////////////////////////

$ord = explode("-", $_POST["order"]);
$mselect = explode("-", $_POST["months"]);
$stop = count($ord);
$idx = 0;
$week_end = [5, 6];
for ($i = 0; $i < count($mselect); $i++) { // for every month in our list
    for ($j = 0; $j < count($days[$mselect[$i]]); $j++) {   // for every day in that month
        if ($idx == $stop) {
            $idx = 0;
        }
        $sql = 'UPDATE doctor_logger SET DOC1=' . $ord[$idx] . ', DOC2=NULL WHERE MONTH=' . $mselect[$i] . ' AND YEAR=' . $year . ' AND DAY=' . $days[$mselect[$i]][$j] . ';';
        $stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if (!in_array($weekdays[$mselect[$i]][$j], $week_end)) { // if our current day is not in the weekend, switch to next doctor
            $idx ++;
        }
    }
}
echo "SUCCESS";
