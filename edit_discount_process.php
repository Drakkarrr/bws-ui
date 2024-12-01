<?php
session_start();
include_once '../bws_ui/db_connection/db_connection.php';

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Get POST data
$data = $_POST;

// Validate required fields
if (!isset($data['id']) || !isset($data['discount_percentage']) || !isset($data['start_time']) || !isset($data['end_time'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing input data.']);
    exit;
}

// Sanitize and prepare data
$id = intval($data['id']);
$discount_percentage = intval($data['discount_percentage']);
$start_time = $data['start_time'];
$end_time = $data['end_time'];
$service_id = intval($data['service_id']);  // Service ID

// Fetch the original price from the services table to calculate the discounted price
$query = "SELECT price FROM services WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$stmt->bind_result($original_price);
$stmt->fetch();
$stmt->close();

if (!$original_price) {
    echo json_encode(['status' => 'error', 'message' => 'Original price not found for service ID.']);
    exit;
}

// Calculate the discounted price
$discounted_price = $original_price - ($original_price * ($discount_percentage / 100));

// Prepare and execute the update query
$query = "UPDATE discounts SET discount_percentage = ?, discounted_price = ?, start_time = ?, end_time = ? WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

// Bind parameters and execute
$stmt->bind_param("idssi", $discount_percentage, $discounted_price, $start_time, $end_time, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Discount updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No rows updated. Please verify the submitted data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update discount: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>