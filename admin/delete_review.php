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

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])){
    header("Location: reviews.php");
    exit();
}


$id = (int)$_GET["id"];


/* CHECK REVIEW */

$check = mysqli_query(
    $conn,
    "SELECT id FROM reviews WHERE id='$id' LIMIT 1"
);

if(mysqli_num_rows($check) == 0){

    header("Location: reviews.php?delete_error=1");
    exit();

}


/* DELETE */

$delete = mysqli_query(
    $conn,
    "DELETE FROM reviews WHERE id='$id'"
);


if($delete){

    header("Location: reviews.php?deleted=1");
    exit();

}else{

    header("Location: reviews.php?delete_error=1");
    exit();

}

?>