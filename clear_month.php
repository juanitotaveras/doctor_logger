<php
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
$month = $_POST["month"];
$year = $_COOKIE["year"];
$sql = 'UPDATE doctor_logger SET DOC1=NULL, DOC2=NULL WHERE MONTH=' . $month . ' AND YEAR=' . $year . ';';
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
//mysqli_stmt_bind_param ($stmt, 'iii', $date[2], $date[0], $date[1]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
echo 'SUCCESS';
