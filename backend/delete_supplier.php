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

// Validate and sanitize supplier ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $supplier_id = intval($_GET['id']);

    // First, check if the supplier has associated orders
    $check_stmt = $conn->prepare("SELECT COUNT(*) as order_count FROM orders WHERE supplier_id = ?");
    $check_stmt->bind_param("i", $supplier_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $order_count = $result->fetch_assoc()['order_count'];
    $check_stmt->close();

    if ($order_count > 0) {
        // Supplier has orders, cannot delete
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Cannot delete supplier with existing orders. Please delete associated orders first.'
        ]);
    } else {
        // Prepare delete statement
        $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $supplier_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Supplier deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete supplier']);
            }

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare delete statement']);
        }
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing supplier ID']);
}

$conn->close();
?> 