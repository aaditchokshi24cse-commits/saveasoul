<?php
require_once '../config.php';
$page_title = "Dashboard";
$active = "dashboard";

$total_donors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM donors"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$pending_verification = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_verified = 0 AND role != 'admin'"))['total'];
$pending_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM requests WHERE status = 'Pending'"))['total'];
$total_units = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(units_available) as total FROM blood_inventory"))['total'];
$upcoming_camps = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM camps WHERE camp_date >= CURDATE()"))['total'];

include 'admin_header.php';
?>

<h2 class="section-title" style="text-align:left;">Admin Dashboard</h2>
<p class="section-subtitle" style="text-align:left;">Overview of the SaveASoul platform</p>

<div class="info-grid">
    <div class="info-card">
        <h4>Total Registered Users</h4>
        <p><?php echo $total_users; ?></p>
    </div>
    <div class="info-card">
        <h4>Total Donors</h4>
        <p><?php echo $total_donors; ?></p>
    </div>
    <div class="info-card">
        <h4>Pending Verifications</h4>
        <p><?php echo $pending_verification; ?></p>
    </div>
    <div class="info-card">
        <h4>Pending Blood Requests</h4>
        <p><?php echo $pending_requests; ?></p>
    </div>
    <div class="info-card">
        <h4>Total Blood Units in Stock</h4>
        <p><?php echo $total_units; ?></p>
    </div>
    <div class="info-card">
        <h4>Upcoming Camps</h4>
        <p><?php echo $upcoming_camps; ?></p>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
