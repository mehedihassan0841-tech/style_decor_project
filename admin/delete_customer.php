<?php

session_start();

if(!isset($_SESSION["user_id"])){

    header("Location: ../login.php");
    exit();

}

if($_SESSION["user_role"]!="admin"){

    header("Location: ../login.php");
    exit();

}

require_once("../config/database.php");

if(!isset($_GET["id"])){

    header("Location: customers.php");
    exit();

}

$id=(int)$_GET["id"];

/* Get Profile Image */

$get=mysqli_query($conn,"
SELECT profile_image
FROM users
WHERE id='$id'
AND role='customer'
");

if(mysqli_num_rows($get)==0){

    header("Location: customers.php");
    exit();

}

$user=mysqli_fetch_assoc($get);

/* Delete Image */

if(!empty($user["profile_image"])){

    $image="../uploads/profile/".$user["profile_image"];

    if(file_exists($image)){

        unlink($image);

    }

}

/* Delete Customer */

mysqli_query($conn,"
DELETE FROM users
WHERE id='$id'
AND role='customer'
");

header("Location: customers.php?deleted=1");

exit();

?>