<?php
require_once 'config.php';
require_once 'includes/shared_data.php';
$page_title = "Find Donors";
$base_path = "";

$blood_group = isset($_GET['blood_group']) ? $_GET['blood_group'] : "";
$city = isset($_GET['city']) ? trim($_GET['city']) : "";

$query = "SELECT u.full_name, u.phone, u.city, d.blood_group, d.is_available
          FROM donors d
          JOIN users u ON d.user_id = u.user_id
          WHERE u.is_verified = 1";

if ($blood_group != "")
{
    $query .= " AND d.blood_group = '" . mysqli_real_escape_string($conn, $blood_group) . "'";
}
if ($city != "")
{
    $query .= " AND u.city LIKE '%" . mysqli_real_escape_string($conn, $city) . "%'";
}

$query .= " ORDER BY d.is_available DESC";
$result = mysqli_query($conn, $query);

include 'includes/header.php';
?>

<div class="container">
    <h2 class="section-title">Find a Blood Donor</h2>
    <p class="section-subtitle">Search verified donors by blood group and city</p>

    <form method="GET" action="search_donors.php" class="search-bar">
        <select name="blood_group">
            <option value="">All Blood Groups</option>
            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                <option value="<?php echo $bg; ?>" <?php echo ($blood_group == $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="city" list="cityList" placeholder="All Cities (type to filter)" value="<?php echo htmlspecialchars($city); ?>">
        <datalist id="cityList">
            <?php foreach ($indian_cities as $c): ?>
                <option value="<?php echo htmlspecialchars($c); ?>">
            <?php endforeach; ?>
        </datalist>
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Blood Group</th>
                <th>City</th>
                <th>Contact</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo $row['blood_group']; ?></td>
                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                        <td>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php echo htmlspecialchars($row['phone']); ?>
                            <?php else: ?>
                                <a href="login.php">Login to view</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['is_available']): ?>
                                <span class="badge badge-available">Available</span>
                            <?php else: ?>
                                <span class="badge badge-unavailable">Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No donors found matching your search.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
