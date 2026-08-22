<?php

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {

    // Local XAMPP Database
    $host = "localhost";
    $dbname = "styledecor";
    $username = "root";
    $password = "";

} else {

    // InfinityFree Live Database
    $host = "sql205.infinityfree.com";
$dbname = "if0_42702045_styledecor";
$username = "if0_42702045";
$password = "boytTCuEqDbDqA";

}

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>