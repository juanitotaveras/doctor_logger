<?php
session_start();

?>
<html>
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Doctor Logger </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="./index.css">

    <!-- Latest compiled and minified JavaScript
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> -->
</head>
<body>
<?php
    if (isset($_SESSION["print_info"])) {
        echo $_SESSION["print_info"];
    }
else {
    echo 'ERROR: $_SESSION["print_info"] is empty.';
}

?>

</body>
<script>
  //  window.print();
</script>
</html>