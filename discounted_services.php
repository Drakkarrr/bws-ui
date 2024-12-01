<?php
session_start();
include_once '../bws_ui/includes/header.php';
include_once '../bws_ui/db_connection/db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query for services with active discounts for the logged-in user
$query = "
    SELECT s.id, s.name AS service_name, s.description, s.image, 
           s.price AS original_price, 
           d.discount_percentage, 
           s.price - (s.price * d.discount_percentage / 100) AS discounted_price,
           d.end_time
    FROM services s 
    JOIN discounts d ON s.id = d.service_id 
    WHERE d.user_id = ? AND CURRENT_TIMESTAMP BETWEEN d.start_time AND d.end_time 
    ORDER BY s.name";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">    
    <!-- Go Back Button -->
    <div class="text-start mb-4">
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Go Back</a>
    </div>

    <h1 class="text-center mb-5">Discounted Services</h1>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $image = htmlspecialchars($row['image']);
                $imagePath = '../bws_ui/services_images/' . $image;
                $serviceId = htmlspecialchars($row['id']);
                $description = htmlspecialchars($row['description']);
                $originalPrice = $row['original_price'];
                $discountedPrice = $row['discounted_price'];
                $discountPercentage = $row['discount_percentage'];
                $endTime = $row['end_time'];
                $currentTime = date('Y-m-d H:i:s');

                echo '<div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex align-items-stretch">';
                echo '  <div class="card h-100">';
                echo '    <img src="' . $imagePath . '" class="card-img-top fixed-image" alt="' . htmlspecialchars($row['service_name']) . '">';
                echo '    <div class="card-body d-flex flex-column">';
                echo '      <h5 class="card-title">' . htmlspecialchars($row['service_name']) . '</h5>';
                echo '      <p class="card-text">' . $description . '</p>';
                
                // Display original price with strikethrough
                echo '      <p class="card-text text-muted" style="text-decoration: line-through; color: #999;">Original Price: ₱' . number_format($originalPrice, 2) . '</p>';
                
                // Display discounted price in red and bold
                echo '      <p class="card-text text-danger"><strong>Discounted Price: ₱' . number_format($discountedPrice, 2) . '</strong></p>';
                
                // Display discount percentage
                echo '      <p class="card-text"><small class="text-muted">(' . $discountPercentage . '% OFF)</small></p>';
                
                // Check if the discount has expired
                if ($currentTime > $endTime) {
                    echo '      <p class="card-text text-danger"><strong>Promo Expired</strong></p>';
                    echo '      <button class="btn btn-secondary mt-auto" disabled>Not Available</button>';
                } else {
                    echo '      <a href="../bws_ui/booking/booking.php?service_id=' . $serviceId . '&discounted_price=' . ($discountedPrice ?: $originalPrice) . '" class="btn btn-primary mt-auto">
                                    <i class="fas fa-calendar-alt"></i> Book Now
                                </a>';
                }
                
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center">No discounted services available at the moment.</p>';
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>

<?php include_once '../bws_ui/includes/footer.php'; ?>