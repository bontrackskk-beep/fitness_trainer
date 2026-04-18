<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "fitness_trainer"; // Updated to match your screenshot

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>