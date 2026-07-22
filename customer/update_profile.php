<?php

session_start();

require_once("../config/database.php");

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: profile.php");
    exit();

}

$user_id = $_SESSION["user_id"];

$full_name = trim($_POST["full_name"]);
$phone = trim($_POST["phone"]);
$address = trim($_POST["address"]);

// Validation

if(empty($full_name) || empty($phone) || empty($address)){

    $_SESSION["error"] = "All fields are required.";

    header("Location: profile.php");

    exit();

}

// Update Query

$sql = "UPDATE users
SET full_name = ?, phone = ?, address = ?
WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(

    "sssi",

    $full_name,
    $phone,
    $address,
    $user_id

);

if($stmt->execute()){

    $_SESSION["success"] = "Profile updated successfully.";

    $_SESSION["user_name"] = $full_name;

}else{

    $_SESSION["error"] = "Profile update failed.";

}

header("Location: profile.php");

exit();

?>