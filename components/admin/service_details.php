<?php

session_start();

if(!isset($_SESSION["user_id"])){

header("Location: ../login.php");

exit();

}

if($_SESSION["user_role"]!="admin"){

header("Location: ../login.php");

exit();

}

require_once("../config/database.php");

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");

if(!isset($_GET["id"])){

header("Location: services.php");

exit();

}

$id=(int)$_GET["id"];

$query=mysqli_query($conn,"
SELECT
decorator_services.*,
users.full_name
FROM decorator_services
LEFT JOIN users
ON decorator_services.decorator_id=users.id
WHERE decorator_services.id='$id'
");

if(mysqli_num_rows($query)==0){

header("Location: services.php");

exit();

}

$service=mysqli_fetch_assoc($query);

$image=!empty($service["service_image"]) && file_exists("../uploads/services/".$service["service_image"])

? "../uploads/services/".$service["service_image"]

: "../images/no-image.png";

?>

<div class="admin-content">

<div class="service-details-page">

<div class="service-details-card">

<div class="service-details-top">

<div class="service-image-left">

<img src="<?php echo $image; ?>">

</div>

<div class="service-info-right">

<h2>

<?php echo htmlspecialchars($service["service_name"]); ?>

</h2>

<p class="service-id">

<i class="fa-solid fa-hashtag"></i>

Service ID :
SER<?php echo str_pad($service["id"],4,"0",STR_PAD_LEFT); ?>

</p>

<span class="service-status <?php echo strtolower($service["availability"]); ?>">

<?php echo ucfirst($service["availability"]); ?>

</span>

</div>

</div>

<div class="service-details-grid">
    <div class="service-info-card">

<div class="service-info-icon">

<i class="fa-solid fa-user"></i>

</div>

<div class="service-info-text">

<label>Decorator</label>

<p><?php echo htmlspecialchars($service["full_name"]); ?></p>

</div>

</div>

<div class="service-info-card">

<div class="service-info-icon">

<i class="fa-solid fa-layer-group"></i>

</div>

<div class="service-info-text">

<label>Category</label>

<p><?php echo htmlspecialchars($service["category"]); ?></p>

</div>

</div>

<div class="service-info-card">

<div class="service-info-icon">

<i class="fa-solid fa-money-bill-wave"></i>

</div>

<div class="service-info-text">

<label>Price</label>

<p>৳<?php echo number_format($service["price"]); ?></p>

</div>

</div>

<div class="service-info-card">

<div class="service-info-icon">

<i class="fa-solid fa-clock"></i>

</div>

<div class="service-info-text">

<label>Duration</label>

<p><?php echo htmlspecialchars($service["duration"]); ?></p>

</div>

</div>

<div class="service-info-card full-width">

<div class="service-info-icon">

<i class="fa-solid fa-align-left"></i>

</div>

<div class="service-info-text">

<label>Description</label>

<p><?php echo nl2br(htmlspecialchars($service["description"])); ?></p>

</div>

</div>

<div class="service-info-card">

<div class="service-info-icon">

<i class="fa-solid fa-calendar-days"></i>

</div>

<div class="service-info-text">

<label>Created Date</label>

<p><?php echo date("d M Y",strtotime($service["created_at"])); ?></p>

</div>

</div>

</div>

<div class="service-details-footer">

<a href="services.php" class="service-back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back to Services

</a>

</div>

</div>

</div>

</div>

<?php include("../includes/admin_footer.php"); ?>