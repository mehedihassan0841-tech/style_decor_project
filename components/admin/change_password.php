<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");


/* =========================================
   COLLECT INPUT
========================================= */

$current_password = $_POST["current_password"] ?? "";
$new_password = $_POST["new_password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

$admin_id = (int)$_SESSION["user_id"];


/* =========================================
   BASIC VALIDATION
========================================= */

if (
    $current_password === "" ||
    $new_password === "" ||
    $confirm_password === ""
) {

    $_SESSION["settings_error"] =
        "All password fields are required.";

    header("Location: settings.php#security");
    exit();
}


if ($new_password !== $confirm_password) {

    $_SESSION["settings_error"] =
        "New password and confirm password do not match.";

    header("Location: settings.php#security");
    exit();
}


if (strlen($new_password) < 6) {

    $_SESSION["settings_error"] =
        "New password must be at least 6 characters long.";

    header("Location: settings.php#security");
    exit();
}


/* =========================================
   GET CURRENT PASSWORD HASH
========================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT password
     FROM users
     WHERE id = ?
     AND role = 'admin'
     LIMIT 1"
);


if (!$stmt) {

    $_SESSION["settings_error"] =
        "Unable to verify password.";

    header("Location: settings.php#security");
    exit();
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $admin_id
);

mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* =========================================
   CHECK ADMIN ACCOUNT
========================================= */

if (!$user) {

    $_SESSION["settings_error"] =
        "Admin account not found.";

    header("Location: settings.php#security");
    exit();
}


/* =========================================
   VERIFY CURRENT PASSWORD
========================================= */

if (
    !password_verify(
        $current_password,
        $user["password"]
    )
) {

    $_SESSION["settings_error"] =
        "Current password is incorrect.";

    header("Location: settings.php#security");
    exit();
}


/* =========================================
   PREVENT SAME PASSWORD
========================================= */

if (
    password_verify(
        $new_password,
        $user["password"]
    )
) {

    $_SESSION["settings_error"] =
        "New password cannot be the same as current password.";

    header("Location: settings.php#security");
    exit();
}


/* =========================================
   CREATE NEW PASSWORD HASH
========================================= */

$new_hash = password_hash(
    $new_password,
    PASSWORD_DEFAULT
);


/* =========================================
   UPDATE PASSWORD
========================================= */

$update_stmt = mysqli_prepare(
    $conn,
    "UPDATE users
     SET password = ?
     WHERE id = ?
     AND role = 'admin'"
);


if (!$update_stmt) {

    $_SESSION["settings_error"] =
        "Unable to prepare password update.";

    header("Location: settings.php#security");
    exit();
}


/* =========================================
   BIND PARAMETERS
========================================= */

mysqli_stmt_bind_param(
    $update_stmt,
    "si",
    $new_hash,
    $admin_id
);


/* =========================================
   EXECUTE UPDATE
========================================= */

if (mysqli_stmt_execute($update_stmt)) {

    $_SESSION["settings_success"] =
        "Password changed successfully.";

} else {

    $_SESSION["settings_error"] =
        "Unable to change password.";
}


mysqli_stmt_close($update_stmt);


/* =========================================
   REDIRECT
========================================= */

header("Location: settings.php#security");
exit();

?>