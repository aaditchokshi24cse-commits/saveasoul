<?php
require_once '../config.php';
$page_title = "Blood Inventory";
$active = "inventory";

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $inventory_id = intval($_POST['inventory_id']);
    $units = intval($_POST['units_available']);

    if ($units < 0) { $units = 0; }

    $stmt = mysqli_prepare($conn, "UPDATE blood_inventory SET units_available = ? WHERE inventory_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $units, $inventory_id);
    mysqli_stmt_execute($stmt);

    $message = "Inventory updated successfully.";
}

$result = mysqli_query($conn, "SELECT * FROM blood_inventory ORDER BY blood_group ASC");

include 'admin_header.php';
?>

<h2 class="section-title" style="text-align:left;">Blood Inventory</h2>
<p class="section-subtitle" style="text-align:left;">Update available blood units for each group. Stock also updates automatically when donations are logged or requests are fulfilled.</p>

<?php if ($message != ""): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Blood Group</th>
            <th>Units Available</th>
            <th>Stock Level</th>
            <th>Last Updated</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><strong><?php echo $row['blood_group']; ?></strong></td>
                <td><?php echo $row['units_available']; ?></td>
                <td>
                    <?php if ($row['units_available'] < 5): ?>
                        <span class="badge badge-low">Low Stock</span>
                    <?php else: ?>
                        <span class="badge badge-ok">Sufficient</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('d M Y, h:i A', strtotime($row['last_updated'])); ?></td>
                <td>
                    <form method="POST" action="inventory.php" style="display:flex; gap:8px;">
                        <input type="hidden" name="inventory_id" value="<?php echo $row['inventory_id']; ?>">
                        <input type="number" name="units_available" value="<?php echo $row['units_available']; ?>" min="0" style="width:80px; padding:6px; border:1px solid #ccc; border-radius:4px;">
                        <button type="submit" class="btn-submit" style="width:auto; padding:6px 16px;">Save</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'admin_footer.php'; ?>
