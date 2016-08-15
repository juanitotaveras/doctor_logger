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


$date = explode("-", $_POST["box"]);
$sql = 'UPDATE doctor_logger SET DOC' . $date[4] . '=NULL WHERE MONTH=' . $date[0] . ' and DAY=' . $date[1] . ' and YEAR=' . $date[2];
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
//mysqli_stmt_bind_param ($stmt, 'iii', $date[2], $date[0], $date[1]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
echo $_POST["box"];
?>