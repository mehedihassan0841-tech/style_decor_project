<?php
require_once("config/database.php");

include("includes/header.php");
include("includes/navbar.php");

$search = trim($_GET["search"] ?? "");

$sql = "SELECT
    ds.id,
    ds.decorator_id,
    ds.service_name,
    ds.category,
    ds.price,
    ds.duration,
    ds.description,
    ds.service_image,
    ds.availability,
    ds.created_at,

    u.full_name,
    u.profile_image,

    dp.company_name,
    dp.experience,
    dp.specialization,
    dp.district

FROM decorator_services ds

INNER JOIN users u
    ON ds.decorator_id = u.id

LEFT JOIN decorator_profiles dp
    ON ds.decorator_id = dp.user_id

WHERE ds.availability = 'Available'
  AND u.role = 'decorator'
  AND u.status = 'approved'
";


/* =========================
   SEARCH
========================= */


if (!empty($search)) {

    $search_safe = $conn->real_escape_string($search);

    $sql .= " AND ds.category LIKE '%{$search_safe}%'";
}


/* =========================
   SORTING
========================= */

$sql .= " ORDER BY ds.created_at DESC";


/* =========================
   EXECUTE QUERY
========================= */

$result = mysqli_query($conn, $sql);

?>

<!-- =====================================================
     SERVICES PAGE
===================================================== -->

<section class="services-page">

    <div class="container">


        <!-- ================= PAGE HEADER ================= -->

        <div class="services-page-header">

            <span class="services-mini-title">
                OUR SERVICES
            </span>

            <h1>
                Find the Perfect Decoration Service
            </h1>

            <div class="services-search-box">

    <form method="GET" action="services.php" class="services-search-form">

        <i class="fas fa-search"></i>

        <input
            type="text"
            name="search"
            placeholder="Search wedding, birthday, interior, restaurant..."
            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
            autocomplete="off"
        >

        <button type="submit">
            Search
        </button>

    </form>

</div>

        </div>


        <!-- ================= SERVICES GRID ================= -->

        <div class="public-services-grid">

            <?php if ($result && mysqli_num_rows($result) > 0): ?>

                <?php while ($service = mysqli_fetch_assoc($result)): ?>


                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | SERVICE IMAGE
                    |--------------------------------------------------------------------------
                    */

                    $service_image = !empty($service["service_image"])
                        ? $service["service_image"]
                        : "default-service.jpg";


                    if (strpos($service_image, "uploads/") === 0) {

                        $image_path = $service_image;

                    } else {

                        $image_path = "uploads/services/" . $service_image;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DECORATOR PROFILE IMAGE
                    |--------------------------------------------------------------------------
                    */

                    $profile_image = !empty($service["profile_image"])
                        ? $service["profile_image"]
                        : "default.png";


                    if (strpos($profile_image, "uploads/") === 0) {

                        $profile_path = $profile_image;

                    } else {

                        $profile_path = "uploads/profile/" . $profile_image;

                    }

                    ?>


                    <!-- ================= SERVICE CARD ================= -->

                    <article class="public-service-card">


                        <!-- SERVICE IMAGE -->

                        <div class="public-service-image">

                            <img
                                src="<?php echo htmlspecialchars($image_path); ?>"
                                alt="<?php echo htmlspecialchars($service["service_name"]); ?>"
                                onerror="this.src='images/default-service.jpg';"
                            >


                            <!-- CATEGORY -->

                            <span class="public-service-category">

                                <?php echo htmlspecialchars($service["category"]); ?>

                            </span>


                            <!-- AVAILABILITY -->

                            <span class="public-service-availability">

                                <?php echo htmlspecialchars($service["availability"]); ?>

                            </span>

                        </div>


                        <!-- ================= CONTENT ================= -->

                        <div class="public-service-content">


                            <!-- SERVICE TITLE -->

                            <h2>

                                <?php echo htmlspecialchars($service["service_name"]); ?>

                            </h2>


                            <!-- DESCRIPTION -->

                            <?php if (!empty($service["description"])): ?>

                                <p class="public-service-description">

                                    <?php

                                    $description = $service["description"];

                                    if (strlen($description) > 120) {

                                        echo htmlspecialchars(
                                            substr($description, 0, 120)
                                        ) . "...";

                                    } else {

                                        echo htmlspecialchars($description);

                                    }

                                    ?>

                                </p>

                            <?php endif; ?>


                            <!-- ================= DECORATOR ================= -->

                            <div class="public-service-decorator">


                                <img
                                    src="<?php echo htmlspecialchars($profile_path); ?>"
                                    alt="<?php echo htmlspecialchars($service["full_name"]); ?>"
                                    onerror="this.src='uploads/default.png';"
                                >


                                <div>

                                    <strong>

                                        <?php echo htmlspecialchars($service["full_name"]); ?>

                                    </strong>


                                    <?php if (!empty($service["company_name"])): ?>

                                        <span>

                                            <?php echo htmlspecialchars($service["company_name"]); ?>

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <!-- ================= SERVICE INFO ================= -->

                            <div class="public-service-meta">


                                <!-- PRICE -->

                                <span>

                                    <i class="fas fa-tag"></i>

                                    ৳<?php echo number_format($service["price"]); ?>

                                </span>


                                <!-- DURATION -->

                                <span>

                                    <i class="fas fa-clock"></i>

                                    <?php echo htmlspecialchars($service["duration"]); ?>

                                </span>


                                <!-- LOCATION -->

                                <?php if (!empty($service["district"])): ?>

                                    <span>

                                        <i class="fas fa-location-dot"></i>

                                        <?php echo htmlspecialchars($service["district"]); ?>

                                    </span>

                                <?php endif; ?>


                            </div>


                            <!-- ================= BOTTOM ================= -->

                            <div class="public-service-bottom">


                                <!-- PRICE -->

                                <div class="public-service-price">

                                    <small>
                                        Service Price
                                    </small>

                                    <strong>

                                        ৳<?php echo number_format($service["price"]); ?>

                                    </strong>

                                </div>


                                <!-- DETAILS -->

                                <a
                                    href="customer/service_details.php?id=<?php echo (int)$service["id"]; ?>"
                                    class="public-service-btn"
                                >

                                    View Details

                                    <i class="fas fa-arrow-right"></i>

                                </a>

                            </div>


                        </div>

                    </article>


                <?php endwhile; ?>


            <?php else: ?>


                <!-- ================= EMPTY STATE ================= -->

                <div class="public-no-services">

                    <div class="public-no-services-icon">

                        <i class="fas fa-box-open"></i>

                    </div>


                    <h2>
                        No Services Available
                    </h2>


                    <p>
                        There are currently no available decoration
                        services. Please check again later.
                    </p>

                </div>


            <?php endif; ?>


        </div>

    </div>

</section>


<?php include("includes/footer.php"); ?>

