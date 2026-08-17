<?php

session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["user_role"] != "admin"){
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");
/* =========================================
   GET CURRENT ADMIN PROFILE
========================================= */

$admin_id = (int)$_SESSION["user_id"];

$admin_query = mysqli_prepare(
    $conn,
    "SELECT full_name, email, phone, profile_image
     FROM users
     WHERE id = ? AND role = 'admin'
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $admin_query,
    "i",
    $admin_id
);

mysqli_stmt_execute($admin_query);

$admin_result = mysqli_stmt_get_result($admin_query);

$admin = mysqli_fetch_assoc($admin_result);

mysqli_stmt_close($admin_query);

$settings_query = mysqli_query(
    $conn,
    "SELECT * FROM settings ORDER BY id ASC LIMIT 1"
);

$settings = mysqli_fetch_assoc($settings_query);

if (!$settings) {
    $settings = [];
}


include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");

?>


<?php

$settings_success = $_SESSION["settings_success"] ?? "";
$settings_error = $_SESSION["settings_error"] ?? "";

unset($_SESSION["settings_success"]);
unset($_SESSION["settings_error"]);

?>
<div class="admin-content">

    <div class="settings-page">

        <!-- =========================================
             PAGE HEADER
        ========================================== -->

        <div class="settings-page-header">

            <div class="settings-title-area">

                <div class="settings-title-icon">
                    <i class="fa-solid fa-sliders"></i>
                </div>

                <div>
                    <h1>Settings</h1>

                    <p>
                        Manage your StyleDecor website and admin preferences.
                    </p>
                </div>

            </div>

        </div>


        <!-- =========================================
             SETTINGS LAYOUT
        ========================================== -->

        <div class="settings-layout">


            <!-- =====================================
                 SETTINGS NAVIGATION
            ====================================== -->

            <div class="settings-navigation">

                <div class="settings-nav-header">

                    <span>Settings</span>

                </div>


                <a
                    href="#appearance"
                    class="settings-nav-item active"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-palette"></i>
                    </span>

                    <span>Appearance</span>

                </a>


                <a
                    href="#footer"
                    class="settings-nav-item"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>

                    <span>Footer Information</span>

                </a>


                <a
                    href="#profile"
                    class="settings-nav-item"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>

                    <span>Admin Profile</span>

                </a>


                <a
                    href="#security"
                    class="settings-nav-item"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>

                    <span>Security</span>

                </a>


                <a
                    href="#social"
                    class="settings-nav-item"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-share-nodes"></i>
                    </span>

                    <span>Social Media</span>

                </a>


                <a
                    href="#booking"
                    class="settings-nav-item"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </span>

                    <span>Booking Settings</span>

                </a>


                <a
                    href="#notifications"
                    class="settings-nav-item"
                >

                    <span class="settings-nav-icon">
                        <i class="fa-solid fa-bell"></i>
                    </span>

                    <span>Notifications</span>

                </a>

            </div>


            <!-- =====================================
                 SETTINGS CONTENT
            ====================================== -->

            <div class="settings-content">

<?php if (!empty($settings_success) || !empty($settings_error)): ?>

<?php if (!empty($settings_success)): ?>

    <div class="settings-toast" id="settingsToast">

        <div class="settings-toast-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <div class="settings-toast-body">
            <p class="settings-toast-title">Success</p>
            <p class="settings-toast-message"><?php echo htmlspecialchars($settings_success); ?></p>
        </div>

        <button type="button" class="settings-toast-close" onclick="document.getElementById('settingsToast').remove()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="settings-toast-progress"></div>

    </div>

<?php endif; ?>


<?php if (!empty($settings_error)): ?>

    <div class="settings-toast settings-toast-error" id="settingsToastError">

        <div class="settings-toast-icon">
            <i class="fa-solid fa-xmark"></i>
        </div>

        <div class="settings-toast-body">
            <p class="settings-toast-title">Something went wrong</p>
            <p class="settings-toast-message"><?php echo htmlspecialchars($settings_error); ?></p>
        </div>

        <button type="button" class="settings-toast-close" onclick="document.getElementById('settingsToastError').remove()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="settings-toast-progress"></div>

    </div>

<?php endif; ?>

<script>

    (function () {

        var toasts = document.querySelectorAll('.settings-toast');

        toasts.forEach(function (toast) {

            setTimeout(function () {

                toast.classList.add('settings-toast-hide');

                setTimeout(function () {
                    toast.remove();
                }, 350);

            }, 4000);

        });

    })();

</script>

<?php endif; ?>


                <!-- =================================
                     APPEARANCE
                ================================== -->

                <section
                    class="settings-section"
                    id="appearance"
                >

                    <div class="settings-section-header">

                        <div>

                            <h2>
                                <i class="fa-solid fa-palette"></i>
                                Website Appearance
                            </h2>

                            <p>
                                Manage your website identity and homepage banner.
                            </p>

                        </div>

                    </div>


                    <form
                        action="save_settings.php"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <input
                            type="hidden"
                            name="settings_section"
                            value="appearance"
                        >


                        <div class="settings-form-grid">


                            <div class="settings-input-group">

                                <label>
                                    Website Name
                                </label>

                                <input
                                    type="text"
                                    name="website_name"
                                    value="<?php echo htmlspecialchars($settings["website_name"] ?? "StyleDecor"); ?>"
                                    placeholder="StyleDecor"
                                >

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Website Tagline
                                </label>

                                <input
                                    type="text"
                                    name="website_tagline"
                                    value="<?php echo htmlspecialchars($settings["website_tagline"] ?? ""); ?>"
                                    placeholder="Beautiful Decorations, Perfect Moments"
                                >

                            </div>


                            <div class="settings-input-group full-width">

                                <label>
                                    Banner Title
                                </label>

                                <input
                                    type="text"
                                    name="banner_title"
                                    value="<?php echo htmlspecialchars($settings["banner_title"] ?? ""); ?>"
                                    placeholder="Make Every Moment Beautiful"
                                >

                            </div>


                            <div class="settings-input-group full-width">

                                <label>
                                    Banner Subtitle
                                </label>

                                <textarea
                                    name="banner_subtitle"
                                    rows="3"
                                    placeholder="Create unforgettable moments with our professional decoration services."
                                ><?php echo htmlspecialchars($settings["banner_subtitle"] ?? ""); ?></textarea>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Banner Button Text
                                </label>

                                <input
                                    type="text"
                                    name="banner_button_text"
                                    value="<?php echo htmlspecialchars($settings["banner_button_text"] ?? ""); ?>"
                                    placeholder="Explore Services"
                                >

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Banner Button Link
                                </label>

                                <input
                                    type="text"
                                    name="banner_button_link"
                                    value="<?php echo htmlspecialchars($settings["banner_button_link"] ?? ""); ?>"
                                    placeholder="services.php"
                                >

                            </div>


                            <div class="settings-input-group full-width">

                                <label>
                                    Banner Image
                                </label>

                                <div class="settings-file-box">

                                    <input
                                        type="file"
                                        name="banner_image"
                                        accept="image/*"
                                    >

                                    <span>
                                        Recommended: JPG, JPEG, PNG or WEBP
                                    </span>

                                </div>

                            </div>


                        </div>


                        <div class="settings-form-actions">

                            <button
                                type="submit"
                                class="settings-save-btn"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Appearance

                            </button>

                        </div>

                    </form>

                </section>


                <!-- =================================
                     FOOTER
                ================================== -->

                <section
                    class="settings-section"
                    id="footer"
                >

                    <div class="settings-section-header">

                        <div>

                            <h2>
                                <i class="fa-solid fa-location-dot"></i>
                                Footer Information
                            </h2>

                            <p>
                                Update the contact information displayed in the website footer.
                            </p>

                        </div>

                    </div>


                    <form
                        action="save_settings.php"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="settings_section"
                            value="footer"
                        >


                        <div class="settings-form-grid">


                            <div class="settings-input-group full-width">

                                <label>
                                    Footer Description
                                </label>

                                <textarea
                                    name="footer_description"
                                    rows="4"
                                    placeholder="StyleDecor helps customers find professional decoration services for their special moments."
                                ><?php echo htmlspecialchars($settings["footer_description"] ?? ""); ?></textarea>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Address
                                </label>

                                <input
                                    type="text"
                                    name="footer_address"
                                    value="<?php echo htmlspecialchars($settings["footer_address"] ?? ""); ?>"
                                    placeholder="Dhaka, Bangladesh"
                                >

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Phone Number
                                </label>

                                <input
                                    type="text"
                                    name="footer_phone"
                                    value="<?php echo htmlspecialchars($settings["footer_phone"] ?? ""); ?>"
                                    placeholder="+880 1XXXXXXXXX"
                                >

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="footer_email"
                                    value="<?php echo htmlspecialchars($settings["footer_email"] ?? ""); ?>"
                                    placeholder="info@styledecor.com"
                                >

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Copyright Text
                                </label>

                                <input
                                    type="text"
                                    name="copyright_text"
                                    value="<?php echo htmlspecialchars($settings["copyright_text"] ?? ""); ?>"
                                    placeholder="© 2026 StyleDecor. All rights reserved."
                                >

                            </div>


                        </div>


                        <div class="settings-form-actions">

                            <button
                                type="submit"
                                class="settings-save-btn"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Footer

                            </button>

                        </div>

                    </form>

                </section>


                <!-- =================================
                     ADMIN PROFILE
                ================================== -->

                <!-- =================================
     ADMIN PROFILE
================================== -->

<section
    class="settings-section"
    id="profile"
>

    <div class="settings-section-header">

        <div>

            <h2>
                <i class="fa-solid fa-user"></i>
                Admin Profile
            </h2>

            <p>
                Update your personal administrator information.
            </p>

        </div>

    </div>


    <form
        action="save_settings.php"
        method="POST"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="settings_section"
            value="profile"
        >


        <div class="settings-profile-layout">


            <!-- PROFILE IMAGE -->

            <div class="settings-profile-preview">

                <div class="settings-profile-avatar">

                    <?php

                    $profile_image =
                        $admin["profile_image"] ?? "default.png";

                    $profile_image_path =
                        "../uploads/" . $profile_image;

                    ?>

                    <img
                        src="<?php echo htmlspecialchars($profile_image_path); ?>"
                        alt="Admin Profile"
                    >

                </div>


                <label class="settings-profile-upload">

                    <i class="fa-solid fa-camera"></i>

                    Change Photo

                    <input
                        type="file"
                        name="profile_image"
                        accept="image/*"
                    >

                </label>

            </div>


            <!-- ADMIN INFORMATION -->

            <div class="settings-form-grid">


                <div class="settings-input-group">

                    <label>
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="admin_name"
                        value="<?php
                            echo htmlspecialchars(
                                $admin["full_name"] ?? ""
                            );
                        ?>"
                        placeholder="Admin Name"
                        required
                    >

                </div>


                <div class="settings-input-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="admin_email"
                        value="<?php
                            echo htmlspecialchars(
                                $admin["email"] ?? ""
                            );
                        ?>"
                        placeholder="admin@example.com"
                        required
                    >

                </div>


                <div class="settings-input-group">

                    <label>
                        Phone Number
                    </label>

                    <input
                        type="text"
                        name="admin_phone"
                        value="<?php
                            echo htmlspecialchars(
                                $admin["phone"] ?? ""
                            );
                        ?>"
                        placeholder="+880 1XXXXXXXXX"
                    >

                </div>


            </div>

        </div>


        <div class="settings-form-actions">

            <button
                type="submit"
                class="settings-save-btn"
            >

                <i class="fa-solid fa-user-pen"></i>

                Update Profile

            </button>

        </div>

    </form>

</section>


                <!-- =================================
                     SECURITY
                ================================== -->

                <section
                    class="settings-section"
                    id="security"
                >

                    <div class="settings-section-header">

                        <div>

                            <h2>
                                <i class="fa-solid fa-shield-halved"></i>
                                Security
                            </h2>

                            <p>
                                Change your administrator account password.
                            </p>

                        </div>

                    </div>


                    <form
                        action="change_password.php"
                        method="POST"
                    >

                        <div class="settings-form-grid">


                            <div class="settings-input-group full-width">

                                <label>
                                    Current Password
                                </label>

                                <div class="settings-password-field">

                                    <input
                                        type="password"
                                        name="current_password"
                                        placeholder="Enter current password"
                                        required
                                    >

                                    <i class="fa-solid fa-lock"></i>

                                </div>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    New Password
                                </label>

                                <div class="settings-password-field">

                                    <input
                                        type="password"
                                        name="new_password"
                                        placeholder="Enter new password"
                                        required
                                    >

                                    <i class="fa-solid fa-key"></i>

                                </div>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Confirm Password
                                </label>

                                <div class="settings-password-field">

                                    <input
                                        type="password"
                                        name="confirm_password"
                                        placeholder="Confirm new password"
                                        required
                                    >

                                    <i class="fa-solid fa-check"></i>

                                </div>

                            </div>


                        </div>


                        <div class="settings-form-actions">

                            <button
                                type="submit"
                                class="settings-save-btn"
                            >

                                <i class="fa-solid fa-key"></i>

                                Change Password

                            </button>

                        </div>

                    </form>

                </section>


                <!-- =================================
                     SOCIAL MEDIA
                ================================== -->

                <section
                    class="settings-section"
                    id="social"
                >

                    <div class="settings-section-header">

                        <div>

                            <h2>
                                <i class="fa-solid fa-share-nodes"></i>
                                Social Media
                            </h2>

                            <p>
                                Manage your StyleDecor social media links.
                            </p>

                        </div>

                    </div>


                    <form
                        action="save_settings.php"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="settings_section"
                            value="social"
                        >


                        <div class="settings-form-grid">


                            <div class="settings-input-group">

                                <label>
                                    Facebook
                                </label>

                                <div class="settings-social-input">

                                    <i class="fa-brands fa-facebook-f"></i>

                                    <input
                                        type="url"
                                        name="facebook_url"
                                        value="<?php echo htmlspecialchars($settings["facebook_url"] ?? ""); ?>"
                                        placeholder="https://facebook.com/"
                                    >

                                </div>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    Instagram
                                </label>

                                <div class="settings-social-input">

                                    <i class="fa-brands fa-instagram"></i>

                                    <input
                                        type="url"
                                        name="instagram_url"
                                        value="<?php echo htmlspecialchars($settings["instagram_url"] ?? ""); ?>"
                                        placeholder="https://instagram.com/"
                                    >

                                </div>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    LinkedIn
                                </label>

                                <div class="settings-social-input">

                                    <i class="fa-brands fa-linkedin-in"></i>

                                    <input
                                        type="url"
                                        name="linkedin_url"
                                        value="<?php echo htmlspecialchars($settings["linkedin_url"] ?? ""); ?>"
                                        placeholder="https://linkedin.com/"
                                    >

                                </div>

                            </div>


                            <div class="settings-input-group">

                                <label>
                                    YouTube
                                </label>

                                <div class="settings-social-input">

                                    <i class="fa-brands fa-youtube"></i>

                                    <input
                                        type="url"
                                        name="youtube_url"
                                        value="<?php echo htmlspecialchars($settings["youtube_url"] ?? ""); ?>"
                                        placeholder="https://youtube.com/"
                                    >

                                </div>

                            </div>


                        </div>


                        <div class="settings-form-actions">

                            <button
                                type="submit"
                                class="settings-save-btn"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Social Links

                            </button>

                        </div>

                    </form>

                </section>


                <!-- =================================
                     BOOKING SETTINGS
                ================================== -->

                <section
                    class="settings-section"
                    id="booking"
                >

                    <div class="settings-section-header">

                        <div>

                            <h2>
                                <i class="fa-solid fa-calendar-check"></i>
                                Booking Settings
                            </h2>

                            <p>
                                Control how customers can make bookings.
                            </p>

                        </div>

                    </div>


                    <form
                        action="save_settings.php"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="settings_section"
                            value="booking"
                        >


                        <div class="settings-option-list">


                            <div class="settings-option">

                                <div class="settings-option-info">

                                    <h3>
                                        Accept New Bookings
                                    </h3>

                                    <p>
                                        Allow customers to create new service bookings.
                                    </p>

                                </div>

                                <label class="settings-switch">

                                    <input
                                        type="checkbox"
                                        name="booking_enabled"
                                        value="1"
                                        checked
                                    >

                                    <span></span>

                                </label>

                            </div>


                            <div class="settings-option">

                                <div class="settings-option-info">

                                    <h3>
                                        Customer Cancellation
                                    </h3>

                                    <p>
                                        Allow customers to cancel their bookings.
                                    </p>

                                </div>

                                <label class="settings-switch">

                                    <input
                                        type="checkbox"
                                        name="customer_cancellation"
                                        value="1"
                                        checked
                                    >

                                    <span></span>

                                </label>

                            </div>


                            <div class="settings-option">

                                <div class="settings-option-info">

                                    <h3>
                                        Require Admin Approval
                                    </h3>

                                    <p>
                                        New bookings require administrator approval.
                                    </p>

                                </div>

                                <label class="settings-switch">

                                    <input
                                        type="checkbox"
                                        name="booking_approval"
                                        value="1"
                                    >

                                    <span></span>

                                </label>

                            </div>


                        </div>


                        <div class="settings-form-actions">

                            <button
                                type="submit"
                                class="settings-save-btn"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Booking Settings

                            </button>

                        </div>

                    </form>

                </section>


                <!-- =================================
                     NOTIFICATIONS
                ================================== -->

                <section
                    class="settings-section"
                    id="notifications"
                >

                    <div class="settings-section-header">

                        <div>

                            <h2>
                                <i class="fa-solid fa-bell"></i>
                                Notifications
                            </h2>

                            <p>
                                Choose which admin notifications you want to receive.
                            </p>

                        </div>

                    </div>


                    <form
                        action="save_settings.php"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="settings_section"
                            value="notifications"
                        >


                        <div class="settings-option-list">


                            <div class="settings-option">

                                <div class="settings-option-info">

                                    <h3>
                                        New Booking
                                    </h3>

                                    <p>
                                        Notify admin when a customer creates a booking.
                                    </p>

                                </div>

                                <label class="settings-switch">

                                    <input
                                        type="checkbox"
                                        name="notify_booking"
                                        value="1"
                                        checked
                                    >

                                    <span></span>

                                </label>

                            </div>


                            <div class="settings-option">

                                <div class="settings-option-info">

                                    <h3>
                                        New Review
                                    </h3>

                                    <p>
                                        Notify admin when a customer submits a review.
                                    </p>

                                </div>

                                <label class="settings-switch">

                                    <input
                                        type="checkbox"
                                        name="notify_review"
                                        value="1"
                                        checked
                                    >

                                    <span></span>

                                </label>

                            </div>


                            <div class="settings-option">

                                <div class="settings-option-info">

                                    <h3>
                                        New Decorator Registration
                                    </h3>

                                    <p>
                                        Notify admin when a decorator creates an account.
                                    </p>

                                </div>

                                <label class="settings-switch">

                                    <input
                                        type="checkbox"
                                        name="notify_decorator"
                                        value="1"
                                        checked
                                    >

                                    <span></span>

                                </label>

                            </div>


                        </div>


                        <div class="settings-form-actions">

                            <button
                                type="submit"
                                class="settings-save-btn"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Notifications

                            </button>

                        </div>

                    </form>

                </section>


            </div>

        </div>

    </div>

</div>


<?php include("../includes/admin_footer.php"); ?>
