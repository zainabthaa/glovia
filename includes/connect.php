<?php
// Database settings live in config.php (not committed to Git).
// Copy config.example.php to config.php and fill in your own values.
require_once __DIR__ . '/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
