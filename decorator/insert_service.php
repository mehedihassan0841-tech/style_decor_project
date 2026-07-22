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

$decorator_id = $_SESSION["user_id"];

$service_name = trim($_POST["service_name"]);
$category = trim($_POST["category"]);
$price = trim($_POST["price"]);
$duration = trim($_POST["duration"]);
$description = trim($_POST["description"]);
$availability = trim($_POST["availability"]);

/*==========================
IMAGE UPLOAD
==========================*/

$image = $_FILES["service_image"]["name"];
$tmp = $_FILES["service_image"]["tmp_name"];
$error = $_FILES["service_image"]["error"];

if ($error != 0) {

    $_SESSION["error"] = "Please select an image.";

    header("Location:add_service.php");
    exit();
}

$extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

$allowed = ["jpg","jpeg","png","webp"];

if (!in_array($extension,$allowed)) {

    $_SESSION["error"] = "Invalid image format.";

    header("Location:add_service.php");
    exit();
}

$new_image = time()."_".uniqid().".".$extension;

move_uploaded_file(
    $tmp,
    "../uploads/services/".$new_image
);

/*==========================
DATABASE INSERT
==========================*/

$sql = "INSERT INTO decorator_services
(
decorator_id,
service_name,
category,
price,
duration,
description,
service_image,
availability
)

VALUES(?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"issdssss",
$decorator_id,
$service_name,
$category,
$price,
$duration,
$description,
$new_image,
$availability
);

if($stmt->execute()){

    $_SESSION["success"]="Service added successfully.";

}else{

    $_SESSION["error"]="Database insert failed.";

}

header("Location:manage_services.php");
exit();

?>