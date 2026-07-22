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

if (!isset($_GET["id"])) {

    header("Location: services.php");
    exit();

}

$id = (int)$_GET["id"];

$sql = "SELECT
ds.*,
dp.company_name,
dp.experience,
dp.bio,
u.full_name,
u.email,
u.phone

FROM decorator_services ds

INNER JOIN decorator_profiles dp
ON ds.decorator_id=dp.user_id

INNER JOIN users u
ON ds.decorator_id=u.id

WHERE ds.id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    echo "Service Not Found";
    exit();

}

$service = $result->fetch_assoc();

?>

<div class="dashboard-content">

<div class="dashboard-main">

<div class="service-details-card">

<div class="service-image">

<img
src="../uploads/services/<?php echo $service["service_image"]; ?>"
alt="Service">

</div>

<div class="service-info">

<span class="category-badge">

<?php echo $service["category"]; ?>

</span>

<h1>

<?php echo htmlspecialchars($service["service_name"]); ?>

</h1>

<h3>

<?php echo htmlspecialchars($service["company_name"]); ?>

</h3>

<p>

<strong>Decorator :</strong>

<?php echo htmlspecialchars($service["full_name"]); ?>

</p>

<p>

<strong>Phone :</strong>

<?php echo $service["phone"]; ?>

</p>

<p>

<strong>Email :</strong>

<?php echo $service["email"]; ?>

</p>

<p>

<strong>Experience :</strong>

<?php echo $service["experience"]; ?>

Years

</p>

<p>

<strong>Duration :</strong>

<?php echo $service["duration"]; ?>

</p>

<p>

<strong>Price :</strong>

৳<?php echo number_format($service["price"]); ?>

</p>

<p>

<strong>Availability :</strong>

<span class="status">

<?php echo $service["availability"]; ?>

</span>

</p>

<div class="description">

<h3>Description</h3>

<p>

<?php echo nl2br(htmlspecialchars($service["description"])); ?>

</p>

</div>

<div class="description">

<h3>About Decorator</h3>

<p>

<?php echo nl2br(htmlspecialchars($service["bio"])); ?>

</p>

</div>

<a
href="book_service.php?id=<?php echo $service["id"]; ?>"
class="book-btn">

<i class="fas fa-calendar-check"></i>

Book Now

</a>

</div>

</div>

</div>

</div>

<?php include("../includes/customer_footer.php"); ?>