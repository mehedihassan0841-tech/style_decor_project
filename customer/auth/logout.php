<?php

session_start();

// সব Session Data মুছে ফেলবে
session_unset();

// Session Destroy করবে
session_destroy();

// Login Page-এ পাঠাবে
header("Location: ../login.php");
exit();


?>