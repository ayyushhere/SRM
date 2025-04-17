<?php
header('Content-Type: application/json');
include '../config/db.php';

// Check if connection is valid
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// SQL query
$sql = "SELECT id, name, email, phone, location FROM suppliers ORDER BY id DESC";
$result = $conn->query($sql);

$suppliers = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
    echo json_encode($suppliers);
} else if ($result && $result->num_rows == 0) {
    echo json_encode(["message" => "No suppliers found"]);
} else {
    echo json_encode(["error" => "Query failed", "details" => $conn->error]);
}

$conn->close();
?>
