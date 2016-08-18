<?php

ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking
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
// delete everything in table
$sql = 'TRUNCATE TABLE names;';
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);



// now replace with new values from post
$fnames = explode(",",$_POST["fnames"]);
$lnames = explode(",", $_POST["lnames"]);
$checks = explode(",", $_POST["checks"]);

for ($i = 0; $i < count($fnames); $i++) {
    if ($checks[$i] == "true") {
        $ccc = 1;
    }
    else {
        $ccc = 0;
    }
    $sql = 'INSERT INTO names VALUES (?, ?, ?, ?)';
    $stmt = mysqli_prepare($connect, $sql);  // fill in the blank
    mysqli_stmt_bind_param($stmt, 'issi', $i, $fnames[$i], $lnames[$i], $ccc);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
echo $_POST["fnames"];
