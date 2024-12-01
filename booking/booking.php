<?php
session_start();
include '../../bws_ui/db_connection/db_connection.php'; // Include your database connection
include '../../bws_ui/includes/header.php'; // Include your database connection


// Fetch service categories
$serviceCategories = [];
$sql = "SELECT * FROM service_categories";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $serviceCategories[] = $row;
    }
} else {
    echo "Error fetching service categories: " . $conn->error;
}

// Fetch services
$services = [];
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
} else {
    echo "Error fetching services: " . $conn->error;
}




?>
<?php
// Fetch discounts data from the database for the logged-in user
$discounts = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT d.id, s.name AS service_name, d.discount_percentage, d.start_time, d.end_time 
            FROM discounts d 
            JOIN services s ON d.service_id = s.id 
            WHERE d.user_id = ? AND CURRENT_TIMESTAMP BETWEEN d.start_time AND d.end_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $discounts[] = $row;
        }
    } else {
        echo "Error fetching discounts: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="../booking/booking style/booking_style.css" rel="stylesheet">
</head>

<body>


    <!-- Navbar Section -->
    <header class="navbar bg-light shadow-sm">
        <div class="container d-flex align-items-center">
            <!-- Sidebar Toggle Button -->
            <button class="sidebar-toggle btn btn-outline-danger me-3" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Logo (Centered) -->
            <a href="index.php" class="navbar-brand mx-auto text-dark">Bernadette Wellness Spa</a>
        </div>
    </header>


    <!-- Sidebar Section -->
    <div class="sidebar bg-light shadow-lg p-3 mb-5 rounded" id="sidebar" style="width: 250px; display: none;">
        <button class="close-btn btn btn-outline-danger mb-4" onclick="toggleSidebar()" style="font-size: 1.5rem; position: absolute; right: 15px; top: 15px;">&times;</button>
        <!-- User Interface Section -->
        <?php if (isset($_SESSION['username']) && isset($_SESSION['role'])): ?>
            <div class="user-interface text-center p-4 mb-4 rounded shadow-sm" style="background-color: #f8f9fa;">
                <?php
                $profile_image = "../bws_ui/images/user_profile/default_logo.jpg";
                if (file_exists("../bws_ui/images/user_profile/" . $_SESSION['username'] . ".jpg")) {
                    $profile_image = "../bws_ui/images/user_profile/" . $_SESSION['username'] . ".jpg";
                }
                // Append a query parameter to bust the cache
                $profile_image_url = $profile_image . '?' . time();
                ?>
                <div class="profile-image-container position-relative mb-3">
                    <img src="<?php echo $profile_image_url; ?>" alt="User Profile" class="profile-icon rounded-circle shadow-sm" style="width: 120px; height: 120px; border: 3px solid #6c757d; box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);">
                </div>
                <h5 class="text-primary fw-bold mt-2"><?php echo $_SESSION['username']; ?></h5>
                <form action="../../bws_ui/upload_profile.php" method="post" enctype="multipart/form-data" class="mt-3">
                    <div class="input-group mb-3">
                        <input type="file" name="profile_image" accept=".jpg, .jpeg, .png" class="form-control" id="inputGroupFile02">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update Profile</button>
                </form>
                <a href="../logout.php" class="btn btn-danger btn-sm mt-3">Log Out</a>
            </div>
        <?php else: ?>
            <div class="user-interface text-center p-4 mb-4 rounded shadow-sm" style="background-color: #f8f9fa;">
                <img src="../../bws_ui/images/user_profile/default_logo.jpg" alt="User Profile" class="profile-icon rounded-circle shadow-sm mb-2" style="width: 120px; height: 120px; border: 3px solid #6c757d; box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);">
                <h5 class="text-secondary mt-2">Guest</h5>
            </div>
        <?php endif; ?>

        <ul class="list-unstyled">
            <li class="mb-3">
                <a href="../index.php" id="homeLink" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-home fa-lg me-3 icon"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../booking/booking.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-calendar-check fa-lg me-3 icon"></i>
                    <span>Booking</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../gallery.php" id="galleryLink" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-images fa-lg me-3 icon"></i>
                    <span>Gallery</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../services.php" id="servicesLink" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-spa fa-lg me-3 icon"></i>
                    <span>Services</span>
                </a>
            </li>

            <li class="mb- 3">
                <a href="#aboutus" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-info-circle fa-lg me-3 icon"></i>
                    <span>About Us</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="container mt-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <form id="bookingForm" action="../../bws_ui/booking/process/booking_process.php" method="POST">
                    <h2 class="mb-4 text-center text-primary">Book Your Services</h2>

                    <!-- Service Selection Section -->
                    <div id="serviceList" class="booking-section">
                        <div class="section-label">Select Your Service</div>
                        <div class="form-group service-item">
                            <select class="form-control mb-3" id="serviceSelect" name="service_id">
                                <option value="" selected>Select a service</option>
                                <?php foreach ($services as $service): ?>
                                    <?php
                                        // Check if the service has a discount
                                        $discountedPrice = null;
                                        $discountQuery = "SELECT discounted_price FROM discounts WHERE service_id = " . $service['id'] . " AND NOW() BETWEEN start_time AND end_time";
                                        $discountResult = $conn->query($discountQuery);
                                        if ($discountResult && $discountRow = $discountResult->fetch_assoc()) {
                                            $discountedPrice = $discountRow['discounted_price'];
                                        }
                                        
                                        // Determine display price
                                        $displayPrice = $discountedPrice ? $discountedPrice : $service['price'];
                                    ?>
                                    <option 
                                        value="<?php echo $service['id']; ?>" 
                                        data-price="<?php echo $service['price']; ?>" 
                                        <?php if ($discountedPrice): ?> data-discounted-price="<?php echo $discountedPrice; ?>"<?php endif; ?>
                                        <?php if ($service['status'] == 'Not Available' || $service['slots'] == 0): ?> disabled<?php endif; ?>>
                                        <?php echo $service['name'] . ' - ₱' . number_format($displayPrice, 2) . ' (' . $service['slots'] . ' slots available)'; ?>
                                        <?php if ($service['status'] == 'Not Available' || $service['slots'] == 0): ?> (Not Available)<?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    
                            <button type="button" class="btn btn-success" onclick="addService()">
                                <i class="fas fa-plus"></i> Add Service
                            </button>
                        </div>
                        <div id="selectedServices" class="selected-services mt-3"></div>
                    
                        <!-- Total Price Display -->
                        <div class="mt-3">
                            <h4 class="text-primary">Total Price: <span id="totalPrice">₱0.00</span></h4>
                        </div>
                    </div>
                    

<!-- Appointment Date & Time Selection -->
<div class="mt-4">
    <div class="section-label">Select Appointment Date & Time</div>
    <input type="date" id="appointmentDate" name="appointment_date" class="form-control mb-2" required min="<?php echo date('Y-m-d'); ?>">
    <input type="time" id="appointmentTime" name="appointment_time" class="form-control mb-2" required min="08:00" max="21:00">
    <div class="mb-3">
        <label for="duration" class="form-label">Duration</label>
        <select name="duration" class="form-select" id="duration" required>
            <option value="30">30 mins</option>
            <option value="60">1 hour</option>
        </select>
    </div>
</div>
                    
                    <!-- Error Modal -->
                    <div id="errorModal" class="modal" tabindex="-1" role="dialog" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content" style="background-color: #0D6EFD; border: 2px solid #0D6EFD;">
                                <div class="modal-header">
                                    <h5 class="modal-title" style="color: white; text-align: center; width: 100%;">Error</h5>
                                    <button type="button" class="btn-close" aria-label="Close" onclick="hideErrorModal()"></button>
                                </div>
                                <div class="modal-body" style="text-align: center;">
                                    <p id="errorMessage" style="color: white;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                    document.getElementById('appointmentDate').addEventListener('input', function() {
                        var inputDate = new Date(this.value);
                        if (inputDate.getDay() === 0) { // 0 = Sunday
                            showErrorModal('Appointments cannot be scheduled on Sundays. Please select another date.');
                            this.value = ''; // Clear the input
                        }
                    });
                    
                    document.getElementById('appointmentTime').addEventListener('input', function() {
                        var inputTime = this.value;
                        var minTime = '08:00';
                        var maxTime = '21:00';
                        if (inputTime < minTime || inputTime > maxTime) {
                            showErrorModal('Appointments can only be scheduled between 8 AM and 9 PM. Please select a valid time.');
                            this.value = ''; // Clear the input
                        }
                    });
                    
                    function showErrorModal(message) {
                        document.getElementById('errorMessage').textContent = message;
                        var errorModal = document.getElementById('errorModal');
                        errorModal.style.display = 'block';
                        document.addEventListener('click', hideErrorModal);
                    }
                    
                    function hideErrorModal() {
                        var errorModal = document.getElementById('errorModal');
                        errorModal.style.display = 'none';
                        document.removeEventListener('click', hideErrorModal);
                    }
                    </script>
                                                        
                    <!-- Include Bootstrap JS -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                    <!-- Payment Method Section -->
                    <div class="mt-4">
                        <div class="section-label">Select Payment Method</div>
                        <select class="form-control" name="payment_method" required>
                            <option value="" selected>Select a payment method</option>
                            <option value="walk-in">Walk-In</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>

                    <!-- Hidden Inputs for Total Price and Selected Services -->
                    <input type="hidden" id="totalPriceInput" name="total_price" value="0">
                    <input type="hidden" id="serviceIdsInput" name="service_ids" value=""> <!-- Comma-separated IDs -->

                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check"></i> Confirm Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
const selectedServiceIds = new Set(); // To keep track of selected service IDs
let totalPrice = 0; // To keep track of the total price

document.addEventListener("DOMContentLoaded", function () {
    // Retrieve URL parameters for service_id and discounted_price
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedServiceId = urlParams.get('service_id');
    const preselectedServicePrice = parseFloat(urlParams.get('discounted_price')) || 0;

    if (preselectedServiceId && preselectedServicePrice) {
        // Preselect the service in the dropdown
        const serviceSelect = document.getElementById('serviceSelect');
        for (let i = 0; i < serviceSelect.options.length; i++) {
            if (serviceSelect.options[i].value === preselectedServiceId) {
                serviceSelect.selectedIndex = i;
                break;
            }
        }

        // Add the preselected service to the selected services list with the discounted price
        addService(preselectedServiceId, serviceSelect.options[serviceSelect.selectedIndex].text, preselectedServicePrice);
    }

    // Add event listener for discount selection
    document.getElementById('discountSelect').addEventListener('change', function () {
        const discountId = this.value;
        if (discountId) {
            // Fetch the discount details from the server
            fetch(`fetch_discount_details.php?discount_id=${discountId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const discountPercentage = data.discount_percentage;
                        const serviceId = data.service_id;
                        const servicePrice = parseFloat(document.querySelector(`#serviceSelect option[value="${serviceId}"]`).getAttribute('data-price'));
                        const discountedPrice = servicePrice * (1 - discountPercentage / 100);

                        // Update the total price
                        totalPrice = discountedPrice;
                        updateTotalPrice();
                    } else {
                        alert('Failed to fetch discount details.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching discount details:', error);
                });
        } else {
            // Reset the total price if no discount is selected
            totalPrice = 0;
            updateTotalPrice();
        }
    });
});

function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function addService(serviceId, serviceText, servicePrice) {
    const serviceSelect = document.getElementById('serviceSelect');
    const selectedServiceId = serviceId || serviceSelect.value;
    const selectedServiceText = serviceText || serviceSelect.options[serviceSelect.selectedIndex].text;
    const selectedServicePrice = servicePrice || parseFloat(serviceSelect.options[serviceSelect.selectedIndex].getAttribute('data-price')) || 0;

    // Check if the service has already been added
    if (selectedServiceIds.has(selectedServiceId)) {
        alert("This service has already been added.");
        return; // Exit the function if it's already added
    }

    // Use the discounted price if available
    const discountedPrice = parseFloat(serviceSelect.options[serviceSelect.selectedIndex].getAttribute('data-discounted-price'));
    const finalPrice = discountedPrice || selectedServicePrice;

    if (selectedServiceId) {
        selectedServiceIds.add(selectedServiceId); // Add the service ID to prevent duplicates
        totalPrice += finalPrice; // Add the correct price (discounted if available) to the total

        const serviceDiv = document.createElement('div');
        serviceDiv.classList.add('selected-service-item', 'mt-2', 'p-2', 'border', 'rounded', 'bg-light');
        serviceDiv.setAttribute('data-id', selectedServiceId);
        serviceDiv.innerHTML = `
            <span>${selectedServiceText.split(' - ')[0]} - ₱${finalPrice.toFixed(2)}</span>
            <button type="button" class="btn btn-danger btn-sm ms-2" onclick="removeService(this, '${selectedServiceId}', ${finalPrice})">
                <i class="fas fa-minus"></i> Remove
            </button>
        `;
        document.getElementById('selectedServices').appendChild(serviceDiv);

        disableSelectedServices();
        updateTotalPrice();
    }
}

function removeService(button, serviceId, servicePrice) {
    const serviceItem = button.parentElement;
    serviceItem.remove();
    selectedServiceIds.delete(serviceId); // Remove the service ID from the set
    totalPrice -= servicePrice; // Subtract from total price
    enableServiceOption(serviceId); // Re-enable the option
    updateTotalPrice(); // Update total price display
}

function updateTotalPrice() {
    const totalPriceElement = document.getElementById('totalPrice');
    totalPriceElement.textContent = `₱${formatNumberWithCommas(totalPrice.toFixed(2))}`; // Display the calculated total price
    document.getElementById('totalPriceInput').value = totalPrice.toFixed(2); // Update the hidden input for total price
    document.getElementById('serviceIdsInput').value = Array.from(selectedServiceIds).join(','); // Update the hidden input for selected services
}

function disableSelectedServices() {
    const serviceSelect = document.getElementById('serviceSelect');
    for (const option of serviceSelect.options) {
        if (selectedServiceIds.has(option.value)) {
            option.disabled = true; // Disable selected options
        }
    }
}

function enableServiceOption(serviceId) {
    const serviceSelect = document.getElementById('serviceSelect');
    for (const option of serviceSelect.options) {
        if (option.value === serviceId) {
            option.disabled = false; // Re-enable the option when removed
            break;
        }
    }
}
</script>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../bws_ui/booking/booking style/sw_services.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FontAwesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.style.display = sidebar.style.display === 'block' ? 'none' : 'block';
        }
    </script>

    <script>
        document.getElementById('date').setAttribute('min', new Date().toISOString().split('T')[0]);
    </script>



</body>

</html>



<style>
    .booking-section {
        padding: 20px;
        margin-bottom: 20px;
        border: 2px solid #007bff;
        border-radius: 8px;
    }

    .section-label {
        font-size: 1.25rem;
        color: #007bff;
        font-weight: bold;
        margin-bottom: 10px;
    }

    #addedServices .card {
        border: 2px dashed #6c757d;
        padding: 15px;
    }

    .total-price-box {
        padding: 15px;
        border: 2px solid #28a745;
        border-radius: 8px;
        background-color: #f9fff9;
        text-align: right;
    }

    .font-weight-bold {
        font-size: 1.15rem;
    }


    .sidebar {
        position: fixed;
        /* Ensure it stays in place */
        left: 0;
        /* Align to the left */
        top: 0;
        /* Align to the top */
        height: 100%;
        /* Full height */
        overflow-y: auto;
        /* Scroll if necessary */
        transition: transform 0.3s ease;
        /* Smooth transition */
    }

    .selected-service-item {
        display: flex;
        align-items: center;
        padding: 10px;
        margin-top: 5px;
        border: 1px dotted #007bff;
        /* Dotted border */
        border-radius: 5px;
        /* Rounded corners */
        background-color: #f8f9fa;
        /* Light background for contrast */
    }

    .selected-service-item span {
        flex-grow: 1;
        /* Take available space */
    }
</style>




<?php include '../../bws_ui/includes/footer.php' ?>