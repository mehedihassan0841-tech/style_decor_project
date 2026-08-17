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


// =========================================
// GET DATA
// =========================================

$user_id = isset($_POST["user_id"])
    ? (int)$_POST["user_id"]
    : 0;

$action = $_POST["action"] ?? "";


// =========================================
// VALIDATE USER ID
// =========================================

if ($user_id <= 0) {

    $_SESSION["settings_error"] =
        "Invalid user request.";

    header("Location: pending_requests.php");
    exit();
}


// =========================================
// DETERMINE NEW STATUS
// =========================================

if ($action == "approve") {

    $new_status = "approved";

} elseif ($action == "block") {

    $new_status = "blocked";

} else {

    $_SESSION["settings_error"] =
        "Invalid request action.";

    header("Location: pending_requests.php");
    exit();
}


// =========================================
// UPDATE USER STATUS
// =========================================

$sql = "
    UPDATE users
    SET status = ?
    WHERE id = ?
      AND role IN ('customer', 'decorator')
";


$stmt = mysqli_prepare($conn, $sql);


if (!$stmt) {

    $_SESSION["settings_error"] =
        "Unable to process request.";

    header("Location: pending_requests.php");
    exit();
}


mysqli_stmt_bind_param(
    $stmt,
    "si",
    $new_status,
    $user_id
);


if (mysqli_stmt_execute($stmt)) {

    if ($action == "approve") {

        $_SESSION["settings_success"] =
            "User approved successfully.";

    } else {

        $_SESSION["settings_success"] =
            "User blocked successfully.";
    }

} else {

    $_SESSION["settings_error"] =
        "Unable to update user status.";
}


mysqli_stmt_close($stmt);

$conn->close();


header("Location: pending_requests.php");
exit();

?>