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

include("../includes/decorator_header.php");
include("../includes/decorator_sidebar.php");

$decorator_id=$_SESSION["user_id"];

$sql="SELECT

b.*,

u.full_name,
u.phone,

ds.service_name,
ds.category

FROM bookings b

INNER JOIN users u
ON b.client_id=u.id

INNER JOIN decorator_services ds
ON b.service_id=ds.id

WHERE ds.decorator_id=?

ORDER BY b.id DESC";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$decorator_id);

$stmt->execute();

$result=$stmt->get_result();

?>

<div class="dashboard-content">

<div class="dashboard-main">
    <?php if(isset($_SESSION["success"])){ ?>

<div class="success-message">

<?php
echo $_SESSION["success"];
unset($_SESSION["success"]);
?>

</div>

<?php } ?>

<?php if(isset($_SESSION["error"])){ ?>

<div class="error-message">

<?php
echo $_SESSION["error"];
unset($_SESSION["error"]);
?>

</div>

<?php } ?>

<div class="page-header">

<h1>Booking Requests</h1>

<p>Manage all customer booking requests.</p>

</div>

<?php if($result->num_rows>0){ ?>

<div class="table-responsive">

<table class="booking-table">

<thead>

<tr>

<th>Booking</th>
<th>Customer</th>
<th>Service</th>
<th>Date</th>
<th>Amount</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>
    <?php while($row=$result->fetch_assoc()){ ?>

<tr>

<td>

<strong><?php echo $row["booking_code"]; ?></strong><br>

<small><?php echo htmlspecialchars($row["event_location"]); ?></small>

</td>

<td>

<?php echo htmlspecialchars($row["full_name"]); ?><br>

<small><?php echo htmlspecialchars($row["phone"]); ?></small>

</td>

<td>

<?php echo htmlspecialchars($row["service_name"]); ?><br>

<small><?php echo htmlspecialchars($row["category"]); ?></small>

</td>

<td>

<?php echo date("d M Y",strtotime($row["event_date"])); ?><br>

<small><?php echo date("h:i A",strtotime($row["event_time"])); ?></small>

</td>

<td>

৳<?php echo number_format($row["total_amount"]); ?>

</td>

<td>

<?php

$status=$row["booking_status"];

$color="#ffc107";

if($status=="Accepted"){
    $color="#28a745";
}

if($status=="Rejected"){
    $color="#dc3545";
}

if($status=="Completed"){
    $color="#007bff";
}

if($status=="Cancelled"){
    $color="#6c757d";
}

?>

<span style="
background:<?php echo $color; ?>;
color:#fff;
padding:6px 12px;
border-radius:20px;
font-size:13px;
font-weight:600;
display:inline-block;
">

<?php echo $status; ?>

</span>

</td>
<td>

<?php

if($status=="Pending"){

?>

<a href="accept_booking.php?id=<?php echo $row["id"]; ?>"
class="accept-btn"
onclick="return confirm('Accept this booking?')">

Accept

</a>

<a href="reject_booking.php?id=<?php echo $row["id"]; ?>"
class="reject-btn"
onclick="return confirm('Reject this booking?')">

Reject

</a>

<?php

}elseif($status=="Accepted"){

?>

<a href="complete_booking.php?id=<?php echo $row["id"]; ?>"
class="complete-btn"
onclick="return confirm('Mark this booking as completed?')">

Complete

</a>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php }else{ ?>

<div class="empty-booking">

<i class="fas fa-calendar-xmark"></i>

<h2>No Booking Requests</h2>

<p>No customer has booked your services yet.</p>

</div>

<?php } ?>

</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>
