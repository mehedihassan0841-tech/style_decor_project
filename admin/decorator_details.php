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
    header("Location: decorators.php");
    exit();
}

$id=(int)$_GET["id"];

$query=mysqli_query($conn,"
SELECT *
FROM users
WHERE id='$id'
AND role='decorator'
");

if(mysqli_num_rows($query)==0){
    header("Location: decorators.php");
    exit();
}

$decorator=mysqli_fetch_assoc($query);

?>

<div class="admin-content">

<div class="decorator-details-page">

<div class="decorator-details-card">

<div class="decorator-profile-section">

<div class="decorator-profile-left">

<img src="../uploads/profile/<?php echo $decorator["profile_image"]; ?>">

</div>

<div class="decorator-profile-right">

<h2>

<?php echo $decorator["full_name"]; ?>

</h2>

<p class="decorator-id">

<i class="fa-solid fa-id-card"></i>

Decorator ID :
DEC<?php echo str_pad($decorator["id"],4,"0",STR_PAD_LEFT); ?>

</p>

<span class="decorator-status <?php echo strtolower($decorator["status"]); ?>">

<?php echo ucfirst($decorator["status"]); ?>

</span>

</div>

</div>

<div class="decorator-info-grid">

<div class="decorator-info-card">

<div class="decorator-info-icon">

<i class="fa-solid fa-envelope"></i>

</div>

<div class="decorator-info-text">

<label>Email Address</label>

<p><?php echo $decorator["email"]; ?></p>

</div>

</div>

<div class="decorator-info-card">

<div class="decorator-info-icon">

<i class="fa-solid fa-phone"></i>

</div>

<div class="decorator-info-text">

<label>Phone Number</label>

<p><?php echo $decorator["phone"]; ?></p>

</div>

</div>

<div class="decorator-info-card">

<div class="decorator-info-icon">

<i class="fa-solid fa-user-shield"></i>

</div>

<div class="decorator-info-text">

<label>Role</label>

<p><?php echo ucfirst($decorator["role"]); ?></p>

</div>

</div>

<div class="decorator-info-card">

<div class="decorator-info-icon">

<i class="fa-solid fa-location-dot"></i>

</div>

<div class="decorator-info-text">

<label>Address</label>

<p><?php echo $decorator["address"]; ?></p>

</div>

</div>

<div class="decorator-info-card">

<div class="decorator-info-icon">

<i class="fa-solid fa-calendar-days"></i>

</div>

<div class="decorator-info-text">

<label>Joined</label>

<p><?php echo date("d M Y",strtotime($decorator["created_at"])); ?></p>

</div>

</div>

<div class="decorator-info-card">

<div class="decorator-info-icon">

<i class="fa-solid fa-clock"></i>

</div>

<div class="decorator-info-text">

<label>Last Login</label>

<p>

<?php

if($decorator["last_login"]==""){

echo "Never";

}else{

echo date("d M Y h:i A",strtotime($decorator["last_login"]));

}

?>

</p>

</div>

</div>

</div>

<div class="decorator-details-footer">

<a href="decorators.php" class="decorator-back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back to Decorators

</a>

</div>

</div>

</div>

</div>

<?php include("../includes/admin_footer.php"); ?>