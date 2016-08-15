<?php
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

$uname = $_POST["uname"];
$pass = $_POST["pass"];
$temp = $pass;
$pass = md5($pass); // encrypt
$sql = "SELECT * FROM accounts;";
$result = $connect->query($sql);
$check = FALSE;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $u = $row["username"];
        $p = $row["password"];
        if ($uname == $u && $pass == $p) {
            $check = TRUE;
        }
    }
}

if ($check) {
    // set cookie to logged in
    echo "PASS";
    setcookie("logged_in", "true", time() + (30*30), "/");
}
else {
    echo "FAIL";
}
