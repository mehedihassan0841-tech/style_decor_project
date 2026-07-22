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

    <h1>Wishlist</h1>

    <p>
        Keep your favourite decorators in one place.
    </p>

</div>
<div class="empty-booking">

    <i class="fas fa-heart"></i>

    <h2>No Favourite Decorators</h2>

    <p>
        Save decorators to your wishlist and access them anytime.
    </p>

    <a href="decorators.php" class="browse-btn">

        Browse Decorators

    </a>

</div>

    </div>

</div>

<?php include("../includes/customer_footer.php"); ?>