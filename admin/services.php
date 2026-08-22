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

/* =========================
   SEARCH & FILTER LOGIC
========================== */
$search = $_GET['search'] ?? "";
$category = $_GET['category'] ?? "";
$availability = $_GET['availability'] ?? "";

// SHOW ALL / LIMIT LOGIC
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == 1;
$limit = $show_all ? "" : " LIMIT 3";

$where = "WHERE 1=1";

if($search != ""){
    $search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (
        decorator_services.service_name LIKE '%$search%' 
        OR users.full_name LIKE '%$search%'
    )";
}

if($category != ""){
    $category = mysqli_real_escape_string($conn, $category);
    $where .= " AND decorator_services.category='$category'";
}

if($availability != ""){
    $availability = mysqli_real_escape_string($conn, $availability);
    $where .= " AND decorator_services.availability='$availability'";
}

/* =========================
   FETCH SERVICES QUERY
========================== */
$service_list = mysqli_query($conn, "
    SELECT 
        decorator_services.*,
        users.full_name
    FROM decorator_services
    LEFT JOIN users ON decorator_services.decorator_id = users.id
    $where
    ORDER BY decorator_services.created_at DESC
    $limit
");

/* =========================
   SERVICE STATISTICS
========================== */
$total_service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM decorator_services"));
$available_service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM decorator_services WHERE availability='available'"));
$unavailable_service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM decorator_services WHERE availability='unavailable'"));
$total_category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT category) total FROM decorator_services"));

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");
?>

<!-- SWEETALERT ALERTS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if(isset($_GET["deleted"])){ ?>
<script>
Swal.fire({
    icon: "success",
    title: "Deleted!",
    text: "Service deleted successfully.",
    confirmButtonColor: "#16a34a"
});
</script>
<?php } ?>

<?php if(isset($_GET["updated"])){ ?>
<script>
Swal.fire({
    icon: "success",
    title: "Service Updated Successfully",
    showConfirmButton: false,
    timer: 1800
});
</script>
<?php } ?>

<?php if(isset($_GET["added"])){ ?>
<script>
Swal.fire({
    icon: "success",
    title: "Service Added Successfully",
    showConfirmButton: false,
    timer: 1800
});
</script>
<?php } ?>

<div class="admin-content">
    <div class="service-page-wrapper">
        <div class="service-page-content">

            <!-- PAGE HEADER -->
            <div class="service-page-header">
                <div class="service-page-title">
                    <h1>
                        <i class="fa-solid fa-briefcase"></i> Services
                    </h1>
                    <p>Manage all decoration services.</p>
                </div>
                <div class="service-page-action">
                    <a href="add_service.php" class="service-add-btn">
                        <i class="fa-solid fa-plus"></i> Add Service
                    </a>
                </div>
            </div>

            <!-- STATISTICS CARDS -->
            <div class="service-summary-grid">
                <div class="service-summary-card">
                    <div class="service-summary-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div class="service-summary-text">
                        <h2><?php echo $total_service["total"]; ?></h2>
                        <p>Total Services</p>
                    </div>
                </div>

                <div class="service-summary-card">
                    <div class="service-summary-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="service-summary-text">
                        <h2><?php echo $available_service["total"]; ?></h2>
                        <p>Available</p>
                    </div>
                </div>

                <div class="service-summary-card">
                    <div class="service-summary-icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="service-summary-text">
                        <h2><?php echo $unavailable_service["total"]; ?></h2>
                        <p>Unavailable</p>
                    </div>
                </div>

                <div class="service-summary-card">
                    <div class="service-summary-icon">
                        <i class="fa-solid fa-list"></i>
                    </div>
                    <div class="service-summary-text">
                        <h2><?php echo $total_category["total"]; ?></h2>
                        <p>Categories</p>
                    </div>
                </div>
            </div>

            <!-- SEARCH & FILTER TOOLBAR -->
            <div class="service-toolbar">
                <form method="GET" class="service-search-form">
                    <div class="service-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search service..." 
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                        >
                    </div>

                    <select name="category" class="service-filter">
                        <option value="">All Categories</option>
                        <option value="Wedding" <?php if($category == 'Wedding') echo 'selected'; ?>>Wedding</option>
                        <option value="Birthday" <?php if($category == 'Birthday') echo 'selected'; ?>>Birthday</option>
                        <option value="Interior" <?php if($category == 'Interior') echo 'selected'; ?>>Interior</option>
                        <option value="Home Decoration" <?php if($category == 'Home Decoration') echo 'selected'; ?>>Home Decoration</option>
                        <option value="Restaurant" <?php if($category == 'Restaurant') echo 'selected'; ?>>Restaurant</option>
                        <option value="Others" <?php if($category == 'Others') echo 'selected'; ?>>Others</option>
                    </select>

                    <select name="availability" class="service-filter">
                        <option value="">Availability</option>
                        <option value="available" <?php if($availability == 'available') echo 'selected'; ?>>Available</option>
                        <option value="unavailable" <?php if($availability == 'unavailable') echo 'selected'; ?>>Unavailable</option>
                    </select>

                    <button type="submit" class="service-search-btn">
                        <i class="fa-solid fa-filter"></i> Search
                    </button>
                </form>
            </div>

            <!-- SERVICE TABLE -->
            <div class="service-table-wrapper">
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Service</th>
                            <th>Decorator</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($service_list) > 0){ ?>
                            <?php while($service = mysqli_fetch_assoc($service_list)){ ?>
                                <tr>
                                    <td>
                                        <?php 
                                            $service_image = !empty($service["service_image"]) && file_exists("../uploads/services/".$service["service_image"]) 
                                                ? "../uploads/services/".$service["service_image"] 
                                                : "../images/no-image.png"; 
                                        ?>
                                        <img class="service-table-image" src="<?php echo $service_image; ?>" alt="Service Image">
                                    </td>
                                    <td>
                                        <strong><?php echo $service['service_name']; ?></strong>
                                    </td>
                                    <td><?php echo $service['full_name']; ?></td>
                                    <td><?php echo $service['category']; ?></td>
                                    <td>৳<?php echo number_format($service['price']); ?></td>
                                    <td><?php echo $service['duration']; ?></td>
                                    <td>
                                        <span class="service-status <?php echo $service['availability']; ?>">
                                            <?php echo ucfirst($service['availability']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="service-action-group">
                                            <a class="service-view-btn" href="service_details.php?id=<?php echo $service['id']; ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a class="service-edit-btn" href="edit_service.php?id=<?php echo $service['id']; ?>">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="#" class="service-delete-btn service-delete" data-id="<?php echo $service["id"]; ?>" title="Delete Service">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" style="text-align:center;padding:35px;">
                                    No Services Found
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- SEE MORE / SHOW LESS BUTTON -->
            <?php if(($total_service["total"] ?? 0) > 3){ ?>
                <div class="see-more-wrapper">
                    <?php if($show_all){ ?>
                        <a href="services.php" class="see-more-btn">
                            <i class="fa-solid fa-chevron-up"></i> Show Less
                        </a>
                    <?php } else { ?>
                        <a href="?show_all=1" class="see-more-btn">
                            <i class="fa-solid fa-layer-group"></i> See More Services
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

<!-- SWEETALERT DELETE CONFIRMATION SCRIPT -->
<script>
document.querySelectorAll(".service-delete").forEach(button => {
    button.addEventListener("click", function(e) {
        e.preventDefault();
        let id = this.dataset.id;
        
        Swal.fire({
            title: "Delete Service?",
            text: "This service will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Delete",
            cancelButtonText: "Cancel",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = "delete_service.php?id=" + id;
            }
        });
    });
});
</script>

<?php include("../includes/admin_footer.php"); ?>