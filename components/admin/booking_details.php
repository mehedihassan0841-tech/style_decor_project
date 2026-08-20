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

if(!isset($_GET["id"])){

    header("Location: bookings.php");
    exit();

}

$id = (int)$_GET["id"];


/* =========================
   GET BOOKING DETAILS
========================= */

$query = mysqli_query($conn, "

    SELECT

        bookings.*,

        client.full_name AS client_name,
        client.email AS client_email,
        client.phone AS client_phone,

        decorator.full_name AS decorator_name,
        decorator.email AS decorator_email,
        decorator.phone AS decorator_phone,

        decorator_services.service_name,
        decorator_services.category,
        decorator_services.price,
        decorator_services.duration

    FROM bookings

    LEFT JOIN users AS client
        ON bookings.client_id = client.id

    LEFT JOIN decorator_services
        ON bookings.service_id = decorator_services.id

    LEFT JOIN users AS decorator
        ON decorator_services.decorator_id = decorator.id

    WHERE bookings.id = '$id'

");


if(mysqli_num_rows($query) == 0){

    header("Location: bookings.php");
    exit();

}


$booking = mysqli_fetch_assoc($query);


include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");

?>


<div class="admin-content">

    <div class="booking-details-page">

        <div class="booking-details-card">


            <!-- =========================
                 BOOKING HEADER
            ========================== -->

            <div class="booking-details-top">

                <div class="booking-details-icon">

                    <i class="fa-solid fa-calendar-check"></i>

                </div>


                <div class="booking-details-title">

                    <h2>

                        Booking Details

                    </h2>

                    <p>

                        View complete booking information.

                    </p>

                </div>

            </div>



            <!-- =========================
                 BOOKING CODE & STATUS
            ========================== -->

            <div class="booking-main-info">

                <div>

                    <label>Booking Code</label>

                    <h3>

                        <?php echo htmlspecialchars($booking["booking_code"]); ?>

                    </h3>

                </div>


                <div>

                    <label>Booking Status</label>

                    <span class="booking-status
                        <?php echo strtolower($booking["booking_status"]); ?>">

                        <?php echo ucfirst($booking["booking_status"]); ?>

                    </span>

                </div>

            </div>



            <!-- =========================
                 CUSTOMER INFORMATION
            ========================== -->

            <div class="booking-section-title">

                <i class="fa-solid fa-user"></i>

                Customer Information

            </div>


            <div class="booking-info-grid">


                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-user"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Customer Name</label>

                        <p>

                            <?php echo htmlspecialchars($booking["client_name"]); ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-envelope"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Email Address</label>

                        <p>

                            <?php echo htmlspecialchars($booking["client_email"]); ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-phone"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Phone Number</label>

                        <p>

                            <?php echo htmlspecialchars($booking["client_phone"] ?? "N/A"); ?>

                        </p>

                    </div>

                </div>


            </div>



            <!-- =========================
                 SERVICE INFORMATION
            ========================== -->

            <div class="booking-section-title">

                <i class="fa-solid fa-briefcase"></i>

                Service Information

            </div>


            <div class="booking-info-grid">


                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-briefcase"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Service Name</label>

                        <p>

                            <?php echo htmlspecialchars($booking["service_name"]); ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-user-tie"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Decorator</label>

                        <p>

                            <?php echo htmlspecialchars($booking["decorator_name"]); ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-list"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Category</label>

                        <p>

                            <?php echo htmlspecialchars($booking["category"]); ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Service Price</label>

                        <p>

                            ৳<?php echo number_format($booking["price"]); ?>

                        </p>

                    </div>

                </div>


            </div>



            <!-- =========================
                 EVENT INFORMATION
            ========================== -->

            <div class="booking-section-title">

                <i class="fa-solid fa-calendar-days"></i>

                Event Information

            </div>


            <div class="booking-info-grid">


                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-calendar-plus"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Booking Date</label>

                        <p>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime($booking["booking_date"])
                            );

                            ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-calendar-check"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Event Date</label>

                        <p>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime($booking["event_date"])
                            );

                            ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-clock"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Event Time</label>

                        <p>

                            <?php

                            echo date(
                                "h:i A",
                                strtotime($booking["event_time"])
                            );

                            ?>

                        </p>

                    </div>

                </div>



                <div class="booking-info-card">

                    <div class="booking-info-icon">

                        <i class="fa-solid fa-location-dot"></i>

                    </div>

                    <div class="booking-info-text">

                        <label>Event Location</label>

                        <p>

                            <?php echo htmlspecialchars($booking["event_location"]); ?>

                        </p>

                    </div>

                </div>


            </div>



            <!-- =========================
                 PAYMENT INFORMATION
            ========================== -->

            <div class="booking-section-title">

                <i class="fa-solid fa-money-bill-wave"></i>

                Payment Information

            </div>


            <div class="booking-payment-box">

                <div>

                    <label>Total Amount</label>

                    <h2>

                        ৳<?php echo number_format($booking["total_amount"], 2); ?>

                    </h2>

                </div>

            </div>



            <!-- =========================
                 SPECIAL INSTRUCTION
            ========================== -->

            <div class="booking-section-title">

                <i class="fa-solid fa-note-sticky"></i>

                Special Instruction

            </div>


            <div class="booking-instruction">

                <?php

                if(!empty($booking["special_instruction"])){

                    echo nl2br(
                        htmlspecialchars($booking["special_instruction"])
                    );

                }else{

                    echo "No special instruction provided.";

                }

                ?>

            </div>



            <!-- =========================
                 FOOTER
            ========================== -->

            <div class="booking-details-footer">

                <a
                    href="bookings.php"
                    class="booking-back-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Bookings

                </a>

            </div>


        </div>

    </div>

</div>


<?php include("../includes/admin_footer.php"); ?>