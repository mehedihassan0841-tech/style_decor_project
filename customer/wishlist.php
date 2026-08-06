<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] != "customer") {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

include("../includes/customer_header.php");
include("../includes/customer_sidebar.php");

$customer_id = $_SESSION["user_id"];

$sql = "SELECT

w.service_id,

ds.*,

dp.company_name,

u.full_name

FROM wishlist w

INNER JOIN decorator_services ds
ON w.service_id = ds.id

INNER JOIN decorator_profiles dp
ON ds.decorator_id = dp.user_id

INNER JOIN users u
ON ds.decorator_id = u.id

WHERE w.customer_id=?

ORDER BY w.id DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$customer_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<div class="dashboard-content">

<div class="dashboard-main">

<div class="page-header">

<h1>My Wishlist</h1>

<p>All your favourite decoration services.</p>

</div>

<?php if($result->num_rows>0){ ?>

<div class="services-grid">

<?php while($row=$result->fetch_assoc()){ ?>

<div class="service-card">

<div class="service-image-box">

<img src="../uploads/services/<?php echo $row["service_image"]; ?>">

<span class="price-tag">

৳<?php echo number_format($row["price"]); ?>

</span>

<span class="available-tag">

<?php echo $row["availability"]; ?>

</span>

</div>

<div class="service-content">

<span class="category">

<?php echo $row["category"]; ?>

</span>

<h3>

<?php echo htmlspecialchars($row["service_name"]); ?>

</h3>

<p>

<i class="fas fa-building"></i>

<?php echo htmlspecialchars($row["company_name"]); ?>

</p>

<p>

<i class="fas fa-user"></i>

<?php echo htmlspecialchars($row["full_name"]); ?>

</p>

<p>

<i class="fas fa-clock"></i>

<?php echo $row["duration"]; ?>

</p>

<div class="service-buttons">

<a href="remove_wishlist.php?id=<?php echo $row["service_id"]; ?>"

class="wish-btn active">

<i class="fas fa-heart"></i>

</a>

<a href="service_details.php?id=<?php echo $row["service_id"]; ?>"

class="view-btn">

<i class="fas fa-eye"></i>

View Details

</a>

</div>

</div>

</div>

<?php } ?>

</div>

<?php }else{ ?>

<div class="empty-booking">

<i class="fas fa-heart"></i>

<h2>No Favourite Services</h2>

<p>

You haven't added any services yet.

</p>

<a href="services.php"

class="browse-btn">

Browse Services

</a>

</div>

<?php } ?>

</div>

</div>

<?php include("../includes/customer_footer.php"); ?>