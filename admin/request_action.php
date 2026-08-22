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
// GET INPUT
// =========================================

$user_id = (int)($_POST["user_id"] ?? 0);

$action = $_POST["action"] ?? "";


// =========================================
// VALIDATION
// =========================================

if ($user_id <= 0) {

    $_SESSION["settings_error"] =
        "Invalid user request.";

    header("Location: pending_requests.php");

    exit();
}


if (!in_array($action, ["approve", "block"])) {

    $_SESSION["settings_error"] =
        "Invalid action.";

    header("Location: pending_requests.php");

    exit();
}


// =========================================
// DETERMINE STATUS
// =========================================

if ($action == "approve") {

    $new_status = "approved";

} else {

    $new_status = "blocked";

}


// =========================================
// UPDATE USER STATUS
// =========================================

$stmt = mysqli_prepare(
    $conn,
    "UPDATE users
     SET status = ?
     WHERE id = ?
       AND role IN ('customer', 'decorator')
       AND status = 'pending'"
);


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

    if (mysqli_stmt_affected_rows($stmt) > 0) {

        if ($action == "approve") {

            $_SESSION["settings_success"] =
                "User approved successfully.";

        } else {

            $_SESSION["settings_success"] =
                "User blocked successfully.";

        }

    } else {

        $_SESSION["settings_error"] =
            "This request is no longer pending.";

    }

} else {

    $_SESSION["settings_error"] =
        "Unable to update user status.";

}


mysqli_stmt_close($stmt);


// =========================================
// BACK TO REQUEST PAGE
// =========================================

header("Location: pending_requests.php");

exit();

?>