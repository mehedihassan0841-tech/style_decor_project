<?php

if(session_status()===PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["user_role"]!="decorator"){
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

if(!isset($_GET["id"])){
    header("Location: booking.php");
    exit();
}

$booking_id=(int)$_GET["id"];

$decorator_id=$_SESSION["user_id"];

/* Verify Booking Belongs To This Decorator */

$sql="SELECT b.id

FROM bookings b

INNER JOIN decorator_services ds

ON b.service_id=ds.id

WHERE b.id=?

AND ds.decorator_id=?";

$stmt=$conn->prepare($sql);

$stmt->bind_param("ii",$booking_id,$decorator_id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    $_SESSION["error"]="Invalid booking.";

    header("Location: booking.php");

    exit();

}

/* Accept Booking */

$update=$conn->prepare("UPDATE bookings
SET booking_status='Accepted'
WHERE id=?");

$update->bind_param("i",$booking_id);

if($update->execute()){

    $_SESSION["success"]="Booking accepted successfully.";

}else{

    $_SESSION["error"]="Failed to accept booking.";

}

header("Location: booking.php");

exit();

?>