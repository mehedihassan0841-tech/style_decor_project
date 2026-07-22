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

<div class="dashboard-content">

    <div class="dashboard-main">
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
        <div class="profile-header">

    <h1>My Profile</h1>

    <p>Manage your personal information.</p>

</div>

<div class="profile-container">

    <!-- Left Card -->

    <div class="profile-left">

        <img src="../uploads/profile/<?php echo $user["profile_image"]; ?>" alt="Profile">

        <h2><?php echo $user["full_name"]; ?></h2>

        <span><?php echo ucfirst($user["role"]); ?></span>

        <button class="change-photo-btn"
    onclick="window.location.href='change_photo.php'">

            <i class="fas fa-camera"></i>

            Change Photo

        </button>

    </div>

    <!-- Right Card -->

    <div class="profile-right">

        <div class="profile-item">

            <label>Full Name</label>

            <p><?php echo $user["full_name"]; ?></p>

        </div>

        <div class="profile-item">

            <label>Email</label>

            <p><?php echo $user["email"]; ?></p>

        </div>

        <div class="profile-item">

            <label>Phone</label>

            <p><?php echo $user["phone"]; ?></p>

        </div>

        <div class="profile-item">

            <label>Address</label>

            <p><?php echo $user["address"]; ?></p>

        </div>

        <div class="profile-item">

            <label>Status</label>

            <p><?php echo ucfirst($user["status"]); ?></p>

        </div>

      <button
    class="edit-profile-btn"
    onclick="document.getElementById('editProfileForm').style.display='block'">

    <i class="fas fa-pen"></i>

    Edit Profile

</button>
<div id="editProfileForm" style="display:none; margin-top:30px;">

    <form action="update_profile.php" method="POST">

        <div class="profile-item">

            <label>Full Name</label>

            <input
                type="text"
                name="full_name"
                value="<?php echo $user["full_name"]; ?>"
                required>

        </div>

        <div class="profile-item">

            <label>Phone</label>

            <input
                type="text"
                name="phone"
                value="<?php echo $user["phone"]; ?>"
                required>

        </div>

        <div class="profile-item">

            <label>Address</label>

            <input
                type="text"
                name="address"
                value="<?php echo $user["address"]; ?>"
                required>

        </div>

        <button
            type="submit"
            class="edit-profile-btn">

            Save Changes

        </button>

    </form>

</div>

    </div>

</div>

    </div>

</div>


<?php include("../includes/customer_footer.php"); ?>