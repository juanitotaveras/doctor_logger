<?php
// connect to DB
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

// first get how many doctors there are
$sql = 'SELECT * from names';
$result = $connect->query($sql);
$ids = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($ids, $row["ID"]);
    }
}

$sql = 'INSERT INTO names VALUES (?, ?, ?, 0)';
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
mysqli_stmt_bind_param ($stmt, 'iss', count($ids), $_POST["first"], $_POST["last"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
echo 'SUCCESS';
