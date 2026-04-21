<?php
$host = "sql100.infinityfree.com";
$user = "if0_41715306";
$pass = "Sudhangmail";
$dbname = "Zenfit";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
?>