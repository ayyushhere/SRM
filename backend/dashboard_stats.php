<?php
header('Content-Type: application/json');

// ✅ Replace these with actual DB credentials
$host = 'localhost';
$db = 'srm_system';         // 👈 Your actual database name
$user = 'root';             // 👈 Your username (default is 'root' for XAMPP)
$pass = '';                 // 👈 Password (usually empty in local dev)

// 🔌 Database connection
$conn = new mysqli($host, $user, $pass, $db);

// ❌ Handle connection error
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// ✅ Fetch total suppliers
$totalSuppliers = 0;
$pendingOrders = 0;
$completedOrders = 0;
$totalOrders = 0;
$totalRevenue = 0.0;

if ($res = $conn->query("SELECT COUNT(*) as total FROM suppliers")) {
    $totalSuppliers = $res->fetch_assoc()['total'] ?? 0;
}

// ✅ Fetch pending orders
if ($res = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'")) {
    $pendingOrders = $res->fetch_assoc()['total'] ?? 0;
}

// ✅ Fetch completed orders
if ($res = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Completed'")) {
    $completedOrders = $res->fetch_assoc()['total'] ?? 0;
}

// ✅ Calculate total orders (pending + completed)
$totalOrders = $pendingOrders + $completedOrders;

// ✅ Fetch total revenue
if ($res = $conn->query("SELECT SUM(amount) as total FROM orders WHERE status = 'Completed'")) {
    $totalRevenue = $res->fetch_assoc()['total'] ?? 0;
}

echo json_encode([
    'totalSuppliers' => $totalSuppliers,
    'pendingOrders' => $pendingOrders,
    'completedOrders' => $completedOrders,
    'totalOrders' => $totalOrders,
    'totalRevenue' => number_format((float)$totalRevenue, 2, '.', '')  // optional formatting
]);

$conn->close();
?>
