```php
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
   GET REVIEW ID
========================= */

if(!isset($_GET["id"])){
    header("Location: reviews.php");
    exit();
}

$id = (int)$_GET["id"];


/* =========================
   GET REVIEW DETAILS
========================= */

$query = mysqli_query($conn, "

    SELECT

        reviews.id,
        reviews.booking_id,
        reviews.rating,
        reviews.review,
        reviews.created_at,

        client.full_name AS client_name,
        client.email AS client_email,
        client.phone AS client_phone,

        decorator.full_name AS decorator_name,

        bookings.booking_code,
        bookings.event_date,
        bookings.event_location,
        bookings.total_amount,
        bookings.booking_status,

        decorator_services.service_name,
        decorator_services.category

    FROM reviews

    LEFT JOIN users AS client
        ON reviews.client_id = client.id

    LEFT JOIN users AS decorator
        ON reviews.decorator_id = decorator.id

    LEFT JOIN bookings
        ON reviews.booking_id = bookings.id

    LEFT JOIN decorator_services
        ON reviews.booking_id IS NOT NULL
        AND bookings.service_id = decorator_services.id

    WHERE reviews.id = '$id'

    LIMIT 1

");


/* =========================
   REVIEW NOT FOUND
========================= */

if(mysqli_num_rows($query) == 0){

    header("Location: reviews.php");
    exit();

}

$review = mysqli_fetch_assoc($query);


include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");

?>


<div class="admin-content">

    <div class="review-details-page">

        <div class="review-details-card">


            <!-- PAGE HEADER -->

            <div class="review-details-header">

                <div>

                    <span class="review-details-label">

                        <i class="fa-solid fa-comment-dots"></i>

                        Customer Review

                    </span>

                    <h1>Review Details</h1>

                    <p>
                        Complete information about this customer feedback.
                    </p>

                </div>


                <a
                    href="reviews.php"
                    class="review-details-back-btn"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Reviews

                </a>

            </div>


            <!-- REVIEW MAIN -->

            <div class="review-details-main">


                <!-- CUSTOMER -->

                <div class="review-details-section">

                    <div class="review-details-section-title">

                        <i class="fa-solid fa-user"></i>

                        Customer Information

                    </div>


                    <div class="review-customer-box">

                        <div class="review-details-avatar">

                            <?php

                            echo strtoupper(
                                substr(
                                    $review["client_name"] ?? "C",
                                    0,
                                    1
                                )
                            );

                            ?>

                        </div>


                        <div>

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $review["client_name"] ?? "Unknown"
                                );
                                ?>
                            </h3>

                            <p>
                                <?php
                                echo htmlspecialchars(
                                    $review["client_email"] ?? "N/A"
                                );
                                ?>
                            </p>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $review["client_phone"] ?? "N/A"
                                );
                                ?>
                            </span>

                        </div>

                    </div>

                </div>


                <!-- RATING -->

                <div class="review-rating-highlight">

                    <div>

                        <span>Customer Rating</span>

                        <div class="review-details-stars">

                            <?php

                            for($i = 1; $i <= 5; $i++){

                                if($i <= $review["rating"]){

                                    echo '<i class="fa-solid fa-star active"></i>';

                                }else{

                                    echo '<i class="fa-regular fa-star"></i>';

                                }

                            }

                            ?>

                        </div>

                    </div>


                    <strong>

                        <?php echo $review["rating"]; ?>/5

                    </strong>

                </div>


                <!-- REVIEW MESSAGE -->

                <div class="review-details-message">

                    <div class="review-details-section-title">

                        <i class="fa-solid fa-quote-left"></i>

                        Customer Feedback

                    </div>


                    <div class="review-quote-box">

                        <i class="fa-solid fa-quote-left"></i>

                        <p>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $review["review"]
                                )
                            );

                            ?>

                        </p>

                    </div>

                </div>


                <!-- SERVICE INFORMATION -->

                <div class="review-details-grid">


                    <div class="review-info-box">

                        <span>Booking Code</span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $review["booking_code"] ?? "N/A"
                            );
                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Decorator</span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $review["decorator_name"] ?? "N/A"
                            );
                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Service</span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $review["service_name"] ?? "N/A"
                            );
                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Category</span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $review["category"] ?? "N/A"
                            );
                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Event Date</span>

                        <strong>

                            <?php

                            if(!empty($review["event_date"])){

                                echo date(
                                    "d M Y",
                                    strtotime($review["event_date"])
                                );

                            }else{

                                echo "N/A";

                            }

                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Event Location</span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $review["event_location"] ?? "N/A"
                            );
                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Total Amount</span>

                        <strong>

                            ৳<?php

                            echo number_format(
                                $review["total_amount"] ?? 0
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="review-info-box">

                        <span>Review Date</span>

                        <strong>

                            <?php

                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $review["created_at"]
                                )
                            );

                            ?>

                        </strong>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="review-details-footer">

                    <a
                        href="reviews.php"
                        class="review-details-back-btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to Reviews

                    </a>


                    <a
                        href="delete_review.php?id=<?php echo $review["id"]; ?>"
                        class="review-details-delete-btn"
                        onclick="return confirm('Are you sure you want to delete this review?');"
                    >

                        <i class="fa-solid fa-trash"></i>

                        Delete Review

                    </a>

                </div>


            </div>

        </div>

    </div>

</div>


<?php include("../includes/admin_footer.php"); ?>

