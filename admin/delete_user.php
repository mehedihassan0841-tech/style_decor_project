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

header("Location: services.php");

exit();

}

$id=(int)$_GET["id"];

/*==============================
GET IMAGE
==============================*/

$result=mysqli_query($conn,"

SELECT service_image

FROM decorator_services

WHERE id='$id'

");

if(mysqli_num_rows($result)==0){

header("Location: services.php");

exit();

}

$row=mysqli_fetch_assoc($result);

$image=$row["service_image"];

/*==============================
DELETE IMAGE
==============================*/

if($image!="" && file_exists("../uploads/services/".$image)){

unlink("../uploads/services/".$image);

}

/*==============================
DELETE DATABASE
==============================*/

mysqli_query($conn,"

DELETE FROM decorator_services

WHERE id='$id'

");

header("Location: services.php?deleted=1");

exit();

?>