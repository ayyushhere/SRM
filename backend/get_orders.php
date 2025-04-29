<?php
// backend/get_orders.php

// Allow CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file setup
$logFile = __DIR__ . '/debug.log';
function logError($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

try {
    // Include database connection
    require_once '../config/db.php';

    // Check connection
    if ($conn->connect_error) {
        logError("Database connection failed: " . $conn->connect_error);
        throw new Exception("Database connection failed");
    }

    // Test database selection
    if (!$conn->select_db('srm_system')) {
        logError("Database selection failed: " . $conn->error);
        throw new Exception("Database selection failed");
    }

    // Check if tables exist
    $tablesExist = $conn->query("
        SELECT COUNT(*) as count 
        FROM information_schema.tables 
        WHERE table_schema = 'srm_system' 
        AND table_name IN ('orders', 'suppliers')
    ");
    
    if (!$tablesExist) {
        logError("Failed to check tables: " . $conn->error);
        throw new Exception("Failed to check tables");
    }

    $tableCount = $tablesExist->fetch_assoc()['count'];
    if ($tableCount < 2) {
        logError("Required tables are missing. Found $tableCount of 2 required tables.");
        throw new Exception("Required tables are missing");
    }

    // SQL query to get orders with supplier information
    $sql = "
        SELECT 
            o.id AS order_id,
            s.name AS supplier_name,
            o.amount,
            o.status,
            DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date
        FROM orders o
        JOIN suppliers s ON o.supplier_id = s.id
        ORDER BY o.id DESC
    ";

    $result = $conn->query($sql);

    if ($result === false) {
        logError("Query failed: " . $conn->error . "\nSQL: $sql");
        throw new Exception("Failed to fetch orders");
    }

    $orders = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        logError("Successfully fetched " . count($orders) . " orders");
    } else {
        logError("No orders found in the database");
    }

    echo json_encode([
        'success' => true, 
        'data' => $orders,
        'debug' => [
            'query' => $sql,
            'rowCount' => count($orders)
        ]
    ]);

} catch (Exception $e) {
    logError("Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} finally {
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>
