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

if($_SERVER["REQUEST_METHOD"]!="POST"){

header("Location: services.php");

exit();

}

/*=========================
GET FORM DATA
==========================*/

$decorator_id=mysqli_real_escape_string($conn,$_POST["decorator_id"]);

$service_name=mysqli_real_escape_string($conn,$_POST["service_name"]);

$category=mysqli_real_escape_string($conn,$_POST["category"]);

$price=mysqli_real_escape_string($conn,$_POST["price"]);

$duration=mysqli_real_escape_string($conn,$_POST["duration"]);

$description=mysqli_real_escape_string($conn,$_POST["description"]);

$availability=mysqli_real_escape_string($conn,$_POST["availability"]);

/*=========================
IMAGE UPLOAD
==========================*/

$service_image="";

if(isset($_FILES["service_image"]) && $_FILES["service_image"]["error"]==0){

$upload_dir="../uploads/services/";

if(!is_dir($upload_dir)){

mkdir($upload_dir,0777,true);

}

$image_name=time()."_".basename($_FILES["service_image"]["name"]);

$target=$upload_dir.$image_name;

move_uploaded_file($_FILES["service_image"]["tmp_name"],$target);

$service_image=$image_name;

}

/*=========================
INSERT
==========================*/

$query=mysqli_query($conn,"

INSERT INTO decorator_services(

decorator_id,

service_name,

category,

price,

duration,

description,

service_image,

availability

)

VALUES(

'$decorator_id',

'$service_name',

'$category',

'$price',

'$duration',

'$description',

'$service_image',

'$availability'

)

");

if($query){

header("Location: services.php?added=1");

exit();

}else{

echo "Database Error : ".mysqli_error($conn);

}

?>