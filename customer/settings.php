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

    <h1>Account Settings</h1>

    <p>
        Manage your account settings.
    </p>

</div>
<div class="settings-card">

    <div class="setting-item">

        <div>

            <h3>Change Password</h3>

            <p>Update your account password.</p>

        </div>

        <a href="change_password.php" class="browse-btn">

            Change

        </a>

    </div>

    <hr>

    <div class="setting-item">

        <div>

            <h3>Profile Picture</h3>

            <p>Upload your profile picture.</p>

        </div>

        <a href="change_photo.php" class="browse-btn">

            Upload

        </a>

    </div>

</div>

    </div>

</div>

<?php include("../includes/customer_footer.php"); ?>