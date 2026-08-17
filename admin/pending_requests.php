<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["user_role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");

/* =========================================
   SUCCESS / ERROR MESSAGE
========================================= */
$success_message = $_SESSION["settings_success"] ?? "";
$error_message   = $_SESSION["settings_error"] ?? "";
unset($_SESSION["settings_success"]);
unset($_SESSION["settings_error"]);

/* =========================================
   GET PENDING USERS
========================================= */
$pending_query = mysqli_query(
    $conn,
    "SELECT
        id,
        full_name,
        email,
        phone,
        role,
        address,
        profile_image,
        status,
        created_at
     FROM users
     WHERE status = 'pending'
       AND role IN ('customer', 'decorator')
     ORDER BY created_at DESC"
);
?>

<?php include("../includes/admin_header.php"); ?>
<?php include("../includes/admin_sidebar.php"); ?>

<div class="admin-content">

    <!-- ==============================
         SUCCESS / ERROR MESSAGES
    =============================== -->
    <?php if ($success_message != ""): ?>
        <div class="settings-alert success-alert">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($success_message); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message != ""): ?>
        <div class="settings-alert error-alert">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <!-- ==============================
         PAGE HEADER
    =============================== -->
    <div class="page-header">
        <div>
            <h1>Pending Requests</h1>
            <p>Review and manage customer and decorator registration requests.</p>
        </div>
    </div>

    <!-- ==============================
         REQUEST BOX
    =============================== -->
    <div class="dashboard-box">

        <div class="dashboard-box-header">
            <h2>
                <i class="fas fa-user-clock"></i>
                Registration Requests
            </h2>
            <span class="request-count">
                <?php echo mysqli_num_rows($pending_query); ?> Pending
            </span>
        </div>

        <div class="request-table-wrapper">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (mysqli_num_rows($pending_query) > 0): ?>
                    <?php $serial = 1; ?>
                    <?php while ($user = mysqli_fetch_assoc($pending_query)): ?>
                        <tr>
                            <!-- Serial -->
                            <td><?php echo $serial++; ?></td>

                            <!-- User -->
                            <td>
                                <div class="request-user">
                                    <img
                                        src="../<?php echo htmlspecialchars($user["profile_image"] ?: "uploads/default.png"); ?>"
                                        alt="Profile"
                                    >
                                    <div>
                                        <strong><?php echo htmlspecialchars($user["full_name"]); ?></strong>
                                        <small><?php echo htmlspecialchars($user["address"]); ?></small>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td><?php echo htmlspecialchars($user["email"]); ?></td>

                            <!-- Phone -->
                            <td><?php echo htmlspecialchars($user["phone"]); ?></td>

                            <!-- Role -->
                            <td>
                                <?php if ($user["role"] == "customer"): ?>
                                    <span class="role-badge customer">Customer</span>
                                <?php else: ?>
                                    <span class="role-badge decorator">Decorator</span>
                                <?php endif; ?>
                            </td>

                            <!-- Date -->
                            <td><?php echo date("d M Y", strtotime($user["created_at"])); ?></td>

                            <!-- Actions -->
                            <td>
                                <div class="request-actions">

                                    <!-- APPROVE FORM -->
                                    <form action="update_request_status.php" method="POST" class="approve-form" style="position: relative;">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$user["id"]; ?>">
                                        <input type="hidden" name="action" value="approve">

                                        <button
                                            type="button"
                                            class="approve-btn"
                                            onclick="showApproveConfirm(<?php echo (int)$user["id"]; ?>)"
                                        >
                                            <i class="fas fa-check"></i>
                                            Approve
                                        </button>

                                        <!-- Approve Confirm Box -->
                                        <div id="approve-confirm-<?php echo (int)$user["id"]; ?>" class="confirm-box approve-confirm" style="display: none;">
                                            <p>Are you sure you want to approve this user?</p>
                                            <div class="confirm-actions">
                                                <button
                                                    type="button"
                                                    class="cancel-confirm"
                                                    onclick="hideApproveConfirm(<?php echo (int)$user["id"]; ?>)"
                                                >
                                                    Cancel
                                                </button>
                                                <button type="submit" class="confirm-ok">OK</button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- BLOCK FORM -->
                                    <form action="update_request_status.php" method="POST" class="block-form" style="position: relative;">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$user["id"]; ?>">
                                        <input type="hidden" name="action" value="block">

                                        <button
                                            type="button"
                                            class="block-btn"
                                            onclick="showBlockConfirm(<?php echo (int)$user["id"]; ?>)"
                                        >
                                            <i class="fas fa-ban"></i>
                                            Block
                                        </button>

                                        <!-- Block Confirm Box -->
                                        <div id="block-confirm-<?php echo (int)$user["id"]; ?>" class="confirm-box block-confirm" style="display: none;">
                                            <p>Are you sure you want to block this user?</p>
                                            <div class="confirm-actions">
                                                <button
                                                    type="button"
                                                    class="cancel-confirm"
                                                    onclick="hideBlockConfirm(<?php echo (int)$user["id"]; ?>)"
                                                >
                                                    Cancel
                                                </button>
                                                <button type="submit" class="confirm-ok block-ok">OK</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-request">
                            <i class="fas fa-check-circle"></i>
                            <h3>No Pending Requests</h3>
                            <p>There are currently no registration requests waiting for approval.</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================
     JAVASCRIPT FOR CONFIRM BOX
=============================== -->
<script>
function showApproveConfirm(userId) {
    // Hide block confirm if open
    hideBlockConfirm(userId);
    document.getElementById('approve-confirm-' + userId).style.display = 'block';
}

function hideApproveConfirm(userId) {
    document.getElementById('approve-confirm-' + userId).style.display = 'none';
}

function showBlockConfirm(userId) {
    // Hide approve confirm if open
    hideApproveConfirm(userId);
    document.getElementById('block-confirm-' + userId).style.display = 'block';
}

function hideBlockConfirm(userId) {
    document.getElementById('block-confirm-' + userId).style.display = 'none';
}
</script>

</body>
</html>