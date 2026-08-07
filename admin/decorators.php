<?php
session_start();

// Auth Check
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");


require_once("../config/database.php");

/*====================================
  STATISTICS QUERIES
====================================*/
$total_decorator_query = mysqli_query($conn, "
SELECT COUNT(*) total
FROM users
WHERE role='decorator'
");

$total_decorator = mysqli_fetch_assoc($total_decorator_query);

$active_decorator_query = mysqli_query($conn, "
SELECT COUNT(*) total
FROM users
WHERE role='decorator'
AND status='active'
");

$active_decorator = mysqli_fetch_assoc($active_decorator_query);

$blocked_decorator_query = mysqli_query($conn, "
SELECT COUNT(*) total
FROM users
WHERE role='decorator'
AND status='blocked'
");

$blocked_decorator = mysqli_fetch_assoc($blocked_decorator_query);

$new_decorator_query = mysqli_query($conn, "
SELECT COUNT(*) total
FROM users
WHERE role='decorator'
AND MONTH(created_at)=MONTH(CURRENT_DATE())
AND YEAR(created_at)=YEAR(CURRENT_DATE())
");

$new_decorator = mysqli_fetch_assoc($new_decorator_query);

/*====================================
  PHP SEE MORE LOGIC & DECORATOR LIST
====================================*/
// URL-এ show_all=1 আছে কিনা তা চেক করা
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';

// show_all না থাকলে শুধু ৩টি ডাটা লিমিট করা হবে
$limit_clause = $show_all ? "" : " LIMIT 3";

$search = "";

if (isset($_GET["search"])) {
    $search = mysqli_real_escape_string($conn, $_GET["search"]);
}

$decorator_list = mysqli_query($conn,"
SELECT *
FROM users
WHERE role='decorator'
AND
(
full_name LIKE '%$search%'
OR
email LIKE '%$search%'
OR
phone LIKE '%$search%'
)
ORDER BY created_at DESC
$limit_clause
");
?>
<?php

if(isset($_GET["deleted"])){

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

Swal.fire({

icon:"success",

title:"Decorator Deleted Successfully",

timer:1800,

showConfirmButton:false

});

</script>

<?php

}

?>
<div class="decorator-page-wrapper">
    <div class="decorator-page-content">

        <!-- ALERT NOTIFICATION -->
        <?php if (isset($_GET["deleted"])): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "Decorator Deleted Successfully",
                    timer: 1800,
                    showConfirmButton: false
                });
            </script>
        <?php endif; ?>

        <!-- PAGE HEADER -->
        <div class="decorator-page-header">
            <div class="decorator-page-title">
                <h1>
                    <i class="fa-solid fa-users"></i> Decorators
                </h1>
                <p>Manage all registered decorators of StyleDecor.</p>
            </div>
            <div class="decorator-page-action">
                <a href="add_decorator.php" class="decorator-add-btn">
                    <i class="fa-solid fa-user-plus"></i> Add Decorator
                </a>
            </div>
        </div>

        <!-- STATISTICS GRID -->
        <div class="decorator-summary-grid">
            <div class="decorator-summary-card">
                <div class="decorator-summary-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="decorator-summary-text">
                    <h2><?php echo $total_decorator["total"] ?? 0; ?></h2>
                    <p>Total Decorators</p>
                </div>
            </div>

            <div class="decorator-summary-card">
                <div class="decorator-summary-icon">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="decorator-summary-text">
                    <h2><?php echo $active_decorator["total"] ?? 0; ?></h2>
                    <p>Active Decorators</p>
                </div>
            </div>

            <div class="decorator-summary-card">
                <div class="decorator-summary-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="decorator-summary-text">
                    <h2><?php echo $new_decorator["total"] ?? 0; ?></h2>
                    <p>New This Month</p>
                </div>
            </div>

            <div class="decorator-summary-card">
                <div class="decorator-summary-icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div class="decorator-summary-text">
                    <h2><?php echo $blocked_decorator["total"] ?? 0; ?></h2>
                    <p>Blocked Decorators</p>
                </div>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <div class="decorator-search-wrapper">
            <form method="GET" class="decorator-search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="search"
                    placeholder="Search decorator..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                >
                <button type="submit" class="decorator-search-btn">
                    Search
                </button>
            </form>
        </div>

        <!-- DECORATOR TABLE -->
        <div class="decorator-table-wrapper">
            <table class="decorator-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name & ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    if (mysqli_num_rows($decorator_list) > 0) {
                        while ($decorator = mysqli_fetch_assoc($decorator_list)) {
                            $profile_img = !empty($decorator["profile_image"]) && file_exists("../uploads/profile/" . $decorator["profile_image"])
                                ? "../uploads/profile/" . $decorator["profile_image"]
                                : "../images/default-user.png";
                    ?>
                            <tr>
                                <td>
                                    <img
                                        src="<?php echo $profile_img; ?>"
                                        alt="Decorator Photo"
                                        class="decorator-table-photo"
                                    >
                                </td>

                                <td>
                                    <div class="decorator-info">
                                        <h4><?php echo htmlspecialchars($decorator["full_name"]); ?></h4>
                                        <span>#DEC<?php echo str_pad($decorator["id"], 4, "0", STR_PAD_LEFT); ?></span>
                                    </div>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($decorator["email"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($decorator["phone"] ?? "N/A"); ?>
                                </td>

                                <td>
                                    <span class="decorator-status <?php echo strtolower($decorator["status"]); ?>">
                                        <?php echo ucfirst($decorator["status"]); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo date("d M Y", strtotime($decorator["created_at"])); ?>
                                </td>

                                <td>
                                    <div class="decorator-action-group">
                                        <a
                                            href="decorator_details.php?id=<?php echo $decorator['id']; ?>"
                                            class="decorator-view-btn"
                                            title="View Details"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <button
                                            type="button"
                                            class="decorator-delete-btn decorator-delete"
                                            data-id="<?php echo $decorator['id']; ?>"
                                            title="Delete Decorator"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:35px;">
                                No Decorators Found
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- SEE MORE BUTTON -->
            <?php if (($total_decorator["total"] ?? 0) > 3) { ?>
                <div class="see-more-wrapper">
                    <?php if ($show_all) { ?>
                        <a href="decorators.php?search=<?php echo urlencode($search); ?>" class="see-more-btn">
                            <i class="fa-solid fa-chevron-up"></i>
                            Show Less
                        </a>
                    <?php } else { ?>
                        <a href="?show_all=1&search=<?php echo urlencode($search); ?>" class="see-more-btn">
                            <i class="fa-solid fa-users"></i>
                            See More Decorators
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.querySelectorAll(".decorator-delete").forEach(button=>{

button.addEventListener("click",function(){

const id=this.dataset.id;

Swal.fire({

title:"Delete Decorator?",

text:"This action cannot be undone.",

icon:"warning",

showCancelButton:true,

confirmButtonColor:"#16a34a",

cancelButtonColor:"#ef4444",

confirmButtonText:"Yes, Delete"

}).then((result)=>{

if(result.isConfirmed){

window.location="delete_decorator.php?id="+id;

}

});

});

});

</script>
<?php include("../includes/admin_footer.php"); ?>