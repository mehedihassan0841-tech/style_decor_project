<?php

session_start();

require_once("../config/database.php");

// Only POST Request Allowed
if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: ../login.php");
    exit();

}

// ==============================
// Receive Form Data
// ==============================

$email = trim($_POST["email"]);
$password = $_POST["password"];

// Basic Validation
if (empty($email) || empty($password)) {

    $_SESSION["error"] = "Please enter your email and password.";

    header("Location: ../login.php");

    exit();

}

// Email Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["error"] = "Invalid email address.";

    header("Location: ../login.php");

    exit();

}

// ==============================
// Find User by Email
// ==============================

$sql = "SELECT * FROM users WHERE email = ? LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    $_SESSION["error"] = "Email or Password is incorrect.";

    header("Location: ../login.php");

    exit();

}

$user = $result->fetch_assoc();

// ==============================
// Verify Password
// ==============================

if (!password_verify($password, $user["password"])) {

    $_SESSION["error"] = "Email or Password is incorrect.";

    header("Location: ../login.php");

    exit();

}

// ==============================
// Account Status Check
// ==============================

if ($user["status"] == "pending") {

    $_SESSION["error"] = "Your account is waiting for admin approval.";

    header("Location: ../login.php");

    exit();

}

if ($user["status"] == "blocked") {

    $_SESSION["error"] = "Your account has been blocked.";

    header("Location: ../login.php");

    exit();

}

// ==============================
// Create Login Session
// ==============================

$_SESSION["user_id"] = $user["id"];

$_SESSION["user_name"] = $user["full_name"];

$_SESSION["user_email"] = $user["email"];

$_SESSION["user_role"] = $user["role"];

$_SESSION["profile_image"] = $user["profile_image"];

$_SESSION["login_success"] = "Welcome back, " . $user["full_name"] . "!";
// ==============================
// Role Based Redirect
// ==============================

if ($user["role"] == "admin") {

    header("Location: ../admin/dashboard.php");

    exit();

}

elseif ($user["role"] == "customer") {

    header("Location: ../customer/dashboard.php");

    exit();

}

elseif ($user["role"] == "decorator") {

    header("Location: ../decorator/dashboard.php");

    exit();

}

else {

    session_destroy();

    $_SESSION["error"] = "Invalid User Role.";

    header("Location: ../login.php");

    exit();

}