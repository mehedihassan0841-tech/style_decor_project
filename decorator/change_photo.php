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

$stmt=$conn->prepare("SELECT profile_image FROM users WHERE id=?");

$stmt->bind_param("i",$user_id);

$stmt->execute();

$result=$stmt->get_result();

$user=$result->fetch_assoc();

$current_photo=$user["profile_image"];
if(isset($_POST["submit"])){

    if(isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"]==0){

        $fileName=$_FILES["profile_image"]["name"];

        $tmpName=$_FILES["profile_image"]["tmp_name"];

        $extension=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

        $allowed=["jpg","jpeg","png","webp"];

        if(in_array($extension,$allowed)){

            $newName=time()."_".rand(1000,9999).".".$extension;

            $destination="../uploads/profile/".$newName;

            if(move_uploaded_file($tmpName,$destination)){

                $update=$conn->prepare("UPDATE users SET profile_image=? WHERE id=?");

                $update->bind_param("si",$newName,$user_id);

                if($update->execute()){

                    if($current_photo!="default.png" && file_exists("../uploads/profile/".$current_photo)){

                        unlink("../uploads/profile/".$current_photo);

                    }

                    $_SESSION["profile_image"]=$newName;

                    $_SESSION["success"]="Profile photo updated successfully.";

                    header("Location: change_photo.php");

                    exit();

                }else{

                    $_SESSION["error"]="Database update failed.";

                }

            }else{

                $_SESSION["error"]="Image upload failed.";

            }

        }else{

            $_SESSION["error"]="Only JPG, JPEG, PNG and WEBP are allowed.";

        }

    }

}

?>
<div class="dashboard-content">

<div class="dashboard-main">

<div class="page-header">

<h1>

<i class="fas fa-camera"></i>

Change Profile Photo

</h1>

<p>

Upload a new profile picture.

</p>

</div>
<div class="photo-card">

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

<div class="current-photo">

<img src="../uploads/profile/<?php echo $current_photo; ?>" alt="Profile Photo">

</div>

<form action="" method="POST" enctype="multipart/form-data">

<label>Select New Photo</label>

<input type="file"
name="profile_image"
accept="image/*"
required>

<button type="submit" name="submit" class="save-btn">

<i class="fas fa-upload"></i>

Upload Photo

</button>

</form>

</div>
</div>

</div>

<?php include("../includes/decorator_footer.php"); ?>