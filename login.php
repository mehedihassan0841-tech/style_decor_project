<?php include("includes/header.php"); ?>
<?php include("includes/navbar.php"); ?>

<section class="login-page-section">

    <div class="login-page-container">

        <!-- Left Side -->
        <div class="login-page-left">

            <h1>Welcome Back!</h1>

            <p>
                Login to continue booking the best decorators
                for your dream events.
            </p>


        </div>

        <!-- Right Side -->
        <div class="login-page-right">

            <div class="login-form-card">
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

                <h2>Login</h2>

                <p>Enter your account credentials</p>

                <form action="auth/login_process.php" method="POST">

                    <!-- Email -->

                    <div class="login-input-group">

                        <i class="fas fa-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Email Address"
                            required>

                    </div>

                    <!-- Password -->

                    <div class="login-input-group login-password-group">

                        <i class="fas fa-lock"></i>

                        <input
                            type="password"
                            name="password"
                            id="loginPassword"
                            placeholder="Password"
                            required>

                        <span class="toggle-password">

                            <i class="fas fa-eye"></i>

                        </span>

                    </div>

                    <div class="login-remember-area">

                        <label>

                            <input type="checkbox">

                            Remember Me

                        </label>

                        <a href="#">Forgot Password?</a>

                    </div>

                    <button
                        
                     type="submit"
                     class="login-submit-btn"
                     id="loginBtn">

                        login
                    </button>
                    

                </form>

                <div class="login-form-footer">

                    Don't have an account?

                    <a href="register.php">

                        Register

                    </a>

                </div>

            </div>

        </div>

    </div>

</section>
<script src="js/login.js"></script>
<?php include("includes/footer.php"); ?>