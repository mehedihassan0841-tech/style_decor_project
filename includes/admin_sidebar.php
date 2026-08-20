<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- ================= MOBILE TOPBAR ================= -->
<div class="mobile-topbar">
    <div class="mobile-logo">
        <h2>StyleDecor</h2>
        <span>ADMIN PANEL</span>
    </div>

    <div class="mobile-top-links">
        <a href="../admin/dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>
        <a href="../admin/customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Customers</span>
        </a>
        <a href="../admin/decorators.php" class="<?php echo ($current_page == 'decorators.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-tie"></i>
            <span>Decorators</span>
        </a>
        <a href="../auth/logout.php" class="mobile-logout">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- ================= DESKTOP SIDEBAR ================= -->
<div class="admin-sidebar">
    <div class="desktop-sidebar-content">
        <div class="admin-logo">
            <h2>StyleDecor</h2>
            <span>ADMIN PANEL</span>
        </div>

        <ul class="admin-menu">
            <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="../admin/dashboard.php">
                    <i class="fas fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
                <a href="../admin/customers.php">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'decorators.php') ? 'active' : ''; ?>">
                <a href="../admin/decorators.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Decorators</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">
                <a href="../admin/services.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Services</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>">
                <a href="../admin/bookings.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
                <a href="../admin/reviews.php">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                <a href="../admin/reports.php">
                    <i class="fas fa-chart-column"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <a href="../admin/settings.php">
                    <i class="fas fa-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="../auth/logout.php" class="admin-logout">
                    <i class="fas fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- ================= MOBILE BOTTOM NAVBAR ================= -->
<div class="mobile-bottom-nav">
    <a href="../admin/services.php" class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">
        <i class="fas fa-briefcase"></i>
        <span>Services</span>
    </a>
    <a href="../admin/bookings.php" class="<?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings</span>
    </a>
    <a href="../admin/reviews.php" class="<?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
        <i class="fas fa-star"></i>
        <span>Reviews</span>
    </a>
    <a href="../admin/reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
        <i class="fas fa-chart-column"></i>
        <span>Reports</span>
    </a>
    <a href="../admin/settings.php" class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
        <i class="fas fa-gear"></i>
        <span>Settings</span>
    </a>
    <a href="../admin/customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
        <i class="fas fa-users"></i>
        <span>Customers</span>
    </a>
</div>