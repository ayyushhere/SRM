<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../config/db.php';

$sql = "SELECT o.id AS order_id, s.name AS supplier_name, o.amount, o.status 
        FROM orders o 
        JOIN suppliers s ON o.supplier_id = s.id 
        ORDER BY o.id DESC 
        LIMIT 5";

$result = $conn->query($sql);

$orders = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
} else {
    echo json_encode([]);
}

$conn->close();
?>
