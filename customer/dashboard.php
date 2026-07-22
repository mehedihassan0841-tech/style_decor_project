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

            <h2>12</h2>

            <p>Total Bookings</p>

        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon orange">

            <i class="fas fa-hourglass-half"></i>

        </div>

        <div class="stat-info">

            <h2>3</h2>

            <p>Pending</p>

        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon green">

            <i class="fas fa-circle-check"></i>

        </div>

        <div class="stat-info">

            <h2>9</h2>

            <p>Completed</p>

        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon red">

            <i class="fas fa-heart"></i>

        </div>

        <div class="stat-info">

            <h2>7</h2>

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

            <tr>

                <td>#BK001</td>
                <td>Dream Decor</td>
                <td>Wedding</td>
                <td>15 Aug 2026</td>
                <td><span class="status pending">Pending</span></td>

            </tr>

            <tr>

                <td>#BK002</td>
                <td>Royal Events</td>
                <td>Birthday</td>
                <td>20 Aug 2026</td>
                <td><span class="status approved">Approved</span></td>

            </tr>

            <tr>

                <td>#BK003</td>
                <td>Elegant Decor</td>
                <td>Engagement</td>
                <td>28 Aug 2026</td>
                <td><span class="status completed">Completed</span></td>

            </tr>

        </tbody>

    </table>

</div>

</div>
</div>





<?php include("../includes/customer_footer.php"); ?>