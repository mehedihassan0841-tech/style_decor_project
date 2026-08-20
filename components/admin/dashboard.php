

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

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");
require_once("../config/database.php");
$pending_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE status = 'pending'
       AND role IN ('customer', 'decorator')"
);

$pending_data = mysqli_fetch_assoc($pending_query);

$pending_requests = (int)$pending_data["total"];

/* Total Customers */

$customer_query = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM users
WHERE role='customer'
");

$total_customers = mysqli_fetch_assoc($customer_query)['total'];

/* Total Decorators */

$decorator_query = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM users
WHERE role='decorator'
");

$total_decorators = mysqli_fetch_assoc($decorator_query)['total'];

/* Total Services */

$service_query = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM decorator_services
");

$total_services = mysqli_fetch_assoc($service_query)['total'];

/* Total Bookings */

$booking_query = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM bookings
");

$total_bookings = mysqli_fetch_assoc($booking_query)['total'];



/*==========================================
LATEST 3 BOOKINGS
==========================================*/

$recent_booking = mysqli_query($conn,"

SELECT

b.*,

u.full_name AS customer_name,

d.service_name,

dp.full_name AS decorator_name

FROM bookings b

INNER JOIN users u
ON b.client_id=u.id

INNER JOIN decorator_services d
ON b.service_id=d.id

INNER JOIN users dp
ON d.decorator_id=dp.id

ORDER BY b.created_at DESC

LIMIT 3

");
/*==========================================
RECENT CUSTOMERS
==========================================*/

$recent_customers = mysqli_query($conn,"

SELECT *

FROM users

WHERE role='customer'

ORDER BY created_at DESC

LIMIT 4

");


/*==========================================
RECENT DECORATORS
==========================================*/

$recent_decorators = mysqli_query($conn,"

SELECT *

FROM users

WHERE role='decorator'

ORDER BY created_at DESC

LIMIT 4

");


?>

<div class="admin-content">

    <div class="admin-header">

        <div class="admin-header-left">

            <h1>Admin Dashboard</h1>

            <p>Welcome back, <?php echo $_SESSION["user_name"]; ?> 👋</p>

        </div>

        <div class="admin-header-right">

            <div class="admin-profile">

                <img src="../uploads/profile/<?php echo $_SESSION["profile_image"]; ?>" alt="Admin">

                <div class="admin-profile-info">

                    <h3><?php echo $_SESSION["user_name"]; ?></h3>

                    <span>Administrator</span>

                </div>

            </div>

        </div>

    </div>

    <div class="admin-cards">

    

        <div class="admin-card">

            <i class="fas fa-users"></i>

            <h4>Total Customers</h4>

            <h2><?php echo $total_customers; ?></h2>

            <p>Registered Customers</p>

        </div>

        <div class="admin-card">

            <i class="fas fa-user-tie"></i>

            <h4>Total Decorators</h4>

            <h2><?php echo $total_decorators; ?></h2>

            <p>Active Decorators</p>

        </div>

        <div class="admin-card">

            <i class="fas fa-calendar-check"></i>

            <h4>Total Bookings</h4>

            <h2><?php echo $total_bookings; ?></h2>

            <p>All Booking Requests</p>

        </div>

        <div class="admin-card">

            <i class="fas fa-wallet"></i>

            <h4>Total Revenue</h4>

            <h2>৳0</h2>

            <p>Total Earnings</p>

        </div>
        <div class="admin-card">

    <i class="fas fa-user-clock"></i>

    <h4>Pending Requests</h4>

    <h2><?php echo $pending_requests; ?></h2>

    <p>Awaiting Approval</p>

    <a href="pending_requests.php" class="pending-btn">
        Review Requests
    </a>

</div>

    </div>
    <div class="dashboard-box">

    <div class="dashboard-box-header">

        <h2>
            <i class="fas fa-calendar-check"></i>
            Recent Bookings
        </h2>

        <a href="booking.php" class="view-all-btn">
            View All
        </a>

    </div>

    <table class="recent-booking-table">

        <thead>

            <tr>

                <th>Booking ID</th>

                <th>Customer</th>

                <th>Decorator</th>

                <th>Event Date</th>

                <th>Status</th>

                <th>Action</th>

            </tr>

        </thead>

        <tbody>

        <?php

        if(mysqli_num_rows($recent_booking)>0){

            while($row=mysqli_fetch_assoc($recent_booking)){

        ?>

            <tr>

                <td>

                    <?php echo $row["booking_code"]; ?>

                </td>

                <td>

                    <?php echo $row["customer_name"]; ?>

                </td>

                <td>

                    <?php echo $row["decorator_name"]; ?>

                </td>

                <td>

                    <?php echo date("d M Y",strtotime($row["event_date"])); ?>

                </td>

                <td>

                    <span class="status <?php echo strtolower($row["booking_status"]); ?>">

                        <?php echo ucfirst($row["booking_status"]); ?>

                    </span>

                </td>

                <td>

                    <a href="booking.php" class="table-view-btn">

                        View

                    </a>

                </td>

            </tr>

        <?php

            }

        }else{

        ?>

        <tr>

            <td colspan="6" style="text-align:center;padding:30px;">

                No Recent Booking Found

            </td>

        </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<div class="dashboard-box">

    <div class="dashboard-box-header">

        <h2>

            <i class="fas fa-users"></i>

            Recent Customers

        </h2>

        <a href="customers.php" class="view-all-btn">

            View All

        </a>

    </div>

    <div class="recent-users-grid">

 <?php

 while($customer=mysqli_fetch_assoc($recent_customers)){

 ?>

   <div class="recent-user-card">

      <div class="recent-user-image">

           <img src="../uploads/profile/<?php echo $customer["profile_image"]; ?>"

         alt="Customer">

        </div>

    <div class="recent-user-info">

    <h3>

    <?php echo $customer["full_name"]; ?>

   </h3>

   <p>

   <?php echo $customer["email"]; ?>

   </p>

  <span class="recent-user-status">

  <?php echo ucfirst($customer["status"]); ?>

  </span>

 </div>

 </div>

 <?php
 
 }

 ?>

</div>

 </div>
 <div class="recent-divider"></div>

<div class="dashboard-box-header">

    <h2>

        <i class="fas fa-user-tie"></i>

        Recent Decorators

    </h2>

    <a href="decorators.php" class="view-all-btn">

        View All

    </a>

</div>

<div class="recent-users-grid">

<?php

while($decorator=mysqli_fetch_assoc($recent_decorators)){

?>

<div class="recent-user-card">

    <div class="recent-user-image">

        <img src="../uploads/profile/<?php echo $decorator["profile_image"]; ?>" alt="Decorator">

    </div>

    <div class="recent-user-info">

        <h3>

            <?php echo $decorator["full_name"]; ?>

        </h3>

        <p>

            <?php echo $decorator["email"]; ?>

        </p>

        <span class="recent-user-status">

            <?php echo ucfirst($decorator["status"]); ?>

        </span>

    </div>

</div>

<?php

}

?>

</div>

<!-- ===========================
     Quick Actions Section
=========================== -->

<section class="dashboard-extra">

    <div class="dashboard-section-title">
        <h2>Quick Actions</h2>
        <p>Manage your StyleDecor platform efficiently.</p>
    </div>

    <div class="quick-actions-grid">

        <div class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>

            <h3>Add Decorator</h3>

            <p>Create a new decorator account and assign services.</p>

            <a href="#">Open <i class="fa-solid fa-arrow-right"></i></a>

        </div>

        <div class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fa-solid fa-calendar-check"></i>
            </div>

            <h3>Manage Bookings</h3>

            <p>Approve or reject customer booking requests.</p>

            <a href="#">Open <i class="fa-solid fa-arrow-right"></i></a>

        </div>

        <div class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>

            <h3>Categories</h3>

            <p>Update decoration categories and services.</p>

            <a href="#">Manage <i class="fa-solid fa-arrow-right"></i></a>

        </div>

    </div>


    <!-- ========================= -->

    <div class="dashboard-bottom-grid">

        <div class="system-status-card">

            <h2>System Status</h2>

            <ul>

                <li><i class="fa-solid fa-circle-check"></i> Database Connected</li>

                <li><i class="fa-solid fa-circle-check"></i> Server Running</li>

                <li><i class="fa-solid fa-circle-check"></i> Payment Gateway Ready</li>

                <li><i class="fa-solid fa-circle-check"></i> Email Notifications Active</li>

                <li><i class="fa-solid fa-circle-check"></i> Daily Backup Completed</li>

            </ul>

        </div>


        <div class="dashboard-tips-card">

            <h2>Platform Tips</h2>

            <ul>

                <li>Keep decorator profiles updated.</li>

                <li>Review customer registrations regularly.</li>

                <li>Approve bookings as quickly as possible.</li>

                <li>Monitor recent platform activity daily.</li>

                <li>Maintain accurate service categories.</li>

            </ul>

        </div>

    </div>

</section>

 </div>

<?php include("../includes/admin_footer.php"); ?>