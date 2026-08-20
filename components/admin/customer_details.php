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

header("Location: customers.php");
exit();

}

$id=(int)$_GET["id"];

$query=mysqli_query($conn,"
SELECT *
FROM users
WHERE id='$id'
AND role='customer'
");

if(mysqli_num_rows($query)==0){

header("Location: customers.php");
exit();

}

$customer=mysqli_fetch_assoc($query);

?>
<div class="admin-content">

<div class="customer-details-page">

<div class="customer-details-card">
<div class="customer-details-top">

    <div class="customer-profile-left">

        <img src="../uploads/profile/<?php echo $customer["profile_image"]; ?>">

    </div>

    <div class="customer-profile-right">

        <h2>

            <?php echo $customer["full_name"]; ?>

        </h2>

        <p class="customer-id">

            <i class="fa-solid fa-id-card"></i>

            Customer ID :
            CUS<?php echo str_pad($customer["id"],4,"0",STR_PAD_LEFT); ?>

        </p>

        <span class="customer-status <?php echo strtolower($customer["status"]); ?>">

            <?php echo ucfirst($customer["status"]); ?>

        </span>

    </div>

</div>


<div class="customer-details-grid">

 <div class="customer-info-card">

    <div class="customer-info-icon">

        <i class="fa-solid fa-envelope"></i>

    </div>

    <div class="customer-info-text">

        <label>Email Address</label>

        <p><?php echo $customer["email"]; ?></p>

    </div>

 </div>

 <div class="customer-info-card">

    <div class="customer-info-icon">

        <i class="fa-solid fa-phone"></i>

    </div>

    <div class="customer-info-text">

        <label>Phone Number</label>

        <p><?php echo $customer["phone"]; ?></p>

    </div>

</div>

<div class="customer-info-card">

    <div class="customer-info-icon">

        <i class="fa-solid fa-location-dot"></i>

    </div>

    <div class="customer-info-text">

        <label>Address</label>

        <p><?php echo $customer["address"]; ?></p>

    </div>

</div>

<div class="customer-info-card">

    <div class="customer-info-icon">

        <i class="fa-solid fa-calendar-days"></i>

    </div>

    <div class="customer-info-text">

        <label>Joined</label>

        <p><?php echo date("d M Y",strtotime($customer["created_at"])); ?></p>

    </div>

</div>

<div class="customer-info-card">

    <div class="customer-info-icon">

        <i class="fa-solid fa-clock"></i>

    </div>

    <div class="customer-info-text">

        <label>Last Login</label>

        <p>

        <?php

        if($customer["last_login"]==""){

        echo "Never";

        }else{

        echo date("d M Y h:i A",strtotime($customer["last_login"]));

        }

        ?>

        </p>

    </div>


</div>


<?php

if($customer["last_login"]==""){

echo "";

}else{

echo date("d M Y h:i A",strtotime($customer["last_login"]));

}

?>

</p>

</div>

</div>

<div class="customer-details-footer">

<a href="customers.php" class="customer-back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back

</a>

</div>

</div>

</div>
</div>

<?php include("../includes/admin_footer.php"); ?>