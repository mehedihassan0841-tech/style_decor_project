<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: portfolio.php");
    exit();
}

require_once("../config/database.php");

$user_id = $_SESSION["user_id"];

$id = (int)$_POST["id"];

$title = trim($_POST["title"]);
$description = trim($_POST["description"]);

$old_image = $_POST["old_image"];

/*
--------------------------------
Image Upload Check
--------------------------------
*/

$new_image = $old_image;

if (!empty($_FILES["image"]["name"])) {

    $image = $_FILES["image"]["name"];
    $tmp = $_FILES["image"]["tmp_name"];
    $error = $_FILES["image"]["error"];

    if ($error == 0) {

        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        $allowed = ["jpg","jpeg","png","webp"];

        if (in_array($extension,$allowed)) {

            $new_image = time()."_".uniqid().".".$extension;

            move_uploaded_file(
                $tmp,
                "../uploads/portfolio/".$new_image
            );

            if(file_exists("../uploads/portfolio/".$old_image)){

                unlink("../uploads/portfolio/".$old_image);

            }

        }

    }

}

/*
--------------------------------
Database Update
--------------------------------
*/

$sql = "UPDATE decorator_portfolio
SET
title=?,
description=?,
image=?
WHERE id=?
AND decorator_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"sssii",
$title,
$description,
$new_image,
$id,
$user_id
);

if($stmt->execute()){

    $_SESSION["success"]="Portfolio updated successfully.";

}else{

    $_SESSION["error"]="Update failed.";

}

header("Location: portfolio.php");
exit();

?>