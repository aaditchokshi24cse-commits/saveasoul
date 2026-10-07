<?php
// Run this file once in your browser to generate a proper password hash.
// Copy the printed hash and UPDATE the admin row in the database with it.
// Then DELETE this file for security.

$plain_password = "admin123";
$hash = password_hash($plain_password, PASSWORD_DEFAULT);

echo "Plain password: " . $plain_password . "<br>";
echo "Hashed password: " . $hash . "<br><br>";
echo "Run this SQL query in phpMyAdmin to update the admin account:<br><br>";
echo "UPDATE users SET password = '" . $hash . "' WHERE email = 'admin@saveasoul.com';";
?>
