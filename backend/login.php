<?php
session_start();
header('Content-Type: application/json');
include '../config/db.php';

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST
    // Check if data is sent as regular form data or as JSON
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Regular form submission
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
    } else {
        // JSON API request
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Email and password are required']);
            exit;
        }
        
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $password = $data['password'];
    }
    
    // Check if the email exists in users table
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password (in a real application, we would use password_verify with hashed passwords)
    if ($password !== $user['password']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
        exit;
    }
    
    // Set user session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
    ];
    
    // Return success with user information
    $response = [
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ],
        'redirect' => '../frontend/dashboard.html'
    ];
    
    // For regular form submission, redirect instead of returning JSON
    if (isset($_POST['email'])) {
        // Set a session variable with success message
        $_SESSION['login_message'] = 'Login successful';
        // Redirect to appropriate page
        header('Location: ' . $response['redirect']);
        exit;
    }
    
    // For API requests, return JSON
    echo json_encode($response);
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?>
