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

require_once("../config/database.php");

include("../includes/decorator_header.php");
include("../includes/decorator_sidebar.php");
$user_id = $_SESSION["user_id"];

$sql = "SELECT
u.full_name,
u.email,
u.phone,
u.profile_image,
u.status,

d.company_name,
d.experience,
d.bio,
d.specialization,
d.district,
d.address,
d.contact_number,
d.facebook,
d.instagram,
d.website

FROM users u

INNER JOIN decorator_profiles d
ON u.id=d.user_id

WHERE u.id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$user_id);

$stmt->execute();

$result=$stmt->get_result();

$decorator=$result->fetch_assoc();
?>
<div class="dashboard-content">

    <div class="dashboard-main">
        <?php if(isset($_SESSION["success"])): ?>

<div class="success-message">

    <i class="fas fa-circle-check"></i>

    <?php
        echo $_SESSION["success"];
        unset($_SESSION["success"]);
    ?>

</div>

<?php endif; ?>
        <div class="profile-banner">

    <div class="profile-image">

        <img src="../uploads/profile/<?php echo $decorator["profile_image"]; ?>" alt="Profile">

    </div>

    <div class="profile-details">

        <h2><?php echo $decorator["full_name"]; ?></h2>

        <h4>
            <?php
            if(!empty($decorator["company_name"])){
                echo $decorator["company_name"];
            }else{
                echo "No Company Added";
            }
            ?>
        </h4>

        <span class="status-badge <?php echo strtolower($decorator["status"]); ?>">
            <?php echo ucfirst($decorator["status"]); ?>
        </span>

    </div>

</div>

<div class="profile-grid">

    <div class="profile-card">

        <h3>Contact Information</h3>

        <p><strong>Email :</strong> <?php echo $decorator["email"]; ?></p>

        <p><strong>Phone :</strong> <?php echo $decorator["phone"]; ?></p>

        <p><strong>District :</strong> <?php echo $decorator["district"] ?: "Not Added"; ?></p>

        <p><strong>Address :</strong> <?php echo $decorator["address"] ?: "Not Added"; ?></p>

    </div>

    <div class="profile-card">

        <h3>Company Information</h3>

        <p><strong>Experience :</strong> <?php echo $decorator["experience"]; ?> Years</p>

        <p><strong>Specialization :</strong> <?php echo $decorator["specialization"] ?: "Not Added"; ?></p>

        <p><strong>Contact :</strong> <?php echo $decorator["contact_number"] ?: "Not Added"; ?></p>

        <p><strong>Website :</strong> <?php echo $decorator["website"] ?: "Not Added"; ?></p>

    </div>

</div>

<div class="profile-card">

    <h3>About Me</h3>

    <p>
        <?php
        if(!empty($decorator["bio"])){
            echo nl2br($decorator["bio"]);
        }else{
            echo "No bio added yet.";
        }
        ?>
    </p>

</div>

<div class="social-card">

    <h3>Social Links</h3>

    <p><strong>Facebook :</strong> <?php echo $decorator["facebook"] ?: "Not Added"; ?></p>

    <p><strong>Instagram :</strong> <?php echo $decorator["instagram"] ?: "Not Added"; ?></p>

</div>

<div class="profile-actions">

    <a href="edit_profile.php" class="edit-btn">
        <i class="fas fa-user-edit"></i>
        Edit Profile
    </a>

    <a href="change_photo.php" class="edit-btn">
        <i class="fas fa-camera"></i>
        Change Photo
    </a>

    <a href="change_password.php" class="edit-btn">
        <i class="fas fa-lock"></i>
        Change Password
    </a>

</div>

        

    </div>

</div>

<?php include("../includes/decorator_footer.php"); ?>
