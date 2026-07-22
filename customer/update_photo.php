<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php

session_start();

require_once("../config/database.php");

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: change_photo.php");
    exit();

}

$user_id = $_SESSION["user_id"];

// কোনো ছবি সিলেক্ট করা হয়েছে কিনা

if (!isset($_FILES["profile_image"]) || $_FILES["profile_image"]["error"] != 0) {

    $_SESSION["error"] = "Please select an image.";

    header("Location: change_photo.php");
    exit();

}

$file = $_FILES["profile_image"];

// File information
$file_name = $file["name"];
$file_tmp = $file["tmp_name"];
$file_size = $file["size"];

// File extension check
$extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

$allowed_extensions = ["jpg", "jpeg", "png"];

if (!in_array($extension, $allowed_extensions)) {

    $_SESSION["error"] = "Only JPG, JPEG and PNG images are allowed.";
    header("Location: change_photo.php");
    exit();

}


// File size check (5MB)
if ($file_size > 5 * 1024 * 1024) {

    $_SESSION["error"] = "Image size must be less than 5MB.";
    header("Location: change_photo.php");
    exit();

}


// Create unique file name
$new_file_name = "profile_" . $user_id . "_" . time() . "." . $extension;


// Upload folder
$upload_folder = "../uploads/profile/";


// Check folder exists
if (!is_dir($upload_folder)) {

    mkdir($upload_folder, 0777, true);

}


// Upload image
$upload_path = $upload_folder . $new_file_name;


if (move_uploaded_file($file_tmp, $upload_path)) {


    // Update database

    $sql = "UPDATE users SET profile_image = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("si", $new_file_name, $user_id);


    if ($stmt->execute()) {
        $_SESSION["profile_image"] = $new_file_name;



        $_SESSION["success"] = "Profile picture updated successfully.";

        header("Location: change_photo.php");
        exit();


    } else {


        $_SESSION["error"] = "Database update failed.";

        header("Location: change_photo.php");
        exit();

    }



} else {


    $_SESSION["error"] = "Image upload failed.";

    header("Location: change_photo.php");
    exit();

}