<?php
require_once '../config.php';
require_once '../includes/shared_data.php';
$page_title = "Donation Camps";
$active = "camps";

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $camp_name = trim($_POST['camp_name']);
    $venue = trim($_POST['venue']);
    $city = trim($_POST['city']);
    $camp_date = $_POST['camp_date'];
    $camp_time = $_POST['camp_time'];
    $organizer = trim($_POST['organizer']);

    if ($camp_name == "" || $venue == "" || $city == "" || $camp_date == "" || $camp_time == "" || $organizer == "")
    {
        $message = "Please fill in all fields to add a camp.";
    }
    else
    {
        $stmt = mysqli_prepare($conn, "INSERT INTO camps (camp_name, venue, city, camp_date, camp_time, organizer) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $camp_name, $venue, $city, $camp_date, $camp_time, $organizer);
        mysqli_stmt_execute($stmt);

        $message = "Donation camp added successfully.";
    }
}

if (isset($_GET['delete']))
{
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM camps WHERE camp_id = $delete_id");
    header("Location: camps.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM camps ORDER BY camp_date ASC");

include 'admin_header.php';
?>

<h2 class="section-title" style="text-align:left;">Manage Donation Camps</h2>

<?php if ($message != ""): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="form-wrapper" style="max-width:100%; margin:0 0 30px 0;">
    <h2 style="text-align:left;">Add New Camp</h2>
    <form method="POST" action="camps.php" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label>Camp Name</label>
                <input type="text" name="camp_name" required>
            </div>
            <div class="form-group">
                <label>Organizer</label>
                <input type="text" name="organizer" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" required>
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" list="cityList" required>
                <datalist id="cityList">
                    <?php foreach ($indian_cities as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="camp_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="camp_time" required>
            </div>
        </div>
        <button type="submit" class="btn-submit">Add Camp</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Camp Name</th>
            <th>Venue</th>
            <th>City</th>
            <th>Date</th>
            <th>Time</th>
            <th>Organizer</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['camp_name']); ?></td>
                <td><?php echo htmlspecialchars($row['venue']); ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><?php echo date('d M Y', strtotime($row['camp_date'])); ?></td>
                <td><?php echo date('h:i A', strtotime($row['camp_time'])); ?></td>
                <td><?php echo htmlspecialchars($row['organizer']); ?></td>
                <td><a href="camps.php?delete=<?php echo $row['camp_id']; ?>" onclick="return confirm('Delete this camp?');">Delete</a></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'admin_footer.php'; ?>
