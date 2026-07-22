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

        <div class="page-header">

            <h1>My Bookings</h1>

            <p>
                View and manage all your event bookings.
            </p>

        </div>
        <div class="empty-booking">

    <i class="fas fa-calendar-xmark"></i>

    <h2>No Bookings Yet</h2>

    <p>
        You haven't booked any decorators yet.
    </p>

    <a href="decorators.php" class="browse-btn">

        Browse Decorators

    </a>

</div>

    </div>

</div>

<?php include("../includes/customer_footer.php"); ?>