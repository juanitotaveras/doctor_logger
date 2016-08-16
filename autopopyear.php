<?php
// connect to DB
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
// first get how many doctors there are
$sql = 'SELECT * from names';
$result = $connect->query($sql);
$ids = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($ids, $row["ID"]);
    }
}

/////////////// copypasta///////////////////////////////////////////////////////////////////
$sql = "SELECT * FROM doctor_logger WHERE YEAR = " . $_POST["year"] . " ORDER BY MONTH, DAY";
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
//$sql = "";
$stop = count($ids);
$idx = 0;
$week_end = [5, 6];
for ($i = 0; $i < count($days); $i++) {
    for ($j = 0; $j < count($days[$i]); $j++) {
        if ($idx == $stop) {
            $idx = 0;
        }
        $sql = 'UPDATE doctor_logger SET DOC1=' . $ids[$idx] . ', DOC2=NULL WHERE MONTH=' . $i . ' AND YEAR=' . $_POST["year"] . ' AND DAY=' . $days[$i][$j] . ';';
        $stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
//mysqli_stmt_bind_param ($stmt, 'iii', $date[2], $date[0], $date[1]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if (!in_array($weekdays[$i][$j], $week_end)) {
            $idx ++;
        }
    }
}
/*
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
//mysqli_stmt_bind_param ($stmt, 'iii', $date[2], $date[0], $date[1]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt); */
echo $sql;
