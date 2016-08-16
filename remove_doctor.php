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

$id = $_POST["id"];
$sql = 'DELETE FROM names WHERE ID=?';
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
mysqli_stmt_bind_param ($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


// get docs
$sql = 'SELECT * from names';
$result = $connect->query($sql);
$fnames = [];
$lnames = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($fnames, $row["FIRST_NAME"]);
        array_push($lnames, $row["LAST_NAME"]);
    }
}


// also set to null wherever this doc appears
$sql = 'UPDATE doctor_logger SET DOC1=NULL WHERE DOC1=?;';
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
mysqli_stmt_bind_param ($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// also set to null wherever this doc appears
$sql = 'UPDATE doctor_logger SET DOC2=NULL WHERE DOC2=?;';
$stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
mysqli_stmt_bind_param ($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


for ($i = 0; $i < count($ids); $i++) {
    // update w/ correct id
    $sql = 'UPDATE doctor_logger SET ID=' . $i . ' WHERE FIRST_NAME=' . $fnames[$i] . ' AND LAST_NAME=' . $lnames[$i] . ';';
    $stmt = mysqli_prepare ($connect, $sql);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
echo 'SUCCESS';
