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

$service_id = (int)$_GET["id"];

/* Already Exists? */

$check = $conn->prepare("
SELECT id
FROM wishlist
WHERE customer_id=?
AND service_id=?");

$check->bind_param("ii",$customer_id,$service_id);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0){

    $insert = $conn->prepare("
    INSERT INTO wishlist
    (customer_id,service_id)
    VALUES(?,?)");

    $insert->bind_param("ii",$customer_id,$service_id);

    $insert->execute();

}

header("Location: services.php");

exit();

?>