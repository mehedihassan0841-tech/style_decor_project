<?php include("includes/header.php"); ?>
<?php include("includes/navbar.php"); ?>

<section class="register-section">

    <div class="register-container">

        <!-- Left Side -->

        <div class="register-left">

            <h1>Create Your Account</h1>

            <p>
                Join Bangladesh's most trusted event decoration platform
                and connect with professional decorators.
            </p>

            <div class="feature-list">

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Verified Decorators</span>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Secure Booking System</span>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Best Price Guarantee</span>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>24/7 Customer Support</span>
                </div>

            </div>

        </div>

        <!-- Right Side -->

        <div class="register-right">

            <div class="register-card">

            <?php

if(isset($_SESSION["error"])){

    echo '<div class="alert-error">'.$_SESSION["error"].'</div>';

    unset($_SESSION["error"]);

}

if(isset($_SESSION["success"])){

    echo '<div class="alert-success">'.$_SESSION["success"].'</div>';

    unset($_SESSION["success"]);

}

?>

                <h2>Create Account</h2>

                <p>Fill in your information below</p>

                <form action="auth/register_process.php" method="POST">

                    <!-- Full Name -->

                    <div class="input-group">

                        <i class="fas fa-user"></i>

                        <input
                            type="text"
                            name="full_name"
                            placeholder="Full Name"
                            required>

                    </div>

                    <!-- Email -->

                    <div class="input-group">

                        <i class="fas fa-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Email Address"
                            required>

                    </div>

                    <!-- Phone -->

                    <div class="input-group">

                        <i class="fas fa-phone"></i>

                        <input
                            type="text"
                            name="phone"
                            placeholder="Phone Number"
                            required>

                    </div>

                    <!-- Address -->

                    <div class="input-group">

                        <i class="fas fa-location-dot"></i>

                        <input
                            type="text"
                            name="address"
                            placeholder="Address"
                            required>

                    </div>

                    <!-- Role -->

                    <div class="input-group">

                        <i class="fas fa-users"></i>

                        <select name="role" required>

                            <option value="">Select Role</option>

                            <option value="customer">Customer</option>

                            <option value="decorator">Decorator</option>

                        </select>

                    </div>
                                        <!-- Password -->

                    <div class="input-group password-group">

                        <i class="fas fa-lock"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Password"
                            required>

                        <span class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </span>

                    </div>

                    <!-- Confirm Password -->

                    <div class="input-group password-group">

                        <i class="fas fa-lock"></i>

                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            placeholder="Confirm Password"
                            required>

                        <span class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </span>

                    </div>
                    <small id="password-message"></small>
                    <small id="password-strength"></small>

                    <!-- Register Button -->

                    <button type="submit" class="register-submit-btn" id="registerBtn">

                        <i class="fas fa-user-plus"></i>

                        Create Account

                    </button>

                </form>

                <div class="login-link">

                    Already have an account?

                    <a href="login.php">Login Here</a>

                </div>

            </div>

        </div>

    </div>

</section>
<script src="js/register.js"></script>

<?php include("includes/footer.php"); ?>