<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
?>

<div class="admin-sidebar">

    <div class="admin-logo">

        <h2>StyleDecor</h2>

        <span>ADMIN PANEL</span>

    </div>

    

    <ul class="admin-menu">

        <li>

            <a href="../admin/dashboard.php">

                <i class="fas fa-gauge-high"></i>

                <span>Dashboard</span>

            </a>

        </li>

        <li>

            <a href="../admin/customers.php">

                <i class="fas fa-users"></i>

                <span>Customers</span>

            </a>

        </li>

        <li>

            <a href="../admin/decorators.php">

                <i class="fas fa-user-tie"></i>

                <span>Decorators</span>

            </a>

        </li>

        <li>

            <a href="../admin/services.php">

                <i class="fas fa-briefcase"></i>

                <span>Services</span>

            </a>

        </li>

        <li>

            <a href="../admin/bookings.php">

                <i class="fas fa-calendar-check"></i>

                <span>Bookings</span>

            </a>

        </li>

        <li>

            <a href="../admin/reviews.php">

                <i class="fas fa-star"></i>

                <span>Reviews</span>

            </a>

        </li>

        <li>

            <a href="../admin/reports.php">

                <i class="fas fa-chart-column"></i>

                <span>Reports</span>

            </a>

        </li>

        <li>

            <a href="../admin/settings.php">

                <i class="fas fa-gear"></i>

                <span>Settings</span>

            </a>

        </li>

    </ul>

    <a href="../auth/logout.php" class="admin-logout">

        <i class="fas fa-right-from-bracket"></i>

        Logout

    </a>

</div>