<?php
// db.php - database connection

$host = "localhost";
$user = "root";       // change if your MySQL user is different
$pass = "";           // change if your MySQL has a password
$dbname = "transitops";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
