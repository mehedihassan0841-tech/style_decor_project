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

require_once("../config/database.php");

include("../includes/decorator_header.php");
include("../includes/decorator_sidebar.php");

$user_id=$_SESSION["user_id"];

if(isset($_POST["submit"])){

    $current_password=trim($_POST["current_password"]);

    $new_password=trim($_POST["new_password"]);

    $confirm_password=trim($_POST["confirm_password"]);

    if($new_password!=$confirm_password){

        $_SESSION["error"]="New password and Confirm password do not match.";

        header("Location: change_password.php");

        exit();

    }

    $stmt=$conn->prepare("SELECT password FROM users WHERE id=?");

    $stmt->bind_param("i",$user_id);

    $stmt->execute();

    $result=$stmt->get_result();

    $user=$result->fetch_assoc();

    if(!password_verify($current_password,$user["password"])){

        $_SESSION["error"]="Current password is incorrect.";

        header("Location: change_password.php");

        exit();

    }

    $hashed_password=password_hash($new_password,PASSWORD_DEFAULT);

    $update=$conn->prepare("UPDATE users SET password=? WHERE id=?");

    $update->bind_param("si",$hashed_password,$user_id);

    if($update->execute()){

        $_SESSION["success"]="Password changed successfully.";

    }else{

        $_SESSION["error"]="Failed to change password.";

    }

    header("Location: change_password.php");

    exit();

}

?>
<div class="dashboard-content">

<div class="dashboard-main">

<div class="page-header">

<h1>

<i class="fas fa-lock"></i>

Change Password

</h1>

<p>

Keep your account secure by changing your password.

</p>

</div>

<?php if(isset($_SESSION["success"])){ ?>

<div class="success-message">

<?php

echo $_SESSION["success"];
unset($_SESSION["success"]);

?>

</div>

<?php } ?>

<?php if(isset($_SESSION["error"])){ ?>

<div class="error-message">

<?php

echo $_SESSION["error"];
unset($_SESSION["error"]);

?>

</div>

<?php } ?>

<div class="photo-card">

<form method="POST">
<div class="form-group">

<label>Current Password</label>

<input type="password" id="current_password" name="current_password" required>
<div class="show-password">

<label>

<input type="checkbox" onclick="togglePassword()">

Show Password

</label>

</div>

</div>

<div class="form-group">

<label>New Password</label>

<input type="password" id="new_password" name="new_password" required>
<div class="show-password">

<label>

<input type="checkbox" onclick="togglePassword()">

Show Password

</label>

</div>
</div>

<div class="form-group">

<label>Confirm Password</label>

<input type="password" id="confirm_password" name="confirm_password" required>
<div class="show-password">

<label>

<input type="checkbox" onclick="togglePassword()">

Show Password

</label>

</div>

</div>

<button type="submit"
name="submit"
class="save-btn">

<i class="fas fa-key"></i>

Change Password

</button>

</form>

</div>

</div>

</div>
<script>

function togglePassword(){

let current=document.getElementById("current_password");

let newpass=document.getElementById("new_password");

let confirm=document.getElementById("confirm_password");

if(current.type==="password"){

current.type="text";
newpass.type="text";
confirm.type="text";

}else{

current.type="password";
newpass.type="password";
confirm.type="password";

}

}

</script>
<?php include("../includes/decorator_footer.php"); ?>