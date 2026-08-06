<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

if (!isset($_GET["id"])) {
    header("Location: bookings.php");
    exit();
}

$booking_id = (int)$_GET["id"];
$customer_id = $_SESSION["user_id"];

$sql = "UPDATE bookings
SET booking_status='Cancelled'
WHERE id=?
AND client_id=?
AND booking_status='Pending'";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii",$booking_id,$customer_id);

$stmt->execute();

header("Location: bookings.php");
exit();

?>