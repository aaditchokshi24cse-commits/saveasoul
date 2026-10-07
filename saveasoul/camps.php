<?php
require_once 'config.php';
$page_title = "Donation Camps";
$base_path = "";

$result = mysqli_query($conn, "SELECT * FROM camps WHERE camp_date >= CURDATE() ORDER BY camp_date ASC");

include 'includes/header.php';
?>

<div class="container">
    <h2 class="section-title">Upcoming Donation Camps</h2>
    <p class="section-subtitle">Find a blood donation drive happening near you</p>

    <div class="features">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($camp = mysqli_fetch_assoc($result)): ?>
                <div class="feature-card" style="text-align:left;">
                    <h3>🩸 <?php echo htmlspecialchars($camp['camp_name']); ?></h3>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($camp['venue']); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($camp['city']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($camp['camp_date'])); ?></p>
                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($camp['camp_time'])); ?></p>
                    <p><strong>Organizer:</strong> <?php echo htmlspecialchars($camp['organizer']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No upcoming camps at the moment. Please check back later.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
