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

// Check if it's a GET request to retrieve supplier data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $supplier_id = intval($_GET['id']);
    
    // Prepare select statement
    $stmt = $conn->prepare("SELECT id, name, email, phone, location FROM suppliers WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $supplier_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $supplier = $result->fetch_assoc();
                echo json_encode(['success' => true, 'supplier' => $supplier]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Supplier not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
        }
        
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare select statement']);
    }
}
// Check if it's a POST request to update supplier data
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }
    
    // Validate required fields
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['email']) || !isset($data['phone']) || !isset($data['location'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    $supplier_id = intval($data['id']);
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $location = $data['location'];
    
    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($location)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }
    
    // Prepare update statement
    $stmt = $conn->prepare("UPDATE suppliers SET name = ?, email = ?, phone = ?, location = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssssi", $name, $email, $phone, $location, $supplier_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Supplier updated successfully']);
            } else {
                echo json_encode(['success' => true, 'message' => 'No changes made to supplier']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update supplier']);
        }
        
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare update statement']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?> 