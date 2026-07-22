<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

if (!isset($_GET["id"])) {
    header("Location: manage_services.php");
    exit();
}

$id = (int)$_GET["id"];
$user_id = $_SESSION["user_id"];

/*==========================
GET IMAGE
==========================*/

$sql = "SELECT service_image
        FROM decorator_services
        WHERE id=?
        AND decorator_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii",$id,$user_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    $_SESSION["error"]="Service not found.";

    header("Location:manage_services.php");
    exit();

}

$service = $result->fetch_assoc();

$image_path = "../uploads/services/".$service["service_image"];

/*==========================
DELETE DATABASE
==========================*/

$sql = "DELETE FROM decorator_services
        WHERE id=?
        AND decorator_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii",$id,$user_id);

if($stmt->execute()){

    if(file_exists($image_path)){

        unlink($image_path);

    }

    $_SESSION["success"]="Service deleted successfully.";

}else{

    $_SESSION["error"]="Delete failed.";

}

header("Location:manage_services.php");
exit();

?>