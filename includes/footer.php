<!-- ================= FOOTER ================= -->

<?php

require_once(__DIR__ . "/../config/database.php");

$footer_query = mysqli_query(
    $conn,
    "SELECT
        footer_description,
        footer_address,
        footer_phone,
        footer_email,
        copyright_text,
        facebook_url,
        instagram_url,
        linkedin_url,
        youtube_url
     FROM settings
     ORDER BY id ASC
     LIMIT 1"
);

$footer = mysqli_fetch_assoc($footer_query);

?>


<footer class="footer" id="contact">

    <div class="container">

        <div class="footer-grid">

            <!-- About -->

            <div class="footer-box">

                <h2>
                    <span class="logo-style">Style</span><span class="logo-decor">Decor</span>
                </h2>

               
<p>
    <?php
    echo htmlspecialchars(
        $footer["footer_description"]
        ?? "StyleDecor is a trusted platform where clients can easily find professional decorators."
    );
    ?>
</p>



            </div>

            <!-- Quick Links -->

            <div class="footer-box">

                <h3>Quick Links</h3>

                <ul>

                    <li><a href="index.php">Home</a></li>

                    <li><a href="services.php">Services</a></li>

                    <li><a href="about.php">About</a></li>

                    <li><a href="contact.php">Contact</a></li>

                </ul>

            </div>

            <!-- Categories -->

            <div class="footer-box">

                <h3>Categories</h3>

                <ul>

                    <li>Wedding</li>

                    <li>Birthday</li>

                    <li>Interior Design</li>

                    <li>Home Decoration</li>

                    <li>Restaurant Decoration</li>

                </ul>

            </div>

            <!-- Contact -->

            <div class="footer-box">

                <h3>Contact</h3>

                <p>
    <i class="fas fa-map-marker-alt"></i>
    <?php echo htmlspecialchars($footer["footer_address"] ?? "Dhaka, Bangladesh"); ?>
</p>

                <p>
    <i class="fas fa-phone"></i>
    <?php echo htmlspecialchars($footer["footer_phone"] ?? "+880 1700-000000"); ?>
</p>

                <p>
    <i class="fas fa-envelope"></i>
    <?php echo htmlspecialchars($footer["footer_email"] ?? "info@styledecor.com"); ?>
</p>

                <div class="social-icons">

                    <a href="<?php echo htmlspecialchars($footer["facebook_url"] ?? "#"); ?>">
    <i class="fab fa-facebook-f"></i>
</a>

                    <a href="<?php echo htmlspecialchars($footer["instagram_url"] ?? "#"); ?>">
    <i class="fab fa-instagram"></i>
</a>

                    <a href="<?php echo htmlspecialchars($footer["linkedin_url"] ?? "#"); ?>">
    <i class="fab fa-linkedin-in"></i>
</a>

                    <a href="<?php echo htmlspecialchars($footer["youtube_url"] ?? "#"); ?>">
    <i class="fab fa-youtube"></i>
</a>

                </div>

            </div>

        </div>

        <div class="footer-bottom">
<p>
    <?php
    echo htmlspecialchars(
        $footer["copyright_text"]
        ?? "© 2026 StyleDecor. All Rights Reserved."
    );
    ?>
</p>

        </div>

    </div>

</footer>

</body>
</html>