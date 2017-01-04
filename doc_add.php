<?php
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


$date = explode("-", $_POST["date"]);
$doc = $_POST["doc"];
// check if val is null
$sql = 'SELECT DOC1, DOC2 FROM doctor_logger WHERE MONTH=' . $date[0] . ' and DAY=' . $date[1] . ' and YEAR=' . $date[2];
$result = $connect->query($sql);
$row = $result->fetch_assoc();
if ($row["DOC1"] == $doc || $row["DOC2"] == $doc) {
    echo "already_added";
}
else if ($row["DOC1"] == NULL) {  // if no doc has been added to it yet, add to doc 1
    $sql = 'UPDATE doctor_logger SET DOC1=' . $doc . ' WHERE MONTH=' . $date[0] . ' and DAY=' . $date[1] . ' and YEAR=' . $date[2];
    $stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
//mysqli_stmt_bind_param ($stmt, 'iii', $date[2], $date[0], $date[1]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "doc_1_added " . $_POST["date"] . "docname-1 " . $doc;
}
else if ($row["DOC2"] == NULL) {
    $sql = 'UPDATE doctor_logger SET DOC2=' . $doc . ' WHERE MONTH=' . $date[0] . ' and DAY=' . $date[1] . ' and YEAR=' . $date[2];
    $stmt = mysqli_prepare ($connect, $sql);  // fill in the blank
//mysqli_stmt_bind_param ($stmt, 'iii', $date[2], $date[0], $date[1]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "doc_2_added " . $_POST["date"] . "docname-2 " . $doc;
}

else {
    echo "full";
}
?>