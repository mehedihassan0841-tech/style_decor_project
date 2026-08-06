<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] != "decorator") {
    header("Location: ../login.php");
    exit();
}

include("../includes/decorator_header.php");
include("../includes/decorator_sidebar.php");
require_once("../config/database.php");

$decorator_id = $_SESSION["user_id"];

/* Total Services */

$total_service = mysqli_fetch_assoc(

mysqli_query($conn,"SELECT COUNT(*) AS total FROM decorator_services WHERE decorator_id='$decorator_id'")

)["total"];

/* Pending Bookings */

$pending_booking = mysqli_fetch_assoc(

mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM bookings b
INNER JOIN decorator_services ds
ON b.service_id=ds.id
WHERE ds.decorator_id='$decorator_id'
AND b.booking_status='Pending'
")

)["total"];

?>

<div class="dashboard-content">

    <div class="dashboard-main">

        <!-- Welcome Section -->

        <div class="welcome-card">

            <div class="welcome-text">

                <h1>Welcome Back 👋</h1>

                <h2><?php echo $_SESSION["user_name"]; ?></h2>

                <p>
                    Manage your decoration services, bookings and portfolio from one place.
                </p>

            </div>

            <div class="welcome-status">

                <span class="status-badge <?php echo $_SESSION["user_status"]; ?>">

                    <?php echo ucfirst($_SESSION["user_status"]); ?>

                </span>

            </div>

        </div>

        <!-- Statistics -->

        <div class="stats-grid">

            <div class="stat-card">

                <i class="fas fa-briefcase"></i>

                <h3>Total Services</h3>

                <h2><?php echo $total_service; ?></h2>

            </div>

            <div class="stat-card">

                <i class="fas fa-calendar-check"></i>

                <h3>Pending Bookings</h3>

                <h2><?php echo $pending_booking; ?></h2>

            </div>

            <div class="stat-card">

                <i class="fas fa-star"></i>

                <h3>Average Rating</h3>

                <h2>0.0</h2>

            </div>

            <div class="stat-card">

                <i class="fas fa-wallet"></i>

                <h3>Total Earnings</h3>

                <h2>৳0</h2>

            </div>

        </div>

        <!-- Quick Actions -->

        <div class="dashboard-section">

            <div class="section-header">

                <h2>Quick Actions</h2>

            </div>

            <div class="action-grid">

                <a href="add_service.php" class="action-card">

                    <i class="fas fa-plus-circle"></i>

                    <span>Add Service</span>

                </a>

                <a href="portfolio.php" class="action-card">

                    <i class="fas fa-images"></i>

                    <span>Portfolio</span>

                </a>

                <a href="booking.php" class="action-card">

                    <i class="fas fa-calendar-alt"></i>

                    <span>Bookings</span>

                </a>

            </div>

        </div>

    </div>

</div>

<?php include("../includes/decorator_footer.php"); ?>