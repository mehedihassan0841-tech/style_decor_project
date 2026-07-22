<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] != "decorator") {
    header("Location: ../login.php");
    exit();
}

include("../includes/decorator_header.php");
include("../includes/decorator_sidebar.php");

?>

<div class="dashboard-content">

    <div class="dashboard-main">

        <div class="edit-profile-header">

            <div>

                <h1>Add Service</h1>

                <p>Create a new decoration service.</p>

            </div>

            <div class="header-icon">

                <i class="fas fa-briefcase"></i>

            </div>

        </div>

        <div class="profile-form-card">

            <form action="insert_service.php"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="form-group">

                    <label>Service Name</label>

                    <input
                        type="text"
                        name="service_name"
                        required>

                </div>

                <div class="form-group">

    <label>
        <i class="fas fa-layer-group"></i>
        Category
    </label>

    <div class="custom-select">

        <select name="category" required>

            <option value="">Select Category</option>

            <option value="Wedding">💍 Wedding</option>

            <option value="Birthday">🎂 Birthday</option>

            <option value="Corporate">🏢 Corporate</option>

            <option value="Home Decoration">🏠 Home Decoration</option>

            <option value="Interior">🛋 Interior</option>

            <option value="Restaurant">🍽 Restaurant</option>

        </select>

    </div>

</div>

                <div class="form-group">

                    <label>Price (৳)</label>

                    <input
                        type="number"
                        name="price"
                        min="0"
                        step="0.01"
                        required>

                </div>

                <div class="form-group">

                    <label>Duration</label>

                    <input
                        type="text"
                        name="duration"
                        placeholder="Example: 1 Day">

                </div>

                <div class="form-group">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="6"
                        required></textarea>

                </div>

                <div class="form-group">

    <label>
        <i class="fas fa-circle-check"></i>
        Availability
    </label>

    <div class="custom-select">

        <select name="availability">

            <option value="Available">🟢 Available</option>

            <option value="Unavailable">🔴 Unavailable</option>

        </select>

    </div>

</div>

                <div class="form-group">

                    <label>Service Image</label>

                    <input
                        type="file"
                        name="service_image"
                        accept="image/*"
                        required>

                </div>

                <button
                    type="submit"
                    class="save-btn">

                    <i class="fas fa-save"></i>

                    Save Service

                </button>

            </form>

        </div>

    </div>

</div>

<?php include("../includes/decorator_footer.php"); ?>