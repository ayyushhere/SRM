<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';

// Check if connection is valid
if (!$conn) {
    error_log("Database connection failed in get_suppliers.php");
    echo json_encode([]);
    exit();
}

try {
    // SQL query
    $sql = "SELECT id, name, email, phone, location FROM suppliers ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result === false) {
        error_log("Query failed in get_suppliers.php: " . $conn->error);
        echo json_encode([]);
        exit();
    }

    $suppliers = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }
    }

    echo json_encode($suppliers);

} catch (Exception $e) {
    error_log("Exception in get_suppliers.php: " . $e->getMessage());
    echo json_encode([]);
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>
