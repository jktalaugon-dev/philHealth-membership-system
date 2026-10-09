<?php
$conn = new mysqli("localhost", "root", "", "philhealth_db");

if ($conn->connect_error) {
    die("Database Connection Error Blocked Core Operations: " . $conn->connect_error);
}

// Uniform standard layout configuration setups
$conn->set_charset("utf8mb4");
?>