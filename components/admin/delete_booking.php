<?php

session_start();

if(!isset($_SESSION["user_id"])){

    header("Location: ../login.php");
    exit();

}

if($_SESSION["user_role"] != "admin"){

    header("Location: ../login.php");
    exit();

}

require_once("../config/database.php");

if(!isset($_GET["id"])){

    header("Location: bookings.php");
    exit();

}

$id = (int)$_GET["id"];

$query = mysqli_query(
    $conn,
    "DELETE FROM bookings WHERE id='$id'"
);

if($query){

    header("Location: bookings.php?deleted=1");
    exit();

}else{

    die("Delete failed: " . mysqli_error($conn));

}

?>