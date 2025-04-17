<?php
// backend/add_order.php

header('Content-Type: application/json');

include '../config/db.php';

// Check DB connection
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get POST data
$supplier_id = $_POST['supplier_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$order_date = $_POST['order_date'] ?? date('Y-m-d');
$status = $_POST['status'] ?? 'Pending';

// Validate inputs
if (!$supplier_id || !$amount) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Prepare and execute insert
$query = "INSERT INTO orders (supplier_id, amount, order_date, status) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "idss", $supplier_id, $amount, $order_date, $status);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Order added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to insert order"]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["error" => "Query preparation failed"]);
}

mysqli_close($conn);
?>
