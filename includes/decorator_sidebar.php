<div class="sidebar">

    <!-- Logo -->

    <div class="sidebar-logo">

        <h2>StyleDecor</h2>

        <span>Decorator Panel</span>

    </div>

    <!-- User -->

    <div class="sidebar-user">

        <img src="../uploads/profile/<?php echo $_SESSION["profile_image"]; ?>" alt="Profile">

        <h3><?php echo $_SESSION["user_name"]; ?></h3>

        <p><?php echo $_SESSION["user_email"]; ?></p>

    </div>

    <!-- Menu -->

    <ul class="sidebar-menu">

        <li>

            <a href="dashboard.php">

                <i class="fas fa-home"></i>

                <span>Dashboard</span>

            </a>

        </li>

        <li>

            <a href="../decorator/profile.php">

                <i class="fas fa-user"></i>

                <span>My Profile</span>

            </a>

        </li>

        <li>

            <a href="../decorator/portfolio.php">

                <i class="fas fa-images"></i>

                <span>Portfolio</span>

            </a>

        </li>

        <li>

            <a href="manage_services.php">

                <i class="fas fa-briefcase"></i>

                <span>Manage Services</span>

            </a>

        </li>

        <li>

            <a href="booking.php">

                <i class="fas fa-calendar-check"></i>

                <span>Bookings</span>

            </a>

        </li>

        <li>

            <a href="settings.php">

                <i class="fas fa-cog"></i>

                <span>Settings</span>

            </a>

        </li>

    </ul>

    <!-- Logout -->

    <a href="../auth/logout.php" class="logout-btn">

        <i class="fas fa-right-from-bracket"></i>

        Logout

    </a>
<div class="account-status">

    <span class="status-label">Account Status</span>

    <span class="status-badge <?php echo $_SESSION["user_status"]; ?>">

        <?php echo ucfirst($_SESSION["user_status"]); ?>

    </span>

</div>
</div>

