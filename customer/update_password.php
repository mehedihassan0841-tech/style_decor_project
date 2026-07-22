<?php

session_start();

require_once("../config/database.php");

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: change_password.php");
    exit();

}

$user_id = $_SESSION["user_id"];

$current_password = $_POST["current_password"];
$new_password = $_POST["new_password"];
$confirm_password = $_POST["confirm_password"];
if (
    empty($current_password) ||
    empty($new_password) ||
    empty($confirm_password)
) {

    $_SESSION["error"] = "All fields are required.";

    header("Location: change_password.php");
    exit();

}

if(strlen($new_password) < 8){

    $_SESSION["error"] = "New password must be at least 8 characters.";

    header("Location: change_password.php");
    exit();

}

if($new_password != $confirm_password){

    $_SESSION["error"] = "New passwords do not match.";

    header("Location: change_password.php");
    exit();

}
$sql = "SELECT password FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();
if(!password_verify($current_password, $user["password"])){

    $_SESSION["error"] = "Current password is incorrect.";

    header("Location: change_password.php");

    exit();

}
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$sql = "UPDATE users SET password = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(

    "si",

    $new_hashed_password,

    $user_id

);
if($stmt->execute()){

    $_SESSION["success"] = "Password changed successfully.";

}else{

    $_SESSION["error"] = "Password update failed.";

}

header("Location: change_password.php");

exit();