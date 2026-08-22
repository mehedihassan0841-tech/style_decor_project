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
// Store old form data
$_SESSION["old"] = [

    "full_name" => $full_name,
    "email"     => $email,
    "phone"     => $phone,
    "address"   => $address,
    "role"      => $role

];
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

    $_SESSION["error"] = "Please fill all required fields.";

header("Location: ../register.php");

exit();

}

// ==============================
// Email Validation
// ==============================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

   $_SESSION["error"] = "Invalid Email Address.";

header("Location: ../register.php");

exit();

}

// ==============================
// Phone Validation
// ==============================

if (!preg_match('/^01[3-9][0-9]{8}$/', $phone)) {

    $_SESSION["error"] = "Invalid Phone Number.";

header("Location: ../register.php");

exit();

}

// ==============================
// Password Length
// ==============================

if (strlen($password) < 8) {

    $_SESSION["error"] = "Password must be at least 8 characters.";

    header("Location: ../register.php");

    exit();

}

// ==============================
// Password Match
// ==============================

if ($password != $confirm_password) {

    $_SESSION["error"] = "Passwords do not match.";

header("Location: ../register.php");

exit();

}

// ==============================
// Role Validation
// ==============================

if ($role != "customer" && $role != "decorator") {

    $_SESSION["error"] = "Invalid Role Selected.";

    header("Location: ../register.php");

    exit();

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

    // নতুন User ID
    $user_id = $conn->insert_id;

    // যদি Decorator হয় তাহলে Profile Table-এ Row তৈরি করবে
    if ($role == "decorator") {

        $verification_status = "pending";

        $sql2 = "INSERT INTO decorator_profiles
        (user_id, verification_status)
        VALUES (?, ?)";

        $stmt2 = $conn->prepare($sql2);

        $stmt2->bind_param("is", $user_id, $verification_status);

        $stmt2->execute();

        $stmt2->close();
    }

    $_SESSION["success"] = "Registration Successful! Please Login.";

    header("Location: ../login.php");
    exit();

} else {

    die("Registration Failed.");

}

$stmt->close();

$conn->close();

?>