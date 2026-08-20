<?php
session_start();

/*=========================================
LOGIN CHECK
=========================================*/
if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["user_role"] != "admin"){
    header("Location: ../login.php");
    exit();
}

/*=========================================
DATABASE
=========================================*/
require_once("../config/database.php");

/*=========================================
SEARCH & FILTER LOGIC
=========================================*/
$search = "";
if(isset($_GET["search"])){
    $search = mysqli_real_escape_string($conn, trim($_GET["search"]));
}

$where = "WHERE 1=1";
if($search != ""){
    $where .= " AND (
        bookings.booking_code LIKE '%$search%'
        OR users.full_name LIKE '%$search%'
        OR decorator_services.service_name LIKE '%$search%'
    )";
}

/*=========================================
FILTERED COUNT FOR SEE MORE
=========================================*/
$filtered_count_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM bookings
    LEFT JOIN users ON bookings.client_id = users.id
    LEFT JOIN decorator_services ON bookings.service_id = decorator_services.id
    $where
");
$filtered_total = mysqli_fetch_assoc($filtered_count_query)["total"];

/*=========================================
SEE MORE LOGIC
=========================================*/
$show_all = isset($_GET["show_all"]) && $_GET["show_all"] == 1;
$limit_clause = $show_all ? "" : " LIMIT 3";

/*=========================================
BOOKING LIST
=========================================*/
$booking_list = mysqli_query($conn, "
    SELECT
        bookings.*,
        users.full_name,
        decorator_services.service_name,
        decorator_services.category
    FROM bookings
    LEFT JOIN users ON bookings.client_id = users.id
    LEFT JOIN decorator_services ON bookings.service_id = decorator_services.id
    $where
    ORDER BY bookings.created_at DESC
    $limit_clause
");

/*=========================================
STATISTICS (OVERALL DASHBOARD)
=========================================*/
$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM bookings"));
$pending_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM bookings WHERE booking_status='Pending'"));
$accepted_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM bookings WHERE booking_status='Accepted'"));
$completed_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM bookings WHERE booking_status='Completed'"));

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");
?>

<div class="admin-content">
    <div class="booking-page-wrapper">
        <div class="booking-page-content">

            <!-- PAGE HEADER -->
            <div class="booking-page-header">
                <div class="booking-page-title">
                    <h1><i class="fa-solid fa-calendar-check"></i> Bookings</h1>
                    <p>Manage all booking requests of StyleDecor.</p>
                </div>
            </div>

            <!-- STATISTICS CARDS -->
            <div class="booking-summary-grid">
                <div class="booking-summary-card">
                    <div class="booking-summary-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="booking-summary-text">
                        <h2><?php echo $total_booking["total"]; ?></h2>
                        <p>Total Bookings</p>
                    </div>
                </div>

                <div class="booking-summary-card">
                    <div class="booking-summary-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div class="booking-summary-text">
                        <h2><?php echo $pending_booking["total"]; ?></h2>
                        <p>Pending</p>
                    </div>
                </div>

                <div class="booking-summary-card">
                    <div class="booking-summary-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="booking-summary-text">
                        <h2><?php echo $accepted_booking["total"]; ?></h2>
                        <p>Accepted</p>
                    </div>
                </div>

                <div class="booking-summary-card">
                    <div class="booking-summary-icon"><i class="fa-solid fa-award"></i></div>
                    <div class="booking-summary-text">
                        <h2><?php echo $completed_booking["total"]; ?></h2>
                        <p>Completed</p>
                    </div>
                </div>
            </div>

            <!-- SEARCH FORM -->
            <div class="booking-search-wrapper">
                <form method="GET" class="booking-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Search booking, customer or service..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="booking-search-btn">Search</button>
                </form>
            </div>

            <!-- BOOKING TABLE -->
            <div class="booking-table-wrapper">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Category</th>
                            <th>Booking Date</th>
                            <th>Event Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($booking_list) > 0){ ?>
                            <?php while($booking = mysqli_fetch_assoc($booking_list)){ ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($booking["booking_code"]); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking["full_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($booking["service_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($booking["category"]); ?></td>
                                    <td><?php echo date("d M Y", strtotime($booking["booking_date"])); ?></td>
                                    <td><?php echo date("d M Y", strtotime($booking["event_date"])); ?></td>
                                    <td>৳<?php echo number_format($booking["total_amount"]); ?></td>
                                    <td>
                                        <span class="booking-status <?php echo strtolower($booking["booking_status"]); ?>">
                                            <?php echo ucfirst($booking["booking_status"]); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="booking-action-group">
                                            <a href="booking_details.php?id=<?php echo $booking["id"]; ?>" class="booking-view-btn" title="View">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button type="button" class="booking-delete-btn booking-delete" data-id="<?php echo $booking["id"]; ?>" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="9" style="text-align:center; padding:35px;">No Booking Found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- SEE MORE / SHOW LESS -->
                <?php if($filtered_total > 3){ ?>
                    <div class="see-more-wrapper">
                        <?php if($show_all){ ?>
                            <a href="bookings.php<?php echo $search != '' ? '?search='.urlencode($search) : ''; ?>" class="see-more-btn">
                                <i class="fa-solid fa-chevron-up"></i> Show Less
                            </a>
                        <?php } else { ?>
                            <a href="?show_all=1<?php echo $search != '' ? '&search='.urlencode($search) : ''; ?>" class="see-more-btn">
                                <i class="fa-solid fa-calendar-days"></i> See More Bookings
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/admin_booking.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Success Message (PHP to JS)
    <?php if(isset($_GET["deleted"]) && $_GET["deleted"] == 1): ?>
        Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Booking has been deleted successfully.",
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            // অ্যালার্ট চলে যাওয়ার পর URL থেকে ?deleted=1 সরিয়ে ফেলবে
            if (window.history.replaceState) {
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path:newUrl}, '', newUrl);
            }
        });
    <?php endif; ?>

    // 2. Delete Confirmation Action
    const deleteButtons = document.querySelectorAll(".booking-delete");
    deleteButtons.forEach(function(button){
        button.addEventListener("click", function(){
            const id = this.getAttribute("data-id");

            Swal.fire({
                title: "Are you sure?",
                text: "This booking will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = "delete_booking.php?id=" + id;
                }
            });
        });
    });

});
</script>

<?php include("../includes/admin_footer.php"); ?>