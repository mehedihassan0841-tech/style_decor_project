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

include("../includes/customer_header.php");
include("../includes/customer_sidebar.php");

?>

<div class="dashboard-content">

    <div class="dashboard-main">

        <div class="page-header">

            <h1>Change Password</h1>

            <p>Update your account password securely.</p>

        </div>

        <?php

        if(isset($_SESSION["success"])){
            echo '<div class="alert-success">'.$_SESSION["success"].'</div>';
            unset($_SESSION["success"]);
        }

        if(isset($_SESSION["error"])){
            echo '<div class="alert-error">'.$_SESSION["error"].'</div>';
            unset($_SESSION["error"]);
        }
        ?>
    <div class="customer-password-panel">


<form action="update_password.php" method="POST">



<div class="customer-password-field">

<label>Current Password</label>


<div class="customer-password-input-wrapper">


<i class="fas fa-lock"></i>


<input
type="password"
id="current_password"
name="current_password"
placeholder="Current Password"
required>


<span class="toggle-password">

<i class="fas fa-eye"></i>

</span>


</div>


</div>





<div class="customer-password-field">

<label>New Password</label>


<div class="customer-password-input-wrapper">


<i class="fas fa-lock"></i>


<input
type="password"
id="new_password"
name="new_password"
placeholder="New Password"
required>


<span class="toggle-password">

<i class="fas fa-eye"></i>

</span>


</div>


</div>





<div class="customer-password-field">

<label>Confirm Password</label>


<div class="customer-password-input-wrapper">


<i class="fas fa-lock"></i>


<input
type="password"
id="confirm_password"
name="confirm_password"
placeholder="Confirm Password"
required>


<span class="toggle-password">

<i class="fas fa-eye"></i>

</span>


</div>


</div>





<button
type="submit"
class="customer-password-submit">


Change Password


</button>




</form>


</div>


<script src="../js/register.js"></script>
<?php include("../includes/customer_footer.php"); ?>