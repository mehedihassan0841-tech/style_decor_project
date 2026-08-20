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


/*=========================================
  SEARCH & FILTER
=========================================*/

$search = $_GET["search"] ?? "";
$rating_filter = $_GET["rating"] ?? "";
$sort = $_GET["sort"] ?? "newest";

$where = "WHERE 1=1";


/* SEARCH */

if($search != ""){

    $search_safe = mysqli_real_escape_string($conn,$search);

    $where .= " AND (

        users.full_name LIKE '%$search_safe%'

        OR decorator.full_name LIKE '%$search_safe%'

        OR reviews.review LIKE '%$search_safe%'

        OR bookings.booking_code LIKE '%$search_safe%'

    )";
}


/* RATING FILTER */

if($rating_filter != ""){

    $rating_filter = (int)$rating_filter;

    if($rating_filter >= 1 && $rating_filter <= 5){

        $where .= " AND reviews.rating='$rating_filter'";

    }
}


/* SORT */

$order_by = "reviews.created_at DESC";

if($sort == "oldest"){

    $order_by = "reviews.created_at ASC";

}

elseif($sort == "highest"){

    $order_by = "reviews.rating DESC, reviews.created_at DESC";

}

elseif($sort == "lowest"){

    $order_by = "reviews.rating ASC, reviews.created_at DESC";

}


/*=========================================
  REVIEW LIST
=========================================*/

$review_list = mysqli_query($conn,"

    SELECT

        reviews.id,
        reviews.booking_id,
        reviews.client_id,
        reviews.decorator_id,
        reviews.rating,
        reviews.review,
        reviews.created_at,

        users.full_name AS client_name,

        decorator.full_name AS decorator_name,

        bookings.booking_code

    FROM reviews

    LEFT JOIN users
        ON reviews.client_id = users.id

    LEFT JOIN users AS decorator
        ON reviews.decorator_id = decorator.id

    LEFT JOIN bookings
        ON reviews.booking_id = bookings.id

    $where

    ORDER BY $order_by

");


/*=========================================
  REVIEW OVERVIEW
=========================================*/

$total_review = mysqli_fetch_assoc(
    mysqli_query($conn,"

        SELECT COUNT(*) AS total

        FROM reviews

    ")
);


/*=========================================
  AVERAGE RATING
=========================================*/

$average_result = mysqli_fetch_assoc(
    mysqli_query($conn,"

        SELECT AVG(rating) AS average

        FROM reviews

    ")
);

$average_rating = $average_result["average"] ?? 0;


/*=========================================
  POSITIVE FEEDBACK
  4 OR 5 STAR
=========================================*/

$positive_feedback = mysqli_fetch_assoc(
    mysqli_query($conn,"

        SELECT COUNT(*) AS total

        FROM reviews

        WHERE rating >= 4

    ")
);


/*=========================================
  NEEDS ATTENTION
  1 OR 2 STAR
=========================================*/

$needs_attention = mysqli_fetch_assoc(
    mysqli_query($conn,"

        SELECT COUNT(*) AS total

        FROM reviews

        WHERE rating <= 2

    ")
);


/*=========================================
  RATING DISTRIBUTION
=========================================*/

$rating_counts = [];

for($i = 5; $i >= 1; $i--){

    $rating_result = mysqli_fetch_assoc(
        mysqli_query($conn,"

            SELECT COUNT(*) AS total

            FROM reviews

            WHERE rating='$i'

        ")
    );

    $rating_counts[$i] = $rating_result["total"];

}


/*=========================================
  INCLUDE ADMIN LAYOUT
=========================================*/

include("../includes/admin_header.php");

include("../includes/admin_sidebar.php");

?>

<div class="admin-content">

    <div class="review-page-wrapper">

        <!-- =========================================
             REVIEW PAGE HEADER
        ========================================== -->
        <div class="review-page-header">
            <div class="review-page-title">
                <span class="review-title-badge">
                    <i class="fa-solid fa-comments"></i>
                    Customer Feedback
                </span>
                <h1>Reviews & Ratings</h1>
                <p>
                    Monitor customer experiences and service feedback.
                </p>
            </div>
        </div>

        <!-- =========================================
             REVIEW OVERVIEW
        ========================================== -->
        <div class="review-overview">

            <!-- OVERALL RATING -->
            <div class="review-overall-card">
                <div class="review-overall-top">
                    <div>
                        <p class="review-small-title">
                            Overall Rating
                        </p>
                        <div class="review-average">
                            <strong>
                                <?php echo number_format($average_rating, 1); ?>
                            </strong>
                            <span>/ 5</span>
                        </div>
                    </div>
                    <div class="review-rating-icon">
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>

                <div class="review-stars">
                    <?php
                    $rounded_rating = round($average_rating);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rounded_rating) {
                            echo '<i class="fa-solid fa-star active"></i>';
                        } else {
                            echo '<i class="fa-regular fa-star"></i>';
                        }
                    }
                    ?>
                </div>

                <p class="review-total-text">
                    <?php echo $total_review["total"]; ?> customer reviews
                </p>
            </div>

            <!-- RATING BREAKDOWN -->
            <div class="review-breakdown-card">
                <div class="review-card-heading">
                    <h3>Rating Breakdown</h3>
                    <i class="fa-solid fa-chart-simple"></i>
                </div>

                <?php
                for ($i = 5; $i >= 1; $i--) {
                    $count = $rating_counts[$i];
                    if ($total_review["total"] > 0) {
                        $percentage = ($count / $total_review["total"]) * 100;
                    } else {
                        $percentage = 0;
                    }
                ?>
                    <div class="rating-row">
                        <span class="rating-number">
                            <?php echo $i; ?>
                            <i class="fa-solid fa-star"></i>
                        </span>

                        <div class="rating-progress">
                            <div class="rating-progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                        </div>

                        <span class="rating-count">
                            <?php echo $count; ?>
                        </span>
                    </div>
                <?php } ?>
            </div>

            <!-- FEEDBACK SUMMARY -->
            <div class="review-feedback-card">
                <div class="review-card-heading">
                    <h3>Feedback Health</h3>
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>

                <div class="feedback-stat positive">
                    <div class="feedback-icon">
                        <i class="fa-solid fa-face-smile"></i>
                    </div>
                    <div>
                        <strong>
                            <?php echo $positive_feedback["total"]; ?>
                        </strong>
                        <span>Positive Feedback</span>
                    </div>
                </div>

                <div class="feedback-stat attention">
                    <div class="feedback-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <strong>
                            <?php echo $needs_attention["total"]; ?>
                        </strong>
                        <span>Needs Attention</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- =========================================
             REVIEW FEED HEADER
        ========================================== -->
        <div class="review-feed-header">
            <div>
                <h2>Customer Feedback</h2>
                <p>Recent reviews from your customers</p>
            </div>

            <div class="review-result-count">
                <?php echo $total_review["total"]; ?> Reviews
            </div>
        </div>

        <!-- =========================================
             SEARCH & FILTER
        ========================================== -->
        <div class="review-filter-bar">
            <form method="GET" class="review-filter-form">

                <div class="review-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Search customer, decorator or review..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <select name="rating" class="review-filter-select">
                    <option value="">All Ratings</option>
                    <option value="5" <?php if ($rating_filter == "5") echo "selected"; ?>>★★★★★ 5 Stars</option>
                    <option value="4" <?php if ($rating_filter == "4") echo "selected"; ?>>★★★★☆ 4 Stars</option>
                    <option value="3" <?php if ($rating_filter == "3") echo "selected"; ?>>★★★☆☆ 3 Stars</option>
                    <option value="2" <?php if ($rating_filter == "2") echo "selected"; ?>>★★☆☆☆ 2 Stars</option>
                    <option value="1" <?php if ($rating_filter == "1") echo "selected"; ?>>★☆☆☆☆ 1 Star</option>
                </select>

                <select name="sort" class="review-filter-select">
                    <option value="newest" <?php if ($sort == "newest") echo "selected"; ?>>Newest First</option>
                    <option value="oldest" <?php if ($sort == "oldest") echo "selected"; ?>>Oldest First</option>
                    <option value="highest" <?php if ($sort == "highest") echo "selected"; ?>>Highest Rating</option>
                    <option value="lowest" <?php if ($sort == "lowest") echo "selected"; ?>>Lowest Rating</option>
                </select>

                <button type="submit" class="review-filter-btn">
                    <i class="fa-solid fa-sliders"></i> Filter
                </button>

            </form>
        </div>

        <!-- =========================================
             REVIEW FEED
        ========================================== -->
        <div class="review-feed">

            <?php if (mysqli_num_rows($review_list) > 0) { ?>

                <?php while ($review = mysqli_fetch_assoc($review_list)) { ?>

                    <div class="review-item-card">

                        <!-- REVIEW TOP -->
                        <div class="review-item-top">
                            <div class="review-user">
                                <div class="review-avatar">
                                    <?php
                                    $name = $review["client_name"] ?? "Customer";
                                    echo strtoupper(substr($name, 0, 1));
                                    ?>
                                </div>

                                <div class="review-user-info">
                                    <h3>
                                        <?php echo htmlspecialchars($review["client_name"] ?? "Unknown Customer"); ?>
                                    </h3>
                                    <span>
                                        Booking: <?php echo htmlspecialchars($review["booking_code"] ?? "N/A"); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- RATING -->
                            <div class="review-item-rating">
                                <div class="review-star-display">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review["rating"]) {
                                            echo '<i class="fa-solid fa-star active"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <strong>
                                    <?php echo $review["rating"]; ?>.0
                                </strong>
                            </div>
                        </div>

                        <!-- REVIEW TEXT -->
                        <div class="review-message">
                            <i class="fa-solid fa-quote-left"></i>
                            <p>
                                <?php echo nl2br(htmlspecialchars($review["review"])); ?>
                            </p>
                        </div>

                        <!-- REVIEW META -->
                        <div class="review-item-bottom">
                            <div class="review-meta">
                                <span>
                                    <i class="fa-solid fa-user-tie"></i>
                                    <?php echo htmlspecialchars($review["decorator_name"] ?? "Unknown Decorator"); ?>
                                </span>

                                <span>
                                    <i class="fa-regular fa-calendar"></i>
                                    <?php echo date("d M Y", strtotime($review["created_at"])); ?>
                                </span>
                            </div>

                            <!-- ACTIONS -->
                            <div class="review-action-group">
                                <a href="review_details.php?id=<?php echo $review["id"]; ?>" class="review-view-btn" title="View Review">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <button type="button" class="review-delete-btn review-delete" data-id="<?php echo $review["id"]; ?>" title="Delete Review">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <!-- EMPTY STATE -->
                <div class="review-empty-state">
                    <div class="review-empty-icon">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3>No Reviews Found</h3>
                    <p>There are no customer reviews matching your search.</p>
                </div>

            <?php } ?>

        </div>

    </div>

</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Success Message Alert (PHP to JS)
    <?php if(isset($_GET["deleted"]) && $_GET["deleted"] == 1): ?>
        Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Review has been deleted successfully.",
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            // URL থেকে ?deleted=1 সরিয়ে পরিচ্ছন্ন URL রাখবে
            if (window.history.replaceState) {
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path:newUrl}, '', newUrl);
            }
        });
    <?php endif; ?>

    // 2. Delete Confirmation SweetAlert Action
    const deleteButtons = document.querySelectorAll(".review-delete");
    deleteButtons.forEach(function(button){
        button.addEventListener("click", function(){
            const id = this.getAttribute("data-id");

            Swal.fire({
                title: "Are you sure?",
                text: "This review will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = "delete_review.php?id=" + id;
                }
            });
        });
    });

});
</script>

<?php include("../includes/admin_footer.php"); ?>