<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: decorators.php");
    exit();
}

$id = (int) $_GET["id"];

$conn->begin_transaction();

try {

    /* ===============================
       CHECK DECORATOR EXISTS
    =============================== */

    $stmt = $conn->prepare("
        SELECT profile_image
        FROM users
        WHERE id = ?
          AND role = 'decorator'
        LIMIT 1
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Decorator not found.");
    }

    $user = $result->fetch_assoc();
    $stmt->close();


    /* ===============================
       COLLECT SERVICE IDs
    =============================== */

    $service_ids = [];
    $svc = $conn->prepare("SELECT id, service_image FROM decorator_services WHERE decorator_id = ?");
    if ($svc) {
        $svc->bind_param("i", $id);
        $svc->execute();
        $svc_result = $svc->get_result();
        while ($row = $svc_result->fetch_assoc()) {
            $service_ids[] = (int)$row["id"];
            if (!empty($row["service_image"])) {
                $img = "../uploads/services/" . $row["service_image"];
                if (file_exists($img)) {
                    @unlink($img);
                }
            }
        }
        $svc->close();
    }


    /* ===============================
       DELETE BOOKINGS linked to these services
       (bookings has service_id, not always decorator_id)
    =============================== */

    if (!empty($service_ids)) {
        $placeholders = implode(",", array_fill(0, count($service_ids), "?"));
        $types = str_repeat("i", count($service_ids));

        // Step 1: get booking ids
        $booking_ids = [];
        $bq = $conn->prepare("SELECT id FROM bookings WHERE service_id IN ($placeholders)");
        if ($bq) {
            $bq->bind_param($types, ...$service_ids);
            $bq->execute();
            $bres = $bq->get_result();
            while ($b = $bres->fetch_assoc()) {
                $booking_ids[] = (int)$b["id"];
            }
            $bq->close();
        }

        if (!empty($booking_ids)) {
            $bph = implode(",", array_fill(0, count($booking_ids), "?"));
            $btypes = str_repeat("i", count($booking_ids));

            $stmt = $conn->prepare("DELETE FROM reviews WHERE booking_id IN ($bph)");
            if ($stmt) {
                $stmt->bind_param($btypes, ...$booking_ids);
                $stmt->execute();
                $stmt->close();
            }

            $stmt = $conn->prepare("DELETE FROM bookings WHERE id IN ($bph)");
            if ($stmt) {
                $stmt->bind_param($btypes, ...$booking_ids);
                $stmt->execute();
                $stmt->close();
            }
        }

        // also delete wishlist entries for these services
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE service_id IN ($placeholders)");
        if ($stmt) {
            $stmt->bind_param($types, ...$service_ids);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Also try direct decorator_id on bookings/reviews/wishlist (if columns exist)
    foreach (["bookings", "reviews", "wishlist"] as $table) {
        $check = @$conn->query("SHOW COLUMNS FROM `$table` LIKE 'decorator_id'");
        if ($check && $check->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM `$table` WHERE decorator_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }


    /* ===============================
       DELETE SERVICES
    =============================== */

    $stmt = $conn->prepare("DELETE FROM decorator_services WHERE decorator_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }


    /* ===============================
       DELETE PORTFOLIO
    =============================== */

    $pf = $conn->prepare("SELECT image FROM decorator_portfolio WHERE decorator_id = ?");
    if ($pf) {
        $pf->bind_param("i", $id);
        $pf->execute();
        $pf_result = $pf->get_result();
        while ($row = $pf_result->fetch_assoc()) {
            if (!empty($row["image"])) {
                $img = "../uploads/portfolio/" . $row["image"];
                if (file_exists($img)) {
                    @unlink($img);
                }
            }
        }
        $pf->close();
    }

    $stmt = $conn->prepare("DELETE FROM decorator_portfolio WHERE decorator_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }


    /* ===============================
       DELETE REMAINING REVIEWS by decorator_id
    =============================== */

    $stmt = $conn->prepare("DELETE FROM reviews WHERE decorator_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }


    /* ===============================
       DELETE DECORATOR PROFILE
    =============================== */

    $stmt = $conn->prepare("DELETE FROM decorator_profiles WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }


    /* ===============================
       DELETE USER ACCOUNT
    =============================== */

    $stmt = $conn->prepare("
        DELETE FROM users
        WHERE id = ?
          AND role = 'decorator'
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Decorator could not be deleted.");
    }

    $stmt->close();


    /* ===============================
       COMMIT
    =============================== */

    $conn->commit();


    /* ===============================
       DELETE PROFILE IMAGE FROM SERVER
    =============================== */

    if (!empty($user["profile_image"]) && $user["profile_image"] !== "default.png") {
        $image = "../uploads/profile/" . $user["profile_image"];
        if (file_exists($image)) {
            @unlink($image);
        }
    }


    /* ===============================
       SUCCESS
    =============================== */

    header("Location: decorators.php?deleted=1");
    exit();

} catch (Exception $e) {

    $conn->rollback();

    echo "
    <div style='
        max-width:600px;
        margin:100px auto;
        padding:30px;
        background:#fff;
        border-radius:15px;
        box-shadow:0 10px 30px rgba(0,0,0,.1);
        font-family:Arial;
        text-align:center;
    '>

        <h2 style='color:#dc2626;'>
            Delete Failed
        </h2>

        <p>" . htmlspecialchars($e->getMessage()) . "</p>

        <a href='decorators.php'
           style='
               display:inline-block;
               margin-top:20px;
               padding:12px 25px;
               background:#6c63ff;
               color:#fff;
               text-decoration:none;
               border-radius:8px;
           '>
            Back to Decorators
        </a>

    </div>
    ";

    exit();
}

?>