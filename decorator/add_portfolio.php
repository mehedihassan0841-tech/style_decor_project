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

                <h1>Add Portfolio</h1>

                <p>Upload your best decoration projects.</p>

            </div>

            <div class="header-icon">

                <i class="fas fa-images"></i>

            </div>

        </div>

        <div class="profile-form-card">

            <form action="insert_portfolio.php"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="form-group">

                    <label>
                        <i class="fas fa-heading"></i>
                        Project Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fas fa-align-left"></i>
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="6"
                        required></textarea>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fas fa-image"></i>
                        Choose Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        required>

                </div>

                <button
                    type="submit"
                    class="save-btn">

                    <i class="fas fa-cloud-upload-alt"></i>

                    Upload Portfolio

                </button>

            </form>

        </div>

    </div>

</div>

<?php include("../includes/decorator_footer.php"); ?>