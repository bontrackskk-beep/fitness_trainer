<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

$host = "sql100.infinityfree.com";
$user = "if0_41715306";
$pass = "Sudhangmail";
$dbname = "if0_41715306_zenfit";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
?>