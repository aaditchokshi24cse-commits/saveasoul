<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin')
{
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - Admin - SaveASoul" : "Admin - SaveASoul"; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-layout">
    <div class="sidebar">
        <h3>SaveASoul Admin</h3>
        <a href="dashboard.php" class="<?php echo $active == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
        <a href="donors.php" class="<?php echo $active == 'donors' ? 'active' : ''; ?>">Manage Donors</a>
        <a href="inventory.php" class="<?php echo $active == 'inventory' ? 'active' : ''; ?>">Blood Inventory</a>
        <a href="camps.php" class="<?php echo $active == 'camps' ? 'active' : ''; ?>">Donation Camps</a>
        <a href="requests.php" class="<?php echo $active == 'requests' ? 'active' : ''; ?>">Blood Requests</a>
        <a href="../index.php">View Website</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="admin-content">
