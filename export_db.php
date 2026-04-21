<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "fitness_trainer";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die("Connection failed"); }

$sql = "";
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($result)) {
    $table = $row[0];
    $sql .= "-- Table: $table\n";
    $sql .= "DROP TABLE IF EXISTS `$table`;\n";
    
    $res = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
    $row2 = mysqli_fetch_array($res);
    $sql .= $row2[1] . ";\n\n";
    
    $res = mysqli_query($conn, "SELECT * FROM `$table`");
    while ($data = mysqli_fetch_assoc($res)) {
        $values = array_map(function($v) use ($conn) {
            return $v === null ? "NULL" : "'" . mysqli_real_escape_string($conn, $v) . "'";
        }, array_values($data));
        $sql .= "INSERT INTO `$table` (" . implode(", ", array_keys($data)) . ") VALUES (" . implode(", ", $values) . ");\n";
    }
    $sql .= "\n";
}

file_put_contents("database.sql", $sql);
echo "Exported to database.sql";
?>
