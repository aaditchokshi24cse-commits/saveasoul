<?php
// =====================================================
// Database Configuration
// Update these values to match your MySQL setup
// =====================================================
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "saveasoul";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn)
{
    die("Database connection failed: " . mysqli_connect_error());
}

session_start();
?>
