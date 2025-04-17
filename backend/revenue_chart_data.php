<?php
header('Content-Type: application/json');
include '../config/db.php'; // ✅ assumes mysqli connection

$sql = "
    SELECT 
        DATE_FORMAT(order_date, '%b') AS month,
        SUM(amount) AS revenue 
    FROM orders 
    WHERE status = 'Completed'
    GROUP BY month
    ORDER BY MONTH(order_date)
";

$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['month'];
        $data[] = $row['revenue'];
    }
    echo json_encode(['labels' => $labels, 'data' => $data]);
} else {
    echo json_encode(['labels' => [], 'data' => []]);
}

$conn->close();
?>
