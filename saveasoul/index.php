<?php
require_once 'config.php';
$page_title = "Home";
$base_path = "";

$donor_count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM donors");
$donor_count = mysqli_fetch_assoc($donor_count_result)['total'];

$camp_count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM camps");
$camp_count = mysqli_fetch_assoc($camp_count_result)['total'];

$units_result = mysqli_query($conn, "SELECT SUM(units_available) as total FROM blood_inventory");
$total_units = mysqli_fetch_assoc($units_result)['total'];
if ($total_units === null) { $total_units = 0; }

include 'includes/header.php';
?>

<section class="hero">
    <h1>Donate Blood, Save Lives</h1>
    <p>SaveASoul connects voluntary blood donors with patients in need, in real time. Join our network and become someone's second chance at life.</p>
    <a href="register.php" class="btn">Become a Donor</a>
    <a href="request_blood.php" class="btn btn-outline">Request Blood</a>
</section>

<section class="stats">
    <div class="stat-box">
        <h2><?php echo $donor_count; ?>+</h2>
        <p>Registered Donors</p>
    </div>
    <div class="stat-box">
        <h2><?php echo $total_units; ?></h2>
        <p>Blood Units Available</p>
    </div>
    <div class="stat-box">
        <h2><?php echo $camp_count; ?></h2>
        <p>Upcoming Camps</p>
    </div>
</section>

<div class="container">
    <h2 class="section-title">How SaveASoul Helps</h2>
    <p class="section-subtitle">A simple platform connecting donors, recipients, and blood banks</p>

    <div class="features">
        <div class="feature-card">
            <div class="icon">🩸</div>
            <h3>Find Donors</h3>
            <p>Search verified donors by blood group and city in seconds during emergencies.</p>
        </div>
        <div class="feature-card">
            <div class="icon">📋</div>
            <h3>Track Inventory</h3>
            <p>Blood banks manage live stock levels for every blood group, updated automatically.</p>
        </div>
        <div class="feature-card">
            <div class="icon">📅</div>
            <h3>Donation Camps</h3>
            <p>Discover upcoming donation drives happening near you.</p>
        </div>
        <div class="feature-card">
            <div class="icon">🏥</div>
            <h3>Raise Requests</h3>
            <p>Hospitals and patients can post urgent blood requirements instantly.</p>
        </div>
        <div class="feature-card">
            <div class="icon">⏰</div>
            <h3>Eligibility Tracker</h3>
            <p>Automatically calculates your next eligible donation date.</p>
        </div>
        <div class="feature-card">
            <div class="icon">🎖️</div>
            <h3>Donation History</h3>
            <p>Every donor gets a record of their life-saving contributions.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
