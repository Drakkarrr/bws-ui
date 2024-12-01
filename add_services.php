<?php include_once '../bws_ui/includes/header.php'; ?>

<!-- Navbar Section -->
<header class="navbar" shadow-sm>
    <div class="container d-flex align-items-center justify-content-between">
        <!-- Sidebar Toggle Button -->
        <button class="sidebar-toggle btn btn-outline-danger me-3" onclick="toggleSidebar()"
            aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar Brand -->
        <a href="../bws_ui/dashboard.php" class="navbar-brand text-dark">Bernadette Wellness Spa Admin Panel</a>

        <!-- Date and Time Display -->
        <div class="ms-auto">
            <span id="currentDateTime" class="text-dark"></span>
        </div>
    </div>
</header>

<!-- Sidebar Section -->
<div class="sidebar bg-light shadow-lg p-3 mb-5 rounded" id="sidebar" style="width: 250px;">
    <button class="close-btn btn btn-outline-purple mb-4" onclick="toggleSidebar()"
        style="font-size: 1.5rem; position: absolute; right: 15px; top: 15px;">&times;</button>

    <!-- Admin Image Section -->
    <div class="text-center mb-3">
        <img src="../bws_ui/images/user_profile/admin.png" alt="Admin Image" class="img-fluid rounded-circle"
            style="width: 100px; height: 100px;">
        <h5 style="color: black;" class="mt-2">Admin</h5>
    </div>
    <ul class="list-unstyled">
        <li class="mb-3">
            <a href="../bws_uidashboard.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-tachometer-alt fa-lg me-3 icon"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="../../bws_ui/login/process/view_all_users.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-users fa-lg me-3 icon"></i> <!-- Registered Users Icon -->
                <span>Registered Users</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="#" id="manageBookingsLink" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item shadow rounded p-3 bg-white" role="button" aria-expanded="false">
                <i class="fas fa-calendar-alt fa-lg me-3 icon text-primary"></i>
                <span class="fw-bold">Manage Bookings</span>
                <i class="fas fa-chevron-down ms-auto toggle-icon text-secondary"></i>
            </a>
            <ul class="list-unstyled ms-4 mt-2 collapse" id="manageBookingsDropdown">
                <li class="mb-2">
                    <a href="../bws_ui/booking/pending_booking.php" class="text-dark text-decoration-none d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-warning"></i> Pending Bookings
                    </a>
                </li>
                <li class="mb-2">
                    <a href="../bws_ui/booking/cancelled_booking_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                        <i class="fas fa-times-circle me-2 text-danger"></i> Cancelled Bookings
                    </a>
                </li>
                <li class="mb-2">
                    <a href="../bws_ui/booking/no-show_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                        <i class="fas fa-user-slash me-2 text-muted"></i> No-Show Bookings
                    </a>
                </li>
                <li class="mb-2">
                    <a href="../bws_ui/booking/approved_booking_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 text-success"></i> Approved Bookings
                    </a>
                </li>
                <li class="mb-2">
                    <a href="../bws_ui/booking/sales.php" class="text-dark text-decoration-none d-flex align-items-center">
                        <i class="fas fa-dollar-sign me-2 text-info"></i> Sales
                    </a>
                </li>
                <li class="mb-2">
                    <a href="../bws_ui/booking/complete_bookings.php" class="text-dark text-decoration-none d-flex align-items-center">
                        <i class="fas fa-calendar-check me-2 text-primary"></i> Complete Bookings
                    </a>
                </li>
            </ul>
        </li>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const manageBookingsLink = document.getElementById('manageBookingsLink');
                const manageBookingsDropdown = document.getElementById('manageBookingsDropdown');
                const toggleIcon = manageBookingsLink.querySelector('.toggle-icon');

                manageBookingsLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default anchor click behavior

                    // Toggle the 'show' class to open/close the dropdown
                    const isExpanded = manageBookingsDropdown.classList.toggle('show');
                    manageBookingsLink.setAttribute('aria-expanded', isExpanded);

                    // Toggle the icon between chevron-down and chevron-up
                    toggleIcon.classList.toggle('fa-chevron-down', !isExpanded);
                    toggleIcon.classList.toggle('fa-chevron-up', isExpanded);
                });
            });
        </script>
        <li class="mb-3">
            <a href="../bws_ui/add_category.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Service Category</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="../bws_ui/add_services.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Services</span>
            </a>
        </li>
        <li style="text-align: center;">
            <h3 style="color: black;">Inventory</h3>
        </li>
        <li class="mb-3">
            <a href="../bws_ui/add_product.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Items</span>
            </a>
        </li>
        <li style="text-align: center; padding: 10px;">
            <h5 style="color: #333; font-weight: 600; margin: 0; font-size: 1.2rem;">Discount Services</h5>
        </li>
        <li class="mb-3">
            <a href="../bws_ui/discount.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Discount</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="../bws_ui/logout.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item"
                id="logoutLink">
                <i class="fas fa-sign-out-alt fa-lg me-3 icon"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</div>

<!-- Main Content Section -->
<div class="container my-4">
    <div class="row">
        <div class="col-md-12 mb-4 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="fas fa-plus"></i> Add Service
            </button>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm" style="height: auto;">
                <div class="card-body">
                    <h2 class="text-center mb-4">Manage Services</h2>
                    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                        <div class="alert alert-success">Service updated successfully!</div>
                    <?php endif; ?>
                    <div class="table-wrapper">
                        <table id="servicesTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include_once '../bws_ui/db_connection/db_connection.php';
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }
                                $query = "SELECT s.id, s.name, s.description, s.price, s.image, s.category_id, c.name AS category_name FROM services s JOIN service_categories c ON s.category_id = c.id";
                                $result = mysqli_query($conn, $query);
                                if (!$result) {
                                    die("Query failed: " . mysqli_error($conn));
                                }
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['category_name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['price']) . '</td>';
                                    echo '<td><img src="../bws_ui/services_images/' . htmlspecialchars($row['image']) . '" alt="Service Image" class="img-fluid" style="max-width: 100px;"></td>';
                                    echo '<td>
                                            <button class="btn btn-warning btn-sm me-2" onclick="openEditServiceModal(' . $row['id'] . ', \'' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '\', \'' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '\', ' . $row['category_id'] . ', ' . htmlspecialchars($row['price']) . ', \'' . htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') . '\')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="delete_service.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this service?\')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>';
                                    echo '</tr>';
                                }
                                mysqli_close($conn);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Add Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="modal-body">
                <form action="add_service_process.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="serviceId" name="serviceId" value="">
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                        <input type="text" class="form-control" id="serviceName" name="serviceName"
                            placeholder="Enter service name" required>
                    </div>
                    <div class="mb-3">
                        <label for="serviceDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="serviceDescription" name="serviceDescription" rows="3"
                            placeholder="Enter description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="servicePrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="servicePrice" name="servicePrice"
                            placeholder="Enter price" required>
                    </div>
                    <div class="mb-3">
                        <label for="serviceCategory" class="form-label">Category</label>
                        <select class="form-select" id="serviceCategory" name="serviceCategory" required>
                            <?php
                            $conn = new mysqli($servername, $username, $password, $dbname);
                            $categoryQuery = "SELECT id, name FROM service_categories";
                            $categoryResult = mysqli_query($conn, $categoryQuery);
                            while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                                echo '<option value="' . $categoryRow['id'] . '">' . htmlspecialchars($categoryRow['name']) . '</option>';
                            }
                            mysqli_close($conn);
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select name="serviceStatus" id="status">
                            <option value="available" selected>Available</option>
                            <option value="not available">Not Available</option>
                        </select>
                    </div>



                    <div class="mb-3">
                        <label for="serviceImage" class="form-label">Service Image</label>
                        <input type="file" class="form-control" id="serviceImage" name="serviceImage" accept="image/*">
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary w-100">Add Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editServiceForm" action="edit_service_process.php" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" id="editServiceId" name="serviceId" value="">
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                        <input type="text" class="form-control" id="editServiceName" name="serviceName"
                            placeholder="Enter service name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editServiceDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editServiceDescription" name="serviceDescription" rows="3"
                            placeholder="Enter description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editServicePrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="editServicePrice" name="servicePrice"
                            placeholder="Enter price" required>
                    </div>
                    <div class="mb-3">
                        <label for="editServiceCategory" class="form-label">Category</label>
                        <select class="form-select" id="editServiceCategory" name="serviceCategory" required>
                            <?php
                            $conn = new mysqli($servername, $username, $password, $dbname);
                            $categoryQuery = "SELECT id, name FROM service_categories";
                            $categoryResult = mysqli_query($conn, $categoryQuery);
                            while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                                echo '<option value="' . $categoryRow['id'] . '">' . htmlspecialchars($categoryRow['name']) . '</option>';
                            }
                            mysqli_close($conn);
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editServiceImage" class="form-label">Service Image</label>
                        <input type="file" class="form-control" id="editServiceImage" name="serviceImage"
                            accept="image/*">
                    </div>
                    <img id="editImagePreview" src="" alt="Current Service Image"
                        style="max-width: 100px; display: none;" />
                    <button type="submit" id="editSubmitButton" class="btn btn-primary w-100">Update Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer Section -->
<footer class="text-center bg-light-purple footer-spacing">
    <p class="mb-0">&copy; 2024 Bernadette Wellness Spa. All rights reserved.</p>
</footer>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<!-- DataTables Initialization Script -->
<script>
    $(document).ready(function() {
        $('#servicesTable').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50, 100]
        });

        // Log when the edit service form is submitted
        $('#editServiceForm').on('submit', function() {
            console.log('Edit Service Form submitted');
        });
    });

    function openEditServiceModal(id, name, description, categoryId, price, image) {
        $('#editServiceId').val(id);
        $('#editServiceName').val(name);
        $('#editServiceDescription').val(description);
        $('#editServicePrice').val(price);
        $('#editServiceCategory').val(categoryId);
        $('#editImagePreview').attr('src', '../bws_ui/services_images/' + image).show();

        // Show the edit modal
        $('#editServiceModal').modal('show');
    }
</script>
<!-- Inline JavaScript for Sidebar Toggle -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    }
</script>

<script>
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>

<style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black */
    }

    /* Adjust Modal Dialog Width */
    .modal-dialog {
        max-width: 500px;
        /* Reduces the modal width */
        margin: 1.75rem auto;
    }

    /* Reduce Modal Body Padding */
    .modal-body {
        padding: 1.5rem;
        /* Adjust padding for a more compact look */
    }

    /* Keep Modal Content Style */
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Modal Header and Button Styles */
    .modal-header {
        background-color: #6a1b9a;
        color: #ffffff;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .modal-title {
        font-weight: 600;
    }

    /* Form Controls and Buttons */
    .input-group-text,
    .form-label {
        color: black;
        border: none;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #6a1b9a;
    }

    .btn-primary {
        background-color: #6a1b9a;
        border: none;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #8e24aa;
    }

    .table-wrapper {
        max-height: 400px;
        overflow-y: auto;
    }

    .footer-spacing {
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .bg-light-purple {
        background-color: #f0eaff;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
        height: 100%;
        background-color: #f8f9fa;
        transition: left 0.3s ease;
    }

    .sidebar.open {
        left: 0;
    }

    .footer-spacing {
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .bg-light-purple {
        background-color: #f0eaff;
    }

    .card-fixed-height {
        min-height: 500px;
        /* Set a minimum height as per your layout needs */
    }
</style>

<?php include_once '../bws_ui/includes/footer.php'; ?>