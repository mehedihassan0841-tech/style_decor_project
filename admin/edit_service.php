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

if(!isset($_GET["id"])){
    header("Location: services.php");
    exit();
}

$id = (int)$_GET["id"];

/*=========================
GET SERVICE
==========================*/
$query = mysqli_query($conn, "SELECT * FROM decorator_services WHERE id='$id'");

if(mysqli_num_rows($query) == 0){
    header("Location: services.php");
    exit();
}

$service = mysqli_fetch_assoc($query);

/*=========================
GET DECORATORS
==========================*/
$decorators = mysqli_query($conn, "SELECT id, full_name FROM users WHERE role='decorator' AND status='approved' ORDER BY full_name");

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");
?>

<div class="admin-content">
    <div class="add-service-page">
        <div class="add-service-card">
            
            <div class="add-service-header">
                <h2>
                    <i class="fa-solid fa-pen"></i> Edit Service
                </h2>
                <p>Update Service Information</p>
            </div>

            <form action="update_service.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $service["id"]; ?>">

                <div class="service-form-grid">
                    
                    <!-- Decorator Field -->
                    <div class="service-input-group">
                        <label>Decorator</label>
                        <select name="decorator_id" required>
                            <?php while($row = mysqli_fetch_assoc($decorators)){ ?>
                                <option value="<?php echo $row["id"]; ?>" <?php if($row["id"] == $service["decorator_id"]) echo "selected"; ?>>
                                    <?php echo $row["full_name"]; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Service Name Field -->
                    <div class="service-input-group">
                        <label>Service Name</label>
                        <input type="text" name="service_name" value="<?php echo $service["service_name"]; ?>" required>
                    </div>

                    <!-- Category Field -->
                    <div class="service-input-group">
                        <label>Category</label>
                        <select name="category">
                            <?php
                            $categories = ["Wedding", "Birthday", "Interior", "Home Decoration", "Restaurant", "Others"];
                            foreach($categories as $cat){
                            ?>
                                <option value="<?php echo $cat; ?>" <?php if($service["category"] == $cat) echo "selected"; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Price Field -->
                    <div class="service-input-group">
                        <label>Price</label>
                        <input type="number" name="price" value="<?php echo $service["price"]; ?>" required>
                    </div>

                    <!-- Duration Field -->
                    <div class="service-input-group">
                        <label>Duration</label>
                        <input type="text" name="duration" value="<?php echo $service["duration"]; ?>" required>
                    </div>

                    <!-- Availability Field -->
                    <div class="service-input-group">
                        <label>Availability</label>
                        <select name="availability">
                            <option value="available" <?php if($service["availability"] == "available") echo "selected"; ?>>Available</option>
                            <option value="unavailable" <?php if($service["availability"] == "unavailable") echo "selected"; ?>>Unavailable</option>
                        </select>
                    </div>

                    <!-- Current Image Preview -->
                    <div class="service-input-group full-width">
                        <label>Current Image</label>
                        <?php 
                            $image_path = !empty($service["service_image"]) && file_exists("../uploads/services/".$service["service_image"]) 
                                ? "../uploads/services/".$service["service_image"] 
                                : "../images/no-image.png"; 
                        ?>
                        <img src="<?php echo $image_path; ?>" class="current-service-image" alt="Current Image">
                    </div>

                    <!-- Change Image Field -->
                    <div class="service-input-group full-width">
                        <label>Change Image</label>
                        <input type="file" name="service_image" accept="image/*">
                    </div>

                    <!-- Description Field -->
                    <div class="service-input-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="6" required><?php echo $service["description"]; ?></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="service-form-buttons">
                        <button type="submit" class="save-service-btn">
                            <i class="fa-solid fa-floppy-disk"></i> Update Service
                        </button>
                        <a href="services.php" class="cancel-service-btn">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<?php include("../includes/admin_footer.php"); ?>