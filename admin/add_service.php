<?php

session_start();

if(!isset($_SESSION["user_id"])){

header("Location: ../login.php");

exit();

}

if($_SESSION["user_role"]!="admin"){

header("Location: ../login.php");

exit();

}

require_once("../config/database.php");

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");

/*=========================
GET ALL DECORATORS
==========================*/

$decorators=mysqli_query($conn,"

SELECT id,full_name

FROM users

WHERE role='decorator'

AND status='active'

ORDER BY full_name ASC

");

?>
<div class="admin-content">

<div class="add-service-page">

<div class="add-service-card">

<div class="add-service-header">

<h2>

<i class="fa-solid fa-plus"></i>

Add New Service

</h2>

<p>

Create a new decoration service.

</p>

</div>

<form
action="save_service.php"
method="POST"
enctype="multipart/form-data">

<div class="service-form-grid">
    <div class="service-input-group">

<label>

Decorator

</label>

<select
name="decorator_id"
required>

<option value="">

Choose Decorator

</option>

<?php

while($row=mysqli_fetch_assoc($decorators)){

?>

<option
value="<?php echo $row["id"]; ?>">

<?php echo $row["full_name"]; ?>

</option>

<?php

}

?>

</select>

</div>
<div class="service-input-group">

<label>

Service Name

</label>

<input

type="text"

name="service_name"

placeholder="Wedding Decoration"

required>

</div>
<div class="service-input-group">

<label>

Category

</label>

<select
name="category"
required>

<option value="">

Choose Category

</option>

<option>

Wedding

</option>

<option>

Birthday

</option>

<option>

Interior

</option>

<option>

Home Decoration

</option>

<option>

Restaurant

</option>

<option>

Others

</option>

</select>

</div>
<div class="service-input-group">

<label>

Price

</label>

<input

type="number"

name="price"

placeholder="15000"

required>

</div>
<div class="service-input-group">

<label>

Duration

</label>

<input

type="text"

name="duration"

placeholder="6 Hours"

required>

</div>
<div class="service-input-group">

<label>

Availability

</label>

<select

name="availability"

required>

<option value="available">

Available

</option>

<option value="unavailable">

Unavailable

</option>

</select>

</div>
<div class="service-input-group">

<label>

Service Image

</label>

<input

type="file"

name="service_image"

accept="image/*"

required>

</div>
<div class="service-input-group full-width">

<label>

Description

</label>

<textarea

name="description"

rows="6"

placeholder="Write service details..."

required>

</textarea>

</div>
<div class="service-form-buttons">

<button

type="submit"

class="save-service-btn">

<i class="fa-solid fa-floppy-disk"></i>

Save Service

</button>

<a

href="services.php"

class="cancel-service-btn">

<i class="fa-solid fa-arrow-left"></i>

Back

</a>

</div>
</div>

</form>

</div>

</div>

</div>

<?php include("../includes/admin_footer.php"); ?>