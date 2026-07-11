<?php

session_start();

echo "<h1>Customer Dashboard</h1>";

echo $_SESSION["user_name"];