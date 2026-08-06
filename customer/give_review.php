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


$customer_id = $_SESSION["user_id"];


// Check booking id

if(isset($_GET["booking_id"])){

    $booking_id = $_GET["booking_id"];

}else{


    // Find latest completed booking without review

    $find_booking = $conn->prepare("

        SELECT b.id

        FROM bookings b

        LEFT JOIN reviews r

        ON b.id = r.booking_id

        WHERE b.client_id = ?

        AND b.booking_status = 'Completed'

        AND r.id IS NULL

        ORDER BY b.id DESC

        LIMIT 1

    ");


    $find_booking->bind_param("i",$customer_id);

    $find_booking->execute();


    $booking_result = $find_booking->get_result();



    if($booking_result->num_rows == 0){


     echo "

<div class='dashboard-content'>

<div class='dashboard-main'>

<div class='empty-booking'>

<i class='fa-solid fa-calendar-xmark'></i>

<h2>No Recent Service</h2>

<p>You don't have any completed service to review.</p>

<a href='services.php' class='browse-btn'>
Browse Services
</a>

</div>

</div>

</div>

";


        include("../includes/customer_footer.php");

        exit();

    }



    $booking_row = $booking_result->fetch_assoc();


    $booking_id = $booking_row["id"];

}



// Booking information fetch

$sql = "SELECT

b.id,

b.booking_code,

b.booking_status,

ds.service_name,

dp.company_name,

dp.user_id AS decorator_id


FROM bookings b


INNER JOIN decorator_services ds

ON b.service_id = ds.id


INNER JOIN decorator_profiles dp

ON ds.decorator_id = dp.user_id


WHERE b.id = ?

AND b.client_id = ?";



$stmt = $conn->prepare($sql);


$stmt->bind_param("ii",$booking_id,$customer_id);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 0){

    echo "Invalid Booking";
    exit();

}



$booking = $result->fetch_assoc();




// Submit review

if(isset($_POST["submit_review"])){


    $rating = $_POST["rating"];

    $review = $_POST["review"];



    // Check duplicate review

    $check = $conn->prepare(
        "SELECT id FROM reviews WHERE booking_id=?"
    );


    $check->bind_param("i",$booking_id);


    $check->execute();


    $check_result = $check->get_result();



    if($check_result->num_rows > 0){


        $message = "You already reviewed this booking";


    }else{


        $insert = $conn->prepare("

            INSERT INTO reviews

            (booking_id,client_id,decorator_id,rating,review)

            VALUES(?,?,?,?,?)

        ");



        $insert->bind_param(

            "iiiis",

            $booking_id,

            $customer_id,

            $booking["decorator_id"],

            $rating,

            $review

        );



        if($insert->execute()){


            header("Location: ./bookings.php");

            exit();


        }else{


            $message = "Something went wrong";


        }


    }


}


?>


<div class="dashboard-content">


<div class="dashboard-main">


<div class="page-header">

<h1>Give Review</h1>

<p>Share your experience with decorator.</p>

</div>



<div class="review-box">


<h2>
<?= htmlspecialchars($booking["company_name"]); ?>
</h2>


<div class="booking-info-card">


<div class="info-item">

<div class="info-icon">
<i class="fa-solid fa-paintbrush"></i>
</div>

<div>
<span>Service</span>

<h3>
<?= htmlspecialchars($booking["service_name"]); ?>
</h3>

</div>

</div>




<div class="info-item">

<div class="info-icon booking-icon">
<i class="fa-solid fa-ticket"></i>
</div>

<div>

<span>Booking Code</span>

<h3>
<?= htmlspecialchars($booking["booking_code"]); ?>
</h3>

</div>

</div>



</div>



<?php if(isset($message)){ ?>

<div class="error-message">

<?= $message; ?>

</div>

<?php } ?>



<form method="POST">


<label>Rating</label>


<div class="star-rating">

<input type="radio" id="star5" name="rating" value="5" required>
<label for="star5">★</label>


<input type="radio" id="star4" name="rating" value="4">
<label for="star4">★</label>


<input type="radio" id="star3" name="rating" value="3">
<label for="star3">★</label>


<input type="radio" id="star2" name="rating" value="2">
<label for="star2">★</label>


<input type="radio" id="star1" name="rating" value="1">
<label for="star1">★</label>

</div>



<label>Your Review</label>


<textarea 
name="review"
rows="5"
placeholder="Write your experience..."
required></textarea>



<button type="submit" name="submit_review">

Submit Review

</button>


</form>



</div>



</div>


</div>


<?php include("../includes/customer_footer.php"); ?>