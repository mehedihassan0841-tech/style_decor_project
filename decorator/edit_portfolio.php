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
    header("Location: portfolio.php");
    exit();
}

$id = (int)$_GET["id"];
$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM decorator_portfolio
        WHERE id = ? AND decorator_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    $_SESSION["error"] = "Portfolio not found.";

    header("Location: portfolio.php");
    exit();
}

$portfolio = $result->fetch_assoc();

?>

<div class="dashboard-content">

<div class="dashboard-main">

<div class="edit-profile-header">

<div>

<h1>Edit Portfolio</h1>

<p>Update your portfolio information.</p>

</div>

<div class="header-icon">

<i class="fas fa-pen"></i>

</div>

</div>

<div class="profile-form-card">

<form action="update_portfolio.php"
method="POST"
enctype="multipart/form-data">

<input type="hidden"
name="id"
value="<?php echo $portfolio["id"]; ?>">

<input type="hidden"
name="old_image"
value="<?php echo $portfolio["image"]; ?>">

<div class="form-group">

<label>Project Title</label>

<input type="text"
name="title"
value="<?php echo htmlspecialchars($portfolio["title"]); ?>"
required>

</div>

<div class="form-group">

<label>Description</label>

<textarea
name="description"
rows="6"
required><?php echo htmlspecialchars($portfolio["description"]); ?></textarea>

</div>

<div class="form-group">

<label>Current Image</label>

<br><br>

<img src="../uploads/portfolio/<?php echo $portfolio["image"]; ?>"
style="width:250px;border-radius:15px;">

</div>

<div class="form-group">

<label>Change Image (Optional)</label>

<input type="file"
name="image"
accept="image/*">

</div>

<button class="save-btn">

<i class="fas fa-save"></i>

Update Portfolio

</button>

</form>

</div>

</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>