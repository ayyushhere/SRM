<?php
header('Content-Type: application/json');

// Database connection parameters
include '../config/db.php';

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Validate and sanitize order ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = intval($_GET['id']);

    // Prepare delete statement
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete order: ' . $conn->error]);
        }

        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare delete statement: ' . $conn->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing order ID']);
}

$conn->close();
?>
