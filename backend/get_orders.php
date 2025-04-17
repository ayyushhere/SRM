<?php
// backend/get_orders.php

header('Content-Type: application/json');
include '../config/db.php';

// Check DB connection
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Query to join orders with suppliers and fetch supplier name
$query = "
    SELECT 
        o.id AS order_id,
        s.name AS supplier_name,
        o.amount,
        o.order_date,
        o.status
    FROM orders o
    JOIN suppliers s ON o.supplier_id = s.id
    ORDER BY o.order_date DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn)]);
    exit;
}

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Output JSON
echo json_encode($orders);
?>
