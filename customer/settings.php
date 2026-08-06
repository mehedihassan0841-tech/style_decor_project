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
                Manage your account preferences and security settings.
            </p>

        </div>



        <div class="settings-grid">



            <div class="setting-card">

                <div class="setting-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <h3>Change Password</h3>

                <p>
                    Update your password and keep your account secure.
                </p>

                <a href="change_password.php" class="setting-btn">
                    Change Password
                </a>

            </div>





            <div class="setting-card">

                <div class="setting-icon">
                    <i class="fa-solid fa-image"></i>
                </div>

                <h3>Profile Picture</h3>

                <p>
                    Upload or change your profile photo.
                </p>

                <a href="change_photo.php" class="setting-btn">
                    Upload Photo
                </a>

            </div>





            <div class="setting-card">

                <div class="setting-icon">
                    <i class="fa-solid fa-user"></i>
                </div>

                <h3>Personal Information</h3>

                <p>
                    Update your name, phone number and personal details.
                </p>

                <a href="profile.php" class="setting-btn">
                    Edit Profile
                </a>

            </div>





            <div class="setting-card">

                <div class="setting-icon">
                    <i class="fa-solid fa-bell"></i>
                </div>

                <h3>Notifications</h3>

                <p>
                    Manage booking alerts and account notifications.
                </p>

                <a href="#" class="setting-btn">
                    Manage
                </a>

            </div>





            <div class="setting-card">

                <div class="setting-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <h3>Privacy & Security</h3>

                <p>
                    Control your account privacy and security options.
                </p>

                <a href="#" class="setting-btn">
                    Settings
                </a>

            </div>





            <div class="setting-card">

                <div class="setting-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>

                <h3>Help & Support</h3>

                <p>
                    Need help? Contact our support team anytime.
                </p>

                <a href="#" class="setting-btn">
                    Contact
                </a>

            </div>




        </div>


    </div>

</div>

<?php include("../includes/customer_footer.php"); ?>
