<?php
require_once '../config.php';
$page_title = "Blood Requests";
$active = "requests";

$message = "";
$error = "";

if (isset($_GET['action']) && isset($_GET['id']))
{
    $id = intval($_GET['id']);

    if ($_GET['action'] == 'fulfill')
    {
        // Get the request details first so we know how many units/what blood group to deduct
        $req_result = mysqli_query($conn, "SELECT blood_group, units_needed, status FROM requests WHERE request_id = $id");
        $req_row = mysqli_fetch_assoc($req_result);

        if ($req_row && $req_row['status'] == 'Pending')
        {
            $blood_group = $req_row['blood_group'];
            $units_needed = intval($req_row['units_needed']);

            // Check current stock
            $stock_result = mysqli_query($conn, "SELECT units_available FROM blood_inventory WHERE blood_group = '" . mysqli_real_escape_string($conn, $blood_group) . "'");
            $stock_row = mysqli_fetch_assoc($stock_result);
            $current_stock = $stock_row ? intval($stock_row['units_available']) : 0;

            if ($current_stock < $units_needed)
            {
                $_SESSION['flash_error'] = "Cannot fulfill: only $current_stock units of $blood_group available, but $units_needed needed. Update inventory first.";
            }
            else
            {
                // Deduct the units from inventory (never below 0, GREATEST protects against race conditions)
                $stmt = mysqli_prepare($conn, "UPDATE blood_inventory SET units_available = GREATEST(units_available - ?, 0) WHERE blood_group = ?");
                mysqli_stmt_bind_param($stmt, "is", $units_needed, $blood_group);
                mysqli_stmt_execute($stmt);

                // Mark request as fulfilled
                mysqli_query($conn, "UPDATE requests SET status = 'Fulfilled' WHERE request_id = $id");

                $_SESSION['flash_message'] = "Request fulfilled. $units_needed unit(s) of $blood_group deducted from inventory.";
            }
        }
    }
    elseif ($_GET['action'] == 'cancel')
    {
        mysqli_query($conn, "UPDATE requests SET status = 'Cancelled' WHERE request_id = $id");
        $_SESSION['flash_message'] = "Request cancelled.";
    }

    header("Location: requests.php");
    exit();
}

if (isset($_SESSION['flash_message']))
{
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
if (isset($_SESSION['flash_error']))
{
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$result = mysqli_query($conn, "SELECT r.*, u.full_name, u.phone FROM requests r
                                JOIN users u ON r.user_id = u.user_id
                                ORDER BY FIELD(r.urgency, 'Critical','Urgent','Normal'), r.created_at DESC");

include 'admin_header.php';
?>

<h2 class="section-title" style="text-align:left;">Blood Requests</h2>
<p class="section-subtitle" style="text-align:left;">Review and manage incoming blood requests</p>

<?php if ($message != ""): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error != ""): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Requested By</th>
            <th>Blood Group</th>
            <th>Units</th>
            <th>Hospital</th>
            <th>City</th>
            <th>Urgency</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?> (<?php echo htmlspecialchars($row['phone']); ?>)</td>
                <td><?php echo $row['blood_group']; ?></td>
                <td><?php echo $row['units_needed']; ?></td>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><span class="badge badge-<?php echo strtolower($row['urgency']); ?>"><?php echo $row['urgency']; ?></span></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] == 'Pending'): ?>
                        <a href="requests.php?action=fulfill&id=<?php echo $row['request_id']; ?>" onclick="return confirm('Fulfill this request? This will deduct <?php echo $row['units_needed']; ?> unit(s) of <?php echo $row['blood_group']; ?> from inventory.');">Mark Fulfilled</a> |
                        <a href="requests.php?action=cancel&id=<?php echo $row['request_id']; ?>">Cancel</a>
                    <?php else: ?>
                        &mdash;
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'admin_footer.php'; ?>
