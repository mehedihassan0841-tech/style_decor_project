<?php

require_once("config/database.php");
include("includes/header.php");
include("includes/navbar.php");

/* =========================================================
   SEARCH / FILTER VALUES
========================================================= */

$location = trim($_GET["location"] ?? "");
$budget   = trim($_GET["budget"] ?? "");


/* =========================================================
   GET APPROVED DECORATORS
========================================================= */

$sql = "
    SELECT
        u.id,
        u.full_name,
        u.profile_image,
        u.status,

        dp.company_name,
        dp.experience,
        dp.bio,
        dp.specialization,
        dp.district,
        dp.address,
        dp.contact_number,
        dp.verification_status,

        (
            SELECT MIN(ds.price)
            FROM decorator_services ds
            WHERE ds.decorator_id = u.id
              AND ds.availability = 'Available'
        ) AS starting_price

    FROM users u

    LEFT JOIN decorator_profiles dp
        ON u.id = dp.user_id

    WHERE u.role = 'decorator'
      AND u.status = 'approved'
";


/* =========================================================
   LOCATION FILTER
========================================================= */

if (!empty($location)) {

    $location_safe = mysqli_real_escape_string($conn, $location);

    $sql .= "
        AND (
            dp.district LIKE '%{$location_safe}%'
            OR dp.address LIKE '%{$location_safe}%'
        )
    ";
}


/* =========================================================
   BUDGET FILTER
========================================================= */

if (!empty($budget)) {

    $budget_value = (float)$budget;

    $sql .= "
        AND EXISTS (
            SELECT 1
            FROM decorator_services ds2
            WHERE ds2.decorator_id = u.id
              AND ds2.availability = 'Available'
              AND ds2.price <= {$budget_value}
        )
    ";
}


/* =========================================================
   ORDERING
========================================================= */

$sql .= "
    ORDER BY u.created_at DESC
";


/* =========================================================
   EXECUTE QUERY
========================================================= */

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

?>
<!-- =========================================================
     DECORATORS PAGE
========================================================= -->

<section class="decorators-page">

    <div class="decorators-container">

        <!-- ================= PAGE HEADER ================= -->

        <div class="decorators-page-header">
            <span class="decorators-mini-title">
                OUR DECORATORS
            </span>

            <h1>
                Meet Our Professional Decorators
            </h1>

            <p>
                Discover trusted decoration professionals and find
                the right decorator for your special event.
            </p>
        </div>


        <!-- ================= SEARCH / FILTER ================= -->

        <div class="decorator-filter-box">

            <form method="GET" class="decorator-filter-form">

                <!-- LOCATION -->
                <div class="decorator-filter-field">
                    <label>
                        <i class="fas fa-location-dot"></i>
                        Location
                    </label>

                    <div class="decorator-input-wrapper">
                        <i class="fas fa-map-marker-alt"></i>
                        <input
                            type="text"
                            name="location"
                            placeholder="Search by location..."
                            value="<?php echo htmlspecialchars($location); ?>"
                        >
                    </div>
                </div>

                <!-- BUDGET -->
                <div class="decorator-filter-field">
                    <label>
                        <i class="fas fa-wallet"></i>
                        Maximum Budget
                    </label>

                    <div class="decorator-input-wrapper">
                        <span class="taka-symbol">৳</span>
                        <input
                            type="number"
                            name="budget"
                            placeholder="Enter maximum budget"
                            min="0"
                            value="<?php echo htmlspecialchars($budget); ?>"
                        >
                    </div>
                </div>

                <!-- SEARCH BUTTON -->
                <button
                    type="submit"
                    class="decorator-search-btn"
                >
                    <i class="fas fa-search"></i>
                    Find Decorators
                </button>

                <!-- CLEAR -->
                <?php if (!empty($location) || !empty($budget)): ?>
                    <a
                        href="decorators.php"
                        class="decorator-clear-btn"
                    >
                        <i class="fas fa-rotate-left"></i>
                        Clear
                    </a>
                <?php endif; ?>

            </form>

        </div>


        <!-- ================= RESULT COUNT ================= -->

        <?php if ($result): ?>

            <div class="decorator-result-info">

                <span>
                    <?php echo mysqli_num_rows($result); ?>
                    decorator<?php echo mysqli_num_rows($result) != 1 ? "s" : ""; ?>
                    found
                </span>

                <?php if (!empty($location)): ?>
                    <span class="result-filter">
                        <i class="fas fa-location-dot"></i>
                        <?php echo htmlspecialchars($location); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($budget)): ?>
                    <span class="result-filter">
                        <i class="fas fa-wallet"></i>
                        Up to ৳<?php echo number_format((float)$budget); ?>
                    </span>
                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- ================= DECORATOR GRID ================= -->

        <div class="decorators-grid">

            <?php if ($result && mysqli_num_rows($result) > 0): ?>

                <?php while ($decorator = mysqli_fetch_assoc($result)): ?>

                    <?php
                    /* -----------------------------------------
                       PROFILE IMAGE
                    ----------------------------------------- */
                    $profile_image = !empty($decorator["profile_image"])
                        ? $decorator["profile_image"]
                        : "default.png";

                    if (strpos($profile_image, "uploads/") === 0) {
                        $image_path = $profile_image;
                    } else {
                        $image_path = "uploads/profile/" . $profile_image;
                    }
                    ?>

                    <!-- ================= DECORATOR CARD ================= -->

                    <div class="public-decorator-card">

                        <!-- IMAGE -->
                        <div class="public-decorator-image">
                            <img
                                src="<?php echo htmlspecialchars($image_path); ?>"
                                alt="<?php echo htmlspecialchars($decorator["full_name"] ?? "Decorator"); ?>"
                                onerror="this.src='uploads/profile/default.png';"
                            >

                            <span class="verified-badge">
                                <i class="fas fa-circle-check"></i>
                                Verified
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="public-decorator-content">

                            <div class="decorator-name-row">
                                <h2>
                                    <?php echo htmlspecialchars($decorator["full_name"] ?? "Professional Decorator"); ?>
                                </h2>
                            </div>

                            <!-- COMPANY -->
                            <?php if (!empty($decorator["company_name"])): ?>
                                <p class="decorator-company-name">
                                    <i class="fas fa-building"></i>
                                    <?php echo htmlspecialchars($decorator["company_name"]); ?>
                                </p>
                            <?php endif; ?>

                            <!-- SPECIALIZATION -->
                            <?php if (!empty($decorator["specialization"])): ?>
                                <div class="decorator-specialization">
                                    <span>
                                        <?php echo htmlspecialchars($decorator["specialization"]); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- INFO -->
                            <div class="decorator-info-list">

                                <?php if (!empty($decorator["experience"])): ?>
                                    <div class="decorator-info-item">
                                        <i class="fas fa-award"></i>
                                        <div>
                                            <small>Experience</small>
                                            <strong>
                                                <?php echo htmlspecialchars($decorator["experience"]); ?> Years
                                            </strong>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($decorator["district"])): ?>
                                    <div class="decorator-info-item">
                                        <i class="fas fa-location-dot"></i>
                                        <div>
                                            <small>Location</small>
                                            <strong>
                                                <?php echo htmlspecialchars($decorator["district"]); ?>
                                            </strong>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <!-- BIO -->
                            <?php if (!empty($decorator["bio"])): ?>
                                <p class="decorator-bio">
                                    <?php
                                    $bio = htmlspecialchars($decorator["bio"]);
                                    echo strlen($bio) > 110
                                        ? substr($bio, 0, 110) . "..."
                                        : $bio;
                                    ?>
                                </p>
                            <?php endif; ?>

                            <!-- PRICE -->
                            <div class="decorator-card-bottom">

                                <?php if (!empty($decorator["starting_price"])): ?>
                                    <span class="starting-price">
                                        From ৳<?php echo number_format($decorator["starting_price"]); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="starting-price">
                                        Contact Decorator
                                    </span>
                                <?php endif; ?>

                                <!-- PROFILE BUTTON -->
                                <a
                                    href="decorator_profile.php?id=<?php echo (int)$decorator["id"]; ?>"
                                    class="decorator-profile-btn"
                                >
                                    View Profile
                                    <i class="fas fa-arrow-right"></i>
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <!-- ================= NO RESULT ================= -->

                <div class="no-decorator-result">
                    <div class="no-decorator-icon">
                        <i class="fas fa-user-slash"></i>
                    </div>

                    <h2>No Decorators Found</h2>

                    <p>
                        We couldn't find any approved decorators
                        matching your search criteria.
                    </p>

                    <a
                        href="decorators.php"
                        class="browse-all-btn"
                    >
                        <i class="fas fa-users"></i>
                        View All Decorators
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php include("includes/footer.php"); ?>