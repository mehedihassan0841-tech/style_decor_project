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

$decorator_id = $_SESSION["user_id"];

// Form Data
$title = trim($_POST["title"]);
$description = trim($_POST["description"]);

// Image
$image = $_FILES["image"]["name"];
$tmp_name = $_FILES["image"]["tmp_name"];
$error = $_FILES["image"]["error"];

// Image Validation
if ($error != 0) {

    $_SESSION["error"] = "Please select an image.";

    header("Location: add_portfolio.php");
    exit();
}

// Extension
$extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

$allowed = ["jpg", "jpeg", "png", "webp"];

if (!in_array($extension, $allowed)) {

    $_SESSION["error"] = "Only JPG, JPEG, PNG and WEBP images are allowed.";

    header("Location: add_portfolio.php");
    exit();
}

// Unique File Name
$new_image = time() . "_" . uniqid() . "." . $extension;

$upload_path = "../uploads/portfolio/" . $new_image;

// Upload Image
if (!move_uploaded_file($tmp_name, $upload_path)) {

    $_SESSION["error"] = "Image upload failed.";

    header("Location: add_portfolio.php");
    exit();
}

// Insert Database
$sql = "INSERT INTO decorator_portfolio
(decorator_id, image, title, description)
VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "isss",
    $decorator_id,
    $new_image,
    $title,
    $description
);

if ($stmt->execute()) {

    $_SESSION["success"] = "Portfolio uploaded successfully.";

} else {

    $_SESSION["error"] = "Database insert failed.";

}

header("Location: portfolio.php");
exit();

?>