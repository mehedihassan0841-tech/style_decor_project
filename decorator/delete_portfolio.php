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
    header("Location: portfolio.php");
    exit();
}

$id = (int) $_GET["id"];
$user_id = $_SESSION["user_id"];

/*
    প্রথমে Image Name বের করবো
*/

$sql = "SELECT image
        FROM decorator_portfolio
        WHERE id = ?
        AND decorator_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $id, $user_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    $_SESSION["error"] = "Portfolio not found.";

    header("Location: portfolio.php");
    exit();
}

$portfolio = $result->fetch_assoc();

$image_path = "../uploads/portfolio/" . $portfolio["image"];

/*
    Database Delete
*/

$sql = "DELETE FROM decorator_portfolio
        WHERE id = ?
        AND decorator_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {

    if (file_exists($image_path)) {

        unlink($image_path);

    }

    $_SESSION["success"] = "Portfolio deleted successfully.";

} else {

    $_SESSION["error"] = "Delete failed.";

}

header("Location: portfolio.php");
exit();

?>