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

if (!isset($_GET["id"])) {
    header("Location: manage_services.php");
    exit();
}

$id = (int)$_GET["id"];
$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM decorator_services
        WHERE id=?
        AND decorator_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii",$id,$user_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    $_SESSION["error"]="Service not found.";

    header("Location: manage_services.php");
    exit();

}

$service = $result->fetch_assoc();

?>

<div class="dashboard-content">

<div class="dashboard-main">

<div class="edit-profile-header">

<div>

<h1>Edit Service</h1>

<p>Update your decoration service.</p>

</div>

<div class="header-icon">

<i class="fas fa-briefcase"></i>

</div>

</div>

<div class="profile-form-card">

<form action="update_service.php"
method="POST"
enctype="multipart/form-data">

<input type="hidden"
name="id"
value="<?php echo $service["id"]; ?>">

<input type="hidden"
name="old_image"
value="<?php echo $service["service_image"]; ?>">

<div class="form-group">

<label>Service Name</label>

<input
type="text"
name="service_name"
value="<?php echo htmlspecialchars($service["service_name"]); ?>"
required>

</div>

<div class="form-group">

<label>Category</label>

<div class="custom-select">

<select name="category" required>

<?php

$categories = [
"Wedding",
"Birthday",
"Corporate",
"Home Decoration",
"Interior",
"Restaurant"
];

foreach($categories as $cat){

?>

<option
value="<?php echo $cat;?>"
<?php if($service["category"]==$cat) echo "selected"; ?>>

<?php echo $cat;?>

</option>

<?php } ?>

</select>

</div>

</div>

<div class="form-group">

<label>Price</label>

<input
type="number"
step="0.01"
name="price"
value="<?php echo $service["price"]; ?>"
required>

</div>

<div class="form-group">

<label>Duration</label>

<input
type="text"
name="duration"
value="<?php echo htmlspecialchars($service["duration"]); ?>">

</div>

<div class="form-group">

<label>Description</label>

<textarea
name="description"
rows="6"
required><?php echo htmlspecialchars($service["description"]); ?></textarea>

</div>

<div class="form-group">

<label>Availability</label>

<div class="custom-select">

<select name="availability">

<option value="Available"
<?php if($service["availability"]=="Available") echo "selected"; ?>>

Available

</option>

<option value="Unavailable"
<?php if($service["availability"]=="Unavailable") echo "selected"; ?>>

Unavailable

</option>

</select>

</div>

</div>

<div class="form-group">

<label>Current Image</label>

<br><br>

<img
src="../uploads/services/<?php echo $service["service_image"]; ?>"
style="width:260px;border-radius:15px;">

</div>

<div class="form-group">

<label>Change Image (Optional)</label>

<input
type="file"
name="service_image">

</div>

<button
type="submit"
class="save-btn">

<i class="fas fa-save"></i>

Update Service

</button>

</form>

</div>

</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>