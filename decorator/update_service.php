<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: manage_services.php");
    exit();
}

require_once("../config/database.php");

$user_id = $_SESSION["user_id"];

$id = (int)$_POST["id"];

$service_name = trim($_POST["service_name"]);
$category = trim($_POST["category"]);
$price = trim($_POST["price"]);
$duration = trim($_POST["duration"]);
$description = trim($_POST["description"]);
$availability = trim($_POST["availability"]);

$old_image = $_POST["old_image"];

/*==========================
IMAGE UPDATE
==========================*/

$new_image = $old_image;

if(!empty($_FILES["service_image"]["name"])){

    $image = $_FILES["service_image"]["name"];

    $tmp = $_FILES["service_image"]["tmp_name"];

    $extension = strtolower(pathinfo($image,PATHINFO_EXTENSION));

    $allowed = ["jpg","jpeg","png","webp"];

    if(in_array($extension,$allowed)){

        $new_image = time()."_".uniqid().".".$extension;

        move_uploaded_file(
            $tmp,
            "../uploads/services/".$new_image
        );

        if(file_exists("../uploads/services/".$old_image)){

            unlink("../uploads/services/".$old_image);

        }

    }

}

/*==========================
DATABASE UPDATE
==========================*/

$sql = "UPDATE decorator_services
SET

service_name=?,
category=?,
price=?,
duration=?,
description=?,
service_image=?,
availability=?

WHERE id=?
AND decorator_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(

"ssdssssii",

$service_name,
$category,
$price,
$duration,
$description,
$new_image,
$availability,
$id,
$user_id

);

if($stmt->execute()){

    $_SESSION["success"]="Service updated successfully.";

}else{

    $_SESSION["error"]="Update failed.";

}

header("Location: manage_services.php");
exit();

?>