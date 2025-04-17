<?php
header('Content-Type: application/json');
include '../config/db.php';

// Check DB connection
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get POST data
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$phone = $_POST['contact'] ?? null; // Change 'contact' to 'phone'
$location = $_POST['address'] ?? null; // Change 'address' to 'location'

// Validate inputs
if (!$name || !$email || !$phone || !$location) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Prepare and execute insert
$query = "INSERT INTO suppliers (name, email, phone, location) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $location);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Supplier added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to insert supplier: " . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["error" => "Query preparation failed: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
