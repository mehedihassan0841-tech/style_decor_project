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

$sql = "SELECT * FROM decorator_services
        WHERE decorator_id=?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<div class="dashboard-content">

<div class="dashboard-main">

<div class="edit-profile-header">

<div>

<h1>Manage Services</h1>

<p>Create and manage your decoration services.</p>

</div>

<div class="header-icon">

<i class="fas fa-briefcase"></i>

</div>

</div>

<?php if(isset($_SESSION["success"])){ ?>

<div class="success-message">

<?php

echo $_SESSION["success"];
unset($_SESSION["success"]);

?>

</div>

<?php } ?>

<?php if(isset($_SESSION["error"])){ ?>

<div class="error-message">

<?php

echo $_SESSION["error"];
unset($_SESSION["error"]);

?>

</div>

<?php } ?>

<div style="margin-bottom:25px;">

<a href="add_service.php"
class="save-btn"
style="text-decoration:none;display:inline-block;width:auto;padding:15px 25px;">

<i class="fas fa-plus"></i>

Add New Service

</a>

</div>

<div class="portfolio-grid">

<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<div class="portfolio-card">

<img src="../uploads/services/<?php echo $row["service_image"]; ?>">

<h3><?php echo htmlspecialchars($row["service_name"]); ?></h3>

<p>

<b>Category :</b>

<?php echo htmlspecialchars($row["category"]); ?>

</p>

<p>

<b>Price :</b>

৳ <?php echo number_format($row["price"],2); ?>

</p>

<p>

<b>Status :</b>

<?php echo htmlspecialchars($row["availability"]); ?>

</p>

<div class="portfolio-buttons">

<a href="edit_service.php?id=<?php echo $row["id"]; ?>"
class="edit-btn">

<i class="fas fa-pen"></i>

Edit

</a>

<a href="delete_service.php?id=<?php echo $row["id"]; ?>"
class="delete-btn"
onclick="return confirm('Delete this service?')">

<i class="fas fa-trash"></i>

Delete

</a>

</div>

</div>

<?php

}

}else{

?>

<div class="empty-portfolio">

<i class="fas fa-briefcase"></i>

<h2>No Service Added</h2>

<p>Click "Add New Service" to create your first service.</p>

</div>

<?php } ?>

</div>

</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>