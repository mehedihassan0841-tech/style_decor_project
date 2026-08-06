<?php

if(session_status()===PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: services.php");
    exit();
}

$client_id = $_SESSION["user_id"];
$service_id = (int)$_POST["service_id"];
$event_date = $_POST["event_date"];
$event_time = $_POST["event_time"];
$event_location = trim($_POST["event_location"]);
$special_instruction = trim($_POST["special_instruction"]);
$total_amount = $_POST["total_amount"];

$booking_date = date("Y-m-d");
$booking_code = "BK" . date("ymdHis") . rand(10,99);

$sql = "INSERT INTO bookings
(
booking_code,
client_id,
service_id,
booking_date,
event_date,
event_location,
event_time,
special_instruction,
total_amount,
booking_status
)
VALUES(?,?,?,?,?,?,?,?,?,'Pending')";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"siisssssd",
$booking_code,
$client_id,
$service_id,
$booking_date,
$event_date,
$event_location,
$event_time,
$special_instruction,
$total_amount
);

if($stmt->execute()){

    $_SESSION["success"]="Booking placed successfully.";

}else{

    $_SESSION["error"]="Booking failed.";

}

header("Location: bookings.php");
exit();