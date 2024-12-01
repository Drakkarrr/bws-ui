<?php
include 'db_connection/db_connection.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secret_key = $_POST['secret_key'];

    // Check if the secret key exists in the database
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_admin WHERE secret_key = ?");
    $stmt->bind_param("s", $secret_key);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if ($count > 0) {
        echo 'valid';
    } else {
        echo 'invalid';
    }
}
?>