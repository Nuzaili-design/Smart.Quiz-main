<?php
// Database connection file (db.php)
$localhost = 'sql211.infinityfree.com';
$username = 'if0_38461346';
$password = '3yHhBuojQmPsWy';
$database = 'if0_38461346_smartquiz';

$conn = new mysqli($localhost, $username, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
