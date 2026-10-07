<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - SaveASoul" : "SaveASoul - Blood Donation Network"; ?></title>
    <link rel="stylesheet" href="<?php echo isset($base_path) ? $base_path : ""; ?>css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">Save<span>A</span>Soul</div>
    <ul>
        <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>index.php">Home</a></li>
        <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>search_donors.php">Find Donors</a></li>
        <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>camps.php">Donation Camps</a></li>
        <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>request_blood.php">Request Blood</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>admin/dashboard.php">Admin Panel</a></li>
            <?php else: ?>
                <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>dashboard.php">My Dashboard</a></li>
            <?php endif; ?>
            <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>login.php">Login</a></li>
            <li><a href="<?php echo isset($base_path) ? $base_path : ""; ?>register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
