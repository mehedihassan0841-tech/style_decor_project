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

$id=(int)$_POST["id"];

$decorator_id=mysqli_real_escape_string($conn,$_POST["decorator_id"]);

$service_name=mysqli_real_escape_string($conn,$_POST["service_name"]);

$category=mysqli_real_escape_string($conn,$_POST["category"]);

$price=mysqli_real_escape_string($conn,$_POST["price"]);

$duration=mysqli_real_escape_string($conn,$_POST["duration"]);

$description=mysqli_real_escape_string($conn,$_POST["description"]);

$availability=mysqli_real_escape_string($conn,$_POST["availability"]);

/* OLD IMAGE */

$result=mysqli_query($conn,"

SELECT service_image

FROM decorator_services

WHERE id='$id'

");

$row=mysqli_fetch_assoc($result);

$image=$row["service_image"];

/* NEW IMAGE */

if(isset($_FILES["service_image"]) && $_FILES["service_image"]["error"]==0){

if($image!="" && file_exists("../uploads/services/".$image)){

unlink("../uploads/services/".$image);

}

$image=time()."_".$_FILES["service_image"]["name"];

move_uploaded_file(

$_FILES["service_image"]["tmp_name"],

"../uploads/services/".$image

);

}

mysqli_query($conn,"

UPDATE decorator_services

SET

decorator_id='$decorator_id',

service_name='$service_name',

category='$category',

price='$price',

duration='$duration',

description='$description',

availability='$availability',

service_image='$image'

WHERE id='$id'

");

header("Location: services.php?updated=1");

exit();

?>