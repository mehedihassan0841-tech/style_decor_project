
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

require_once("../config/database.php");

$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

include("../includes/customer_header.php");
include("../includes/customer_sidebar.php");

?>
<!-- 3D Floating Animated Background Cubes -->
<div class="cube-background-wrapper">
    <div class="cube-item"></div>
    <div class="cube-item"></div>
    <div class="cube-item"></div>
    <div class="cube-item"></div>
    <div class="cube-item"></div>
    <div class="cube-item"></div>
</div>
<div class="dashboard-content">

    <div class="dashboard-main">

        <div class="page-header">

            <h1>Change Profile Picture</h1>

            <p>Upload a new profile picture for your account.</p>

        </div>

        <?php

        if(isset($_SESSION["success"])){

            echo '<div class="alert-success">'.$_SESSION["success"].'</div>';

            unset($_SESSION["success"]);

        }

        if(isset($_SESSION["error"])){

            echo '<div class="alert-error">'.$_SESSION["error"].'</div>';

            unset($_SESSION["error"]);

        }

        ?>

        <div class="customer-photo-panel">


    <form action="update_photo.php"
          method="POST"
          enctype="multipart/form-data">



        <div class="customer-photo-preview">


            <img
            src="../uploads/profile/<?php echo $user["profile_image"]; ?>"
            alt="Profile">


        </div>





        <div class="customer-photo-field">


            <label>
                Select New Photo
            </label>


            <input
            type="file"
            name="profile_image"
            accept=".jpg,.jpeg,.png"
            required>


        </div>





        <button
        type="submit"
        class="customer-photo-upload-btn">


            <i class="fas fa-cloud-arrow-up"></i>

            Upload Photo


        </button>




    </form>


</div>

<?php include("../includes/customer_footer.php"); ?>