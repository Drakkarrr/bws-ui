<?php
include_once '../bws_ui/db_connection/db_connection.php';

// Fetch services based on search term
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);

    $query = "
    SELECT s.id, s.name AS service_name, s.description, s.image, 
           s.price AS original_price, 
           d.discount_percentage, 
           CASE 
               WHEN CURRENT_TIMESTAMP BETWEEN d.start_time AND d.end_time 
               THEN s.price - (s.price * d.discount_percentage / 100) 
               ELSE NULL 
           END AS discounted_price,
           s.status, s.slots
    FROM services s 
    LEFT JOIN discounts d ON s.id = d.service_id AND CURRENT_TIMESTAMP BETWEEN d.start_time AND d.end_time 
    WHERE s.name LIKE '%$searchTerm%'
    GROUP BY s.id 
    ORDER BY s.name";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $image = htmlspecialchars($row['image']);
            $imagePath = '../bws_ui/services_images/' . $image;
            $serviceId = htmlspecialchars($row['id']);
            $description = htmlspecialchars($row['description']);
            $originalPrice = $row['original_price'];
            $discountedPrice = $row['discounted_price'];
            $discountPercentage = $row['discount_percentage'];
            $status = $row['status'];
            $slots = $row['slots'];
            $maxWords = 30;
            $words = explode(' ', $description);
            if (count($words) > $maxWords) {
                $description = implode(' ', array_slice($words, 0, $maxWords)) . '...';
            }

            echo '<div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex align-items-stretch service-item">';
            echo '  <div class="card h-100">';

            if (!empty($image) && file_exists($imagePath)) {
                echo '    <img src="' . $imagePath . '" class="card-img-top fixed-image" alt="' . htmlspecialchars($row['service_name']) . '">';
            }

            echo '    <div class="card-body d-flex flex-column">';
            echo '      <h5 class="card-title">' . htmlspecialchars($row['service_name']) . '</h5>';
            echo '      <p class="card-text flex-grow-1">' . $description . '</p>';

            if ($discountedPrice !== null) {
                // Show original price with strikethrough and discounted price if a discount is active
                echo '<p class="card-text text-muted" style="text-decoration: line-through; color: #999;">Original Price: ₱' . number_format($originalPrice, 2) . '</p>';
                echo '<p class="card-text text-danger"><strong>Discounted Price: ₱' . number_format($discountedPrice, 2) . '</strong></p>';
                echo '<p class="card-text"><small class="text-muted">(' . $discountPercentage . '% OFF)</small></p>';
            } else {
                // Show only the original price if there's no active discount
                echo '<p class="card-text"><strong>Original Price: ₱' . number_format($originalPrice, 2) . '</strong></p>';
            }

            // Display the number of slots available
            echo '<p class="card-text"><strong>Slots Available: ' . $slots . '</strong></p>';

            // Check if the service is available before allowing booking
            if ($status == 'Available') {
                echo '      <a href="../bws_ui/booking/booking.php?service_id=' . $serviceId . '&discounted_price=' . ($discountedPrice ?: $originalPrice) . '" class="btn btn-primary mt-auto">
                                <i class="fas fa-calendar-alt"></i> Book Now
                            </a>';
            } else {
                echo '      <button class="btn btn-secondary mt-auto" disabled>Not Available</button>';
            }

            echo '    </div>';
            echo '  </div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-center">No services available for this category.</p>';
    }

    mysqli_close($conn);
    exit;
}

// Fetch services based on category ID
if (isset($_POST['categoryId'])) {
    $categoryId = intval($_POST['categoryId']);

    // Fetch services based on category
    $query = "SELECT * FROM services WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="row">';
        while ($service = $result->fetch_assoc()) {
            $serviceName = htmlspecialchars($service['name']);
            $serviceDescription = htmlspecialchars($service['description']);
            $servicePrice = htmlspecialchars($service['price']);
            $serviceImage = htmlspecialchars($service['image']); // Ensure this path is correct

            // Sanitize image URL for security
            $serviceImage = filter_var($serviceImage, FILTER_SANITIZE_URL);
?>
            <div class="col-md-4">
                <div class="card mx-2 mb-3" style="width: 18rem; border-radius: 15px;">
                    <img src="<?php echo $serviceImage; ?>" alt="Service Image" class="img-fluid" style="border-radius: 15px 15px 0 0; height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $serviceName; ?></h5>
                        <p class="card-text"><?php echo $serviceDescription; ?></p>
                        <p class="card-text">Price: ₱<?php echo $servicePrice; ?></p>
                    </div>
                </div>
            </div>
<?php
        }
        echo '</div>';
    } else {
        echo '<p>No services found for this category.</p>';
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>