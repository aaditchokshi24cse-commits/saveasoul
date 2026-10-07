<?php
require_once 'config.php';
$page_title = "My Dashboard";
$base_path = "";

if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$donor = null;
$donation_history = [];
$my_requests = [];

if ($role == 'donor')
{
    $stmt2 = mysqli_prepare($conn, "SELECT * FROM donors WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $user_id);
    mysqli_stmt_execute($stmt2);
    $donor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

    if ($donor)
    {
        $stmt3 = mysqli_prepare($conn, "SELECT * FROM donations WHERE donor_id = ? ORDER BY donation_date DESC");
        mysqli_stmt_bind_param($stmt3, "i", $donor['donor_id']);
        mysqli_stmt_execute($stmt3);
        $donation_history = mysqli_fetch_all(mysqli_stmt_get_result($stmt3), MYSQLI_ASSOC);
    }
}
else
{
    $stmt3 = mysqli_prepare($conn, "SELECT * FROM requests WHERE user_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt3, "i", $user_id);
    mysqli_stmt_execute($stmt3);
    $my_requests = mysqli_fetch_all(mysqli_stmt_get_result($stmt3), MYSQLI_ASSOC);
}

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h2>
        <p>Role: <?php echo ucfirst($role); ?> | City: <?php echo htmlspecialchars($user['city']); ?></p>
    </div>

    <?php if ($role == 'donor' && $donor): ?>

        <div class="info-grid">
            <div class="info-card">
                <h4>Blood Group</h4>
                <p><?php echo $donor['blood_group']; ?></p>
            </div>
            <div class="info-card">
                <h4>Total Donations</h4>
                <p><?php echo count($donation_history); ?></p>
            </div>
            <div class="info-card">
                <h4>Availability</h4>
                <p><?php echo $donor['is_available'] ? 'Available' : 'Not Available'; ?></p>
            </div>
            <div class="info-card">
                <h4>Next Eligible Date</h4>
                <p>
                <?php
                    if ($donor['next_eligible_date'] && $donor['next_eligible_date'] > date('Y-m-d'))
                    {
                        echo date('d M Y', strtotime($donor['next_eligible_date']));
                    }
                    else
                    {
                        echo "Eligible Now";
                    }
                ?>
                </p>
            </div>
        </div>

        <h3 class="section-title" style="text-align:left; margin-bottom:16px;">Donation History</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Units Donated</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($donation_history) > 0): ?>
                    <?php foreach ($donation_history as $d): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($d['donation_date'])); ?></td>
                            <td><?php echo $d['units']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2">No donations recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php else: ?>

        <h3 class="section-title" style="text-align:left; margin-bottom:16px;">My Blood Requests</h3>
        <table>
            <thead>
                <tr>
                    <th>Blood Group</th>
                    <th>Units</th>
                    <th>Hospital</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($my_requests) > 0): ?>
                    <?php foreach ($my_requests as $r): ?>
                        <tr>
                            <td><?php echo $r['blood_group']; ?></td>
                            <td><?php echo $r['units_needed']; ?></td>
                            <td><?php echo htmlspecialchars($r['hospital_name']); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($r['urgency']); ?>"><?php echo $r['urgency']; ?></span></td>
                            <td><?php echo $r['status']; ?></td>
                            <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">You haven't raised any requests yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
