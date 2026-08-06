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
b.*,
ds.service_name,
ds.category,
dp.company_name,
dp.user_id AS decorator_id,
r.id AS review_id

FROM bookings b

INNER JOIN decorator_services ds 
ON b.service_id = ds.id

INNER JOIN decorator_profiles dp 
ON ds.decorator_id = dp.user_id

LEFT JOIN reviews r
ON b.id = r.booking_id
AND r.client_id = ?

WHERE b.client_id=?

ORDER BY b.id DESC";


$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $customer_id, $customer_id);

$stmt->execute();

$result = $stmt->get_result();


// Check recent service for review

$review_check = $conn->prepare("
SELECT b.id
FROM bookings b

LEFT JOIN reviews r
ON b.id = r.booking_id

WHERE b.client_id = ?

AND b.booking_status = 'Completed'

AND r.id IS NULL
");


$review_check->bind_param("i", $customer_id);

$review_check->execute();

$review_result = $review_check->get_result();


?>


<div class="dashboard-content">

<div class="dashboard-main">

<div class="page-header">

<h1>My Bookings</h1>

<p>View and manage all your event bookings.</p>

</div>

<?php if($result->num_rows > 0){ ?>

<div class="table-responsive">

<table class="booking-table">

<thead>

<tr>

<th>Booking Code</th>
<th>Service</th>
<th>Company</th>
<th>Category</th>
<th>Event Date</th>
<th>Amount</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row["booking_code"]; ?></td>

<td><?php echo htmlspecialchars($row["service_name"]); ?></td>

<td><?php echo htmlspecialchars($row["company_name"]); ?></td>

<td><?php echo htmlspecialchars($row["category"]); ?></td>

<td><?php echo date("d M Y", strtotime($row["event_date"])); ?></td>

<td>৳<?php echo number_format($row["total_amount"]); ?></td>

<td>

<?php

$status = $row["booking_status"];

$color = "#ffc107";

if($status=="Accepted") $color="#28a745";
if($status=="Rejected") $color="#dc3545";
if($status=="Completed") $color="#007bff";
if($status=="Cancelled") $color="#6c757d";

?>

<span style="
background:<?= $color ?>;
color:#fff;
padding:6px 12px;
border-radius:20px;
font-size:13px;
font-weight:600;
display:inline-block;
">

<?= htmlspecialchars($status) ?>

</span>

</td>

<td>

<?php if($status=="Pending"){ ?>

<a href="cancel_booking.php?id=<?php echo $row["id"]; ?>"
class="cancel-btn"
onclick="return confirm('Are you sure you want to cancel this booking?')">

Cancel

</a>


<?php } elseif($status=="Completed"){ ?>


<?php if($row["review_id"]){ ?>

<span style="
color:#28a745;
font-weight:600;
">
Reviewed ✓
</span>


<?php } else { ?>


<a href="give_review.php?booking_id=<?php echo $row['id']; ?>"
class="review-btn">

Give Review ⭐

</a>


<?php } ?>


<?php } else { ?>

-

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } else { ?>

<div class="empty-booking">

<i class="fas fa-calendar-xmark"></i>

<h2>No Bookings Yet</h2>

<p>You haven't booked any decorators yet.</p>

<a href="services.php" class="browse-btn">

Browse Services

</a>

</div>

<?php } ?>

</div>

</div>

<?php include("../includes/customer_footer.php"); ?>