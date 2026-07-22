<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

$user_id = $_SESSION["user_id"];

// Form Data
$company_name   = trim($_POST["company_name"]);
$experience     = trim($_POST["experience"]);
$bio            = trim($_POST["bio"]);
$specialization = trim($_POST["specialization"]);
$district       = trim($_POST["district"]);
$address        = trim($_POST["address"]);
$contact_number = trim($_POST["contact_number"]);
$facebook       = trim($_POST["facebook"]);
$instagram      = trim($_POST["instagram"]);
$website        = trim($_POST["website"]);

// Update Query
$sql = "UPDATE decorator_profiles SET
        company_name = ?,
        experience = ?,
        bio = ?,
        specialization = ?,
        district = ?,
        address = ?,
        contact_number = ?,
        facebook = ?,
        instagram = ?,
        website = ?,
        updated_at = NOW()
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sissssssssi",
    $company_name,
    $experience,
    $bio,
    $specialization,
    $district,
    $address,
    $contact_number,
    $facebook,
    $instagram,
    $website,
    $user_id
);

if ($stmt->execute()) {

    $_SESSION["success"] = "Profile updated successfully.";

} else {

    $_SESSION["error"] = "Profile update failed.";

}

header("Location: profile.php");
exit();

?>