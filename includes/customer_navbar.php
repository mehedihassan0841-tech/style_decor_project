<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

?>

<nav class="customer-navbar">

    <div class="customer-nav-container">

        <!-- LOGO -->
        <a href="../index.php" class="customer-logo">
            <span class="logo-style">Style</span><span class="logo-decor">Decor</span>
        </a>


        <!-- NAVIGATION -->
        <ul class="customer-nav-menu">

            <li>
                <a
                    href="../index.php"
                    class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"
                >
                    <i class="fas fa-home"></i>
                    Home
                </a>
            </li>


            <li>
                <a
                    href="../services.php"
                    class="<?php echo ($current_page == 'services.php' || $current_page == 'service_details.php') ? 'active' : ''; ?>"
                >
                    <i class="fas fa-concierge-bell"></i>
                    Services
                </a>
            </li>


            <li>
                <a
                    href="../decorators.php"
                    class="<?php echo ($current_page == 'decorators.php' || $current_page == 'decorator_profile.php') ? 'active' : ''; ?>"
                >
                    <i class="fas fa-user-tie"></i>
                    Decorators
                </a>
            </li>


            <li>
                <a
                    href="../about.php"
                    class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>"
                >
                    <i class="fas fa-circle-info"></i>
                    About
                </a>
            </li>


            <li>
                <a
                    href="../contact.php"
                    class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>"
                >
                    <i class="fas fa-envelope"></i>
                    Contact
                </a>
            </li>

        </ul>


        <!-- CUSTOMER AREA -->
        <div class="customer-nav-right">

            <a href="dashboard.php" class="dashboard-link">
                <i class="fas fa-gauge-high"></i>
                Dashboard
            </a>


            <a href="profile.php" class="customer-profile-link">
                <i class="fas fa-user-circle"></i>

                <?php
                echo htmlspecialchars($_SESSION["full_name"] ?? "Customer");
                ?>
            </a>


            <a href="index.php" class="customer-logout">
                <i class="fas fa-right-from-bracket"></i>
                Logout
            </a>

        </div>

    </div>

</nav>