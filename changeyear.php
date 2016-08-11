<?php
/*
 * Created by PhpStorm.
 * User: juanito
 * Date: 8/10/16
 * Time: 3:21 PM
 */
session_start();
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking
setcookie("year", $_POST["year"], time() + (86400 * 30), "/");
?>
