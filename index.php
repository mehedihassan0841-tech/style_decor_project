<?php 
require_once("config/database.php");
include("includes/header.php"); 
include("includes/navbar.php");

// Fetch Latest Customer Reviews
$review_query = mysqli_query(
    $conn,
    "SELECT
        r.id,
        r.rating,
        r.review,
        r.created_at,
        u.full_name,
        u.profile_image
     FROM reviews r
     INNER JOIN users u
        ON r.client_id = u.id
     WHERE u.role = 'customer'
     ORDER BY r.created_at DESC
     LIMIT 10"
);

// Fetch Featured Decorators
$decorator_query = mysqli_query(
    $conn,
    "SELECT
        u.id,
        u.full_name,
        u.profile_image,
        dp.company_name,
        dp.experience,
        dp.specialization,
        dp.district
     FROM users u
     INNER JOIN decorator_profiles dp
        ON u.id = dp.user_id
     WHERE u.role = 'decorator'
       AND u.status = 'approved'
     ORDER BY dp.created_at DESC"
);

// Fetch Hero Banner Settings
$banner_query = mysqli_query(
    $conn,
    "SELECT
        banner_title,
        banner_subtitle,
        banner_button_text,
        banner_button_link,
        banner_image
     FROM settings
     ORDER BY id ASC
     LIMIT 1"
);

$banner_settings = mysqli_fetch_assoc($banner_query) ?? [];
?>

<!-- ================= HERO SECTION ================= -->
<section
    class="hero"
    style="background-image: url('<?php echo htmlspecialchars($banner_settings["banner_image"] ?? "images/hero-banner.jpg"); ?>');"
>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <h1>
                <?php echo htmlspecialchars($banner_settings["banner_title"] ?? "Design Your Dream Event"); ?>
            </h1>
            <p>
                <?php echo htmlspecialchars($banner_settings["banner_subtitle"] ?? "Discover the best decorators for weddings, birthdays, home decoration, interior design, restaurants and every special occasion."); ?>
            </p>
            <div class="hero-buttons">
                <a href="<?php echo htmlspecialchars($banner_settings["banner_button_link"] ?? "services.php"); ?>" class="btn-primary">
                    <?php echo htmlspecialchars($banner_settings["banner_button_text"] ?? "Explore Services"); ?>
                </a>
                <a href="register.php" class="btn-secondary">
                    Become a Decorator
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================= CATEGORIES ================= -->
<section class="categories">
    <div class="container">
        <div class="section-title">
            <h2>Our Categories</h2>
            <p>Explore our professional decoration services for every occasion.</p>
        </div>

        <div class="category-grid">
            <div class="category-card">
                <div class="category-icon">💍</div>
                <h3>Wedding Decoration</h3>
                <p>Elegant wedding decorations designed to make your special day unforgettable.</p>
            </div>

            <div class="category-card">
                <div class="category-icon">🎂</div>
                <h3>Birthday Decoration</h3>
                <p>Creative birthday themes for kids and adults with modern decoration ideas.</p>
            </div>

            <div class="category-card">
                <div class="category-icon">🏠</div>
                <h3>Home Decoration</h3>
                <p>Transform your home into a stylish and beautiful living space.</p>
            </div>

            <div class="category-card">
                <div class="category-icon">🛋️</div>
                <h3>Interior Design</h3>
                <p>Modern interior decoration solutions for homes and offices.</p>
            </div>

            <div class="category-card">
                <div class="category-icon">🍽️</div>
                <h3>Restaurant Decoration</h3>
                <p>Premium decoration services for restaurants and cafés.</p>
            </div>

            <div class="category-card">
                <div class="category-icon">🎉</div>
                <h3>Custom Events</h3>
                <p>Personalized decoration services for every special event.</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= FEATURED DECORATORS ================= -->

<!-- ================= FEATURED DECORATORS ================= -->
<section class="featured-decorators">
    <div class="container">
        <div class="section-title">
            <h2>Featured Decorators</h2>
            <p>Meet our professional decorators for your dream event.</p>
        </div>

        <div class="decorator-slider-wrapper">
            <!-- Previous Button -->
            <button
                type="button"
                class="decorator-nav decorator-prev"
                aria-label="Previous decorators"
            >
                ‹
            </button>

            <!-- Cards Slider -->
            <div class="decorator-slider">
                <div class="decorator-track" id="decoratorTrack">
                    <?php if ($decorator_query && mysqli_num_rows($decorator_query) > 0): ?>
                        <?php while ($decorator = mysqli_fetch_assoc($decorator_query)): ?>
                            <?php
                                // Image Path Handling
                                $profile_image = !empty($decorator["profile_image"]) ? $decorator["profile_image"] : "default.png";
                                
                                if (strpos($profile_image, "uploads/") === 0) {
                                    $image_path = $profile_image;
                                } else {
                                    $image_path = "uploads/profile/" . $profile_image;
                                }
                            ?>
                            <div class="decorator-card">
                                <div class="decorator-image">
                                    <img
                                        src="<?php echo htmlspecialchars($image_path); ?>"
                                        alt="<?php echo htmlspecialchars($decorator["full_name"]); ?>"
                                        class="decorator-photo"
                                        onerror="this.src='uploads/default.png';"
                                    >
                                </div>

                                <div class="decorator-content">
                                    <h3><?php echo htmlspecialchars($decorator["full_name"]); ?></h3>

                                    <?php if (!empty($decorator["company_name"])): ?>
                                        <p class="decorator-company">
                                            <?php echo htmlspecialchars($decorator["company_name"]); ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="rating">★★★★★</div>

                                    <?php if (!empty($decorator["experience"])): ?>
                                        <p>
                                            <strong>Experience:</strong> 
                                            <?php echo htmlspecialchars($decorator["experience"]); ?> Years
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($decorator["district"])): ?>
                                        <p>
                                            <strong>Location:</strong> 
                                            <?php echo htmlspecialchars($decorator["district"]); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($decorator["specialization"])): ?>
                                        <p>
                                            <strong>Speciality:</strong> 
                                            <?php echo htmlspecialchars($decorator["specialization"]); ?>
                                        </p>
                                    <?php endif; ?>

                                    <a href="decorator/profile.php?id=<?php echo (int)$decorator["id"]; ?>" class="profile-btn">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-decorators">
                            No approved decorators available yet.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Next Button -->
            <button
                type="button"
                class="decorator-nav decorator-next"
                aria-label="Next decorators"
            >
                ›
            </button>
        </div>
    </div>
</section>
<!-- ================= WHY CHOOSE US ================= -->
<section class="sd-why-section">

    <div class="container">

        <div class="section-title">

            <h2>Why Choose StyleDecor?</h2>

            <p>
                We provide professional decorators with secure booking
                and quality service.
            </p>

        </div>

        <div class="sd-why-grid">

            <div class="sd-why-card">

                <i class="fas fa-user-check"></i>

                <h3>Verified Decorators</h3>

                <p>
                    All decorators are verified to ensure trusted
                    and quality services.
                </p>

            </div>


            <div class="sd-why-card">

                <i class="fas fa-lock"></i>

                <h3>Secure Booking</h3>

                <p>
                    Your booking information and payments are
                    completely secure.
                </p>

            </div>


            <div class="sd-why-card">

                <i class="fas fa-wallet"></i>

                <h3>Affordable Pricing</h3>

                <p>
                    Compare multiple decorators and choose
                    the best package.
                </p>

            </div>


            <div class="sd-why-card">

                <i class="fas fa-headset"></i>

                <h3>24/7 Support</h3>

                <p>
                    Our support team is always ready to help
                    you anytime.
                </p>

            </div>

        </div>

    </div>

</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="how-it-works">
    <div class="container">
        <div class="section-title">
            <h2>How It Works</h2>
            <p>Book your decoration service in four simple steps.</p>
        </div>

        <div class="steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <i class="fas fa-user-plus"></i>
                <h3>Create Account</h3>
                <p>Register as a Client or Decorator.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <i class="fas fa-search"></i>
                <h3>Choose Decorator</h3>
                <p>Browse decorators and compare their services.</p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <i class="fas fa-calendar-check"></i>
                <h3>Book Service</h3>
                <p>Select your event date and confirm your booking.</p>
            </div>

            <div class="step-card">
                <div class="step-number">4</div>
                <i class="fas fa-star"></i>
                <h3>Enjoy Your Event</h3>
                <p>Celebrate your special day with beautiful decoration.</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= CUSTOMER REVIEWS ================= -->
<section class="reviews">
    <div class="container">
        <div class="section-title">
            <h2>What Our Clients Say</h2>
            <p>Real feedback from our happy customers.</p>
        </div>
<div class="review-slider-wrapper">

    <!-- Previous -->
    <button
        type="button"
        class="review-nav review-prev"
        aria-label="Previous reviews"
    >
        ‹
    </button>


    <!-- Slider -->
    <div class="review-slider">

        <div class="review-track" id="reviewTrack">

            <?php if ($review_query && mysqli_num_rows($review_query) > 0): ?>

                <?php while ($review = mysqli_fetch_assoc($review_query)): ?>

                    <?php
                        $review_image = !empty($review["profile_image"])
                            ? $review["profile_image"]
                            : "default.png";

                        $image_path = "uploads/profile/" . $review_image;

                        $rating = (int)$review["rating"];
                    ?>

                    <div class="review-card">

                        <img
                            src="<?php echo htmlspecialchars($image_path); ?>"
                            alt="<?php echo htmlspecialchars($review["full_name"]); ?>"
                            onerror="this.src='uploads/default.png';"
                        >

                        <h3>
                            <?php echo htmlspecialchars($review["full_name"]); ?>
                        </h3>

                        <div class="stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? "★" : "☆";
                            }
                            ?>
                        </div>

                        <p>
                            <?php echo htmlspecialchars($review["review"]); ?>
                        </p>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p class="no-reviews">
                    No customer reviews available yet.
                </p>

            <?php endif; ?>

        </div>

    </div>


    <!-- Next -->
    <button
        type="button"
        class="review-nav review-next"
        aria-label="Next reviews"
    >
        ›
    </button>

</div>
    </div>
</section>

<!-- ================= CALL TO ACTION ================= -->
<section class="cta">
    <div class="container">
        <h2>Ready to Design Your Dream Event?</h2>
        <p>Book trusted decorators today and make your special moments unforgettable.</p>

        <div class="cta-buttons">
            <a href="services.php" class="cta-btn">Book Now</a>
            <a href="register.php" class="cta-btn-outline">Join as Decorator</a>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
<script>


document.addEventListener("DOMContentLoaded", function () {

    const track = document.getElementById("decoratorTrack");
    const prevBtn = document.querySelector(".decorator-prev");
    const nextBtn = document.querySelector(".decorator-next");

    if (!track || !prevBtn || !nextBtn) {
        return;
    }

    const cards = track.querySelectorAll(".decorator-card");

    if (cards.length === 0) {
        return;
    }

    let currentIndex = 0;

    const visibleCards = 3;

    function updateSlider() {

        const cardWidth = cards[0].offsetWidth;
        const gap = 30;

        const moveAmount = cardWidth + gap;

        track.style.transform =
            `translateX(-${currentIndex * moveAmount}px)`;

        prevBtn.disabled = currentIndex === 0;

        nextBtn.disabled =
            currentIndex >= cards.length - visibleCards;
    }


    nextBtn.addEventListener("click", function () {

        if (currentIndex < cards.length - visibleCards) {

            currentIndex++;

            updateSlider();

        }

    });


    prevBtn.addEventListener("click", function () {

        if (currentIndex > 0) {

            currentIndex--;

            updateSlider();

        }

    });


    window.addEventListener("resize", updateSlider);

    updateSlider();

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const track = document.getElementById("reviewTrack");
    const prevBtn = document.querySelector(".review-prev");
    const nextBtn = document.querySelector(".review-next");

    if (!track || !prevBtn || !nextBtn) {
        return;
    }

    const cards = track.querySelectorAll(".review-card");

    if (cards.length === 0) {
        return;
    }

    let currentIndex = 0;

    const visibleCards = 3;

    function updateReviewSlider() {

        const cardWidth = cards[0].offsetWidth;
        const gap = 30;

        const moveAmount = cardWidth + gap;

        track.style.transform =
            `translateX(-${currentIndex * moveAmount}px)`;

        prevBtn.disabled = currentIndex === 0;

        nextBtn.disabled =
            currentIndex >= cards.length - visibleCards;
    }


    nextBtn.addEventListener("click", function () {

        if (currentIndex < cards.length - visibleCards) {

            currentIndex++;

            updateReviewSlider();

        }

    });


    prevBtn.addEventListener("click", function () {

        if (currentIndex > 0) {

            currentIndex--;

            updateReviewSlider();

        }

    });


    window.addEventListener("resize", updateReviewSlider);

    updateReviewSlider();

});
</script>