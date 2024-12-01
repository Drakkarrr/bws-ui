<?php
session_start();
include_once '../bws_ui/db_connection/db_connection.php';

header('Content-Type: application/json');

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch discount data
$query = "
    SELECT s.id, s.name AS service_name, s.price, u.username, d.discount_percentage, 
           s.price - (s.price * d.discount_percentage / 100) AS discounted_price, 
           d.start_time, d.end_time 
    FROM services s 
    JOIN discounts d ON s.id = d.service_id 
    JOIN tbl_users u ON d.user_id = u.user_id 
    ORDER BY s.name";
$result = $conn->query($query);

$discounts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $discounts[] = [
            $row['service_name'],
            $row['username'], // Ensure this matches the column name in your database
            '₱' . number_format($row['price'], 2), // Correct column name for original price
            $row['discount_percentage'] . '%',
            '₱' . number_format($row['discounted_price'], 2),
            $row['start_time'],
            $row['end_time'],
            $row['id'] // Ensure this matches the column name for discount ID
        ];
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No discounts found.']);
    exit;
}

$conn->close();

// Output as JSON for DataTables
echo json_encode(['data' => $discounts]);
?>