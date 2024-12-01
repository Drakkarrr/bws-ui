<?php
session_start();
include_once '../bws_ui/db_connection/db_connection.php';

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Get the discount ID from the request body (JSON)
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing ID.']);
    exit;
}

$id = intval($data['id']); // Sanitize and get the ID

// Prepare and execute the DELETE query
$query = "DELETE FROM discounts WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

// Bind the ID and execute the delete operation
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Discount deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No rows affected. The discount may not exist.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete discount: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>