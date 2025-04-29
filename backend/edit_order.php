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

// Check if it's a GET request to retrieve order data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
    
    // Prepare select statement to get order details
    $stmt = $conn->prepare("
        SELECT 
            o.id AS order_id,
            o.supplier_id,
            s.name AS supplier_name,
            o.amount,
            o.order_date,
            o.status
        FROM orders o
        JOIN suppliers s ON o.supplier_id = s.id
        WHERE o.id = ?
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $order_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $order = $result->fetch_assoc();
                
                // Get all suppliers for the dropdown
                $suppliers_query = "SELECT id, name FROM suppliers ORDER BY name";
                $suppliers_result = $conn->query($suppliers_query);
                $suppliers = [];
                
                if ($suppliers_result->num_rows > 0) {
                    while ($row = $suppliers_result->fetch_assoc()) {
                        $suppliers[] = $row;
                    }
                }
                
                echo json_encode([
                    'success' => true, 
                    'order' => $order,
                    'suppliers' => $suppliers
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Order not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to execute query: ' . $conn->error]);
        }
        
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare select statement: ' . $conn->error]);
    }
}
// Check if it's a POST request to update order data
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }
    
    // Validate required fields
    if (!isset($data['order_id']) || !isset($data['supplier_id']) || !isset($data['amount']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    $order_id = intval($data['order_id']);
    $supplier_id = intval($data['supplier_id']);
    $amount = floatval($data['amount']);
    $status = $data['status'];
    
    // Basic validation
    if ($amount <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount must be greater than zero']);
        exit;
    }
    
    if (!in_array($status, ['Pending', 'Completed', 'Cancelled'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid status value']);
        exit;
    }
    
    // Prepare update statement
    $stmt = $conn->prepare("UPDATE orders SET supplier_id = ?, amount = ?, status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("idsi", $supplier_id, $amount, $status, $order_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
            } else {
                echo json_encode(['success' => true, 'message' => 'No changes made to order']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update order: ' . $conn->error]);
        }
        
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare update statement: ' . $conn->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?> 