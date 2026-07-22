<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] != "customer") {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

include("../includes/customer_header.php");
include("../includes/customer_sidebar.php");

/*==========================
GET ALL AVAILABLE SERVICES
==========================*/

$search = $_GET["search"] ?? "";
$category = $_GET["category"] ?? "";
$price = $_GET["price"] ?? "";

$sql = "SELECT
ds.*,
dp.company_name,
u.full_name

FROM decorator_services ds

INNER JOIN decorator_profiles dp
ON ds.decorator_id = dp.user_id

INNER JOIN users u
ON ds.decorator_id = u.id

WHERE ds.availability='Available'";

if(!empty($search)){

    $search = trim($search);

    $search = $conn->real_escape_string($search);

    $sql .= " AND (

        ds.service_name LIKE '%{$search}%'

        OR

        ds.category LIKE '%{$search}%'

        OR

        dp.company_name LIKE '%{$search}%'

        OR

        u.full_name LIKE '%{$search}%'

    )";

}

if(!empty($category)){

    $category = $conn->real_escape_string($category);

    $sql .= " AND ds.category='$category'";

}

if(!empty($price)){

    $price = (int)$price;

    $sql .= " AND ds.price <= $price";

}

$sql .= " ORDER BY ds.id DESC";

$result = $conn->query($sql);

$result = $conn->query($sql);

$wishlist = [];

$customer_id = $_SESSION["user_id"];

$wish_sql = "SELECT service_id
FROM wishlist
WHERE customer_id=?";

$wish_stmt = $conn->prepare($wish_sql);

$wish_stmt->bind_param("i",$customer_id);

$wish_stmt->execute();

$wish_result = $wish_stmt->get_result();

while($wish = $wish_result->fetch_assoc()){

    $wishlist[] = $wish["service_id"];

}

?>

<div class="dashboard-content">

    <div class="dashboard-main">

        <div class="page-header">

            <h1>Browse Services</h1>

            <p>
                Find the perfect decoration service for your event.
            </p>

        </div>
        <div class="service-filter-bar">

    <form method="GET" class="filter-form">

        <div class="filter-search">

            <i class="fas fa-search"></i>

            <input
                type="text"
                name="search"
                placeholder="Search services..."
                value="<?php echo $_GET['search'] ?? ''; ?>">

        </div>

        <select name="category">

            <option value="">All Categories</option>

            <option value="Wedding">Wedding</option>

            <option value="Birthday">Birthday</option>

            <option value="Engagement">Engagement</option>

            <option value="Corporate">Corporate</option>

            <option value="Interior">Interior</option>

            <option value="Others">Others</option>

        </select>

        <select name="price">

            <option value="">Any Price</option>

            <option value="5000">Under ৳5,000</option>

            <option value="10000">Under ৳10,000</option>

            <option value="20000">Under ৳20,000</option>

            <option value="50000">Under ৳50,000</option>

        </select>

        <button type="submit" class="filter-btn">

            <i class="fas fa-filter"></i>

            Filter

        </button>

    </form>

</div>

        <div class="services-grid">

<?php

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

?>

<div class="service-card">

    <div class="service-image-box">

        <img src="../uploads/services/<?php echo $row["service_image"]; ?>" alt="Service">

        <span class="price-tag">

            ৳<?php echo number_format($row["price"]); ?>

        </span>

        <span class="available-tag">

            <?php echo $row["availability"]; ?>

        </span>

    </div>

    <div class="service-content">

        <span class="category">

            <?php echo $row["category"]; ?>

        </span>

        <h3>

            <?php echo htmlspecialchars($row["service_name"]); ?>

        </h3>

        <p>

            <i class="fas fa-building"></i>

            <?php echo htmlspecialchars($row["company_name"]); ?>

        </p>

        <p>

            <i class="fas fa-user"></i>

            <?php echo htmlspecialchars($row["full_name"]); ?>

        </p>

        <p>

            <i class="fas fa-clock"></i>

            <?php echo $row["duration"]; ?>

        </p>

        <div class="service-buttons">

    <?php if(in_array($row["id"],$wishlist)){ ?>

        <a href="remove_wishlist.php?id=<?php echo $row["id"]; ?>" class="wish-btn active">

            <i class="fas fa-heart"></i>

        </a>

    <?php } else { ?>

        <a href="add_wishlist.php?id=<?php echo $row["id"]; ?>" class="wish-btn">

            <i class="far fa-heart"></i>

        </a>

    <?php } ?>

    <a href="service_details.php?id=<?php echo $row["id"]; ?>" class="view-btn">

        <i class="fas fa-eye"></i>

        View Details

    </a>

</div>

    </div>

</div>

<?php

}

}else{

?>

<div class="no-service">

    <i class="fas fa-box-open"></i>

    <h2>No Services Available</h2>

</div>

<?php

}

?>

        </div>

    </div>

</div>

<script>

window.onload = function(){

    if(localStorage.getItem("scrollpos")){

        window.scrollTo(0, localStorage.getItem("scrollpos"));

        localStorage.removeItem("scrollpos");

    }

}

document.querySelectorAll(".wish-btn").forEach(function(btn){

    btn.addEventListener("click",function(){

        localStorage.setItem("scrollpos",window.scrollY);

    });

});

</script>

<?php include("../includes/customer_footer.php"); ?>