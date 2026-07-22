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

if(!isset($_GET["id"])){

    header("Location: services.php");
    exit();

}

$service_id = (int)$_GET["id"];

$sql = "SELECT *

FROM decorator_services

WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$service_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    die("Service not found.");

}

$service = $result->fetch_assoc();

?>
<div class="dashboard-content">

<div class="dashboard-main">

<div class="page-header">

<h1>Book Service</h1>

<p>Complete your booking information.</p>

</div>

<div class="profile-form-card">

<form action="insert_booking.php" method="POST">

<input
type="hidden"
name="service_id"
value="<?php echo $service["id"]; ?>">

<div class="form-group">

<label>Service</label>

<input
type="text"
value="<?php echo $service["service_name"]; ?>"
readonly>

</div>

<div class="form-group">

<label>Total Amount</label>

<input
type="text"
value="৳ <?php echo number_format($service["price"]); ?>"
readonly>

<input
type="hidden"
name="total_amount"
value="<?php echo $service["price"]; ?>">

</div>

<div class="form-group">

<label>Event Date</label>

<input
type="date"
name="event_date"
required>

</div>

<div class="form-group">

<label>Event Time</label>

<input
type="time"
name="event_time"
required>

</div>

<div class="form-group">

<label>Event Location</label>

<input
type="text"
name="event_location"
required>

</div>

<div class="form-group">

<label>Special Instruction</label>

<textarea
name="special_instruction"
rows="5"></textarea>

</div>

<button
type="submit"
class="save-btn">

<i class="fas fa-calendar-check"></i>

Confirm Booking

</button>

</form>

</div>

</div>

</div>

<?php include("../includes/customer_footer.php"); ?>