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

include("../includes/customer_header.php");

include("../includes/customer_sidebar.php");
require_once("../config/database.php");

$customer_id = $_SESSION["user_id"];

/* Total Bookings */
$total_booking = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM bookings WHERE client_id='$customer_id'")
)['total'];

/* Pending */
$pending_booking = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM bookings WHERE client_id='$customer_id' AND booking_status='Pending'")
)['total'];

/* Completed */
$completed_booking = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM bookings WHERE client_id='$customer_id' AND booking_status='Completed'")
)['total'];

/* Wishlist */
$wishlist = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM wishlist WHERE customer_id='$customer_id'")
)['total'];
$recent_booking = mysqli_query($conn,"
SELECT
b.booking_code,
b.event_date,
b.booking_status,
ds.category,
dp.company_name
FROM bookings b
INNER JOIN decorator_services ds
ON b.service_id = ds.id
INNER JOIN decorator_profiles dp
ON ds.decorator_id = dp.user_id
WHERE b.client_id='$customer_id'
ORDER BY b.id DESC
LIMIT 5
");
?>

<div class="dashboard-content">

    <div class="dashboard-main">
        <div class="dashboard-header">

    <div class="header-left">
        <h2>

<i class="fas fa-house"></i>

Customer Dashboard

</h2>

        


    </div>

    <div class="header-right">

        <button class="notification-btn">

            <i class="fas fa-bell"></i>

        </button>

        <div class="header-profile">

           <img src="../uploads/profile/<?php echo $_SESSION["profile_image"]; ?>" alt="Profile">

            <div>

                <h4><?php echo $_SESSION["user_name"]; ?></h4>

                <span>Customer</span>

            </div>

        </div>

    </div>

</div>
    <div class="welcome-card">

    <div>

           <h1>

            Welcome Back, <?php echo $_SESSION["user_name"]; ?> 👋

        </h1>

        <p>
            Manage your bookings, profile and favourite decorators
            from one place.
        </p>

    <a href="services.php" class="browse-service-btn">

        <i class="fas fa-store"></i>

        Browse Services

    </a>

    </div>

    <div>

        <i class="fas fa-calendar-check welcome-icon"></i>

    </div>

</div>
<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-icon blue">

            <i class="fas fa-calendar-check"></i>

        </div>

        <div class="stat-info">

            <h2><?php echo $total_booking; ?></h2>

            <p>Total Bookings</p>

        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon orange">

            <i class="fas fa-hourglass-half"></i>

        </div>

        <div class="stat-info">

            <h2><?php echo $pending_booking; ?></h2>

            <p>Pending</p>

        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon green">

            <i class="fas fa-circle-check"></i>

        </div>

        <div class="stat-info">

            <h2><?php echo $completed_booking; ?></h2>

            <p>Completed</p>

        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon red">

            <i class="fas fa-heart"></i>

        </div>

        <div class="stat-info">

           <h2><?php echo $wishlist; ?></h2>

            <p>Wishlist</p>

        </div>

    </div>

</div>

<!-- Recent Bookings -->

<div class="recent-bookings">

    <div class="section-title">

        <h2>Recent Bookings</h2>

        <a href="bookings.php">View All</a>

    </div>

    <table>

        <thead>

            <tr>

                <th>Booking ID</th>
                <th>Decorator</th>
                <th>Event</th>
                <th>Date</th>
                <th>Status</th>

            </tr>

        </thead>

        <tbody>

           <?php if(mysqli_num_rows($recent_booking)>0){ ?>

<?php while($row=mysqli_fetch_assoc($recent_booking)){ ?>

<tr>

<td><?php echo $row["booking_code"]; ?></td>

<td><?php echo htmlspecialchars($row["company_name"]); ?></td>

<td><?php echo htmlspecialchars($row["category"]); ?></td>

<td><?php echo date("d M Y",strtotime($row["event_date"])); ?></td>

<td>

<?php

$status=$row["booking_status"];

$class="pending";

if($status=="Accepted"){
    $class="approved";
}

if($status=="Completed"){
    $class="completed";
}

if($status=="Rejected"){
    $class="rejected";
}

if($status=="Cancelled"){
    $class="cancelled";
}

?>

<span class="status <?php echo $class; ?>">

<?php echo $status; ?>

</span>

</td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="5" style="text-align:center;padding:30px;">

No bookings found.

</td>

</tr>

<?php } ?>

        </tbody>

    </table>

</div>

</div>
</div>





<?php include("../includes/customer_footer.php"); ?>