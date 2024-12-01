<?php
include_once '../bws_ui/db_connection/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
    $discount_percentage = isset($_POST['discount_percentage']) ? intval($_POST['discount_percentage']) : 0;
    $start_time = isset($_POST['start_time']) ? $conn->real_escape_string($_POST['start_time']) : '';
    $end_time = isset($_POST['end_time']) ? $conn->real_escape_string($_POST['end_time']) : '';
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    // Validate inputs
    if ($service_id == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid service ID."]);
        exit;
    }
    if ($discount_percentage == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid discount percentage."]);
        exit;
    }
    if (empty($start_time)) {
        echo json_encode(["status" => "error", "message" => "Start time is required."]);
        exit;
    }
    if (empty($end_time)) {
        echo json_encode(["status" => "error", "message" => "End time is required."]);
        exit;
    }
    if ($user_id == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid user ID."]);
        exit;
    }

    // Fetch the original price of the service
    $price_sql = "SELECT price FROM services WHERE id = ?";
    $price_stmt = $conn->prepare($price_sql);
    $price_stmt->bind_param("i", $service_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    if ($price_result->num_rows > 0) {
        $price_row = $price_result->fetch_assoc();
        $original_price = $price_row['price'];
    } else {
        echo json_encode(["status" => "error", "message" => "Service not found."]);
        exit;
    }

    // Calculate the discounted price
    $discounted_price = $original_price - ($original_price * ($discount_percentage / 100));

    // Insert the discount into the database
    $insert_sql = "INSERT INTO discounts (service_id, discount_percentage, discounted_price, start_time, end_time, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iidssi", $service_id, $discount_percentage, $discounted_price, $start_time, $end_time, $user_id);
    if ($insert_stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Discount added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add discount."]);
    }
}
?>