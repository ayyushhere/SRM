<?php
// config/db.php

$host = 'localhost'; // Database host
$user = 'root';      // Database username
$password = '';      // Database password
$database = 'srm_system'; // Database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
