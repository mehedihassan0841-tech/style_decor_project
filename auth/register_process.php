<?php

session_start();

require_once("../config/database.php");

// শুধু POST Request Allow
if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: ../register.php");
    exit();

}

// ==============================
// Receive Form Data
// ==============================

$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$phone = trim($_POST["phone"]);
$address = trim($_POST["address"]);
$role = trim($_POST["role"]);

$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

// HTML Special Characters Convert
$full_name = htmlspecialchars($full_name);
$email = htmlspecialchars($email);
$phone = htmlspecialchars($phone);
$address = htmlspecialchars($address);

// Default Values
$status = "pending";
$profile_image = "default.png";
// ==============================
// Empty Validation
// ==============================

if (

    empty($full_name) ||
    empty($email) ||
    empty($phone) ||
    empty($address) ||
    empty($role) ||
    empty($password) ||
    empty($confirm_password)

) {

    die("All fields are required.");

}

// ==============================
// Email Validation
// ==============================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    die("Invalid Email Address.");

}

// ==============================
// Phone Validation
// ==============================

if (!preg_match('/^01[3-9][0-9]{8}$/', $phone)) {

    die("Invalid Phone Number.");

}

// ==============================
// Password Length
// ==============================

if (strlen($password) < 8) {

    die("Password must be at least 8 characters.");

}

// ==============================
// Password Match
// ==============================

if ($password != $confirm_password) {

    die("Password doesn't match.");

}

// ==============================
// Role Validation
// ==============================

if ($role != "Customer" && $role != "Decorator") {

    die("Invalid Role.");

}
// ==============================
// Email Duplicate Check
// ==============================

$sql = "SELECT id FROM users WHERE email = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $_SESSION["error"] = "Email already exists.";
    header("Location: ../register.php");
exit();

}

$stmt->close();


// ==============================
// Phone Duplicate Check
// ==============================

$sql = "SELECT id FROM users WHERE phone = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $phone);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

   $_SESSION["error"] = "Phone number already exists.";
   header("Location: ../register.php");
exit();

}

$stmt->close();


// ==============================
// Password Hash
// ==============================

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
// ==============================
// Insert User
// ==============================

$sql = "INSERT INTO users
(full_name, email, phone, password, role, profile_image, address, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(

    "ssssssss",

    $full_name,
    $email,
    $phone,
    $hashed_password,
    $role,
    $profile_image,
    $address,
    $status

);

if ($stmt->execute()) {

    $_SESSION["success"] = "Registration Successful! Please Login.";

    header("Location: ../login.php");

    exit();

} else {

    die("Registration Failed.");

}

$stmt->close();

$conn->close();

?>