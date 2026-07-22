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

$sql = "SELECT * FROM decorator_portfolio
        WHERE decorator_id = ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

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

<?php if(isset($_SESSION["error"])): ?>

<div class="error-message">

    <i class="fas fa-circle-xmark"></i>

    <?php
    echo $_SESSION["error"];
    unset($_SESSION["error"]);
    ?>

</div>

<?php endif; ?>

        <div class="edit-profile-header">

            <div>

                <h1>My Portfolio</h1>

                <p>Showcase your decoration projects.</p>

            </div>

            <div class="header-icon">

                <i class="fas fa-images"></i>

            </div>

        </div>

        <div style="margin-bottom:25px;">

            <a href="add_portfolio.php" class="save-btn" style="text-decoration:none;display:inline-block;width:auto;padding:15px 25px;">

                <i class="fas fa-plus"></i>

                Add New Work

            </a>

        </div>

        <div class="portfolio-grid">

<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<div class="portfolio-card">

<img src="../uploads/portfolio/<?php echo $row["image"]; ?>">

<h3><?php echo htmlspecialchars($row["title"]); ?></h3>

<p><?php echo htmlspecialchars($row["description"]); ?></p>

<div class="portfolio-buttons">

<a href="edit_portfolio.php?id=<?php echo $row["id"]; ?>" class="edit-btn">

<i class="fas fa-pen"></i>

Edit

</a>

<a href="delete_portfolio.php?id=<?php echo $row["id"]; ?>"
   class="delete-btn"
   onclick="return confirm('Are you sure you want to delete this portfolio?');">
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

<i class="fas fa-images"></i>

<h2>No Portfolio Added Yet</h2>

<p>Click "Add New Work" to upload your first project.</p>

</div>

<?php } ?>

</div>

</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>