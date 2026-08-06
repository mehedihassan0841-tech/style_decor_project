



<div class="dashboard-wrapper">

    <!-- Sidebar -->

    <aside class="sidebar">

        <!-- Logo -->

        <div class="sidebar-logo">

            <h2>StyleDecor</h2>

            <span>Customer Panel</span>

        </div>

        <!-- User Info -->
<div class="sidebar-user">

    <img src="../uploads/profile/<?php echo $_SESSION["profile_image"]; ?>" alt="Profile">

    <h3><?php echo $_SESSION["user_name"]; ?></h3>

    <p><?php echo $_SESSION["user_email"]; ?></p>

</div>

        <!-- Menu -->

        <ul class="sidebar-menu">

            <li class="active">
                <a href="dashboard.php">
                    <i class="fas fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="profile.php">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <li>
                <a href="bookings.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Bookings</span>
                </a>
            </li>

            <li>
                <a href="wishlist.php">
                    <i class="fas fa-heart"></i>
                    <span>Wishlist</span>
                </a>
            </li>

            <li>
                <a href="give_review.php">
                    <i class="fas fa-star"></i>
                    <span>Give Review</span>
                </a>
            </li>

            <li>
                <a href="settings.php">
                    <i class="fas fa-gear"></i>
                    <span>Settings</span>
                </a>
            </li>

        </ul>

        <!-- Logout -->

        <div class="sidebar-footer">

            <a href="../auth/logout.php" class="logout-btn">

                <i class="fas fa-right-from-bracket"></i>

                Logout

            </a>

        </div>

    </aside>