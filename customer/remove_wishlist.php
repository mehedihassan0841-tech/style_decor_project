<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

$customer_id = $_SESSION["user_id"];

if(!isset($_GET["id"])){
    header("Location: services.php");
    exit();
}

$service_id=(int)$_GET["id"];

$delete=$conn->prepare("
DELETE FROM wishlist
WHERE customer_id=?
AND service_id=?");

$delete->bind_param("ii",$customer_id,$service_id);

$delete->execute();

header("Location: services.php");

exit();

?>