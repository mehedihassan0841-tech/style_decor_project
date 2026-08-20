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
   GET SETTINGS ROW
========================================= */

$settings_query = mysqli_query(
    $conn,
    "SELECT id FROM settings ORDER BY id ASC LIMIT 1"
);

if (!$settings_query) {

    $_SESSION["settings_error"] = "Unable to access settings.";

    header("Location: settings.php");
    exit();
}


/* =========================================
   CREATE SETTINGS ROW IF NOT EXISTS
========================================= */

if (mysqli_num_rows($settings_query) == 0) {

    $insert_query = mysqli_query(
        $conn,
        "INSERT INTO settings (website_name)
         VALUES ('StyleDecor')"
    );

    if (!$insert_query) {

        $_SESSION["settings_error"] =
            "Unable to create settings.";

        header("Location: settings.php");
        exit();
    }

    $settings_id = mysqli_insert_id($conn);

} else {

    $settings = mysqli_fetch_assoc($settings_query);

    $settings_id = (int)$settings["id"];
}


/* =========================================
   CHECK SECTION
========================================= */

$section = $_POST["settings_section"] ?? "";



/* =========================================
   ADMIN PROFILE
========================================= */

if ($section == "profile") {

    $admin_name = trim($_POST["admin_name"] ?? "");
    $admin_email = trim($_POST["admin_email"] ?? "");
    $admin_phone = trim($_POST["admin_phone"] ?? "");

    /* -------------------------------------
       Update Basic Profile Information
    ------------------------------------- */

    $sql = "UPDATE users SET
                full_name = ?,
                email = ?,
                phone = ?
            WHERE id = ? AND role = 'admin'";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $_SESSION["settings_error"] = "Unable to prepare profile update.";
        header("Location: settings.php#profile");
        exit();
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $admin_name,
        $admin_email,
        $admin_phone,
        $_SESSION["user_id"]
    );

    if (!mysqli_stmt_execute($stmt)) {
        $_SESSION["settings_error"] = "Unable to update admin profile.";
        mysqli_stmt_close($stmt);
        header("Location: settings.php#profile");
        exit();
    }

    mysqli_stmt_close($stmt);

    /* update session so navbar shows new name immediately */
    $_SESSION["user_name"] = $admin_name;

    /* -------------------------------------
       Profile Image Upload
    ------------------------------------- */

    if (
        isset($_FILES["profile_image"]) &&
        $_FILES["profile_image"]["error"] == UPLOAD_ERR_OK
    ) {
        $upload_dir = "../uploads/profile/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES["profile_image"]["name"];
        $tmp_name  = $_FILES["profile_image"]["tmp_name"];

        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "webp"];

        if (in_array($extension, $allowed_extensions)) {

            $new_name = "admin_" . time() . "_" . uniqid() . "." . $extension;
            $destination = $upload_dir . $new_name;

            if (move_uploaded_file($tmp_name, $destination)) {

                $image_path = $new_name;

                $image_sql = "UPDATE users
                              SET profile_image = ?
                              WHERE id = ? AND role = 'admin'";

                $image_stmt = mysqli_prepare($conn, $image_sql);

                if ($image_stmt) {
                    mysqli_stmt_bind_param(
                        $image_stmt,
                        "si",
                        $image_path,
                        $_SESSION["user_id"]
                    );

                    mysqli_stmt_execute($image_stmt);
                    mysqli_stmt_close($image_stmt);
                }

                /* update session so navbar shows new picture immediately */
                $_SESSION["profile_image"] = $image_path;
            }
        }
    }

    $_SESSION["settings_success"] = "Admin profile updated successfully.";
    header("Location: settings.php#profile");
    exit();
}
/* =========================================
   APPEARANCE
========================================= */

if ($section == "appearance") {

    $website_name = trim($_POST["website_name"] ?? "");
    $website_tagline = trim($_POST["website_tagline"] ?? "");

    $banner_title = trim($_POST["banner_title"] ?? "");
    $banner_subtitle = trim($_POST["banner_subtitle"] ?? "");

    $banner_button_text =
        trim($_POST["banner_button_text"] ?? "");

    $banner_button_link =
        trim($_POST["banner_button_link"] ?? "");


    $sql = "UPDATE settings SET
                website_name = ?,
                website_tagline = ?,
                banner_title = ?,
                banner_subtitle = ?,
                banner_button_text = ?,
                banner_button_link = ?
            WHERE id = ?";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        $_SESSION["settings_error"] =
            "Unable to prepare appearance settings.";

        header("Location: settings.php#appearance");
        exit();
    }


    mysqli_stmt_bind_param(
        $stmt,
        "ssssssi",
        $website_name,
        $website_tagline,
        $banner_title,
        $banner_subtitle,
        $banner_button_text,
        $banner_button_link,
        $settings_id
    );


    if (mysqli_stmt_execute($stmt)) {

        /* ===============================
           BANNER IMAGE
        =============================== */

        if (
            isset($_FILES["banner_image"]) &&
            $_FILES["banner_image"]["error"] == UPLOAD_ERR_OK
        ) {

            $upload_dir = "../uploads/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }


            $file_name =
                $_FILES["banner_image"]["name"];

            $tmp_name =
                $_FILES["banner_image"]["tmp_name"];


            $extension =
                strtolower(
                    pathinfo(
                        $file_name,
                        PATHINFO_EXTENSION
                    )
                );


            $allowed_extensions = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            if (in_array($extension, $allowed_extensions)) {

                $new_name =
                    "banner_" .
                    time() .
                    "_" .
                    uniqid() .
                    "." .
                    $extension;


                $destination =
                    $upload_dir . $new_name;


                if (move_uploaded_file(
                    $tmp_name,
                    $destination
                )) {

                    $image_path =
                        "uploads/" . $new_name;


                    $image_sql =
                        "UPDATE settings
                         SET banner_image = ?
                         WHERE id = ?";


                    $image_stmt =
                        mysqli_prepare(
                            $conn,
                            $image_sql
                        );


                    if ($image_stmt) {

                        mysqli_stmt_bind_param(
                            $image_stmt,
                            "si",
                            $image_path,
                            $settings_id
                        );

                        mysqli_stmt_execute(
                            $image_stmt
                        );

                        mysqli_stmt_close(
                            $image_stmt
                        );
                    }
                }
            }
        }


        $_SESSION["settings_success"] =
            "Appearance settings updated successfully.";

    } else {

        $_SESSION["settings_error"] =
            "Unable to update appearance settings.";
    }


    mysqli_stmt_close($stmt);


    header("Location: settings.php#appearance");
    exit();
}


/* =========================================
   FOOTER
========================================= */

if ($section == "footer") {

    $footer_description =
        trim($_POST["footer_description"] ?? "");

    $footer_address =
        trim($_POST["footer_address"] ?? "");

    $footer_phone =
        trim($_POST["footer_phone"] ?? "");

    $footer_email =
        trim($_POST["footer_email"] ?? "");

    $copyright_text =
        trim($_POST["copyright_text"] ?? "");


    $sql = "UPDATE settings SET
                footer_description = ?,
                footer_address = ?,
                footer_phone = ?,
                footer_email = ?,
                copyright_text = ?
            WHERE id = ?";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        $_SESSION["settings_error"] =
            "Unable to prepare footer settings.";

        header("Location: settings.php#footer");
        exit();
    }


    mysqli_stmt_bind_param(
        $stmt,
        "sssssi",
        $footer_description,
        $footer_address,
        $footer_phone,
        $footer_email,
        $copyright_text,
        $settings_id
    );


    if (mysqli_stmt_execute($stmt)) {

        $_SESSION["settings_success"] =
            "Footer information updated successfully.";

    } else {

        $_SESSION["settings_error"] =
            "Unable to update footer information.";
    }


    mysqli_stmt_close($stmt);


    header("Location: settings.php#footer");
    exit();
}


/* =========================================
   SOCIAL MEDIA
========================================= */

if ($section == "social") {

    $facebook_url =
        trim($_POST["facebook_url"] ?? "");

    $instagram_url =
        trim($_POST["instagram_url"] ?? "");

    $linkedin_url =
        trim($_POST["linkedin_url"] ?? "");

    $youtube_url =
        trim($_POST["youtube_url"] ?? "");


    $sql = "UPDATE settings SET
                facebook_url = ?,
                instagram_url = ?,
                linkedin_url = ?,
                youtube_url = ?
            WHERE id = ?";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        $_SESSION["settings_error"] =
            "Unable to prepare social settings.";

        header("Location: settings.php#social");
        exit();
    }


    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $facebook_url,
        $instagram_url,
        $linkedin_url,
        $youtube_url,
        $settings_id
    );


    if (mysqli_stmt_execute($stmt)) {

        $_SESSION["settings_success"] =
            "Social media links updated successfully.";

    } else {

        $_SESSION["settings_error"] =
            "Unable to update social media links.";
    }


    mysqli_stmt_close($stmt);


    header("Location: settings.php#social");
    exit();
}


/* =========================================
   BOOKING SETTINGS
========================================= */

if ($section == "booking") {

    $booking_enabled =
        isset($_POST["booking_enabled"])
        ? 1
        : 0;


    $customer_cancellation =
        isset($_POST["customer_cancellation"])
        ? 1
        : 0;


    $booking_approval =
        isset($_POST["booking_approval"])
        ? 1
        : 0;


    $sql = "UPDATE settings SET
                booking_enabled = ?,
                customer_cancellation = ?,
                booking_approval = ?
            WHERE id = ?";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        $_SESSION["settings_error"] =
            "Unable to prepare booking settings.";

        header("Location: settings.php#booking");
        exit();
    }


    mysqli_stmt_bind_param(
        $stmt,
        "iiii",
        $booking_enabled,
        $customer_cancellation,
        $booking_approval,
        $settings_id
    );


    if (mysqli_stmt_execute($stmt)) {

        $_SESSION["settings_success"] =
            "Booking settings updated successfully.";

    } else {

        $_SESSION["settings_error"] =
            "Unable to update booking settings.";
    }


    mysqli_stmt_close($stmt);


    header("Location: settings.php#booking");
    exit();
}


/* =========================================
   NOTIFICATIONS
========================================= */

if ($section == "notifications") {

    $notify_booking =
        isset($_POST["notify_booking"])
        ? 1
        : 0;


    $notify_review =
        isset($_POST["notify_review"])
        ? 1
        : 0;


    $notify_decorator =
        isset($_POST["notify_decorator"])
        ? 1
        : 0;


    $sql = "UPDATE settings SET
                notify_booking = ?,
                notify_review = ?,
                notify_decorator = ?
            WHERE id = ?";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        $_SESSION["settings_error"] =
            "Unable to prepare notification settings.";

        header("Location: settings.php#notifications");
        exit();
    }


    mysqli_stmt_bind_param(
        $stmt,
        "iiii",
        $notify_booking,
        $notify_review,
        $notify_decorator,
        $settings_id
    );


    if (mysqli_stmt_execute($stmt)) {

        $_SESSION["settings_success"] =
            "Notification settings updated successfully.";

    } else {

        $_SESSION["settings_error"] =
            "Unable to update notification settings.";
    }


    mysqli_stmt_close($stmt);


    header("Location: settings.php#notifications");
    exit();
}


/* =========================================
   INVALID SECTION
========================================= */

$_SESSION["settings_error"] =
    "Invalid settings section.";

header("Location: settings.php");
exit();

?>

