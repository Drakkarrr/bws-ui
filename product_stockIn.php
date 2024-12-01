<?php
$conn = new mysqli('localhost', 'root', '', 'bwsdb');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['addedProducts']) && isset($_POST['stockDate'])) {
    $addedProducts = json_decode($_POST['addedProducts'], true);
    $stockDate = $_POST['stockDate'];

    if (is_array($addedProducts) && !empty($addedProducts)) {
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ?, stock_in_date = ? WHERE id = ?");
        if ($stmt === false) {
            echo json_encode(["status" => "error", "message" => "Prepare statement failed: " . $conn->error]);
            exit;
        }

        foreach ($addedProducts as $productId => $productData) {
            if (isset($productData['quantity']) && is_numeric($productData['quantity'])) {
                $quantityToAdd = (int) $productData['quantity'];
                $stmt->bind_param("isi", $quantityToAdd, $stockDate, $productId);

                if (!$stmt->execute()) {
                    echo json_encode(["status" => "error", "message" => "Error updating product with ID $productId: " . $stmt->error]);
                    exit;
                }

                // Check for low stock threshold
                $query = "SELECT quantity, threshold FROM products WHERE id = ?";
                $checkStmt = $conn->prepare($query);
                $checkStmt->bind_param("i", $productId);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $row = $result->fetch_assoc();

                if ($row && $row['quantity'] <= $row['threshold']) {
                    echo json_encode(["status" => "warning", "message" => "Product ID $productId is low on stock."]);
                    exit;
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid quantity data for product ID $productId"]);
                exit;
            }
        }

        echo json_encode(["status" => "success", "message" => "Stock updated successfully!"]);
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "No valid products to update."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No products to update or date missing."]);
}

$conn->close();
?>