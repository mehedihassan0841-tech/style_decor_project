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

$sql = "SELECT * FROM decorator_profiles WHERE user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$profile = $result->fetch_assoc();

?>

<div class="dashboard-content">

    <div class="dashboard-main">

        <div class="edit-profile-header">

    <div>

        <h1>Edit Profile</h1>

        <p>Keep your professional information updated to attract more clients.</p>

    </div>

    <div class="header-icon">

        <i class="fas fa-user-edit"></i>

    </div>

</div>

        <div class="profile-form-card">

           <form action="update_profile.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text"
                           name="company_name"
                           value="<?php echo htmlspecialchars($profile["company_name"]); ?>">
                </div>

                <div class="form-group">
                    <label>Experience (Years)</label>
                    <input type="number"
                           name="experience"
                           value="<?php echo $profile["experience"]; ?>">
                </div>

                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text"
                           name="specialization"
                           value="<?php echo htmlspecialchars($profile["specialization"]); ?>">
                </div>

                <div class="form-group">
                    <label>District</label>
                    <input type="text"
                           name="district"
                           value="<?php echo htmlspecialchars($profile["district"]); ?>">
                </div>

                <div class="form-group">
                    <label>Address</label>

                    <textarea
                        name="address"
                        rows="4"><?php echo htmlspecialchars($profile["address"]); ?></textarea>

                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text"
                           name="contact_number"
                           value="<?php echo htmlspecialchars($profile["contact_number"]); ?>">
                </div>

                <div class="form-group">
                    <label>Facebook</label>
                    <input type="url"
                           name="facebook"
                           value="<?php echo htmlspecialchars($profile["facebook"]); ?>">
                </div>

                <div class="form-group">
                    <label>Instagram</label>
                    <input type="url"
                           name="instagram"
                           value="<?php echo htmlspecialchars($profile["instagram"]); ?>">
                </div>

                <div class="form-group">
                    <label>Website</label>
                    <input type="url"
                           name="website"
                           value="<?php echo htmlspecialchars($profile["website"]); ?>">
                </div>

                <div class="form-group">

                    <label>Bio</label>

                    <textarea
                        name="bio"
                        rows="6"><?php echo htmlspecialchars($profile["bio"]); ?></textarea>

                </div>
                <div class="form-actions">

    <a href="profile.php" class="cancel-btn">

        <i class="fas fa-arrow-left"></i>

        Back to Profile

    </a>

</div>

                <button type="submit" class="save-btn">

                    <i class="fas fa-save"></i>

                    Save Changes

                </button>

            </form>

        </div>

    </div>

</div>

<?php include("../includes/decorator_footer.php"); ?>
