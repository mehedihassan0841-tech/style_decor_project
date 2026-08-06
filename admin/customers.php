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
  STATISTICS (Single Query Optimization)
====================================*/
$stats_query = mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) AS new_this_month
    FROM users
    WHERE role = 'customer'
");

$stats = mysqli_fetch_assoc($stats_query);

/*====================================
  PHP SEE MORE LOGIC & CUSTOMER LIST
====================================*/
// URL-এ show_all=1 আছে কিনা তা চেক করা
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';

// show_all না থাকলে শুধু ৩টি ডাটা লিমিট করা হবে
$limit_clause = $show_all ? "" : " LIMIT 3";

$customer_list = mysqli_query($conn, "
    SELECT *
    FROM users
    WHERE role = 'customer'
    ORDER BY created_at DESC
    {$limit_clause}
");
?>



<div class="customer-page-wrapper">
    <div class="customer-page-content">

        <!-- ALERT NOTIFICATION -->
        <?php if (isset($_GET["deleted"])): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "Customer Deleted Successfully",
                    timer: 1800,
                    showConfirmButton: false
                });
            </script>
        <?php endif; ?>

        <!-- PAGE HEADER -->
        <div class="customer-page-header">
            <div class="customer-page-title">
                <h1>
                    <i class="fa-solid fa-users"></i> Customers
                </h1>
                <p>Manage all registered customers of StyleDecor.</p>
            </div>
            <div class="customer-page-action">
                <a href="add_customer.php" class="customer-add-btn">
                    <i class="fa-solid fa-user-plus"></i> Add Customer
                </a>
            </div>
        </div>

        <!-- STATISTICS GRID -->
        <div class="customer-summary-grid">
            <div class="customer-summary-card">
                <div class="customer-summary-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="customer-summary-text">
                    <h2><?php echo $stats["total"] ?? 0; ?></h2>
                    <p>Total Customers</p>
                </div>
            </div>

            <div class="customer-summary-card">
                <div class="customer-summary-icon">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="customer-summary-text">
                    <h2><?php echo $stats["active"] ?? 0; ?></h2>
                    <p>Active Customers</p>
                </div>
            </div>

            <div class="customer-summary-card">
                <div class="customer-summary-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="customer-summary-text">
                    <h2><?php echo $stats["new_this_month"] ?? 0; ?></h2>
                    <p>New This Month</p>
                </div>
            </div>

            <div class="customer-summary-card">
                <div class="customer-summary-icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div class="customer-summary-text">
                    <h2><?php echo $stats["blocked"] ?? 0; ?></h2>
                    <p>Blocked Customers</p>
                </div>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <div class="customer-search-wrapper">
            <div class="customer-search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="customerSearchInput" placeholder="Search customer...">
            </div>
        </div>

        <!-- CUSTOMER TABLE -->
       <!-- ===============================
        CUSTOMER TABLE
================================ -->

<div class="customer-table-wrapper">

<table class="customer-table">

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

if(mysqli_num_rows($customer_list)>0){


while($customer=mysqli_fetch_assoc($customer_list)){


$profile_img = !empty($customer["profile_image"]) 
&& file_exists("../uploads/profile/".$customer["profile_image"])

? "../uploads/profile/".$customer["profile_image"]

: "../images/default-user.png";


?>


<tr>


<td>

<img

src="<?php echo $profile_img; ?>"

alt="Customer Photo"

class="customer-table-photo"

>

</td>



<td>


<div class="customer-info">


<h4>

<?php echo htmlspecialchars($customer["full_name"]); ?>

</h4>


<span>

#CUS<?php echo str_pad($customer["id"],4,"0",STR_PAD_LEFT); ?>

</span>


</div>


</td>



<td>

<?php echo htmlspecialchars($customer["email"]); ?>

</td>



<td>

<?php echo htmlspecialchars($customer["phone"] ?? "N/A"); ?>

</td>



<td>


<span class="customer-status <?php echo strtolower($customer["status"]); ?>">


<?php echo ucfirst($customer["status"]); ?>


</span>


</td>



<td>

<?php echo date("d M Y",strtotime($customer["created_at"])); ?>

</td>




<td>


<div class="customer-action-group">


<a

href="customer_details.php?id=<?php echo $customer['id']; ?>"

class="customer-view-btn"

title="View Details"

>


<i class="fa-solid fa-eye"></i>


</a>



<button

type="button"

class="customer-delete-btn customer-delete"

data-id="<?php echo $customer['id']; ?>"

title="Delete Customer"

>


<i class="fa-solid fa-trash"></i>


</button>



</div>


</td>



</tr>



<?php


}


}else{


?>


<tr>

<td colspan="7" style="text-align:center;padding:35px;">

No Customers Found

</td>

</tr>


<?php

}


?>


</tbody>


</table>



<!-- ===============================
        SEE MORE BUTTON
================================ -->


<?php if(($stats["total"] ?? 0) > 3){ ?>


<div class="see-more-wrapper">


<?php if($show_all){ ?>


<a href="customers.php" class="see-more-btn">


<i class="fa-solid fa-chevron-up"></i>

Show Less


</a>


<?php }else{ ?>


<a href="?show_all=1" class="see-more-btn">


<i class="fa-solid fa-users"></i>

See More Customers


</a>


<?php } ?>


</div>


<?php } ?>


</div>

<?php include("../includes/admin_footer.php"); ?>