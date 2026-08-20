<?php

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {

    // Local XAMPP Database
    $host = "localhost";
    $dbname = "styledecor";
    $username = "root";
    $password = "";

} else {

    // InfinityFree Live Database
    $host = "sql213.infinityfree.com";
$dbname = "if0_42473841_styledecor";
$username = "if0_42473841";
$password = "1234Re4321";

}

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>