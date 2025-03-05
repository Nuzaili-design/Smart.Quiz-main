<?php
// Database connection file (db.php)
$localhost = 'localhost';
$username = 'root1';
$password = '';
$database = 'smartquiz';

$conn = new mysqli($localhost, $username, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
