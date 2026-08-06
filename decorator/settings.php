<?php

if(session_status()===PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["user_role"]!="decorator"){
    header("Location: ../login.php");
    exit();
}

include("../includes/decorator_header.php");
include("../includes/decorator_sidebar.php");

?>

<div class="dashboard-content">

<div class="dashboard-main">

<div class="page-header">

<h1><i class="fas fa-cog"></i> Settings</h1>

<p>Manage your account settings.</p>

</div>

<div class="settings-grid">

<a href="edit_profile.php" class="setting-card">

<i class="fas fa-user-edit"></i>

<h3>Edit Profile</h3>

<p>Update your personal information.</p>

</a>

<a href="change_photo.php" class="setting-card">

<i class="fas fa-camera"></i>

<h3>Change Photo</h3>

<p>Upload a new profile picture.</p>

</a>

<a href="change_password.php" class="setting-card">

<i class="fas fa-lock"></i>

<h3>Change Password</h3>

<p>Keep your account secure.</p>

</a>

<a href="../logout.php" class="setting-card logout-card">

    <i class="fas fa-right-from-bracket"></i>

    <h3>Logout</h3>

    <p>Sign out from your account.</p>

</a>

<a href="#" class="setting-card">

    <i class="fas fa-bell"></i>

    <h3>Notifications</h3>

    <p>Manage notification preferences.</p>

    <label class="switch">

        <input type="checkbox" checked>

        <span class="slider"></span>

    </label>

</a>

<a href="#" class="setting-card">

    <i class="fas fa-moon"></i>

    <h3>Dark Mode</h3>

    <p>Enable dark appearance.</p>

    <label class="switch">

        <input type="checkbox">

        <span class="slider"></span>

    </label>

</a>

 

</div>

</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>